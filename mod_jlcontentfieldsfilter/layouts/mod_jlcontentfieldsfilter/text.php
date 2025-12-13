<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
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
$field    = $displayData['field'];
if (!empty($field->hidden))
{
	return;
}
$label = Text::_($field->label);
$value = $field->value;
?>

<label class="jlmf-label" for="<?php echo $field->name . '-' . $moduleId; ?>"><?php echo $label; ?></label>
<input
        type="text"
        value="<?php echo $value; ?>"
        id="<?php echo $field->name . '-' . $moduleId; ?>"
        name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
        class="jlmf-input"
/>
