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

class plgSystemJlContentFieldsFilter extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 *
	 * @param $form
	 * @param $data
	 *
	 * @return bool
	 *
	 * @since version
	 * @throws Exception
	 */
	public function onContentPrepareForm($form, $data)
	{
		if(!($form instanceof JForm))
		{
			return false;
		}

		$name = $form->getName();

		$app = JFactory::getApplication();

		if(!in_array($name, array( 'com_fields.fieldcom_content.article', 'com_fields.field.com_content.article', 'com_fields.fieldcom_contact.contact', 'com_fields.field.com_contact.contact' ))
            || !$app->isAdmin())
		{
			return true;
		}
		else
		{
			$category_extension = explode('.', str_replace(array('com_fields.field.', 'com_fields.field'), '', $name))[0];
		}

		JForm::addFormPath(__DIR__ . '/params');
		$form->loadFile('params', false);

		if(is_object($data) && !empty($data->type)){
			$dataType = $data->type;
		}
		else if(is_array($data) && !empty($data['type'])){
			$dataType = $data['type'];
		}
		else{
			$dataType = $form->getFieldAttribute('type', 'default');
		}

		$form->setFieldAttribute('content_filter', 'dataType', $dataType, 'params');
		$form->setFieldAttribute('disabled_categories', 'extension', $category_extension, 'params');

		return true;
	}

	public function onBeforeCompileHead(){
        if(JFactory::getApplication()->isClient('administrator'))
        {
            return;
        }
        $this->doMeta();
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
        $itemid = $app->input->get('id', 0, 'int') . ':' . $app->input->get('Itemid', 0, 'int');

		if($option == 'com_tags'){
            if($view != 'tag'){
                return;
            }
            $catid = $app->getUserStateFromRequest($option.'.jlcontentfieldsfilter.tag_category_id', 'tag_category_id', 0, 'int');
            $tagids = $app->getUserStateFromRequest($option.'.jlcontentfieldsfilter.tag_ids', 'id', array(), 'array');
            $itemid = implode(',', $tagids) . ':' . $app->input->get('Itemid', 0, 'int');
        }
		else if(!in_array($option, array('com_content', 'com_contact')) || $view != 'category' || $catid == 0)
		{
			return;
		}

        if($option == 'com_tags'){
            $context = $option.'.cat_'.implode('_', $tagids).'.jlcontentfieldsfilter';
        }
        else{
            $context = $option.'.cat_'.$catid.'.jlcontentfieldsfilter';
        }

		$filterData = $app->getUserStateFromRequest($context, 'jlcontentfieldsfilter', array(), 'array');


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
		else if($option == 'com_tags' && !class_exists('TagsModelTag'))
		{
			require_once __DIR__.'//models/com_tags/tag.php';
            $context = 'com_content.article';
		}


		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, type');
		$query->from('#__fields');
		$query->where('context = '.$db->quote($context));
		$fieldsTypes = $db->setQuery($query)->loadObjectList('id');

		$count = 0;
		$filterArticles = array();

		foreach($filterData as $k=>$v)
		{
			if(!isset($fieldsTypes[$k])){
				continue;
			}

			$where = '';

			switch ($fieldsTypes[$k]->type){
				case 'radio':
				case 'checkboxes':
				case 'list':
					if(is_array($v) && count($v)){
						$newVal = array();
						foreach ( $v as $val ) {
							if($val !== '')
								$newVal[] = $val;
						}
						if(count($newVal)){
							$where = '(field_id = '.(int)$k.' AND value IN(\''.implode("', '", $newVal).'\'))';
						}
					}
					else if(!empty($v)){
						$where = '(field_id = '.(int)$k.' AND value = '.$db->quote($v).')';
					}
					break;
				case 'text':
					if(!empty($v)){
						if(is_array($v)){
							if(!empty($v['from']) && !empty($v['to'])){
								$where = '(field_id = '.(int)$k.' AND CAST(`value` AS SIGNED) BETWEEN '.(int)$v['from'].' AND '.$v['to'].')';
							}
							else if(!empty($v['from'])){
								$where = '(field_id = '.(int)$k.' AND CAST(`value` AS SIGNED) >= '.(int)$v['from'].')';
							}
							else if(!empty($v['to'])){
								$where = '(field_id = '.(int)$k.' AND CAST(`value` AS SIGNED) <= '.(int)$v['to'].')';
							}
						}
						else{
							$where = '(field_id = '.(int)$k.' AND value LIKE '.$db->quote('%'.$v.'%').')';
						}
					}
					break;
				default:

					break;
			}

			if(!empty($where)){
				$query->clear()->select(' DISTINCT item_id');
				$query->from('#__fields_values');
				$query->where($where);
				$query->group('item_id');
				$aIds = $db->setQuery($query)->loadColumn();
				$aIds = !is_array($aIds) ? array() : $aIds;

				if($count == 0){
					$filterArticles = $aIds;
				}
				else{
					$filterArticles = array_intersect($filterArticles, $aIds);
				}

				$count++;

				if(!count($filterArticles)){
					break;
				}
			}
		}

		$context = $option.'.category.list.' . $itemid;

		if($count > 0)
		{
			if(!count($filterArticles))
			{
				$filterArticles = array(0);
			}

			$app->setUserState($context . 'filter.article_id_include', true);
			$app->setUserState($context . 'filter.article_id', $filterArticles);
		}
		else{
			$app->setUserState($context . 'filter.article_id_include', null);
			$app->setUserState($context . 'filter.article_id', null);
		}

		if(!empty($filterData['ordering']))
		{
			list($ordering, $dirn) = explode('.', $filterData['ordering']);
			$dirn = !empty($dirn) ? strtoupper($dirn) : 'ASC';

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
                            break;
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

	private function doMeta(){
	    if(!JComponentHelper::isEnabled('com_jlcontentfieldsfilter')){
	        return;
        }

        require_once JPATH_ROOT.'/administrator/components/com_jlcontentfieldsfilter/helpers/jlcontentfieldsfilter.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $option = $input->getString('option', '');
        $view = $input->getString('view', '');
        $catid = $input->getInt('id', 0);

        if(!in_array($option, array('com_content')) || $view != 'category' || $catid == 0)
        {
            return;
        }

        if($option == 'com_tags'){
            $tagids = $app->getUserStateFromRequest($option.'.jlcontentfieldsfilter.tag_ids', 'id', array(), 'array');
            $context = $option.'.cat_'.implode('_', $tagids).'.jlcontentfieldsfilter';
        }
        else{
            $context = $option.'.cat_'.$catid.'.jlcontentfieldsfilter';
        }

        $filterData = $app->getUserStateFromRequest($tagids, 'jlcontentfieldsfilter', array(), 'array');

	    $doc = JFactory::getDocument();

        if(isset($filterData['ordering'])){
            unset($filterData['ordering']);
        }
        if(isset($filterData['is_filter'])){
            unset($filterData['is_filter']);
        }

        if (!is_array($filterData) || !count($filterData)) {
            return;
        }

        $params = JComponentHelper::getParams('com_jlcontentfieldsfilter');
        $autogeneration = $params->get('autogeneration', 0);

        $filter = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $hash = JlcontentfieldsfilterHelper::createHash($filter);


        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('`#__jlcontentfieldsfilter_data`')
            ->where('`filter_hash` = '.$db->quote($hash))
            ->where('`publish`  = 1')
        ;
        $result = $db->setQuery($query,0,1)->loadObject();
        if(empty($result->filter_hash)){
            if(!$autogeneration){
                return;
            }
            else{
                $result = JlcontentfieldsfilterHelper::createMeta($catid, $filterData);
            }
        }

        if(!empty($result->meta_title)){
            $doc->setTitle($result->meta_title);
        }

        if(!empty($result->meta_desc)){
            $doc->setMetaData('description', $result->meta_desc);
        }

        if(!empty($result->meta_keywords)){
            $doc->setMetaData('keywords', $result->meta_keywords);
        }

    }
}
