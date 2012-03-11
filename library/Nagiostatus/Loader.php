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
 * A lightweight autoloader for Nagiostatus classes.
 *
 * In most instances you should use a PSR-0 compliant autoloader such as the
 * ones provided by the Zend Framework or Symfony.
 *
 * @package    Nagiostatus
 * @subpackage Loader
 */
class Nagiostatus_Loader
{

    /**
     * Registers the autoloader.
     *
     * @return bool
     */
    static public function register()
    {
        return spl_autoload_register(array(new self, 'load'));
    }

    /**
     * Autoloads a class.
     *
     * @param string $class
     *   The name of the class being loaded.
     */
    static public function load($class)
    {
        if (class_exists($class, FALSE) || interface_exists($class, FALSE)) {
            return;
        }
        if (0 === strpos($class, 'Nagiostatus_')) {
            $class = str_replace(array('Nagiostatus_', '_'), array('', '/'), $class);
            $file = dirname(__FILE__) . '/' . $class . '.php';
            require_once $file;
        }
    }
}
