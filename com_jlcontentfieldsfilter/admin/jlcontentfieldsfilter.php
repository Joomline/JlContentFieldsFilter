<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined( '_JEXEC' ) or die; // No direct access
/**
 * Component jlcontentfieldsfilter
 * @author Joomline
 */
require_once JPATH_COMPONENT.'/helpers/jlcontentfieldsfilter.php';
$controller = JControllerLegacy::getInstance( 'jlcontentfieldsfilter' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();