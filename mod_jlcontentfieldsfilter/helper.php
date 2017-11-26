<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT. '/administrator/components/com_fields/helpers/fields.php';

class ModJlContentFieldsFilterHelper
{
	public static function getFields($params, $category_id, $values)
	{
		$app = JFactory::getApplication();
		$fields = array();
        $enabledFields = $params->get('searchfields', array());

        if(!is_array($enabledFields) && !count($enabledFields))
        {
            return $fields;
        }

		$context = 'com_content.article';

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

			foreach ($fields as $key => $original)
			{
				if(!in_array($original->id, $enabledFields)){
					unset($fieldIds[$key]);
					continue;
				}

				$field = clone $original;
				$field->value = isset($values[$field->id]) ? $values[$field->id] : '';
				$field->rawvalue = $field->value;

				$new[$key] = JLayoutHelper::render(
					'field.'.$field->type,
					array('field' => $field),
					JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts',
					array('component' => 'auto', 'client' => 0)
				);
			}
			$fields = $new;
		}
		return $fields;
	}
}
