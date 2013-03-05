
function gloomyAjaxUpdater()
{
    var url         = arguments[0];
    var div         = arguments[1];
    var options     = arguments[2] || {};

    var spinner     = options['spinner'] || null;
    var onsuccess   = options['onsuccess'] || function() {};
    var type		= options['type'] || 'get';
    var data		= options['data'] || null;
    
    $(spinner).show();
    $.ajax({
              url: url,
              cache: false,
              type: type,
              data: data,
              success: function (html) {
                            $(div).html(html);
                            $(spinner).hide();
                            onsuccess();
                        },
              error: function () {
                            $(spinner).hide();
                            $.jGrowl('Une erreur est survenue', {theme: 'error'});
                        }

    });

    return ( false );
};

// TODO : a fusionner avec la fonction gloomyAjaxUpdater() ??
function gloomyAjaxUpdaterDialog()
{
    var url         = arguments[0];
    var dialog      = arguments[1];
    var options     = arguments[2] || {};

    var spinner     = options['spinner'] || null;
    var onsuccess   = options['onsuccess'] || function() {};

    $(spinner).show();
    $.ajax({
              url: url,
              cache: false,
              success: function (html) {
                            $(dialog).html(html);
                            $(spinner).hide();
                            onsuccess();
                            $(dialog).dialog('open');
                        },
              error: function () {
                            $(spinner).hide();
                            $.jGrowl('Une erreur est survenue', {theme: 'error'});
                        }

    });
    return ( false );
}

// jQuery-ui Dialog
function gloomyAjaxDialogAction()
{
    var dialog      = arguments[0];
    var options     = arguments[1] || {};

    var form        = options['form'] || $(dialog).find('form:first');
    var spinner     = options['spinner'] || null;
    var onsuccess   = options['onsuccess'] || function() {};
    var onformerror = options['onformerror'] || function() {};

    $(spinner).show();
    $.ajax({type: 'POST',
              url: $(form).attr('action'),
              data: $(form).serializeArray(),

              success: function (data, textStatus, jqXHR) {

                            $(spinner).hide();

                            // La réponse est du JSON
                            if (typeof(data) == 'object' && jqXHR.getResponseHeader( "content-type" ).indexOf('json') != -1) {
                                try {
                                    if (data['success']) {
                                        $(dialog).dialog( "close" );
                                        onsuccess();
                                        $.jGrowl(data['message'], {theme: 'success'});
                                    }
                                    else {
                                        $.jGrowl(data['message'], {theme: 'error'});
                                    }
                                }
                                catch (e) {
                                    $.jGrowl('Une erreur est survenue', {theme: 'error'});
                                }
                            }
                            else {
                                $.jGrowl('Le formulaire comporte des erreurs', {theme: 'warning'});
                                $(dialog).html(data);
                            	onformerror();
                            }
                        },
              error: function () {
                            $(spinner).hide();
                            $.jGrowl('Une erreur est survenue', {theme: 'error'});
                        }
    });
    return ( false );
}

// Twitter Bootstrap Modal
function gloomyAjaxModalAction()
{
    var modal       = arguments[0];
    var options     = arguments[1] || {};

    var form        = options['form'] || $(modal).find('form:first');
    var spinner     = options['spinner'] || null;
    var onsuccess   = options['onsuccess'] || function() {};
    var onformerror = options['onformerror'] || function() {};
    var body        = options['body'] || $(modal).find('.modal-body:first');

    $(spinner).show();
    $.ajax({type: 'POST',
              url: $(form).attr('action'),
              data: $(form).serializeArray(),

              success: function (data, textStatus, jqXHR) {

                            $(spinner).hide();

                            // La réponse est du JSON
                            if (typeof(data) == 'object' && jqXHR.getResponseHeader( "content-type" ).indexOf('json') != -1) {
                                try {
                                    if (data['success']) {
                                        $(modal).modal( "hide" );
                                        onsuccess();
                                        $.jGrowl(data['message'], {theme: 'success'});
                                    }
                                    else {
                                        $.jGrowl(data['message'], {theme: 'error'});
                                    }
                                }
                                catch (e) {
                                    $.jGrowl('Une erreur est survenue', {theme: 'error'});
                                }
                            }
                            else {
                                $.jGrowl('Le formulaire comporte des erreurs', {theme: 'warning'});
                                $(body).html(data);
                            	onformerror();
                            }
                        },
              error: function () {
                            $(spinner).hide();
                            $.jGrowl('Une erreur est survenue', {theme: 'error'});
                        }
    });
    return ( false );
}

function gloomyAjaxAction()
{
    var url         = arguments[0];
    var options     = arguments[1] || {};

    var spinner     = options['spinner'] || null;
    var onsuccess   = options['onsuccess'] || function() {};
    var type		= options['type'] || 'get';
    var data		= options['data'] || null;
    
    $(spinner).show();
    $.ajax({  url: url,
		      type: type,
		      data: data,
              success: function (data) { // La réponse est normalement toujours en JSON
                            $(spinner).hide();
                            if (data['success']) {
                                onsuccess();
                                $.jGrowl(data['message'], {theme: 'success'});
                            }
                            else {
                                $.jGrowl(data['message'], {theme: 'error'});
                            }
                        },
              error: function () {
                            $(spinner).hide();
                            $.jGrowl('Une erreur est survenue', {theme: 'error'});
                        }
    });
    return ( false );
}

function gloomyFormURL(form)
{
    return $(form).attr('action')+'&'+$(form).serialize();
}