<?php

namespace Naked\Routing;

/**
 * Default route when no match was found
 *
 * @package Naked
 * @subpackage Routing
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class DefaultRoute extends Route
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('home', '$^');

        $this->setModule('default');
        $this->setController('Index');
        $this->setAction('index');
    }
}
