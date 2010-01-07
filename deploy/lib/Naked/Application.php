<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

use Naked\Application;
use Naked\Application\Dispatcher;
use Naked\Routing;
use Naked\Routing\NotFoundRoute;
use Naked\DI;

/**
 * Represents the big show
 *
 * @author matthew
 */
class Application
{
    /**
     *  @var Naked\Application\Environment
     */
     protected $environment;

     /**
      * @var Naked\Application\Configuration
      */
     protected $configuration;

    /**
     * Constructor
     *
     * @Inject
     * @author Matthew Purdon <matthew@codenaked.org>
     */
    public function __construct(\Naked\Application\Environment $environment,
                                \Naked\Application\Configuration $configuration)
    {
        $this->environment = $environment;
        $this->configuration = $configuration;

        $this->setDefaultTimezone();
        $this->setLocale();
    }

    /**
     * Set the default timezone for the application
     *
     * @return boolean
     */
    public function setDefaultTimezone()
    {
        if ($this->configuration->timezone) {
            return date_default_timezone_set($this->configuration->timezone);
        }

        date_default_timezone_set('America/Toronto');
    }

    /**
     * Set the locale for the application
     *
     * @return boolean
     */
    public function setLocale()
    {
        if ($this->configuration->locale) {
            return setlocale(LC_ALL, $this->configuration->locale);
        }

        setlocale('en_US');
    }

    /**
     * Run the application based on the provided request and environment
     *
     * @param Request $request
     * @return void
     */
    public function run($request)
    {
        // Get the route we are going to dispatch
        $di = DI::container();
        $routes = $di->get('Naked\Routing\Routes');
        $route = $routes->matches($request->path());

        // Dispatch the route we found otherwise send a 404 response
        try {
            $dispatcher = $di->get('Naked\Application\Dispatcher');
            if ($route) {
                $dispatcher->dispatch($route, $request);
            } else {
                $dispatcher->dispatch(new NotFoundRoute(), $request);
            }
        } catch (\RuntimeException $e) {
            echo 'Application Caught Run Time Exception: ',$e->getMessage(),'<pre>',var_dump($e->getTraceAsString()),'<pre>';
        } catch (\Exception $e) {
            echo 'Application Caught Exception: ',$e->getMessage(),'<pre>',var_dump($e->getTraceAsString()),'<pre>';
        }

        $di->get('Naked\Log')->flushMessages();
    }
}
