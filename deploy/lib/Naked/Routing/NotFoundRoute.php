<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Routing;

/**
 * Could not find a route that matches the path
 *
 * @package Naked
 * @subpackage Routing
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class NotFoundRoute extends BasicRoute
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('NotFound', '^$');

        $this->setModule('default');
        $this->setController('error');
        $this->setAction('FourOhFour');
    }
}
