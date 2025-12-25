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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_graphs();
#
#-Description:
#
#	A class to create graphs. I have tried six or seven other packages but
#	none of them work or are very hard to set up. I wanted a program that
#	would work without all of the hassles.
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
#	Mark Manning			Simulacron I			Sun 01/10/2021  0:16:17.68 
#		Original Program.
#
#	Mark Manning			Simulacron I			Tue 01/19/2021 10:06:08.97 
#	---------------------------------------------------------------------------
#	REMEMBER! Everything here is an ARRAY. So the data for the points is an
#	ARRAY. All of the colors are COLORS['data'] for each of the sets of data.
#	And so forth.
#
#	Also! This class ONLY HANDLES ONE GRAPH. YOU must make a new graph each time.
#	BUT! You CAN have more than one item in a graph (like multiple bar graphs).
#
#	Mark Manning			Simulacron I			Sat 02/20/2021 12:13:31.67 
#	---------------------------------------------------------------------------
#	So everything is an array. This means that you can do TWO different things.
#	The first is to make column ZERO have the two labels (X&Y) -OR- you can
#	just have the data in an array and then PROVIDE the labels to use.
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
#		CLASS_GRAPHS.PHP. A class to handle working with graphs.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD-3-Patent License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_graphs
{
	private	$gd = null;
	private $gd_cnt = null;
	private $width = null;		#	Width of graph
	private $height = null;		#	Height of graph
	private $data = null;		#	All of the data for the graph
	private	$titles = null;		#	All of the graph's titles
	private $labels = null;		#	Subtitles labels for the graph
	private $min_x = null;
	private $max_x = null;
	private $min_y = null;
	private $max_y = null;
	private $scale_x = null;	#	How to apply a scale. I.E.: (Max-Min+20)/scale
	private $scale_y = null;	#	How to apply a scale. I.E.: (Max-Min+20)/scale
	private $div_x = null;		#	How to divide up each section - X
	private $div_y = null;		#	How to divide up each section - Y
	private $colors = null;		#	All of the colors for the graph.

################################################################################
#	__construct(). Constructor.
################################################################################
public function __construct()
{
	if( !isset($GLOBALS['class']['graphs']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['graphs']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
#
#	What you can send:
#
#	min-x, max-x, min-y, max-y, scale-x, scale-y, div-x, div-y
#
################################################################################
public function init( $min_x=null, $max_x=null, $min_y=null, $max_y=null,
	$scale_x=null, $scale_y=null, $div_x=null, $div_y=null )
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

#
#	Default items
#
	if( is_null($min_x) ){ $min_x = -100; }
	if( is_null($max_x) ){ $max_x = 100; }
	if( is_null($min_y) ){ $min_y = -100; }
	if( is_null($max_y) ){ $max_y = 100; }
	if( is_null($scale_x) ){ $scale_x = 1; }
	if( is_null($scale_y) ){ $scale_y = 1; }
	if( is_null($div_x) ){ $div_x = 1; }
	if( is_null($div_y) ){ $div_y = 1; }

	$this->gd = null;
	$this->gd_cnt = -1;
	$this->width = null;
	$this->height = null;
	$this->data = [];
	$this->titles = [];
	$this->labels = [];
	$this->name = [];
	$this->colors = [];
	$this->min_x = $min_x;
	$this->max_x = $max_x;
	$this->min_y = $min_y;
	$this->max_y = $max_y;
	$this->scale_x = $scale_x;
	$this->scale_y = $scale_y;
	$this->div_x = $div_x;
	$this->div_y = $div_y;
}
################################################################################
#	data(). Data MUST BE AN ARRAY. Data used to plot graph. If you leave the
#		$TITLES part off, then that means the titles are in the $DATA
#		variable (locations [1...N][0] or [0][1...N].
#
#	NOTE - location [0,0] IS NOT USED IF YOU HAVE TITLES.
#
#	Titles. If you send over the titles - then the first row is the Y axis and
#		the second row is the X axis. You can override this by passing in
#		"X" and "Y" or 0 and 1 if you want to do so.
################################################################################
public function data( $data=null, $titles=null )
{
	if( is_null($data) ){ die( "No data given" ); }
	if( is_null($titles) ){
		$yc = count( $data );		#	How many DOWN titles
		$xc = count( $data[0] );	#	How many ACROSS titles
		for( $i=1; $i<$xc; $i++ ){ $this->titles[0][$i] = $data[0][$i]; }
		for( $i=1; $i<$yc; $i++ ){ $this->titles[1][$i] = $data[$i][0]; }
#
#	Now get rid of the titles
#
		for( $i=1; $i<$xc; $i++ ){
			for( $j=1; $j<$yc; $j++ ){
				$this->data[$i-1][$j-1] = $data[$i][$j];
				}
			}
		}
		else {
			foreach( $titles as $k=>$v ){
				foreach( $v as $k1=>$v1 ){
					if( preg_match("/x|0/i", $k) ){ $this->titles[0][$k1] = $v1; }
						else if( preg_match("/y|1/i", $k) ){ $this->titles[1][$k1] = $v1; }
					}
				}

			foreach( $data as $k=>$v ){
				foreach( $v as $k1=>$v1 ){
					$this->data[$k][$k1] = $v1;
					}
				}
			}
}
################################################################################
#	start().
################################################################################
public function start()
{
#
#	It is important that we increment gd_cnt as that is how we tell where we are.
#
	$this->gd_cnt++;

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Ok. If there are no arguments passed in AND there is no WIDTH or HEIGHT
#	THEN - we just return. We MUST have a WIDTH and HEIGHT or we can't create
#	the graphical image.
#
	if( is_null($args) || (count($args) < 1) &&
		is_null($this->width) && is_null($this->height) ){ return false; }


	foreach( $args as $k=>$v ){
#
#	$cg->start( array( "w"=>5, "h"=>5, "n"=>"5" ) );
#
		if( is_array($v) ){
			foreach( $v as $k1=>$v1 ){
				if( preg_match("/w(idth)*/i", $k1) ){ $this->width = $v1; }
					else if( preg_match("/h(eight)*/i", $k1) ){ $this->height = $v1; }
					else if( preg_match("/t(itle)*/i", $k1) ){ $this->title[$this->gd_cnt] = $v1; }
				}
			}
#
#	$cg->start( "w"=>5, "h"=>5, "n"=>"5" );
#
			else if( preg_match("/w(idth)*/i", $k) ){ $this->width = $v; }
			else if( preg_match("/h(eight)*/i", $k) ){ $this->height = $v; }
			else if( preg_match("/t(itle)*/i", $k) ){ $this->title[$this->gd_cnt] = $v; }
#
#	$cg->start( "w=5", "h=5", "n=5" );
#
			else if( preg_match("/w(idth)*=/i", $v) ){
				$this->width = substr( $v, 2, strlen($v) );
				}
			else if( preg_match("/h(eight)*=/i", $v) ){
				$this->height = substr( $v, 2, strlen($v) );
				}
			else if( preg_match("/t(itle)*=/i", $v) ){
				$this->title[$this->gd_cnt] = substr( $v, 2, strlen($v) );
				}
#
#	$cg->start( 5,5,"5" );
#
			else if( ($k == 0) && is_numeric($v) ){ $this->width = $v; }
			else if( ($k == 1) && is_numeric($v) ){ $this->height = $v; }
			else if( ($k == 2) && is_string($v) ){ $this->title[$this->gd_cnt] = $v; }
		}

	if( is_null($this->title[$this->gd_cnt]) ){ $this->title[$this->gd_cnt] = $this->gd_cnt; }
	if( !is_null($this->width) && !is_null($this->height) ){
		$this->gd = imagecreatetruecolor( $this->width, $this->height );
		}
#
#	REMEMBER! THIS DOES NOT MEAN THAT GD IS NULL. It means GD IS NULL.
#	There is a difference!
#
		else { $this->gd = null; }

	return $this->title[$this->gd_cnt];
}
################################################################################
#	add(). Add data to the graph.
#	Example:	$cg->add( array(1,2,3,4,...) );
#				$cg->add( array("a"=>1, "b"=>2, "c"=>3,...) );
#				$cg->add( array( "today"=>1, "tomorrow"=>2, "another day"=>3,...) );
#
#				$a = array( "1/1/20"=>5000, "1/2/20"=>4000, "1/3/20"=>1,...);
#				$cg->add( $a );
################################################################################
public function add()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Have they not called start() yet? If not - make it so.
#
	$name = null;
	if( is_null($this->gd) ){ $name = $this->start(); }
#
#	If we called START() but got a false return, then that means
#	we have no idea how large the graphica image should be. So now
#	we turn to our incoming arguments and look if we can figure out
#	how big the image should be.
#
	if( $name === false ){
		$high = -99999;
		$low = 99999;
		$cnt = 0;
		foreach( $args as $k=>$v ){
			if( is_array($v) ){
				foreach( $v as $k1=>$v1 ){
					if( is_numeric($v1) ){
						if( $v1 > $high ){ $high = $v1; }
							else if( $v1 < $low ){ $low = $v1; }
						}
					}
				}
#
#	If the incoming information is NOT an array, then we have to check
#	to see if it is numeric. If so - again - see about setting the high
#	and low values.
#
			else if( is_numeric($v) ){
				if( $v > $high ){ $high = $v; }
					else if( $v < $low ){ $low = $v; }
				}
#
#	Ok. It is not an array and it is not numeric. In this case
#	we are really lost. The most we can do is to change the characters
#	that have been sent over into a hexadecimal value and store that.
#	IN THIS CASE - we always put a "0x" in front of the value and take it
#	that something like "ABCD" would become whatever the hex value is
#	and THAT becomes the high-low values.
			else { $cnt++; }
			}
		}
#
#	Ok. The ARGS variable now has all of the data sent here.
#	First - See if it is just NULL.
#
	if( is_null($args) ){ return false; }
#
#	Move the data over. This will do arrays or single data points.
#
	$cnt = 0;
	foreach( $args as $k=>$v ){
		if( !is_array($v) ){ $this->data[$name] = $v; }
			else { $this->data[$cnt++] = $v; }
		}
print_r( $this->data ); echo "\n";exit;

	return true;
}
################################################################################
#	title(). Put a title on the graph.
#	Note:	You send over the title, the location, and the direction.
#
#		title = "abc...";	#	The TITLE itself
#		loc = u(p), b(ottom), l(eft), r(ight), c(enter) #	where the
#			title is located
#		dir = u(p), b(ottom), l(eft), r(ight) #	Where the bottom of the letters
#			are located. (So UP means the BOTTOM of the letter points UP.)
################################################################################
function title( $title=null, $loc=null, $dir=null )
{
	if( is_null($loc) ){ $loc = 't'; }
	if( is_null($dir) ){ $dir = 'b'; }

	$this->title = [];
	$this->title['label'] = $title;
	$this->title['loc'] = substr( $loc, 0, 1 );
	$this->title['dir'] = substr( $dir, 0, 1 );

	return true;
}
################################################################################
#	subtitles(). Subtitles for the graph.
#	Example: $cg->subtitles( <LABEL>, <DIRECTION> );
#
#	Notes:	<LABEL> = Placement of a label [t(op), b(ot), l(eft), r(ight)]
#			<DIRECTION> = Where the bottom of the letters are.
#				U(P) = Bottom to the top (upside down)
#				D(OWN) = Bottom to the bottom (right-side up)
#				L(EFT) = Bottom to the left
#				R(IGHT) = Bottom to the right
#
#	Mark Manning			Simulacron I			Tue 01/19/2021 10:34:59.26 
#	---------------------------------------------------------------------------
#	Using "l_" for labels and "d_" for directions.
#
################################################################################
function subtitles( $labels=null, $dirs=null )
{
	$flag = false;
	$this->labels = [];

	if( isset($labels['t']) ){ $this->labels['l_t'] = substr( $labels['t'], 0, 1 ); }
		else { $this->labels['l_t'] = ""; $flag = true; }

	if( isset($labels['b']) ){ $this->labels['l_b'] = substr( $labels['b'], 0, 1 ); }
		else { $this->labels['l_b'] = ""; $flag = true; }

	if( isset($labels['l']) ){ $this->labels['l_l'] = substr( $labels['l'], 0, 1 ); }
		else { $this->labels['l_l'] = ""; $flag = true; }

	if( isset($labels['r']) ){ $this->labels['l_r'] = substr( $labels['r'], 0, 1 ); }
		else { $this->labels['l_r'] = ""; $flag = true; }

	if( isset($dirs['t']) ){ $this->sub_dirs['d_t'] = substr( $dirs['t'], 0, 1 ); }
		else { $this->sub_dirs['d_t'] = ""; $flag = true; }

	if( isset($dirs['b']) ){ $this->sub_dirs['d_b'] = substr( $dirs['b'], 0, 1 ); }
		else { $this->sub_dirs['d_b'] = ""; $flag = true; }

	if( isset($dirs['l']) ){ $this->sub_dirs['d_l'] = substr( $dirs['l'], 0, 1 ); }
		else { $this->sub_dirs['d_l'] = ""; $flag = true; }

	if( isset($dirs['r']) ){ $this->sub_dirs['d_r'] = substr( $dirs['r'], 0, 1 ); }
		else { $this->sub_dirs['d_r'] = ""; $flag = true; }

	if( is_null($labels) ){ $this->labels['l_t'] = ""; }
	if( is_null($dirs) ){ $this->labels['d_t'] = 'u'; }

	return $flag;
}
################################################################################
#	colors(). Set the colors for something.
#
#	fgc	=	Foreground color given as ARGB (Alpha, Red, Green, Blue)
#	bgc	=	Background color given as ARGB (Alpha, Red, Green, Blue)
#
#	Note:	If you send over an array, it MUST BE in ARGB format.
################################################################################
private function colors( $fgc=null, $bgc=null, $opt=null )
{
	if( is_null($opt) ){
		$cnt = count( $this->title ) - 1;
		}

	$ary_fgc = [];
	if( !is_array($fgc) ){
		$a = ($c >> 24) & 0xff;
		$r = ($c >> 16) & 0xff;
		$g = ($c >> 8) & 0xff;
		$b = ($c & 0xff);

		$ary_fgc = [];
		$ary_fgc['a'] = $a;
		$ary_fgc['r'] = $r;
		$ary_fgc['g'] = $g;
		$ary_fgc['b'] = $b;
		}
		else {
			foreach( $fgc as $k=>$v ){
				if( preg_match("/[argbARGB]/", $k) ){ $ary_fgc[$k] = $v; }
				}
			}

	$ary_bgc = [];
	if( !is_array($bgc) ){
		$a = ($c >> 24) & 0xff;
		$r = ($c >> 16) & 0xff;
		$g = ($c >> 8) & 0xff;
		$b = ($c & 0xff);

		$ary_bgc['a'] = $a;
		$ary_bgc['r'] = $r;
		$ary_bgc['g'] = $g;
		$ary_bgc['b'] = $b;
		}
		else {
			foreach( $bgc as $k=>$v ){
				if( preg_match("/[argbARGB]/", $k) ){ $ary_bgc[$k] = $v; }
				}
			}

	$cnt = count( $this->title ) - 1;
	if( preg_match("/g/i", $opt) ){
		$this->graph_fgc = $ary_fgc;
		$this->graph_bgc = $ary_bgc;
		}
		else if( preg_match("/l/i", $opt) ){
			$this->line_fgc = $ary_fgc;
			$this->line_bgc = $ary_bgc;
			}
		else if( preg_match("/t/i", $opt) ){
			$this->text_fgc = $ary_fgc;
			$this->text_bgc = $ary_bgc;
			}

	return true;
}
################################################################################
#	Short functions to the colors() function.
################################################################################
public function graph_colors( $fgc = null, $bgc = null ){ return $this->colors( $fgc, $bgc, "g" ); }
public function line_colors( $fgc = null, $bgc = null ){ return $this->colors( $fgc, $bgc, "l" ); }
public function text_colors( $fgc = null, $bgc = null ){ return $this->colors( $fgc, $bgc, "t" ); }
################################################################################
#	line(). Creates a line graph.
################################################################################
function line( $name=null )
{
	list( $min, $max ) = $this->get_minmax();
	list( $w, $h ) = $this->get_width_height( $name, $min, $max );

echo "MIN = $min, MAX = $max\n";
}
################################################################################
#	get_width_height(). Get how big the graph has to be.
################################################################################
private function get_width_height( $name=null, $min=0, $max=0 )
{
	$height = abs( $max - $min ) * 100;
	if( $height < 100 ){ $height = 100; }
#
#	If no name was sent over - use the last name created.
#
	if( is_null($name) ){ $name = $this->name; }
#
#	Go thru the data and get the maximum and minimu amounts.
#
	$data = $this->data[$name];
	echo "Data = "; print_r( $data ); echo "\n";

	foreach( $data as $k=>$v ){
		if( is_array($v) ){
			$width = count( $v );
			}
		}
}
################################################################################
#	minmax(). Find the minimum and maximum number of the data.
################################################################################
private function get_minmax( $name=null )
{
	$min = 99999;
	$max = -99999;
#
#	If no name was sent over - use the last name created.
#
	if( is_null($name) ){ $name = $this->name; }
#
#	Go thru the data and get the maximum and minimu amounts.
#
	$data = $this->data[$name];
	echo "Data = "; print_r( $data ); echo "\n";

	foreach( $data as $k=>$v ){
		if( is_array($v) ){
			foreach( $v as $k1=>$v1 ){
				if( $min > $v1 ){ $min = $v1; }
				if( $max < $v1 ){ $max = $v1; }
				}
			}
			else {
				if( $min > $v ){ $min = $v; }
				if( $max < $v ){ $max = $v; }
				}
		}

	return array( $min, $max );
}
################################################################################
#	__destruct(). ALWAYS get rid of everything!
################################################################################
function __destruct()
{
	foreach( $this->gd as $k=>$v ){
		imagedestroy( $v );
		}

	unset( $this->data );
	$this->data = null;
	$this->gd = null;

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['graphs']) ){
		$GLOBALS['classes']['graphs'] = new class_graphs();
		}

?>
