<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper class for jlcontentfieldsfilter component
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterHelper
{
    /**
     * Add submenu.
     *
     * @param   string  $vName  The view name.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function addSubmenu($vName)
    {
        Sidebar::addEntry(
            Text::_('ITEM_SUBMENU'),
            'index.php?option=com_jlcontentfieldsfilter&view=items',
            $vName == 'items');
    }

    /**
     * Get the available actions for the current user.
     *
     * @return  \stdClass  An object with the available actions.
     *
     * @since   1.0.0
     */
    public static function getActions()
    {
        $user = Factory::getApplication()->getIdentity();
        $result = new \stdClass();
        $assetName = 'com_jlcontentfieldsfilter';
	    $actions = Access::getActionsFromFile(
		    JPATH_ADMINISTRATOR . '/components/com_jlcontentfieldsfilter/access.xml',
		    '/access/section[@name="component"]/'
	    );
        foreach ($actions as $action) {
            $result->{$action->name} = $user->authorise($action->name, $assetName);
        }
        return $result;
    }

    /**
     * Create a filter string from filter data array
     *
     * @param   array    $filter  Filter data array
     * @param   boolean  $safe    Whether to URL encode values
     *
     * @return  string  Filter string
     *
     * @since   1.0.0
     */
    public static function createFilterString($filter, $safe=true)
    {
        ksort($filter);
        $data = [];
        foreach ($filter as $key => $item) {
            if (is_array($item)) {
                $val = [];
                ksort($item);
                foreach ($item as $k => $v) {

                    if($k === 'from' || $k === 'to'){
                        continue;
                    }

                    if (!empty($v)) {
                        $val[] = $safe ? urlencode($v) : $v;
                    }
                }
                if (count($val)) {
                    $data[] = $key . '=' . implode(',', $val);
                }
            } else {
                $data[] = $key . '=' . $safe ? urlencode($item) : $item;;
            }
        }

        $data = implode('&', $data);
        return $data;
    }

    /**
     * Create an MD5 hash from a string
     *
     * @param   string  $string  Input string
     *
     * @return  string  MD5 hash
     *
     * @since   1.0.0
     */
    public static function createHash($string)
    {
        return md5($string);
    }

    /**
     * Create meta data object for a filter
     *
     * @param   int    $catid       Category ID
     * @param   array  $filterData  Filter data array
     *
     * @return  \stdClass  Meta data object with title, description and keywords
     *
     * @since   1.0.0
     */
    public static function createMeta($catid, $filterData)
    {
        $object = new \stdClass();
        $object->meta_title = '';
        $object->meta_desc = '';
        $object->meta_keywords = '';

        if(!$catid){
            return $object;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName('title'))
            ->from('#__categories')
            ->where('id = '.(int)$catid)
        ;
        $catName = $db->setQuery($query,0,1)->loadResult();


        $query->clear()->select($db->quoteName(['id', 'title', 'type', 'fieldparams']))
            ->from($db->quoteName('#__fields'))
            ->where('(' . $db->quoteName('context') . ' = '.$db->quote('com_content.article').' OR ' . $db->quoteName('context') . ' = '.$db->quote('com_contact.contact').')')
        ;
        $result = $db->setQuery($query)->loadObjectList();

        if(!count($result)){
            return $object;
        }

        $fields = [];

        foreach ($result as $field)
        {
            $values = false;

            $fieldparams = json_decode($field->fieldparams, true);
            if(isset($fieldparams['options']) && is_array($fieldparams['options']) && count($fieldparams['options'])){
                $values = [];
                foreach ($fieldparams['options'] as $option) {
                    $key = $option['value'];
                    if(is_numeric($key)){
                        $key = (int)$key;
                    }
                    $values[$key] = Text::_($option['name']);
               }
            }

            $fields[$field->id] = [
                'id' => $field->id,
                'name' => Text::_($field->title),
                'values' => $values,
            ];
        }

        $titles = $desc = $keyvords = [];
        foreach ($filterData as $key => $f) {
            if(!isset($fields[$key]) || empty($f)){
                continue;
            }
            $fname = $fields[$key]['name'];
            if(is_array($f)){
                $fValues = [];
                foreach ($f as $fk => $fv) {
                    if(in_array($fk, ['from', 'to'])){
                        continue;
                    }
                    if(empty($fv)){
                        continue;
                    }
                    if(is_numeric($fv)){
                        $fv = (int)$fv;
                    }
                    if($fields[$key]['values'] === false){
                        $fValues[] = $fv;
                    }
                    else if(isset($fields[$key]['values'][$fv])){
                        $fValues[] = $fields[$key]['values'][$fv];
                    }
                    else{
                        $fValues[] = $fv;
                    }
                }
                $fValue = implode(', ', $fValues);
            }
            else{
                if(is_numeric($f)){
                    $f = (int)$f;
                }
                if($fields[$key]['values'] === false){
                    $fValue = $f;
                }
                else if(isset($fields[$key]['values'][$f])){
                    $fValue = $fields[$key]['values'][$f];
                }
                else{
                    $fValue = $f;
                }
            }

            if(empty($fValue)){
                continue;
            }
            $titles[] = $desc[] = $fname.': '.$fValue;
            $keyvords[] = $fValue;
        }
        $object->catid = $catid;
        $object->filter = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $object->filter_hash = JlcontentfieldsfilterHelper::createHash($object->filter);
        $object->meta_title = $catName.'. '.implode('; ', $titles);
        $object->meta_desc = $catName.'. '.implode('; ', $desc);
        $object->meta_keywords = implode(', ', $keyvords);
        $object->publish = 1;


        $db->insertObject('#__jlcontentfieldsfilter_data', $object, 'id');

        return $object;
    }
}