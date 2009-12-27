<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

/**
 * Represents an HTTP Response to the client
 */
class Response
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * Constructor
     */
    public function __construct($body)
    {
        //echo "Instantiating the response object<br>";
        $this->body = $body;
    }

    /**
     * String representation of this response
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->body;
    }
}
