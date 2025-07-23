<?php
/**
 * AD SPL autoloader.
 * PHP Version 7.1.1
 * @package AD
 * @version 0.0.1
 * @link none
 * @author Philip Tomson (Avalon) <philip.tomson@avalon-zone.be>
 * @copyright 2021 - Tomson Philip
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * AD SPL autoloader.
 * @param string $classname The name of the class to load
 */

$_SESSION["ADFastMode"] = false;

function ADAutoload($classname)
{
    
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.$classname.'.php';
    
    if (is_readable($filename)) {
        require $filename;
    }
}

spl_autoload_register('ADAutoload', true, true);