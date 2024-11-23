<?php
/**
 */

namespace Zend\ServiceManager;

use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\InvalidServiceException;

/**
 * Interface for a plugin manager
 *
 * A plugin manager is a specialized service locator used to create homogeneous objects
 */
interface PluginManagerInterface extends ServiceLocatorInterface
{
    /**
     * Validate an instance
     *
     * @param  object $instance
     * @return void
     * @throws InvalidServiceException If created instance does not respect the
     *     constraint on type imposed by the plugin manager
     * @throws ContainerException if any other error occurs
     */
    public function validate($instance);
}
