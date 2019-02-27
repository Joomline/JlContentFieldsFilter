<?php
defined( '_JEXEC' ) or die; // No direct access
/**
 * Component jlcontentfieldsfilter
 * @author Joomline
 */
require_once JPATH_COMPONENT.'/helpers/jlcontentfieldsfilter.php';
$controller = JControllerLegacy::getInstance( 'jlcontentfieldsfilter' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();