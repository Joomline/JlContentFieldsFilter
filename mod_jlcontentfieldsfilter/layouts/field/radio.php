<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
        <label class="span6" for="<?php echo $field->name.'_'. $i; ?>">
            <input
                    type="radio"
                    value="<?php echo $v->value; ?>"
                    id="<?php echo $field->name.'_'. $i; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>][]"<?php echo $checked; ?>
            />
			<?php echo $v->name; ?>
        </label>
		<?php if($i % 2 == 0) : ?>
    </div>
    <div class="controls">
		<?php endif; ?>

		<?php $i++; ?>
		<?php endforeach; ?>
    </div>
    <input type="button"
           class="btn btn-small"
           value="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RADIO_RESET')?>"
           onclick="clearJlContentFieldsRadio(this);"
    >
</div>
