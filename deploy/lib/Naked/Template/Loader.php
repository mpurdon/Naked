<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

use Naked\Template;

/**
 * Template Loader
 */
class Loader
{
    /**
     * Get a template from disk
     *
     * @param string $templateFile
     * @return Naked\Template
     */
    public static function getTemplate($templateFile)
    {
        // @todo the template loader should look in a "core" location for templates first

        $parts = explode('/', $templateFile);
        $newParts = array(
            $parts[0],
            'views/templates',
            $parts[1],
            $parts[2]
        );
        $templateFile = implode('/', $newParts);

        //echo "Loading template {$templateFile}<br>";
        return new Template(file_get_contents($templateFile, FILE_USE_INCLUDE_PATH));
    }
}
