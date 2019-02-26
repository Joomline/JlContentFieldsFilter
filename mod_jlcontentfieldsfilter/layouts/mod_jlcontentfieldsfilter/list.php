<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$field = $displayData['field'];
$label = JText::_($field->label);
$value = $field->value;
$listOptions = (array)$field->fieldparams->get('options', array());
$options = array();
if(is_array($listOptions)){
	foreach ( $listOptions as $listOption ) {
		$options[] = JHtml::_('select.option', $listOption->value, $listOption->name);
    }
}
if(!count($options)){
	return;
}
?>
<label class="jlmf-label" for="<?php echo $field->name.'-'.$moduleId; ?>"><?php echo $label; ?></label>
<select
    name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
    id="<?php echo $field->name.'-'.$moduleId; ?>"
    class="jlmf-select"
>
    <option value=""><?php echo JText::_('JSELECT'); ?></option>

	<?php foreach($options as $k => $v) : ?>
	    <?php $checked = ($v->value == $value) ? ' selected="selected"' : ''; ?>
        <option value="<?php echo $v->value; ?>" <?php echo $checked; ?>><?php echo JText::_($v->text); ?></option>
	<?php endforeach; ?>
</select>
