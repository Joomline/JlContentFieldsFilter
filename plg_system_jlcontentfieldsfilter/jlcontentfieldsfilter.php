<?php
/**
 * @version        1.1 from Arkadiy Sedelnikov
 * @copyright      Copyright (C) 2013 Arkadiy Sedelnikov. All rights reserved.
 * @license        GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;

class plgSystemJlContentFieldsFilter extends JPlugin
{
	/** Подмена модели категории контента.
	 * @throws Exception
	 */
	public function onAfterRoute()
	{
		if(JFactory::getApplication()->isClient('administrator'))
		{
			return;
		}

		$input = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$catid = $input->getInt('catid', 0);
		$id = $input->getInt('id', 0);

		if($view == 'category')
		{
			$catid = $id;
		}

		if($option != 'com_content' || $catid == 0 || class_exists('ContentModelCategory'))
		{
			return;
		}

		require_once __DIR__.'/classes/category.php';
	}

	/**
	 * @param $itemsModel
	 * @throws Exception
	 */
	public function onGetContentItems(&$itemsModel)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$catid = $input->getInt('catid', 0);
		$id = $input->getInt('id', 0);

		if($view == 'category')
		{
			$catid = $id;
		}

		if($catid == 0)
		{
			return;
		}

		$filterData = $app->getUserStateFromRequest('cat_'.$catid.'.jlcontentfieldsfilter', 'jlcontentfieldsfilter', array(), 'array');

		if(!count($filterData))
		{
			return;
		}

		$filterArticles = $catArticles = array();

		$enableFilter = count($filterData) > 0;
		$isFilteredInput = false;

		if($enableFilter)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id, type');
			$query->from('#__fields');
			$query->where('context = '.$db->quote('com_content.article'));
			$fieldsTypes = $db->setQuery($query)->loadObjectList('id');

			$query->clear()->select('`item_id`');
			$query->from('`#__fields_values`');


			$where = array();
			foreach($filterData as $k=>$v)
			{
				if(!isset($fieldsTypes[$k])){
					continue;
				}



				switch ($fieldsTypes[$k]->type){
					case 'radio':
					case 'checkboxes':
						if(is_array($v) && count($v)){
							$where[] = '(`field_id` = '.(int)$k.' AND `value` IN(\''.implode("', '", $v).'\'))';
						}

						break;
					case 'list':
						if(!empty($v)){
							$where[] = '(`field_id` = '.(int)$k.' AND `value` = '.$db->quote($v).')';
						}
						break;
					case 'text':
						if(!empty($v)){
							$where[] = '(`field_id` = '.(int)$k.' AND `value` LIKE '.$db->quote('%'.$v.'%').')';
						}
						break;
					default:

						break;
				}
			}
			$count = count($where);

			if($count == 0){
				return;
			}
			$query->where(implode(' OR ', $where));
			$query->having("COUNT(`item_id`) = " . (int) $count);
			$query->group('item_id');

			$filterArticles = $db->setQuery($query)->loadColumn();
			$filterArticles = (empty($filterArticles)) ? array() : $filterArticles;
			$result = $filterArticles;
		}
		else
		{
			$result = array();
		}

		if(count($result) || $isFilteredInput)
		{
			if(!count($result))
			{
				$result = array(0);
			}

			$itemsModel->setState('filter.article_id.include', true);
			$itemsModel->setState('filter.article_id', $result);
		}
	}
}
