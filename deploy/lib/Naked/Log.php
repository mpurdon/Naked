<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked;

use Naked\Log\Message;

/**
 * Provides logging services for the application
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Log
{
    protected $writers = array();
    protected $messages = array();
    protected $lastMessage;

    /**
     * Constructor
     */
    public function __construct()
    {}

    /**
     * Add a log writer to the logger
     *
     * @param Naked\Log\LogWriter $writer
     */
    public function addWriter(LogWriter $writer)
    {
        $this->writers[] = $writer;
    }

    /**
     *
     * @param $message
     */
    public function log ($message, $priority=LOG_NOTICE)
    {
        $md5 = md5($message);

        // If we have a new log message, send the last one
        if ($this->messageIsNew($md5)) {
            $this->flushLastMessage();
            $this->lastMessage = $md5;
            $this->messages[$md5] = new Message($message, $priority);

            return true;
        }

        // Otherwise we just add to the count of the last message.
        $this->messages[$md5]->wasRepeated();
    }

    /**
     * Determine if the MD5 for the message matches the last one
     *
     * @param string $md5
     * @return boolean
     */
    protected function messageIsNew($md5)
    {
        return $this->lastMessage != $md5;
    }

    /**
     * Send the last message to the writers
     */
    protected function flushLastMessage()
    {
        $this->messages[$this->lastMessage];
        if (!$message) {
            return false;
        }

        foreach ($this->writers as $writer) {
            $writer->write($message);
        }

        unset($this->messages[$this->lastMessage]);
        return true;
    }

    /**
     * Destructor
     *
     *  Ensure we flush the last log message
     */
    public function __destruct()
    {
        $this->flushLastMessages();
        unset($this);
    }
}

