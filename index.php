<?php

function getTitleFromFileName($filename){
	return ucwords(strtolower(str_replace('_',' ',array_shift(explode('.',$filename)))));
}

function thumbnail($inputFileName, $outputFileName, $maxSize = 100){
        $info = getimagesize($inputFileName);

        $type = isset($info['type']) ? $info['type'] : $info[2];

        // Check support of file type
        if ( !(imagetypes() & $type) )
        {
            // Server does not support file type
            return false;
        }

        $width  = isset($info['width'])  ? $info['width']  : $info[0];
        $height = isset($info['height']) ? $info['height'] : $info[1];

        // Calculate aspect ratio
        $wRatio = $maxSize / $width;
        $hRatio = $maxSize / $height;

        // Using imagecreatefromstring will automatically detect the file type
        $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));

        // Calculate a proportional width and height no larger than the max size.
        if ( ($width <= $maxSize) && ($height <= $maxSize) )
        {
            // Input is smaller than thumbnail, do nothing
            return $sourceImage;
        }
        elseif ( ($wRatio * $height) < $maxSize )
        {
            // Image is horizontal
            $tHeight = ceil($wRatio * $height);
            $tWidth  = $maxSize;
        }
        else
        {
            // Image is vertical
            $tWidth  = ceil($hRatio * $width);
            $tHeight = $maxSize;
        }

        $thumb = imagecreatetruecolor($tWidth, $tHeight);

        if ( $sourceImage === false )
        {
            // Could not load image
            return false;
        }

        // Copy resampled makes a smooth thumbnail
        imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
        imagedestroy($sourceImage);

        $ext = strtolower(substr($outputFileName, strrpos($outputFileName, '.')));

        switch ( $ext )
        {
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


?><!doctype html>
<html>
<head>
<title>Ojo</title>
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

$myDirectory = opendir("images");
while($entryName = readdir($myDirectory)) {
	if($entryName != '.' && $entryName != '..'){
	if(!is_file('thumbs/'.$entryName)){
		thumbnail('images/'.$entryName, 'thumbs/'.$entryName ,140);
	}
?>
	<a href="images/<?php echo $entryName;?>" class="lightbox thumblink" title="<?php echo getTitleFromFileName($entryName); ?>"><img src="thumbs/<?php echo $entryName; ?>" class="thumb" title="<?php echo getTitleFromFileName($entryName); ?>" /></a>
<?php
	}
}
closedir($myDirectory);
?>
</div>
</body>
</html>
