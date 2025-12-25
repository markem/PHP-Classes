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
#	enums();
#
#-Description:
#
#	A class to handle enums. All you do is to put :
#
#		$ce = new class_enums();
#
#	At the beginning of your program and then just put new variables after
#	the "$ce" part like so:
#
#		$ce->a = "X";
#		$ce->b= 5;
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
#	Mark Manning			Simulacron I			11/14/2015
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
#		CLASS_ENUMS.PHP. A class to handle working with ENUMS.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
################################################################################
#
#END DOC
################################################################################
class class_enums
{
	private $enums;
	private $clear_flag;
	private $last_value;

################################################################################
#	__construct(). Construction function.  Optionally pass in your enums.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['enums']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['enums']; }
}
################################################################################
#	init(). Way to be able to re-init the class.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$this->enums = array();
	$this->clear_flag = false;
	$this->last_value = 0;

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	return $this->put( $args );
}
################################################################################
#	put(). Insert one or more enums.
################################################################################
function put()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Did they send us an array of enums?
#	Ex: $c->put( array( "a"=>0, "b"=>1,...) );
#	OR  $c->put( array( "a", "b", "c",... ) );
#
	if( is_array($args) ){
#
#	Add them all in
#
		foreach( $args as $k=>$v ){
#
#	Don't let them change it once it is set.
#	Remove the IF statement if you want to be able to modify the enums.
#
			if( !isset($this->enums[$k]) ){
#
#	If they sent an array of enums like this: "a","b","c",... then we have to
#	change that to be "A"=>#. Where "#" is the current count of the enums.
#
				if( is_numeric($k) ){
					$this->enums[$v] = $this->last_value++;
					}
#
#	Else - they sent "a"=>"A", "b"=>"B", "c"=>"C"...
#
					else {
						$this->last_value = $v + 1;
						$this->enums[$k] = $v;
						}
				}
			}
		}
#
#	Nope!  Did they just sent us one enum?
#
		else {
#
#	Is this just a default declaration?
#	Ex: $c->put( "a" );
#
			if( is_array($args) && count($args) < 2 ){
#
#	Again - remove the IF statement if you want to be able to change the enums.
#
				if( !isset($this->enums[$args[0]]) ){
					$this->enums[$args[0]] = $this->last_value++;
					}
#
#	No - they sent us a regular enum
#	Ex: $c->put( "a", "This is the first enum" );
#
					else {
#
#	Again - remove the IF statement if you want to be able to change the enums.
#
						if( !isset($this->enums[$args[0]]) ){
							$this->last_value = $args[1] + 1;
							$this->enums[$args[0]] = $args[1];
							}
						}
				}
			}

	return true;
}
################################################################################
#	get(). Get one or more enums.
################################################################################
function get()
{
	$num = func_num_args();
	$args = func_get_args();
#
#	Is this an array of enums request? (ie: $c->get(array("a","b","c"...)) )
#
	if( is_array($args[0]) ){
		$ary = array();
		foreach( $args[0] as $k=>$v ){
			$ary[$v] = $this->enums[$v];
			}

		return $ary;
		}
#
#	Is it just ONE enum they want? (ie: $c->get("a") )
#
		else if( ($num > 0) && ($num < 2) ){
			return $this->enums[$args[0]];
			}
#
#	Is it a list of enums they want? (ie: $c->get( "a", "b", "c"...) )
#
		else if( $num > 1 ){
			$ary = array();
			foreach( $args as $k=>$v ){
				$ary[$v] = $this->enums[$v];
				}

			return $ary;
			}
#
#	They either sent something funky or nothing at all.
#
	return false;
}
################################################################################
#	clear(). Clear out the enum array.
#		Optional.  Set the flag in the __construct function.
#		After all, ENUMS are supposed to be constant.
################################################################################
function clear()
{
	if( $clear_flag ){
		unset( $this->enums );
		$this->enums = array();
		}

	return true;
}
################################################################################
#	__call().  In case someone tries to blow up the class.
################################################################################
function __call( $name, $arguments )
{
	if( isset($this->enums[$name]) ){ return $this->enums[$name]; }
		else if( !isset($this->enums[$name]) && (count($arguments) > 0) ){
			$this->last_value = $arguments[0] + 1;
			$this->enums[$name] = $arguments[0];
			return true;
			}
		else if( !isset($this->enums[$name]) && (count($arguments) < 1) ){
			$this->enums[$name] = $this->last_value++;
			return true;
			}

	return false;
}
################################################################################
#	__get(). Gets the value.
################################################################################
function __get($name)
{
	if( isset($this->enums[$name]) ){ return $this->enums[$name]; }
		else if( !isset($this->enums[$name]) ){
			$this->enums[$name] = $this->last_value++;
			return true;
			}

	return false;
}
################################################################################
#	__set().  Sets the value.
################################################################################
function __set( $name, $value=null )
{
	if( is_array($value) ){ return false; }
	if( is_object($value) ){ return false; }
	if( is_resource($value) ){ return false; }
	if( isset($this->enums[$name]) ){ return false; }
		else if( !isset($this->enums[$name]) && !is_null($value) ){
			$this->last_value = $value + 1;
			$this->enums[$name] = $value;
			return true;
			}
		else if( !isset($this->enums[$name]) && is_null($value) ){
			$this->enums[$name] = $this->last_value++;
			return true;
			}

	return false;
}
################################################################################
#	__destruct().  Deconstruct the class.  Remove the list of enums.
################################################################################
function __destruct()
{
	unset( $this->enums );
	$this->enums = null;

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['enums']) ){
		$GLOBALS['classes']['enums'] = new class_enums();
		}
?>
