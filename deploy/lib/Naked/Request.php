<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

/**
 * Represents an HTTP request
 *
 * @package default
 * @author Matthew Purdon
 */
class Request
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * Construct a Request
     */
    public function __construct($url=null)
    {
        if (is_null($url)) {
            $url = $_SERVER['REQUEST_URI'];
        }

        $requestParts = parse_url($url);

        //echo '<pre>', var_dump($requestParts) ,'</pre>';

        $this->method = $_SERVER['REQUEST_METHOD'];

        if (isset($requestParts['scheme'])) {
            $this->scheme = $requestParts['scheme'];
        }

        if (isset($requestParts['path'])) {
            $this->path = trim($requestParts['path'], '/');
        }

        if (isset($requestParts['query'])) {
            $this->query = $requestParts['query'];
        }
    }

    /**
     * Get a parameter
     *
     * @return string
     */
    public function get($key, $default=null)
    {
        if($this->isGet() && array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }

        if($this->isPost() && array_key_exists($key, $_POST)) {
            return $_POST[$key];
        }

        return $default;
    }

    /**
     * Is the request a GET request?
     *
     * @return boolean
     */
    public function isGet()
    {
        return $this->method == 'GET';
    }

    /**
     * Is the request a POST request?
     *
     * @return boolean
     */
    public function isPost()
    {
        return $this->method == 'POST';
    }

    /**
     * Get the request path
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * String representation of this Request
     *
     * @return string
     * @author Matthew Purdon
     */
    public function __toString()
    {
        return $this->path;
    }
}
