<?php

/**
 * Nagiostatus
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License that is bundled
 * with this package in the file LICENSE.txt. It is also available for download
 * at http://www.gnu.org/licenses/gpl-2.0.txt.
 *
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @copyright  Copyright (c) 2012 Chris Pliakas <cpliakas@gmail.com>
 */

/**
 * Container for messages set by the parser.
 *
 * @package    Nagiostatus
 * @subpackage Message
 */
class Nagiostatus_Message
{
    /**
     * Zend_Log compatible severity constants.
     */
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

    /**
     * The message.
     *
     * @var string
     */
    protected $_message;

    /**
     * The severity of the message, see class constants.
     *
     * @var string
     */
    protected $_severity;

    /**
     * Additional data relevant to the message.
     *
     * @var array
     */
    protected $_data;

    /**
     * Sets the message and severity.
     *
     * @param string $message
     *   The message.
     * @param int $severity
     *   The severity of the message, see class constants.
     * @param array $data
     *   Additional data relevant to the message.
     */
    public function __construct($message, $severity, array $data)
    {
        $this->_message = $message;
        $this->_severity = $severity;
        $this->_data = $data;
    }

    /**
     * Returns the message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Returns the severity.
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->_severity;
    }

    /**
     * Returns the additional data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
