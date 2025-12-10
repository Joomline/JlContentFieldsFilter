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

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for edit current element.
 *
 * @since  1.0.0
 */
class ItemController extends FormController
{
    /**
     * Class constructor.
     *
     * @param array $config Configuration array
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        $this->view_list = 'items';
        parent::__construct($config);
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param array $data An array of input data.
     * @param string $key The name of the key for the primary key.
     *
     * @return bool
     *
     * @since    1.0.0
     */
    protected function allowEdit($data = [], $key = 'id')
    {
        // Initialise variables.
        $recordId = ( int )isset($data[$key]) ? $data[$key] : 0;
        $user     = Factory::getApplication()->getIdentity();
        $userId   = $user->get('id');
        // Check general edit permission first.
        if ($user->authorise('core.edit', 'com_jlcontentfieldsfilter.item.' . $recordId)) {
            return true;
        }
        // Fallback on edit.own.
        // First test if the permission is available.
        if ($user->authorise('core.edit.own', 'com_jlcontentfieldsfilter.item.' . $recordId)) {
            // Now test the owner is the user.
            $ownerId = ( int )isset($data['created_by']) ? $data['created_by'] : 0;
            if (empty($ownerId) && $recordId) {
                // Need to do a lookup from the model.
                $record = $this->getModel()->getItem($recordId);

                if (empty($record)) {
                    return false;
                }

                $ownerId = $record->created_by;
            }
            // If the owner matches 'me' then do the test.
            if ($ownerId == $userId) {
                return true;
            }
        }
        // Since there is no asset tracking, revert to the component permissions.
        return false;
    }
}
