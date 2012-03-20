
    function showFiltersOpts( id ) {

        container   = jQuery('#div_container_'+id);
        input       = jQuery('#input_filter_'+id);
        option      = jQuery('#div_opt_'+id);

        if ( container.is(':visible') ) {
            return;
        }

        // TAILLE DE LA DIV
        size                = input.outerWidth() - 22; // 22 ? padding, border ???
        if ( size < 230 ) {
            size        = 230;
        }
        option.width(size);

        // PLACEMENT DE LA DIV
        inputPosition       = input.offset();
        inputPosition.left  -= 10; // Déplacement à cause du padding
        inputPosition.top   += input.outerHeight();
        container.fadeIn(); // La DIV doit être visible avant l'appel à la fonction offset pour qu'elle fonctionne
        container.offset(inputPosition);

        // MASQUER LES AUTRES DIV
        divs        = jQuery('body div[id*=div_container_]');
        divs.each(function(index, element) { if (element.id != 'div_container_'+id )jQuery(element).fadeOut() });
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

        filter      = jQuery('#div_filter_'+id);
        container   = jQuery('#div_container_'+id);

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

        select      = jQuery('#select_filter_'+id);
        input       = jQuery('#input_filter_'+id);
        clear       = jQuery('#clear_filter_'+id);

        // LES MEMES REGLES DOIVENT ETRE REPRISES DANS LA TEMPLATE

//        if ( select.val() == 'in' || select.val() == 'containsIn' ) {
//
//            if ( input.nodeName == 'INPUT' ) {
//
//                txt             = document.createElement( 'textarea' );
//                txt.id          = input.id;
//                txt.name        = input.name;
//                txt.value       = input.value;
//                txt.style.borderColor    = input.style.borderColor;
//                txt.readOnly    = input.readOnly;
//                txt.onkeyup     = input.onkeyup;
//                txt.onchange    = input.onchange;
//
//                $( 'div_filter_'+name ).insertBefore( txt, input );
//                $( 'div_filter_'+name ).removeChild( input );
//            }
//        }
//        else {
//
//            if ( input.nodeName == 'TEXTAREA' ) {
//
//                txt             = document.createElement( 'input' );
//                txt.id          = input.id;
//                txt.name        = input.name;
//                txt.value       = input.value;
//                txt.style.borderColor    = input.style.borderColor;
//                txt.readOnly    = input.readOnly;
//                txt.onkeyup     = input.onkeyup;
//                txt.onchange    = input.onchange;
//
//                $( 'div_filter_'+name ).insertBefore( txt, input );
//                $( 'div_filter_'+name ).removeChild( input );
//            }
//        }

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

        input           = jQuery('#input_filter_'+id);
        input[0].value  = '';

        select          = jQuery('#select_filter_'+id);
        select[0].value = 'contains';

        filtersOptsChanged(id);
    }

    function clearFilters() {

//        listInputs  = $$('input.filters');      // TODO: detection par la classe pas beau !!
//
//        for ( i = 0; i < listInputs.length; i++ ) {
//            listInputs[i].value     = '';
//
//            // Remettre l'opérateur sur le premier élément
//            idInput     = listInputs[i].id;
//            idSelect    = idInput.replace( 'input_filter_', 'select_filter_' );
//            if ( $( idSelect ) ) {
//                $( idSelect ).selectedIndex = 0;
//            }
//        }
    }

    function submitEnter( event, input ) {

        if ( event && event.keyCode == 13 ) {
            if (input.form.onsubmit) {
                if (input.form.onsubmit()) {
                    input.form.submit();
                }
            }
            else {
                input.form.submit();
            }
        }
    }