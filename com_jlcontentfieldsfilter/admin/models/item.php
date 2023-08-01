<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

// No direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;

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
    public function getForm($data = [], $loadData = true)
    {
        return false;
    }


    /**
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return JTable|mixed
     */
    public function getTable($type = 'jlcontentfieldsfilter_data', $prefix = 'Table', $config = [])
    {
        return Table::getInstance($type, $prefix, $config);
    }

    function saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $publish, $filterData)
    {
        if (!is_array($filterData) || !count($filterData)) {
            return false;
        }

        $table = $this->getTable();
        $filter = \JlcontentfieldsfilterHelper::createFilterString($filterData);
        $unsafe_filter = \JlcontentfieldsfilterHelper::createFilterString($filterData, false);
        $hash = \JlcontentfieldsfilterHelper::createHash($filter);
        $unsafe_hash = \JlcontentfieldsfilterHelper::createHash($unsafe_filter);
        if ($id > 0) {
            $table->load($id);
        } else {
            $table->load(['filter_hash' => $hash]);
            $id = $table->id;

			if($id == 0){
				$table->load(['filter_hash' => $unsafe_hash]);
				$id = $table->id;
			}
        }

        $data = [
	        'filter_hash'   => $hash,
	        'filter'        => $filter,
	        'meta_title'    => $meta_title,
	        'meta_desc'     => $meta_desc,
	        'meta_keywords' => $meta_keywords,
	        'publish'       => $publish
        ];

        if ($id == 0) {
            $data['catid'] = $cid;
        }

        return $table->save($data);
    }

    function getRows($filterData)
    {
        $filter = \JlcontentfieldsfilterHelper::createFilterString($filterData);
		$unsafe_filter = \JlcontentfieldsfilterHelper::createFilterString($filterData, false);
        $hash = \JlcontentfieldsfilterHelper::createHash($filter);
	    $unsafe_hash = \JlcontentfieldsfilterHelper::createHash($unsafe_filter);
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__jlcontentfieldsfilter_data')
            ->where('filter_hash = ' . $db->quote($hash), 'OR')
            ->where('filter_hash = ' . $db->quote($unsafe_hash));

        $result = $db->setQuery($query)->loadObjectList('id');
        if (!is_array($result)) {
            $result = [];
        }
        return $result;
    }
}