<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

use Naked\DI;
use Naked\Controller;
use Naked\Template\Context;

/**
 * Provides Home actions for the defaut module
 *
 * @package default
 * @author Matthew Purdon
 */
class Home extends Controller
{
    /**
     * Perform the index action
     *
     * @return Naked\Response
     * @author Matthew Purdon <matthew@codenaked.org>
     */
    public function indexAction()
    {
        $c = new Context();
        $c->my_name = 'Matthew Variable';
        $c->date = date('Y-m-d');
        $c->time = date('H:i:s');
        $c->num_things = 2;
        $c->true_value = 1;
        $c->false_value = 0;
        $c->unknown_value = null;
        $c->is_development = $this->environment->isDevelopment();
        $c->locale = $this->configuration->locale;

        $di = DI::container();
        $car = $di->get('models\Car', 'commuting');
        $c->spec_car = (string)$car;

        return $this->directToTemplate('default/Home/index.phtml', $c);
    }

    /**
     * Perform the benchmark action
     *
     * @return void
     * @author Matthew Purdon
     */
    public function benchAction()
    {
        //echo '<pre>',var_dump($this),'</pre>';

        $c = new Context();
        $c->my_name = 'Matthew Variable';

        return $this->directToTemplate('default/Home/bench.phtml', $c);
    }
}
