<?php

function getTitleFromFileName($filename){
	return ucwords(strtolower(str_replace('_',' ',array_shift(explode('.',$filename)))));
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
?>
	<a href="images/<?php echo $entryName;?>" class="lightbox thumblink" title="<?php echo getTitleFromFileName($entryName); ?>"><img src="images/<?php echo $entryName; ?>" class="thumb" title="<?php echo getTitleFromFileName($entryName); ?>" /></a>
<?php
	}
}
closedir($myDirectory);
?>
</div>
</body>
</html>
