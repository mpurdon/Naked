<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace index\controllers;

use Naked\DI;
use Naked\Controller;
use Naked\Template\Context;

use index\models\Car;
use index\models\Car\Bmw;

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
        $car = $di->get('index\models\Car', 'commuting');
        $c->spec_car = (string)$car;

        /*
        $pets1 = Car::objects()->filter('id_gte=1', 'id_lte=10');
        $pets2 = $pets1->orderBy('+name', '-age', 'email');
        $pets3 = $pets2->limit(10);
        $pets4 = $pets3->filter('weight_range=115,120');
        $pets5 = $pets4->orderBy('-lastname', '-name');

        echo "Filter SQL:<br>$pets1<br>";
        echo "Order SQL:<br>$pets2<br>";
        echo "Limit SQL:<br>$pets3<br>";
        echo "Range filter SQL:<br>$pets4<br>";
        echo "New order SQL:<br>$pets5<br>";

        foreach ($pets1 as $pet) {
            echo "$pet<br>";
        }
        */

        /*
        $map = new \Naked\Objects\Map(get_class($car), $di->getAnnotations());
        echo '<pre>',var_dump($map),'</pre>';

        $query = new \Naked\Objects\Query\MySQL($map);
        $query->addFilter('model_eq=750i');
        $query->addOrdering('+model');
        $query->limit(10);
        echo "$query<br>";
        */

        $cars = Bmw::objects()->filter('year_gt=2001');
        //var_dump($cars);

        echo "Found ",count($cars)," cars<br>";
        foreach ($cars as $car) {
            var_dump($car);
        }

        /*
        $manager = Car::objects();
        $manager->insert($car);
        */

        //echo '<pre>',var_dump($manager),'</pre>';

        return $this->directToTemplate('index/Home/index.phtml', $c);
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

        return $this->directToTemplate('index/Home/bench.phtml', $c);
    }
}
