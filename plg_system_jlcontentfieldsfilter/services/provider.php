<?php
/**
 * JL Content Fields Filter
 *
 * @version    @version@
 * @author     Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license    GNU General Public License version 2 or later; see    LICENSE.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Jlcontentfieldsfilter\Extension\Jlcontentfieldsfilter;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param Container $container The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(DispatcherInterface::class);
                $config = (array)PluginHelper::getPlugin('system', 'Jlcontentfieldsfilter');
                $plugin = new Jlcontentfieldsfilter($subject, $config);
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase(Factory::getContainer()->get(DatabaseInterface::class));
                return $plugin;
            }
        );
    }
};