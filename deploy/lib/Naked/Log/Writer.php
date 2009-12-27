<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Log;

/**
 * Defines the public interface for a log writer object
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
interface Writer
{
    /**
     * Constructor
     *
     * @param integer $logLevel
     */
    public function __construct($logLevel);

    /**
     * Write a log message out
     *
     * @param \Naked\Log\Message $message
     */
    public function write(\Naked\Log\Message $message);
}