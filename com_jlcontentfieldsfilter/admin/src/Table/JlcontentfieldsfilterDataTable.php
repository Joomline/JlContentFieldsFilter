<?php

/**
 * JL Content Fields Filter.
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Table class for jlcontentfieldsfilter data.
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterDataTable extends Table
{
    /**
     * Class constructor.
     *
     * @param DatabaseInterface $db Database driver
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__jlcontentfieldsfilter_data', 'id', $db);
    }

    /**
     * Method for loading data into the object field.
     *
     * @param array $array Array of data to bind
     * @param string $ignore Fields to ignore
     *
     * @return bool True on success
     *
     * @since   1.0.0
     */
    public function bind($array, $ignore = '')
    {
        // This table only has: id, catid, filter_hash, filter, meta_title, meta_desc, meta_keywords, state
        // No need for special processing like alias, created_by, etc.
        return parent::bind($array, $ignore);
    }

}
