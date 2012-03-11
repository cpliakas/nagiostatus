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
 * Renders the status data as JSON.
 *
 * @package    Nagiostatus
 * @subpackage Plugin
 */
class Nagiostatus_Plugin_Json extends Nagiostatus_Plugin_Abstract
{
    /**
     * Returns the status data in machine readable format.
     */
    public function execute()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            // Encode <, >, ', &, and " using json_encode() options parameter.
            $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
            return json_encode($var, $options);
        } else {
            // Work around for poor JSON encoding in PHP 5.2.
            return $this->encode($this->toArray());
        }
    }

    /**
     * Helper function for properly encoding JSON properly.
     *
     * @param mixed $var
     *   The variable being encoded.
     *
     * @return string
     *   The variable in JSON.
     *
     * @see http://api.drupal.org/api/drupal/includes%21json-encode.inc/function/drupal_json_encode_helper/7
     */
    function encode($var) {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!

            case 'integer':
            case 'double':
                return $var;

            case 'resource':
            case 'string':
                // Always use Unicode escape sequences (\u0022) over JSON escape
                // sequences (\") to prevent browsers interpreting these as
                // special characters.
                $replacePairs = array(
                    // ", \ and U+0000 - U+001F must be escaped according to the
                    // RFC 4627 standard.
                    '\\' => '\u005C',
                    '"' => '\u0022',
                    "\x00" => '\u0000',
                    "\x01" => '\u0001',
                    "\x02" => '\u0002',
                    "\x03" => '\u0003',
                    "\x04" => '\u0004',
                    "\x05" => '\u0005',
                    "\x06" => '\u0006',
                    "\x07" => '\u0007',
                    "\x08" => '\u0008',
                    "\x09" => '\u0009',
                    "\x0a" => '\u000A',
                    "\x0b" => '\u000B',
                    "\x0c" => '\u000C',
                    "\x0d" => '\u000D',
                    "\x0e" => '\u000E',
                    "\x0f" => '\u000F',
                    "\x10" => '\u0010',
                    "\x11" => '\u0011',
                    "\x12" => '\u0012',
                    "\x13" => '\u0013',
                    "\x14" => '\u0014',
                    "\x15" => '\u0015',
                    "\x16" => '\u0016',
                    "\x17" => '\u0017',
                    "\x18" => '\u0018',
                    "\x19" => '\u0019',
                    "\x1a" => '\u001A',
                    "\x1b" => '\u001B',
                    "\x1c" => '\u001C',
                    "\x1d" => '\u001D',
                    "\x1e" => '\u001E',
                    "\x1f" => '\u001F',
                    // Prevent browsers from interpreting these as as special.
                    "'" => '\u0027',
                    '<' => '\u003C',
                    '>' => '\u003E',
                    '&' => '\u0026',
                    // Prevent browsers from interpreting the solidus as special
                    // and non-compliant JSON parsers from interpreting // as a
                    // comment.
                    '/' => '\u002F',
                    // While these are allowed unescaped according to ECMA-262,
                    // section 15.12.2, they cause problems in some JSON
                    // parsers.
                    "\xe2\x80\xa8" => '\u2028', // U+2028, Line Separator.
                    "\xe2\x80\xa9" => '\u2029', // U+2029, Paragraph Separator.
                );
                return '"' . strtr($var, $replacePairs) . '"';

            case 'array':
            // Arrays in JSON can't be associative. If the array is empty or if
            // it has sequential whole number keys starting with 0, it's not
            // associative so we can go ahead and convert it as an array.
            if (empty($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                $output = array();
                foreach ($var as $v) {
                    $output[] = $this->encode($v);
                }
                return '[ ' . implode(', ', $output) . ' ]';
            }

            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] = $this->encode(strval($k)) . ':' . $this->encode($v);
                }
                return '{' . implode(', ', $output) . '}';

            default:
                return 'null';
        }
    }
}
