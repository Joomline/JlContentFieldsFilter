<?php

/**
 * JL Content Fields Filter.
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Registry\Registry;

/**
 * Controller for items list.
 *
 * @since  1.0.0
 */
class ItemsController extends BaseController
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
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'published', 'a.published',
                'created', 'a.created',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to get the model.
     *
     * @param string $name Model name
     * @param string $prefix Model prefix
     * @param array $config Configuration array
     *
     * @return \Joomla\CMS\MVC\Model\BaseDatabaseModel The model instance
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Item', $prefix = 'JlcontentfieldsfilterModel', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
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
                $module = $app->bootModule('mod_jlcontentfieldsfilter', 'Site');
                $lang   = $app->getLanguage();
                $lang->load('mod_jlcontentfieldsfilter', JPATH_SITE);
                $module_helper = $module->getHelper('JlcontentfieldsfilterHelper');
                /**
                 * Fields are pulled from the module. The method needs parameters of a specific module
                 * (actually not needed for admin), so we set any number here.
                 */
                $module->id = 1;

                $fields = $module_helper->getFields($params, $cid, [], $module->id, 'com_content');
            }
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
        $publish       = $app->getInput()->getInt('publish', 0);
        $filterData    = $app->getInput()->get('jlcontentfieldsfilter', [], 'array');

        $model  = $this->getModel();
        $result = $model->saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $publish, $filterData);
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }

    /**
     * Delete a filter item.
     *
     * Deletes the filter item with the specified ID.
     *
     * @return void Outputs JSON and exits
     *
     * @since   1.0.0
     */
    public function delete()
    {
        $app     = Factory::getApplication();
        $id      = $app->getInput()->getInt('id', 0);
        $model   = $this->getModel();
        $result  = $model->delete($id);
        $message = $result ? '' : 'Error delete item';
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }
}
