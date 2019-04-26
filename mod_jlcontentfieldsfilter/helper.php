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

require_once JPATH_ROOT. '/administrator/components/com_fields/helpers/fields.php';

class ModJlContentFieldsFilterHelper
{
	public static function getFields($params, $category_id, $values, $moduleId, $option)
	{
		$app = JFactory::getApplication();
		$fields = array();
		$template = $app->getTemplate();

		$context = '';

		if($option == 'com_content'){
			$context = 'com_content.article';
		}
		else if($option == 'com_contact'){
			$context = 'com_contact.contact';
		}


		$item = new stdClass();
		$item->language = $app->getLanguage()->getTag();
		$item->catid = $category_id;

		$fields = FieldsHelper::getFields($context, $item);
		if(count($fields)){
			$fieldIds = array_map(
				function ($f)
				{
					return $f->id;
				},
				$fields
			);

			$new = array();
            $usedFieldIds = array();

			foreach ($fields as $key => $original)
			{
			    if(in_array($original->id, $usedFieldIds)){
			        continue;
                }
                $usedFieldIds[] = $original->id;
				$field = clone $original;
				$field->value = isset($values[$field->id]) ? $values[$field->id] : '';
				$field->rawvalue = $field->value;

				$content_filter = $original->params->get('content_filter', '');

				$disabled_categories = $original->params->get('disabled_categories', array());
				if (in_array($category_id, $disabled_categories))
				{
					continue;
				}

				if(empty($content_filter)){
					unset($fieldIds[$key]);
					continue;
				}

				$filter_layout = $original->params->get('filter_layout', '');
				if(!empty($filter_layout)) {
					$filter_layout = explode(':', $filter_layout);
					$src = $filter_layout[0];
					$layout = $filter_layout[1];
				}
				else {
					$layout = $content_filter;
					$src = '_';
				}

				$basePath = $src === '_'
					? (
						is_file(JPATH_ROOT.'/templates/'.$template.'/html/layouts/mod_jlcontentfieldsfilter/'.$layout.'.php')
							? JPATH_ROOT.'/templates/'.$template.'/html/layouts'
							: JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts'
					)
					: (
						is_file(JPATH_ROOT.'/templates/'.$src.'/html/layouts/mod_jlcontentfieldsfilter/'.$layout.'.php')
							? JPATH_ROOT.'/templates/'.$src.'/html/layouts'
							: JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts'
					);

				$displayData = array('field' => $field, 'params' => $params, 'moduleId' => $moduleId, 'rangedata' => array());

				if(preg_match("/^range?.*?$/isu", $layout)) {
					$displayData = self::addRangeData($displayData, $category_id, $option);
				}

				$new[$key] = JLayoutHelper::render(
					'mod_jlcontentfieldsfilter.'.$layout,
					$displayData,
					$basePath,
					array('component' => 'auto', 'client' => 0, 'suffixes' => array())
				);
			}
			$fields = $new;
		}
		return $fields;
	}

	private static function addRangeData($displayData, $category_id, $option){
		$field = $displayData['field'];
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MIN(CAST(`value` AS SIGNED)) AS `min`, MAX(CAST(`value` AS SIGNED)) AS `max`')
		      ->from('`#__fields_values`')
		      ->where('`field_id` ='.(int)$field->id)
		      ->where('`field_id` ='.(int)$field->id)
		;
		$subquery = '';

		if($option == 'com_tags'){
		    $tagIds = JFactory::getApplication()->input->get('id', array(), 'array');
		    if(!is_array($tagIds)){
                $tagIds = array((int)$tagIds);
            }
            $tagIds = implode(', ', $tagIds);
            $q = $db->getQuery(true);
            $q->select('content_item_id')
                ->from('#__contentitem_tag_map')
                ->where('type_alias = '.$db->quote('com_content.article'))
                ->where('tag_id IN('.$tagIds.')')
            ;
            $subquery = (string)$q;
        }
		else if($option == 'com_content')
		{
            $params = JComponentHelper::getParams('com_content');
            $showSubcategories = $params->get('show_subcategory_content', '0');

            if($showSubcategories != 0){
                $q = $db->getQuery(true);
                $q->select('id, lft, rgt, level')
                    ->from('#__categories')
                    ->where('id = '.(int)$category_id)
                ;
                $oCat = $db->setQuery($q,0,1)->loadObject();

                if(empty($oCat->id)){
                    return $displayData;
                }

                $q->clear()->select('id')
                    ->from('#__categories')
                    ->where('lft >= '.(int)$oCat->lft)
                    ->where('rgt <= '.(int)$oCat->rgt)
                ;

                if($showSubcategories > 0){
                    $maxLevel = $oCat->level + $showSubcategories;
                    $q->where('level <= '.(int)$maxLevel);
                }

                $aCats = $db->setQuery($q)->loadColumn();

                if(!is_array($aCats) || !count($aCats)){
                    return $displayData;
                }

                $subquery = 'SELECT `id` FROM `#__content` WHERE `catid` IN('.implode(',', $aCats).')';
            }
            else{//No subcategories
                $subquery = 'SELECT `id` FROM `#__content` WHERE `catid` = '.(int)$category_id;
            }

		}
		else if($option == 'com_contact'){
			$subquery = 'SELECT `id` FROM `#__contact_details` WHERE `catid` = '.(int)$category_id;
		}
		if(!empty($subquery)){
			$query->where('`item_id` IN ('.$subquery.')');
		}
		$result = $db->setQuery($query)->loadObject();
		$displayData['min'] = !empty($result->min) ? (int)$result->min : '';
		$displayData['max'] = !empty($result->max) ? (int)$result->max : '';
		return $displayData;
	}

	public static function getOrderingSelect($selectedOrdering, $moduleId, $option){
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$options = array();
		if($option == 'com_content'){
			$options[] = JHtml::_('select.option', '', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DEFAULT'));
			$options[] = JHtml::_('select.option', 'ordering.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_ASC'));
			$options[] = JHtml::_('select.option', 'ordering.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DESC'));
			$options[] = JHtml::_('select.option', 'title.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_TITLE_ASC'));
			$options[] = JHtml::_('select.option', 'title.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_TITLE_DESC'));
			$options[] = JHtml::_('select.option', 'created.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_ASC'));
			$options[] = JHtml::_('select.option', 'created.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_DESC'));
			$options[] = JHtml::_('select.option', 'created_by.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_BY_ASC'));
			$options[] = JHtml::_('select.option', 'created_by.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_BY_DESC'));
			$options[] = JHtml::_('select.option', 'hits.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_ASC'));
			$options[] = JHtml::_('select.option', 'hits.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_DESC'));
		}
		else if($option == 'com_contact'){
			$options[] = JHtml::_('select.option', '', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DEFAULT'));
			$options[] = JHtml::_('select.option', 'ordering.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_ASC'));
			$options[] = JHtml::_('select.option', 'ordering.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DESC'));
			$options[] = JHtml::_('select.option', 'name.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_NAME_ASC'));
			$options[] = JHtml::_('select.option', 'name.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_NAME_DESC'));
			$options[] = JHtml::_('select.option', 'position.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_POSITION_ASC'));
			$options[] = JHtml::_('select.option', 'position.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_POSITION_DESC'));
			$options[] = JHtml::_('select.option', 'hits.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_ASC'));
			$options[] = JHtml::_('select.option', 'hits.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_DESC'));
		}

		$basePath = is_file(JPATH_ROOT.'/templates/'.$template.'/html/layouts/mod_jlcontentfieldsfilter/ordering.php')
			? JPATH_ROOT.'/templates/'.$template.'/html/layouts'
			: JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts';

		$html = JLayoutHelper::render(
			'mod_jlcontentfieldsfilter.ordering',
			array('options' => $options, 'selected' => $selectedOrdering, 'moduleId' => $moduleId),
			$basePath,
			array('component' => 'auto', 'client' => 0)
		);

		return $html;
	}

	public static function countCatArticles($catid){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from('`#__content`')
            ->where('`catid` ='.(int)$catid)
            ->where('`state` = 1')
        ;

        return $db->setQuery($query)->loadResult();
    }
}
