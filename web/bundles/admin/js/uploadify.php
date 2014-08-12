<?php
#not sure where the file will be published
$dir = __DIR__;
while(!file_exists($dir.'/autoload.php'))
	$dir = dirname($dir);
require_once $dir.'/autoload.php';

$kernel = new Kernel();
$kernel->load();
$request = \Asgard\Http\Request::createFromGlobals();
header('Content-Type: application/javascript');
?>

function multiple_upload(el, url) {
	var elID = '#'+el;
  $('#'+el+'-filesupload').uploadify({
	'swf'  : '../bundles/admin/uploadify/uploadify.swf',
	'uploader'    : url,
	'auto'      : true,
	'multi'           : true,
	'formData'  : {'PHPSESSID':'<?=session_id()?>'},
	'queueID'        : el+'-custom-queue',
	'uploadLimit' : 3,
	'onSelect'   : function(file) {
		$(elID).find('.uploadmsg').text(file.name + ' <?=__(' was added to the queue.')?>');
	},
	'onUploadSuccess' : function(file, data, response) {
		if(!response)
			return;
		$(elID).parent().find('.list').append(data);
	},
	'onQueueComplete'  : function(data) {
		$(elID).find('.uploadmsg').text(data.uploadsSuccessful + ' <?=__('files uploaded')?>, ' + data.uploadsErrored + ' <?=__('errors')?>.');
	}
  });
};