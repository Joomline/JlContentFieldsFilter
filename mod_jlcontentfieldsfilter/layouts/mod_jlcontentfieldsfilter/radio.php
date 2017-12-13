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

$field = $displayData['field'];
$label = JText::_($field->label);
$value = is_array($field->value) ? $field->value : array();
$options = (array)$field->fieldparams->get('options', array());
$moduleParams = $displayData['params'];
$count_cols = (int)$moduleParams->get('count_cols', 2);
$width = (int)(100/$count_cols);

if(!is_array($options) || !count($options)){
	return;
}

?>
<div class="control-group">
    <h4>
		<?php echo $label; ?>
    </h4>
    <div class="controls">

		<?php $i = 1; ?>
		<?php foreach($options as $k => $v) : ?>
		<?php $checked = in_array($v->value, $value) ? ' checked="checked"' : ''; ?>
        <label class="span6" for="<?php echo $field->name.'_'. $i; ?>"
               style="margin-left: 0px; margin-right: 0px; width: <?php echo $width; ?>%;">
            <input
                    type="radio"
                    value="<?php echo $v->value; ?>"
                    id="<?php echo $field->name.'_'. $i; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>][]"<?php echo $checked; ?>
            />
			<?php echo $v->name; ?>
        </label>
		<?php if($i % $count_cols == 0) : ?>
    </div>
    <div style="clear: both"></div>
    <div class="controls">
		<?php endif; ?>

		<?php $i++; ?>
		<?php endforeach; ?>
    </div>
    <input type="button"
           class="btn btn-small"
           value="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RADIO_RESET')?>"
           onclick="JlContentFieldsFilter.clearRadio(this);"
    >
</div>
