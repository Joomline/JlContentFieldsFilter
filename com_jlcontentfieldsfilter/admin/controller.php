<?php
defined( '_JEXEC' ) or die; // No direct access

/**
 * Default Controller
 * @author Joomline
 */
class JlcontentfieldsfilterController extends JControllerLegacy
{

	/**
	 * Method to display a view.
	 * @param bool $cachable
	 * @param array $urlparams
	 * @return JControllerLegacy
	 */
	function display( $cachable = false, $urlparams = array() )
	{
		$this->default_view = 'items';
		parent::display( $cachable, $urlparams );
		return $this;
	}
}