<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Script file of jlcontentfieldsfilter plugin.
 *
 * @since  1.0.0
 */
class plgSystemJlcontentfieldsfilterInstallerScript
{
    /**
     * Method to update the component.
     *
     * @param object $parent The class calling this method.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function update($parent)
    {

    }

    /**
     * Method to install the component.
     *
     * @param object $parent The class calling this method.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function install($parent)
    {
        //$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
    }

    /**
     * Method to uninstall the component.
     *
     * @param object $parent The class calling this method.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function uninstall($parent)
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * Method executed before install/update/uninstall.
     *
     * @param string $type The type of change: install, update or discover_install.
     * @param object $parent The class calling this method.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function preflight($type, $parent)
    {
        //echo '<p>' . JText::_('PLG_MINICCK_PREFLIGHT_' . strtoupper($type) . '_TEXT') . '</p>';
    }

    /**
     * Method executed after install/update/uninstall.
     *
     * @param string $type The type of change: install, update or discover_install.
     * @param object $parent The class calling this method.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public function postflight($type, $parent)
    {
        $db    = \Joomla\CMS\Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->update('#__extensions')
              ->set('enabled = 1')
              ->where('element = '.$db->quote('jlcontentfieldsfilter'))
              ->where('type = '.$db->quote('plugin'))
              ->where('folder = '.$db->quote('system'))
        ;
        $db->setQuery($query)->execute();
    }
}
