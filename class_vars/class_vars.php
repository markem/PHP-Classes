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
#	class_vars();
#
#-Description:
#
#	A class to make new variables. All you do is to put :
#
#		$cv = new class_vars();
#
#	At the beginning of your program and then just put new variables after
#	the "$cv" part like so:
#
#		$cv->a = "X";
#		$cv->b= 5;
#		$cv->c = array(1,2,3);
#		$cv->d = [];
#
#	and so on.
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
#	Mark Manning			Simulacron I			Sun 11/14/2021 20:04:39.43 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sun 11/14/2021 20:11:02.99 
#	---------------------------------------------------------------------------
#		REMEMBER! If you use the CLEAR() function IT WILL WIPE ALL VARIABLES OUT!
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
#		CLASS_VARS.PHP. A class to handle working with variables.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
################################################################################
#
#END DOC
################################################################################
class class_vars
{
	private $vars = null;

################################################################################
#	__construct(). Construction function.  Optionally pass in your vars.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['vars']) ){ $this->init( func_get_args() );}
		else{ return $GLOBALS['class']['vars']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
public function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->vars = [];

	return $this->put( "args", $args );
}
################################################################################
#	put(). Insert one or more vars. MUST HAVE TWO ARGUMENTS.
#	Args:	[0] = NAME
#			[1] = VALUE (can be anything)
################################################################################
public function put()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Because this is twisted somewhat - I made a new subroutine
#	which later on I can incorporate here.
#
	$this->put_var( $args[0], $args[1] );

	return true;
}
################################################################################
#	get(). Get one or more vars. MUST HAVE ONE ARGUMENT.
#	Args:	[0] = NAME
################################################################################
public function get()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Because I was having problems doing this - I made a new function below.
#
	return $this->get_var( $args );
}
################################################################################
#	clear(). Clear out the enum array.
#		Optional.  Set the flag in the __construct function.
#		After all, vars are supposed to be constant.
################################################################################
public function clear()
{
	$this->var = [];
	return true;
}
################################################################################
#	__call().  In case someone tries to blow up the class.
################################################################################
public function __call( $name, $arguments )
{
#	$this->debug->dump( $name );
#	$this->debug->dump( $arguments );

	if( count($arguments) < 1 ){
		echo "Calling GET\n";
		return $this->get( $name );
		}
		else {
			return $this->put( $name, $arguments[0] );
			}

	return false;
}
################################################################################
#	__get(). Gets the value.
################################################################################
public function __get($name)
{
	return $this->get( $name );
}
################################################################################
#	__set().  Sets the value.
################################################################################
public function __set( $name, $value=null )
{
	return $this->put( $name, $value );
}
################################################################################
#	put_var(). Put a value into the global array.
################################################################################
private function put_var( $name=null, $value=null )
{
#
#	See if we already have a variable named this
#	and if it already exists - just update it.
#
	$flag = true;
	foreach( $this->vars as $k=>$v ){
		if( $v['name'] === $name ){
			$flag = false;
			$this->vars[$k]['value'] = $value;
			return true;
			}
		}
#
#	Otherwise, just create a new variable
#
	if( $flag ){
		$c = count( $this->vars );
		$this->vars[$c]['name'] = $name;
		$this->vars[$c]['value'] = $value;
		}

	return true;
}
################################################################################
#	get_var(). Get a value from the global array.
################################################################################
private function get_var( $name=null )
{
	if( is_null($name) ){ return false; }
#
#	If there is already a variable named this name
#	then just get it and return it
#
	foreach( $this->vars as $k=>$v ){
		if( $v['name'] === $name ){
			$v = $this->vars[$k]['value'];
			return $v;
			}
		}
#
#	Else, if there isn't a variable named this name
#	then make one and set it to NULL
#
	$c = count( $this->vars );
	$this->vars[$c]['name'] = $name;
	$this->vars[$c]['value'] = null;

	return false;
}
################################################################################
#	fn(). Find a given variable. Return VALUE.
################################################################################
public function fn( $name=null )
{
	if( is_null($name) ){ return false; }

	foreach( $this->vars as $k=>$v ){
		if( $v['name'] === $name ){
			$a = $v['value'];
			return $a;
			}
		}

	return false;
}
################################################################################
#	fv(). Find a given variable via the VALUE. Returns multiple NAMES.
################################################################################
public function fv( $value=null )
{
	if( is_null($name) ){ return false; }

	$vars = [];
	foreach( $this->vars as $k=>$v ){
		if( $v['value'] === $value ){
			$vars[] = $v['name'];
			}
		}

	if( is_array($vars) && (count($vars) > 0) ){ return $vars; }

	return false;
}
################################################################################
#	fn_all(). PREG_MATCH. Send regexp partial NAME - get list of names back.
################################################################################
public function fn_all( $regexp=null )
{
	if( is_null($regexp) ){ return false; }

	$vars = [];
	foreach( $this->vars as $k=>$v ){
		if( preg_match($regexp, $v['name']) ){
			$vars[] = $v['name'];
			}
		}

	if( is_array($vars) && (count($vars) > 0) ){ return $vars; }

	return false;
}
################################################################################
#	fv_all(). PREG_MATCH. Send regexp partial NAME - get list of values back.
################################################################################
public function fv_all( $regexp=null )
{
	if( is_null($regexp) ){ return false; }

	$vars = [];
	foreach( $this->vars as $k=>$v ){
		if( preg_match($regexp, $v['name']) ){
			$vars[] = $v['value'];
			}
		}

	if( is_array($vars) && (count($vars) > 0) ){ return $vars; }

	return false;
}
################################################################################
#	__destruct().  Deconstruct the class.  Remove the list of vars.
################################################################################
public function __destruct()
{
	if( isset($this->vars) ){
#		$this->debug->dump( $this->vars );
		unset( $this->vars );
		}

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['vars']) ){ $GLOBALS['classes']['vars'] = new class_vars(); }

?>
