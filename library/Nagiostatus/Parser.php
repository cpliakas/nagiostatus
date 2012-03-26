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
 * Library that parses Nagios status.dat files into machine readable formats.
 *
 * The parser implements the observer pattern so that loggers and error handlers
 * can be attached. In addition, plugins can be registered that render the data
 * in different machine readable formats.
 *
 * @package    Nagiostatus
 * @subpackage Parser
 */
class Nagiostatus_Parser implements SplSubject
{
    /**
     * The name of the file to pass to fopen().
     *
     * @var string
     */
    protected $_filename;

    /**
     * An array of messages set during parsing.
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * The name of the file to pass to fopen().
     *
     * @var array
     */
    protected $_observers = array();

    /**
     * Plugin registry.
     *
     * @var array
     */
    static protected $_plugins = array(
        'xml' => 'Nagiostatus_Plugin_Xml',
        'json' => 'Nagiostatus_Plugin_Json',
    );

    /**
     * Default plugin.
     *
     * @var string
     */
    static protected $_defaultPlugin = 'xml';

    /**
     * Registers a plugin.
     *
     * @param string $pluginName
     *   The machine name of the plugin.
     * @param string $className
     *   Then name of the plugin class.
     */
    static public function registerPlugin($pluginName, $className)
    {
        self::$_plugins[$pluginName] = $className;
    }

    /**
     * Sets the default plugin.
     *
     * @param string $pluginName
     *   The machine name of the plugin.
     */
    static public function setDefaultPlugin($pluginName)
    {
        self::$_defaultPlugin = $pluginName;
    }

    /**
     * Returns the default plugin.
     */
    static public function getDefaultPlugin()
    {
        return self::$_defaultPlugin;
    }

    /**
     * Implements SplSubject::attach().
     */
    public function attach(SplObserver $observer)
    {
        $id = spl_object_hash($observer);
        $this->_observers[$id] = $observer;
    }

    /**
     * Implements SplSubject::detach().
     */
    public function detach(SplObserver $observer)
    {
        $id = spl_object_hash($observer);
        unset($this->_observers[$id]);
    }

    /**
     * Implements SplSubject::notify().
     */
    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Sets a message, alerts observers.
     *
     * @param string $message
     *   The message.
     * @param int $severity
     *   The severity of the message, see Nagiostatus_Message class constants.
     *   Defaults to Nagiostatus_Message::INFO.
     * @param array $data
     *   Additional data relevant to the message. Defaults to an empty array
     *
     * @return Nagiostatus_Parser
     *   An instance of this class.
     */
    public function setMessage($message, $severity = Nagiostatus_Message::INFO, array $data = array())
    {
        $this->_messages[] = new Nagiostatus_Message($message, $severity, $data);
        $this->notify();
        return $this;
    }

    /**
     * Returns the last message.
     *
     * @return Nagiostatus_Message|false
     *   The last message edded to the system, false if there are no messages.
     */
    public function getLastMessage()
    {
        return end($this->_messages);
    }

    /**
     * Returns all messages.
     *
     * @return array
     *   The array of Nagiostatus_Message objects.
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Parses the status data into an associatice array.
     *
     * @param string $filename
     *   The name of the file to pass to fopen(). This could be an absolute /
     *   relative path to a file or php://stdin.
     */
    public function __construct($filename)
    {
        $this->_filename = $filename;
    }

    /**
     * Returns the filename set in the constructor.
     *
     * @return string
     *   The filename set in the constructor.
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Builds the document.
     *
     * @param Nagiostatus_Plugin_Abstract $plugin
     *   The plugin being used to render the data.
     */
    public function buildDocument(Nagiostatus_Plugin_Abstract $plugin)
    {
        $data = array();

        // Opens file, iterates over lines.
        if (!$fh = @fopen($this->_filename, 'r')) {
            $this->setMessage('error reading file', Nagiostatus_Message::CRIT);
        }

        // Initializes document, iterates over lines.
        $plugin->initDocument();
        while (!feof($fh)) {

            // Reads line into buffer, skips processing if empty.
            $buffer = rtrim(fgets($fh, 1024));
            if (!$buffer) {
                continue;
            }

            // Processes buffer.
            try {
                if ($inStatus) {
                    if (false !== ($pos = strpos($buffer, '='))) {
                        // Parse out report data inside of a status block.
                        $key = ltrim(substr($buffer, 0, $pos));
                        $status[$key] = substr($buffer, $pos + 1);
                    } elseif (strpos($buffer, '}')) {
                        // Closing tag for status block found.
                        $plugin->execute($status);
                        $inStatus = false;
                    } else {
                        // We encountered something unexpected.
                        throw new Exception('invalid report');
                    }
                } elseif (strpos($buffer, '{')) {
                    // Starting tag for status block found.
                    $inStatus = true;
                    $status = array('_type' => rtrim($buffer, " {"));
                } elseif (0 !== strpos($buffer, '#')) {
                    // We encountered something unexpected.
                    throw new Exception('invalid line');
                }
            } catch (Exception $e) {
                $data = array('buffer' => $buffer);
                if ($inStatus) {
                    $data['status'] = $inStatus;
                }
                $this->setMessage($e->getMessage(), Nagiostatus_Message::ERR, $data);
            }
        }

        // Finalizes document, closes handle.
        $plugin->finalizeDocument();
        fclose($fh);
    }

    /**
     * Outputs the data using the rendering plugin.
     *
     * @param string $pluginName
     *   The name of the plugin used to render the data, i.e. "xml" or "json".
     *   Defaults to null meaning the default plugin is used.
     * @param bool $return
     *   Return the variable representation instead of outputing it.
     *
     * @return string|bool
     *   Returns false on errors. If $return is false, this method returns true
     *   on success. If $return is true, this method returns the parsed document
     *   as a string in the machine readable format determined by the plugin.
     */
    public function render($pluginName = null, $return = false)
    {
        if (null === $pluginName) {
            $pluginName = self::getDefaultPlugin();
        }
        $data = array('plugin name' => $pluginName);
        if (isset(self::$_plugins[$pluginName])) {
            // Starts buffering if document is being returned as a string.
            if ($return) {
                ob_start();
            }

            // Sets info message and begins parsing.
            $this->setMessage('begin parsing', Nagiostatus_Message::INFO, $data);
            $plugin = new self::$_plugins[$pluginName]($this);
            $this->buildDocument($plugin);
            $this->setMessage('end parsing', Nagiostatus_Message::INFO, $data);

            // Returns document as a string or true if outputted directly.
            return ($return) ? ob_get_clean() : true;
        } else {
            $this->setMessage('invalid plugin', Nagiostatus_Message::ERR, $data);
        }
        return false;
    }
}
