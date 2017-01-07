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

// ---------------- MODULE SETTINGS ----------------
$mod_iiupload_max_file_size      = 10240;	// in kb
$mod_iiupload_resize_max_edge_min = 100;	// minimum value for 'mod_iiupload_resize_max_edge'
$mod_iiupload_resize_max_edge_max = 2000;	// maximum value for 'mod_iiupload_resize_max_edge'
$mod_iiupload_thumb_max_edge_min = 100;		// minimum value for 'mod_iiupload_thumb_max_edge'
$mod_iiupload_thumb_max_edge_max = 2000;	// maximum value for 'mod_iiupload_thumb_max_edge'

// ---------------- CLASS DEFAULTS -----------------
/*	This defaults can only be disabled, if they are enabled.
 *	If they are disabled, users (module users) could not enable them.
 */

$mod_iiupload_allow_jpeg 			= 1;
$mod_iiupload_allow_png 			= 1;
$mod_iiupload_allow_gif 			= 1;
$mod_iiupload_allow_svg 			= 1;

$mod_iiupload_allow_base64 			= 0;
$mod_iiupload_allow_crop 			= 1;
$mod_iiupload_allow_override 		= 0;


// --------------- DATABASE DEFAULTS ---------------
$mod_iiupload_resize_images  = 1; 			// scale images after upload								// changeble in admin tools
$mod_iiupload_resize_max_edge = 1200; 		// max value for height and width (for scaled images)		// changeble in admin tools
$mod_iiupload_create_thumbs  = 1;			// create thumb images next to uploads						// changeble in admin tools
$mod_iiupload_thumb_max_edge = 500;			// max value for height and width (for thumbnails)			// changeble in admin tools
$mod_iiupload_thumb_folder   = 'thumbs'; 	// place thumbnails in extra folder ?						// changeble in admin tools
$mod_iiupload_thumb_prefix 	 = 'thumb_'; 	// thumbnail file prefix 									// changeble in admin tools
?>