<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

\defined( '_JEXEC' ) or die; // No direct access

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
	function display( $cachable = false, $urlparams = [] )
	{
		$this->default_view = 'items';
		parent::display( $cachable, $urlparams );
		return $this;
	}
}