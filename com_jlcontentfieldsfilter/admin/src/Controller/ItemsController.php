<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Model;

\defined('_JEXEC') or die; // No direct access

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Registry\Registry;

/**
 * Controller for list current element
 * @author Aleks.Denezh
 */
class ItemsModel extends ListModel
{

    /**
     * Class constructor
     * @param array $config
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
     * Method to get current model
     * @param String $name (model name)
     * @param String $prefix (model prefox)
     * @param Array $config
     * @return object model for current element
     */
    public function getModel($name = 'Item', $prefix = 'JlcontentfieldsfilterModel', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    function get_form()
    {
        $fields = [];
        $error = 0;
        $message = '';

        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);

        if ($cid == 0) {
            $error = 1;
            $message = 'CID = 0';
        }

        if (!$error) {
            $file = JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter/src/Helper/JlcontentfieldsfilterHelper.php';
            if (!is_file($file)) {
                $error = 1;
                $message = 'Module helper not found';
            } else {
                $module = ModuleHelper::getModule('mod_jlcontentfieldsfilter');
                $params = new Registry;
                $params->loadString($module->params);
				$module = $app->bootModule('mod_jlcontentfieldsfilter', 'Site');
	            $lang = $app->getLanguage();
	            $lang->load('mod_jlcontentfieldsfilter' , JPATH_SITE);
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

    function get_rows()
    {
        $fields = [];
        $error = 0;
        $message = '';

        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);
	    $app->getLanguage()->load('mod_jlcontentfieldsfilter', JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter');

        if ($cid == 0) {
            $error = 1;
            $message = 'CID = 0';
        }

        $filterData = $app->getInput()->get('jlcontentfieldsfilter', [], 'array');

        $model = $this->getModel();
        $rows = $model->getRows($filterData);


        exit(json_encode(['error' => $error, 'message' => $message, 'rows' => $rows]));
    }

    public function save()
    {
        $error = 0;
        $message = '';
        $app = Factory::getApplication();
        $cid = $app->getInput()->getInt('cid', 0);
        $id = $app->getInput()->getInt('id', 0);
        $meta_title = $app->getInput()->getString('meta_title', '');
        $meta_desc = $app->getInput()->getString('meta_desc', '');
        $meta_keywords = $app->getInput()->getString('meta_keywords', '');
        $publish = $app->getInput()->getInt('publish', 0);
        $filterData = $app->getInput()->get('jlcontentfieldsfilter', [], 'array');

        $model = $this->getModel();
        $result = $model->saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $publish, $filterData);
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }

    public function delete()
    {
        $app = Factory::getApplication();
        $id = $app->getInput()->getInt('id', 0);
        $model = $this->getModel();
        $result = $model->delete($id);
        $message = $result ? '' : 'Error delete item';
        exit(json_encode(['error' => !$result, 'message' => $message]));
    }
}