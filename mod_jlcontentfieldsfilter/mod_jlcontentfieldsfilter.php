<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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

if($view == 'category')
{
    $catid = $id;
}

if($option != 'com_content' || (!in_array($catid, $allowedCats) && $allowedCats[0] != -1) || $catid == 0)
{
    return;
}

JHtml::_('behavior.framework');

$action = JRoute::_(ContentHelperRoute::getCategoryRoute($catid));

if(count($jlContentFieldsFilter))
{
    $app->setUserState('cat_'.$catid.'.jlcontentfieldsfilter', $jlContentFieldsFilter);
}
else{
	$jlContentFieldsFilter = $app->getUserState('cat_'.$catid.'.jlcontentfieldsfilter', array());
}

$fields = ModJlContentFieldsFilterHelper::getFields($params, $catid, $jlContentFieldsFilter);

if(count($fields)){
	require JModuleHelper::getLayoutPath('mod_jlcontentfieldsfilter', $params->get('layout', 'default'));
}

