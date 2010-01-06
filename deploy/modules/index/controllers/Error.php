<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

use Naked\Controller;
use Naked\Template\Context;

/**
 * A controller to handle error actions
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Error extends Controller
{
    /**
     * Handle a 404 not found error
     */
    public function fourOhFourAction()
    {
        $c = new Context();
        $c->request_path = (string) $this->request;
        return $this->directToTemplate('default/Error/404.phtml', $c);
    }

    /**
     * Handle a 500 internal server error
     */
    public function fiveHundredAction()
    {
        $c = new Context();
        $c->request_path = (string) $this->request;
        return $this->directToTemplate('default/Error/500.phtml', $c);
    }
}
