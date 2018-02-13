<?php
/**
 * JL Content Fields Filter
 *
 * @version 	1.0.4
 * @author		Joomline
 * @copyright	(C) 2017 Arkadiy Sedelnikov. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
	return;
}

$uid = '_' . uniqid();

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
<div class="control-group">
    <h4>
		<?php echo $label; ?>
    </h4>
    <div class="controls">
        <select
                name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
                id="<?php echo $field->name . $uid; ?>"
                class="span12"
        >
            <option value=""><?php echo JText::_('JSELECT'); ?></option>

		    <?php foreach($options as $k => $v) : ?>
			    <?php $checked = ($v->value == $value) ? ' selected="selected"' : ''; ?>
                <option value="<?php echo $v->value; ?>" <?php echo $checked; ?>><?php echo $v->text; ?></option>
		    <?php endforeach; ?>
        </select>

    </div>
</div>
