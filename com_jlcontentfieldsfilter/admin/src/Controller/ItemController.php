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
use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for edit current element.
 *
 * @since  1.0.0
 */
class ItemController extends BaseController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_JLCONTENTFIELDSFILTER_ITEM';

    /**
     * Method to display the view.
     *
     * @param boolean $cachable  If true, the view output will be cached
     * @param array   $urlparams An array of safe URL parameters
     *
     * @return  BaseController  This object to support chaining
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }

    /**
     * Method to cancel an edit.
     *
     * @param string $key The name of the primary key of the URL variable
     *
     * @return boolean True if access level checks pass, false otherwise
     *
     * @since   1.0.0
     */
    public function cancel($key = null)
    {
        $this->checkToken();

        $app = Factory::getApplication();
        $app->setUserState('com_jlcontentfieldsfilter.edit.item.data', null);

        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=com_jlcontentfieldsfilter&view=items', false)
        );

        return true;
    }

    /**
     * Method to save a record.
     *
     * @param string $key    The name of the primary key of the URL variable
     * @param string $urlVar The name of the URL variable if different from the primary key
     *
     * @return boolean True if successful, false otherwise
     *
     * @since   1.0.0
     */
    public function save($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $data  = $this->input->post->get('jform', [], 'array');
        $task  = $this->getTask();

        // Validate data
        if (empty($data['catid'])) {
            $app->enqueueMessage('Category is required', 'error');
            $this->setRedirect(
                \Joomla\CMS\Router\Route::_('index.php?option=com_jlcontentfieldsfilter&view=items', false)
            );
            return false;
        }

        // Get the ID from the data or input
        $id = isset($data['id']) ? (int) $data['id'] : $this->input->getInt('id', 0);

        // Use direct table access instead of model
        $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $table = new \Joomla\Component\Jlcontentfieldsfilter\Administrator\Table\JlcontentfieldsfilterDataTable($db);

        if ($id > 0) {
            $table->load($id);
        }

        // Update table data
        $table->catid = (int) $data['catid'];
        $table->meta_title = $data['meta_title'] ?? '';
        $table->meta_desc = $data['meta_desc'] ?? '';
        $table->meta_keywords = $data['meta_keywords'] ?? '';
        $table->state = (int) ($data['state'] ?? 1);

        // Save the table
        if ($table->store()) {
            $app->enqueueMessage('Item saved successfully', 'message');

            // Get the saved ID
            $savedId = $table->id;

            // Determine where to redirect based on task
            if ($task === 'save') {
                // Save and close: redirect to items list
                $this->setRedirect(
                    \Joomla\CMS\Router\Route::_('index.php?option=com_jlcontentfieldsfilter&view=items', false)
                );
            } else {
                // Apply: redirect back to edit form
                $this->setRedirect(
                    \Joomla\CMS\Router\Route::_('index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit&id=' . $savedId, false)
                );
            }

            return true;
        } else {
            $app->enqueueMessage('Error saving item: ' . $table->getError(), 'error');
            return false;
        }
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
