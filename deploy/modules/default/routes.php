<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

use Naked\Routing\Routes;
use Naked\Routing\BasicRoute;

$routes = new Routes('default', '^');

$home = new BasicRoute('home', '^$');
$home->setController('Home')
     ->setAction('index');

$routes->append($home);

$bench = new BasicRoute('bench', '^bench$');
$bench->setController('Home')
      ->setAction('bench');

$routes->append($bench);
