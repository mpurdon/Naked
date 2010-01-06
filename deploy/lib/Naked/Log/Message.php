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
 * Represents a log message
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Message
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct($message, $priority)
    {
        $this->message = $message;
        $this->priority = $priority;
    }

    /**
     * Increment the count of the number of times this message was sent
     */
    public function wasRepeated()
    {
        $this->count++;
    }

    /**
     * Return the priority of this message
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * The string representation of this message
     *
     * @return string
     */
    public function __toString()
    {
        $string = $this->message;
        if ($this->count > 0) {
            $string .= " (repeated {$this->count} more times)";
        }

        return $string;
    }
}
