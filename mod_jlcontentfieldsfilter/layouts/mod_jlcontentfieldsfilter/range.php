<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$min = $displayData['min'];
$max = $displayData['max'];
$field = $displayData['field'];
$label = JText::_($field->label);
$value = $field->value;

$fromPlaceholder = $min !== '' ? JText::sprintf('MOD_JLCONTENTFIELDSFILTER_MIN', $min) : '';
$toPlaceholder = $max !== '' ? JText::sprintf('MOD_JLCONTENTFIELDSFILTER_MAX', $max) : '';
?>
<div class="control-group">
    <h4>
		<?php echo $label; ?>
    </h4>
    <div class="controls">
    <div class="row-fluid">
        <div class="span1"><?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_FROM');?></div>
        <div class="span4">
            <input
                    type="text"
                    placeholder="<?php echo $fromPlaceholder; ?>"
                    value="<?php echo !empty($value['from']) ? $value['from'] : ''; ?>"
                    id="<?php echo $field->name.'-'.$moduleId; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>][from]"
                    style="width: 100%"
            />
        </div>
        <div class="span1 offset1"><?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_TO');?></div>
        <div class="span4">
            <input
                    type="text"
                    placeholder="<?php echo $toPlaceholder; ?>"
                    value="<?php echo !empty($value['to']) ? $value['to'] : ''; ?>"
                    id="<?php echo $field->name.'-'.$moduleId; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>][to]"
                    style="width: 100%"
            />
        </div>
    </div>
    </div>
</div>
