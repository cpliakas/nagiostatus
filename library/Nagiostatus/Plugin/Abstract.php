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
     * The calling Nagiostatus instance.
     *
     * @var Nagiostatus
     */
    protected $_status;

    /**
     * Sets the calling Nagiostatus instance.
     *
     * @param Nagiostatus $status
     *   The calling Nagiostatus instance.
     */
    public function __construct(Nagiostatus $status)
    {
        $this->_status = $status;
    }

    /**
     * Returns the calling Nagiostatus instance.
     *
     * @return Nagiostatus
     *   The calling Nagiostatus instance.
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Renders the status data as an array.
     *
     * @return array
     *   The associative array of status data.
     */
    public function toArray()
    {
        return $this->_status->toArray();
    }

    /**
     * Returns the status data in machine readable format.
     */
    abstract public function execute();
}
