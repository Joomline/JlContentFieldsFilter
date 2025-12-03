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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper\JlcontentfieldsfilterHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Component jlcontentfieldsfilter (DEPRECATED - Legacy file)
 *
 * @deprecated  This file is deprecated and kept for reference only
 */
$controller = BaseController::getInstance('jlcontentfieldsfilter');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();

// jlcontentfieldsfilter.php	-->	admin/src/Controller/DisplayController.php