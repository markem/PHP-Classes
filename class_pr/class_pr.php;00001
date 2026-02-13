<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
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

		$libs = getenv( "my_libs" );
		$libs = str_replace( "\\", "/", $libs );

		if( file_exists("./$class") ){ $libs = "."; }
			else if( file_exists("../$class") ){ $libs = ".."; }
			else if( !file_exists("$libs/$class") ){
				die( "Can't find $libs/$class - aborting\n" );
				}

		include "$libs/$class";
		});

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_pr();
#
#-Description:
#
#	A class to handle printing. Mainly to be able to do a print_r for all types
#	of variables.
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
#	Mark Manning			Simulacron I			Sat 03/15/2025 20:15:55.63
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
#		CLASS_PR.PHP. A class to handle printing out information.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_pr
{
	public $pipes = null;
	private	$files = null;
	private $opts = null;
	private $circuit = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	return $this->init( func_get_args() );
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	global $fp;

	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$fp = null;

	$this->opts = [];
	$this->opts['type'] = true;
	$this->opts['title'] = true;
	$this->opts['arylen'] = true;
	$this->opts['strlen'] = true;
	$this->opts['spaces'] = 1;

	$this->cwd = getcwd();
	$this->cwd = str_replace( "\\", "/", $this->cwd );
	$this->bas = "inkey.bas";
	$this->exe = "inkey.exe";
	$this->dat = "inkey.dat";	#	Where we write the keys that were pressed

	$this->circuit = array(
		0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
#		2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		2 => array("file", "$this->cwd/stderr.txt", "w"),  // stderr is a pipe that the child will write to
		);

	return true;
}
#################################################################################
#	set_opts(). Allows you to set various options.
#	NOTES	:	The single variable is an associative array. So you do "X"=>"Y"
#		entries in it.
#
#	TITLE	=	Boolean. Default is TRUE. Turns on/off titles.
#	TYPE	=	Boolean. Default is TRUE. Turns on/off type information.
#
#################################################################################
function set_opts( $opts=null )
{
	foreach( $opts as $k=>$v ){ $this->opts[$k] = $v; }
	return true;
}
#################################################################################
#	To be able to print anything
#	NOTE :
#		VAR		=	The variable to print
#		TITLE	=	The Title to give the print
#################################################################################
function pr( $var=null, $title=null, $ary=false )
{
	$e = new Exception();
	$trace = explode( "\n", $e->getTraceAsString() );
	$trace = explode( ':', $trace[0] );
	$trace = explode( '(', $trace[1] );
	$path = explode( "\\", $trace[0] );

	$a = array_pop( $path );
	$a = explode( '.', $a );

	$file = $a[0];
	$line = $trace[1] = substr( $trace[1], 0, -1 );
#
#	echo str_repeat( "-", 80 ) . "\n";
#	echo "Path = "; print_r( $path ); echo "\n";
#	echo "File = "; print_r( $file ); echo "\n";
#	echo "Line = "; print_r( $line ); echo "\n";
#	print_r( $trace );
#
	$files = [];
	$lines = [];
	$classes = [];
	$functions = [];

	static $tabs = 0;
	static $spaces = " ";
	static $line = "";
	$opts = $this->opts;
	if( is_null($opts) ){
		$this->opts = [];
		$this->opts['type'] = true;
		$this->opts['title'] = true;
		$this->opts['arylen'] = true;
		$this->opts['strlen'] = true;
		$this->opts['spaces'] = 1;
		}
#
#	If $var is null - then the user probably wants a backtrace.
#
	if( is_null($var) && ($tabs < 1) ){
		$var = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$title = "Debug Backtrace";
		}

	$spaces = str_repeat( " ", $this->opts['spaces'] );
#
#	If this is the FIRST time coming through AND they do not want to see
#	the TITLE - THEN blank it out.
#
	if( $this->opts['arylen'] === false ){ $arylen = "(1)"; }
		else if( is_array($var) ){ $arylen = "(" . count($var) . ")"; }
#
#	Get the line number. Taken from the DUMP() function
#
	$dbg = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
#
#	Get all of the info
#
	foreach( $dbg as $k=>$v ){
		$p = explode( "\\", $v['file'] );
		$a = array_pop( $p );
		$a = explode( '.', $a );
		$files[] = $a[0];

		$lines[] = $v['line'];
		$classes[] = $v['class'];
		$functions[] = $v['function'];
		}

#	print_r( $dbg ); echo "\n";
#
#	Get the path for the lines
#
	$lines = array_reverse( $lines );
	$line = implode( '->', $lines );
	$line = "LINE(" . $line . ") @ ";
#
#	Then get the path of the files that have been called
#
	$files = array_reverse( $files );
	$file = implode( '->', $files );
#
#	Then get the list of classes that have been called
#
	$class = array_shift( $classes );
#
#	Then get the list of functions that have been called
#
	$function = array_pop( $functions );

	if( strlen($title) < 1 ){ $title = ""; }
		else { $title = trim( $title ); }

	if( is_array($var) ){
		echo str_repeat( $spaces, $tabs) . $line . "$file::$class->$function - $title Array$arylen->{\n";
		$tabs += 2;
		foreach( $var as $k=>$v ){ $this->pr( $v, "[$k]", true ); }
		echo str_repeat( $spaces, $tabs) . "}\n";
		$tabs -= 2;
		return;
		}
		else {
			echo str_repeat( $spaces, $tabs) . $line . "$file::$class->$function - ";
			}
#
#	Check the title
#
	if( is_null($title) ){ $title = ""; }
		else { $title = trim( $title ); }
#
#	Print out the variable
#
	$type = "";
	if( $ary === false ){ echo str_repeat( $spaces, $tabs); }
	if( is_scalar($var) ){
		if( is_bool($var) ){
				$kind = "BOOL";
				if( $this->opts['type'] == true ){ $type = "[$kind]"; }
				echo "$type$title " . ($var ? "TRUE" : "FALSE") . "\n";
			}
			else if( is_int($var) ){
				$kind = "INT";
				if( $this->opts['type'] == true ){ $type = "[$kind] "; }
				echo "$type$title $var\n";
				}
			else if( is_float($var) ){
				$kind = "FLOAT";
				if( $this->opts['type'] == true ){ $type = "[$kind]"; }
				echo "$type $title $var\n";
				}
			else if( is_string($var) ){
				if( $this->opts['strlen'] == true ){
					$a = explode( ' ', $title );
					$s = array_pop( $a );
					while( !preg_match("/\w/", $s) && (count($a) > 0) ){
						$s = array_pop($a);
						}

					$a[] = $s;
					$title = implode( ' ', $a );
					$strlen = "(" . strlen($var) . ") =";
					}
					else { $strlen = ""; }

				$kind = "STRING";
				if( $this->opts['type'] == true ){ $type = "[$kind]"; }

				echo "$type$title$strlen ";
				if( strlen($var) > 15 ){
					echo "\n";
					$tabs++;
					if( strlen($var) < 76 ){
						echo str_repeat( $spaces, $tabs) . "	=>$var\n\n";
						}
						else {
							$cmd = $this->wordwrap( $var );
							foreach( $cmd as $k=>$v ){
								echo str_repeat( $spaces, $tabs ) . "	=>$v\n";
								}

							echo "\n";
							}

					$tabs--;
					}
					else { echo "$var\n"; }
				}
		}
		else if( is_object($var) ){
			$kind = "OBJECT";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( is_resource($var) ){
			$kind = "RESOURCE";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( is_null($var) ){
			$kind = "NULL";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( is_callable($var) ){
			$kind = "CALLABLE";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( is_interface($var) ){
			$kind = "INTERFACE";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( class_exists($var) ){
			$kind = "CLASS";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}
		else if( enum_exists($var) ){
			$kind = "ENUM";
			if( $this->opts['type'] == true ){ $type = "[$kind]"; }
			echo "$type$title $kind\n";
			}

	return true;
}
################################################################################
#	wordwrap(). My version of wordwrap.
################################################################################
function wordwrap( $var, $len=70 )
{
	$ret = [];
	while( strlen($var) > $len ){
		$ret[] = substr( $var, 0, $len );
		$var = substr( $var, $len, strlen($var) );
		}

	if( strlen($var) > 0 ){ $ret[] = $var; }

	return $ret;
}
################################################################################
#	ask(). Print out a statement and get an answer.
#	NOTES :
#		$prompt = The prompt to use to get the incoming information.
#		$text = Array. Additional text that will be printed out BEFORE the
#			prompt is displayed.
################################################################################
function ask( $prompt=null, $text=null )
{
	if( !is_array($text) ){ $text = explode( "\n", $text ); }
	foreach( $text as $k=>$v ){ echo "$v\n"; }
	echo $prompt;
	$input = rtrim( stream_get_line(STDIN, 1024, PHP_EOL) );
#
#	If this is a path, change the backslashes to forward slashes
#
	$input = str_replace( "\\", "/", $from );
	return $input;
}
################################################################################
#	pathinfo(). My version of pathinfo().
################################################################################
function pathinfo( $path=null, $fromString=null, $toString=null )
{
	if( is_null($path) ){ return false; }
#
#	If the user wants to convert the path string from one type to another
#	you do it here. Like from UTF-8 to ISO-8859-1.
#
	if( !is_null($fromString) && !is_null($toString) ){
		if( function_exists(mb_convert_encoding) ){
			$path = mb_convert_encoding( $path, $toString, $fromString );
			}
		}
#
#	Fix the Windows backslash problem
#		ie: From c:\a\b\c.dat to c:/a/b/c.dat
#
#	Note : This might not work on Windows 98 or earlier. Please check to make
#		sure it does work or just comment it out.
#
	$path = str_replace( "\\", "/", $path );
#
#	If the given $path IS A DIRECTORY - then set $pathinfo to be just a
#	directory. This fixes the problem with pathinfo and WINDOWs where
#	the BASENAME, EXTENSION, and FILENAME would be incorrectly set if you
#	just sent a directory to pathinfo. Example:
#
#	c:/my/path/info
#
#	Would return 
#
#		$pathinfo['dirname'] = "c:/my/path";
#		$pathinfo['basename'] = "info";
#		$pathinfo['extension'] = "";
#		$pathinfo['filename'] = "info";
#
#	Now it returns:
#
#		$pathinfo['dirname'] = "c:/my/path/info";
#		$pathinfo['basename'] = "";
#		$pathinfo['extension'] = "";
#		$pathinfo['filename'] = "";
#
#	NOTE : If someone makes a directory like:
#
#	C:/my/path/info.txt
#
#	Or any other weirdly named directory - this function now handles it
#	properly.
#
	if( is_dir($path) ){
		$pathinfo = [];
		$pathinfo['dirname'] = $path;
		$pathinfo['basename'] = "";
		$pathinfo['extension'] = "";
		$pathinfo['filename'] = "";
		}
		else { $pathinfo = pathinfo( $path ); }

	return $pathinfo;
}
################################################################################
#	__destruct(). Be sure to close everything.
################################################################################
function __destruct()
{
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['pr']) ){
		$GLOBALS['classes']['pr'] = new class_pr();
		}

?>
