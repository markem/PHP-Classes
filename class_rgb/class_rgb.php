<?php

	include_once( "C:/Users/marke/My Programs/PHP/lib/class_debug.php" );
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
#
#END DOC
################################################################################
function class_rgb()
{
	private $debug = null;
	private $colors = null;
	private $gd = null;

################################################################################
#	__construct(). Starts the class.
################################################################################
function __construct()
{
	$args = func_get_args();
	$this->debug = new class_debug( func_get_args() );
	$this->debug->in();
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
function rgb_DIFF( $a=null, $b=null, $diff=25, $roll="rgb" )
{
	$this->debug->in();

	if( is_null($a) || is_null($b) ){ return false; }

	list( $aa, $ar, $ag, $ab ) = $this->get_ARGB( $a );
	list( $ba, $br, $bg, $bb ) = $this->get_ARGB( $b );

	$ca = abs( $aa - $ba );
	$cr = abs( $ar - $br );
	$cg = abs( $ag - $bg );
	$cb = abs( $ab - $bb );

	if( preg_match("/a/i", $roll) && $ca > $diff ){ return true; }
	if( preg_match("/r/i", $roll) && $cr > $diff ){ return true; }
	if( preg_match("/g/i", $roll) && $cg > $diff ){ return true; }
	if( preg_match("/b/i", $roll) && $cb > $diff ){ return true; }

	$this->debug->out();

	return false;
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
						if( ++$b > 255 ){ $this->debug-t( "B > 255" ); }
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
#		If there is - it returns TRUE, else FALSE.
################################################################################
function is_trans( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ return false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$color = imagecolorat( $gd, $x, $y );
			list( $a, $r, $g, $b ) = $this->get_ARGB( $color );
			if( ($a > 0) && ($a < 128) ){
				$this->debug->out();
				return true;
				}
			}
		}

	$this->debug->out();

	return false;
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
#	get_colors(). Get the colors in an image.
################################################################################
function get_colors( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) || !is_resource($gd) ){ return false; }

	$colors = [];
	$w = imagesx( $gd );
	$h = imagesy( $gd );
	for( $i=0; $i<$w; $i++ ){
		for( $j=0; $j<$h; $j++ ){
			$rgb = imagecolorat( $gd, $i, $j );

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
	for( $i=0; $i<$w; $i++ ){
		for( $j=0; $j<$h; $j++ ){
			$rgb = imagecolorat( $gd, $i, $j );
			if( $rgb === $color ){ $this->fca( $i, $j, $color ); }
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
	$blank = imagecolorat( $gd, 0, 0 );
	imagesetpixel( $gd, $x, $y, $blank );

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

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['rgb']) ){ $GLOBALS['classes']['rgb'] = new class_rgb(); }

?>
