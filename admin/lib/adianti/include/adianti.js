function Adianti(){}

/**
 * Goto a given page
 */
function __adianti_goto_page(page)
{
    window.location = page;
}

/**
 * Returns the URL Base
 */
function __adianti_base_url()
{
   return window.location.protocol +'//'+ window.location.host + window.location.pathname.split( '/' ).slice(0,-1).join('/');
}

/**
 * Returns the query string
 */
function __adianti_query_string()
{
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0; i<vars.length; i++)
    {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined")
        {
            query_string[pair[0]] = pair[1];
            // If second entry with this name
        }
        else if (typeof query_string[pair[0]] === "string")
        {
            var arr = [ query_string[pair[0]], pair[1] ];
            query_string[pair[0]] = arr;
        }
        else
        {
            query_string[pair[0]].push(pair[1]);
        }
    } 
    return query_string;
}

/**
 * Converts query string into json object
 */
function __adianti_query_to_json(query)
{
    var decode = function (s) { return decodeURIComponent(s.replace(/\+/g, " ")); };
    var urlParams = {};
    var search = /([^&=]+)=?([^&]*)/g;
    while (match = search.exec(query)) {
       urlParams[decode(match[1])] = decode(match[2]);
    }
    return urlParams;
}

/**
 * Loads an HTML content
 */
function __adianti_load_html(content, afterCallback)
{
    var match_container = content.match('adianti_target_container="([0-z-]*)"');
    
    if ( match_container !== null)
    {
        var target_container = match_container[1];
        $('#'+target_container).empty();
        $('#'+target_container).html(content);
    }
    else if ($('[widget="TWindow"]').length > 0 && (content.indexOf("TWindow") > 0))
    {
        $('[widget="TWindow"]').attr('remove', 'yes');
        $('#adianti_online_content').empty();
        content = content.replace(new RegExp('__adianti_append_page', 'g'), '__adianti_append_page2'); // chamadas presentes em botões seekbutton em window, abrem em outra janela
        $('#adianti_online_content').html(content);
        $('[widget="TWindow"][remove="yes"]').remove();
    }
    else
    {
        if (content.indexOf("TWindow") > 0)
        {
            content = content.replace(new RegExp('__adianti_append_page', 'g'), '__adianti_append_page2'); // chamadas presentes em botões seekbutton em window, abrem em outra janela
            $('#adianti_online_content').html(content);
        }
        else
        {
            if (typeof Adianti.onClearDOM == "function")
            {
                Adianti.onClearDOM();
            }
            
            $('[widget="TWindow"]').remove();
            $('#adianti_div_content').html(content);
        }
    }
    
    if (typeof afterCallback == "function")
    {
        afterCallback();
    }
}

/**
 * Loads an HTML content. This function is called if there is an window opened.
 */
function __adianti_load_html2(content)
{
   if ($('[widget="TWindow2"]').length > 0)
   {
       $('[widget="TWindow2"]').attr('remove', 'yes');
       $('#adianti_online_content2').hide();
       content = content.replace(new RegExp('__adianti_load_html', 'g'), '__adianti_load_html2'); // se tem um botão de buscar, ele está conectado a __adianti_load_html
       content = content.replace(new RegExp('__adianti_post_data', 'g'), '__adianti_post_data2'); // se tem um botão de buscar, ele está conectado a __adianti_load_html
       content = content.replace(new RegExp('TWindow','g'), 'TWindow2'); // quando submeto botão de busca, é destruído tudo que tem TWindow2 e recarregado
       content = content.replace(new RegExp('generator="adianti"', 'g'), 'generator="adianti2"'); // links também são alterados
       $('#adianti_online_content2').html(content);
       $('[widget="TWindow2"][remove="yes"]').remove();
       $('#adianti_online_content2').show();
   }
   else
   {
       if (content.indexOf("TWindow2") > 0)
       {
           $('#adianti_online_content2').html(content);
       }
       else if (content.indexOf("TWindow") > 0)
       {
           $('#adianti_online_content').html(content);
       }
       else
       {
           $('#adianti_div_content').html(content);
       }
   }
}

function __adianti_load_page_no_register(page)
{
    $.get(page)
    .done(function(data) {
        __adianti_load_html(data);
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

function __adianti_load_page_no_register2(page)
{
    $.get(page)
    .done(function(data) {
        __adianti_load_html2(data);
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

/**
 * Called by Seekbutton. Add the page content. 
 */
function __adianti_append_page(page)
{
    page = page.replace('engine.php?','');
    params_json = __adianti_query_to_json(page);

    uri = 'engine.php?' 
        + 'class=' + params_json.class
        + '&method=' + params_json.method
        + '&static=' + (params_json.static == '1' ? '1' : '0');

    $.post(uri, params_json)
    .done(function(data){
        data = data.replace(new RegExp('__adianti_append_page', 'g'), '__adianti_append_page2'); // chamadas presentes em botões seekbutton em window, abrem em outra janela
        $('#adianti_online_content').after('<div></div>').html(data);
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

/**
 * Called by Seekbutton from opened windows. 
 */
function __adianti_append_page2(page)
{
    page = page.replace('engine.php?','');
    params_json = __adianti_query_to_json(page);

    uri = 'engine.php?' 
        + 'class=' + params_json.class
        + '&method=' + params_json.method
        + '&static=' + (params_json.static == '1' ? '1' : '0');

    $.post(uri, params_json)
    .done(function(data) {
        data = data.replace(new RegExp('__adianti_load_html', 'g'), '__adianti_load_html2'); // se tem um botão de buscar, ele está conectado a __adianti_load_html
        data = data.replace(new RegExp('__adianti_post_data', 'g'), '__adianti_post_data2'); // se tem um botão de buscar, ele está conectado a __adianti_load_html
        data = data.replace(new RegExp('TWindow', 'g'),             'TWindow2'); // quando submeto botão de busca, é destruído tudo que tem TWindow2 e recarregado
        data = data.replace(new RegExp('generator="adianti"', 'g'), 'generator="adianti2"'); // links também são alterados
        $('#adianti_online_content2').after('<div></div>').html(data);
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

/**
 * Open a page using ajax
 */
function __adianti_load_page(page, callback)
{
    if (typeof page !== 'undefined')
    {
        $( '.modal-backdrop' ).remove();
        url = page;
        url = url.replace('index.php', 'engine.php');
        
        if(url.indexOf('engine.php') == -1) {
            url = 'xhr-'+url;
        }
        
        if (typeof Adianti.onBeforeLoad == "function")
        {
            Adianti.onBeforeLoad(url);
        }
        
        if (url.indexOf('&static=1') > 0)
        {
            $.get(url)
            .done(function(data) {
                __adianti_parse_html(data);
                
                if (typeof callback == "function")
                {
                    callback();
                }
                
                if (typeof Adianti.onAfterLoad == "function")
                {
                    Adianti.onAfterLoad();
                }
            }).fail(function(jqxhr, textStatus, exception) {
               __adianti_error('Error', textStatus + ': ' + 'connection failed');
            });
        }
        else
        {
            $.get(url)
            .done(function(data) {
                __adianti_load_html(data, Adianti.onAfterLoad);
                
                if (typeof callback == "function")
                {
                    callback();
                }
                
                if ( url.indexOf('register_state=false') < 0 && history.pushState && (data.indexOf("TWindow") < 0) )
                {
                    __adianti_register_state(url, 'adianti');
                    Adianti.currentURL = url;
                }
            }).fail(function(jqxhr, textStatus, exception) {
               __adianti_error('Error', textStatus + ': ' + 'connection failed');
            });
        }
    }
}

/**
 * Used by all links inside a window (generator=adianti)
 */
function __adianti_load_page2(page)
{
    url = page;
    url = url.replace('index.php', 'engine.php');
    __adianti_load_page_no_register2(url);
}

/**
 * Start blockUI dialog
 */
function __adianti_block_ui(wait_message)
{
    if (typeof $.blockUI == 'function')
    {
        if (typeof Adianti.blockUIConter == 'undefined')
        {
            Adianti.blockUIConter = 0;
        }
        Adianti.blockUIConter = Adianti.blockUIConter + 1;
        if (typeof wait_message == 'undefined')
        {
            wait_message = Adianti.waitMessage;
        }
        
        $.blockUI({ 
           message: '<h1><i class="fa fa-spinner fa-pulse"></i> '+wait_message+'</h1>',
           css: { 
               border: 'none', 
               top: '100px',
               maxWidth: '300px',
               width: 'inherit',
               padding: '15px', 
               backgroundColor: '#000', 
               'border-radius': '5px 5px 5px 5px',
               opacity: .5, 
               color: '#fff' 
           }
        });
        
        $('.blockUI.blockMsg').mycenter();
    }
}

/**
 * Open a window
 */
function __adianti_window(title, width, height, content)
{
    $('<div />').html(content).dialog({
        modal: true,
        title: title,
        width : width,
        height : height,
        resizable: true,
        closeOnEscape:true,
        close: function(ev, ui) { $(this).remove(); },
        focus:true
    });
}

function __adianti_window_page(title, width, height, page)
{
    if (width<2)
    {
        width = $(window).width() * width;
    }
    if (height<2)
    {
        height = $(window).height() * height;
    }
    
    $('<div />').append($("<iframe style='width:100%;height:97%' />").attr("src", page)).dialog({
        modal: true,
        title: title,
        width : width,
        height : height,
        resizable: false,
        closeOnEscape:true,
        close: function(ev, ui) { $(this).remove(); },
        focus:true
    });
}

/**
 * Show standard dialog
 */
function __adianti_dialog( options )
{
    if (options.type == 'info') {
        var icon = (options.icon ? options.icon : 'fa fa-info-circle fa-5x blue');
    }
    else if (options.type == 'error') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-circle fa-5x red');
    }
    
    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: options.title,
          animate: false,
          backdrop: true,
          onEscape: function() {
            if (typeof options.callback != 'undefined')
            { 
                options.callback();
            }
          },
          message: '<div>'+
                    '<span class="'+icon+'" style="float:left"></span>'+
                    '<span display="block" style="margin-left:20px;width:80%;float:left">'+options.message+'</span>'+
                    '</div>',
          buttons: {
            success: {
              label: "OK",
              className: "btn-default",
              callback: function() {
                if (typeof options.callback != 'undefined')
                { 
                    options.callback();
                }
              }
            }
          }
        });
    }
    else {
        // fallback mode
        alert(options.message);
        if (typeof options.callback != 'undefined') {
            options.callback();
        }
    }
}

/**
 * Show message error dialog
 */
function __adianti_error(title, message, callback)
{
    __adianti_dialog( { type: 'error', title: title, message: message, callback: callback} );
}

/**
 * Show message info dialog
 */
function __adianti_message(title, message, callback)
{
    __adianti_dialog( { type: 'info', title: title, message: message, callback: callback} );
}

/**
 * Show question dialog
 */
function __adianti_question(title, message, callback_yes, callback_no, label_yes, label_no)
{
    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: title,
          animate: false,
          message: '<div>'+
                    '<span class="fa fa-question-circle fa-5x blue" style="float:left"></span>'+
                    '<span display="block" style="margin-left:20px;float:left">'+message+'</span>'+
                    '</div>',
          buttons: {
            yes: {
              label: label_yes,
              className: "btn-default",
              callback: function() {
                if (typeof callback_yes != 'undefined') {
                    callback_yes();
                }
              }
            },
            no: {
              label: label_no,
              className: "btn-default",
              callback: function() {
                if (typeof callback_no != 'undefined') {
                    callback_no();
                }
              }
            },
          }
        });
    }
    else
    {
        // fallback mode
        var r = confirm(message);
        if (r == true) {
            if (typeof callback_yes != 'undefined') {
                callback_yes();
            }
        } else {
            if (typeof callback_no != 'undefined') {
                callback_no();
            }
        }
    }
}

/**
 * Show input dialog
 */
function __adianti_input(question, callback)
{
    if (typeof bootbox == 'object')
    {
        bootbox.prompt(question, function(result) {
          if (result !== null) {
            callback(result);
          }
        });
    }
    else
    {
        var result = prompt(question, '');
        callback(result);
    }
}

/**
 * Closes blockUI dialog
 */
function __adianti_unblock_ui()
{
    if (typeof $.blockUI == 'function')
    {
        Adianti.blockUIConter = Adianti.blockUIConter -1;
        if (Adianti.blockUIConter <= 0)
        {
            $.unblockUI();
            Adianti.blockUIConter = 0;
        }
    }
}

/**
 * Post form data
 */
function __adianti_post_data(form, action)
{
    __adianti_block_ui();
    
    if (action.substring(0,4) == 'xhr-')
    {
        url = action;
    }
    else
    {
        url = 'index.php?'+action;
        url = url.replace('index.php', 'engine.php');
    }
    
    data = $('#'+form).serialize();
    
    if (typeof Adianti.onBeforePost == "function")
    {
        Adianti.onBeforePost(url);
    }
    
    if (url.indexOf('&static=1') > 0 || (action.substring(0,4) == 'xhr-'))
    {
        $.post(url, data)
        .done(function(result) {
            __adianti_parse_html(result);
            __adianti_unblock_ui();
            
            if (typeof Adianti.onAfterPost == "function")
            {
                Adianti.onAfterPost();
            }
        }).fail(function(jqxhr, textStatus, exception) {
            __adianti_unblock_ui();
            __adianti_error('Error', textStatus + ': ' + 'connection failed');
        });
    }
    else
    {
        $.post(url, data)
        .done(function(result) {
            __adianti_load_html(result, Adianti.onAfterPost);
            __adianti_unblock_ui();
            Adianti.currentURL = url;
        }).fail(function(jqxhr, textStatus, exception) {
            __adianti_unblock_ui();
            __adianti_error('Error', textStatus + ': ' + 'connection failed');
        });
    }
}

/**
 * Post form data over window
 */
function __adianti_post_data2(form, url)
{
    url = 'index.php?'+url;
    url = url.replace('index.php', 'engine.php');
    data = $('#'+form).serialize();
    
    $.post(url, data)
    .done(function(result)
    {
        __adianti_load_html2(result);
        __adianti_unblock_ui();
    }).fail(function(jqxhr, textStatus, exception) {
        __adianti_unblock_ui();
        __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

/**
 * Register URL state
 */
function __adianti_register_state(url, origin)
{
    if (Adianti.registerState !== false || origin == 'user')
    {
        var stateObj = { url: url };
        if (typeof history.pushState != 'undefined') {
            history.pushState(stateObj, "", url.replace('engine.php', 'index.php').replace('xhr-', ''));
        }
    }
}

/**
 * Ajax lookup
 */
function __adianti_ajax_lookup(action, field)
{
    var value = field.value;
    __adianti_ajax_exec(action +'&key='+value+'&ajax_lookup=1', null);
}

/**
 * Execute an Ajax action
 */
function __adianti_ajax_exec(action, callback, automatic_output)
{
    var uri = 'engine.php?' + action +'&static=1';
    var automatic_output = (typeof automatic_output === "undefined") ? true : automatic_output;
    
    $.ajax({url: uri})
    .done(function( data ) {
        if (automatic_output) {
            __adianti_parse_html(data, callback);
        }
        else {
            callback(data);
        }
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_error('Error', textStatus + ': ' + 'connection failed');
    });
}

/**
 * Get remote content
 */
function __adianti_get_page(action, callback, postdata)
{
    var uri = 'engine.php?' + action +'&static=1';
    
    if (typeof postdata !== "undefined") {
        if (typeof postdata.static !== "undefined") {
            var uri = 'engine.php?' + action +'&static='+postdata.static;
        }
    }
    
    $.ajax({
      url: uri,
      data: postdata
      }).done(function( data ) {
          return callback(data);
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_error('Error', textStatus + ': ' + 'connection failed');
      });
}

function __adianti_post_lookup(form, action, field, callback) {
    if (typeof field == 'string') {
        field_obj = $('#'+field);
    }
    else if (field instanceof HTMLElement) {
        field_obj = $(field);
    }
    
    var formdata = $('#'+form).serializeArray();
    formdata.push({name: '_field_value', value: field_obj.val()});
    
    var uri = 'engine.php?' + action +'&static=1';
    formdata.push({name: '_field_id',   value: field_obj.attr('id')});
    formdata.push({name: '_field_name', value: field_obj.attr('name')});
    formdata.push({name: '_form_name',  value: form});
    formdata.push({name: '_field_data', value: $.param(field_obj.data(), true)});
    formdata.push({name: 'key',         value: field_obj.val()}); // for BC
    formdata.push({name: 'ajax_lookup', value: 1});
    
    $.ajax({
      type: 'POST',
      url: uri,
      data: formdata
      }).done(function( data ) {
          __adianti_parse_html(data, callback);
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_error('Error', textStatus + ': ' + 'connection failed');
      });
}

/**
 * Parse returning HTML
 */
function __adianti_parse_html(data, callback)
{
    tmp = data;
    tmp = new String(tmp.replace(/window\.opener\./g, ''));
    tmp = new String(tmp.replace(/window\.close\(\)\;/g, ''));
    tmp = new String(tmp.replace(/^\s+|\s+$/g,""));
    
    if ($('[widget="TWindow2"]').length > 0)
    {
       // o código dinâmico gerado em ajax lookups (ex: seekbutton)
       // deve ser modificado se estiver dentro de window para pegar window2
       tmp = new String(tmp.replace(/TWindow/g, 'TWindow2'));
    }
    
    try {
        // permite código estático também escolher o target
        var match_container = data.match('adianti_target_container="([0-z]*)"');
        
        if ( match_container !== null)
        {
            var target_container = match_container[1];
            $('#'+target_container).empty();
            $('#'+target_container).html(tmp);
        }
        else
        {
            // target default
            $('#adianti_online_content').append(tmp);
        }
        
        if (callback && typeof(callback) === "function")
        {
            callback(data);
        }
        
    } catch (e) {
        if (e instanceof Error) {
            $('<div />').html(e.message + ': ' + tmp).dialog({modal: true, title: 'Error', width : '80%', height : 'auto', resizable: true, closeOnEscape:true, focus:true});
        }
    }
}

/**
 * Download a file
 */
function __adianti_download_file(file)
{
    extension = file.split('.').pop();
    screenWidth  = screen.width;
    screenHeight = screen.height;
    if (extension !== 'html')
    {
        screenWidth /= 3;
        screenHeight /= 3;
    }
    
    window.open('download.php?file='+file, '_blank',
      'width='+screenWidth+
     ',height='+screenHeight+
     ',top=0,left=0,status=yes,scrollbars=yes,toolbar=yes,resizable=yes,maximized=yes,menubar=yes,location=yes');
}

/**
 * Open page in new tab
 */
function __adianti_open_page(page)
{
    var win = window.open(page, '_blank');
    if (win)
    {
        win.focus();
    }
    else
    {
        alert('Please allow popups for this website');
    }
}

/**
 * Process popovers
 */
function __adianti_process_popover()
{
    var get_placement = function (tip, element) {
        $element = $(element);
        if (typeof $element.attr('popside') === "undefined") {
            return 'auto top';
        }
        else {
            return $(element).attr("popside");
        }
    };
    
    var get_content = function (tip, element) {
        if (typeof $(this).attr('popaction') === "undefined") {
            return $(this).attr("popcontent");
        }
        else {
            
            var inst = $(this);
            __adianti_get_page($(this).attr('popaction'), function(data) {
                var popover = inst.attr('data-content',data).data('bs.popover');
                popover.setContent();
                popover.show();
            }, {'static': '0'});
            return '<i class="fa fa-spinner fa-spin fa-5x fa-fw"></i>';
        }
    };
    
    var get_title = function () {
        return $(this).attr("poptitle");
    };
    
    var pop_template = '<div class="popover" role="tooltip" style="z-index:100000;max-width:800px"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"><div class="data-content"></div></div></div>';
    
    $('[popover="true"]:not([poptrigger]):not([processed="true"])').popover({
        placement: get_placement,
        trigger: 'hover',
        container: 'body',
        template: pop_template,
        delay: { show: 10, hide: 10 },
        content: get_content,
        html: true,
        title: get_title
    }).attr('processed', true);
    
    $('[popover="true"][poptrigger="click"]:not([processed="true"])').popover({
        placement: get_placement,
        trigger: 'click',
        container: 'body',
        template: pop_template,
        delay: { show: 10, hide: 10 },
        content: get_content,
        html: true,
        title: get_title
    }).on('shown.bs.popover', function (e) {
        if (typeof $(this).attr('popaction') !== "undefined") {
            var inst = $(this);
            __adianti_get_page($(this).attr('popaction'), function(data) {
                var popover = inst.attr('data-content',data).data('bs.popover');
                popover.setContent();
                popover.$tip.addClass( $(e.target).attr('popside') );
            }, {'static': '0'});
        }
    }).attr('processed', true);
    
    $('body').on('click', function (e) {
        $('.tooltip').hide();
        if (!$(e.target).is('[popover="true"]') && !$(e.target).parents('.popover').length > 0) {
            // avoid closing dropdowns inside popover (colorpicker, datepicker) when they are outside popover DOM
            if (!$(e.target).parents('.dropdown-menu').length > 0) {
                $('.popover').popover('hide');
            }
        }
    });
}

/**
 * Start actions
 */
$(function() {
    Adianti.blockUIConter = 0;
    
    if (typeof $().tooltip == 'function')
    {
        $(document.body).tooltip({
            selector: "[title]",
            placement: function (tip, element) {
                    $element = $(element);
                    if (typeof $element.attr('titside') === "undefined") {
                        return 'auto';
                    }
                    else {
                        return $(element).attr("titside");
                    }
                },
            trigger: 'hover',
            cssClass: 'tooltip',
            container: 'body',
            content: function () {
                return $(this).attr("title");
            },
            html: true
        });
    }
    
    if (typeof $().popover == 'function')
    {
        $( document ).on( "dialogopen", function(){
            __adianti_process_popover();
        });
    }
    
    if (typeof jQuery.ui !== 'undefined')
    {
        $.ui.dialog.prototype._focusTabbable = $.noop;
    }
});

/**
 * On Ajax complete actions
 */
$(document).ajaxComplete(function ()
{
    if (typeof $().popover == 'function')
    {
        __adianti_process_popover();
    }
    
    if (typeof $().DataTable == 'function')
    {
        $('table[datatable="true"]:not(.dataTable)').DataTable( {
            responsive: true,
            paging: false,
            searching: false,
            ordering:  false,
            info: false
        });
    }
});

/**
 * Override the default page loader
 */
$( document ).on( 'click', '[generator="adianti"]', function()
{
   __adianti_load_page($(this).attr('href'));
   return false;
});

/**
 * Override the default page loader for new windows
 */
$( document ).on( 'click', '[generator="adianti2"]', function()
{
   __adianti_load_page2($(this).attr('href'));
   return false;
});

/**
 * Register page navigation
 */
window.onpopstate = function(stackstate)
{
    if (stackstate.state)
    {
        __adianti_load_page_no_register(stackstate.state.url);
    }
};

$.fn.mycenter = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}