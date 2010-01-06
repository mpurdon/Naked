<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Application;

use Naked\Loader;
use Naked\Loader\Zend;
use Naked\Loader\Apc;
use Naked\Routing\Builder;

/**
 * Application environment
 *
 * @package Naked
 * @author Matthew Purdon
 */
class Environment
{
    /**
     * @var array
     */
    protected $paths;

    /**
     * @var array
     */
    protected $modules;

    /**
     * @var array
     */
    protected $modulePaths;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->verifyInstall();
        $this->initIncludePath();
        $this->initLoader();
        $this->loadCoreFiles();
    }

    /**
     * Make sure that things are good to go with the platform
     */
    public function verifyInstall()
    {
        if (!getenv('ENVIRONMENT')) {
            throw new \RuntimeException('You must set up the ENVIRONMENT variable');
        }
    }

    /**
     * Determine if we are operating in a production environment
     *
     * @return boolean
     */
    public function isProduction()
    {
        return strcasecmp(getenv('ENVIRONMENT'), 'production') == 0;
    }

    /**
     * Determine if we are operating in a development environment
     *
     * @return boolean
     */
    public function isDevelopment()
    {
        return strcasecmp(getenv('ENVIRONMENT'),'development') == 0;
    }

    /**
     * Determine if we are being run from command line
     *
     * @return boolean
     */
    public function isCommandLine()
    {
        return $_SERVER['argc'] > 0;
    }

    /**
     * Set up the include path
     */
    protected function initIncludePath()
    {
        // We set this up in index.php
        $rootPath = get_include_path();

        $this->paths = array();
        $this->paths['lib'] = $rootPath . DIRECTORY_SEPARATOR . 'lib';
        $this->paths['modules'] = $rootPath . DIRECTORY_SEPARATOR . 'modules';

        $includePath = implode(PATH_SEPARATOR, $this->paths);
        set_include_path($includePath);

        $this->paths['root'] = $rootPath;
        $this->paths['configuration'] = $rootPath . DIRECTORY_SEPARATOR . 'configuration';
        $this->paths['tmp'] = sys_get_temp_dir();
    }

    /**
     * Given a module, add its paths to the include path so we can do work
     *
     * @todo Maybe I should just add all module paths? What if we need something from another module?
     *
     * @param string $module
     */
    public function addModuleToIncludePath($module)
    {
        $includePath = get_include_path();
        $newPath = $this->getModulePath($module);
        if ($newPath) {
            $includePath .= PATH_SEPARATOR . $newPath .
                            PATH_SEPARATOR . $newPath . DIRECTORY_SEPARATOR . 'controllers' .
                            PATH_SEPARATOR . $newPath . DIRECTORY_SEPARATOR . 'models' .
                            PATH_SEPARATOR . $newPath . DIRECTORY_SEPARATOR . 'views';

            set_include_path($includePath);
        }
    }

    /**
     * Get the fully qualified path for the desired resource location
     */
    public function getPath($path)
    {
        if (isset($this->paths[$path])) {
            return $this->paths[$path];
        }

        return false;
    }

    /**
     * Initialize the autoloader
     */
    protected function initLoader()
    {
        if (function_exists('zend_shm_cache_fetch')) {
            require_once 'Naked/Loader/Zend.php';
            Zend::registerAutoload();
        } else  if (function_exists('apc_fetch')) {
            require_once 'Naked/Loader/Apc.php';
            Apc::registerAutoload();
        } else {
            require_once 'Naked/Loader.php';
            Loader::registerAutoload();
        }
    }

    /**
     * Load some core classes to avoid autoloading
     */
    protected function loadCoreFiles()
    {
        $coreFiles = array(
            'Naked\Application\Dispatcher.php',
            'Naked\Log.php',
            'Naked\UnitOfWork.php',
            'Naked\Controller.php',
            'Naked\Request.php',
            'Naked\Response.php',
            'Naked\Template\Context.php',
            'Naked\DI.php',
            'Naked\DI\Registry.php',
            'Naked\DI\Builder.php',
            'Naked\DI\ConfigLoader.php',
            'Naked\DI\Buildable.php',
            'Naked\DI\Specification.php',
            'Naked\DI\FactoryMethod.php',
            'Naked\Annotations\Annotation.php',
            'Naked\Annotations\ClassAnnotations.php',
            'Naked\Annotations\ReflectionClass.php',
            'Naked\Annotations\Builder.php',
            'Naked\Annotations\Registry.php',
            'Naked\Annotations.php',
            'Naked\Application.php',
            'Naked\Application\Configuration\Builder.php',
            'Naked\Application\Configuration.php',
            'Naked\Routing\Builder.php',
            'Naked\Routing\PathMatching.php',
            'Naked\Routing\Routes.php',
            'Naked\Routing\BasicRoute.php',
            'Naked\Cache.php',
            'Naked\Cache\Memcached.php',
            'Naked\UnitOfWork.php',
            'Naked\DomainModel.php'
        );

        foreach ($coreFiles as $file) {
            require_once $this->paths['lib'] . DIRECTORY_SEPARATOR . $file;
        }
    }

    /**
     * Get the modules that are installed in this application
     *
     * @return array
     */
    public function getModules()
    {
        if (is_null($this->modules)) {
            $this->modules = array();
            $directory = new \DirectoryIterator($this->paths['modules']);

            foreach ($directory as $file) {
                if ($file->isDir() && !$file->isDot()) {
                    $this->modules[] = $file->getFilename();
                }
            }
        }

        return $this->modules;
    }

    /**
     * Get the path for the specified module
     *
     * @param string $module
     * @return string
     */
    public function getModulePath($module)
    {
        $paths = $this->getModulePaths();

        if (isset($paths[$module])) {
            return $paths[$module];
        }

        return null;
    }

    /**
     * Get the fully qualified paths for the modules installed in this application
     *
     * @return array
     */
    public function getModulePaths()
    {
        if (is_null($this->modulePaths)) {
            $this->modulePaths = array();
            foreach ($this->getModules() as $module) {
                $this->modulePaths[$module] = $this->getPath('modules') . DIRECTORY_SEPARATOR . $module;
            }
        }

        return $this->modulePaths;
    }
}
