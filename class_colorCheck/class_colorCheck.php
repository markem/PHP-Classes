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

	ini_set( 'memory_limit', -1 );
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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_colorCheck();
#
#-Description:
#
#	Taken from :
#
#		https://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
#
#	User information : EllisGL
#	Posted : 16 years ago
#
#	This is a compilation of various color tests talked about by Andreas Gohr.
#	I've included my standard stuff to make this a really nice class.
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
#	Mark Manning			Simulacron I			Thu 01/23/2025  0:29:28.82
#		Originially taken from
#
#		https://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
#
#		Originial author information:
#
#			User information : EllisGL
#			Posted : 16 years ago
#
#	Found this on the above website. It looks very useful but I am going to
#	convert it to how I do things. :-)
#
#END DOC
################################################################################
class class_colorCheck
{

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['colorCheck']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['colorCheck']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	return true;
}
################################################################################
#	colorDiff(). Calculates the color difference between two colors
################################################################################
public function colorDiff( $c1=null, $c2=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }
	if( is_null($c2) ){ die("***** ERROR : Color #2 is NULL" ); }

	$a1 = ($c1 >> 24) & 0xff;
	$r1 = ($c1 >> 16) & 0xff;
	$g1 = ($c1 >> 8) & 0xff;
	$b1 = ($c1 & 0xff);

	$a2 = ($c2 >> 24) & 0xff;
	$r2 = ($c2 >> 16) & 0xff;
	$g2 = ($c2 >> 8) & 0xff;
	$b2 = ($c2 & 0xff);

	return max( $r1, $r2 ) - min( $r1, $r2 ) +
		max( $g1, $g2 ) - min( $g1, $g2 ) +
		max( $b1, $b2 ) - min( $b1, $b2 );
}
################################################################################
#	crisp(). Makes the colors crisper for me for book MTG Accounting
################################################################################
function crisp( $color=null )
{
	if( is_null($color) ){ die("***** ERROR : COLOR is NULL"); }
#	echo "COLOR = " . bin2hex($color) . "\n";
#
#	Get the color's parts
#
	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = ($color & 0xff);
#
#	Did we get a transparent color?
#
	if( $a > 0 ){ return $color; }
	echo __LINE__ . " : A = $a, R = $r, G = $g, B = $b\n";
#
#	Last but not least - is this color darker than the middle grey color?
#
	$diff = 128;
	if( ($r < $diff) && ($g < $diff) && ($b < $diff) ){
		$r = 255;  $g = 0; $b = 0;
#		echo __LINE__ . " : A = $a, R = $r, G = $g, B = $b\n";
		}

	$r = 255;  $g = 0; $b = 0;
	$color = (($a & 0xff) << 24);
	$color += (($r & 0xff) << 16);
	$color += (($g & 0xff) << 8);
	$color += ($b & 0xff);

	echo __LINE__ . " : A = $a, R = $r, G = $g, B = $b\n";

	return( $color );
}
################################################################################
#	calcDiff(). Calculate the difference. Returns an array of the differences
#		between the two points. POSITIVE numbers means the FIRST color is
#		HIGHER (ie: More towards 255) and a negative number means it is more
#		towards a LOWER amount (ie: More towards 0).
################################################################################
function calcDiff( $c1=null, $c2=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }
	if( is_null($c2) ){ die("***** ERROR : Color #2 is NULL" ); }

	$a1 = ($c1 >> 24) & 0xff;
	$r1 = ($c1 >> 16) & 0xff;
	$g1 = ($c1 >> 8) & 0xff;
	$b1 = ($c1 & 0xff);

	$a2 = ($c2 >> 24) & 0xff;
	$r2 = ($c2 >> 16) & 0xff;
	$g2 = ($c2 >> 8) & 0xff;
	$b2 = ($c2 & 0xff);

	return array( ($a1 - $a2), ($r1 - $r2), ($g1 - $g2), ($b1 - $b2) );
}
################################################################################
#	brightDiff(). Calculates the brightness using two points.
################################################################################
public function brightDiff( $c1=null, $c2=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }
	if( is_null($c2) ){ die("***** ERROR : Color #2 is NULL" ); }

	$a1 = ($c1 >> 24) & 0xff;
	$r1 = ($c1 >> 16) & 0xff;
	$g1 = ($c1 >> 8) & 0xff;
	$b1 = ($c1 & 0xff);

	$a2 = ($c2 >> 24) & 0xff;
	$r2 = ($c2 >> 16) & 0xff;
	$g2 = ($c2 >> 8) & 0xff;
	$b2 = ($c2 & 0xff);

	$br1 = ( (299.0 * $r1) + (587.0 * $g1) + (114.0 * $b1) ) / 1000.0;
	$br2 = ( (299.0 * $r2) + (587.0 * $g2) + (114.0 * $b2) ) / 1000.0;

	return abs( $br1-$bR2 );
}
################################################################################
#	brightOne(). Calculates the brightness using two points.
################################################################################
public function brightOne( $c=null )
{
	if( is_null($c) ){ die("***** ERROR : Color #1 is NULL" ); }

	$a = ($c >> 24) & 0xff;
	$r = ($c >> 16) & 0xff;
	$g = ($c >> 8) & 0xff;
	$b = ($c & 0xff);

	$br1 = ( (299.0 * $r) + (587.0 * $g) + (114.0 * $b) ) / 1000.0;

	return $br1;
}
################################################################################
#	lumDiff(). Calculates the luminous value using two points.
################################################################################
public function lumDiff( $c1=null, $c2=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }
	if( is_null($c2) ){ die("***** ERROR : Color #2 is NULL" ); }

	$a1 = ($c1 >> 24) & 0xff;
	$r1 = ($c1 >> 16) & 0xff;
	$g1 = ($c1 >> 8) & 0xff;
	$b1 = ($c1 & 0xff);

	$a2 = ($c2 >> 24) & 0xff;
	$r2 = ($c2 >> 16) & 0xff;
	$g2 = ($c2 >> 8) & 0xff;
	$b2 = ($c2 & 0xff);

	$l1 = 0.2126 * pow( $r1/ 255.0, 2.2 ) +
		0.7152 * pow( $g1 / 255.0, 2.2 ) +
		0.0722 * pow( $b1 / 255.0, 2.2 );

	$l2 = 0.2126 * pow( $r2/ 255.0, 2.2 ) +
		0.7152 * pow( $g2 / 255.0, 2.2 ) +
		0.0722 * pow( $b2 / 255.0, 2.2 );

	if( $l1 > $l2 ){ return ( $l1 + 0.05 ) / ( $l2 + 0.05 ); }
		else { return ( $l2 + 0.05 ) / ( $l1 + 0.05 ); }
}
################################################################################
#	lumOne(). Calculates the luminous value using two points.
################################################################################
public function lumOne( $c1=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }

	$a = ($c >> 24) & 0xff;
	$r = ($c >> 16) & 0xff;
	$g = ($c >> 8) & 0xff;
	$b = ($c & 0xff);


	$l1 = 0.2126 * pow( $r/ 255.0, 2.2 ) +
		0.7152 * pow( $g / 255.0, 2.2 ) +
		0.0722 * pow( $b / 255.0, 2.2 );

	return $l1;
}
################################################################################
#	pythDiff(). Calculates the Pythagorean differences using two points
################################################################################
public function pythDiff( $c1=null, $c2=null )
{
	if( is_null($c1) ){ die("***** ERROR : Color #1 is NULL" ); }
	if( is_null($c2) ){ die("***** ERROR : Color #2 is NULL" ); }

	$a1 = ($c1 >> 24) & 0xff;
	$r1 = ($c1 >> 16) & 0xff;
	$g1 = ($c1 >> 8) & 0xff;
	$b1 = ($c1 & 0xff);

	$a2 = ($c2 >> 24) & 0xff;
	$r2 = ($c2 >> 16) & 0xff;
	$g2 = ($c2 >> 8) & 0xff;
	$b2 = ($c2 & 0xff);

	$rd = $r1 - $r2;
	$gd = $g1 - $g2;
	$bd = $b1 - $b2;

	return sqrt( ($rd * $rd) + ($gd * $gd) + ($bd * $bd) ) ;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['colorCheck']) ){
		$GLOBALS['classes']['colorCheck'] = new class_colorCheck();
		}

?>

