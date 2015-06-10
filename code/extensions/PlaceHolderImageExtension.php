<?php

//	<img src="$PlaceHolderImage(300,200,cc0000,444).URL" />
class PlaceHolderImageExtension extends DataExtension {

	public static $folder = 'placeholder_images';

	public function PlaceHolderImage($width=100, $height=100, $bgColor='000', $fgColor='fff') {
		$text = $width.'x'.$height;
		$filename = 'placeholder-'.$text.'-'.$bgColor.'-'.$fgColor.'.jpg';

		$image = Image::get()->filter('Name',$filename)->first();
		if($image && $image->exists()) return $image;

		$foldername = trim(Config::inst()->get('PlaceHolderImageExtension', 'folder'),'/');		
		$folder = Folder::find_or_make($foldername);
		$filepath = $folder->Filename.$filename;

		$image = imagecreatetruecolor($width, $height);
		$bgColor = $this->hex2rgb($bgColor);
		$setBgColor = imagecolorallocate($image, $bgColor['r'], $bgColor['g'], $bgColor['b']);

		$fgColor = $this->hex2rgb($fgColor);
		$setFgColor = imagecolorallocate($image, $fgColor['r'], $fgColor['g'], $fgColor['b']);

		$fontSize = 4;
		$fontWidth = imagefontwidth($fontSize);
		$fontHeight = imagefontheight($fontSize);
		$textLength = strlen($text);
		$textWidth = $textLength * $fontWidth;
		$x = (imagesx($image) - $textWidth) / 2;
		$y = (imagesy($image) - $fontHeight) / 2;

		imagestring($image, $fontSize, $x, $y, $text, $setFgColor);
		imagejpeg($image, Director::baseFolder().'/'.$filepath);
		imagedestroy($image);

		$image = new Image();
		$image->ParentID = $folder->ID;
		$image->FileName = $filepath;
		$image->setName($filename);
		$image->write();

		return $image;	
	}

	public function hex2rgb($hex) {
		$hex = preg_replace("/[^abcdef0-9]/i", '', $hex);

		if(strlen($hex) == 6) {
			list($r, $g, $b) = str_split($hex, 2);
			return array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
		}
		else if(strlen($hex) == 3) {
			list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
			return array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
		} 
		user_error('Incorrect RGB value');
	}

}

