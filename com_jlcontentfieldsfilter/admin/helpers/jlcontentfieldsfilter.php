<?php

defined('_JEXEC') or die;

/**
 * Class JlcontentfieldsfilterHelper
 */
class JlcontentfieldsfilterHelper
{
    /**
     * Добавление подменю
     * @param String $vName
     */
    static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('ITEM_SUBMENU'),
            'index.php?option=com_jlcontentfieldsfilter&view=items',
            $vName == 'items');
    }

    /**
     * Получаем доступные действия для текущего пользователя
     * @return JObject
     */
    public static function getActions()
    {
        $user = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_jlcontentfieldsfilter';
        $actions = JAccess::getActions($assetName);
        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }
        return $result;
    }

    public static function createFilterString($filter)
    {
        ksort($filter);
        $data = array();
        foreach ($filter as $key => $item) {
            if (is_array($item)) {
                $val = array();
                ksort($item);
                foreach ($item as $k => $v) {

                    if($k === 'from' || $k === 'to'){
                        continue;
                    }

                    if (!empty($v)) {
                        $val[] = $v;
                    }
                }
                if (count($val)) {
                    $data[] = $key . '=' . implode(',', $val);
                }
            } else {
                $data[] = $key . '=' . $item;
            }
        }

        $data = implode('&', $data);
        return $data;
    }

    public static function createHash($string)
    {
        return md5($string);
    }

    public static function createMeta($catid, $filterData)
    {
        $object = new stdClass();
        $object->meta_title = '';
        $object->meta_desc = '';
        $object->meta_keywords = '';

        if(!$catid){
            return $object;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`title`')
            ->from('#__categories')
            ->where('id = '.(int)$catid)
        ;
        $catName = $db->setQuery($query,0,1)->loadResult();


        $query->clear()->select('`id`, `title`, `type`, `fieldparams`')
            ->from('`#__fields`')
            ->where('(`context` = '.$db->quote('com_content.article').' OR `context` = '.$db->quote('com_contact.contact').')')
        ;
        $result = $db->setQuery($query)->loadObjectList();

        if(!count($result)){
            return $object;
        }

        $fields = array();

        foreach ($result as $field)
        {
            $values = false;

            $fieldparams = json_decode($field->fieldparams, true);
            if(isset($fieldparams['options']) && is_array($fieldparams['options']) && count($fieldparams['options'])){
                $values = array();
                foreach ($fieldparams['options'] as $option) {
                    $key = $option['value'];
                    if(is_numeric($key)){
                        $key = (int)$key;
                    }
                    $values[$key] = JText::_($option['name']);
               }
            }

            $fields[$field->id] = array(
                'id' => $field->id,
                'name' => JText::_($field->title),
                'values' => $values,
            );
        }

        $titles = $desc = $keyvords = array();
        foreach ($filterData as $key => $f) {
            if(!isset($fields[$key]) || empty($f)){
                continue;
            }
            $fname = $fields[$key]['name'];
            if(is_array($f)){
                $fValues = array();
                foreach ($f as $fk => $fv) {
                    if(in_array($fk, array('from', 'to'))){
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