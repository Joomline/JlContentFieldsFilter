
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