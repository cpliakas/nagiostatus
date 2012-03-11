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
     * An associative array modeling the status.dat data.
     *
     * @var array
     */
    protected $_data = array();

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
        $inStatus = FALSE;
        $errors = array();

        // Opens file, iterates over lines.
        if (!$fh = @fopen($filename, 'r')) {
            // @todo Handle errors
        }
        while (!feof($fh)) {

            // Reads line into buffer, skips processing if empty.
            $buffer = trim((string) fgets($fh, 1024));
            if (!$buffer) {
                continue;
            }

            // Processes buffer, adds statuses and reports to data array.
            // @todo Eliminate preg_match (?) and reorder for faster processing.
            if (preg_match('/^([_a-zA-Z]*)\\s*{$/', $buffer, $matches)) {
                $inStatus = $matches[1];
                $status = array();
            } elseif (0 === strpos($buffer, '}')) {
                $this->_data[$inStatus][] = $status;
                $inStatus = FALSE;
            } elseif ($inStatus) {
                if (preg_match('/^([_a-zA-Z]*)=(.*)$/', $buffer, $matches)) {
                    $status[$matches[1]] = $matches[2];
                } else {
                    // @todo Handle errors
                }
            } else {
                // @todo Handle errors
            }
        }

        fclose($fh);
    }

    /**
     * Returns the prsed data array.
     *
     * @return array
     *   The associative array of status data.
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Renders the data using the rendering plugin.
     *
     * @param string $pluginName
     *   The name of the plugin used to render the data, i.e. "xml" or "json".
     *
     * @return string|false
     *   The rendered status data, false if there are errors.
     */
    public function render($pluginName = null)
    {
        if (null === $pluginName) {
            $pluginName = self::getDefaultPlugin();
        }
        if (isset(self::$_plugins[$pluginName])) {
            $plugin = new self::$_plugins[$pluginName]($this);
            return $plugin->execute();
        } else {
            // @todo Handle errors
        }
        return false;
    }
}