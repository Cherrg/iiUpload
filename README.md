#**iiUpload**
##Module for Websitebaker/WBCE
---

**Info:** 
```
Das Modul befindet sich in einem Alphastadium. Für eventuelle Schäden an laufenden Systemen wird keine Haftung übernommen, allerdings sind mir bis jetzt keine aufgefallen.
Testet es am besten auf einer Testinstallation.
Auch ist nicht garantiert, dass zwischen einzelnen Versionen ein Modulupdate funktioniert. Von daher sollte vorerst statt eines Updates eine Neuinstallation des Modules stattfinden.

Aktiviertes Javascript wird vorrausgesetzt.

Getestet in WB 2.8.3 SP7

Eine Beispiel befindet sich in den Admintools.

###Was geht schon?
* Bilder Upload in /media und Unterordnern davon
* Drag & Drop
* Bilder vor Upload Umbenennen, Rotieren, Schneiden
	* Einschließlich Indikatoren, welche Zeigen, dass ein Bild bearbeitet wurde, oder es damit Fehler gibt
* Thumbnails nach dem Upload erstellen
* Bilder nach dem Upload in der Größe reduzieren, falls nötig
* Nutzbar in anderen Modulen durch php-Klasse
* Erweiterte Optionen (Thumbnais aktivieren/deaktivieren, Sprache, Uploadpfad zeigen, erlaubte Bildtypen, ...) können je nach Wunsch gesetzt werden, wobei die Admineinstellungen/das .config-File immer das letzte Wort haben (opt out)
* Zugriffsschutz über Gruppenrechte
* getestet in aktuellem Internet Explorer / Chrome / Firefox
* JS-Callback für jedes Bild, so dass Beispielsweise ein CKEditor Plugin erstellt werden kann, welches Thumbnails anzeigt, und die Bilder verlinkt
* unterstützte Bildformate: JPEG, PNG, GIF, SVG

###Was geht (noch) nicht:
* ohne JavaScript läuft nix (Die Upload-UI ist als JQuery-Plugin implementiert)
	* --> Warnung wird angezeigt
* Support älterer Browser (IE...)
* Das Modul ist zwar darauf ausgelegt, einfach weitere Sprachen zu unterstützen (Sprachdateien für php und JS), allerdings ist momentan nur eine deutsches Version hinterlegt
alle anderen Bildformate


```
---
###Einbinden in andere WB Module im Backend

```php
if (class_exists('iiUpload')) {
	$ii = new iiUpload();
	$ii->showButton();
}
```
###Moduleinstellungen: /inc/default_values.php
In dieser Datei können die Systemeinstellungen gesetzt werden.
Optionen die hier deaktiviert werden, können von keinem Modulersteller umgangen werden.
D.h., der Modulentwickler kann Optionen deaktivieren, aber nicht aktivieren, falls diese global gesperrt worden.

#####**$mod_iiupload_max_file_size** (default: 10240)

> Maximale Dateigröße für hochgeladene Dateien in KB.

#####**$mod_iiupload_resize_max_edge_min** (default: 100)

> Der Modulentwickler kann bestimmen, welche Zielgröße (maximale *px* für Kanten) die Bilder haben sollen. 

> Dieser Wert legt das Minimum fest.

#####**$mod_iiupload_resize_max_edge_max** (default: 2000)

> Der Modulentwickler kann bestimmen, welche Zielgröße (maximale *px* für Kanten) die Bilder haben sollen. 

> Dieser Wert legt das Maximum fest.

#####**$mod_iiupload_thumb_max_edge_min** (default: 100)

> Der Modulentwickler kann bestimmen, welche Zielgröße (maximale *px* für Kanten) die Thumbnail-Bilder haben sollen. 

> Dieser Wert legt das Minimum fest.

#####**$mod_iiupload_thumb_max_edge_max** (default: 2000)

> Der Modulentwickler kann bestimmen, welche Zielgröße (maximale *px* für Kanten) die Thumbnail-Bilder haben sollen. 

> Dieser Wert legt das Maximum fest.

#####**$mod_iiupload_allow_jpeg** (default: 1)

> Erlaubt den Upload von JPG/JPEG Dateien.

#####**$mod_iiupload_allow_png** (default: 1)

> Erlaubt den Upload von PNG Dateien.

#####**$mod_iiupload_allow_gif** (default: 1)

> Erlaubt den Upload von GIF Dateien.

#####**$mod_iiupload_allow_svg** (default: 1)

> Erlaubt den Upload von SVG Dateien.

#####**$mod_iiupload_allow_base64** (default: 0)

> Erlaubt den Upload von base64 kodierten Dateien als Text.

#####**$mod_iiupload_allow_crop** (default: 1)

> Erlaubt das Zuschneiden und Rotieren von Uploads.

#####**$mod_iiupload_allow_override** (default: 0)

> Erlaubt Überschreiben bereits vorhandener Dateien.

###folgende Werte können im Backend/in den Adminoptionen  verändert werden

* Bildgröße nach dem Upload reduzieren
* Zielgröße (Maximale Kantenlänge) für Bilder
* Thumbnails erzeugen
* Zielgröße (Maximale Kantenlänge) für Thumbnails
* Thumbnailpfad - Relativ zum gewählten Verzeichnis
* Thumbnailpräfix

###Klassenoperationen

Die Operationen müssen vor dem Funktionsaufruf von '**showBotton**' erfolgen.

> Sollen nach dem Upload Thumbnails erzeugt werden? / Create Thumbnails?

> $value = *true*|*false*

```
	$ii->createThumbs(bool $value);
```
---
> Option setzen? / Set option.

> $value = *true*|*false*

> $key = '*base64*|*crop*|*svg*|*gif*|*jpeg*|*png*|*override*'

```
	$ii->allow(string $key, bool $value);
```

---
> Sprache ändern? / Change Language

> $value = Ländercode (2 Großbuchstaben) / two letter country code (Uppercase)

```
	$ii->setLanguage(string $value);
```

---
> JSCallbackFunktion setzen? / Set JS callback function

> $value =  allowed chars: 'A-Za-z0-9_'

```
	$ii->setUploadJsCallback(string $value);
```

Die Funktion wird mit Abschluss eines jeden Uploads aufgerufen.
> return value: (callerobj, obj)

> obj - SUCCESS properties:
	>>success, imgpath, thumbpath, msg
	
> obj - FAILED properties:
	>> success, msg

---
> Upload Zielverzeichnis in media? / Upload directory path inside /media

> $value = string

```
	$ii->setUploadFolder(string $value);
```

---
> Upload Zielverzeichnis durch Nutzer änderbar? / Upload directory path changable /media

> $value = *true*|*false*

```
	$ii->setUploadFolder(bool $value);
```
---
> Upload  Button Text

> $value string

```
	$ii->setButtonText(string $value);
```
---
> Button HTML Code ändern? / Change button HTML.

> $value string

```HTML
<div class="myVeryOwnButton [CLASS]" id="[ID]">[BUTTONLABEL]</div>
```

```
	$ii->setButtonText(bool $value);
```
Das Element muss mindestens [ID] mit beinhalten.

---
> Zusätzliche CSS Datei für Button und Overlay / Add additional CSS File for Button and Overlay

> $path_to_file string

```
	$ii->setCss(string $path_to_file);
```

---

---

Kindly regards to https://css-tricks.com/drag-and-drop-file-uploading/ -> used this tutorial for Drag&Drop detection
