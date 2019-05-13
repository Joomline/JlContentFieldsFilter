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

// Include the helper.
require_once __DIR__ . '/helper.php';
require_once JPATH_ROOT. '/components/com_content/helpers/route.php';

$app = JFactory::getApplication();
$input = $app->input;

$option = $input->getString('option', '');
$view = $input->getString('view', '');
$catid = $input->getInt('catid', 0);
$id = $input->getInt('id', 0);

$hide_if_empty_category = $params->get('hide_if_empty_category', 0);
$show_only_category_page = $params->get('show_only_category_page', 0);

if($show_only_category_page && $view != 'category'){
    return;
}

if($view == 'category')
{
	$catid = $id;

    if($hide_if_empty_category && !ModJlContentFieldsFilterHelper::countCatArticles($catid)){
        return;
    }
}


$enabledComponents = $params->get('enabled_components', array());
$allowedCats = $params->get('categories', array());
$allowedContactCats = $params->get('contact_categories', array());
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$form_method = $params->get('form_method', 'post');
$autho_send = (int)$params->get('autho_send', 0);
$ajax = (int)$params->get('ajax', 0);
$ajax_selector = $params->get('ajax_selector', '#content');
$enableOrdering = $params->get('enable_ordering', 0);
$ajax_loader = $params->get('ajax_loader', '');
$ajax_loader = !empty(($ajax_loader)) ? JUri::root().$ajax_loader : '';
$ajax_loader_width = (int)$params->get('ajax_loader_width', 32);



if($option == 'com_tags'){
    if($view != 'tag' || !in_array($option, $enabledComponents)){
        return;
    }
    $allowedTags = $params->get('tags_tags', array());
    $catid = (int)$params->get('tags_fields_category', 0);
    $tagIds = $input->get('id', array(), 'array');
    if(!count(array_intersect($allowedTags, $tagIds))){
        return;
    }
}
else if(
	!in_array($option, $enabledComponents)
    || ($option == 'com_content' && !(!count($allowedCats) || in_array($catid, $allowedCats) || $allowedCats[0] == -1))
    || ($option == 'com_contact' && !(!count($allowedContactCats) || in_array($catid, $allowedContactCats) || $allowedContactCats[0] == -1))
	|| $catid == 0
)
{
    return;
}

if($option == 'com_tags'){
    $context = $option.'.cat_'.implode('_', $tagIds).'.jlcontentfieldsfilter';
}
else{
    $context = $option.'.cat_'.$catid.'.jlcontentfieldsfilter';
}

$jlContentFieldsFilter = $app->getUserStateFromRequest($context, 'jlcontentfieldsfilter', array(), 'array');

if($option == 'com_content'){
	$action = JRoute::_(ContentHelperRoute::getCategoryRoute($catid));
}
else if($option == 'com_contact'){
	$action = JRoute::_(ContactHelperRoute::getCategoryRoute($catid));
}
else{
	$action = JRoute::_(TagsHelperRoute::getTagsRoute());
}

$fields = ModJlContentFieldsFilterHelper::getFields($params, $catid, $jlContentFieldsFilter, $module->id, $option);

if(count($fields)){
	if($enableOrdering){
		$selectedOrdering = !empty($jlContentFieldsFilter['ordering']) ? $jlContentFieldsFilter['ordering'] : '';
		$orderingSelect = ModJlContentFieldsFilterHelper::getOrderingSelect($selectedOrdering, $module->id, $option);
	}
	require JModuleHelper::getLayoutPath('mod_jlcontentfieldsfilter', $params->get('layout', 'default'));
}

