<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n" );
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

	include_once( "$lib/class_files.php" );

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_rgb();
#
#-Description:
#
#	A class to handle RGB stuff.
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
#	Mark Manning			Simulacron I			Mon 10/05/2020 22:20:48.71 
#		Original Program.
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
#		CLASS_RGB.PHP. A class to handle working with rgb stuff.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_rgb
{
	private $debug = null;
	private $colors = null;
	private $transparent = null;
	private $gd = null;
	private $cf = null;
	private $new_gd = null;

################################################################################
#	__construct(). Starts the class.
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
#	init(). A function to start the entire thing over again.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->cf = new class_files();

	$this->debug->out();
	return true;
}
################################################################################
#	get_ARGB(). Get the A-R-G-B elements out of a color.
################################################################################
function get_ARGB( $c=null )
{
	$this->debug->in();

	if( is_null($c) ){ return false; }

	$a = ($c >> 24) & 0xff;
	$r = ($c >> 16) & 0xff;
	$g = ($c >> 8) & 0xff;
	$b = ($c & 0xff);

	$this->debug->out();

	return array( $a, $r, $g, $b );
}
################################################################################
#	get_RGB(). Get the R-G-B elements out of a color.
################################################################################
function get_RGB( $c=null )
{
	$this->debug->in();

	list( $a, $r, $g, $b ) = $this->get_ARGB( $c );

	$this->debug->out();

	return array( $r, $g, $b );
}
################################################################################
#	put_RGB(). Create a color out of the RGBA values.
################################################################################
function put_RGB( $r=null, $g=null, $b=null )
{
	$this->debug->in();
	$this->debug->out();

	return $this->put_ARGB( 0, $r, $g, $b );

}
################################################################################
#	put_ARGB(). Create an ARGB value.
################################################################################
function put_ARGB( $a=null, $r=null, $g=null, $b=null )
{
	$this->debug->in();

	if( is_null($a) || is_null($r) || is_null($g) || is_null($b) ){ return false; }

	$a = (($a & 0xff) << 24);
	$r = (($r & 0xff) << 16);
	$g = (($g & 0xff) << 8);
	$b = ($b & 0xff);

	$this->debug->out();
	return ($a + $r + $g + $b);
}
################################################################################
#	rgb_DIFF(). Get the difference of two colors.
################################################################################
function argb_DIFF( $c1=null, $c2=null )
{
	$this->debug->in();

	if( is_null($c1) || is_null($c2) ){ return false; }

	list( $c1a, $c1r, $c1g, $c1b ) = $this->get_ARGB( $c1 );
	list( $c2a, $c2r, $c2g, $c2b ) = $this->get_ARGB( $c2 );

	$da = abs( $c1a - $c2a );
	$dr = abs( $c1r - $c2r );
	$dg = abs( $c1g - $c2g );
	$db = abs( $c1b - $c2b );

	$this->debug->out();
	return array( $da, $dr, $dg, $db );
}
################################################################################
#	unique_color(). Find a color to be our unique color.
################################################################################
function unique_color( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$colors = $this->get_colors( $gd );
#
#	Now pick a new one that is NOT in the image.
#
	$a = 127;	#	Always make it transparent.
	$r = $g = $b = 0;
	while( true ){
		$color = $this->put_RGB( $r, $g, $b );

		foreach( $colors as $k=>$v ){
			if( $color == $k ){
				if( ++$r > 255 ){
					$r = 0;
					if( ++$g > 255 ){
						$g = 0;
						if( ++$b > 255 ){ echo "B > 255\n"; }
						}
					}
				}
				else { break; }
			}

		$this->debug->out();

		$color = $this->put_ARGB( $a, $r, $g, $b );
		return $color;
		}

	$this->debug->out();

	return false;
}
################################################################################
#	is_trans(). Checks to see if there is already a transparent background.
#		If there is - it returns the color - else it returns FALSE.
################################################################################
function is_trans( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ die("***** ERROR : GD is NULL!\n"); }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->get_ARGB( $color );
			if( ($a > 0) && ($a < 128) ){
				$this->debug->out();
				return $color;
				}
			}
		}
#
#	We did not find a transparent color so now we need to create one.
#
	$trans = $this->cf->unique_color( $gd );
	$a = 127;
	$r = ($trans >> 16) & 0xff;
	$g = ($trans >> 8) & 0xff;
	$b = $trans & 0xff;

	$trans = ($a & 0xff) << 24;
	$trans |= ($r & 0xff) << 16;
	$trans |= ($g & 0xff) << 8;
	$trans |= ($b & 0xff);

	$this->debug->out();
	return $trans;
}
################################################################################
#	magic_wand(). A function to act like a magic wand.
################################################################################
function magic_wand( $gd=null, $color=null, $dif=null )
{
	if( is_null($gd) || !is_resource($gd) ){ $this->debug->t( "GD is NULL" ); }
	if( is_null($color) ){ $this->debug->t( "COLOR is NULL" ); }
	if( is_null($dif) ){ $this->debug->t( "DIF is NULL" ); }
	if( $dif < 1 ){ $this->debug->t( "DIF is less than one" ); }

	$dif = abs( $dif ) + 1;
	$trans = imagecolorat( $gd, 0, 0 );

	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$gd2 = imagecreatetruecolor( $w, $h );

	if (function_exists('imagecolorallocatealpha')) {
		imagealphablending($gd2, false);
		imagesavealpha($gd2, true);
		imagefilledrectangle($gd2, 0, 0, $w, $h, $trans);
		}

	$black = imagecolorallocatealpha( $gd2, 0, 0, 0, 0 );

	list( $ca, $cr, $cg, $cb ) = $this->get_ARGB( $color );
	$min_r = $cr - $dif;
	$max_r = $cr + $dif;
	$min_g = $cg - $dif;
	$max_g = $cg + $dif;
	$min_b = $cb - $dif;
	$max_b = $cb + $dif;

	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$rgb = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->get_ARGB( $rgb );
			if( (($r > $min_r) && ($r < $max_r)) &&
				(($g > $min_g) && ($g < $max_g)) &&
				(($b > $min_b) && ($b < $max_b)) ){
				imagesetpixel( $gd2, $x, $y, $black );
				imagesetpixel( $gd, $x, $y, $trans );
				}
			}
		}

	return $gd2;
}
################################################################################
#	make_cube(). Make a color cube. Actually, it just makes an array with all
#		of the colors left after doing the math.
################################################################################
function make_cube( $gd, $div=255 )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ $this->debug->t( "GD is NULL" ); }
	if( abs($div) > 255 ){ $div = abs( $div % 255 ); }
		else if( abs($div) < 1 ){ $div = 0; }

	$div = abs( $div ) + 1;
	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$colors = [];
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->get_ARGB( $color );
			$r = floor( $r / $div ) * $div;
			$g = floor( $g / $div ) * $div;
			$b = floor( $b / $div ) * $div;
			$color = $this->put_ARGB($a, $r, $g, $b );
			if( isset($colors[$color]) ){ $colors[$color]++; }
				else { $colors[$color] = 1; }
			}
		}

	$this->debug->out();

	return $colors;
}
################################################################################
#	reduce_colors(). Converts an image to only having X number of colors
#	NOTES:	$div MUST BE 0-255.
#		This is like calling make_cube() and then reducing the colors - BUT -
#		you get BOTH a modified GD as well as the list of colors.
################################################################################
function reduce_colors($gd=null, $div=255)
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ $this->debug->t( "GD is NULL" ); }
	if( abs($div) > 255 ){ $div = abs( $div % 255 ); }
		else if( abs($div) < 1 ){ $div = 0; }

	$div = abs( $div ) + 1;

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$colors = [];
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->get_ARGB( $color );
			$r = floor( $r / $div ) * $div;
			$g = floor( $g / $div ) * $div;
			$b = floor( $b / $div ) * $div;

			$color = $this->put_ARGB($a, $r, $g, $b );
			if( isset($colors[$color]) ){ $colors[$color]++; }
				else { $colors[$color] = 1; }

			imagesetpixel( $gd, $x, $y, $color );
			}
		}

	$this->debug->out();

	return array( $gd, $colors );
}
################################################################################
#	trim_left(). Trim the left side of the image based upon the color given.
################################################################################
function trim_left( $gd=null, $color=null, $trim=0 )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) || is_null($color) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$left = null;
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			if( imagecolorat($gd, $x, $y) !== $color ){ $left = $x; break; }
			}

		if( !is_null($left) ){ break; }
		}

	$w = ( $w - ($left + ($trim * 2)) );
	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
	imagecopyresampled( $gd2, $gd, 0, 0, ($left-$trim), 0, $w, $h, $w, $h );
	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	trim_right(). Trim the right side of the image based upon the color given.
################################################################################
function trim_right( $gd=null, $color=null, $trim=0 )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) || is_null($color) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$right = null;
	for( $x=($w-1); $x>-1; $x-- ){
		for( $y=0; $y<$h; $y++ ){
			if( imagecolorat($gd, $x, $y) !== $color ){ $right = $x; break; }
			}

		if( !is_null($right) ){ break; }
		}

	$w = ( $right + ($trim * 2) );
	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
	imagecopyresampled( $gd2, $gd, 0, 0, $trim, 0, $w, $h, $w, $h );
	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	trim_top(). Trim the top side of the image based upon the color given.
################################################################################
function trim_top( $gd=null, $color=null, $trim=0 )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) || is_null($color) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$top = null;
	for( $y=0; $y<$h; $y++ ){
		for( $x=0; $x<$w; $x++ ){
			if( imagecolorat($gd, $x, $y) !== $color ){ $top = $y; break; }
			}

		if( !is_null($top) ){ break; }
		}

	$h = ( $h - ($top + ($trim * 2)) );
	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
	imagecopyresampled( $gd2, $gd, 0, 0, 0, ($top + $trim), $w, $h, $w, $h );
	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	trim_bot(). Trim the bottom side of the image based upon the color given.
################################################################################
function trim_bot( $gd=null, $color=null, $trim=0 )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) || is_null($color) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$bot = null;
	for( $y=($h-1); $y>-1; $y-- ){
		for( $x=0; $x<$w; $x++ ){
			if( imagecolorat($gd, $x, $y) !== $color ){ $bot = $y; break; }
			}

		if( !is_null($bot) ){ break; }
		}

	$h = ($bot - $trim);
	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
	imagecopyresampled( $gd2, $gd, 0, 0, 0, $trim, $w, $h, $w, $h );
	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	trim_image(). Trims all four sides.
################################################################################
function trim_image( $gd=null, $color=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) || is_null($color) ){ return false; }

	$gd = trim_left( $gd, $color );
	$gd = trim_right( $gd, $color );
	$gd = trim_top( $gd, $color );
	$gd = trim_bot( $gd, $color );

	$this->debug->out();
	return $gd;
}
################################################################################
#	copy_some(). Copies part of an image according to the parameters.
################################################################################
function copy_some( $gd=null, $top=null, $left=null, $bot=null, $right=null )
{
	$this->debug->in();

	if( !is_resource($gd) ){ return false; }
	if( is_null($top) ){ return false; }
	if( is_null($bot) ){ return false; }
	if( is_null($left) ){ return false; }
	if( is_null($right) ){ return false; }

	$w = abs( $right - $left );
	$h = abs( $bot - $top );

	$gd2 = imagecreatetruecolor( $w, $h );
	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );
	imagecopyresampled( $gd2, $gd, 0, 0, $left, $top, $w, $h, $w, $h );
	imagedestroy( $gd );

	$this->debug->out();
	return $gd2;
}
################################################################################
#	get_colors(). Get the colors in an image.
################################################################################
function get_colors( $gd=null, $alpha=true )
{
	$this->debug->in();

	if( is_null($gd) ){ $gd = $this->gd; }

	$colors = [];
	$w = imagesx( $gd );
	$h = imagesy( $gd );
	for( $i=0; $i<$w; $i++ ){
		for( $j=0; $j<$h; $j++ ){
			$rgb = imagecolorat( $gd, $i, $j );
#
#	Remove the alpha from the color. IF WE ONLY WWANT THE RGB VALUES.
#	(See $alpha above.)
#
			if( $alpha == false ){
				list( $r, $g, $b ) = $this->get_RGB( $rgb );
				$rgb = $this->put_RGB( $r, $g, $b );
				}

			if( isset($colors[$rgb]) ){ $colors[$rgb]++; }
				else { $colors[$rgb] = 1; }
			}
		}

	$this->debug->out();

	return $colors;
}
################################################################################
#	follow_color(). Follow the color.
################################################################################
function fc( $gd, $color )
{
	$this->debug->in();
	$this->colors = [];
	$this->gd = $gd;

	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$this->transparent = $this->unique_color( $gd );

	for( $i=0; $i<$w; $i++ ){
		for( $j=0; $j<$h; $j++ ){
			$rgb = imagecolorat( $gd, $i, $j );
			if( ($rgb === $this->transparent) === false ){
				if( $rgb === $color ){ $this->fca( $i, $j, $color ); }
				}
			}
		}

	$this->gd = null;
	$this->debug->out();
	return $this->colors;
}
################################################################################
#	fca(). Follow the color.
################################################################################
function fca( $x, $y, $color )
{
	$this->debug->in();

	$gd = $this->gd;
	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$c = count( $this->colors );
	$this->colors[$c][0] = $x;
	$this->colors[$c][1] = $y;
	$transparent = $this->transparent;
	imagesetpixel( $gd, $x, $y, $transparent );

	for( $i=-1; $i<2; $i++ ){
		$nx = $x + $i;
		if( ($nx > -1) && ($nx < $w) ){
			for( $j=-1; $j<2; $j++ ){
				$ny = $w + $j;
				if( ($ny > -1) && ($ny < $w) ){
					$rgb = imagecolorat( $gd, $nx, $ny );
					if( $rgb == $color ){ $this->fca( $nx, $ny, $color ); }
					}
				}
			}
		}

	$this->debug->out();
}
################################################################################
#	split(). Break up an image into it's separate parts.
################################################################################
function split( $gd )
{
	$new_gd = [];
	$new_gd[] = $gd;
}
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", $num );
################################################################################
function dump( $title=null, $arg=null )
{
	$this->debug->in();
	echo "--->Entering DUMP\n";

	if( is_null($title) ){ return false; }
	if( is_null($arg) ){ return false; }

	$title = trim( $title );
#
#	Get the backtrace
#
	$dbg = debug_backtrace();
#
#	Start a loop
#
	foreach( $dbg as $k=>$v ){
		$a = array_pop( $dbg );

		foreach( $a as $k1=>$v1 ){
			if( !isset($a[$k1]) || is_null($a[$k1]) ){ $a[$k1] = "--NULL--"; }
			}

		$func = $a['function'];
		$line = $a['line'];
		$file = $a['file'];
		$class = $a['class'];
		$obj = $a['object'];
		$type = $a['type'];
		$args = $a['args'];

		echo "$k ---> $title in $class$type$func @ Line : $line =\n";
		foreach( $args as $k1=>$v1 ){
			if( is_array($v1) ){
				foreach( $v1 as $k2=>$v2 ){
					echo "	$k " . str_repeat( '=', $k1 + 3 ) ."> " . $title. "[$k1][$k2] = $v2\n";
					}
				}
				else { echo "	$k " . str_repeat( '=', $k1 + 3 ) . "> " . $title . "[$k1] = $v1\n"; }
			}

#		if( is_array($arg) ){ print_r( $arg ); echo "\n"; }
#			else { echo "ARG = $arg\n"; }
		}

	echo "<---Exiting DUMP\n\n";
	$this->debug->out();
	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['rgb']) ){
		$GLOBALS['classes']['rgb'] = new class_rgb();
		}

?>
