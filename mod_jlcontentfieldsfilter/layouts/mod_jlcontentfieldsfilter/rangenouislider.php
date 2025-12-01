<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright    (C) 2017-2020 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if (!key_exists('field', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$min      = $displayData['min'];
$max      = $displayData['max'];
$field    = $displayData['field'];
if (!empty($field->hidden))
{
	return;
}
$label = Text::_($field->label);
$value = $field->value;
$from  = !empty($value['from']) ? $value['from'] : $min;
$to    = !empty($value['to']) ? $value['to'] : $max;

$fromPlaceholder = $min !== '' ? Text::sprintf('MOD_JLCONTENTFIELDSFILTER_MIN', $min) : '';
$toPlaceholder   = $max !== '' ? Text::sprintf('MOD_JLCONTENTFIELDSFILTER_MAX', $max) : '';

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('mod_jlcontentfieldsfilter.nouislider.script', 'mod_jlcontentfieldsfilter/nouislider.min.js')
    ->registerAndUseScript('mod_jlcontentfieldsfilter.sliders.script', 'mod_jlcontentfieldsfilter/sliders.js')
    ->registerAndUseStyle('mod_jlcontentfieldsfilter.nouislider.style', 'mod_jlcontentfieldsfilter/nouislider.min.css')
    ->registerAndUseStyle('mod_jlcontentfieldsfilter.range.style', 'mod_jlcontentfieldsfilter/range.css');

?>
<div class="jlmf-label"><?php echo $label; ?></div>
<div class="jlmf-list-2 range-sliders">
    <div>
        <label class="jlmf-label"
               for="<?php echo $field->name . '-from-' . $moduleId; ?>"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_FROM'); ?></label>
        <input
                type="text"
                placeholder="<?php echo $fromPlaceholder; ?>"
                value="<?php echo $from ?>"
                id="<?php echo $field->name . '-from-' . $moduleId; ?>"
                name="jlcontentfieldsfilter[<?php echo $field->id; ?>][from]"
                class="jlmf-input input-min"
                data-min="<?php echo $min ?>"
        />
    </div>
    <div>
        <label class="jlmf-label"
               for="<?php echo $field->name . '-to-' . $moduleId; ?>"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_TO'); ?></label>
        <input
                type="text"
                placeholder="<?php echo $toPlaceholder; ?>"
                value="<?php echo $to ?>"
                id="<?php echo $field->name . '-to-' . $moduleId; ?>"
                name="jlcontentfieldsfilter[<?php echo $field->id; ?>][to]"
                class="jlmf-input input-max"
                data-max="<?php echo $min ?>"
        />
    </div>

    <div class="jlmf-range-block">
        <div class="jlmf-range" data-min="<?php echo $min ?>" data-max="<?php echo $max ?>"
             data-from="<?php echo $from ?>" data-to="<?php echo $to ?>"></div>
    </div>
</div>
