<?php

// No direct access
defined('_JEXEC') or die;

/**
 * Модель редактирования текущего элемента
 * @author Joomline
 */
class JlcontentfieldsfilterModelItem extends JModelAdmin
{

    /**
     * загрузка текущей формы
     * @param Array $data
     * @param Boolean $loadData
     * @return Object form data
     */
    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }


    /**
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return JTable|mixed
     */
    public function getTable($type = 'jlcontentfieldsfilter_data', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $publish, $filterData)
    {
        if (!is_array($filterData) || !count($filterData)) {
            return false;
        }

        $table = $this->getTable();
        $filter = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $hash = JlcontentfieldsfilterHelper::createHash($filter);
        if ($id > 0) {
            $table->load($id);
        } else {
            $table->load(array('filter_hash' => $hash));
            $id = $table->id;
        }

        $data = array(
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords,
            'publish' => $publish
        );

        if ($id == 0) {
            $data['catid'] = $cid;
            $data['filter_hash'] = $hash;
            $data['filter'] = $filter;
        }

        return $table->save($data);
    }

    function getRows($filterData)
    {
        $filter = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $hash = JlcontentfieldsfilterHelper::createHash($filter);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__jlcontentfieldsfilter_data')
//            ->where('filter LIKE ' . $db->quote('%' . $filter . '%'))
            ->where('filter_hash = ' . $db->quote($hash))
        ;
        $result = $db->setQuery($query)->loadObjectList('id');
        if (!is_array($result)) {
            $result = array();
        }
        return $result;
    }
}