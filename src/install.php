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
/* ------------------------------------------------------- */
// stop direct file access
if(!defined('WB_PATH')) die(header('Location: index.php'));
/* ------------------------------------------------------- */

$module_directory = 'ii_upload';
$mod_inst_error = false;
require(WB_PATH.'/modules/'.$module_directory.'/inc/default_values.php');

// ------------------ DATABASE inserts ------------------

$table = TABLE_PREFIX .$module_directory."_settings";
$database->query("DROP TABLE IF EXISTS `$table`");

$database->query("
	CREATE TABLE IF NOT EXISTS `$table` (
		`id` INT(11) NOT NULL DEFAULT '0',
		`resize_images` TINYINT(1) NOT NULL DEFAULT '$mod_iiupload_resize_images',
		`resize_max_edge` INT(11) NOT NULL DEFAULT '$mod_iiupload_resize_max_edge',
		`create_thumbs` TINYINT(1) NOT NULL DEFAULT '$mod_iiupload_create_thumbs',
		`thumbs_max_size` INT(11) NOT NULL DEFAULT '$mod_iiupload_thumb_max_edge',
		`thumb_folder` VARCHAR(64) NOT NULL DEFAULT '$mod_iiupload_thumb_folder',
		`thumb_prefix` VARCHAR(64) NOT NULL DEFAULT '$mod_iiupload_thumb_prefix',
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
");
if($database->is_error()) {
	  $admin->print_error($database->get_error(), 'javascript:history_back();');
	  $mod_inst_error = true;
} else {
	$database->query("INSERT INTO `$table` (`id`) VALUES ('1' )");
	if($database->is_error()) {
		$admin->print_error($database->get_error(), 'javascript:history_back();');
		$mod_inst_error = true;
	}
}

// ------------------------ INSTALL DUMMY MODULE ------------------------
// -------- admin can grant sepparate permissions for upload and settings --------
//(ii_upload_admin)
/* ----- create dummy module directory ------ */
$ad_mod_path = WB_PATH . '/modules/ii_upload_admin';
$copy_files = false;
if (!$mod_inst_error){
	if(!is_dir($ad_mod_path)) {
	   	if (!mkdir($ad_mod_path, 0755, true)) {
	   		 $admin->print_error('Erstellung des Verzeichnisses ("'.$ad_mod_path.'") schlug fehl...', 'javascript:history_back();');
		} else {
			$copy_files = true;
		}
	} else {
		$copy_files = true;
	}
}
/* ----- copy files ------ */
if ($copy_files){
	if(! copy ( WB_PATH.'/modules/'.$module_directory.'/install/index_php_inst' ,
				 $ad_mod_path.'/index.php' ) ) {
		$admin->print_error('Fehler beim Kopieren der Dateien', 'javascript:history_back();');
		$copy_files = false;
	}
}
if ($copy_files){
	if(! copy ( WB_PATH.'/modules/'.$module_directory.'/install/tool_php_admin' ,
				 $ad_mod_path.'/tool.php' ) ) {
		$admin->print_error('Fehler beim Kopieren der Dateien', 'javascript:history_back();');
		$copy_files = false;
	}
}
if ($copy_files){
	if(! copy ( WB_PATH.'/modules/'.$module_directory.'/install/info_php_admin' ,
				 $ad_mod_path.'/info.php' ) ) {
		$admin->print_error('Fehler beim Kopieren der Dateien', 'javascript:history_back();');
		$copy_files = false;
	}
}
/* ----- create db inserts ------ */
// does the module already exist?
$mod_where = "WHERE `type` = 'module' AND `directory` = 'ii_upload_admin'";
$mod_sql1  = "SELECT COUNT(*) FROM `".TABLE_PREFIX."addons` ".$mod_where;
if ( $database->get_one($mod_sql1) ) {
	$copy_files = false;
}

if ($copy_files){
    $mod_sql2  = 'INSERT INTO `'.TABLE_PREFIX.'addons` (`directory`,'.
    												' `name`,'.
    												' `description`,'.
    												' `type`,'.
    												' `function`,'.
    												' `version`,'.
    												' `platform`,'.
    												' `author`,'.
    												' `license`) ' .
    		" VALUES ('ii_upload_admin',".
    			" 'IIUpload Admin',".
    			" 'Dummy Module for IIUpload. Used to control user access to module settings.', ".
    			" 'module',".
    			" 'tool',".
    			" '0.0.1',".
    			" '2.8.x',".
    			" 'Michael Gnehr',".
    			" 'Copyright (C) 2016, Michael Gnehr - All rights reserved')";

	$database->query($mod_sql2);
	if($database->is_error()) {
		  $admin->print_error($database->get_error(), 'javascript:history_back();');
	}
}


?>