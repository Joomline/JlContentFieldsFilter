<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomla All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

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

$fromPlaceholder = $min !== '' ? Text::sprintf('MOD_JLCONTENTFIELDSFILTER_MIN', $min) : '';
$toPlaceholder   = $max !== '' ? Text::sprintf('MOD_JLCONTENTFIELDSFILTER_MAX', $max) : '';
?>
<div class="jlmf-label"><?php echo $label; ?></div>
<div class="jlmf-list-2">
    <div>
        <label class="jlmf-label"
               for="<?php echo $field->name . '-from-' . $moduleId; ?>"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_FROM'); ?></label>
        <input
                type="text"
                placeholder="<?php echo $fromPlaceholder; ?>"
                value="<?php echo !empty($value['from']) ? $value['from'] : ''; ?>"
                id="<?php echo $field->name . '-from-' . $moduleId; ?>"
                name="jlcontentfieldsfilter[<?php echo $field->id; ?>][from]"
                class="jlmf-input"
        />
    </div>
    <div>
        <label class="jlmf-label"
               for="<?php echo $field->name . '-to-' . $moduleId; ?>"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_TO'); ?></label>
        <input
                type="text"
                placeholder="<?php echo $toPlaceholder; ?>"
                value="<?php echo !empty($value['to']) ? $value['to'] : ''; ?>"
                id="<?php echo $field->name . '-to-' . $moduleId; ?>"
                name="jlcontentfieldsfilter[<?php echo $field->id; ?>][to]"
                class="jlmf-input"
        />
    </div>
</div>
