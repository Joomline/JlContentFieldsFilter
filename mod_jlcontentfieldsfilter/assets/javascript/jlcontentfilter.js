
function clearJlContentFieldsFilterForm(){
    jQuery(':checked, :selected, select','#mod-finder-searchform')
        .not(':button, :submit, :reset, :hidden')
        .removeAttr('checked')
        .removeAttr('selected');
    jQuery('input[type="text"]','#mod-finder-searchform').val('')
}

function clearJlContentFieldsRadio(element) {
    jQuery(element).parent().find('input[type="radio"]:checked').removeAttr('checked');
}

jQuery(document).ready(function ()
{
    if(jlcffsettings.autho_send == '1')
    {
        var sendTimeoutID=0;
        jQuery('input[type="radio"], input[type="checkbox"], select','#mod-finder-searchform')
            .on('change', function ()
            {
                jQuery(this).parents('form').submit();
        });
        jQuery('input[type="text"]','#mod-finder-searchform')
            .on('keyup', function ()
            {
                var input = jQuery(this);
                clearTimeout(sendTimeoutID);
                sendTimeoutID = setTimeout(function(){
                    input.parents('form').submit();
                }, 500);
        });
    }
});