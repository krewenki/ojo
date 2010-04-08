<?php



// CONFIG
define('FILE_NAME',array_pop(explode('/',$_SERVER['SCRIPT_NAME'])));
define('IMAGE_DIR',dirname(__FILE__).'/images/');
define('IMAGE_URL','http://'.$_SERVER['SERVER_NAME'].str_replace(FILE_NAME,null,$_SERVER['SCRIPT_NAME']).'images/');
define('THUMB_DIR',dirname(__FILE__).'/thumbs/');
define('THUMB_URL','http://'.$_SERVER['SERVER_NAME'].str_replace(FILE_NAME,null,$_SERVER['SCRIPT_NAME']).'thumbs/');
define('FILE_URL','http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
define('TITLE','Ojo Photo Gallery');

// END CONFIG

@mkdir(THUMB_DIR);

function getTitleFromFileName($filename){
	return ucwords(strtolower(str_replace('_',' ',array_shift(explode('.',$filename)))));
}

function thumbnail($inputFileName, $outputFileName, $maxSize = 100){
	$info = getimagesize($inputFileName);
	$type = isset($info['type']) ? $info['type'] : $info[2];
	if (!(imagetypes() & $type)){
		return false;
	}
	$width  = isset($info['width'])  ? $info['width']  : $info[0];
	$height = isset($info['height']) ? $info['height'] : $info[1];
	$wRatio = $maxSize / $width;
	$hRatio = $maxSize / $height;
	$sourceImage = imagecreatefromstring(file_get_contents($inputFileName));
	if (($width <= $maxSize) && ($height <= $maxSize)){
		return $sourceImage;
	} elseif (($wRatio * $height) < $maxSize){
		$tHeight = ceil($wRatio * $height);
		$tWidth  = $maxSize;
	} else {
		$tWidth  = ceil($hRatio * $width);
		$tHeight = $maxSize;
	}
	$thumb = imagecreatetruecolor($tWidth, $tHeight);
	if ($sourceImage === false){
		return false;
	}
	imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
	imagedestroy($sourceImage);
	$ext = strtolower(substr($outputFileName, strrpos($outputFileName, '.')));
	switch ($ext){
		case '.gif':
			imagegif($thumb, $outputFileName);
			break;
		case '.jpg':
		case '.jpeg':
			imagejpeg($thumb, $outputFileName, 80);
			break;
		case '.png':
			imagepng($thumb, $outputFileName);
			break;
		case '.bmp':
			imagewbmp($thumb, $outputFileName);
			break;
		default:
			return false;
		}
	return true;
}

$files = array();

$myDirectory = opendir(IMAGE_DIR);
while($entryName = readdir($myDirectory)) {
	if($entryName != '.' && $entryName != '..'){
		$files[filemtime(IMAGE_DIR.$entryName)] = $entryName;
		if(!is_file(THUMB_DIR.$entryName)){
			thumbnail(IMAGE_DIR.$entryName, THUMB_DIR.$entryName ,140);
		}
	}
}
closedir($myDirectory);

ksort($files);
$files = array_reverse($files,true);


if($_REQUEST['mode'] == 'rss'){
	header('Content-type: application/rss');
	echo "<?xml version=\"1.0\"?>\n";
?>
<rss version="2.0">
  <channel>
    <title><?php echo TITLE; ?></title>
    <link><?php echo FILE_URL; ?></link>
    <description>Photos</description>
<?php

foreach($files as $time=>$filename){
?>
	<item>
		<title><?php echo getTitleFromFileName($filename); ?></title>
		<link><?php echo FILE_URL; ?>?individual=true&amp;filename=<?php echo $filename; ?></link>
		<description><?php echo getTitleFromFileName($filename); ?><br /><img src="<?php echo IMAGE_URL.$filename; ?>" /></description>
		<pubDate><?php echo date("D, d M Y H:i:s T",$time); ?></pubDate>
	</item>
<?php
}
?>
  </channel>
</rss>
<?php
	exit();
}


?><!doctype html>
<html>
<head>
<title><?php echo TITLE; ?></title>
<link rel="alternate" type="application/rss+xml" title="RSS Feed for Ojo" 
  href="<?php echo FILE_URL; ?>?mode=rss" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://github.com/krewenki/jquery-lightbox/raw/master/jquery.lightbox.js"></script>
<link rel="stylesheet" type="text/css" href="http://github.com/krewenki/jquery-lightbox/raw/master/css/lightbox.css" />
<style>

	.container {
		width: 960px;
		margin: auto;
	}

.thumblink {
	float: left;
	width: 140px;
	margin: 10px;
}

.thumb {
	max-width: 140px;
	max-height: 140px;
}

</style>
<script>
$(document).ready(function(){
	$('.lightbox').lightbox({
		fileLoadingImage: 'http://github.com/krewenki/jquery-lightbox/raw/master/images/loading.gif',
		fileBottomNavCloseImage: 'http://github.com/krewenki/jquery-lightbox/raw/master/images/closelabel.gif'
});
});
</script>
</head>
<body>

<div class="container">

<?php


if($_REQUEST['individual'] == 'true' && isset($_REQUEST['filename'])){
?>
	<img src="images/<?php echo $_REQUEST['filename']; ?>" />
<?php
} else {
	foreach($files as $key=>$entryName){
?>
	<a href="images/<?php echo $entryName;?>" class="lightbox thumblink" title="<?php echo getTitleFromFileName($entryName); ?>"><img src="thumbs/<?php echo $entryName; ?>" class="thumb" title="<?php echo getTitleFromFileName($entryName); ?>" /></a>
<?php
	}
}
?>
</div>
</body>
</html>