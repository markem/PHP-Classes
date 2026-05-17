<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array()" ); }
#
#	  Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
#		throw new ErrorException($errstring, $errno, 0, $errfile, $errline);
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n"
		); });

	ini_set( 'memory_limit', -1 );
	date_default_timezone_set( "UTC" );
#
#	$libs is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	spl_autoload_register(function ($class){
#
#	This might seem stupid but it works. If X is there - get rid of it and then put
#	X onto the string. If X is not there - just put it onto the string. Get it?
#
		$class = str_ireplace( ".php", "", $class ) . ".php";
#
#	Set up LIBS so we can check whereever the class is located
#	Put in the standard stuff
#
		$libs = [];
		$libs[] = ".";
		$libs[] = "..";
#
#	Now get the environment information - IF it is there
#
		$env = getenv( "my_libs" );
		if( !is_null($env) ){
			$libs[] = $env;
			}
#
#	Now insert all of the other locations to look in
#
		$libs[] = "C:\xampp\php\usr\fpdf186";
		$libs[] = "C:\xampp\php\usr\setasign";
		$libs[] = "C:\xampp\php\usr\simplehtmldom_1_9_1";
		$libs[] = "C:\xampp\php\usr";

		foreach( $libs as $k=>$v ){
			$libs[$k] = str_replace( "\\", "/", $v );
			}

		$flag = true;
		foreach( $libs as $k=>$v ){
			if( file_exists("$v/$class") ) { $lib = $v; $flag = false; }
			}

		if( $flag ){ die( "Can't find $class - aborting\n" ); }

		include_once "$lib/$class";
		});

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_html();
#
#-Description:
#
#	A class to handle my html.
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
#	Mark Manning			Simulacron I			Sun 07/07/2019 15:33:42.40 
#		Original Program.
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
#		CLASS_HTML.PHP. A class to handle working with html.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_html
{

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['files']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['files']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
}
################################################################################
#	get_links().  Use the following function to extract all of the
#		links from a HTML file.
#
#	Taken from :
#
#		https://www.hashbangcode.com/article/extract-links-html-file-php
#
################################################################################
function get_links( $html )
{
	$links = [];
	if( preg_match_all('/<a\s+.*?href=[\"\']?([^\"\' >]*)[\"\']?[^>]*>(.*?)<\/a>/i',
		$html, $matches, PREG_SET_ORDER)){
		foreach( $matches as $match ){
			$links[] = $match;
			}
		}

	return $links;
}
################################################################################
#	get_css(). Get and break up all CSS information sent to this function.
#	NOTES:	You MUST send the entire CSS string. EVERYTHING. It will then be
#		copressed down to a SINGLE string and THEN broken back apart. I know
#		it sounds dumb but I don't want to have to create two separate functions
#		to do this. So it is easier to do it once. Period.
################################################################################
function get_css( $css )
{
$pr = new class_pr();
#
#	If $css is an array - make it a string only.
#
	if( is_array($css) ){ $css = implode( $css ); }
#
#	Now start breaking it apart. First we do the semicolons
#
	$css = str_replace( ";", ";\r", $css );
#
#	Now break apart the closing braces.
#
	$css = str_replace( "}", "\r}\r", $css );
#
#	Now put any }\r. back to being }.
#
	$css = str_replace( "}\r.", "}.", $css );
#
#	Now break apart the opening braces.
#
	$css = str_replace( "{", "{\r", $css );
#
#	Now the hard one. Make anything like \w\+{ to be \r\w\+{
#
	$css = str_replace( " ", "\r", $css );
#
#	Now break it apart
#
	$css = explode( "\r", $css );
#
#	Now see if there is NOT a semicolon at the end of a ':' command
#	and put one on there
#
	foreach( $css as $k=>$v ){
		if( preg_match("/:/", $v) && !preg_match("/;/", $v) &&
			!preg_match("/{\$/", $v) ){
			$css[$k] = preg_replace( "/\$/", ";", $v );
			}
		}

	$c = [];
	$cnt = 0;
	foreach( $css as $k=>$v ){
		if( strlen(trim($v)) < 1 ){ continue; }
		if( preg_match("/{\$/", $v) || (preg_match("/^\./", $v) || preg_match("/^\w/", $v)) ){
			$type = substr( $v, 0, -1 );
			$c[$type] = [];
			}
			else if( preg_match("/:/", $v) && preg_match("/;\$/", $v) ){
				$a = explode( ":", $v );
				$c[$type][] = $a;
				}
			else if( preg_match("/(;|}|>)/", $v) ){ $c[$type][] = $v; }
			else if( preg_match("/^(\.|\w|@|\+|\!)/", $v) ){ $c[$type][] = $v; }
			else if( preg_match("/^-/", $v) ){ $c[$type][] = $v; }
			else {
				$type = $cnt++;
				$c[$type][] = "***** ERROR : $v";
				}
		}

	return array( $css, $c);
}
################################################################################
#	get_css_names(). Gets the NAMES of all of the CSS information.
#	NOTES:	First things first - whatever is set over is automatically
#		squished down to just one line.
################################################################################
function get_css_names( $css )
{
	$links = [];
	if( is_array($css) ){
		$css = implode( $css );	#	Squish down to a single line.
		}
		else if( preg_match("/(\r|\n)/", $css) ){
			$css = preg_replace( "/(\r|\n)/", "", $css );
			}

	$css = str_replace( "{", "{\n", $css );
	$css = str_replace( ";", ";\n", $css );
print_r( $css ); echo "\n";exit;
}

}

?>
