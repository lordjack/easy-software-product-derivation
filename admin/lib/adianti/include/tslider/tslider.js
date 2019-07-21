function tslider_start(id, value, min, max, step)
{
    $( id+'_div' ).slider({
        value: value,
        min: min,
        max: max,
        step: step,
        slide: function( event, ui ) {
            $( id ).val( ui.value );
        }
    });
}

function tslider_enable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').attr('readonly', false) },1);
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().show() },1);
}

function tslider_disable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').attr('readonly', true) },1);
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().hide() },1);    
}