<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Controller for list current element
 * @author Aleks.Denezh
 */
class JlcontentfieldsfilterControllerItems extends JControllerAdmin
{

    /**
     * Class constructor
     * @param array $config
     */
    function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to get current model
     * @param String $name (model name)
     * @param String $prefix (model prefox)
     * @param Array $config
     * @return model for current element
     */
    public function getModel($name = 'Item', $prefix = 'JlcontentfieldsfilterModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    function get_form()
    {
        $fields = array();
        $error = 0;
        $message = '';

        $app = JFactory::getApplication();
        $cid = $app->input->getInt('cid', 0);

        if ($cid == 0) {
            $error = 1;
            $message = 'CID = 0';
        }

        if (!$error) {
            $file = JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter/helper.php';
            if (!is_file($file)) {
                $error = 1;
                $message = 'Module helper not found';
            } else {
                JFactory::getLanguage()->load('mod_jlcontentfieldsfilter', JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter');
                include_once $file;
                $module = JModuleHelper::getModule('mod_jlcontentfieldsfilter');
                $params = new JRegistry;
                $params->loadString($module->params);
                $fields = ModJlContentFieldsFilterHelper::getFields($params, $cid, array(), $module->id, 'com_content');
            }
        }


        exit(json_encode(array('error' => $error, 'message' => $message, 'fields' => $fields)));
    }

    function get_rows()
    {
        $fields = array();
        $error = 0;
        $message = '';

        $app = JFactory::getApplication();
        $cid = $app->input->getInt('cid', 0);
        JFactory::getLanguage()->load('mod_jlcontentfieldsfilter', JPATH_ROOT . '/modules/mod_jlcontentfieldsfilter');

        if ($cid == 0) {
            $error = 1;
            $message = 'CID = 0';
        }

        $filterData = $app->input->get('jlcontentfieldsfilter', array(), 'array');
        $filter = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $hash = JlcontentfieldsfilterHelper::createHash($filter);

        $model = $this->getModel();
        $rows = $model->getRows($filterData);


        exit(json_encode(array('error' => $error, 'message' => $message, 'rows' => $rows)));
    }

    public function save()
    {
        $error = 0;
        $message = '';
        $app = JFactory::getApplication();
        $cid = $app->input->getInt('cid', 0);
        $id = $app->input->getInt('id', 0);
        $meta_title = $app->input->getString('meta_title', '');
        $meta_desc = $app->input->getString('meta_desc', '');
        $meta_keywords = $app->input->getString('meta_keywords', '');
        $publish = $app->input->getInt('publish', 0);
        $filterData = $app->input->get('jlcontentfieldsfilter', array(), 'array');

        $model = $this->getModel();
        $result = $model->saveItem($id, $cid, $meta_title, $meta_desc, $meta_keywords, $publish, $filterData);
        exit(json_encode(array('error' => !$result, 'message' => $message)));
    }

    public function delete()
    {
        $app = JFactory::getApplication();
        $id = $app->input->getInt('id', 0);
        $model = $this->getModel();
        $result = $model->delete($id);
        $message = $result ? '' : 'Error delete item';
        exit(json_encode(array('error' => !$result, 'message' => $message)));
    }
}