<?php
/**
 * -------------------------------------------------------------------------------
 * Modul: Intertopia Image Upload
 * -------------------------------------------------------------------------------
 * Copyright (C) 2016, Michael Gnehr - All rights reserved
 * -------------------------------------------------------------------------------
 *  This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Dieses Programm ist Freie Software: Sie können es unter den Bedingungen
 *  der GNU General Public License, wie von der Free Software Foundation,
 *  Version 3 der Lizenz oder (nach Ihrer Wahl) jeder neueren
 *  veröffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 *  Dieses Programm wird in der Hoffnung, dass es nützlich sein wird, aber
 *  OHNE JEDE GEWÄHRLEISTUNG, bereitgestellt; sogar ohne die implizite
 *  Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
 *  Siehe die GNU General Public License für weitere Details.
 *
 *  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 *  Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------------
 *
 * @category        module - tool
 * @package         ii_upload
 * @author          Michael Gnehr
 * @copyright       2016-TODAY, Michael Gnehr
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.6 and up
 * @license 		GNU GPLv3 or any later
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) die(header('Location: index.php'));  
/* -------------------------------------------------------- */

//store old values
$mod_dir_org = null;
$module_directory_org = null;
$module_name_org = null;
$module_function_org = null;
$module_version_org = null;
$module_platform_org = null;
$module_author_org = null;
$module_license_org = null;
$module_description_org = null;

if(isset($mod_dir)) $mod_dir_org = $mod_dir;
if(isset($module_directory)) $module_directory_org = $module_directory;
if(isset($module_name)) $module_name_org = $module_name;
if(isset($module_function)) $module_function_org = $module_function;
if(isset($module_version)) $module_version_org = $module_version;
if(isset($module_platform)) $module_platform_org = $module_platform;
if(isset($module_author)) $module_author_org = $module_author;
if(isset($module_license)) $module_license_org = $module_license;
if(isset($module_description)) $module_description_org = $module_description;

// obtain module directory
$mod_dir = basename(dirname(__FILE__));
require(WB_PATH.'/modules/'.$mod_dir.'/info.php');

// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/'.$module_directory.'/languages/'.LANGUAGE .'.php')) {
    // no module language file exists for the language set by the user, include default module language file DE.php
    require_once(WB_PATH .'/modules/'.$module_directory.'/languages/DE.php');
} else {
    // a module language file exists for the language defined by the user, load it
    require_once(WB_PATH .'/modules/'.$module_directory.'/languages/'.LANGUAGE .'.php');
}

if (!class_exists('upload')) {
	include_once(WB_PATH .'/modules/'.$module_directory.'/inc/class.upload.php');
}
include_once(WB_PATH .'/modules/'.$module_directory.'/inc/default_values.php');
require_once(WB_PATH .'/modules/'.$module_directory.'/inc/class.iiuploadIncControl.php');
include_once(WB_PATH .'/modules/'.$module_directory.'/inc/class.iiupload.php');

//restore values
if($mod_dir_org) $mod_dir = $mod_dir_org;
if($module_directory_org) $module_directory = $module_directory_org;
if($module_name_org) $module_name = $module_name_org;
if($module_function_org) $module_function = $module_function_org;
if($module_version_org) $module_version = $module_version_org;
if($module_platform_org) $module_platform = $module_platform_org;
if($module_author_org) $module_author = $module_author_org;
if($module_license_org) $module_license = $module_license_org;
if($module_description_org) $module_description = $module_description_org;