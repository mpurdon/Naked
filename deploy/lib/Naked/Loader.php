<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

use Naked\Loader\Exception;

/**
 * Handles automatically loading classes
 *
 * @package default
 * @author Matthew Purdon
 */
class Loader
{
    /**
     * Register {@link autoload()} with spl_autoload()
     *
     * @return void
     * @throws Zend_Exception if spl_autoload() is not found
     * or if the specified class does not have an autoload() method.
     */
    public static function registerAutoload()
    {
        if (!function_exists('spl_autoload_register')) {
            throw new \RuntimeException('spl_autoload does not exist in this PHP installation');
        }

        spl_autoload_register(array('Naked\Loader', 'autoload'));
    }

    /**
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Naked_Loader', 'autoload'));
     * </code>
     *
     * @param string $class
     * @return string|false Class name on success; false on failure
     */
    public static function autoload($class)
    {
        try {
            static::loadClass($class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Loads a class from a PHP file.  The filename must be formatted
     * as "$class.php".
     *
     * It will attempt to load it from PHP's include_path.
     *
     * @param string $class      - The full class name of a Zend component.
     * @param string|array $dirs - OPTIONAL Either a path or an array of paths
     *                             to search.
     * @return void
     * @throws Naked_Exception
     */
    public static function loadClass($class)
    {
        if (strlen($class) == 0) {
            throw new \RuntimeException("You must specify a class name to load");
        }

        $normalizedClass = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $classPath = $normalizedClass . '.php';
        if((include($classPath)) && (class_exists($class) || interface_exists($class))) {
            return true;
        }

        throw new \RuntimeException("File '{$classPath}' which should contain the class '{$class}' does not exist");
    }

    /**
     * Get the file path for the file
     *
     * @param string $file
     * @return string
     */
    public static function getQualifiedPath($file)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;
            //echo "-- Trying $fullPath ";
            if (file_exists($fullPath)) {
                //echo "ok<br>";
                return $fullPath;
            }
            //echo "fail<br>";
        }

        return null;
    }
}
