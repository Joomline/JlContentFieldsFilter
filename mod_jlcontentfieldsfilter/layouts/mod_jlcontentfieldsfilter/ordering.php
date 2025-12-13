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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

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


