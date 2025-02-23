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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_svg();
#
#-Description:
#
#	class_svg
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
#	Mark Manning			Simulacron I			Fri 12/22/2023 13:34:06.02 
#		Original Program.
#
#	Mark Manning			Simulacron I			Fri 12/22/2023 14:22:44.97 
#	---------------------------------------------------------------------------
#		REMEMBER! ALL COMMANDS USE SINGLE QUOTES and not double quotes to
#		encapsulate all commands. YOU HAVE BEEN WARNED!!!
#
#	Mark Manning			Simulacron I			Fri 12/22/2023 14:36:45.16 
#	---------------------------------------------------------------------------
#		A lot of the additional options information comes from the W3School
#		wesite.
#
#	Mark Manning			Simulacron I			Sat 07/17/2021 14:56:52.53 
#	---------------------------------------------------------------------------
#		REMEMBER! We are now following the PHP code of NOT killing the program
#		but instead always setting a DEBUG MESSAGE and returning FALSE. So I'm
#		getting rid of all of the DIE() calls.
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
#		CLASS_SVG.PHP. A class to handle working with svg.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_svg
{
	private $html = null;
	private $svg_start = null;
	private $svg_end = null;
	private $cmd_list = null;
	private $tabs = null;

################################################################################
#	__construct(). Constructor.
################################################################################
public function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['svg']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['svg']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
public function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->html = null;
	$this->svg_start = [];
	$this->svg_end = [];
	$this->cmd_list = [];
	$this->tabs = 0;

	$this->debug->out();
	return true;
}
################################################################################
#	open(). Start up the svg are with the width and height. Use an associative
#	array to send over the "option"="value".
#
#		<svg>	Creates an SVG document fragment
#		x="top left corner when embedded (default 0)"
#		y="top left corner when embedded (default 0)"
#		width="the width of the svg fragment (default 100%)"
#		height="the height of the svg fragment (default 100%)"
#		viewBox="the points "seen" in this SVG drawing
#			area. 4 values separated by white space or commas. (min x,
#			min y, width, height)"
#		preserveAspectRatio="'none' or any of the 9
#			combinations of 'xVALYVAL' where VAL is 'min', 'mid' or
#			'max'. (default xMidYMid)"
#		zoomAndPan="'magnify' or 'disable'. Magnify option
#			allows users to pan and zoom your file (default magnify)"
#		xml="outermost <svg> element needs to setup SVG
#			and its namespace: xmlns="http://www.w3.org/2000/svg"
#			xmlns:xlink="http://www.w3.org/1999/xlink"
#			xml:space="preserve""
#		
#		+ presentation attributes:
#			All
################################################################################
public function open()
{
	$this->debug->in();

	$this->def( func_get_args(), "svg", false );

	$this->debug->out();
	return true;
}
################################################################################
#	def(). The default way to make a new SVG command.
################################################################################
private function def( $args=null, $cmd=null, $end=null )
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$s = "";
	foreach( $args as $k=>$v ){
		if( is_null($width) ){ die("***** ERROR : Width is NULL" ); }
		if( is_null($height) ){ die("***** ERROR : Height is NULL" ); }
		$s .= "$k='$v' ";
		}

	if( true ){ $this->dump( __FUNCTION__ . " Command", $args ); }
#
#	If we are making only THE cOMMAND - then just use the "/>" part - BUT
#	if not - then add the closing command to the svg_end array.
#		REMEMBER! The SVG_END array is BACKWARDS to how you need to add
#		the commands. (So <svg....> has a <svg /> ending tag.
#
	if( $end ){ $end = "/>"; }
		else {
			$end = ">";
			$this->tabs++;
			$this->svg_start[] = $cmd;
			$this->svg_end[] = $cmd;
			}

	$this->html .= str_repeat("	", $this->tabs) . "<$cmd $s$end\n";
	$this->cmd_list[] = $cmd;

	if( $end ){ $this->tabs--; }

	$this->debug->out();
	return true;
}
################################################################################
#	circle(). Creates a circle. The best way to do this is to send over an
#			associative array with "option"="value".
#	Notes:	cx="the x-axis center of the circle"
#			cy="the y-axis center of the circle"
#			r="The circle's radius". Required.
#			Color=
#			FillStroke=
#			Graphics=
################################################################################
public function circle()
{
	$this->debug->in();

	$this->def( func_get_args(), "circle", true );

	$this->debug->out();
	return true;
}
################################################################################
#	rect(). Create a rectangular area. Use associative arrays for options.
#	<rect>	x="the x-axis top-left corner of the rectangle"
#			y="the y-axis top-left corner of the rectangle"
#			rx="the x-axis radius (to round the element)"
#			ry="the y-axis radius (to round the element)"
#			width="the width of the rectangle". Required.
#			height="the height of the rectangle" Required.
#	
#			+ presentation attributes:
#			Color, FillStroke, Graphics
################################################################################
public function rect()
{
	$this->debug->in();

	$this->def( func_get_args(), "rect", true );

	$this->debug->out();
	return true;
}
################################################################################
#	ellipse(). Create an ellipse.
#		cx="the x-axis center of the ellipse"
#		cy="the y-axis center of the ellipse"
#		rx="the length of the ellipse's radius along the x-axis". Required.
#		ry="the length of the ellipse's radius along the y-axis". Required.
#	
#		+ presentation attributes:
#			Color, FillStroke, Graphics
################################################################################
public function ellipse()
{
	$this->debug->in();

	$this->def( func_get_args(), "ellipse", true );

	$this->debug->out();
	return true;
}
################################################################################
#	line(). Draw a line
#		x1="the x start point of the line"
#		y1="the y start point of the line"
#		x2="the x end point of the line"
#		y2="the y end point of the line"
#	
#		+ presentation attributes:
#			Color, FillStroke, Graphics, Markers
################################################################################
public function line()
{
	$this->debug->in();

	$this->def( func_get_args(), "line", true );

	$this->debug->out();
	return true;
}
################################################################################
#	polygon(). Create a polygon.
#		points="the points of the polygon. The total number of points must be even". Required.
#		fill-rule="part of the FillStroke presentation attributes"
#
#		+ presentation attributes:
#			Color, FillStroke, Graphics, Markers
################################################################################
public function polygon()
{
	$this->debug->in();

	$this->def( func_get_args(), "polygon", true );

	$this->debug->out();
	return true;
}
################################################################################
#	polyline(). Create a line from a set of points.
#		points="the points on the polyline". Required.
#
#		+ presentation attributes:
#			Color, FillStroke, Graphics, Markers
################################################################################
public function polyline()
{
	$this->debug->in();

	$this->def( func_get_args(), "polyline", true );

	$this->debug->out();
	return true;
}
################################################################################
#	path(). Create a path which, when closed, forms a polygon.
#		d="a set of commands which define the path"
#		pathLength="If present, the path will
#			be scaled so that the computed path length of the points
#			equals this value"
#
#		transform="a list of transformations"
#
#		+ presentation attributes:
#			Color, FillStroke, Graphics, Markers
#
#		The following commands are available for path data:
#
#			M = moveto
#			L = lineto
#			H = horizontal lineto
#			V = vertical lineto
#			C = curveto
#			S = smooth curveto
#			Q = quadratic Bézier curve
#			T = smooth quadratic Bézier curveto
#			A = elliptical Arc
#			Z = closepath
#
#			<path d="M150 0 L75 200 L225 200 Z" />
#			
################################################################################
public function path()
{
	$this->debug->in();

	$this->def( func_get_args(), "path", true );

	$this->debug->out();
	return true;
}
################################################################################
#	text(). Show some text
#		x="a list of x-axis positions. The
#			nth x-axis position is given to the nth character in
#			the text. If there are additional characters after
#			the positions run out they are placed after the last
#			character. 0 is default"
#
#		y="a list of y-axis positions. (see x). 0 is default"
#
#		dx="a list of lengths which moves the
#			characters relative to the absolute position of the last
#			glyph drawn. (see x)"
#
#		dy="a list of lengths which moves the
#			characters relative to the absolute position of the last
#			glyph drawn. (see x)"
#
#		rotate="a list of rotations. The nth
#			rotation is performed on the nth character. Additional
#			characters are NOT given the last rotation value"
#
#		textLength="a target length for the
#			text that the SVG viewer will attempt to display
#			the text between by adjusting the spacing and/or the
#			glyphs. (default: The text's normal length)"
#
#		lengthAdjust="tells the viewer what to
#			adjust to try to accomplish rendering the text if the
#			length is specified. The two values are 'spacing' and
#			'spacingAndGlyphs'"
#
#		+ presentation attributes:
#			Color, FillStroke, Graphics, FontSpecification, TextContentElements
################################################################################
public function text()
{
	$this->debug->in();

	$this->def( func_get_args(), "text", true );

	$this->debug->out();
	return true;
}
################################################################################
#	filter(). Create a filter to be used. THIS ROUTINE ALSO INCLUDES A
#		call to make a DEFS command.
################################################################################
public function filter()
{
	$this->debug->in();

	$this->def( func_get_args(), "defs", false );
	$this->def( func_get_args(), "filter", false );

	$this->debug->out();
	return true;
}
################################################################################
#	pop(). POP off an entry in the SVG_END list.
################################################################################
public function pop()
{
	$this->debug->in();

	$end = array_pop( $this->svg_end );
	while( ($cmd = array_pop($this->cmd_list)) ){
		$this->html .= array_pop( $this->svg_end );
		}

	$this->debug->out();
	return true;
}
################################################################################
#	stroke(). Build the various STROKE commands and return them.
################################################################################
public function stroke( $color=null, $width=null, $linecap=null, $dasharray=null )
{
	$this->debug->in();

	$s = "";
	if( !is_null($color) ){ $s .= "stroke='$color' "; }
	if( !is_null($width) ){ $s .= "stroke-width='$width' "; }
	if( !is_null($linecap) ){ $s .= "stroke-linecap='$linecap' "; }
	if( !is_null($dasharray) ){ $s .= "stroke-dasharray='$dasharray' "; }


	$this->debug->out();
	return $s;
}
################################################################################
#	end(). This pops the svg_start and svg_end, compares them, and if
#		svg_start does not equal svg_end - continues to pop the svg_start
#		until the svg_start and svg_end match. Then the svg_end name is
#		added onto the $this->html as "<XXXXX />".
################################################################################
public function end()
{
	$this->debug->in();

	$s = array_pop( $this->svg_start );
	$e = array_pop( $this->svg_end );

	while( !($s === $e) && (count($svg_start) > 1) ){
		$s = array_pop( $this->svg_start );
		}

	$this->html .= str_repeat("	", $this->tabs) . "</$e>\n";
	$this->tabs--;

	$this->debug->out();
	return true;
}
################################################################################
#	blur(). Create a Gaussian blur command.
################################################################################
public function blur( $dev=null )
{
	$this->debug->in();

	if( is_null($dev) ){ $dev = 15; }

	$this->html .= str_repeat("	", $this->tabs) .
		"<feGaussianBlur in='SourceGraphic' stdDeviation='$dev' />\n";

	$this->debug->out();
	return true;
}
################################################################################
#	ds(). DropShadow function.
#	NOTE:	Send an associative array with all of the options in it with the
#		values.
################################################################################
public function ds()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$result = null;
	$in = $in2 = null;
	$dx = null;
	$dy = null;
	$mode = null;
	if( isset($args['result']) ){ $result = $args['result']; }
		else { $result = "offOut"; }

	if( isset($args['in']) ){ $in = $args['in']; $in2 = $in; }
		else { $in = "SourceGraphic";  $in2 = $in; }

	if( isset($args['dx']) ){ $dx = $args['dx']; }
		else { $dx = 20;  }

	if( isset($args['dy']) ){ $dy = $args['dy']; }
		else { $dy = 20;  }

	if( isset($args['mode']) ){ $mode = $args['mode']; }
		else { $mode = "normal";  }

	$this->html .= str_repeat("	", $this->tabs) .
		"<feOffset result='$result' in='SourceGraphic' dx='$dx' dy='$dy' />\n";

	$this->html .= str_repeat("	", $this->tabs) .
		"<feBlend in='SourceGraphic', in2='offOut' mode='$mode' />\n";

	$this->debug->out();
	return true;
}
################################################################################
#	linear(). Do a linear gradiant.
#	NOTE:	Because we are doing the start and stop, you need to make an
#		associative array with subarrays labeled one(1) thru X. Like this:
#
#		$a = [];
#		$a[1] = [];
#		$a[1]["offset"] = "0%";
#		etc....
################################################################################
public function linear()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	if( isset($args['id']) ){ $id = $args['id']; }
		else { $id = "linearGrad"; }

	if( isset($args['x1']) ){ $x1 = $args['xq']; }
		else { $x1 = "0%"; }

	if( isset($args['x2']) ){ $x2 = $args['x2']; }
		else { $x2 = "100%"; }

	if( isset($args['y1']) ){ $y1 = $args['y1']; }
		else { $y1 = "0%"; }

	if( isset($args['y2']) ){ $y2 = $args['y2']; }
		else { $y2 = "100%"; }

	$s = "<linearGradient id='$id' x1='$x1' y1='$y1' x2='$x2' y2='$y2'>\n";

	foreach( $args as $k=>$v ){
		if( is_numeric($k) ){
			foreach( $v as $k1=>$v1 ){ $stop[$k][$k1] = $v1; }
			$s .= "<stop offset='" . $v['offset'] . " style='" . $v['style'] . "' />\n";
			}
		}

	$s .= "</linearGradient>\n";
	$this->html .= $s;

	$this->debug->out();
	return $s;
}
################################################################################
#	radial(). Like the linear function - you send everything like you do for
#		that function.
################################################################################
public function radial()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	if( isset($args['id']) ){ $id = $args['id']; }
		else { $id = "linearGrad"; }

	if( isset($args['cx']) ){ $cx = $args['xq']; }
		else { $cx = "50%"; }

	if( isset($args['cy']) ){ $cy = $args['cy']; }
		else { $cy = "50%"; }

	if( isset($args['r']) ){ $r = $args['r']; }
		else { $r = "50%"; }

	if( isset($args['fx']) ){ $fx = $args['fx']; }
		else { $fx = "50%"; }

	if( isset($args['fy']) ){ $fy = $args['fy']; }
		else { $fy = "50%"; }

	$s = "<radialGradient id='$id' cx='$cx' cy='$cy' r='$r' fx='$fx' fy='$fy'>\n";

	foreach( $args as $k=>$v ){
		if( is_numeric($k) ){
			foreach( $v as $k1=>$v1 ){ $stop[$k][$k1] = $v1; }
			$s .= "<stop offset='" . $v['offset'] . " style='" . $v['style'] . "' />\n";
			}
		}

	$s .= "</radialGradient>\n";
	$this->html .= $s;

	$this->debug->out();
	return $s;
}
################################################################################
#	print(). Print out the HTML.
################################################################################
public function print()
{
	$this->debug->in();

	print_r( $this->html );

	$this->debug->out();
	return true;
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
	if( !isset($GLOBALS['classes']['svg']) ){
		$GLOBALS['classes']['svg'] = new class_svg();
		}

?>
