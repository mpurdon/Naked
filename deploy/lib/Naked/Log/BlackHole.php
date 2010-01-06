<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Log;

use Naked\Log;

/**
 * Provides logging services for the application
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class BlackHole extends Log
{
    /**
     * Add a log writer to the logger
     *
     * @param Naked\Log\LogWriter $writer
     */
    public function addWriter(\Naked\Log\Writer $writer)
    {}

    /**
     *
     * @param $message
     */
    public function log ($message, $priority=LOG_NOTICE)
    {}

    /**
     * Destructor
     *
     *  Ensure we flush the last log message before we die
     */
    public function __destruct()
    {}
}
