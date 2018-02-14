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

// Include the helper.
require_once __DIR__ . '/helper.php';
require_once JPATH_ROOT. '/components/com_content/helpers/route.php';

$app = JFactory::getApplication();
$input = $app->input;

$option = $input->getString('option', '');
$view = $input->getString('view', '');
$catid = $input->getInt('catid', 0);
$id = $input->getInt('id', 0);
$jlContentFieldsFilter = $input->get('jlcontentfieldsfilter', array(), 'array');

$allowedCats = $params->get('categories', array());
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$form_method = $params->get('form_method', 'post');
$autho_send = (int)$params->get('autho_send', 0);
$ajax = (int)$params->get('ajax', 0);
$ajax_selector = $params->get('ajax_selector', '#content');
$enableOrdering = $params->get('enable_ordering', 0);
$ajax_loader = $params->get('ajax_loader', '');
$ajax_loader = !empty(($ajax_loader)) ? JUri::root().$ajax_loader : '';
$ajax_loader_width = (int)$params->get('ajax_loader_width', 32);

if($view == 'category')
{
    $catid = $id;
}

if($option != 'com_content' || (!in_array($catid, $allowedCats) && $allowedCats[0] != -1) || $catid == 0)
{
    return;
}

$action = JRoute::_(ContentHelperRoute::getCategoryRoute($catid));

if(count($jlContentFieldsFilter))
{
    $app->setUserState('cat_'.$catid.'.jlcontentfieldsfilter', $jlContentFieldsFilter);
}
else{
	$jlContentFieldsFilter = $app->getUserState('cat_'.$catid.'.jlcontentfieldsfilter', array());
}

$fields = ModJlContentFieldsFilterHelper::getFields($params, $catid, $jlContentFieldsFilter, $module->id);

if(count($fields)){
	if($enableOrdering){
		$selectedOrdering = !empty($jlContentFieldsFilter['ordering']) ? $jlContentFieldsFilter['ordering'] : '';
		$orderingSelect = ModJlContentFieldsFilterHelper::getOrderingSelect($selectedOrdering, $module->id);
	}
	require JModuleHelper::getLayoutPath('mod_jlcontentfieldsfilter', $params->get('layout', 'default'));
}

