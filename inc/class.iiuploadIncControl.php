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

/**
 * Class iiUploadIncControl
 * provides an singleton class to detect if files like js or css are included already
 * iiupload registers all js and css files this way.
 *
 * @author    Michael Gnehr <michael@gnehr.de>
 * @copyright Michael Gnehr
 */
class iiUploadIncControl { 
	
	/**
	 * Static reference to the single instance of this class we maintain.
	 */
	protected static $instance;
	
	/**
	 * Call this method to get singleton
	 *
	 * @return iiUploadIncControl
	 */
	public static function getInstance()
	{
		
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Private constructor so nobody else can instance it
	 */
	protected function __construct() {
		$includedFiles = array();
	}
	
	/**
	 * prevent cloning of an instance via the clone operator
	 */
	protected function __clone() {}
	
	/**
	 * prevent unserializing via the global function unserialize()
	 * 
	 * @throws Exception
	 */
	public function __wakeup()
	{
		throw new Exception("Cannot unserialize singleton");
	}
	
	/**
	 * normal instance properties: $includedFiles
	 * stores included files
	 * 
	 * @access private
	 * @var array
	 */
	private $includedFiles;
	
	/**
	 * check and set filename of included files
	 * returns false if file was included before
	 * returns true if file was set
	 * 
	 * @access public
	 * @param string $name 
	 * @return boolean
	 */
	public function checkAndSet($name){
		//check variable
		if(!is_string($name)){
			throw new Exception("filename must be string");
		}
		$name = trim($name);
		if ($name == ''){
			return false;
		} else if (array_key_exists($name, $this->includedFiles)){
			return false;
		} else {
			$this->includedFiles[$name] = true;
			return true;
		}
	}
}
