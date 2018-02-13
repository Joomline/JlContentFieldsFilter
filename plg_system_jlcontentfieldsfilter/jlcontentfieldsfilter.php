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

class plgSystemJlContentFieldsFilter extends JPlugin
{
	public function onContentPrepareForm($form, $data)
	{
		if(!($form instanceof JForm))
		{
			return FALSE;
		}

		$name = $form->getName();

		$app = JFactory::getApplication();

		if(!in_array($name, array( 'com_fields.fieldcom_content.article' )) || !$app->isAdmin())
		{
			return TRUE;
		}


		JFactory::getLanguage()->load('plg_system_jlcontentfieldsfilter', JPATH_ROOT . '/plugins/system/jlcontentfieldsfilter');

		JForm::addFormPath(__DIR__ . '/params');
		$form->loadFile('params', FALSE);

		$dataType = $data->type;
		if($dataType == 'list' && !empty($data->fieldparams["multiple"])){
			$dataType = 'multiselect';
		}

		$form->setFieldAttribute('content_filter', 'dataType', $dataType, 'params');
		$form->setFieldAttribute('content_filter', 'fieldId', $data->id, 'params');

		return TRUE;
	}

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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, type');
		$query->from('#__fields');
		$query->where('context = '.$db->quote('com_content.article'));
		$fieldsTypes = $db->setQuery($query)->loadObjectList('id');

		$query->clear()->select('item_id');
		$query->from('#__fields_values');
		
		$where = array();
		foreach($filterData as $k=>$v)
		{
			if(!isset($fieldsTypes[$k])){
				continue;
			}

			switch ($fieldsTypes[$k]->type){
				case 'radio':
				case 'checkboxes':
				case 'list':
					if(is_array($v) && count($v)){
						$where[] = '(field_id = '.(int)$k.' AND value IN(\''.implode("', '", $v).'\'))';
					}
					else if(!empty($v)){
						$where[] = '(field_id = '.(int)$k.' AND value = '.$db->quote($v).')';
					}
					break;
				case 'text':
					if(!empty($v)){
						$where[] = '(field_id = '.(int)$k.' AND value LIKE '.$db->quote('%'.$v.'%').')';
					}
					break;
				default:

					break;
			}
		}

		$count = count($where);

		if($count > 0){
			$query->where(implode(' OR ', $where));
			$query->having("COUNT(item_id) = " . (int) $count);
			$query->group('item_id');

			$filterArticles = $db->setQuery($query)->loadColumn();
			$filterArticles = (empty($filterArticles)) ? array() : $filterArticles;
			$result = $filterArticles;

			if(!count($result))
			{
				$result = array(0);
			}

			$itemsModel->setState('filter.article_id.include', true);
			$itemsModel->setState('filter.article_id', $result);
		}

		if(!empty($filterData['ordering']))
		{
			list($ordering, $dirn) = explode('.', $filterData['ordering']);
			$dirn = !empty($dirn) ? strtoupper($dirn) : 'ASC';

			switch ($ordering){
				case 'ordering':
					$ordering = 'a.ordering';
					break;
				case 'title':
					$ordering = 'a.title';
					break;
				case 'created':
					$ordering = 'a.created';
					break;
				case 'created_by':
					$ordering = 'a.created_by';
					break;
				case 'hits':
					$ordering = 'a.hits';
					break;
				default:
					$ordering = '';
					break;
			}

			if(!empty($ordering)){
				$itemsModel->setState('list.ordering', $ordering);
				$itemsModel->setState('list.direction', $dirn);
			}
		}
	}
}
