<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright    (C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see    LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (!key_exists('options', $displayData))
{
	return;
}

$moduleId = $displayData['moduleId'];
$options  = $displayData['options'];
$selected = $displayData['selected'];

if (!is_array($options) || !count($options))
{
	return;
}

?>
<label class="jlmf-label"
       for="jlcontentfieldsfilter-ordering-<?php echo $moduleId; ?>"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_ORDERING'); ?></label>
<?php
echo HTMLHelper::_('select.genericlist', $options, 'jlcontentfieldsfilter[ordering]',
	'class="jlmf-select" ', 'value', 'text', $selected, 'jlcontentfieldsfilter-ordering-' . $moduleId);
?>


