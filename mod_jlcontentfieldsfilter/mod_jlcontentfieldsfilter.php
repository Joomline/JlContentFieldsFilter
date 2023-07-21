<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Component\Contact\Site\Helper\RouteHelper as ContactRouteHelper;
use Joomla\Component\Tags\Site\Helper\RouteHelper as TagsRouteHelper;
// Include the helper.
require_once __DIR__ . '/helper.php';



if(count($fields)){
	if($enableOrdering){
		$selectedOrdering = !empty($jlContentFieldsFilter['ordering']) ? $jlContentFieldsFilter['ordering'] : '';
		$orderingSelect = ModJlContentFieldsFilterHelper::getOrderingSelect($selectedOrdering, $module->id, $option);
	}
	require JModuleHelper::getLayoutPath('mod_jlcontentfieldsfilter', $params->get('layout', 'default'));
}

