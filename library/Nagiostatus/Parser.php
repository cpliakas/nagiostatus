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
 * @package    Nagiostatus
 * @subpackage Parser
 */
class Nagiostatus_Parser
{
    /**
     * The name of the file to pass to fopen().
     *
     * @var string
     */
    protected $_filename;

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
     * Builds the document.
     *
     * @param Nagiostatus_Plugin_Abstract $plugin
     *   The plugin being used to render the data.
     */
    public function buildDocument(Nagiostatus_Plugin_Abstract $plugin)
    {
        // Opens file, iterates over lines.
        if (!$fh = @fopen($this->_filename, 'r')) {
            // @todo Handle errors
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
            if ($inStatus) {
                if (false !== ($pos = strpos($buffer, '='))) {
                    $key = ltrim(substr($buffer, 0, $pos));
                    $status[$key] = substr($buffer, $pos + 1);
                } elseif (strpos($buffer, '}')) {
                    $plugin->execute($status);
                    $inStatus = false;
                } else {
                    // @todo Handle errors
                }
            } elseif (strpos($buffer, '{')) {
                $inStatus = true;
                $status = array('_type' => rtrim($buffer, " {"));
            } elseif (0 !== strpos($buffer, '#')) {
                // @todo Handle errors
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
        if (isset(self::$_plugins[$pluginName])) {
            if ($return) {
                ob_start();
            }
            $plugin = new self::$_plugins[$pluginName]($this);
            $this->buildDocument($plugin);
            return ($return) ? ob_get_clean() : true;
        } else {
            // @todo Handle errors
        }
        return false;
    }
}