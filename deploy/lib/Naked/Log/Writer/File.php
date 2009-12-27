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
 * Log messages to file
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class File implements Writer
{
    /**
     * @var integer
     */
    protected $logLevel;

    /**
     * @var string
     */
    protected $logFile;

    /**
     * Constructor
     *
     * @param integer $logLevel
     */
    public function __construct($logLevel, Naked\Application\Configuration $configuration)
    {
        $this->logLevel = $logLevel;
        $this->logFile = $configuration->log_file;
    }

    /**
     * Write a log message out
     *
     * @param Naked\Log\Message $message
     */
    public function write(Naked\Log\Message $message)
    {
        if ($message->getPriority() <= $this->logLevel) {
            return error_log((string)$message."\n", 3, $this->logFile);
        }

        return true;
    }
}