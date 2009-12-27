<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Log\Writer;

use Naked\Log\Writer;

/**
 * Log messages to the system log
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class BlackHole implements Writer
{
    /**
     * @var integer
     */
    protected $logLevel;

    /**
     * Constructor
     *
     * @param integer $logLevel
     */
    public function __construct($logLevel, Naked\Application\Configuration $configuration)
    {
        $this->logLevel = $logLevel;
    }

    /**
     * Write a log message out
     *
     * @param Naked\Log\Message $message
     */
    public function write(Naked\Log\Message $message)
    {
        return true;
    }
}