<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2020 Aleksey Morozov, Arkadiy Sedelnikov, Joomline. All rights reserved.
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

		if ($option == 'com_content') {
			$context = 'com_content.article';
		} elseif ($option == 'com_contact') {
			$context = 'com_contact.contact';
		}


		$item = new stdClass();
		$item->language = $app->getLanguage()->getTag();
		$item->catid = $category_id;

		$fields = FieldsHelper::getFields($context, $item);
		if (count($fields)) {
			$fieldIds = array_map(
				function ($f) {
					return $f->id;
				},
				$fields
			);

			$new = array();
			$usedFieldIds = array();

			foreach ($fields as $key => $original) {
				if (in_array($original->id, $usedFieldIds)) {
					continue;
				}
				$usedFieldIds[] = $original->id;
				$field = clone $original;
				$field->value = isset($values[$field->id]) ? $values[$field->id] : '';
				$field->rawvalue = $field->value;

				$content_filter = $original->params->get('content_filter', '');

				$disabled_categories = $original->params->get('disabled_categories', array());
				if (in_array($category_id, $disabled_categories)) {
					continue;
				}

				if (empty($content_filter)) {
					unset($fieldIds[$key]);
					continue;
				}

				$filter_layout = $original->params->get('filter_layout', '');
				if (!empty($filter_layout)) {
					$filter_layout = explode(':', $filter_layout);
					$src = $filter_layout[0];
					$layout = $filter_layout[1];
				} else {
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

                if ($field->params->get('field_hidden', false) || $field->params->get('options_hidden', false)) {
                    $field = self::setHiddenOptions($field, $category_id, $option);
                }

				$displayData = array('field' => $field, 'params' => $params, 'moduleId' => $moduleId, 'rangedata' => array(), 'articleoptions');

				if (preg_match("/^range?.*?$/isu", $layout)) {
					$displayData = self::addRangeData($displayData, $category_id, $option);
				}

				if ($layout == 'articles') {
					$displayData = self::addArticlesFieldData($displayData);
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

	private static function setHiddenOptions($field, $category_id, $context)
	{

		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		$db = JFactory::getDbo();

		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSql());

		$articlesQuery = '
			select a.id
			from #__content AS a
			left join #__categories AS c ON c.id = a.catid
			where (a.access in (' . $groups . '))
				and (c.access in (' . $groups . '))
				and (c.published = 1)
				and (a.state = 1)
				and (a.catid = ' . (int)$category_id . ')
				and ((a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . '))
				and ((a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . '))
			';

		$contactsQuery = '
			select a.id
			from #__contact_details AS a
			left join #__categories AS c ON c.id = a.catid
			where (a.access in (' . $groups . '))
				and (c.access in (' . $groups . '))
				and (c.published = 1)
				and (a.state = 1)
				and (a.catid = ' . (int)$category_id . ')
				and ((a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . '))
				and ((a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . '))
			';

		$tagsQuery = '
			select a.id
			from #__tags AS a
			where (a.access in (' . $groups . '))
				and (c.access in (' . $groups . '))
				and (c.published = 1)
				and ((a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . '))
				and ((a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . '))
			';

		switch ($context) {
			case 'com_content':
				$subQuery = $articlesQuery;
				break;
			case 'com_contact':
				$subQuery = $contactsQuery;
				break;
			case 'com_tags':
				$subQuery = $tagsQuery;
				break;
		}

		$query = '
			select %s
			from #__fields_values
			where (field_id = ' . (int)$field->id . ')
				and (item_id in (' . $subQuery . '))
				%s
			';
		if (in_array($field->type, ['checkboxes', 'list', 'radio'])) {
			$options = (array)$field->fieldparams->get('options', []);
			$hidden = false;
			$q = '';
			$firstKeyOption = '';
			foreach ($options as $key => $option) {
				if ($key == array_key_first($options)) {
					$firstKeyOption = $key;
					continue;
				}
				$tmp = sprintf($query, 'count(field_id)', 'and (value = ' . $db->quote($option->value) . ')');
				$q .= ', (' . $tmp . ') as `' . $key . '`';
			}
			$q = sprintf($query, 'count(field_id) as `' . $firstKeyOption . '`' . $q, 'and (value = ' . $db->quote($options[$firstKeyOption]->value) . ')');
			$cnt = $db->setQuery($q)->loadObject();
			foreach ($options as $key => $option) {
				$options[$key]->hidden = $cnt->$key == 0;
				$hidden = !$hidden ? false : $cnt->$key == 0;
			}
			$field->fieldparams->set('options', $options);
			$field->hidden = $hidden;
		} else {
			$query = sprintf($query, 'count(field_id)', '');
			$cnt = $db->setQuery($query)->loadResult();
			$field->hidden = $cnt == 0;
		}
		return $field;
	}

	private static function addRangeData($displayData, $category_id, $option)
	{
		$field = $displayData['field'];
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MIN(CAST(`value` AS SIGNED)) AS `min`, MAX(CAST(`value` AS SIGNED)) AS `max`')
			->from('`#__fields_values`')
			->where('`field_id` ='.(int)$field->id)
			->where('`field_id` ='.(int)$field->id)
		;
		$subquery = '';

		if ($option == 'com_tags') {
			$tagIds = JFactory::getApplication()->input->get('id', array(), 'array');
			if (!is_array($tagIds)) {
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
		} elseif ($option == 'com_content') {
			$params = JComponentHelper::getParams('com_content');
			$showSubcategories = $params->get('show_subcategory_content', '0');

			if ($showSubcategories != 0) {
				$q = $db->getQuery(true);
				$q->select('id, lft, rgt, level')
					->from('#__categories')
					->where('id = '.(int)$category_id)
				;
				$oCat = $db->setQuery($q, 0, 1)->loadObject();

				if (empty($oCat->id)) {
					return $displayData;
				}

				$q->clear()->select('id')
					->from('#__categories')
					->where('lft >= '.(int)$oCat->lft)
					->where('rgt <= '.(int)$oCat->rgt)
				;

				if ($showSubcategories > 0) {
					$maxLevel = $oCat->level + $showSubcategories;
					$q->where('level <= '.(int)$maxLevel);
				}

				$aCats = $db->setQuery($q)->loadColumn();

				if (!is_array($aCats) || !count($aCats)) {
					return $displayData;
				}

				$subquery = 'SELECT `id` FROM `#__content` WHERE `catid` IN('.implode(',', $aCats).')';
			} else {//No subcategories
				$subquery = 'SELECT `id` FROM `#__content` WHERE `catid` = '.(int)$category_id;
			}
		} elseif ($option == 'com_contact') {
			$subquery = 'SELECT `id` FROM `#__contact_details` WHERE `catid` = '.(int)$category_id;
		}
		if (!empty($subquery)) {
			$query->where('`item_id` IN ('.$subquery.')');
		}
		$result = $db->setQuery($query)->loadObject();
		$displayData['min'] = !empty($result->min) ? (int)$result->min : 0;
		$displayData['max'] = !empty($result->max) ? (int)$result->max : 0;
		return $displayData;
	}

	private static function addArticlesFieldData($displayData) {
		$field = $displayData['field'];
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
        $query->select('DISTINCT A.`value`, B.`title`')
			->from('`#__fields_values` A')
			->leftJoin('`#__content` B ON A.`value` = B.`id`')
			->where('A.`field_id` ='.(int)$field->id)
			->order('A.`value` ASC');
		;
		$displayData['articleoptions'] = $db->setQuery($query)->loadObjectList();
		return $displayData;
	}

	public static function getOrderingSelect($selectedOrdering, $moduleId, $option)
	{
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$options = array();
		if ($option == 'com_content') {
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
		} elseif ($option == 'com_contact') {
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

	public static function countCatArticles($catid)
	{
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

if (!function_exists('array_key_first')) {
    function array_key_first(array $array)
    {
        foreach ($array as $key => $unused) {
            return $key;
        }
        return null;
    }
}
