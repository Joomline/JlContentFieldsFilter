<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Extension\JlcontentfieldsfilterComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The component service provider.
 */
return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\Joomla\\Component\\Jlcontentfieldsfilter'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\Jlcontentfieldsfilter'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new JlcontentfieldsfilterComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));

                return $component;
            }
        );
    }
};