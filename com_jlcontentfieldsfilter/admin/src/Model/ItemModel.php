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

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper\JlcontentfieldsfilterHelper;
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Table\JlcontentfieldsfilterDataTable;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Item model for jlcontentfieldsfilter component.
 *
 * @since  1.0.0
 */
class ItemModel extends AdminModel
{
    /**
     * Load the current form.
     *
     * @param array $data The form data.
     * @param bool $loadData True to load the default data.
     *
     * @return mixed Form object or false on error.
     *
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    /**
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return JlcontentfieldsfilterDataTable|Table
     */
    public function getTable($type = 'jlcontentfieldsfilter_data', $prefix = 'Table', $config = [])
    {
        return new JlcontentfieldsfilterDataTable($this->getDatabase());
    }

    /**
     * Save a filter item.
     *
     * @param int $id Item ID
     * @param int $cid Category ID
     * @param string $meta_title Meta title
     * @param string $meta_desc Meta description
     * @param string $meta_keywords Meta keywords
     * @param int $state Item state
     * @param array $filterData Filter data array
     *
     * @return bool True on success, false otherwise
     *
     * @since   1.0.0
     */
    public function saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $state, $filterData)
    {
        if (!\is_array($filterData) || !\count($filterData)) {
            return false;
        }

        $table         = $this->getTable();
        $filter        = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $unsafe_filter = JlcontentfieldsfilterHelper::createFilterString($filterData, false);
        $hash          = JlcontentfieldsfilterHelper::createHash($filter);
        $unsafe_hash   = JlcontentfieldsfilterHelper::createHash($unsafe_filter);
        if ($id > 0) {
            $table->load($id);
        } else {
            $table->load(['filter_hash' => $hash]);
            $id = $table->id;

            if ($id == 0) {
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
            'state'         => $state,
        ];

        if ($id == 0) {
            $data['catid'] = $cid;
        }

        return $table->save($data);
    }

    /**
     * Get rows matching the filter data.
     *
     * @param array $filterData Filter data array
     *
     * @return array Array of matching rows
     *
     * @since   1.0.0
     */
    public function getRows($filterData)
    {
        $filter        = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $unsafe_filter = JlcontentfieldsfilterHelper::createFilterString($filterData, false);
        $hash          = JlcontentfieldsfilterHelper::createHash($filter);
        $unsafe_hash   = JlcontentfieldsfilterHelper::createHash($unsafe_filter);
        $db            = Factory::getContainer()->get(DatabaseInterface::class);
        $query         = $db->getQuery(true);
        $query->select('*')
            ->from('#__jlcontentfieldsfilter_data')
            ->where('filter_hash = ' . $db->quote($hash), 'OR')
            ->where('filter_hash = ' . $db->quote($unsafe_hash));

        $result = $db->setQuery($query)->loadObjectList('id');
        if (!\is_array($result)) {
            $result = [];
        }
        return $result;
    }

    /**
     * Delete a filter item.
     *
     * @param array &$pks An array of record primary keys
     *
     * @return bool True on success, false otherwise
     *
     * @since   1.0.0
     */
    public function delete(&$pks)
    {
        $pks = (array) $pks;
        $table = $this->getTable();

        foreach ($pks as $i => $pk) {
            if (!$table->delete($pk)) {
                $this->setError($table->getError());
                return false;
            }
        }

        return true;
    }
}
