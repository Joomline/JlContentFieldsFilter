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
     * Build the query to get the list of records.
     *
     * @return \Joomla\Database\QueryInterface The database query object.
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $query = $this->getDatabase()->getQuery(true);
        $query->select('*');
        $query->from('#__jlcontentfieldsfilter_data');
        return $query;
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
