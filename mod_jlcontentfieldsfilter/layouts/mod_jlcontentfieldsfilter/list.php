<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright    (C) 2017-2020 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see    LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$field    = $displayData['field'];
$label    = Text::_($field->label);
if (!empty($field->hidden))
{
	return;
}
$value       = $field->value;
$listOptions = (array) $field->fieldparams->get('options', []);
$options     = [];
if (is_array($listOptions))
{
	foreach ($listOptions as $listOption)
	{
		if (empty($listOption->hidden))
		{
			$options[] = HTMLHelper::_('select.option', $listOption->value, $listOption->name);
		}
	}
}
if (!count($options))
{
	return;
}
?>
<label class="jlmf-label" for="<?php echo $field->name . '-' . $moduleId; ?>"><?php echo $label; ?></label>
<select
        name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
        id="<?php echo $field->name . '-' . $moduleId; ?>"
        class="jlmf-select"
>
    <option value=""><?php echo Text::_('JSELECT'); ?></option>

	<?php
	foreach ($options as $k => $v) :
		$checked = ($v->value == $value) ? ' selected="selected"' : '';
		?>
        <option value="<?php echo $v->value; ?>" <?php echo $checked; ?>><?php echo Text::_($v->text); ?></option>
	<?php endforeach; ?>
</select>
