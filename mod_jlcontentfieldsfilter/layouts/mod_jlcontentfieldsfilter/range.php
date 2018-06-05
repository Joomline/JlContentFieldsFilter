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
<div class="jlmf-label"><?php echo $label; ?></div>
<div class="jlmf-list-2">
    <div>
        <label class="jlmf-label" for="<?php echo $field->name.'-from-'.$moduleId; ?>"><?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_FROM');?></label>
        <input
            type="text"
            placeholder="<?php echo $fromPlaceholder; ?>"
            value="<?php echo !empty($value['from']) ? $value['from'] : ''; ?>"
            id="<?php echo $field->name.'-from-'.$moduleId; ?>"
            name="jlcontentfieldsfilter[<?php echo $field->id; ?>][from]"
            class="jlmf-input"
        />
    </div>
    <div>
        <label class="jlmf-label" for="<?php echo $field->name.'-to-'.$moduleId; ?>"><?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_TO');?></label>
        <input
            type="text"
            placeholder="<?php echo $toPlaceholder; ?>"
            value="<?php echo !empty($value['to']) ? $value['to'] : ''; ?>"
            id="<?php echo $field->name.'-to-'.$moduleId; ?>"
            name="jlcontentfieldsfilter[<?php echo $field->id; ?>][to]"
            class="jlmf-input"
        />
    </div>
</div>
