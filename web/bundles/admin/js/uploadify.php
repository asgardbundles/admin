<?php
#depending on wether the assets are published by copy or symlink, the file will be in a different subdirectory
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
	'formData'  : {'PHPSESSID':'<?php echo session_id() ?>'},
	'queueID'        : el+'-custom-queue',
	'uploadLimit' : 3,
	'onSelect'   : function(file) {
		$(elID).find('.uploadmsg').text(file.name + ' <?php echo __(' was added to the queue.') ?>');
	},
	'onUploadSuccess' : function(file, data, response) {
		if(!response)
			return;
		var result = JSON.parse(data);
		if(result.type == 'image') {
			$(elID).parent().find('.imglist').append('<li>\
							<img src="'+result.thumb_url+'" alt=""/>\
							<ul>\
								<li class="view"><a href="'+result.url+'" rel="facebox"><?php echo __('See') ?></a></li>\
								<li class="delete"><a href="'+result.deleteurl+'"><?php echo __('Del.') ?></a></li>\
							</ul>\
						</li>');
			$('a[rel*=facebox]').facebox()
		}
		else {
			$(elID).parent().find('.list').append('<li><a href="'+result.url+'"><?php echo __('Download') ?></a> | <a href="'+result.deleteurl+'"><?php echo __('Delete') ?></a>\
						</li>');
		}
	},
	'onQueueComplete'  : function(data) {
		$(elID).find('.uploadmsg').text(data.uploadsSuccessful + ' <?php echo __('files uploaded') ?>, ' + data.uploadsErrored + ' <?php echo __('errors') ?>.');
	}
  });
};