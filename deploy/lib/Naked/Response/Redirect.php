<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Response;
use Naked\Response;

/**
 * Response that redirects the client browser to the desired URL
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Redirect extends Response
{
    /**
     * The string representation of this Response
     *
     * @return string
     */
    public function __toString()
    {
        header('Location: ' . (string) $this->body);
    }
}
