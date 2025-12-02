<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Controller;

\defined('_JEXEC') or die; // No direct access

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Jlcontentfieldsfilter master display controller.
 * @author Joomline
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     */
    protected $default_view = 'items';

    /**
     * Method to display a view.
     */
    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }
}