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
use Joomla\CMS\Language\Text;
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
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_JLCONTENTFIELDSFILTER_ITEM';

    /**
     * Method to save a record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   1.0.0
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        $this->checkToken();

        $app   = Factory::getApplication();
        $data  = $this->input->post->get('jform', [], 'array');
        $task = $this->getTask();

        // Get model
        $model = $this->getModel('Item', 'Administrator');
        
        // Attempt to save the data.
        if (!$model->save($data)) {
            // Redirect back to the edit screen.
            $app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');

            $id = !empty($data['id']) ? (int) $data['id'] : 0;
            $this->setRedirect(
                \Joomla\CMS\Router\Route::_(
                    'index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit' . ($id > 0 ? '&id=' . $id : ''),
                    false
                )
            );

            return false;
        }

        // Success message
        $app->enqueueMessage(
            Text::_('COM_JLCONTENTFIELDSFILTER_ITEM_SAVE_SUCCESS'),
            'success'
        );

        // Redirect the user based on the chosen task.
        if ($task === 'save') {
            // Redirect to list view
            $this->setRedirect(
                \Joomla\CMS\Router\Route::_(
                    'index.php?option=com_jlcontentfieldsfilter&view=items',
                    false
                )
            );
        } else {
            // Redirect back to the edit screen.
            $savedId = $model->getState('item.id');
            $this->setRedirect(
                \Joomla\CMS\Router\Route::_(
                    'index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit&id=' . $savedId,
                    false
                )
            );
        }

        return true;
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param array  $data An array of input data.
     * @param string $key  The name of the key for the primary key.
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
