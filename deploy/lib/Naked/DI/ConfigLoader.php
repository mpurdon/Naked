<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

use Naked\Application;

/**
 * A utility class that initializes the dependency injection container
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class ConfigLoader
{
    /**
     * @var Naked\Application\Environment
     */
    protected $environment;

    /**
     * Constructor
     *
     * @param Naked\Application\Environment $environment
     */
    public function __construct(\Naked\Application\Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Load all dependencies
     */
    public function load()
    {
        $this->loadFrameworkDependencies();
        $this->loadModuleDependencies();
    }

    /**
     * Load the dependencies required by the Naked Framework
     *
     * @todo Cache the loading of dependency specifications
     */
    protected function loadFrameworkDependencies()
    {
        $configDir = $this->environment->getPath('configuration');
        $dependencyDir = $configDir . DIRECTORY_SEPARATOR . 'dependencies';
        $this->loadDependenciesFromDirectory($dependencyDir);
    }

    /**
     * Load the dependencies required for each of the installed modules
     *
     * @todo Cache the loading of dependency specifications
     */
    protected function loadModuleDependencies()
    {
        $modulesDir = $this->environment->getPath('modules');
        foreach ($this->environment->getModules() as $module) {
            $moduleDir = $modulesDir .  DIRECTORY_SEPARATOR .
                         $module . DIRECTORY_SEPARATOR .
                         'configuration' . DIRECTORY_SEPARATOR .
                         'dependencies';
            $this->loadDependenciesFromDirectory($moduleDir);
        }
    }

    /**
     * Load all of the dependency configuration files we find in a given directory
     *
     * @param string $directory
     */
    protected function loadDependenciesFromDirectory($directory)
    {
        $dependencies = $this->getDependenciesFromDirectory($directory);

        foreach ($dependencies as $dependency) {
            //echo "Loading $dependency<br>";
            include($dependency);
        }
    }

    /**
     * Search the given directory for dependency specifications
     *
     * @param string $directory
     */
    protected function getDependenciesFromDirectory($directory)
    {
        //echo "Attempting to load dependencies from $directory<br>";
        $dependencies = array();

        $directoryIterator = new \DirectoryIterator($directory);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isFile() && substr($fileInfo->getFilename(), -4) == '.php') {
                $dependencies[] = $fileInfo->getPathname();
            }
        }

        return $dependencies;
    }
}