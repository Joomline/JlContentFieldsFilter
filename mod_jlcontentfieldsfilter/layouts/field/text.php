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
                    id="<?php echo $field->name; ?>"
                    name="jlcontentfieldsfilter[<?php echo $field->id; ?>]"
            />


    </div>
</div>
