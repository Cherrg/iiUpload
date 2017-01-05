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

/* ------------- VARIABLE ------------- */
$mod_dir = basename(__DIR__);			// module name
$showSettingsPermission = false;		// show settings on admin				// answer incoming Json Request
$json_result = array();
$skip = false;
$printed_admin_header = false;

$js_back = ADMIN_URL.'/admintools/tool.php';
$toolUrl = ADMIN_URL.'/admintools/tool.php?tool=ii_upload';
if( !$admin->get_permission($mod_dir,'module' ) ) {
    $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $toolUrl);
}
include(WB_PATH .'/modules/'.$mod_dir.'/init.php');

//show and accept admin settings?
$showSettingsPermission = (is_dir(WB_PATH.'/modules/ii_upload_admin') && $admin->get_permission('ii_upload_admin','module' ))? true : false;

// ------------- answer request or show settings -------------
if($showSettingsPermission && isset($_POST['action'])&&$_POST['action']==='save'&& isset($_POST['mfunction']) && $_POST['mfunction']==='save_settings')  {
// ------ save_settings ------
	if (!$admin->checkFTAN())
	{
		if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}
		$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$_SERVER['REQUEST_URI']);
		$skip = true;
	} else {
		$thumb_folder = substr(preg_replace("/[^A-Za-z0-9\-_]/", '', $_POST['thumb_folder']), 0, 63);
		$thumb_prefix = substr(preg_replace("/[^A-Za-z0-9\-_]/", '', $_POST['thumb_prefix']), 0, 63);
		if ($thumb_prefix == '') $thumb_prefix = $mod_iiupload_thumb_prefix;
		$resize_max_edge = filter_var( (($_POST['resize_size'])) , FILTER_VALIDATE_INT, array ('options' => array ('default' => $mod_iiupload_resize_max_edge, 'min_range' => $mod_iiupload_resize_max_edge_min, 'max_range' => $mod_iiupload_resize_max_edge_max) ) );
		$resize_images = filter_var( (($_POST['resize_images'])) , FILTER_VALIDATE_INT, array ('options' => array ('default' => 0, 'min_range' => 0, 'max_range' => 1) ) );
		$thumbs_max_size = filter_var( (($_POST['thumbs_size'])) , FILTER_VALIDATE_INT, array ('options' => array ('default' => $mod_iiupload_thumb_max_edge, 'min_range' => $mod_iiupload_thumb_max_edge_min, 'max_range' => $mod_iiupload_thumb_max_edge_max) ) );
		$create_thumbs = filter_var( (($_POST['create_thumbs'])) , FILTER_VALIDATE_INT, array ('options' => array ('default' => 0, 'min_range' => 0, 'max_range' => 1) ) );

		$table_mod = TABLE_PREFIX .$module_directory."_settings";
		$mod_update_sql = "UPDATE `$table_mod` SET `thumb_folder` = '$thumb_folder'".
							", `thumb_prefix` = '$thumb_prefix'".
							", `resize_max_edge` = '$resize_max_edge'".
							", `resize_images` = '$resize_images'".
							", `create_thumbs` = '$create_thumbs'".
							", `thumbs_max_size` = '$thumbs_max_size'".
						 " WHERE `id` = '1'";
		$database->query($mod_update_sql);
		if ($database->is_error()){
			if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}
			$admin->print_error($database->get_error(),$_SERVER['REQUEST_URI']);
		} else {
			if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}
			$admin->print_success($MESSAGE['PAGES']['SAVED'],$_SERVER['REQUEST_URI'] );
		}
    	$skip = true;
    }
} 

if($showSettingsPermission&&!$skip){
// ------ show settings ------
	$table = TABLE_PREFIX .$module_directory.'_settings';
	$sql_result = $database->query("SELECT * FROM $table WHERE id = '1'");
	if ($database->is_error()){
		if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}
		$admin->print_error($database->get_error(),$_SERVER['REQUEST_URI']);
		$skip = true;
	} else {
		$sql_row = $sql_result->fetchRow();
		$resize_images = ($sql_row['resize_images'] == 1);
		$resize_max_edge = $sql_row['resize_max_edge'];
		$create_thumbs = ($sql_row['create_thumbs'] == 1);
		$thumbs_max_size = $sql_row['thumbs_max_size'];
		$thumb_folder = str_replace('..', '', $sql_row['thumb_folder']);
		$thumb_prefix = str_replace('..', '', $sql_row['thumb_prefix']);

		if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}

		?>
	<?/* ----- EDIT CSS files ... 'edit_module_files' in /modules/ works, but redirects to other pageid but not back to tools
	<form name="mod_iiupload_css_edit" action="<?php echo WB_URL; ?>/modules/edit_module_files.php" method="post" style="margin: 0;">
		<div class="mod_iiupload_css_edit">
			<input type="hidden" name="page_id" value="1" />
			<input type="hidden" name="section_id" value="1" />
			<input type="hidden" name="mod_dir" value="<?php echo $module_directory ?>/css" />
			<input type="hidden" name="edit_file" value="frontend.css" />
			<input type="hidden" name="action" value="edit" />
			<?php echo $admin->getFTAN();?>
			<button class="noselect" type="submit" title="<?php echo $IIUPLOAD_TEXTS['CSS_BTN_TITLE']; ?>"><?php echo $IIUPLOAD_TEXTS['CSS_BTN_TITLE']; ?></button>
		</div>
	</form> */ ?>
	<form name="store_settings<?php echo $section_id; ?>" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" style="margin: 0;">
		<?php echo $admin->getFTAN(); ?>
		<input type="hidden" name="mfunction" value="save_settings">
		<input type="hidden" name="action" value="save">
		<div class="mod_iiupload_form_wrapper">
			<div class="mod_iiupload_hl"><?php echo $IIUPLOAD_TEXTS['HEADLINE'] ?></div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['RESIZE_IMAGES'].':' ?></div>
				<div>
					<input type="checkbox" name="resize_images" id="resize_images" value="1" <?php echo ($resize_images? 'checked="checked"' : ''); ?>>
					<label tabindex="0" for="resize_images"><div></div></label>
				</div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['RESIZE_MAX_SIZE'].':' ?></div>
				<div><input type="number" name="resize_size" min="<?php echo mod_iiupload_resize_max_edge_min;?>" max="<?php echo mod_iiupload_resize_max_edge_max;?>" id="resize_size" value="<?php echo $resize_max_edge; ?>"></div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['CREATE_THUMBS'].':' ?></div>
				<div>
					<input type="checkbox" name="create_thumbs" id="create_thumbs" value="1" <?php echo ($create_thumbs? 'checked="checked"' : ''); ?>>
					<label tabindex="0" for="create_thumbs"><div></div></label>
				</div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['THUMBS_MAX_SIZE'].':' ?></div>
				<div><input type="number" name="thumbs_size" min="<?php echo mod_iiupload_thumb_max_edge_min;?>" max="<?php echo mod_iiupload_thumb_max_edge_max;?>" id="thumbs_size" value="<?php echo $thumbs_max_size; ?>"></div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['THUMB_FOLDER'].':' ?></div>
				<div><input type="text" name="thumb_folder" maxlength="62" value="<?php echo $thumb_folder;?>"></div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div><?php echo $IIUPLOAD_TEXTS['THUMB_PREFIX'].':' ?></div>
				<div><input type="text" name="thumb_prefix" maxlength="62" value="<?php echo $thumb_prefix;?>"></div>
			</div>
			<div class="mod_iiupdate_setting_line">
				<div></div>
				<div></div>
			</div>
		</div>
		<div class="mod_iiupdate_submit">
			<button tabindex="0" type="button" onclick="javascript: window.location = '<?php echo $js_back;?>';"><?php echo $TEXT['CANCEL'] ?></button>
			<button tabindex="0" type="submit"><?php echo $TEXT['SAVE']; ?></button>
		</div>
	</form>

<?php } }

if(!$admin_header && !$printed_admin_header) { $admin->print_header(); $printed_admin_header = true;}
if(!$skip){
	//Module Usage
	echo '<h3>'.$IIUPLOAD_TEXTS['TEST_BTN_HEADLINE'].'</h3>';

    if (class_exists('iiUpload')) {
	    $i2 = new iiUpload();
		$i2->showButton();
	}
}

//	$admin->print_footer(); already done in /admin/admintools/tool.php

