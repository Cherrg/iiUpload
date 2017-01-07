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

// Deutsche Modulbeschreibung
$module_description = 'Einfach zu verwendender Bild-Upload.';

// Ueberschriften und Textausgaben
$IIUPLOAD_TEXTS['HEADLINE'] = 'IImage Upload';
$IIUPLOAD_TEXTS['CSS_BTN_TITLE'] = 'CSS bearbeiten';

$IIUPLOAD_TEXTS['RESIZE_IMAGES'] = 'Bildgröße nach upload anpassen';
$IIUPLOAD_TEXTS['RESIZE_MAX_SIZE'] = 'Maximale Kantengröße (px)';
$IIUPLOAD_TEXTS['CREATE_THUMBS'] = 'Thumbnails für Bilder erstellen';
$IIUPLOAD_TEXTS['THUMBS_MAX_SIZE'] = 'Maximale Thumbnailgröße (px)';
$IIUPLOAD_TEXTS['THUMB_FOLDER'] = 'Thumbnail Ordner (frei lassen -> kein extra Ordner)';
$IIUPLOAD_TEXTS['THUMB_PREFIX'] = 'Thumbnail Prefix';
$IIUPLOAD_TEXTS['TEST_BTN_HEADLINE'] = 'Upload jetzt testen';

$IIUPLOAD_TEXTS['CREATE_UPLOADER_BTN'] = 'Dateien hochladen...';
$IIUPLOAD_TEXTS['JS_DISABLED'] = 'Bitte aktiviere dein JavaScript, um die Uploadfunktion zu nutzen.';

$IIUPLOAD_TEXTS['ACCESS_DENIED'] = "Sicherheitsverletzung!! Zugriff wurde verweigert!";
$IIUPLOAD_TEXTS['ERROR_DIR_CREATE'] = "Zielordner konnte nicht erstellt werden";
$IIUPLOAD_TEXTS['ERROR_ILLEGAL_FILENAME'] = "Dateiname ungültig.";
$IIUPLOAD_TEXTS['ERROR_ILLEGAL_FILEEXTENSION'] = "Dateierweiterung ungültig.";
$IIUPLOAD_TEXTS['ERROR_ILLEGAL_CROPPARAMS'] = "Ungültige Schnittparameter.";
$IIUPLOAD_TEXTS['ERROR_FILEUPLOAD'] = "Unbekannter Uploadfehler.";
$IIUPLOAD_TEXTS['ERROR_FILE_SIZE'] = "Datei zu groß.";
$IIUPLOAD_TEXTS['ERROR_NO_OVERRIDE'] = "Die Datei existiert bereits und kann nicht überschrieben werden.";
$IIUPLOAD_TEXTS['ERROR_NO_IMAGEFILE'] = "Die übertragene Datei ist kein Bild.";
?>
