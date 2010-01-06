<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\Annotations;

/**
 * A Registry for annotations
 *
 * Uses shared memory caching to make things faster
 */
class Registry
{
    /**
     * @var boolean
     */
    protected $hasNewAnnotations = false;

    /**
     * @var array
     */
    protected $annotations=array();

    /**
     * Constructor
     */
    public function __construct()
    {
        // @todo enable annotation caching
        //$this->loadCachedAnnotations();
    }

    /**
     * Determine if we have annotations for a given class
     *
     * @param $class
     * @return boolean
     */
    public function has($class){
        return isset($this->annotations[$class]);
    }

    /**
     * Get annotations for a given class
     */
    public function get($class)
    {
        if (isset($this->annotations[$class])) {
            return $this->annotations[$class];
        }

        return null;
    }

    /**
     * Set the annotatations for the given class
     *
     * @param string $class
     * @param Naked\Annotations\ClassAnnotations $annotations
     */
    public function set($class, $annotations)
    {
        //echo "Added new annotations for class $class<pre>",var_dump($annotations),"</pre>";
        $this->annotations[$class] = $annotations;
        $this->hasNewAnnotations = true;
        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->saveCachedAnnotations();
    }

    /**
     * Load cached annotations
     */
    protected function loadCachedAnnotations()
    {
        $annotationKey = 'class_annotations';

        $annotations = false;
        // @todo loadCachedAnnotations needs a strategy
        if (function_exists('zend_shm_cache_fetch')) {
            $annotations = zend_shm_cache_fetch($annotationKey);
        } else if (function_exists('apc_fetch')) {
            $annotations = apc_fetch($annotationKey);
        }

        if ($annotations) {
            //echo "Loaded annotations ",implode(',', array_keys($annotations))," from cache<br>";

            /*
            foreach ($annotations as $class => $annotation) {
                echo "Loaded annotations  for class $class<pre>",var_dump($annotation),"</pre>";
            }
            */

            $this->annotations = $annotations;
            return true;
        }

        return false;
    }

    /**
     * Save annotations in the cache if we have new ones.
     */
    protected function saveCachedAnnotations()
    {
        if ($this->hasNoNewAnnotations()) {
            return true;
        }

        $annotationKey = 'class_annotations';
        //echo "Saving annotations ",implode(',', array_keys($this->annotations))," to cache<br>";

        // @todo loadCachedAnnotations needs a strategy
        if (function_exists('zend_shm_cache_fetch')) {
            return zend_shm_cache_store($annotationKey, $this->annotations);
        } else  if (function_exists('apc_fetch')) {
            return apc_store($annotationKey, $this->annotations);
        }

        return false;
    }

    /**
     * Determine if we have not added any annotations
     */
    protected function hasNoNewAnnotations()
    {
        return $this->hasNewAnnotations === false;
    }


}
