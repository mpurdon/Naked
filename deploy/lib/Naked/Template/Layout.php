<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

/**
 * Manages the layout
 *
 * @package Naked
 * @author Matthew Purdon
 */
class Layout
{
    /**
     * @array
     */
    protected $views;

    /**
     * Constructor
     */
    public function __construct()
    {
        echo "Instantiating layout<br>";
    }

    /**
     * Render the internal template
     */
    public function render()
    {
        echo 'Rendering layout<br>';
    }
}
