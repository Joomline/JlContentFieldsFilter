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
?>
<div class="control-group">
    <h4>
		<?php echo $label; ?>
    </h4>
    <div class="controls">

            <input
                    type="text"
                    value="<?php echo $value; ?>"
                    id="<?php echo $field->name . $uid; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
            />


    </div>
</div>
