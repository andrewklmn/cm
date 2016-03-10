<?php

/*
 * 
 */

    if (!isset($c)) exit;

    include 'app/view/php-barcode-2.0.1.php';
    
    $text = (isset($_REQUEST['code'])) ? $_REQUEST['code'] : "0123456789";
    
    $im     = imagecreatetruecolor(800, 60);  
    $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
    $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
    imagefilledrectangle($im, 0, 0, 800, 60, $white);  
    $data = Barcode::gd($im, $black, 400, 20, 0, "code128", $text, 2, 40);
    $canvas = imagecreatetruecolor($data['width'], 60);
    imagefilledrectangle($canvas, 0, 0, $data['width'], 60, $white);  
    imagecopy($canvas, $im, 0, 0, $data['p1']['x'], $data['p1']['y'], 800, 60);
    imageString($canvas, 5, 0, 40, '* '.$text.' *', $black);
    
    Header('Content-type: image/png');
    imagePng($canvas);

?>
