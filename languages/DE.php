<?php
/**
 * -------------------------------------------------------------------------------
 * Copyright (C) 2016, Michael Gnehr - All rights reserved
 * -------------------------------------------------------------------------------
 * Modul: Intertopia Image Upload
 * -------------------------------------------------------------------------------
 * TODO: CHANGE HEADER TO SOME PUBLIC LICENSE
 *
 * @category        module - tool
 * @package         ii_upload
 * @author          Michael Gnehr
 * @copyright       2016-TODAY, Michael Gnehr
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.6 and up
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) die(header('Location: index.php'));  
/* -------------------------------------------------------- */

// Deutsche Modulbeschreibung
$module_description = 'Einfach zu verwendender Bild-Upload.';

// Ueberschriften und Textausgaben
$IIUPLOAD_TEXTS['HEADLINE'] = 'Intertopia Image Upload';
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