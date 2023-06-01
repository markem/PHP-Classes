<?php
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		echo "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n";
		});

	date_default_timezone_set( "UTC" );
#
#	$lib is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );
	if( !file_exists($lib) ){ $lib = ".."; }

	if( file_exists("$lib/class_debug.php") ){
		include_once( "$lib/class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( __FILE__ . ": Can not load CLASS_DEBUG" );
			}

	include_once( "$lib/class_rgb.php" );
	include_once( "$lib/class_files.php" );
	include_once( "$lib/class_color.php" );
	include_once( "$lib/class_misc.php" );

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_gd();
#
#-Description:
#
#	Class to work with GD.
#
#-Inputs:
#
#	None.
#
#-Outputs:
#
#	None.
#
#-Revisions:
#
#	Name					Company					Date
#	---------------------------------------------------------------------------
#	Mark Manning			Simulacron I			Wed 08/10/2022 15:12:53.14
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 08/17/2021 14:56:52.53 
#	---------------------------------------------------------------------------
#		REMEMBER! We are now following the PHP code of NOT killing the program
#		but instead always setting a DEBUG MESSAGE and returning FALSE. So I'm
#		getting rid of all of the DIE() calls.
#
#	Mark Manning			Simulacron I			Sun 02/05/2023 22:25:38.92 
#	---------------------------------------------------------------------------
#		REMEMBER ALSO! These routines are made to use the internal $GD variable.
#		So you do NOT need to send a GD - IF you SET() it first. Also remember
#		that if you already have something in the internal GD you can also call
#		the GET() function to get the GD back and then set a new GD via the SET()
#		function.
#
#	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
#	---------------------------------------------------------------------------
#		This is now under the BSD Three Clauses Plus Patents License.
#		See the BSD-3-Patent.txt file.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows:
#
#		CLASS_GD.PHP. A class to handle working with GD.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_gd
{
	private $cr = null;
	private $cf = null;
	private $cc = null;
	private	$cm = null;
	public $gd = null;

	public $w = null;
	public $h = null;
	public $old_x = null;
	public $old_y = null;
	public $old_color = null;
	public $old_string = null;

	public $exts = null;
	public $exts_all = null;
	public $top = null;
	public $bot = null;
	public $left = null;
	public $right = null;

	public $trans = null;
	public $colors = null;
	public $black = null;
	public $white = null;

################################################################################
#	__construct(). Constructor.
#	NOTES	:	You can send over the WIDTH and HEIGHT of an image to create.
#				Default size is 100, 100.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['gd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['gd']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
#	NOTE : you can pass in a GD if need be.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	if( is_resource($args) && is_resource($this->gd) ){
		imagedestroy( $this->gd );
		$this->gd = $args;
		}

	$gd = $this->gd;
	if( is_resource($gd) ){
		$w = imagesx( $gd );
		$h = imagesy( $gd );
		}
		else {
			$w = 1000;
			$h = 1000;
			$gd = imagecreatetruecolor( $w, $h );
			imagealphablending( $gd, false );
			imagesavealpha( $gd, true );
			}

	$this->gd = $gd;

	$this->w = $w;
	$this->h = $h;
	$this->top = 0;
	$this->left = 0;
	$this->bot = $h;
	$this->right = $w;
#
#	Get the CLASS_RGB file and the CLASS_FILES file.
#
	$this->cr = $GLOBALS['classes']['rgb'];
	$this->cf = $GLOBALS['classes']['files'];
	$this->cc = $GLOBALS['classes']['color'];
	$this->cm = $GLOBALS['classes']['misc'];
#
#	We always make a TRUE color image. This means it will always
#	have the alpha channel (transparency). The weird thing about
#	transparency is that only the numbers between zero(0) and
#	127 affect the alpha/transparency set.
#
	$this->black = imagecolorallocate( $gd, 0, 0, 0 );
	$this->white = imagecolorallocate( $gd, 255, 255, 255 );

	imagefilledrectangle($gd, 0, 0, $w, $h, $this->black);
#
#	First, get all of the colors in this image
#
	$this->colors = $this->cr->get_colors( $gd );
#
#	Now find a transparent color in case we need it.
#
	$this->trans = $this->cf->unique_color( $gd );
	list( $a, $r, $g, $b ) = $this->cr->get_ARGB( $this->trans );
	$this->trans = $this->cr->put_ARGB( 127, $r, $g, $b );
#
#	Known graphic file extensions.
#
	$this->exts = array();		#	File Extension RegExps
	$this->exts['png'] = "png|pngp";
	$this->exts['bmp'] = "bmp";
	$this->exts['gif'] = "gif";
	$this->exts['tif'] = "tif|tiff";
	$this->exts['jpg'] = "exif|jfif|jfi|jpg|jpeg";
	$this->exts['webp'] = "web|webp";

	$this->exts_all = implode( "|", $this->exts );

	$this->debug->out();
}
################################################################################
#	set(). Set the GD for doing the rest of this stuff.
################################################################################
function set( $gd=null )
{
	if( is_null($gd) ){ $this->died( "*****ERROR : GD not given\n" ); }

	$this->gd = $gd;
	return true;
}
################################################################################
#	get(). Get the GD you were working with. This is NOT the magic __GET().
################################################################################
function get(){ return $this->gd; }
################################################################################
#	destroy(). Get rid of the GD
################################################################################
function destroy( $gd=null )
{
	if( !is_null($gd) ){ imagedestroy( $gd ); }
		else if( !is_null($this->gd) ){ imagedestroy( $this->gd ); }

	return true;
}
################################################################################
#	pc2byte(). Convert a floating point percentage (pc) into a single integer
#					value between -255 to 255. (ie: an integer)
#	NOTES	:	Percentage should be between -100 and 100.
################################################################################
function pc2byte( $percentage=null )
{
	$this->debug->in();

	if( is_null($percentage) ){ $this->died( "Percentage MUST BE between -100 and 100" ); }
	if( ($percentage < -100) || ($percentage > 100) ){
		$this->died( "Percentage MUST BE between -100 and 100" );
		}

	$percentage = ( $percentage / 100.0 );

	$this->debug->out();

	return ceil($percentage * 255.0);
}
################################################################################
#	crop_new(). Crops part of an image according to the parameters.
#	Variables	:
#		$old	:	(top,left)-(bottom,right) defines original rectangle
#		$new	:	(top,left)-(bottom,right) defines new rectangle
#	Example	:	crop_new( $gd, array(0,0,$w,$h), array($left,$top,$w,$h)
#	NOTES	:	IF you leave the first two numbers in OLD off - they become zeros(0)
#				IF you leave the last two numbers in NEW off - they become the
#					last two entries in the $old array.
#				Also - if you do not send any of the arguments over - it dies
################################################################################
function crop_new( $gd=null, $old=null, $new=null, $del=true )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	if( is_null($old) ){
		$this->died( "***** ERROR (" . __LINE__ . "): OLD is not defined in class_gd->crop_new()" );
		}

	if( is_null($new) ){
		$this->died( "***** ERROR (" . __LINE__ . "): NEW is not defined in class_gd->crop_new()" );
		}
#
#	This is for the OLD array.
#
#	Ok, because someone might use 'top', 'left', 'bottom', 'right' - we need to
#	convert it over to 0, 1, 2, 3.
#
	$o = $this->convertArray( $old );
#
#	This is for the NEW array.
#
#	Ok, because someone might use 'top', 'left', 'bottom', 'right' - we need to
#	convert it over to 0, 1, 2, 3.
#
	$n = $this->convertArray( $new );
#
#	Check to see if there are only TWO entries in the old array. If so - fix it.
#
	if( count($o) < 4 ){
		$a = [];
		$a[] = 0;
		$a[] = 0;
		$a[] = $o[0];
		$a[] = $o[1];

		unset( $o );
		$o = $a;
		}
#
#	Check to see if there are only TWO entries in the new array. If so - fix it.
#
	if( count($n) < 4 ){
		$a = [];
		$a[] = $n[0];
		$a[] = $n[1];
		$a[] = $o[2];
		$a[] = $o[3];

		unset( $n );
		$n = $a;
		}
#
#	Compute the new width and height
#
	$w = abs( $n[2] - $n[0] );
	$h = abs( $n[3] - $n[1] );

	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
#
#	From the online PHP documentation at www.php.net
#
#	imagecopyresampled(
#	    GdImage $dst_image,
#	    GdImage $src_image,
#	    int $dst_x,
#	    int $dst_y,
#	    int $src_x,
#	    int $src_y,
#	    int $dst_width,
#	    int $dst_height,
#	    int $src_width,
#	    int $src_height
#	): bool
#
	imagecopyresampled( $gd2, $gd, $n[1], $n[0], $o[1], $o[0], $w, $h, $o[3], $o[2] );
	if( $del === true ){ imagedestroy( $gd ); }

	$this->debug->out();
	return $gd2;
}
################################################################################
#	convertArray(). Converts the (top,left)-(bottom,right) kind of array
#		and return a 0,1,2,3.
#	NOTES	:	If the incoming array has numbers - just copy it over.
#			Remember 57, 79, 999, 8000 will still come out as 0, 1, 2, 3.
################################################################################
function convertArray( $a )
{
	$this->debug->in();

	$n = [];
	foreach( $a as $k=>$v ){
		if( preg_match("/\s*t.*/i", $k) ){ $n[0] = $v; }	#	top
			else if( preg_match("/\s*l.*/i", $k) ){ $n[1] = $v; }	#	left
			else if( preg_match("/\s*b.*/i", $k) ){ $n[2] = $v; }	#	bottom
			else if( preg_match("/\s*r.*/i", $k) ){ $n[3] = $v; }	#	right
			else { $n[] = $v; }	#	number
		}

	$this->debug->out();
	return $n;
}
################################################################################
#	sepia(). By shahilahmed4242 at gmail dot com at
#		https://www.php.net/manual/en/function.imagefilter.php
################################################################################
function sepia( $gd=null, $opt=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($opt) ){ $r = 90; $g = 55; $b = 30; }
		else if( $opt == 1 ){ $r = 100; $g = 50; $b = 0; }
		else if( $opt == 2 ){ $r = 100; $g = 70; $b = 50; }
		else if( $opt == 3 ){ $r = 90; $g = 60; $b = 30; }
		else if( $opt == 4 ){ $r = 60; $g = 60; $b = 0; }
		else if( $opt == 4 ){ $r = 90; $g = 90; $b = 0; }
		else if( $opt == 4 ){ $r = 45; $g = 45; $b = 0; }

	$gd = $this->greyscale( $gd );
	$gd = $this->brightness( -30 );
	$gd = $this->colorize( $gd, $r, $g, $b );

	$this->debug->out();
	return $gd;
}
################################################################################
#	colorize(). Does the GD function of the same name via the IMAGEFILTER part.
################################################################################
function colorize( $gd=null, $r=null, $g=null, $b=null, $a=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($r) ){ $this->died( "COLORIZE - No RED(r=null) value given" ); }
	if( is_null($g) ){ $this->died( "COLORIZE - No GREEN(g=null) value given" ); }
	if( is_null($b) ){ $this->died( "COLORIZE - No BLUE(b=null) value given" ); }
	if( is_null($a) ){ $a = 0; }

	$return = imagefilter( $gd, IMG_FILTER_COLORIZE, $r, $g, $b, $a );

	$this->debug->out();
	return $gd;
}
################################################################################
#	pixelate_v52(). Pre 5.3 version by martijn(97+1) at gmail dot com (solve math)
#		at https://www.php.net/manual/en/function.imagefilter.php
################################################################################
function pixelate_v52( $gd=null, $pixelsize=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($pixelsize) ){ $pixelsize = 1; }

    $maxX = imagesx( $gd );
    $maxY = imagesy( $gd );
    $rad = floor( $pixelsize/2 );
    for( $x=$rad; $x<$maxX; $x+=$pixelsize )
        for( $y=$rad; $y<$maxY; $y+=$pixelsize ){
            $color = imagecolorat( $gd, $x, $y );
            imagefilledrectangle( $gd, $x-$rad, $y-$rad,
				$x+$pixelsize-1, $y+$pixelsize-1,$color );
        }

	$this->debug->out();
	return $gd;
}
################################################################################
#	greyscale(). My own conversion to convert an image to greyscale.
################################################################################
function greyscale( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

    $w = imagesx( $gd );
    $h = imagesy( $gd );
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
            $color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->cr->get_ARGB( $color );
			$val = round(.299*$r + .587*$g + .114*$b);
#			$val = round( ($r + $g + $b) / 3 );
			$grey = $this->cr->put_ARGB( $a, $val, $val, $val );
			imagesetpixel( $gd, $x, $y, $grey );
			}
		}

	imagepng( $gd, "C:/Users/marke/Desktop/Books/Abby Rokah/out/test.png" );
	$this->debug->out();

	return $gd;
}
################################################################################
#	split_image(). Split an image up into where each part is a section.
#	NOTES:	The outfile MUST NOT have the extension on it. Use the FILENAME
#		and not the BASENAME.
################################################################################
function split_image( $gd=null, $path=null, $dir=null, $trans=null )
{
	$this->debug->in();

	if( is_null($gd) ){
		$this->died( "*****ERROR : GD is NULL!\n" );
		}

	if( is_null($path) ){
		$this->died( "*****ERROR : No output path given" );
		}

	if( is_null($dir) ){ $this->died( "Directory number is NULL!" ); }

	if( is_null($trans) ){ $this->died( "No Transparent color given!" ); }

	global $gd, $gd2, $trans, $magic_wand;
	global $xLow, $xHigh, $yLow, $yHigh, $width, $height;
	global $low_h, $low_s, $low_l;
	global $high_h, $high_s, $high_l;

	$dq = '"';
	$cf = $this->cf;
	$cr = $this->cr;
#
#	Set a magic_wand up.
#
#	Ratio : a/b = c/d
#
	$magic_wand = ( 10.0 / 100.0 ) * 255.0;
#
#	Make a new GD image to write to.
#
    $width = $w = imagesx( $gd );
    $height = $h = imagesy( $gd );
#
#	Set up where we store the image.
#
	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );

	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
#
#	Get the fixed color we are looking for and the current point
#	color we need to do testing.
#
			$pixel = imagecolorat( $gd, $x, $y );
			list( $pixel_a, $pixel_r, $pixel_g, $pixel_b ) = $cr->get_ARGB( $pixel );
#			echo "Pixel = " . sprintf( "%0x", $pixel ) . "\n";
			if( $pixel_a > 0 ){
#				echo "Pixel : A = $pixel_a, R = $pixel_r, G = $pixel_g, B = $pixel_b\n";
#				echo "Skipping @ " . __LINE__ . " : X = $x, Y = $y\n";
				continue;
				}
#
#	Clear the image area.
#	
			$this->rectf( $gd2, $trans, 0, 0, $w, $h );
#
#	Set up the low/high variables.
#
			$xLow = $yLow = 99999;
			$xHigh = $yHigh = -99999;
#
#	Set the directory we are going to work with.
#
			$file = sprintf( "%05d-%05d", $x, $y );
#
#	Covert the pixel to an HSL value so we can compare the HUE part.
#
			list( $pixel_a, $pixel_r, $pixel_g, $pixel_b ) = $cr->get_ARGB( $pixel );

			list( $low_h, $low_s, $low_l) =
				$this->rgb2hsl( $pixel_r, $pixel_g, $pixel_b );
#
#	Ok, we have the HSL of pixel's color. Now we want a range for
#	the Hue's range and the luminosity's range.
#
#	Hue = 0 to 360
#	Saturation = 0.0 to 1.0
#	Luminosity = 0 to 255
#
#	We don't care if the color is brilliant or very dull
#	We are giving Hue a +/- 10
#	We are giving luminosity a +/- 10
#
			$high_h = $low_h + 10;
			$high_s = $low_s + 0.2;
			$high_l = $low_l + 0.2;
			$low_h -= 10;
			$low_s -= 0.2;
			$low_l -= 0.2;
#
#	Create a new image on the fly.
#
#			echo "Location : X = $x, Y = $y\n";
			$this->find_image( $x, $y );
#			echo "Saving : $path/$dir/$file/$file.png\n";
			$this->save_image( $path, $dir, $file );
			}
		}

	imageDestroy( $gd2 );

	$this->debug->out();
	return true;
}
################################################################################
#	find_image(). Using the starting position, get the rest of THAT image.
#	NOTES	:	GD2 is the COPY of the image and GD3 is the NEW image.
################################################################################
function find_image( $x=null, $y=null, $cnt=0 )
{
	$this->debug->in();

	global $gd, $gd2, $trans, $magic_wand;
	global $xLow, $xHigh, $yLow, $yHigh, $width, $height;
	global $low_h, $low_s, $low_l;
	global $high_h, $high_s, $high_l;

#	$this->dump( "Entering---->", __LINE__, "X = $x, Y = $y, CNT = $cnt" );
#	echo "Memory Usage = " . $this->show_memory() . "\n";

	$cr = $this->cr;

    $w = $width;
    $h = $height;
#	echo "Width = $width, Height = $height\n";
#	echo "xLow = $xLow, xHigh = $xHigh, yLow = $yLow, yHigh = $yHigh\n";
#
#	Adjust the high/low variables
#
	if( $x < $xLow ){ $xLow = $x; }
	if( $x > $xHigh ){ $xHigh = $x; }
	if( $y < $yLow ){ $yLow = $y; }
	if( $y > $yHigh ){ $yHigh = $y; }
#
#	echo "xLow = $xLow, xHigh = $xHigh, yLow = $yLow, yHigh = $yHigh\n";
#
	$pixel = imagecolorat( $gd, $x, $y );
#
#	Move the pixel to GD2.
#
	imagesetpixel( $gd, $x, $y, $trans );
	imagesetpixel( $gd2, $x, $y, $pixel );

	for( $i=-1; $i<2; $i++ ){
		$nx = $x + $i;
		if( ($nx < 0) || ($nx >= $w) ){
#			echo "<----Skipping : NX = $nx\n";
			continue;
			}
		for( $j=-1; $j<2; $j++ ){
			$ny = $y + $j;
			if( ($ny < 0) || ($ny >= $h) ){
#				echo "<----Skipping : NY = $ny\n";
				continue;
				}

#			echo "Looking AT : NX = $nx, NY = $ny\n";
			if( abs($i) == abs($j) ){
#				echo "<----Skipping - I == J : I = $i, J = $j\n";
				continue;
				}

			$color = imagecolorat( $gd, $nx, $ny );
			list( $color_a, $color_r, $color_g, $color_b ) = $cr->get_ARGB( $color );
			if( $color_a > 0 ){
#				echo "A = $color_a, R = $color_r, G = $color_g, B = $color_b\n";
#				echo "<----Skipping : Color = Transparent (Already looked at)\n";
				continue;
				}
#
#	See if the color falls within the magic wand area.
#
			list( $color_h, $color_s, $color_l) =
				$this->rgb2hsl( $color_r, $color_g, $color_b );
#
#	Because I was having problems using the Magic Wand - I changed
#	it to using the HUE and Luminous values. Luminous can be changed
#	to Brilliance by just doing a ratio to make it go between 0% and 100%
#	Luminous values go from 0 to 255.
#
#			echo "Hues : low_h = $low_h, Color_h = $color_h, Magic Wand = $magic_wand\n";
			if( ($color_h < $low_h) || ($color_h > $high_h) ||
				($color_l < $low_l) || ($color_l > $high_l) ){
#				echo "<----Skipping : NX = $nx, NY = $ny\n";
				continue;
				}
#
#	Adjust the high/low variables
#
			if( $nx < $xLow ){ $xLow = $nx; }
			if( $nx > $xHigh ){ $xHigh = $nx; }
			if( $ny < $yLow ){ $yLow = $ny; }
			if( $ny > $yHigh ){ $yHigh = $ny; }
#			echo "xLow = $xLow, xHigh = $xHigh, yLow = $yLow, yHigh = $yHigh\n";

#
#	It does! Call ourselves again.
#
#			echo "Pixel = $pixel, Trans = $trans\n";
#			echo "Entering to : I = $i, J = $j, NX=$nx, NY=$ny, CNT=$cnt\n";
			$this->find_image( $nx, $ny, ++$cnt );

			$cnt--;
#			echo " Exiting from : I = $i, J = $j, NX=$nx, NY=$ny, CNT=$cnt\n";
			}
		}

#	echo "xLow = $xLow, xHigh = $xHigh, yLow = $yLow, yHigh = $yHigh\n";
#	$this->dump( "Exiting<----", __LINE__, "X = $x, Y = $y, CNT = $cnt" );

	$this->debug->out();
}
################################################################################
#	save_image(). Saves the image to a given location.
################################################################################
function save_image( $path=null, $dir=null, $file=null )
{
	global $gd, $gd2, $trans, $magic_wand;
	global $xLow, $xHigh, $yLow, $yHigh, $width, $height;
#
#	Don't save nothing images
#
	$c = 0;
    $w = $width;
    $h = $height;

	if( ($xLow < 0) || ($xLow >= $w) || ($xHigh < 0) || ($xHigh >= $w) ||
		($yLow < 0) || ($yLow >= $h) || ($yHigh < 0) || ($yHigh >= $h) ){
		$this->died(
			"*****ERROR : xHigh = $xHigh, xLow = $xLow, yHigh = $yHigh, yLow = $yLow\n"
			);
		}
#
#	Create a new GD
#
	$w = abs($xHigh - $xLow) + 1;
	$h = abs($yHigh - $yLow) + 1;
	if( ($w < 10) || ($h < 10) ){
#		echo "ABORTING : Not saving - too small. W = $w, H = $h\n";
		return false;
		}
#
#	Make a directory to hold everything.
#
	if( !file_exists("$path/$dir") ){ mkdir( "$path/$dir" ); }
	if( !file_exists("$path/$dir/$file") ){ mkdir( "$path/$dir/$file" ); }
#
#	Set up where we store the image.
#
	$gd3 = imagecreatetruecolor( $w+2, $h+2 );
	imagealphablending( $gd3, false );
	imagesavealpha( $gd3, true );
#
#	Clear it out
#
	$this->rectf( $gd3, $trans, 0, 0, $w+2, $h+2 );
#
#	Cut the image out. DO NOT DELETE!!! You are taking
#	the image OUT of GD2 and putting it into GD3 so you
#	can SAVE the IMAGE!!!
#
	imagecopy( $gd3, $gd2, 1,1, $xLow, $yLow, $w, $h );
	imagealphablending( $gd3, false );
	imagesavealpha( $gd3, true );
#
#	Save it
#
	imagepng( $gd3, "$path/$dir/$file/$file.png" );
#
#	And destroy it
#
	imagedestroy( $gd3 );
#
#	And leave
#
	if( $c++ > 0 ){ exit; }
	return true;
}
################################################################################
#	greyscale(). Convert to grayscale by vdepizzol at hotmail dot com at
#		at https://www.php.net/manual/en/function.imagefilter.php
################################################################################
function grayscale( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	imagefilter( $gd, IMG_FILTER_GRAYSCALE );

	$this->debug->out();

	return $gd;
}
################################################################################
#	negate(). Inverses the image. Taken from
#		https://www.geeksforgeeks.org/php-imagefilter-function/
################################################################################
function negate( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_NEGATE );

	$this->debug->out();

	return $gd;
}
################################################################################
#	smooth(). Applies a smoothing algorithm to the image
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function smooth( $gd=null, $amount=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($amount) ){
		$this->died( "*****ERROR : No Amount Given - \$this->smooth(GD=null,AMOUNT=null)\n" );
		}

	imagefilter( $gd, IMG_FILTER_SMOOTH, $amount );

	$this->debug->out();

	return $gd;
}
################################################################################
#	mean_removal(). Applies a mean removal to the image
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function mean_removal( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_MEAN_REMOVAL );

	$this->debug->out();

	return $gd;
}
################################################################################
#	blur(). Applies a selective blur to the image
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function blur( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_SELECTIVE_BLUR );

	$this->debug->out();

	return $gd;
}
################################################################################
#	gaussian(). Applies a Gaussian blur to the image
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function gaussian( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_GAUSSIAN_BLUR );

	$this->debug->out();

	return $gd;
}
################################################################################
#	emboss(). Emboss's the picture
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function emboss( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_EMBOSS );

	$this->debug->out();

	return $gd;
}
################################################################################
#	edgedetect(). Finds the edges of everything. Taken from
#		at https://www.phpied.com/image-fun-with-php-part-2/
################################################################################
function edgedetect( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	imagefilter( $gd, IMG_FILTER_EDGEDETECT );

	$this->debug->out();

	return $gd;
}
################################################################################
#	brightness(). Adjusts the brightness of an image. Taken from "Hacking with PHP"
#		at http://www.hackingwithphp.com/11/2/15
################################################################################
function brightness( $gd=null, $percent=0.0 )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( abs($percentage) > 100 ){ $this->died( "Percentage MUST BE between -100 and 100" ); }

	$value = $this->pc2byte( $percent );
	imagefilter( $gd, IMG_FILTER_BRIGHTNESS, $value );

	$this->debug->out();

	return $gd;
}
################################################################################
#	contrast(). Adjusts the contrast of an image. Taken from "Hacking with PHP"
#		at http://www.hackingwithphp.com/11/2/15
################################################################################
function contrast( $gd=null, $percent=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($percent) ){ $percent = 0.0; }
	if( abs($percent) > 100 ){ $this->died( "Percentage MUST BE between -100 and 100" ); }

	$value = $this->pc2byte( $percent );
	imagefilter( $gd, IMG_FILTER_CONTRAST, $value );

	$this->debug->out();

	return $gd;
}
################################################################################
#	darken(). Darkens one or more colors
#	ARGUMENTS	:
#		OLD_GD = The original GD to work with.
#		PERCENT = A number between -100 & 100. Numbers larger than 100 are cropped.
#		OLD_COLORS = EITHER a single color OR an array of colors. Single colors
#			are automatically converted to an array. The FORMAT of OLD_COLORS is :
#
#				$color_value = <RGB Value>;	#	A normal RGB value.
#				$old_colors[<color value>] = <data_value>;	#	Data value is ignored.
#
#				By doing this - you can ONLY HAVE ONE COLOR entry in the array.
#				Like $old_colors[012203]. The "012203" can only be in the array ONCE.
#				Because if you do it again - you STILL only have one entry in the array.
#
#	NOTES	:	What this routine will do is to take the list of colors presented
#				and darken them down by the given percentage. A negative amount means
#				that you actually want to brighten up a color or colors.
#				(i.e.: Darken is the reverse of brighten/brightness and works in
#				a different way. This one will ONLY lighten/darken the colors you
#				send over. Remember! It is NOT color[1]="rrggbb"; color[2]="rrggbb";
#				RATHER it is color['rrggbb'] = ###|1; Or in other words the
#				color makes up the array names. Got it?)
################################################################################
function darken( $old_gd=null, $percent=null, $old_colors=null, $del=true )
{
	$this->debug->in();

	if( is_null($old_gd) ){ $old_gd = $this->gd; }
	if( is_null($percent) ){
		$this->died( "NO PERCENTAGE GIVEN" );
		}

	if( is_null($old_colors) ){
		$this->died( "NO COLOR GIVEN" );
		}
#
#	If there is only one color.
#
	$cr = $this->cr;
	if( (count($old_colors) < 2) && !is_array($old_colors) ){
		$a = $old_colors;
		unset($old_colors);
		$old_colors = [];
		$old_colors[$a] = 1;
		}

	if( abs($percent) > 100.0 ){ $percent = ($percent / abs($percent)) * 100.0; }

	$w = imagesx( $old_gd );
	$h = imagesy( $old_gd );
	$percent = $percent / 100.0;
#
#	Make a new image
#
	$new_gd = imagecreatetruecolor( $w, $h );
	imagealphablending( $new_gd, false );
	imagesavealpha( $new_gd, true );
#
#	Now make a new image with the new color
#
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
            $color = imagecolorat( $old_gd, $x, $y );
#
#			$p_color = sprintf( "%08x", $color );
#			echo "COLOR = $color, p_color = $p_color\n";
#
			if( isset($old_colors[$color]) ){
#
#				echo "Color BEFORE = $p_color\n";
#
				list( $a, $r, $g, $b ) = $cr->get_ARGB( $color );
				$r = $r - ($r * $percent);
				$g = $g - ($g * $percent);
				$b = $b - ($b * $percent);
#
#				$p_color = sprintf( "%08x", $color );
#				echo "Color AFTER = $p_color\n";
#
				$color = $cr->put_ARGB( $a, $r, $g, $b );
				}

			imagesetpixel( $new_gd, $x, $y, $color );
			}
		}

	if( $del ){ imagedestroy( $old_gd ); }

	$this->debug->out();

	return $new_gd;
}
################################################################################
#	extract(). A function to extract color from an image. The new image is
#		created from the old image. The way we do this is to use an array. We
#		make an array with one entry per color. The value of the array is what
#		we want. So it looks like this:
#
#		$color[<ARGB Value>] = <Options>;
#
#		Where
#			<ARGB Value>	=	Is an RGB value (like what you get from the
#				imagecolorat() GD function.
#
#			<Options>	=	Some combination of the letters 'a', 'r', 'g', and 'b'.
#				which are the alpha, red, green, and blue values that go from 0-255.
#
#			There is also a DEFAULT value ($default) which is what the other colors
#			are set to. This needs to be set as "#;#;#;#". In the "argb" order.
#			NOTE	:	This DOES become the background color.
#
################################################################################
function extract( $old_gd=null, $old_color=null, $default=null )
{
	$this->debug->in();

	if( is_null($old_gd) ){ $old_gd = $this->gd; }
	if( is_null($old_color) ){ $this->died( "NO COLOR GIVEN" ); }
	if( is_null($default) ){ $default = "0;0;0;0"; }
#
#	Convert string to array.
#
	$cr = $this->cr;
	$a = explode( ";", $default );
#
#	Make sure the ALPHA is only 0-127. Anything else gets set to zero(0).
#
	if( abs($a[0]) > 127 ){ $a[0] = 0; }

	$default = [];
	foreach( $a as $k=>$v ){ $default[$k] = abs($v); }

	$w = imagesx( $old_gd );
	$h = imagesy( $old_gd );
#
#	Make a new image
#
	$new_gd = imagecreatetruecolor( $w, $h );
	imagealphablending( $new_gd, false );
	imagesavealpha( $new_gd, true );
#
#	Now make a new image of the new color
#
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
#
#	Set everything to the DEFAULT color.
#
			$new_a = $default[0];
			$new_r = $default[1];
			$new_g = $default[2];
			$new_b = $default[3];
#
#	Now get the current color.
#
			$color = imagecolorat( $old_gd, $x, $y );
#
#	Is this what we are looking for?
#
			if( isset($old_color[$color]) ){
				$options = $old_color[$color];
				list( $a, $r, $g, $b ) = $cr->get_ARGB( $color );
				if( preg_match("/a/i", $options) ){ $new_a = $a; }
				if( preg_match("/r/i", $options) ){ $new_r = $r; }
				if( preg_match("/g/i", $options) ){ $new_g = $g; }
				if( preg_match("/b/i", $options) ){ $new_b = $b; }
				}

			$color = $cr->put_ARGB( $new_a, $new_r, $new_g, $new_b );
			imagesetpixel( $new_gd, $x, $y, $color );
			}
		}

	$this->debug->out();

	return $new_gd;
}
################################################################################
#	sign(). Returns the sign of a number. By Milosz
#			on https://stackoverflow.com/questions/7556574/how-to-get-sign-of-a-number
################################################################################
function sign( $n=0 ){ return ($n > 0) - ($n < 0); }
################################################################################
#	color_atol(). Taken from the www.php.net website. Computes tolerance. Returns
#		HOW FAR it is towards being in tolerance. If the answer is NOT 35 - then
#		it is not in tolerance BUT the number returned tells you where it is out
#		of tolerance. This is similar to a magic wand.
#
#	Author	:	info at codeworx dot ch
#	Page	:	https://www.php.net/manual/en/function.imagecolorclosest.php
################################################################################
function color_atol( $color1=null, $color2=null, $tolerance=35 )
{
	$this->debug->in();

	if( is_null($color1) ){ $this->died("COLOR_TOL:COLOR #1 is NULL" ); }
	if( is_null($color2) ){ $this->died("COLOR_TOL:COLOR #1 is NULL" ); }
	if( is_null($tolerance) ){ $this->died("COLOR_TOL:TOLERANCE is NULL" ); }
#
#	Break up the colors
#
	$cr = $this->cr;
	list( $a1, $r1, $g1, $b1 ) = $cr->get_ARGB( $color1 );
	list( $a2, $r2, $g2, $b2 ) = $cr->get_ARGB( $color2 );
#
#	Compute Bottom
#
	$ba = $a2 - $tolerance;
	$br = $r2 - $tolerance;
	$bg = $g2 - $tolerance;
	$bb = $b2 - $tolerance;
#
#	Compute Top
#
	$ta = $a2 + $tolerance;
	$tr = $r2 + $tolerance;
	$tg = $g2 + $tolerance;
	$tb = $b2 + $tolerance;
#
#	Do tests
#
	$flag = 0;
	if( ($a1 >= $ba) && ($a1 <= $ta) ){ $flag += 1; }
	if( ($r1 >= $br) && ($r1 <= $tr) ){ $flag += 2; }
	if( ($g1 >= $bg) && ($g1 <= $tg) ){ $flag += 4; }
	if( ($b1 >= $bb) && ($b1 <= $tb) ){ $flag += 8; }

	$this->debug->out();
	return $flag;
}
################################################################################
#	color_tol(). Gets the tolerance of JUST THE COLOR (no Alpha). Alpha is
#		always either zero or one. So we just get rid of it.
################################################################################
function color_tol( $color1=null, $color2=null, $tolerance=35 )
{
	$this->debug->in();

	$flag = $this->color_atol( $color1, $color2, $tolerance );

	$test = $flag % 2;

	$this->debug->out();
	return ($flag - $test);
}
################################################################################
#	rgb2hsl(). A function to convert RGB values to HSL values.
#	Author	:	Brandon Heyer (Currently - Principal Engineer), Hyattsville, MD.
#	Page	:	Taken from https://gist.github.com/brandonheyer/5254516
#	NOTES	:	Modified the code to fit my programming style.
#			:	H = Hue, S = saturation, and L = Luminosity
#			:	R = red, G = green, B = blue
#			:	HUE can ONLY BE 0 thru 360
################################################################################
function rgb2hsl( $r=null, $g=null, $b=null )
{
	$this->debug->in();

	if( is_null($r) ){ $this->died( "R is NULL" ); }
	if( is_null($g) ){ $this->died( "G is NULL" ); }
	if( is_null($b) ){ $this->died( "B is NULL" ); }

	$oldR = $r;
	$oldG = $g;
	$oldB = $b;

	$r /= 255;
	$g /= 255;
	$b /= 255;

	$max = max( $r, $g, $b );
	$min = min( $r, $g, $b );

	$h;
	$s;
	$l = ( $max + $min ) / 2;
	$d = $max - $min;

   	if( $d == 0 ){
	   	$h = $s = 0; // achromatic
		}
		else {
			$s = $d / ( 1 - abs( 2 * $l - 1 ) );

			switch( $max ){
				case $r:
					$h = 60 * fmod( ( ($g - $b) / $d ), 6 );
					if( $b > $g ){ $h += 360; }
					break;

				case $g:
					$h = 60 * ( ($b - $r) / $d + 2 );
					break;

				case $b:
					$h = 60 * ( ($r - $g) / $d + 4 );
					break;
				}
			}
#
#	Make sure Hue is between 0 and 360
#
	$h = $h % 360;

	$this->debug->out();

	return array( $h, $s, $l );
}
################################################################################
#	color2hsl(). Send a color to the function and return the HSL values.
################################################################################
function color2hsl( $color=null )
{
	if( is_null($color) ){ $this->died( "COLOR is NULL" ); }

	list( $a, $r, $g, $b ) = $this->cr->get_ARGB( $color );
	return $this->rgb2hsl( $r, $g, $b );
}
################################################################################
#	hsl2rgb(). A function to convert HSL values to RGB values.
#	Author	:	Brandon Heyer (Currently - Principal Engineer), Hyattsville, MD.
#	Page	:	Taken from https://gist.github.com/brandonheyer/5254516
#	NOTES	:	Modified the code to fit my programming style.
#			:	H = Hue, S = saturation, and L = Luminosity
#			:	R = red, G = green, B = blue
################################################################################
function hsl2rgb( $h=null, $s=null, $l=null )
{
	$this->debug->in();

	if( is_null($h) ){ $this->died( "H is NULL" ); }
	if( is_null($s) ){ $this->died( "S is NULL" ); }
	if( is_null($l) ){ $this->died( "L is NULL" ); }

	$c = ( 1.0 - abs(2 * $l - 1.0) ) * $s;
	$x = $c * ( 1.0 - abs(fmod(($h / 60.0), 2.0) - 1.0) );
	$m = $l - ( $c / 2.0 );

	if( $h < 60.0 ){
		$r = $c;
		$g = $x;
		$b = 0;
		}
		else if( $h < 120.0 ){
			$r = $x;
			$g = $c;
			$b = 0;
			}
		else if( $h < 180.0 ){
			$r = 0;
			$g = $c;
			$b = $x;
			}
		else if( $h < 240.0 ){
			$r = 0;
			$g = $x;
			$b = $c;
			}
		else if( $h < 300.0 ){
			$r = $x;
			$g = 0;
			$b = $c;
			}
		else {
			$r = $c;
			$g = 0;
			$b = $x;
			}

	$r = ( $r + $m ) * 255.0;
	$g = ( $g + $m ) * 255.0;
	$b = ( $b + $m ) * 255.0;

	$this->debug->out();

	return array( round($r), round($g), round($b) );
}
################################################################################
#	hsb2rgb(). Convert between an HSB value and RGB colors. Returns R, G, and B.
################################################################################
function hsb2rgb()
{
	$this->debug->in();

	if( is_null($h) ){ $this->died( "H is NULL" ); }
	if( is_null($s) ){ $this->died( "S is NULL" ); }
	if( is_null($l) ){ $this->died( "L is NULL" ); }

	$this->died( "HSB2RGB is not written yet so not defined" );
	$this->debug->out();
}
################################################################################
#	rgb2hsb(). Convert between an R,G,B color to an HSB color. Returns HSB.
#	From:	https://stackoverflow.com/questions/6614792/
#				fast-optimized-and-accurate-rgb-hsb-conversion-code-in-c
#	Author:	sehe on StackOverflow
################################################################################
function rgb2hsb( $rgb_r=null, $rgb_g=null, $rgb_b=null )
{
	$this->debug->in();

	if( is_null($r) ){ $this->died( "R is NULL" ); }
	if( is_null($g) ){ $this->died( "G is NULL" ); }
	if( is_null($b) ){ $this->died( "B is NULL" ); }

	$r = $rgb_r / 255.0;
	$g = $rgb_g / 255.0;
	$b = $rgb_b / 255.0;
	$max = max( $rgb_r, $rgb_g, $rgb_b );
	$min = min( $rgb_r, $rgb_g, $rgb_b );
    $delta = $max - $min;
    if( $delta != 0 ){
        $hue = null;
        if( $rgb_r == $max ){
            $hue = ($rgb_g - $rgb_b) / $delta;
			}
			else {
				if( $rgb_g == $max ){
					$hue = 2 + ($rgb_b - $rgb_r) / $delta;
					}
					else {
						$hue = 4 + ($rgb_r - $rgb_g) / $delta;
						}
				}

		$hue *= 60;
		if( $hue < 0 ){ $hue += 360; }
		}
		else {
			$hue = 0;
			}

    $saturation = ($max == 0) ? 0 : ($max - $min) / $max;
    $brightness = $max;

	return array( $hue, $saturation, $brightness );
} 
################################################################################
#	rgb2hsv(). Convert from RGB to an HSV set of values
#	From:	https://stackoverflow.com/questions/6614792/
#				fast-optimized-and-accurate-rgb-hsb-conversion-code-in-c
#	Author:	idbrii on StackOverflow
################################################################################
function rgb2hsv( $rgb_r=null, $rgb_g=null, $rgb_b=null )
{
    $K = 0.0;

    if( $rgb_g < $rgb_b ){
        list( $rgb_g, $rgb_b ) = $this->cm->swap( $rgb_g, $rgb_b );
        $K = -1.0;
		}

    if( $rgb_r < $rgb_g ){
        list( $rgb_r, $rgb_g ) = $this->cm->swap( $rgb_r, $rgb_g );
        $K = -2.0 / 6.0 - $K;
		}

    $chroma = $rgb_r - min($rgb_g, $rgb_b);
    $h = abs($K + ($rgb_g - $rgb_b) / (6.0 * $chroma + 1.0e-20));
    $s = $chroma / ($rgb_r + 1.0e-20);
    $v = $rgb_r;

	return array( $h, $s, $v );
}
################################################################################
#	hsv2rgb(). Convert an HSV value to an RGB value.
################################################################################
function hsv2rgb( $h=null, $s=null, $v=null )
{
	$this->debug->in();

	if( is_null($h) ){ $this->died( "H is NULL" ); }
	if( is_null($s) ){ $this->died( "S is NULL" ); }
	if( is_null($l) ){ $this->died( "L is NULL" ); }

	$this->died( "HSB2RGB not written yet - so not defined" );
	$this->debug->out();
}
################################################################################
#	rect(). Does an UNFILLED rectangle.
#	NOTES	:	If you leave W and H off, they are taken from the current info.
#				If you leave X and Y off, they are taken from the old values.
#				REMEMBER! CALL SET() FIRST! THEN USE THESE ROUTINES.
################################################################################
function rect( $gd=null, $color=null, $x=null, $y=null, $w=null, $h=null, $opt=TRUE )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($color) ){ $color = $this->trans; }
	if( is_null($x) ){ $x = $this->old_x; }
	if( is_null($y) ){ $y = $this->old_y; }
	if( is_null($w) ){ $w = $this->w; }
	if( is_null($h) ){ $h = $this->h; }

	$this->old_color = $color;
	if( $opt ){ $return = imagerectangle( $gd, $x, $y, $w, $h, $color ); }
		else { $return = imagefilledrectangle( $gd, $x, $y, $w, $h, $color ); }

	$this->debug->out();
	return $return;
}
################################################################################
#	rectf(). Does  FILLED RECTANGLE.
#	NOTES	:	If you leave W and H off, they are taken from the current info.
################################################################################
function rectf( $gd=null, $color=null, $x=null, $y=null, $w=null, $h=null )
{
	$this->debug->in();
	$this->debug->out();

	return $this->rect( $gd, $color, $x, $y, $w, $h, FALSE );
}
################################################################################
#	info(). Does the GD_INFO function.
################################################################################
function info( $gd=null )
{
	$this->debug->in();
	$this->debug->out();

	if( is_null($gd) ){ $gd = $this->gd; }

	return gd_info( $gd );
}
################################################################################
#	fsize(); Does the getimagesize() function.
#	NOTES	:	The "f" at the front signafies this is a FILE operation.
#			:	Dies if no filename is given or does not exist or wrong type.
#	OPT		:	Is set to TRUE or FALSE. TRUE means just do fsize(), FALSE
#				means to do the fsizefs() function.
################################################################################
function fsize( $filename=null, $opt=TRUE )
{
	$this->debug->in();

	if( is_null($filename) ){ $this->died( "FILENAME is NULL" ); }
	if( !file_exists($filename) ){
		$this->died( "FILENAME does not exist ($filename)" );
		}
	if( !preg_match("/$this->exts_all$/i", $filename) ){
		$this->died( "Unknown type of FILENAME ($filename)" );
		}

	$JPGAPP = [];
	if( $opt ){ $info = $this->gd->getimagesize( $filename, $jpgapp ); }
		else { $info = $this->gd->getimagesizefromstring( $filename, $jpgapp ); }
	$info[] = $jpgapp;

	$this->debug->out();
	return $info;
}
#-------------------------------------------------------------------------------
function fsizefs( $filename=null ){ return $this->fsize( $filename, FALSE ); }
################################################################################
#	ellipse(). Does the gd imageellipse() function.
#	NOTES	:	COLOR is first because if you want the ellipse to be centered
#				the function just calculates the default.
#	DEFAUTS	:	Center X&Y = (w/2)&(h/2). Width and Height are already given.
#	OPT		:	TRUE = imageellipse, FALSE = imagefilledellipse
#
#	IMPORTANT : These routines use the $THIS->GD. So FIRST! you must call
#		the SET() function to SET $THIS->GD!!!!! DO NOT FORGET!!!!
################################################################################
function ellipse( $gd=null, $color=null, $cx=null, $cy=null, $w=null, $h=null, $opt=true )
{
	$this->debug->in();

#	echo "COLOR = $color, CX = $cx, CY = $cy, W = $w, H = $h\n";
	if( is_null($gd) || !is_resource($gd) ){ $gd = $this->gd; }
	if( is_null($color) ){ $color = $this->white; }
	if( is_null($cx) ){ $cx = ceil( $this->w / 2 ); }	#	Always round up on even numbers
	if( is_null($cy) ){ $cy = ceil( $this->h / 2 ); }	#	Always round up on even numbers
	if( is_null($w) ){ $w = $this->w; }
	if( is_null($h) ){ $h = $this->h; }

#	echo "W = $w, H = $h\n";
	$w = ceil( $w / 2.0 );
	$h = ceil( $h / 2.0 );

	$this->old_color = $color;
	if( $opt ){ $return = imageellipse( $gd, $cx, $cy, $w, $h, $color ); }
		else { $return = imagefilledellipse( $gd, $cx, $cy, $w, $h, $color ); }

	$this->debug->out();
	return $return;
}
#-------------------------------------------------------------------------------
function ellipsef( $gd=null, $color=null, $cx=null,
	$cy=null, $w=null, $h=null, $opt=FALSE )
{
	$this->debug->in();

	$this->debug->out();

	return $this->ellipse( $gd, $color, $cx, $cy, $w, $h, $opt );
}
#-------------------------------------------------------------------------------
function circle( $gd=null, $color=null, $cx=null, $cy=null, $w=null, $opt=TRUE )
{
	$this->debug->in();

	if( is_null($w) ){ $w = $this->w; }

	$this->debug->out();

	return $this->ellipse( $gd, $color, $cx, $cy, $w, $w, $opt );
}
#-------------------------------------------------------------------------------
function circlef( $gd=null, $color=null, $cx=null, $cy=null, $w=null, $opt=FALSE )
{
	$this->debug->in();

	if( is_null($w) ){ $w = $this->w; }

	$this->debug->out();

	return $this->ellipse( $gd, $color, $cx, $cy, $w, $w, $opt );
}
################################################################################
#	plot(). Plots a point
#	NOTES	:	If COLOR is null, then it is white.
#			:	FLIP makes the Y axis flip over so zero is at the top
################################################################################
function plot( $gd=null, $color=null, $x=null, $y=null, $flip=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($x) ){ $this->died( "X is NULL" ); }
	if( is_null($y) ){ $this->died( "Y is NULL" ); }
	if( is_null($color) ){ $color = $this->white; }
	if( is_null($flip) ){ $flip = true; }
#
#	Flip the Y value?
#
	if( $flip ){ $y = $this->h - $y; }

	$this->old_color = $color;
	$gd = imagesetpixel( $gd, $x, $y, $color );

	$this->debug->out();
	return $gd;
}
#-------------------------------------------------------------------------------
function pixel( $gd=null, $x=null, $y=null, $color=null, $flip=false )
{
	return $this->plot( $gd, $x, $y, $color );
}
#-------------------------------------------------------------------------------
function point( $gd=null, $x=null, $y=null, $color=null, $flip=false )
{
	return $this->plot( $gd, $x, $y, $color );
}
#-------------------------------------------------------------------------------
function dot( $gd=null, $x=null, $y=null, $color=null, $flip=false )
{
	return $this->plot( $gd, $x, $y, $color );
}
################################################################################
#	plots(). Plots as many points as you send over.
#	NOTES	:	IF YOU SPECIFY COLOR - THEN IT IS APPLIED TO ALL POINTS WITH NO
#				COLOR ASSIGNED TO THEM.
#				IF you specify the SIZE - then this automatically calls ellipsef().
#	DEFAULT	:	The FLIP option is already done so you can leave it off.
################################################################################
function plots( $gd=null, $array=null, $color=null, $size=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($array) ){ $this->died( "ARRAY is NULL" ); }

	$return = [];
	foreach( $array as $k=>$v ){
		$c = 0;
		foreach( $v as $k1=>$v1 ){
#
#	Move over the information in the format of X, Y, COLOR.
#
			if( $c < 1 ){ $x = $v1; }
				else if( $c == 1 ){ $y = $v1; }
				else if( $c == 2 && is_null($color) ){ $c = $v1; }
				else { $flip = $v1; }
			}

		if( !is_null($size) ){ $return[] = $this->plot( $c, $x, $y ); }
			else { $return[] = $this->ellipsef( $c, $x, $y, $size ); }
		}

	$this->debug->out();

	return $return;
}
#-------------------------------------------------------------------------------
function pixels( $gd=null, $array=null, $color=null, $size=null )
{
	return $this->plots( $gd, $array, $color, $size );
}
#-------------------------------------------------------------------------------
function points( $gd=null, $array=null, $color=null, $size=null )
{
	return $this->plots( $gd, $array, $color, $size );
}
#-------------------------------------------------------------------------------
function dots( $gd=null, $array=null, $color=null, $size=null )
{
	return $this->plots( $gd, $array, $color, $size );
}
################################################################################
#	moveto(). Set where to move to.
################################################################################
function moveto( $x=null, $y=null )
{
	$this->debug->in();

	if( is_null($x) ){ $this->died("X is NULL" ); }
	if( is_null($y) ){ $this->died("Y is NULL" ); }

	$this->old_x = $x;
	$this->old_y = $y;

	$this->debug->out();

	return true;
}
################################################################################
#	lineto(). Draw a line from the last location to the new location.
################################################################################
function lineto( $gd=null, $color=null, $x=null, $y=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$ox = $this->old_x;
	$oy = $this->old_y;

	$this->debug->out();

	return $this->line( $gd, $color, $ox, $oy, $x, $y );
}
################################################################################
#	line(). Draws a line.
################################################################################
function line( $gd=null, $color=null, $sx=null, $sy=null, $ex=null, $ey=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($sx) ){ $this->died("SX is NULL" ); }
	if( is_null($sy) ){ $this->died("SY is NULL" ); }
	if( is_null($ex) ){ $this->died("EX is NULL" ); }
	if( is_null($ey) ){ $this->died("EY is NULL" ); }

	$this->old_color = $color;
	$gd = imageline( $gd, $sx, $sy, $ex, $ey, $color );

	$this->old_x = $x;
	$this->old_y = $y;

	$this->debug->out();

	return $gd;
}
################################################################################
#	get_color(). Gets a color and returns that RGB value.
################################################################################
function get_color( $gd=null, $name=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ $gd = $this->gd; }
	if( is_null($name) ){ $this->died( "COLOR is NULL" ); }

	list( $red, $green, $blue ) = $this->cc->name2rgb( $name );
	echo "NAME = $name, RED = $red, GREEN = $green, BLUE = $blue\n";

	$color = imagecolorallocate( $gd, $red, $green, $blue );

	$this->debug->out();
	return $color;
}
################################################################################
#	get_topleft(). Returns the top-left most pixel of an image.
#		First color is used to help find where the top of the image is.
#	Returns	:	The X & Y location of the top-left corner.
################################################################################
function get_topleft( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$border = imagecolorat( $gd, 0, 0 );
#
#	T->B
#
	for( $y=0; $y<$h; $y++ ){
#
#	L->R
#
		for( $x=0; $x<$w; $x++ ){
#
#	Get a color and if it is the same as the border - continue on.
#	Otherwise - return the Y location where we got it.
#
            $color = imagecolorat( $gd, $x, $y );
			if( $color != $border ){ return array( $x, $y ); }
			}
		}

	$this->debug->out();
}
################################################################################
#	get_botleft(). Returns the bottom-left most pixel of an image.
#		First color is used to help find where the top of the image is.
#	Returns	:	The X & Y location of the bottom-left corner.
################################################################################
function get_botleft( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$border = imagecolorat( $gd, 0, 0 );
#
#	B->T
#
	for( $y=($h-1); $y>0; $y-- ){
#
#	L->R
#
		for( $x=0; $x<$w; $x++ ){
#
#	Get a color and if it is the same as the border - continue on.
#	Otherwise - return the Y location where we got it.
#
            $color = imagecolorat( $gd, $x, $y );
			if( $color != $border ){ return array( $x, $y ); }
			}
		}

	$this->debug->out();
}
################################################################################
#	get_topright(). Returns the left most pixel of an image.
#		First color is used to help find where the top of the image is.
#	NOTES	:	Just because get_topleft gives you an X location - that DOES NOT
#				mean that THAT location is the left most pixel. Think of a STAR.
################################################################################
function get_topright( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$ret_x = -99999;
	$ret_y = -99999;
	$border = imagecolorat( $gd, 0, 0 );
#
#	T->B
#
	for( $y=0; $y<$h; $y++ ){
#
#	R->L
#
		for( $x=($w-1); $x>0; $x-- ){
#
#	Get a color and if it is the same as the border - continue on.
#	Otherwise - return the Y location where we got it.
#
            $color = imagecolorat( $gd, $x, $y );
			if( $color != $border ){
				$cnt = 0;
				for( $i=-2; $i<3; $i++ ){
					$nx = $x + $i;
					if( ($nx < 0) || ($nx >= $w) ){
						for( $j=-2; $j<3; $j++ ){
							$cnt++;
							}

						continue;
						}

					for( $j=-2; $j<3; $j++ ){
						$ny = $y + j;
						if( ($ny < 0) || ($ny >= $h) ){
							$cnt++;
							continue;
							}

						$c = imagecolorat( $gd, $nx, $ny );
						if( $c == $border ){ $cnt++; }
						}
					}
#
#	If there are four or more spaces which ARE transparent around
#	this dot - AND IF - the dot meets our criteria for a point
#	THEN - we can make it the farthest point to the right of the
#	image.
#
				if( $cnt > 3 ){
						if( ($x > $ret_x) && ($y > $ret_y) ){
							$ret_x = $x;
							$ret_y = $y;
							}
						}
				}
			}
		}

	$this->debug->out();

	return array( $ret_x, $ret_y );
}
################################################################################
#	get_right(). Returns the right most pixel of an image.
#		First color is used to help find where the top of the image is.
#	NOTES	:	Just because get_bot gives you an X location - that DOES NOT
#				mean that THAT location is the right most pixel. Think of a STAR.
################################################################################
function get_botright( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$border = imagecolorat( $gd, 0, 0 );
#
#	R->L
#
	for( $x=($w-1); $x>0; $x-- ){
#
#	B->T
#
		for( $y=($h-1); $y>0; $y-- ){
#
#	Get a color and if it is the same as the border - continue on.
#	Otherwise - return the Y location where we got it.
#
            $color = imagecolorat( $gd, $x, $y );
			if( $color != $border ){ return array( $x, $y ); }
			}
		}

	$this->debug->out();
}
################################################################################
#	get_bb(). Gets the image's bounding box.
################################################################################
function get_bb( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$bb = [];
	$border = imagecolorat( $gd, 0, 0 );
#
#	We want to find the top-left, top-right, bottom-left, and bottom-right locations.
#
#	bb[0][left|1], bb[0][top|0]
#	bb[1][left|1], bb[1][bottom|0]
#	bb[2][right|1], bb[2][top|0]
#	bb[3][right|1], bb[3][bottom|0]
#
	list( $bb[0][0], $bb[0][1] ) = $this->get_topleft( $gd );
	list( $bb[1][0], $bb[1][1] ) = $this->get_botleft( $gd );
	list( $bb[2][0], $bb[2][1] ) = $this->get_topright( $gd );
	list( $bb[3][0], $bb[3][1] ) = $this->get_botright( $gd );

	$this->debug->out();

	return $bb;
}
#--------------------------------------------------------------------------------
function get_bounding_box( $gd=null ){ return $this->get_bb( $gd ); }
################################################################################
#	string(). Print a string
################################################################################
function string( $gd=null, $font=null, $x=null, $y=null, $string=null, $color=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ $gd = $this->gd; }
	if( is_null($font) ){ $font = 3; }
	if( is_null($x) ){ $x = $this->old_x; }
	if( is_null($y) ){ $y = $this->old_y; }
	if( is_null($string) ){
		$string = $this->old_string;
		if( is_null($string) ){ $this->died( "STRING IS NULL" ); }
		}

	if( is_null($color) ){ $color = $this->old_color; }

	$this->debug->out();

	return imagestring( $gd, $font, $x, $y, $string, $color );
}
################################################################################
#	stringUp(). Print a string
################################################################################
function stringUp( $gd=null, $font=null, $x=null, $y=null, $string=null, $color=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($font) ){ $font = 3; }
	if( is_null($x) ){ $x = $this->old_x; }
	if( is_null($y) ){ $y = $this->old_y; }
	if( is_null($string) ){
		$string = $this->old_string;
		if( is_null($string) ){ $this->died( "STRING IS NULL" ); }
		}

	if( is_null($color) ){ $color = $this->old_color; }

	$this->debug->out();

	return imagestringup( $gd, $font, $x, $y, $string, $color );
}
################################################################################
#	rotString(). Rotates a string by a certain number of degrees
#	NOTES : Returns and ARRAY of GD and whether or not it worked (true/false)
################################################################################
function rotString( $gd=null, $angle=null, $font=null, $x=null,
	$y=null, $string=null, $color=null )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }
	if( is_null($font) ){ $font = 3; }
	if( is_null($x) ){ $x = $this->old_x; }
	if( is_null($y) ){ $y = $this->old_y; }
	if( is_null($string) ){
		$string = $this->old_string;
		if( is_null($string) ){ $this->died( "STRING IS NULL" ); }
		}

	if( is_null($color) ){ $color = $this->old_color; }
	
	$width = ($font + 3) * strlen( $string );

	$gd1 = imagecreatetruecolor( $width, 50 );
	imagealphablending( $gd1, false );
	imagesavealpha( $gd1, true );

	$black = imagecolorallocate( $gd1, 0, 0, 0 );
	$white = imagecolorallocate( $gd1, 255, 255, 255 );
#
#	Create a transparent color for this image
#
	$trans = $this->cf->unique_color( $gd1 );
	list( $a, $r, $g, $b ) = $this->cr->get_ARGB( $trans );
	$trans = $this->cr->put_ARGB( 127, $r, $g, $b );
#
#	Make everything transparent
#
	$this->rectf($gd1, 0, 0, $width, 50, $trans);
	imagestring( $font, $x, $y, $string, $color );
#
#	Rotate it
#
	$gd2 = imagerotate( $gd1, $angle, $trans );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
#
#	Get the new size
#
	$w = imagesx( $gd2 );
	$h = imagesy( $gd2 );
	if( is_resource($gd1) ){ imagedestroy( $gd1 ); }
#
#	Merge it with whatever is in GD. The text will ALWAYS overlay
#	whatever is in GD.
#
	$ret = imagecopy( $gd, $gd2, $x, $y, 0, 0, $w, $h );
	imagealphablending( $gd, false );
	imagesavealpha( $gd, true );

	imagedestroy( $gd2 );

	$this->debug->out();

	return array( $gd, $ret );
}
################################################################################
#	oneTrans(). Look at all of the transparent colors and change
#		them all to be just ONE transparent color.
################################################################################
function oneTrans( $gd=null )
{
	$this->debug->in();
	if( is_null($gd) ){ $gd = $this->gd; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$cr = $this->cr;
	$cf = $this->cf;
#
#	Get a single transparent color for the whole image
#
	$trans = $cf->unique_color( $gd );
	list( $a, $r, $g, $b ) = $cr->get_ARGB( $trans );
	$trans = $cr->put_ARGB( 127, $r, $g, $b );

	for( $x=0; $x<$w; $x++ ){
		$ey = false;
		for( $y=0; $y<$h; $y++ ){
			$color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $cr->get_ARGB( $color );
			if( $a > 0 ){ imagesetpixel( $gd, $x, $y, $trans ); }
				else {
					for( $y2=($h-1); $y2>=0; $y2-- ){
						$color = imagecolorat( $gd, $x, $y2 );
						list( $a, $r, $g, $b ) = $cr->get_ARGB( $color );
						if( $a > 0 ){ imagesetpixel( $gd, $x, $y, $trans ); }
							else {
								for( $y3=$y+1; $y3<$y2-1; $y3++ ){
									$color = imagecolorat( $gd, $x, $y3 );
									list( $a, $r, $g, $b ) = $cr->get_ARGB( $color );
									if( $a > 0 ){
										$color = $cr->put_ARGB( 0, $r, $g, $b );
										imagesetpixel( $gd, $x, $y, $trans );
										}
									}

								$y = $h;
								$y2 = -1;
								break;
								}
						}
					}
			}
		}

	$this->debug->out();
	return $gd;
}
################################################################################
#	save(). Saves an image.
################################################################################
function save( $filename=null )
{
	$this->debug->in();
	$this->cf->put_image( $this->gd, $filename );
	$this->debug->out();
 
	return true;
}
################################################################################
#	set_color(). Sets the color to use
################################################################################
function set_color( $color=null )
{
	$this->debug->in();
	if( is_null($color) ){ $this->died( "COLOR IS NULL" ); }

	$this->old_color = $color;
	$this->debug->out();

	return true;
}
#--------------------------------------------------------------------------------
function get_used_color(){ return $this->old_color; }
################################################################################
#	died(). A simple function to print an error message and then die.
################################################################################
function died( $string=null, $opt=FALSE )
{
	$this->debug->in();

	if( is_null($string) ){ $string = "Program Aborted"; }

	$backtrace = debug_backtrace();

	echo "TITLE : $string\n";
	foreach( $backtrace as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			$flag = false;
			if( is_array($v1) ){
				$flag = true;
				foreach( $v1 as $k2=>$v2 ){
					echo "	" . strtoupper($k1) . " : [$k2] = $v2\n";
					}
				}
	
			if( preg_match("/object/i", $k1) &&  $opt ){
				$flag = true;
				}
				else if( preg_match("/object/i", $k1) ){ $flag = true; }

			if( !$flag ){
				echo "	" . strtoupper($k1) . " = " . $v1 . "\n";
				}
			}


		echo "\n";
		}

	echo __FILE__ . ":" . __CLASS__ . ":" . __METHOD__ . ":" . __LINE__ . " = $string";

	$this->debug->out();

	exit(-1);
}
################################################################################
#	show_memory(). Taken from
#		https://www.php.net/manual/en/function.memory-get-usage.php
#		using xelozz -at- gmailcom's code
################################################################################
function show_memory()
 {
	$size = memory_get_usage( true );
    $unit=array('b','kb','mb','gb','tb','pb');
    $s = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	return $s;
 }
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", __LINE__, $num );
################################################################################
function dump( $title=null, $line=null, $arg=null )
{
	$this->debug->in();

	if( is_null($title) ){ $title = "DUMP: "; }
	if( is_null($line) ){ $line = "unknown"; }
	if( is_null($arg) ){ $arg = ""; }

	if( is_array($arg) ){
		echo "$title @ Line = $line : \n";
		print_r( $arg );
		echo "\n";
		}
		else {
			echo "$title @ Line = $line : $arg\n";
			}

	$this->debug->out();
	return true;
}
################################################################################
#	__destruct(). Do the clean-up necessary.
################################################################################
function __destruct()
{
	$this->debug->in();

	if( is_resource($this->gd) ){ imagedestroy( $this->gd ); }

	$this->debug->out();
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['gd']) ){
		$GLOBALS['classes']['gd'] = new class_gd();
		}
?>
