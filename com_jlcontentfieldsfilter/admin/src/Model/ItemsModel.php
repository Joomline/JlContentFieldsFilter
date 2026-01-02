<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\ListModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Items model for jlcontentfieldsfilter component.
 *
 * @since  1.0.0
 */
class ItemsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'meta_title', 'a.meta_title',
                'catid', 'a.catid', 'category_title',
                'extension', 'category_extension',
                'filter', 'a.filter',
                'state', 'a.state',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to get the filter form.
     *
     * @param   array    $data      Data.
     * @param   boolean  $loadData  Load current data.
     *
     * @return  \Joomla\CMS\Form\Form|bool  The Form object or false on error.
     *
     * @since   1.0.0
     */
    public function getFilterForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_jlcontentfieldsfilter.items.filter',
            'filter_items',
            [
                'control' => '',
                'load_data' => $loadData,
            ]
        );
    }

    /**
     * Build the query to get the list of records.
     *
     * @return \Joomla\Database\QueryInterface The database query object.
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select required fields
        $query->select('a.*')
            ->from($db->quoteName('#__jlcontentfieldsfilter_data', 'a'));

        // Join over categories
        $query->select([
                $db->quoteName('c.title', 'category_title'),
                $db->quoteName('c.extension', 'category_extension')
            ])
            ->leftJoin($db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
            $query->where('(a.meta_title LIKE ' . $search . ')');
        }

        // Filter by category
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        }

        // Filter by published state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $state);
        }

        // Filter by extension
        $extension = $this->getState('filter.extension');
        if (!empty($extension)) {
            $query->where($db->quoteName('c.extension') . ' = ' . $db->quote($extension));
        }

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function populateState($ordering = 'a.id', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $state);

        $extension = $this->getUserStateFromRequest($this->context . '.filter.extension', 'filter_extension');
        $this->setState('filter.extension', $extension);

        parent::populateState($ordering, $direction);
    }

    /**
     * Get category options for select field.
     *
     * @return string HTML options for category select
     *
     * @since   1.0.0
     */
    public function getCategoryOptions()
    {
        $categoryOptions = HTMLHelper::_(
            'select.options',
            HTMLHelper::_('category.options', 'com_content'),
            'value',
            'text',
            ['class' => 'form-select'],
            0
        );
        return $categoryOptions;
    }
}
