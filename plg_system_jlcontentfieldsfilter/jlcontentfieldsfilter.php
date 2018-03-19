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

class plgSystemJlContentFieldsFilter extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	public function onContentPrepareForm($form, $data)
	{
		if(!($form instanceof JForm))
		{
			return false;
		}

		$name = $form->getName();

		$app = JFactory::getApplication();

		if(!in_array($name, array( 'com_fields.fieldcom_content.article', 'com_fields.fieldcom_contact.contact' )) || !$app->isAdmin())
		{
			return true;
		}


		JForm::addFormPath(__DIR__ . '/params');
		$form->loadFile('params', false);

		$dataType = (is_object($data))? $data->type : $data['type'];
		if (empty($dataType)) $dataType = $form->getFieldAttribute('type', 'default');
		$form->setFieldAttribute('content_filter', 'dataType', $dataType, 'params');

		return true;
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

		$app = JFactory::getApplication();
		$input = $app->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$catid = $input->getInt('id', 0);

		if(!in_array($option, array('com_content', 'com_contact')) || $view != 'category' || $catid == 0)
		{
			return;
		}

		$filterData = $app->getUserStateFromRequest($option.'.cat_'.$catid.'.jlcontentfieldsfilter', 'jlcontentfieldsfilter', array(), 'array');
		$itemid = $app->input->get('id', 0, 'int') . ':' . $app->input->get('Itemid', 0, 'int');

		if(!count($filterData))
		{
			return;
		}

		if($option == 'com_content' && !class_exists('ContentModelCategory'))
		{
			require_once __DIR__.'/models/com_content/category.php';
			$context = 'com_content.article';
		}
		else if($option == 'com_contact' && !class_exists('ContactModelCategory'))
		{
			require_once __DIR__.'/models/com_contact/category.php';
			$context = 'com_contact.contact';
		}


		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, type');
		$query->from('#__fields');
		$query->where('context = '.$db->quote($context));
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
		$context = $option.'.category.list.' . $itemid;

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

			$app->setUserState($context . 'filter.article_id_include', true);
			$app->setUserState($context . 'filter.article_id', $result);
		}
		else{
			$app->setUserState($context . 'filter.article_id_include', null);
			$app->setUserState($context . 'filter.article_id', null);
		}

		if(!empty($filterData['ordering']))
		{
			list($ordering, $dirn) = explode('.', $filterData['ordering']);
			$dirn = !empty($dirn) ? strtoupper($dirn) : 'ASC';

			$ordering = '';

			switch ($option){
				case 'com_content':
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
					}
					break;
				case 'com_contact':
					switch ($ordering){
						case 'ordering':
							$ordering = 'a.ordering';
							break;
						case 'name':
							$ordering = 'a.name';
						case 'position':
							$ordering = 'a.con_position';
							break;
						case 'hits':
							$ordering = 'a.hits';
							break;
					}
					break;
			}

			if(!empty($ordering)){
				$app->setUserState($option.'.category.list.' . $itemid . '.filter_order', $ordering);
				$app->setUserState($option.'.category.list.' . $itemid . '.filter_order_Dir', $dirn);
			}
		}
	}
}
