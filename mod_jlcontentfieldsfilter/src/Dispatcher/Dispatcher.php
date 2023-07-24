<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Module\Jlcontentfieldsfilter\Site\Dispatcher;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentHelperRoute;
use Joomla\Component\Contact\Site\Helper\RouteHelper as ContactRouteHelper;

/**
 * Dispatcher class for mod_Jlcontentfieldsfilter
 *
 * @since  1.0.0
 */
class Dispatcher extends AbstractModuleDispatcher
{
	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();
		$app = $this->getApplication();
		$helper = $app->bootModule('mod_jlcontentfieldsfilter', 'Site')->getHelper('JlcontentfieldsfilterHelper');
		$input = $app->getInput();
		$option = $data['option'] = $input->getString('option', '');
		$view = $input->getString('view', '');
		$catid = $input->getInt('catid', 0);
		$id = $input->getInt('id', 0);

		$hide_if_empty_category = ($data['params'])->get('hide_if_empty_category', 0);
		$show_only_category_page = ($data['params'])->get('show_only_category_page', 0);

		if($show_only_category_page && $view != 'category'){
			return $data;
		}

		if($view == 'category')
		{
			$catid = $id;

			if($hide_if_empty_category && !$helper->countCatArticles($catid)){
				return $data;
			}
		}


		$enabledComponents = ($data['params'])->get('enabled_components', array());
		$allowedCats = ($data['params'])->get('categories', array());
		$allowedContactCats = ($data['params'])->get('contact_categories', array());
		$data['moduleclass_sfx'] = ($data['params'])->get('moduleclass_sfx', '');
		$data['form_method'] = ($data['params'])->get('form_method', 'post');
		$data['autho_send'] = (int)($data['params'])->get('autho_send', 0);
		$data['ajax'] = (int)($data['params'])->get('ajax', 0);
		$data['ajax_selector'] = ($data['params'])->get('ajax_selector', '#content');
		$data['enableOrdering'] = $enableOrdering = ($data['params'])->get('enable_ordering', 0);

		$ajax_loader = ($data['params'])->get('ajax_loader', '');
		$data['ajax_loader'] = !empty(($ajax_loader)) ? Uri::root().$ajax_loader : '';
		$data['ajax_loader_width'] = (int)($data['params'])->get('ajax_loader_width', 32);


		if($option == 'com_tags'){
			if($view != 'tag' || !in_array($option, $enabledComponents)){
				return $data;
			}
			$allowedTags = ($data['params'])->get('tags_tags', array());
			$catid = (int)($data['params'])->get('tags_fields_category', 0);
			$tagIds = $input->get('id', array(), 'array');
			if(!count(array_intersect($allowedTags, $tagIds))){
				return $data;
			}
		}
		else if(
			!in_array($option, $enabledComponents)
			|| ($option == 'com_content' && !(!count($allowedCats) || in_array($catid, $allowedCats) || $allowedCats[0] == -1))
			|| ($option == 'com_contact' && !(!count($allowedContactCats) || in_array($catid, $allowedContactCats) || $allowedContactCats[0] == -1))
			|| $catid == 0
		)
		{
			return $data;
		}

		if($option == 'com_tags'){
			$context = $option.'.cat_'.implode('_', $tagIds).'.jlcontentfieldsfilter';
		}
		else{
			$context = $option.'.cat_'.$catid.'.jlcontentfieldsfilter';
		}

		$jlContentFieldsFilter = $app->getUserStateFromRequest($context, 'jlcontentfieldsfilter', array(), 'array');

		if($option == 'com_content'){
			$data['action'] = Route::_(ContentHelperRoute::getCategoryRoute($catid));
		}
		else if($option == 'com_contact'){
			$data['action'] = Route::_(ContactRouteHelper::getCategoryRoute($catid));
		}
		else
		{

			$menus    = Factory::getContainer()->get(MenuFactoryInterface::class)->createMenu('site');
			$active = $menus->getActive();
			$data['action'] = Route::_($active->link.'&Itemid='.$active->id);

		}

		$data['fields'] = $helper->getFields(($data['params']), $catid, $jlContentFieldsFilter, $data['module']->id, $option);

		if(count($data['fields'])){
			if($enableOrdering){
				$selectedOrdering = !empty($jlContentFieldsFilter['ordering']) ? $jlContentFieldsFilter['ordering'] : '';
				$data['orderingSelect'] = $helper->getOrderingSelect($selectedOrdering, $data['module']->id, $option);
			}
		}

		return $data;
	}
}