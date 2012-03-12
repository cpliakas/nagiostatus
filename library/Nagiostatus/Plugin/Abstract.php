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
 * @package    Nagiostatus
 * @subpackage Plugin
 */
abstract class Nagiostatus_Plugin_Abstract
{

    /**
     * The Nagiostatus_Parser instance containing the parsed data.
     *
     * @var Nagiostatus
     */
    protected $_parser;

    /**
     * Sets the Nagiostatus_Parser instance containing the parsed data.
     *
     * @param Nagiostatus_Parser $status
     *   The Nagiostatus_Parser instance containing the parsed data.
     */
    public function __construct(Nagiostatus_Parser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Returns the Nagiostatus_Parser instance containing the parsed data
     *
     * @return Nagiostatus_Parser
     *   The Nagiostatus_Parser instance containing the parsed data
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
     *   The parsed status information.
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
