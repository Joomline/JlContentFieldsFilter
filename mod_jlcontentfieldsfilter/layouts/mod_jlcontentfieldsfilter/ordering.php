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

if (!key_exists('options', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$options = $displayData['options'];
$selected = $displayData['selected'];

if(!is_array($options) || !count($options)){
	return;
}

?>
<div class="control-group">
    <h4>
		<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING'); ?>
    </h4>
    <div class="controls">
		<?php
        echo JHtml::_('select.genericlist', $options, 'jlcontentfieldsfilter[ordering]',
			'class="span12" ', 'value', 'text', $selected, 'jlcontentfieldsfilter-ordering-'.$moduleId);
		?>
    </div>
</div>
