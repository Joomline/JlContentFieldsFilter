<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined( '_JEXEC' ) or die; // No direct access
/**
 * Component jlcontentfieldsfilter
 * @author Joomline
 */
require_once JPATH_COMPONENT.'/helpers/jlcontentfieldsfilter.php';
$controller = BaseController::getInstance( 'jlcontentfieldsfilter' );
$controller->execute( Factory::getApplication()->getInput()->get( 'task' ) );
$controller->redirect();