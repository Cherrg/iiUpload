/* ---------- JQUERY IIUPLOAD OBJECT ----------- */
if(!jQuery().iiUploader) {
	"use strict";
	(function( $ ){
		/* -------------------------- MESSAGE SYSTEM -------------------------- */
		const MESSAGE_TYPE_NORMAL = 0; //blue
		const MESSAGE_TYPE_INFO = 1; //blue
		const MESSAGE_TYPE_WARNING = 2; //red
		const MESSAGE_TYPE_SUCCESS = 3; //green

		//hide parent object -> fade opacity

		var mod_iiupload_hide_remove_parent = function (object){
			$(object).parent().stop(true, false).animate({ height: 0, opacity: 0 }, 300,function(){ $(this).remove(); });
		}

		//show messages on screen
		var mod_iiupload_add_message = function (msg, type, hide_delay){
			var msg_div = document.createElement('div');
			switch(type){
				case MESSAGE_TYPE_INFO: 
					msg_div.className = "mod_iiupload_message_info";
				break; 
				case MESSAGE_TYPE_WARNING: 
					msg_div.className = "mod_iiupload_message_warning";
				break; 
				case MESSAGE_TYPE_SUCCESS: 
					msg_div.className = "mod_iiupload_message_success";
				break;
				case MESSAGE_TYPE_NORMAL:
				default:break;
			}
			msg_div.innerHTML = msg;
				var close_i = document.createElement('i');
				close_i.innerHTML = 'X';
				$(close_i).click(function(){
					mod_iiupload_hide_remove_parent(this);
				});
				msg_div.appendChild(close_i);
			msg_div.style.display = 'none';

			$(msg_div).appendTo('#mod_iiupload_message_container').slideDown( 300 );

			if (hide_delay === parseInt(hide_delay, 10)){
				if (hide_delay > 0){
					$(msg_div).delay(hide_delay).animate({ height: 0, opacity: 0 }, 300,function(){ $(this).remove(); });
				}
			}
		};

		//reload settings page
		var mod_iiupload_auto_page_reload = function (delay){
			setTimeout(function() { window.location.replace(window.location); }, delay);
		};

		/* ------------------------ Helper functions ------------------------ */

		//generate random id
		function randomString(length)
		{
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			for( var i=0; i < length; i++ )
				text += possible.charAt(Math.floor(Math.random() * possible.length));
			return text;
		}

		//drag$drop feature detection
		var isAdvancedUpload = function() {
			var div = document.createElement('div');
				return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
		}();

		/* ------------------------ Variables ------------------------*/

		var currentOpeningCaller;
		var $modal = null;
		var $preview = null;
		var $previewInsert = null;
		var $label = null;
		var $footer_uploading = null;
		var backgroundClose = function(){};
		var i18n = null;
		var droppedFiles = [];
		var fileList = {};
		var sendingList = [];
		var $currentXhr = null; //current Ajax request

		/* ------------------------ Plugin ------------------------ */
		$.fn.iiUploader = function(methodOrOptions) {
				if ( methods[methodOrOptions] ) {
					return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
				} else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
					// Default to "init"
					return methods.init.apply( this, arguments );
				} else {
					$.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.iiUploader' );
				} 
		};

		var updateGetKeys = function (id, settings){
			var $tan_input = $('#hidden_id_'+id);
			settings.tanname = $tan_input.attr('name');
			settings.tankey = $tan_input.attr('value');
			return settings;
		}

		//public available methods
		var methods = {
			init : function(options) {
				var settings = $.extend({}, $.fn.iiUploader.defaults, options );
				//add hidden inputfield
				var ownHiddenKey = $('<input/>', {'name': settings.tanname, 'value': settings.tankey, id: 'hidden_id_'+$(this).attr('id'), 'type': 'hidden'});
				ownHiddenKey.insertAfter(this);
				$(this).click(function(){


					currentOpeningCaller = $(this);
					//load language
					i18n = ii_upload_i18n[settings.language];
					//create new modal if needed
					if ($modal == null){
						backgroundClose=closeOverlay; //set close function
						settings = updateGetKeys($(this).attr('id'), settings);
						createModal(settings);
						setupForm(settings);
						$('body').append($modal);
						//listen to esc key to close window
						$(document).keyup(function(e) {
							if (e.keyCode === 27) backgroundClose();   // esc
						});
					}
				});
			},
			close : function(){
				backgroundClose();
			}
		};

		// --------- DEFAULT PARAMETER ---------
		$.fn.iiUploader.defaults = {
			url: '/admin/admintools/tool.php?tool=ii_upload',
			max_file_size: 10240, //empty, jpeg, png, gif, svg, base64, crop
			allow_mask: [0,1,1,1,1,1,1],
			resize_images: 1200,
			create_thumbs: 1,
			jsCallback: function (obj) {},
			language: 'de',
			tanname: '',
			tankey: '',
			uploadfolder: '',
			hideFolder: 0
		}

		/* ------------------------ member functions ------------------------ */
		//close crop view
		var closeCropper=function(){
			if ($modal != null){
				var $cropwrapper = $('.mod_iiupload_modal .mod_iiupload_crop_wrapper');
				var $img_crop_src = $cropwrapper.find('img#mod_iiupload_crop_src');
				if ($img_crop_src.attr('src') != ''){
					$img_crop_src.cropper('destroy');
					window.URL.revokeObjectURL($img_crop_src.attr('src')); // release memory for file
					$img_crop_src.attr('src', '');
				}
				if ($cropwrapper.hasClass('wrapper_open')) {
					$cropwrapper.removeClass('wrapper_open');
					$('.mod_iiupload_click_overlay').show();
				}

				$cropwrapper.find('div.rotate_ccw').unbind('click');
				$cropwrapper.find('div.rotate_cw').unbind('click');
				$cropwrapper.find('div.reset_crop').unbind('click');
				$cropwrapper.find('div.assign_crop').unbind('click');
				$cropwrapper.find('div.abort_crop').unbind('click');
			}
		}

		//close modal overlay
		var closeOverlay=function(){
			if ($modal != null){
				if ($currentXhr != null){
					$currentXhr.abort();
					$currentXhr = null;
				}
				closeCropper();
				$($modal).remove();
				$modal = null;
				$preview = null;
				$previewInsert = null;
				$label = null;
				$footer_uploading = null;
				backgroundClose=function(){};
				droppedFiles = [];
				fileList = {};
				sendingList = [];
				
			}
		}

		//remove preview image and element
		var removePreview = function (ev){
			$prev_elem = $(ev.currentTarget).closest('.preview_element');
			img_id = $prev_elem.attr('id');
			for (var property in fileList){
				if (fileList.hasOwnProperty(property)){
					if (fileList[property].id==img_id){
						//remove from UI
						$prev_elem.addClass('preview_element_remove').delay(300).queue(function() {
							$(this).remove();
						});
						//remove entry from list
						delete fileList[property];
						//if uploading in progress update sending list
						if(sendingList.length > 0){
							var inx = sendingList.indexOf(property);
							if (inx > -1){
								sendingList.splice(inx, 1);
							}
						}
						//update file counter
						showFileCounter(null);
						break;
					}
				}
			}
		}

		//check if file is valid(extension,type) and was not already added
		var checkFileBeforeAdd = function(file, settings) {
			//file/filename already on list?
			for (var property in fileList){
				if (fileList.hasOwnProperty(property)){
					if (property==file.name || fileList[property].rename==file.name){
						mod_iiupload_add_message(	i18n.file_alredy_added.replace('[FILENAME]', file.name), MESSAGE_TYPE_WARNING, 3000);
						//update file counter
						showFileCounter(null);
						return false;
					}
				}
			}

			//empty, jpeg, png, gif, svg, base64, crop
			isValid = true;
			var validFileExtensions = [];
			switch(file.type){
				case 'image/jpg':
				case 'image/jpeg':
					isValid = (settings.allow_mask[1] == 1);
					if (settings.allow_mask[1] == 1) { //jpeg
						validFileExtensions.push('.jpeg');
						validFileExtensions.push('.JPEG');
						validFileExtensions.push('.jpg');
						validFileExtensions.push('.JPG');
					}
					break;
				case 'image/png':
					isValid = (settings.allow_mask[2] == 1);
					if (settings.allow_mask[2] == 1) { //png
						validFileExtensions.push('.png');
						validFileExtensions.push('.PNG');
					}
					break;
				case 'image/gif':
					isValid = (settings.allow_mask[3] == 1);
					if (settings.allow_mask[3] == 1) { //gif
						validFileExtensions.push('.gif');
						validFileExtensions.push('.GIF');
					}
					break;
				case 'image/svg':
				case 'image/svg+xml':
					isValid = (settings.allow_mask[4] == 1);
					if (settings.allow_mask[4] == 1) { //svg
						validFileExtensions.push('.svg');
						validFileExtensions.push('.SVG');
					}
					break;
				default:
					isValid = false;
					break;
			}

			//wrong file format
			if (!isValid){
				console.log(file.type); //TODO remove
				mod_iiupload_add_message(	(file.type == '')? i18n.unknown_file_type : i18n.wrong_file_type,
											MESSAGE_TYPE_WARNING,
											3000);
				return false;
			}

			//check file extension is set and correct
			var currentFileExtension = file.name.substr(file.name.lastIndexOf('.'));
			isValid = false;
			for (var j = 0; j < validFileExtensions.length; j++) {
				if (currentFileExtension == validFileExtensions[j]) {
					isValid = true;
					break;
				}
			}
			if (!isValid){
				console.log(currentFileExtension); //TODO remove
				mod_iiupload_add_message(	i18n.wrong_file_extension,
											MESSAGE_TYPE_WARNING,
											3000);
				return false;
			}
			return true;
		}

		//add preview image and element
		var addImage = function(file, settings) {
			//check extension and type
			if (!checkFileBeforeAdd(file, settings)){ return false; }

			//check file size
			if(file.size/1000 > settings.max_file_size){ isValid=false; }

			fileList[file.name] = {	file 		: file,
									id 			: randomString(16), 
									crop 		: false, 
									crop_tblr	: false, 
									check 		: isValid,
									rename  	: false,
									processed 	: false
								};
			//show file counter
			showFileCounter(null);

			//container ------ create elements
			var $prev_elem = $('<div>', {class: 'preview_element' + ((!isValid)?' is_error':''), id: fileList[file.name].id});
				//image preview
				var img 		= document.createElement('img');
					img.onload 	= function () { 
						//featuredetection objectfit
						if ('objectFit' in document.documentElement.style === false){ 
							$prev_elem[0].style.backgroundImage = 'url(' + this.src + ')';
							this.style.display = 'none';
						}
						setTimeout(function(){ window.URL.revokeObjectURL(this.src); }, 3000);
					};
				var myURL = window.URL || window.webkitURL;
					img.src 	= myURL.createObjectURL(file);
					$prev_elem.append(img);

				//name and size
				var $s_name = $('<span>', {class: 'name'}).html(file.name);
					$prev_elem.append($s_name);

				var $s_size = $('<span>', {class: 'size'}).html((file.size/1000 <= 1000) ? (file.size/1000).toFixed(1) + ' KB' : (file.size/1000000).toFixed(1) + ' MB');
					$prev_elem.append($s_size);

				//buttons (modify - rename) ------------------------------------------------------------------
				var $btn_mod 			= $('<div>', {class: 'modify'});
					/* --------------- rename upload ---------------- */
					$btn_mod.click(function(){
						var currentFileExtension = file.name.substr(file.name.lastIndexOf('.'));
						var currentFilename = (fileList[file.name].rename==false)? file.name: fileList[file.name].rename;
						var newName = prompt(i18n.rename_file.replace( '[FILENAME]', currentFilename),
											currentFilename.substr(0, currentFilename.length - currentFileExtension.length)).trim();
						//pressed ok button
						if (newName != false && newName != null && newName != '' && (newName+currentFileExtension) != currentFilename) {
							newName += currentFileExtension;
							//check name not used elsewhere
							allowRename = true;
							for (var property in fileList){
								if (fileList.hasOwnProperty(property)){
									//other filename
									if (property==newName && fileList[file.name].id != fileList[property].id){
										allowRename = false;
										mod_iiupload_add_message( i18n.duplicate_rename, MESSAGE_TYPE_WARNING, 3000);
										break;
									}
									//other renamed file
									if (fileList[property].rename!=false && fileList[property].rename==newName){
										allowRename = false;
										mod_iiupload_add_message( i18n.duplicate_rename, MESSAGE_TYPE_WARNING, 3000);
										break;
									}
								}
							}
							if (allowRename){
								fileList[file.name].processed = false;
								if ($('#'+fileList[file.name].id).hasClass('is_error')) $('#'+fileList[file.name].id).removeClass('is_error');
								fileList[file.name].rename = newName;
								$('#'+fileList[file.name].id).find('.name').html(newName);
							}
						}
					});
					$prev_elem.append($btn_mod);
				if (settings.allow_mask[6] == 1){
					var $btn_crop = $('<div>', {class: 'crop_rotate'});
						$btn_crop.click(function (){
							//show overlay
							var $cropwrapper = $('.mod_iiupload_modal .mod_iiupload_crop_wrapper');
							$cropwrapper.addClass('wrapper_open');
							$('.mod_iiupload_click_overlay').hide();
							//target image
							var $img_crop_src = $('.mod_iiupload_modal img#mod_iiupload_crop_src');
							//get image data
							$img_crop_src.attr('src', window.URL.createObjectURL(file));
							//append cropper
							$img_crop_src.cropper({
								viewMode: 2,
								scalable: false,
								autoCrop: true,
								autoCropArea: 1,
								checkOrientation: true,
								built: function () {
									if (fileList[file.name].crop!=false) $img_crop_src.cropper('setData', fileList[file.name].crop);
								} });
							//link buttons to cropper
							$cropwrapper.find('div.rotate_ccw').click(function(){ $img_crop_src.cropper('rotate', -90); $img_crop_src.cropper('zoomTo', 0); $img_crop_src.cropper('moveTo', 0); });
							$cropwrapper.find('div.rotate_cw').click(function(){ $img_crop_src.cropper('rotate', 90); $img_crop_src.cropper('zoomTo', 0); $img_crop_src.cropper('moveTo', 0); });
							$cropwrapper.find('div.reset_crop').click(function(){ $img_crop_src.cropper('reset'); });
							$cropwrapper.find('div.abort_crop').click(function(){ closeCropper(); });
							$cropwrapper.find('div.assign_crop').click(function(){ 
								$img_crop_src.cropper('setData', $img_crop_src.cropper('getData', false));
								fileList[file.name].crop = $img_crop_src.cropper('getData', true);
								$('#'+fileList[file.name].id).find('.indicators .cropped').css( "display", "inline-block");
								//calculate tblrr (top bottom left right rotate)
								var tmp_image_data = $img_crop_src.cropper('getImageData');
								var tmp_tblrr = {};
								tmp_tblrr['rotate'] = fileList[file.name].crop.rotate;
								if (tmp_tblrr['rotate']<0) tmp_tblrr['rotate'] = tmp_tblrr['rotate'] + 360;
								if (tmp_tblrr.rotate == 0){
									tmp_tblrr['t'] = fileList[file.name].crop.y;
									tmp_tblrr['b'] = tmp_image_data.naturalHeight - (fileList[file.name].crop.y + fileList[file.name].crop.height);
									tmp_tblrr['l'] = fileList[file.name].crop.x;
									tmp_tblrr['r'] = tmp_image_data.naturalWidth - (fileList[file.name].crop.x + fileList[file.name].crop.width);
								} else if (tmp_tblrr.rotate == 90){
									tmp_tblrr['l'] = fileList[file.name].crop.y;
									tmp_tblrr['r'] = tmp_image_data.naturalWidth - (fileList[file.name].crop.y + fileList[file.name].crop.height);
									tmp_tblrr['b'] = fileList[file.name].crop.x;
									tmp_tblrr['t'] = tmp_image_data.naturalHeight - (fileList[file.name].crop.x + fileList[file.name].crop.width);
								} else if (tmp_tblrr.rotate == 180){
									tmp_tblrr['b'] = fileList[file.name].crop.y;
									tmp_tblrr['t'] = tmp_image_data.naturalHeight - (fileList[file.name].crop.y + fileList[file.name].crop.height);
									tmp_tblrr['r'] = fileList[file.name].crop.x;
									tmp_tblrr['l'] = tmp_image_data.naturalWidth - (fileList[file.name].crop.x + fileList[file.name].crop.width);
								} else if (tmp_tblrr.rotate == 270){
									tmp_tblrr['r'] = fileList[file.name].crop.y;
									tmp_tblrr['l'] = tmp_image_data.naturalWidth - (fileList[file.name].crop.y + fileList[file.name].crop.height);
									tmp_tblrr['t'] = fileList[file.name].crop.x;
									tmp_tblrr['b'] = tmp_image_data.naturalHeight - (fileList[file.name].crop.x + fileList[file.name].crop.width);
								}
								if (tmp_tblrr['t'] == 0 && tmp_tblrr['b'] == 0 && tmp_tblrr['l'] == 0 && tmp_tblrr['r'] == 0 && tmp_tblrr['rotate'] == 0){
									tmp_tblrr = false;
									fileList[file.name].crop = false;
									$('#'+fileList[file.name].id).find('.indicators .cropped').css( "display", "none");
								}
								fileList[file.name].crop_tblr = tmp_tblrr;

								closeCropper();
							});
						});
						$prev_elem.append($btn_crop);
				}
				var $btn_rem = $('<div>', {class: 'remove'}).html('&times;');
					$btn_rem.click(removePreview);
					$prev_elem.append($btn_rem);

				var $indicators = $('<footer>', {class: 'indicators'}).html('<div class="cropped" title="'+i18n.image_changed_crop+'"></div><div class="errors"></div>');
					$prev_elem.append($indicators);
					//add file size indicator text
					if (!isValid) $indicators.find('.errors').attr( "title", i18n.error_file_size );

			//append image container
			$previewInsert.before($prev_elem);
		};

		//create Overlay
		function createModal(settings){
			//extendions ------------
			aExt = [];
			if (settings.allow_mask[1] == 1) { //jpeg
				aExt.push('.jpeg');	aExt.push('.JPEG');
				aExt.push('.jpg');	aExt.push('.JPG');
			}
			if (settings.allow_mask[2] == 1) { //png
				aExt.push('.png');	aExt.push('.PNG');
			}
			if (settings.allow_mask[3] == 1) { //gif
				aExt.push('.gif');	aExt.push('.GIF');
			}
			if (settings.allow_mask[4] == 1) { //svg
				aExt.push('.svg');	aExt.push('.SVG');
			}

			$modal = $("<div></div>").attr('class','mod_iiupload_modal').html(
				'<div class="mod_iiupload_click_overlay"></div>'+
				'<form class="mod_iiupload_form" method="post" action="" enctype="multipart/form-data">'+
					'<div class="mod_iiupload_modal_content">'+
						'<div class="mod_iiupload_modal_header">'+
							'<span tabindex="0" class="mod_iiupload_modal_close noselect">&times;</span>'+
							'<div>'+i18n.modal_headline+'</div>'+
						'</div>'+
						((settings.hideFolder == 0)? '' +
						'<div class="mod_iiupload_pathline">'+
							'<div class="headline noselect">'+i18n.path+'</div>'+
							'<div class="inputline">'+
								'<span>'+i18n.default_path+'</span>'+
								'<input type="text" name="img_path" id="img_path" value="'+settings.uploadfolder+'">'+
							'</div>'+
						'</div>' : '' ) +
						'<div class="mod_iiupload_dragdropline">'+
							'<div class="dragdrop_wrapper">'+
								'<div class="dragdrop_area box__input">'+
									'<div class="symbol noselect">↶</div>'+
									'<input class="box__file" type="file" name="files[]" accept="'+aExt.join()+'" id="file" multiple />'+
									'<label for="file"><strong>'+i18n.select_file+'</strong></label>'+
									'<span class="drop_files_here noselect">'+i18n.drop_files_here+'</span>'+
								'</div>'+
								'<div class="preview_area">'+
									'<div class="clearall"></div>'+
								'</div>'+
							'</div>'+
							'<div class="button_wrapper">'+
								'<button tabindex="0" type="button" class="mod_iiupload_modal_close">'+i18n.btn_abort+'</button>'+
								'<button tabindex="0" type="submit" class="submit">'+i18n.upload_start+'</button>'+
							'</div>'+
						'</div>'+
						'<div class="mod_iiupload_modal_footer">'+
							'<div class="mod_iiupload_uploading"><img alt="Uploading..." src="/modules/ii_upload/images/transfer.gif"><div class="progress1"></div><div class="progress2"></div></div>'+
							'<div id="mod_iiupload_message_container"></div>'+
						'</div>'+
						((settings.allow_mask[6] == 1) ? ''+
							'<div class="mod_iiupload_crop_wrapper">'+
								'<div class="crop_box"><img id="mod_iiupload_crop_src" alt="" src=""></div>'+
								'<div class="crop_buttons noselect">'+
									'<div class="rotate_ccw title="'+i18n.btn_rotate_anticlochwise+'"></div>'+
									'<div class="rotate_cw" title="'+i18n.btn_rotate_clochwise+'"></div>'+
									'<div class="reset_crop">'+i18n.btn_reset+'</div>'+
									'<div class="abort_crop">'+i18n.btn_abort+'</div>'+
									'<div class="assign_crop">'+i18n.btn_assign+'</div>'+
								'</div>'+
							'</div>' : '' ) +
					'</div>'+
				'</form>');
			$modal.find('.mod_iiupload_modal_header .mod_iiupload_modal_close').click(backgroundClose);
			$modal.find('.mod_iiupload_dragdropline .mod_iiupload_modal_close').click(backgroundClose);
			$modal.children('.mod_iiupload_click_overlay').click(backgroundClose);
		}

		function setupForm(settings){
			$form = $modal.children('form');
			$input = $modal.find( 'input[type="file"]' );
			$label = $form.find('.dragdrop_area label');
			$preview = $modal.find('.preview_area');
			$previewInsert = $modal.find('.preview_area .clearall');
			$footer_uploading = $modal.find('.mod_iiupload_modal_footer .mod_iiupload_uploading');

			//show file counter
			showFileCounter = function(files) {
				//file counter
				file_count = Object.keys(fileList).length;
				if (file_count > 0){
					$label.text(file_count > 0 ? (i18n['data_multiple_caption'] || '').replace( '[COUNT]', file_count ) : '');
				} else if (files != null && files.length > 0) {
					$label.text(files.length > 1 ? (i18n['data_multiple_caption'] || '').replace( '[COUNT]', files.length ) : files[ 0 ].name);
				} else {
					$label.text(i18n.select_file);
				}
				if ($form.hasClass('is-uploading')){
					$footer_uploading.children('.progress2').html(i18n.progress_files_left.replace('[COUNT]', sendingList.length));
				}
			};

			// letting the server side to know we are going to make an Ajax request
			$form.append( '<input type="hidden" name="ajax" value="1" />' );
			
			$input.on( 'change', function( e )
			{
				showFileCounter( e.target.files );
				$.each(e.target.files, function (index, file) {7
					addImage(file, settings);
				});
				if (e.target.files.length>0 && isAdvancedUpload) {
					$input.val("");
				}
			});

			// drag&drop files if available
			if (isAdvancedUpload) {
				$form.addClass('has-advanced-upload');

				//events for feature detection
				$form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
					e.preventDefault();
					e.stopPropagation();
				})
				.on('dragover dragenter', function() {
					$form.addClass('is-dragover');
				})
				.on('dragleave dragend drop', function() {
					$form.removeClass('is-dragover');
				})
				.on('drop', function(e) {
					droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
					showFileCounter( droppedFiles );

					$.each(droppedFiles, function (index, file) {7
						addImage(file, settings);
					});
				});
			}

			//get upload event
			$form.on('submit', function(e) {
				if ($form.hasClass('is-uploading')) return false;

				$form.addClass('is-uploading').removeClass('is-error');

				if (isAdvancedUpload) {
					e.preventDefault();
					sendingList = [];
					for (var property in fileList){
						if (fileList.hasOwnProperty(property) && (!fileList[property].processed) && fileList[property].check){
							sendingList.unshift(property);							
						}
					}
					advancedSendTestAndSend(settings);
				} else { // ajax for legacy browsers (IE 8 + 9)
				// TODO

				}
			});

			// Firefox focus bug fix for file input
			$input
				.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
				.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });

		}

		var advancedSendTestAndSend = function (settings) {
			var ajaxData = new FormData();
			ajaxData.append( 'mfunction', 'dummyupload' );
			ajaxData.append( settings.tanname, settings.tankey );
			$.ajax({
				url: settings.url,
				type: "POST",
				data: ajaxData,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function(data) {
					if (data.success == true && typeof data.new_name!='undefined' && data.new_name!=false && data.new_name!=null){
						//update tans on page and in settings
						var $tan_input = $('input[name="'+settings.tanname+'"]'); 
						$tan_input.attr('name', data.new_name);
						$tan_input.attr('value', data.new_key);
						settings.tanname = data.new_name;
						settings.tankey = data.new_key;
						//call image upload
						advancedSend(settings);
					} else {
						if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
						sendingList=[];
						mod_iiupload_add_message( i18n.error_access, MESSAGE_TYPE_WARNING, 0);
						settings.jsCallback(currentOpeningCaller, {success: false, msg: i18n.error_access});
					}
				},
				error: function(e) {
					if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
					sendingList=[];
					mod_iiupload_add_message( i18n.error_unexpected_answer, MESSAGE_TYPE_WARNING, 0);
					settings.jsCallback(currentOpeningCaller, {success: false,  eMsg: i18n.error_access});
				}
			});

		}

		//hadle upload progress
		var progressbarHandler = function(e){
			if(e.lengthComputable){
				$footer_uploading.children('.progress1').html( parseInt(e.loaded / e.total * 100) + '%');
			}
		}

		var advancedSend = function(settings){
			if (sendingList.length==0) {
				if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
				return false;
			}

			//single file at once
			var cFileName = sendingList.pop(); //current file name
			$footer_uploading.children('.progress2').html(i18n.progress_files_left.replace('[COUNT]', sendingList.length));
			var $elem_prev = $('#'+fileList[cFileName].id);
			//hide delete btn
			$elem_prev.children('.remove, .modify, .crop_rotate').hide();
			//add file, filedata and settings
			var ajaxData = new FormData($form.get(0));

			ajaxData.delete('files[]');
			if(settings.hideFolder!=0){
				ajaxData.append( 'img_path', settings.uploadfolder );
			}
			console.log(ajaxData);
			ajaxData.append( 'mfunction', 'upload' );
			ajaxData.append( 'file', fileList[cFileName].file );
			ajaxData.append( 'crop', JSON.stringify(fileList[cFileName].crop_tblr) );
			ajaxData.append( 'rename', fileList[cFileName].rename );
			ajaxData.append( 'create_thumbs', settings.create_thumbs );
			ajaxData.append( settings.tanname, settings.tankey );

			if (!$elem_prev.hasClass('is_uploading')) $elem_prev.addClass('is_uploading');
			$currentXhr = $.ajax({
				url: settings.url,
				type: "POST",
				data: ajaxData,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {  
					var xmlHttpRequest = $.ajaxSettings.xhr(); // XMLhttpRequest
					if(xmlHttpRequest.upload){ // check if upload property exists
						xmlHttpRequest.upload.addEventListener('progress',progressbarHandler, false); // handle upload progress
					}
					return xmlHttpRequest;
				},
				complete: function() {
					if ($elem_prev.hasClass('is_uploading')) $elem_prev.removeClass('is_uploading');
				},
				success: function(data) {
					$elem_prev.children('.remove, .modify, .crop_rotate').show();
					adata = data;
					fileList[cFileName].processed=true;
					if (adata.success == true){
						if ($elem_prev.hasClass('is_error')) $elem_prev.removeClass('is_error');
						if (!$elem_prev.hasClass('is_ok')) $elem_prev.addClass('is_ok');
					} else {
						if (!$elem_prev.hasClass('is_error')) {
							$elem_prev.addClass('is_error');
							if (typeof $elem_prev.find('.errors').attr( "title")!='undefined') $elem_prev.find('.errors').removeAttr("title");
						}
						$elem_prev.find('.errors').attr( "title", ((typeof $elem_prev.find('.errors').attr( "title")!='undefined')?' - '+ $elem_prev.find('.errors').attr( "title"):'')+adata.eMsg );
					}
					if(typeof adata.new_name!='undefined' && adata.new_name!=false && adata.new_name!=null){
						//update tans on page and in settings
						var $tan_input = $('input[name="'+settings.tanname+'"]'); 
						$tan_input.attr('name', adata.new_name);
						$tan_input.attr('value', adata.new_key);
						settings.tanname = adata.new_name;
						settings.tankey = adata.new_key;

						if (sendingList.length>0){
							advancedSend(settings);
						} else {
							if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
						}	
						if (adata.success == true){ // (OK): success, imgpath, thumbpath, msg 
							settings.jsCallback(currentOpeningCaller, {success: true, msg: adata.msg, imgpath: adata.imgpath, thumbpath: adata.thumbpath});
						} else {
							settings.jsCallback(currentOpeningCaller, {success: false, msg: adata.eMsg }); //(FAILED): success, msg
						}
					} else {
						if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
						sendingList=[];
						if (!$elem_prev.hasClass('is_error')) {
							$elem_prev.addClass('is_error');
							if (typeof $elem_prev.find('.errors').attr( "title")!='undefined') $elem_prev.find('.errors').removeAttr("title");
						}
						if(typeof adata.eMsg!='undefined' && adata.eMsg!=false && adata.eMsg!=null){
							mod_iiupload_add_message( adata.eMsg, MESSAGE_TYPE_WARNING, 0);
						}
						mod_iiupload_add_message( i18n.error_unexpected_answer, MESSAGE_TYPE_WARNING, 0);
						settings.jsCallback(currentOpeningCaller, {success: false, msg: i18n.error_access});
					}
				},
				error: function(e) {
					$elem_prev.children('.remove, .modify, .crop_rotate').show();
					if ($form.hasClass('is-uploading')) $form.removeClass('is-uploading');
					sendingList=[];
					mod_iiupload_add_message( i18n.error_unexpected_answer, MESSAGE_TYPE_WARNING, 0);
					settings.jsCallback(currentOpeningCaller, {success: false,  eMsg: i18n.error_access});
				}
			});
		}
	})( jQuery );
}