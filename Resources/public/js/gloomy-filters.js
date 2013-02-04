
function showFiltersOpts( id ) {

    container   = $('#div_container_'+id);
    input       = $('#input_filter_'+id);
    option      = $('#div_opt_'+id);

    if ( container.is(':visible') ) {
        return;
    }

    // TAILLE DE LA DIV
    size                = input.outerWidth() - 22; // 22 ? padding, border ???
    if ( size < 220 ) {
        size        = 220;
    }
    option.width(size);

    // PLACEMENT DE LA DIV
    inputPosition       = input.offset();
    inputPosition.left  -= 10; // Déplacement à cause du padding
    inputPosition.top   += input.outerHeight();
    container.fadeIn(); // La DIV doit être visible avant l'appel à la fonction offset pour qu'elle fonctionne
    container.offset(inputPosition);

    // MASQUER LES AUTRES DIV
    divs        = $('body div[id*=div_container_]');
    divs.each(function(index, element) { if (element.id != 'div_container_'+id ) $(element).fadeOut() });
}

function isRelated( obj, container ) {

    if ( ! obj ) return ( true );       // ??

    while ( obj != container && obj.nodeName != 'BODY' ) {
        obj     = obj.parentNode
        if ( ! obj ) return ( true );   // ??
    }

    if ( obj.id == container.id ) {
        return ( true );
    }

    return ( false );
}

function hideFiltersOpts( e, id ) {

    filter      = $('#div_filter_'+id);
    container   = $('#div_container_'+id);

    // http://www.quirksmode.org/js/events_mouse.html
    if ( ! e ) var e = window.event;
    var tg      = (window.event) ? e.srcElement : e.target;
    var reltg   = (e.relatedTarget) ? e.relatedTarget : e.toElement;
    if ( isRelated( reltg, filter[0] ) ) {
        return ( true );
    }

    if ( container.is(':visible') ) {
        container.fadeOut();
    }
}

function filtersOptsChanged( id ) {

    select      = $('#select_filter_'+id);
    input       = $('#input_filter_'+id);
    clear       = $('#clear_filter_'+id);

    if ( select.val() == 'null' || select.val() == 'notNull' ) {
    	input.attr('class', 'gloomy-filters notNullFilter');
        clear[0].style.visibility   = 'hidden';
    }
    else if ( input.val() ) {
    	input.attr('class', 'gloomy-filters textFilter');
        clear[0].style.visibility   = 'visible';
    }
    else {
    	input.attr('class', 'gloomy-filters');
        clear[0].style.visibility   = 'hidden';
    }

    input[0].focus();
}

function resetFilter( id ) {

    input           = $('#input_filter_'+id).val('');
    select          = $('#select_filter_'+id).val('contains');

    filtersOptsChanged(id);
}

// Not used anymore
function submitEnter( event, input ) {

	//return true;
    if ( event && event.keyCode == 13 ) {
    	$(input.form).submit();
    }
}