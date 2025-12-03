<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_jlcontentfieldsfilter
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterComponent extends MVCComponent implements BootableExtensionInterface
{
    use HTMLRegistryAwareTrait;

    /**
     * Booting the extension. This is the function to set up the environment of the extension.
     *
     * @param   ContainerInterface  $container  The DI container.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function boot(ContainerInterface $container): void
    {
        // Initialization code if needed
    }
}