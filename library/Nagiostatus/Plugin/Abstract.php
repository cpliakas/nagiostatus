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
 * Base class for Nagiostatus plugins.
 *
 * Plugins render the status.dat file in machine readable formats and output the
 * data directly to STDOUT as opposed to building the document in memory.
 *
 * @package    Nagiostatus
 * @subpackage Plugin
 */
abstract class Nagiostatus_Plugin_Abstract
{
    /**
     * The calling Nagiostatus_Parser instance.
     *
     * @var Nagiostatus
     */
    protected $_parser;

    /**
     * Sets the calling Nagiostatus_Parser instance.
     *
     * @param Nagiostatus_Parser $status
     *   The calling Nagiostatus_Parser instance.
     */
    public function __construct(Nagiostatus_Parser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Returns the calling Nagiostatus_Parser instance.
     *
     * @return Nagiostatus_Parser
     *   The calling Nagiostatus_Parser instance.
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Outputs any document headers.
     */
    public function initDocument()
    {
        // Override to add any headers ...
    }

    /**
     * Outputs the status data in machine readable format.
     *
     * @param array $status
     *   An individual status information block parsed as an associative array.
     */
    abstract public function execute(array $status);

    /**
     * Outputs any document footers.
     */
    public function finalizeDocument()
    {
        // Override to add any footers ...
    }
}
