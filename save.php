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
 * @license         GNU GPLv3 or any later
 *
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
require_once(WB_PATH.'/framework/functions.php');

/* ------------- VARIABLES ------------- */
$admin_header = false;
$mod_dir = basename(__DIR__);           // module name
$json_result = array();
$admin = new admin('admintools', 'admintools', $admin_header ); // right admin created?

$js_back = ADMIN_URL.'/admintools/tool.php';
$toolUrl = ADMIN_URL.'/admintools/tool.php?tool=ii_upload';

if( !$admin->get_permission($mod_dir,'module' ) ) {
    if(!$admin_header) { $admin->print_header(); $printed_admin_header = true;}
    $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $toolUrl);
    exit(0);
}

//include module files
include(WB_PATH .'/modules/'.$mod_dir.'/init.php');

// ------------- answer request or show settings -------------
if(isset($_POST['mfunction'])&&($_POST['mfunction']==='upload'||$_POST['mfunction']==='dummyupload'))  {
// ------ upload_file ------
    if( $admin->get_permission('ii_upload','module' ) ) {
        if (class_exists('iiUpload')) {
            $i3 = new iiUpload();
            $json_result = $i3->handleUpload();
        }
    }
    echo json_encode($json_result, JSON_HEX_QUOT | JSON_HEX_TAG);
} else {
    // no access anyway
    header('location: '.WB_URL.'/modules/'.$mod_dir.'/index.php');
    exit(0);
}