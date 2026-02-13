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
#	class_images();
#
#-Description:
#
#	A class to handle my images.
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
#		CLASS_IMAGES.PHP. A class to handle working with images.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_images
{
	public $temp_path = null;

	private $cwd = null;
	private $cmds = null;
	private $cnt_cmds = null;
	private $argv = null;
	private $args = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['images']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['images']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

#
#	Get all options from the call line.
#
	$this->argv = [];
	foreach( $argv as $k=>$v ){ $this->argv[$k] = $v; }
	$this->dump( "ARGV", $argv );
#
#	Get all arguments for the function (comes usually from __construct() )
#
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->args = [];
	foreach( $args as $k=>$v ){ $this->args[$k] = $v; }
	$this->dump( "ARGS", $args );

	$this->temp_path = "c:/temp/images";
	if( !file_exists("c:/temp") ){ mkdir( "c:/temp" ); chmod( "c:/temp", 0777 ); }
	if( !file_exists("c:/temp/images") ){
		mkdir( "c:/temp/images" );
		chmod( "c:/temp/images", 0777 );
		}

	$this->cwd = getcwd();

	$this->cmds = [];
	$this->cnt_cmds = 0;
}
################################################################################
#	getcmds(). Get all image commands. Default file is cmd.dat.
################################################################################
function getcmds( $file=null )
{
#
#	Is the FILE null?
#
	if( is_null($file) ){ die( "ERROR : FILE is NULL" ); }
#
#	Make sure to change all backslashes to forward slashes
#
	$file = str_replace( "\\", "/", $file );
#
#	Did they just send us a "./"?
#
	if( preg_match(";/;", $file) ){
		$a = explode( '/', $file );
#
#	Is the directory the current one? Also, we do not handle or want the "..".
#
		if( ($a[0] === ".") || ($a[0] === "..") ){ array_shift( $a ); }
		if( count($a) > 1 ){ $file = implode( '/', $a ); }
			else { $file = $a[0]; }
		}
#
#	If the path is not already there - put in the current working directory
#
	if( !preg_match(";/;", $file) ){ $file = $this->cwd . "/$file"; }
#
#	Get the file
#
	$info = file_get_contents( $file );
#
#	If the file has the normal "\n" in it, then first get rid of any Control-Ms
#	and then split up the information.
#
	if( preg_match("/\n/", $info) ){
		$info = str_replace( "", "", $info );
		$info = explode( "\n", $info );
		}
#
#	Else - just split up the lines along the Control-Ms
#
		else if( preg_match("//", $info) ){
			$info = explode( "", $info );
			}
#
#	Now go through and split up the commands
#	All commands are "<cmd><tab><data>"
#	Example: get	myfile.dat
#
	foreach( $info as $k=>$v ){
		$a = explode( "\t", $v );
		$this->cmds[$this->cnt_cmds][0] = $a[0];
		$this->cmds[$this->cnt_cmds][1] = $a[1];
		$this->cnt_cmds++;
		}

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['images']) ){
		$GLOBALS['classes']['images'] = new class_images();
		}

?>
