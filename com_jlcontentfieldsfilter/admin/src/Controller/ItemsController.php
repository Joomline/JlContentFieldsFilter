<?php

/**
 * JL Content Fields Filter.
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

/**
 * Controller for items list.
 *
 * @since  1.0.0
 */
class ItemsController extends AdminController
{
    /**
     * Constructor.
     *
     * @param array $config Configuration array
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * Proxy for getModel.
     *
     * @param string $name Model name
     * @param string $prefix Model prefix
     * @param array $config Configuration array
     *
     * @return \Joomla\CMS\MVC\Model\BaseDatabaseModel The model instance
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Item', $prefix = '', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Method to delete one or more records.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function delete()
    {
        // Check for request forgeries
        Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

        $app = Factory::getApplication();
        $cid = $app->getInput()->get('cid', [], 'array');

        if (empty($cid)) {
            $app->enqueueMessage(Text::_('JERROR_NO_ITEMS_SELECTED'), 'warning');
        } else {
            try {
                // Get model using MVC Factory (getModel() returns false due to configuration)
                $mvcFactory = $app->bootComponent('com_jlcontentfieldsfilter')->getMVCFactory();
                $model = $mvcFactory->createModel('Item', 'Administrator');
                $model->delete($cid);
                $app->enqueueMessage(Text::plural('COM_JLCONTENTFIELDSFILTER_N_ITEMS_DELETED', count($cid)), 'success');
            } catch (\Exception $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $this->setRedirect(Route::_('index.php?option=com_jlcontentfieldsfilter&view=items', false));
    }

    /**
     * Method to publish a list of items.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function publish()
    {
        // Check for request forgeries
        Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

        $app = Factory::getApplication();
        $cid = $app->getInput()->get('cid', [], 'array');
        $task = $this->getTask();
        $value = ($task == 'publish') ? 1 : 0;

        if (empty($cid)) {
            $app->enqueueMessage(Text::_('JERROR_NO_ITEMS_SELECTED'), 'warning');
        } else {
            try {
                // Get model using MVC Factory (getModel() returns false due to configuration)
                $mvcFactory = $app->bootComponent('com_jlcontentfieldsfilter')->getMVCFactory();
                $model = $mvcFactory->createModel('Item', 'Administrator');
                $model->publish($cid, $value);
                
                if ($value == 1) {
                    $message = Text::plural('COM_JLCONTENTFIELDSFILTER_N_ITEMS_PUBLISHED', count($cid));
                } else {
                    $message = Text::plural('COM_JLCONTENTFIELDSFILTER_N_ITEMS_UNPUBLISHED', count($cid));
                }
                $app->enqueueMessage($message, 'success');
            } catch (\Exception $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $this->setRedirect(Route::_('index.php?option=com_jlcontentfieldsfilter&view=items', false));
    }

    /**
     * Method to unpublish a list of items.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function unpublish()
    {
        $this->publish();
    }

    /**
     * Get form fields for a specific category.
     *
     * This method returns the fields configuration in JSON format
     * for the specified category ID.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function get_form()
    {
        // Suppress warnings/notices to ensure clean JSON output
        $old_error_reporting = error_reporting();
        error_reporting(E_ERROR | E_PARSE);

        $fields  = [];
        $error   = 0;
        $message = '';

        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);

        if ($cid == 0) {
            $error   = 1;
            $message = 'CID = 0';
        }

        if (!$error) {
            $file = JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter/src/Helper/JlcontentfieldsfilterHelper.php';
            if (!is_file($file)) {
                $error   = 1;
                $message = 'Module helper not found';
            } else {
                $module = ModuleHelper::getModule('mod_jlcontentfieldsfilter');
                $params = new Registry();
                $params->loadString($module->params);
                $bootedModule = $app->bootModule('mod_jlcontentfieldsfilter', 'Site');
                $lang   = $app->getLanguage();
                $lang->load('mod_jlcontentfieldsfilter', JPATH_SITE);
                $module_helper = $bootedModule->getHelper('JlcontentfieldsfilterHelper');

                /**
                 * Fields are pulled from the module. The method needs parameters of a specific module
                 * (actually not needed for admin), so we set any number here.
                 * Use 1 as module ID - it's only used for layout rendering context.
                 */
                $moduleId = 1;

                $fields = $module_helper->getFields($params, $cid, [], $moduleId, 'com_content');
            }
        }

        // Restore error reporting
        error_reporting($old_error_reporting);

        // Clean output buffer to remove any warnings/notices
        if (ob_get_level()) {
            ob_clean();
        }

        exit(json_encode(['error' => $error, 'message' => $message, 'fields' => $fields]));
    }

    /**
     * Get filtered rows based on the provided filter data.
     *
     * This method returns the filtered rows in JSON format.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function get_rows()
    {
        $fields  = [];
        $error   = 0;
        $message = '';

        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);
        $app->getLanguage()->load('mod_jlcontentfieldsfilter', JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter');

        if ($cid == 0) {
            $error   = 1;
            $message = 'CID = 0';
        }

        $filterData = $app->getInput()->get('jlcontentfieldsfilter', [], 'array');

        $model = $this->getModel();
        $rows  = $model->getRows($filterData);

        exit(json_encode(['error' => $error, 'message' => $message, 'rows' => $rows]));
    }

    /**
     * Get items for a specific category.
     *
     * This method returns all filter items for the specified category in JSON format.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function getItems()
    {
        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);

        $error   = 0;
        $message = '';
        $items   = [];

        if ($cid == 0) {
            $error   = 1;
            $message = 'CID = 0';
        } else {
            try {
                $model = $this->getModel('Items', 'Administrator');
                
                // Get all items for this category
                $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
                $query = $db->getQuery(true);
                
                $query->select('*')
                    ->from($db->quoteName('#__jlcontentfieldsfilter_data'))
                    ->where($db->quoteName('catid') . ' = ' . (int) $cid)
                    ->order($db->quoteName('id') . ' DESC');
                
                $db->setQuery($query);
                $items = $db->loadObjectList();
            } catch (\Exception $e) {
                $error = 1;
                $message = $e->getMessage();
            }
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => !$error,
            'message' => $message,
            'data' => $items
        ]);
        
        exit();
    }

    /**
     * Save a filter item.
     *
     * Saves the filter configuration including metadata and filter values.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function save()
    {
        $error         = 0;
        $message       = '';
        $app           = Factory::getApplication();
        $cid           = $app->getInput()->getInt('cid', 0);
        $id            = $app->getInput()->getInt('id', 0);
        $meta_title    = $app->getInput()->getString('meta_title', '');
        $meta_desc     = $app->getInput()->getString('meta_desc', '');
        $meta_keywords = $app->getInput()->getString('meta_keywords', '');
        $state         = $app->getInput()->getInt('state', 0);
        $filterData    = $app->getInput()->get('jlcontentfieldsfilter', [], 'array');

        $model  = $this->getModel();
        $result = $model->saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $state, $filterData);
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }

    /**
     * Delete a filter item (JSON endpoint).
     *
     * Deletes the filter item with the specified ID and returns JSON response.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function deleteJson()
    {
        $app     = Factory::getApplication();
        $id      = $app->getInput()->getInt('id', 0);
        $model   = $this->getModel();
        $pks     = [$id];
        $result  = $model->delete($pks);
        $message = $result ? '' : 'Error delete item';
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }
}
