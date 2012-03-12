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
 * Renders the status data as XML.
 *
 * @package    Nagiostatus
 * @subpackage Plugin
 */
class Nagiostatus_Plugin_Xml extends Nagiostatus_Plugin_Abstract
{
    /**
     * Overrides Nagiostatus_Plugin_Abstract::initDocument().
     */
    public function initDocument()
    {
        echo '<?xml version="1.0" encoding="UTF-8" ?><statusDat>';
    }

    /**
     * Implements Nagiostatus_Plugin_Abstract::execute().
     */
    public function execute(array $status)
    {
        $type = $this->escape($status['_type']);
        unset($status['_type']);
        echo '<status type="' . $type . '">';
        echo "<type>$type</type>";
        echo '<report>';
        foreach ($status as $reportName => $reportData) {
            echo "<$reportName>" . $this->escape($reportData) . "</$reportName>";
        }
        echo '</report>';
        echo '</status>';
    }

    /**
     * Overrides Nagiostatus_Plugin_Abstract::finalizeDocument().
     */
    public function finalizeDocument()
    {
        echo '</statusDat>';
    }

    /**
     * Escapes a string for XML.
     *
     * @param string $string
     *   The string being escaped.
     *
     * @return string
     *   The XML-safe string.
     *
     * @see http://www.phpedit.net/snippet/Remove-Invalid-XML-Characters
     */
    public function escape($string)
    {
        $escaped = '';

        // Removes invalid characters.
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $current = ord($string{$i});
            if (
                ($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF))
            ) {
                $escaped .= chr($current);
            } else {
                $escaped .= " ";
            }
        }

        // Note: we don't escape apostrophes because of the many clients that
        // don't support numerical entities (and XML in general) properly.
        // @see http://api.drupal.org/api/drupal/core%21includes%21xmlrpc.inc/function/xmlrpc_value_get_xml/8
        return htmlspecialchars($escaped);
    }
}
