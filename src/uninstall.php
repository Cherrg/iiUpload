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

$module_directory = 'ii_upload';
require(WB_PATH.'/modules/'.$module_directory.'/inc/default_values.php');
$mod_inst_error = false;

$table = TABLE_PREFIX .$module_directory."_settings";
$database->query("DROP TABLE `$table`");

if($database->is_error()) {
	$admin->print_error($database->get_error(), 'javascript:history_back();');
	$mod_inst_error = true;
}

// ------------------------ UNINSTALL DUMMY MODULE ------------------------
// -------- admin can grant sepparate permissions for upload and settings --------
//(ii_upload_admin)
function uninst_ii_upload_delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		if (is_dir("$dir/$file")){
			uninst_ii_upload_delTree("$dir/$file");
		} else {
			$res = unlink("$dir/$file");
			if (!$res) echo '<strong>ERROR on unlinking file</strong>: ' . "$dir/$file<br>";
		}
	}
	$res = rmdir($dir);
	if (!$res) echo '<strong>ERROR on removing directory</strong>: ' . "$dir<br>";
	return $res;
}

$ad_mod_path = WB_PATH . '/modules/ii_upload_admin';
/* ----- remove directory ----- */
if (is_dir($ad_mod_path)) {
	uninst_ii_upload_delTree($ad_mod_path);
}
/* ----- remove database entry ----- */
if (!$mod_inst_error){
	$mod_sql  = "DELETE FROM `".TABLE_PREFIX."addons` WHERE `type` = 'module' AND `directory` = 'ii_upload_admin'";
	$database->query($mod_sql);
	if($database->is_error()) {
		$admin->print_error($database->get_error(), 'javascript:history_back();');
		$mod_inst_error = true;
	}
}

?>