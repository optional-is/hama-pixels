<?php
/*
Brian Suda
brian@suda.co.uk

This will loop through the pixels within an image and try to move each color to the closest color from the provided list. In this case, it is RGB values for HAMA beads, but could be anything that fits the same format.

Usage:
php squares.php > output.svg

*/
header('Content-Type: image/svg+xml');

echo '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg width="100%" height="100%" version="1.1"
xmlns="http://www.w3.org/2000/svg">';


// Path to your JPG image file
$im = imagecreatefromjpeg("example.jpg");
$x = imagesx($im);
$y = imagesy($im);

$stepper = 5; // This is the size of the output squares
$color_counter = array();


$handle = fopen('hama.json', "rb");
$colors = fread($handle, filesize('hama.json'));
fclose($handle);
$colors = json_decode($colors,true);

// Loop through each pixel and make it an SVG square
for($i=0;$i<$x;$i++){
  for($j=0;$j<$y;$j++){
	$k = imagecolorat($im, $i, $j);
	$k = imagecolorsforindex($im, $k);
	$rad = $stepper;

	$k = closestColor($k,$colors);

	$color_counter[$k['name']]++;

	$hex = str_pad(dechex($k['red']),2,'0',STR_PAD_LEFT).str_pad(dechex($k['green']),2,'0',STR_PAD_LEFT).str_pad(dechex($k['blue']),2,'0',STR_PAD_LEFT);
	
	// output it backwards
	echo '<rect x="'.(($x*$stepper)-($i*$stepper)).'" y="'.($j*$stepper).'" width="'.$rad.'" height="'.$rad.'" fill="#'.$hex.'" />';
	echo "\n";		
	
    
  }
}
// Get ourselves a shopping list!
echo '<!-- ';
print_r($color_counter);

echo '-->';

echo '</svg>';

function closestColor($rgb,$colors){
	$r = $rgb['red'];
    $g = $rgb['green'];
    $b = $rgb['blue'];
    
	$smallest = 765;
	

	foreach($colors as $c){
		$tSmall = colorDistance($r,$g,$b,$c['red'],$c['green'],$c['blue']);

		if($smallest > $tSmall){
			$new_color['name']  = $c['name'];
			$new_color['red']   = $c['red'];
			$new_color['green'] = $c['green'];
			$new_color['blue']  = $c['blue'];

			$smallest = $tSmall;
		}
		
	}
	
	
	return $new_color;
}
function colorDistance($r,$g,$b,$target_r,$target_g,$target_b){
	$d = sqrt(
		pow($r-$target_r,2)+
		pow($g-$target_g,2)+
		pow($b-$target_b,2)
		);
	return $d;
}
?>
