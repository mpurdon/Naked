<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Loader;

require_once('../lib/Naked/Loader.php');
use Naked\Loader;

/**
 * A loader that stores class paths in Zend Cache
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Zend extends Loader
{
    /**
     * Register {@link autoload()} with spl_autoload()
     *
     * @return void
     */
    public static function registerAutoload()
    {
        if (!function_exists('spl_autoload_register')) {
            throw new \RuntimeException('spl_autoload does not exist in this PHP installation');
        }

        spl_autoload_register(array('Naked\Loader\Zend', 'autoload'));
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
     */
    public static function loadClass($class)
    {
        if (strlen($class) == 0) {
            throw new \RuntimeException("You must specify a class name to load");
        }

        // get class path from cache
        $cacheKey = 'class_path_cache::' . $class;

        //echo "Loading $class using cache key $cacheKey: ";

        $classPath = zend_shm_cache_fetch($cacheKey);

        if ($classPath) {
            //echo " Hit<br>";
            if ((include($classPath))) {
                return true;
            }
        } else if ($classPath === false) {
            //echo " Miss<br>";
            $normalizedClass = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $classPath = self::getQualifiedPath($normalizedClass . '.php');
            if((include($classPath)) && (class_exists($class) || interface_exists($class))) {
                zend_shm_cache_store($cacheKey, $classPath, 3600);
                return true;
            }
        }

        throw new \RuntimeException("File '{$classPath}' which should contain the class '{$class}' does not exist");
    }
}