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

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) die(header('Location: index.php'));  
/* -------------------------------------------------------- */

/**
 * Class iiUpload
 * Class provides an easy way for image uploading in WebsiteBaker/WBCE and other modules
 *
 * @author    Michael Gnehr <michael@gnehr.de>
 * @copyright Michael Gnehr
 */
class iiUpload { 
    
    /**
     * module name
     *
     * @access private
     * @var string
     */
    private $module_name;
    
    /**
     * path to module
     *
     * @access private
     * @var string
     */
    private $module_path;
    
    /**
     * web path to module directory
     *
     * @access private
     * @var string
     */
    private $module_webpath;
    
    /**
     * web url
     *
     * @access private
     * @var string
     */
    private $webpath;
    
    /**
     * path to media directory
     *
     * @access private
     * @var string
     */
    private $media_path;
    
    /**
     * path to media directory
     *
     * @access private
     * @var string
     */
    private $media_webpath;
    
    /**
     * contains class settings from file
     *
     * @access private
     * @var array
     */
    private $const_settings;
    
    /**
     * contains current class settings
     *
     * @access private
     * @var array
     */
    private $current_settings;

    /**
     * contains language file
     *
     * @access private
     * @var array
     */
    private $TEXTS;
    
    /**
     * default language
     *
     * @access private
     * @var string
     */
    private $defaultLanguage;
    
    /**
     * choosen language
     *
     * @access private
     * @var string
     */
    private $choosenLanguage;

    /**
     * choosen language file for js object
     *
     * @access private
     * @var string
     */
    private $jsLangfile;

    /**
     * callback function name for upload javascript
     * functionname may contains: A-Za-z0-9_
     * 
     * get json result object. (calleronj, obj)
     * obj ==> Object properties: (OK): success, imgpath, thumbpath, msg (FAILED): success, msg
     * 
     * @access private
     * @var array
     */
    private $jsCallback;
    
    /**
     * path to uploadfolder - used to set path in js - upload is allowed anywhere in /media, if the file does not already exist
     * max length: 127
     * 
     * @access private
     * @var string
     */
    private $uploadFolder;
    
    /**
     * hide upload folder from frontend
     *
     * @access private
     * @var bool
     */
    private $hideUploadFolder;
    
    /**
     * Upload button text - this parameter is optional, could also be changed in Language file
     *
     * @access private
     * @var array
     */
    private $optionalButtonText;
    
    /**
     * set own Button HTML - note: make shure you add '[ID]' as placeholder for classname and [BUTTONLABEL] for button text. [CLASS] is optional
     * e.g.: '<div class="myVeryOwnButton [CLASS]" id="[ID]">[BUTTONLABEL]</div>'
     *
     * @access private
     * @var array
     */
    private $optionalButtonHtml;
    
    /**
     * optional css file
     *
     * @access private
     * @var array
     */
    private $optionalCssFile;
    
    /**
     * includeCheck
     * contains an instance of iiUploadIncControl class
     *
     * @access private
     * @var class iiUploadIncControl
     */
    private $includeCheck;
    
    /**
     * WB db class
     *
     * @access private
     * @var class (WB FRAMEWORK CLASS)
     */
    private $db;

    /**
     * WB admin class - need to generate access tans
     *
     * @access private
     * @var class (WB FRAMEWORK CLASS)
     */
    private $admin;
    
    /**
     * random_button_id
     * random html id for html button -> multiple mudules can include uploadbutton on same page
     *
     * @access private
     * @var string
     */
    private $random_button_id;
    
    
    /**
     * initialize Membervars
     * 
     * @access private
     */
    private function initVars($adm = null, $db = null){
        if(!defined('WB_PATH')||!defined('WB_URL')) throw new Exception('Something whent wrong. Could not finish constructing');
        $this->module_name = "ii_upload";
        $this->module_path = WB_PATH .'/modules/'.$this->module_name;
        $this->module_webpath = WB_URL .'/modules/'.$this->module_name;
        $this->media_path = WB_PATH . MEDIA_DIRECTORY . '/';
        $this->media_webpath = WB_URL . MEDIA_DIRECTORY . '/';
        $this->webpath = WB_URL;
        $this->defaultLanguage = 'DE';
        $this->choosenLanguage = (defined('WB_URL'))? LANGUAGE : $this->defaultLanguage;
        $this->jsCallback = null;
        $this->optionalButtonText = '';
        $this->uploadFolder = '';
        $this->optionalCssFile = null;
        $this->optionalButtonHtml = null;
        $this->jsLangfile='';
        if ($db===null){
            global $database;
            $this->db = $database;
        } else {
            $this->db = $db;
        }
        $this->admin = array();
        if ($adm===null){
            global $admin;
            $this->admin['admin'] = $admin;
        } else {
            $this->admin['admin'] = $adm;
        }
        $this->generateFTAN();
        $this->hideUploadFolder=0;
        $this->includeCheck = iiUploadIncControl::getInstance();
        include($this->module_path.'/inc/default_values.php');
        $this->const_settings=array ('max_file_size' => $mod_iiupload_max_file_size,
                                    'allow_png' => $mod_iiupload_allow_png,
                                    'allow_jpeg' => $mod_iiupload_allow_jpeg,
                                    'allow_gif' => $mod_iiupload_allow_gif,
                                    'allow_svg' => $mod_iiupload_allow_svg,
                                    'allow_base64' => $mod_iiupload_allow_base64,
                                    'allow_crop' => $mod_iiupload_allow_crop,
                                    'allow_override' => $mod_iiupload_allow_override);
        $table = TABLE_PREFIX .$this->module_name.'_settings';
        $sql_result = $this->db->query("SELECT * FROM $table WHERE id = '1'");
        if ($this->db->is_error()){
            throw new Exception('DB ERROR.');
        } else {
            $sql_row = $sql_result->fetchRow();
            $this->const_settings['resize_images'] = $sql_row['resize_images'];
            $this->const_settings['resize_max_edge'] = $sql_row['resize_max_edge'];
            $this->const_settings['create_thumbs'] = $sql_row['create_thumbs'];
            $this->const_settings['thumbs_max_size'] = $sql_row['thumbs_max_size'];
            $this->const_settings['thumb_folder'] = str_replace('..', '', $sql_row['thumb_folder']);
            $this->const_settings['thumb_prefix'] = str_replace('..', '', $sql_row['thumb_prefix']);
        }
        $this->current_settings = $this->const_settings;
        //random_id
        $this->random_button_id = $this->urandomString(8);
    }
    
    /**
     * generates WB Access Tans
     * 
     * @access private
     */
    private function generateFTAN(){
        if (is_array($this->admin)){
            $tan = $this->admin['admin']->getFTAN();
            $this->admin['key'] = substr($tan, strpos($tan, "value") + 7);
            $this->admin['key'] = substr($this->admin['key'], 0, strpos($this->admin['key'], '"'));
            $this->admin['name'] = substr($tan, strpos($tan, "name") + 6);
            $this->admin['name'] = substr($this->admin['name'], 0, strpos($this->admin['name'], '"'));
        }
    }
    
    /**
     * easy (but unsecure !) function to generate simple id string for JS to identify the button created with this class
     * do not copy this funtion somewhere else. For secure random generator look at:
     * http://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
     * 
     * @param integer $length
     */
    private function urandomString($length = 10, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $charLength = strlen($chars);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, $charLength - 1)];
        }
        return $result;
    }

    /**
     * class constructor
     * 
     * @access public
     */
    public function __construct($adm = null, $db=null ){
        $this->initVars($adm, $db);
    }

    /**
     * set Setting: 'create Thumbs'
     * 
     * @access public
     * @param bool $value
     */
    public function createThumbs($value){
        $this->current_settings['create_thumbs'] = ($value==true && $this->const_settings['create_thumbs'])? 1: 0;
    }
    
    /**
     * set Setting: 'base64, crop, svg, gif, jpeg, png, override'
     *
     * @access public
     * @param string $key base64|crop|svg|gif|jpeg|png|override
     * @param bool $value
     */
    public function allow($key, $value){
        $key = $key.'';
        $value = $value.'';
        switch ($key){
            case 'base64':
                $this->current_settings['allow_base64'] = ($value==true && $this->const_settings['allow_base64'])? 1: 0;
                break;
            case 'svg':
                $this->current_settings['allow_svg'] = ($value==true && $this->const_settings['allow_svg'])? 1: 0;
                break;
            case 'gif':
                $this->current_settings['allow_gif'] = ($value==true && $this->const_settings['allow_gif'])? 1: 0;
                break;
            case 'jpeg':
                $this->current_settings['allow_jpeg'] = ($value==true && $this->const_settings['allow_jpeg'])? 1: 0;
                break;
            case 'png':
                $this->current_settings['allow_png'] = ($value==true && $this->const_settings['allow_png'])? 1: 0;
                break;
            case 'crop':
                $this->current_settings['allow_crop'] = ($value==true && $this->const_settings['allow_crop'])? 1: 0;
                break;
            case 'override':
                $this->current_settings['allow_override'] = ($value==true && $this->const_settings['allow_override'])? 1: 0;
                break;
            default:
                break;
        }
    }
    
    /**
     * set Setting: 'language'
     *
     * @access public
     * @param string $value two letter country code (Uppercase)
     */
    public function setLanguage($value){
        $value = preg_replace("/[^A-Z]/", '', $value.'');
        if (strlen($value)===2){
            $this->choosenLanguage = $value;
        }
    }

    /**
     * set Setting: 'jsCallback'
     * get json result object. (callerobj, obj)
     * obj ==> Object properties: (OK): success, imgpath, thumbpath, msg (FAILED): success, msg
     *
     * @access public
     * @param string $value callback function name - allowed chars: 'A-Za-z0-9_'
     */
    public function setUploadJsCallback($value){
        $value = preg_replace("/[^A-Za-z0-9_]/", '', $value.'');
        $this->jsCallback  = $value;
    }
    
    /**
     * set Setting: 'uploadFolder'
     * path to uploadfolder - used to set path in js - upload is allowed anywhere in /media, if the file does not already exist
     * whitespaces are forbidden
     * max length: 127
     *
     * @access public
     * @param string $value upload folder path - allowed chars: '(?<!^)[^A-Za-z0-9\._\/-]|^[^A-Za-z0-9_\.-]|[^A-Za-z0-9_-]$|\.[^A-Za-z0-9_\/-]|\/[^A-Za-z0-9\._-]|\/\/|\/\.\.|\.\/|\.\.\/'
     */
    public function setUploadFolder($value){
        $value = preg_replace("/(?<!^)[^A-Za-z0-9\._\/-]|^[^A-Za-z0-9_\.-]|[^A-Za-z0-9_-]$|\.[^A-Za-z0-9_\/-]|\/[^A-Za-z0-9\._-]|\/\/|\/\.\.|\.\/|\.\.\//", '', $value.'');
        $value = preg_replace("/(?<!^)[^A-Za-z0-9\._\/-]|^[^A-Za-z0-9_\.-]|[^A-Za-z0-9_-]$|\.[^A-Za-z0-9_\/-]|\/[^A-Za-z0-9\._-]|\/\/|\/\.\.|\.\/|\.\.\//", '', $value.'');
        $value = substr( $value , 0, 127);
        $this->uploadFolder  = $value;
    }
    
    /**
     * set Setting: 'hideUploadFolder'
     * hide uploadfolder from frontend
     *
     * @access public
     * @param bool
     */
    public function hideUploadFolder($value){
        $this->hideUploadFolder  = ($value==true)? 1: 0;
    }
    
    /**
     * set Setting: 'optional Button Text'
     *
     * @access public
     * @param string $value upload button text - allowed chars: 'A-Za-z0-9_\-#+*~%&?(): '
     */
    public function setButtonText($value){
        $value = preg_replace("/[^A-Za-z0-9_\-#+*~%&?!():. ]/", '', $value.'');
        $this->optionalButtonText = $value;
    }
    
    /**
     * set Setting: 'optional Button HTML'
     * e.g.: '<div class="myVeryOwnButton [CLASS]" id="[ID]">[BUTTONLABEL]</div>'
     *
     * @access public
     * @param string $value upload button text - allowed chars: 'A-Za-z0-9_\-#+*~%&?(): '
     */
    public function setButtonHtml($value){
        $value = trim($value.'');
        $this->optionalButtonHtml = $value;
    }
    
    /**
     * set Setting: 'optional CSS file'
     *
     * @access public
     * @param string $value include additional CSS file - allowed chars: 'A-Za-z0-9_\-\/:.?&='
     */
    public function setCss($value){
        $value = preg_replace("/[^A-Za-z0-9_\-\/:.?&=]/", '', $value.'');
        $this->optionalCssFile = $value;
    }
    
    /**
     * echos script + stylesheet (per js to head) + html to include upload functionality
     *
     * @access private
     */
    private function loadLanguage(){
        // check if module language file exists for the language set by the user (e.g. DE, EN)
        if(!file_exists($this->module_path.'/languages/'. $this->choosenLanguage .'.php')) {
            // no module language file exists for the language set by the user, include default module language file DE.php
            require($this->module_path.'/languages/'. $this->defaultLanguage .'.php');
        } else {
            // a module language file exists for the language defined by the user, load it
            require($this->module_path.'/languages/'. $this->choosenLanguage .'.php');
        }
        if(!file_exists($this->module_path.'/js/lang/'.$this->module_name.'-'.$this->choosenLanguage .'.js')) {
            //no js lang file exist -> set default language
            $this->jsLangfile = $this->module_webpath.'/js/lang/'.$this->module_name.'-'.$this->defaultLanguage .'.js';
        } else {
            // a module language file exists for the language defined by the user, load it
            $this->jsLangfile = $this->module_webpath.'/js/lang/'.$this->module_name.'-'.$this->choosenLanguage .'.js';
        }
        $this->TEXTS = $IIUPLOAD_TEXTS;
    }
    
    /**
     * return js string with parameters for jquery_iiuploader
     * 
     * @access private
     * @return string js string with parameters for jquery_iiuploader
     */
    private function create_js_uploader(){
        $result = '$(document).ready(function(){';
        $result.= "$('#mod_iiupload_".$this->random_button_id."').iiUploader({".
            "url: \"$this->module_webpath/save.php\",".
            "max_file_size:".$this->current_settings['max_file_size'].",". 
            "allow_mask: [0,".  //dummy, jpeg, png, gif, svg, base64, crop
                                $this->current_settings['allow_jpeg'].",".
                                $this->current_settings['allow_png'].",".
                                $this->current_settings['allow_gif'].",".
                                $this->current_settings['allow_svg'].",".
                                $this->current_settings['allow_base64'].",".
                                $this->current_settings['allow_crop']."],".
            "resize_images:".(($this->current_settings['resize_images'])? $this->current_settings['resize_max_edge'] : '0').",".
            "create_thumbs:".(($this->current_settings['create_thumbs'])? '1': '0' ).",".
            "language: '".strtolower(substr($this->jsLangfile, -5, 2))."',".
            "tankey: '".$this->admin['key']."',".
            "tanname: '".$this->admin['name']."',".
            "uploadfolder: '".$this->uploadFolder."',".
            "hideFolder: '".$this->hideUploadFolder."'".
            (($this->jsCallback!=null)?", jsCallback:".$this->jsCallback :'').
                    "});});";
        return $result;
    }
    
    /**
     * echo script + stylesheet (per js to head) + html to include upload functionality
     *
     * @access public
     */
    public function showButton(){
        //check admin permission
        if (    (!isset($this->admin['admin']) || !is_a($this->admin['admin'], 'admin'))    ||  //admin not set
                (!$this->admin['admin']->is_authenticated()) || //not authenticated
                (!$this->admin['admin']->get_permission($this->module_name,'module' ) ) ) {         //has module permission
                return false;
        }
        $this->loadLanguage();
        //append html
        if ($this->optionalButtonHtml!=null){
            echo str_replace(array( '[ID]',
                                    '[CLASS]',
                                    '[BUTTONLABEL]'), 
                             array( 'mod_iiupload_'.$this->random_button_id,
                                    'mod_iiupload_create_uploader_btn',
                                    ($this->optionalButtonText!='')? $this->optionalButtonText : $this->TEXTS['CREATE_UPLOADER_BTN']),
                            $this->optionalButtonHtml);
        } else {
            echo '<div tabindex="0" class="noselect mod_iiupload_cubtn_style" id="mod_iiupload_'.$this->random_button_id.'">'.
                        (($this->optionalButtonText!='')?   $this->optionalButtonText : 
                                                            $this->TEXTS['CREATE_UPLOADER_BTN']).'</div>';
        }
        //add 'JS is needed' hint
        echo '<span class="mod_iiupload_nojs_hint" style="border:2px solid red; background-color: #fff; font-weight: bold; color: red; padding:5px 10px; margin-top: 5px; margin-bottom: 5px; display: block;">'.$this->TEXTS['JS_DISABLED'].'</span>';
        //include JS Language file
        if ($this->includeCheck->checkAndSet($this->jsLangfile)){
            echo '<script src="'.$this->jsLangfile.'" type="text/javascript"></script>'."\n";
        }
        //load jQuery if nessesary
        if ($this->includeCheck->checkAndSet("$this->webpath/include/jquery/jquery-min.js")){
            echo "<script>\n    window.jQuery || document.write('<script src=\"$this->webpath/include/jquery/jquery-min.js<\/script>');</script>";
        }
        // remove 'JS is needed' hint
        echo "<script>
                (function(){
                    var paras = document.getElementsByClassName('mod_iiupload_nojs_hint');
                    while(paras[0]) {
                        paras[0].parentNode.removeChild(paras[0]);
                    };\n
                    
                    var mod_iiupload_loadCss = function (file) {
                        var head = document.head; var nLink = document.createElement('link');
                        nLink.type = 'text/css'; nLink.href = file; nLink.rel = 'stylesheet';
                        head.appendChild(nLink);
                    };\n";
        //append css files to head
        if ($this->includeCheck->checkAndSet("$this->module_webpath/css/iiupload.css")){
            echo "\n        mod_iiupload_loadCss('$this->module_webpath/css/iiupload.css');";
        }
        if ($this->optionalCssFile!=null){ //user css
            if ($this->includeCheck->checkAndSet("$this->optionalCssFile")){
                echo "\n        mod_iiupload_loadCss('$this->optionalCssFile');";
            }
        }
        if ($this->includeCheck->checkAndSet("$this->module_webpath/css/cropper.css")){
            echo "\n        mod_iiupload_loadCss('$this->module_webpath/css/cropper.css');";
        }
        echo "})();\n</script>";
        
        //include js files
        if ($this->includeCheck->checkAndSet("$this->module_webpath/js/iiupload.js")){
            echo "\n        <script src=\"$this->module_webpath/js/iiupload.js\" type=\"text/javascript\"></script>";
        }
        //crop library: cropper.js (https://github.com/fengyuanchen/cropperjs)
        if (($this->current_settings['allow_crop'])){
            if ($this->includeCheck->checkAndSet("$this->module_webpath/js/cropper.js")){
                echo "\n        <script src=\"$this->module_webpath/js/cropper.js\" type=\"text/javascript\"></script>";
            }
        }
        // APPEND iiuploader to button
        echo "\n<script>\n".$this->create_js_uploader()."\n</script>\n";
        
        return true;
    }

    //handles file upload;
    public function handleUpload(){
        //check admin permission
        $this->loadLanguage();
        if (    (!isset($this->admin['admin']) || !is_a($this->admin['admin'], 'admin'))    ||  //admin not set
                (!$this->admin['admin']->is_authenticated())                                || //not authenticated
                (!$this->admin['admin']->get_permission($this->module_name,'module' ) )     ||  //has module permission
                (!$this->admin['admin']->checkFTAN())                                       ){  //check FTAN
            return array('success' => false, 'eMsg' => $this->TEXTS['ACCESS_DENIED']);
        }
        //ckeck permission before first image was send
        //so image data will not be send meaningless
        if (isset($_POST['mfunction'])&&$_POST['mfunction']==='dummyupload'){
            $this->generateFTAN();
            return array('success' => true, 'msg' => 'ok', 'new_name' => $this->admin['name'], 'new_key' => $this->admin['key']);
        }

        //check post arguments
        if (    (!(isset($_POST['mfunction'])&&$_POST['mfunction']==='upload')) ||  //wrong post arguments
                (!isset($_POST['ajax']) || $_POST['ajax']!='1')                                     ||
                (!isset($_POST['img_path']))                                    ||
                (!isset($_POST['crop']))                                        ||
                (!isset($_POST['rename']))                                      ||
                (!isset($_POST['create_thumbs']))                               ||
                (!isset($_FILES['file']) || !isset($_FILES['file']['name']))    ){
            return array('success' => false, 'eMsg' => $this->TEXTS['ACCESS_DENIED']);
        }
        
        //check variables - create thumbs
        $create_thumbs = $_POST['create_thumbs'].'';
        $this->createThumbs($create_thumbs);
        if ($create_thumbs!='1' && $create_thumbs !='0' ){
            return array('success' => false, 'eMsg' => $this->TEXTS['ACCESS_DENIED']);
        }
        //check file error code
        if ($_FILES['file']['error']!= UPLOAD_ERR_OK){
            $this->generateFTAN();
            return array(   'success' => false,
                'eMsg' => $this->TEXTS['ERROR_FILEUPLOAD'],
                'new_name' => $this->admin['name'],
                'new_key' => $this->admin['key']);
        }
        //check filesize
        if ($_FILES['file']['size']/1000 > $this->const_settings['max_file_size']){
            $this->generateFTAN();
            return array(   'success' => false,
                'eMsg' => $this->TEXTS['ERROR_FILE_SIZE'],
                'new_name' => $this->admin['name'],
                'new_key' => $this->admin['key']);
        }
        
        //check variables - filename
        $filename = '';
        if ($_POST['rename'] != 'false'){
            $rename = preg_replace("/[^A-Za-z0-9\._-]|[^A-Za-z0-9]$|\.[^A-Za-z0-9_-]/", '', $_POST['rename'].'');
            $filename = substr( $rename , 0, 127);
        } else {
            $name = preg_replace("/[^A-Za-z0-9\._-]|[^A-Za-z0-9]$|\.[^A-Za-z0-9_-]/", '', $_FILES['file']['name'].'');
            $filename = substr( $name , 0, 127);
        }
        if ($filename == ''){
            $this->generateFTAN();
            return array(   'success' => false,
                            'eMsg' => $this->TEXTS['ERROR_ILLEGAL_FILENAME'],
                            'new_name' => $this->admin['name'],
                            'new_key' => $this->admin['key']);
        }
        //check variables - fileextension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext_allowed = false;
        $allowed_mimes = array();
        switch($ext){
            case 'jpg':
            case 'jpeg':
            case 'JPG':
            case 'JPEG':
                if ($this->const_settings['allow_jpeg']) $ext_allowed = true;
                $allowed_mimes[]='image/jpg';
                $allowed_mimes[]='image/jpeg';
                break;
            case 'png':
            case 'PNG':
                if ($this->const_settings['allow_png']) $ext_allowed = true;
                $allowed_mimes[]='image/png';
                break;
            case 'gif':
            case 'GIF':
                if ($this->const_settings['allow_gif']) $ext_allowed = true;
                $allowed_mimes[]='image/gif';
                break;
            case 'svg':
            case 'SVG':
                if ($this->const_settings['allow_svg']) $ext_allowed = true;
                $allowed_mimes[]='image/svg';
                $allowed_mimes[]='image/svg+xml';
                break;
            default:
                break;
        }
        if (!$ext_allowed ){
            $this->generateFTAN();
            return array(   'success' => false,
                'eMsg' => $this->TEXTS['ERROR_ILLEGAL_FILEEXTENSION'],
                'new_name' => $this->admin['name'],
                'new_key' => $this->admin['key']);
        }
        
        //check variables - cropping and rotate
        $rotate = 0;
        $crop = false;
        if ($_POST['crop'] != 'false'){
            $croprotate = json_decode($_POST['crop'], true, 3);
            if( !isset($croprotate['t']) || !isset($croprotate['b']) || 
                !isset($croprotate['l']) || !isset($croprotate['r']) || 
                !isset($croprotate['rotate']) || count($croprotate) != 5){
                $this->generateFTAN();
                return array(   'success' => false,
                'eMsg' => $this->TEXTS['ERROR_ILLEGAL_CROPPARAMS'],
                    'new_name' => $this->admin['name'],
                    'new_key' => $this->admin['key']);
            } else {
                foreach ($croprotate as $key => $value){
                    $croprotate[$key] = filter_var( $croprotate[$key] , FILTER_VALIDATE_INT, array ('options' => array ('default' => -1, 'min_range' => 0, 'max_range' => 32000) ) );
                    if ($croprotate[$key] == -1 || ($key=='rotate' && !($croprotate[$key]==0 || $croprotate[$key]==90 || $croprotate[$key] == 180 || $croprotate[$key] == 270) ) ){
                        $this->generateFTAN();
                        return array(   'success' => false,
                            'eMsg' => $this->TEXTS['ERROR_ILLEGAL_CROPPARAMS'],
                            'new_name' => $this->admin['name'],
                            'new_key' => $this->admin['key']);
                    }
                }
                $rotate = $croprotate['rotate'];
                unset($croprotate['rotate']);
                $crop = array($croprotate['t'], $croprotate['r'], $croprotate['b'], $croprotate['l']);
            }
        }
        
        //check variables - folder name - could be emty so uploding will be directed to media folder
        $this->setUploadFolder($_POST['img_path']);
        if ($this->uploadFolder != '') $this->uploadFolder .= '/';
        
        //check and create upload folder
        $imagepath = $this->media_path . $this->uploadFolder;
        $imagewebfolder = $this->media_webpath . $this->uploadFolder;
        if (!is_dir($imagepath)) {
            if (!mkdir($imagepath, 0755, true)) {
                return array('success' => false, 'eMsg' => $this->TEXTS['ERROR_DIR_CREATE']);
            }
        }
        
        //check file already exists
        if ($this->current_settings['allow_override']==0){
            if(file_exists ($imagepath.$filename)){
                $this->generateFTAN();
                return array(   'success' => false,
                    'eMsg' => $this->TEXTS['ERROR_NO_OVERRIDE'],
                    'new_name' => $this->admin['name'],
                    'new_key' => $this->admin['key']);
            }
        }
        
        //thumb folder
        $thumbfolder = '';
        $thumbwebfolder = '';
        $thumb_filename = '';
        if ($this->current_settings['create_thumbs']==1){
            $thumbfolder = $imagepath.(($this->current_settings['thumb_folder']!='')?$this->current_settings['thumb_folder'].'/':'');
            $thumbwebfolder = $imagewebfolder.(($this->current_settings['thumb_folder']!='')?$this->current_settings['thumb_folder'].'/':'');
            $thumb_filename = $this->const_settings['thumb_prefix'].$filename;
            //disable thumbnail if thumbfolder and filename equals the uploaded image
            if ($thumbfolder == $imagepath && $thumb_filename == $filename){
                $this->current_settings['create_thumbs'] = 0;
            } else {
                //check file already exists 
                if ($this->current_settings['allow_override']==0){
                    if(file_exists ($thumbfolder.$thumb_filename)){
                        $this->generateFTAN();
                        return array(   'success' => false,
                            'eMsg' => $this->TEXTS['ERROR_NO_OVERRIDE'],
                            'new_name' => $this->admin['name'],
                            'new_key' => $this->admin['key']);
                    }
                }
                $thumb_filename = pathinfo($thumb_filename, PATHINFO_FILENAME);
            }
        }
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        
        $file_dest_name = ''; //temp filename for uploaded file
        //use upload class to handle the rest
        $upper = new upload($_FILES['file'], strtolower($this->choosenLanguage).'_'.$this->choosenLanguage);
        if ($upper->uploaded) {
            $upper->file_safe_name = true;
            $upper->file_force_extension = true;
            $upper->file_auto_rename = false;
            $upper->dir_auto_chmod = false;
            $upper->file_max_size = $this->const_settings['max_file_size'] * 1024;
            $upper->file_overwrite = ($this->current_settings['allow_override']==1);
            //filename
            $upper->file_new_name_body = $filename;
            //accepted files
            $upper->allowed=$allowed_mimes;
            $upper->mime_check = true;
            //is image file
            if (!$upper->file_is_image){
                $this->generateFTAN();
                return array(   'success' => false,
                    'eMsg' => $this->TEXTS['ERROR_NO_IMAGEFILE'],
                    'new_name' => $this->admin['name'],
                    'new_key' => $this->admin['key']);
            }
            //precrop 'T R B L'
            if ($crop != false){
                $upper->image_precrop = $crop;
            }
            //rotate
            $upper->image_auto_rotate = false;
            if($rotate != 0){
                $upper->image_rotate = $rotate;
            }
            //resize
            $upper->image_resize = true;
            $upper->image_x = $this->const_settings['resize_max_edge'];
            $upper->image_y = $this->const_settings['resize_max_edge'];
            $upper->image_ratio = true;
            $upper->image_no_enlarging = true;
            // run
            $upper->Process($imagepath);
            if ($upper->processed) {
                $file_dest_name = $upper->file_dst_name;
                if ($this->current_settings['create_thumbs']!=1){ //no thumbnails
                    $upper->Clean();
                    $this->generateFTAN();
                    return array('success' => true, 'msg' => 'ok', 
                                'imgpath' => $imagewebfolder.$upper->file_dst_name, 
                                'thumbpath' => $imagewebfolder.$upper->file_dst_name, 
                                'new_name' => $this->admin['name'], 
                                'new_key' => $this->admin['key']);
                }
            } else { //error while processing
                $this->generateFTAN();
                return array('success' => false, 'eMsg' => $upper->error, 'new_name' => $this->admin['name'], 'new_key' => $this->admin['key']);
            }
            //TODO svg isn'T supported by class.upload.php

            //create thumnails -------------------------------------------------
            if ($this->current_settings['create_thumbs']==1){
                $upper->file_safe_name = true;
                $upper->file_force_extension = true;
                $upper->file_auto_rename = false;
                $upper->dir_auto_chmod = false;
                $upper->file_max_size = $this->const_settings['max_file_size'] * 1024;
                $upper->file_overwrite = ($this->current_settings['allow_override']==1);
                //filename
                $upper->file_new_name_body = $thumb_filename;
                //accepted files
                $upper->allowed=$allowed_mimes;
                $upper->mime_check = true;
                //is image file
                if (!$upper->file_is_image){
                    $this->generateFTAN();
                    return array(   'success' => false,
                        'eMsg' => $this->TEXTS['ERROR_NO_IMAGEFILE'],
                        'new_name' => $this->admin['name'],
                        'new_key' => $this->admin['key']);
                }
                //precrop 'T R B L'
                if ($crop != false){
                    $upper->image_precrop = $crop;
                }
                //rotate
                $upper->image_auto_rotate = false;
                if($rotate != 0){
                    $upper->image_rotate = $rotate;
                }
                //resize
                $upper->image_resize = true;
                $upper->image_x = $this->current_settings['thumbs_max_size'];
                $upper->image_y = $this->current_settings['thumbs_max_size'];
                $upper->image_ratio = true;
                $upper->image_no_enlarging = true;
                // run
                $upper->Process($thumbfolder);
                if ($upper->processed) {
                    $upper->Clean(); //cleanup
                    $this->generateFTAN();
                    return array('success' => true, 'msg' => 'ok',
                        'imgpath' => $imagewebfolder.$file_dest_name,
                        'thumbpath' => $thumbwebfolder.$upper->file_dst_name,
                        'new_name' => $this->admin['name'],
                        'new_key' => $this->admin['key']);
                } else { //error while processing
                    $this->generateFTAN();
                    return array('success' => false, 'eMsg' => $upper->error, 'new_name' => $this->admin['name'], 'new_key' => $this->admin['key']);
                }
            }
        } 
        return array('success' => false, 'eMsg' => $this->TEXTS['ERROR_FILEUPLOAD']. ' ID 2');
    }
} 