/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		'/',
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
	];

	config.removeButtons = 'Source,Save,Templates,NewPage,Preview,Print,PasteFromWord,PasteText,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,HiddenField,About,Flash';
    config.skin = 'office2013';
    config.language = 'vi';
	config.filebrowserBrowseUrl = 'http://localhost/ckfinder/ckfinder.html';
	//config.filebrowserUploadUrl= 'http://localhost/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	//config.filebrowserBrowseUrl = "{{asset('ckfinder/ckfinder.html')}}";
	config.filebrowserUploadUrl = "{{asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files')}}";
	config.filebrowserWindowWidth = '1000';
	config.filebrowserWindowHeight = '700';
};
