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

require_once 'FirePHPCore/FirePHP.class.php';

/**
 * Log messages to Firebug
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Firebug implements Writer
{
    /**
     * @var integer
     */
    protected $logLevel;

    /**
     * @var Fire
     */
    protected $firePhp;

    /**
     * Constructor
     *
     * @param integer $logLevel
     */
    public function __construct($logLevel)
    {
        $this->logLevel = $logLevel;
        $this->firePhp = \FirePHP::getInstance(true);
    }

    /**
     * Write a log message out
     *
     * @param Naked\Log\Message $message
     * @return boolean
     */
    public function write(\Naked\Log\Message $message)
    {
        if ($message->getPriority() <= $this->logLevel) {
            return $this->firePhp->log((string)$message."\n");
        }

        return true;
    }
}