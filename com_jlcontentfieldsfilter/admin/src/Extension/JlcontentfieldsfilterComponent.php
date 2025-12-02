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
 */
class JlcontentfieldsfilterComponent extends MVCComponent implements BootableExtensionInterface
{
    use HTMLRegistryAwareTrait;

    /**
     * Booting the extension. This is the function to set up the environment of the extension.
     */
    public function boot(ContainerInterface $container): void
    {
        // Initialization code if needed
    }
}