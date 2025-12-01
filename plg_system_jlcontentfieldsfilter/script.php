<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */
// Запрет прямого доступа.
\defined('_JEXEC') or die;

class plgSystemJlcontentfieldsfilterInstallerScript
{
    /**
     * Метод для обновления компонента.
     *
     * @param   object  $parent  Класс, который вызывает этом метод.
     *
     * @return  void
     */
    public function update($parent)
    {

    }

    /**
     * Метод для установки компонента.
     *
     * @param   object  $parent  Класс, который вызывает этом метод.
     *
     * @return  void
     */
    public function install($parent)
    {
        //$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
    }

    /**
     * Метод для удаления компонента.
     *
     * @param   object  $parent  Класс, который вызывает этом метод.
     *
     * @return  void
     */
    public function uninstall($parent)
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * Метод, который исполняется до install/update/uninstall.
     *
     * @param   object  $type    Тип изменений: install, update или discover_install
     * @param   object  $parent  Класс, который вызывает этом метод. Класс, который вызывает этом метод.
     *
     * @return  void
     */
    public function preflight($type, $parent)
    {
        //echo '<p>' . JText::_('PLG_MINICCK_PREFLIGHT_' . strtoupper($type) . '_TEXT') . '</p>';
    }

    /**
     * Метод, который исполняется после install/update/uninstall.
     *
     * @param   object  $type    Тип изменений: install, update или discover_install
     * @param   object  $parent  Класс, который вызывает этом метод. Класс, который вызывает этом метод.
     *
     * @return  void
     */
    public function postflight($type, $parent)
    {
    	$db = \Joomla\CMS\Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
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