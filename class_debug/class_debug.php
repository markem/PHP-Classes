<?php
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		echo "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n";
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
#	class_debug();
#
#-Description:
#
#	Class to handle debug statements.
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
#	Mark Manning			Simulacron I			Wed 09/30/2020 22:47:46.40 
#		Original Program.
#
#	Mark Manning			Simulacron I			Mon 10/05/2020 12:27:10.97 
#	---------------------------------------------------------------------------
#	Ok. It would be nice to make the arguments all in one string - but I'd have
#	to rewrite the entire class. So unfortunately, you have to specify each
#	option as a separte command. So it is "('debug', 'save')" but you ONLY NEED
#	to specify enough to make a command unique. So like "debug" can just be the
#	letter "D" or "d" and "save" can just be "S" or "s". Options are listed at
#	the beginning of a function.
#
#	If you notice, I have this after getting the arguments:
#
#		while( count($args) && is_array($args[0]) ){ $args = $args[0]; }
#
#	This is because if you call one of the extra functions (like the "kill"
#	function) - it sends over the arguments it was called with BUT the:
#
#		$args = func_get_args();
#
#	function returns and ARRAY. So then that array goes to the next function
#	and THAT function then gets ARRAY[ARRAY[ARGS]]. So - you just test the
#	first argument and if it is an array you just pop the stack. So it then
#	goes:
#
#		$args = ARRAY[ARRAY[ARGS]];
#		$args = ARRAY[ARGS];
#		$args = ARGS;
#
#	And we are through and can go on.
#
#	Ok. I know some nut out there is going "But ARGS is an array! This won't
#	work!" Ok dummy - remember that ARGS is an ARRAY OF INFORMATION. So ARGS
#	does NOT equal ARRAY. ARGS equals things like "debug" and so on. Remember:
#	we are not testing the ARRAY - we are testing what is INSIDE of the ARRAY
#	and these functions don't use ARRAYs as part of their calling stuff.
#
#	Mark Manning			Simulacron I			Mon 11/02/2020 12:55:46.41 
#	---------------------------------------------------------------------------
#	Added in a LOG file
#
#	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
#	---------------------------------------------------------------------------
#		This is now under the BSD Three Clauses Plus Patents License.
#		See the BSD-3-Patent.txt file.
#
#		CLASS_DEBUG.PHP. A class to handle working with debugging programs.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_debug
{
	private $debug_flag = false;
	private $debug = null;
	private $local_flag = false;	#	Set to TRUE to always display debug info
	private $save_info = false;		#	Save to a file?
	private $fp = null;
	private	$level = 3;
	private $logfile = null;

################################################################################
#	__construct(). Constructor.
#
#	Arguments:
#		"d(ebug)"		:	Turn on the debug feature.
#		"s(ave)"		:	Turn on saving the debug info into a file.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['debug']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['debug']; }
}
################################################################################
#	init(). This is the __construct() function but made like this so you can
#		call it any time.
#
#	Options:	debug = TRUE|FALSE, save = TRUE|FALSE
#
################################################################################
public function init()
{
	$debug = $save = false;

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	if( is_array($args) ){
		foreach( $args as $k=>$v ){
			if( preg_match("/^d.*/i", $v) ){ $debug = true; }
				else if( preg_match("/^s.*/i", $v) ){ $save = true; }
			}
		}

	$this->debug_flag = false;
	$this->save_info = false;

	$this->log();
}
#
#--------------------------------------------------------------------------------
#
public function on(){ $this->debug_flag = true; }
public function off(){ $this->debug_flag = false; }
################################################################################
#	in(). Display the ENTER comment and store it to the file.
#
#	Arguments:
#		<Your Message>	:	This will be displayed also and/or saved.
#
#	enter(). Alternate function name.
################################################################################
public function in()
{
	if( $this->debug_flag || $this->local_flag ){
		$ary = $this->get_backtrace();
		array_pop( $ary );

		foreach( $ary as $k=>$v ){
			$this->level++;

			$this->log( str_repeat('-',$this->level) . ">Entering	:	" . $v['file'] .
				" [" . $v['class'] . "] " .  $v['function'] . " # " . $v['line'] . "\n" );
			}

		$args = func_get_args();
		while( count($args) && is_array($args[0]) ){ $args = $args[0]; }

		if( count($args) > 0 ){
			foreach( $args as $k=>$v ){
				$this->log( "IN : $v\n" );
				}
			}
		}
}
################################################################################
#   enter(). Another function to start up the debugger.
################################################################################
function enter(){ $this->in( func_get_args() ); }
################################################################################
#	out(). Display the EXIT comment and store it to the file.
#
#	Arguments:
#		<Your Message>	:	This will be displayed also and/or saved.
#
#	exit(). Alternate function name.
################################################################################
public function out()
{
	if( $this->debug_flag || $this->local_flag ){
		$ary = $this->get_backtrace();
		array_pop( $ary );

		foreach( $ary as $k=>$v ){
			if( $this->level > 1 ){ $this->level--; }

			$this->log( '<' . str_repeat('-', $this->level) . "Exiting	:	" .
				$v['file'] . " [" . $v['class'] . "] " .
				$v['function'] . " # " . $v['line'] . "\n" );
			}
#
#	Get any MESSAGES HERE!
#
		$args = func_get_args();
		while( count($args) && is_array($args[0]) ){ $args = $args[0]; }

		if( count($args) > 0 ){
			foreach( $args as $k=>$v ){
				$this->log( "OUT : $v\n" );
				}
			}
		}
}
################################################################################
#	exit(). Shows the arguments passed to it.
################################################################################
public function exit(){ $this->out( func_get_args() ); }
################################################################################
#	msg(). Message (msg) out debug statements.
#
#	Arguments:
#		<Your Message>	:	This will be displayed also and/or saved.
#
#	dump(). Alternate function name.
################################################################################
public function msg()
{
	if( $this->debug_flag || $this->local_flag ){
		$dbg = $this->get_backtrace();
		array_pop( $dbg );

		if( $this->debug_flag || $this->local_flag ){
			$this->log( "\n\n" );
			foreach( $dbg as $k=>$v ){
				$cmd = $this->pcmd( $v );
				$this->log( $cmd );
				}

			$this->log( "\n\n" );
			$args = func_get_args();
			while( count($args) && is_array($args[0]) ){ $args = $args[0]; }

			if( count($args) > 0 ){
				$this->log( "MSG : $args[0]\n" );
				$this->log( "\n\n" );
				}
			}
		}
}
################################################################################
#	get_backtrace(). Get the debug backtrace and fix any blank areas.
################################################################################
private function get_backtrace( $opt=false, $last=true )
{
	$dbg = debug_backtrace();
	if( $opt === false ){ $dbg = array_reverse( $dbg ); }
#
#	Remove the LAST function and go back again.
#
	if( $last ){ array_pop( $dbg ); }

	foreach( $dbg as $k=>$v ){
		if( !isset($v['file']) || is_null($v['file']) ){ $dbg[$k]['file'] = "--NULL--"; }
		if( !isset($v['class']) || is_null($v['class']) ){ $dbg[$k]['class'] = "--NULL--"; }
		if( !isset($v['function']) || is_null($v['function']) ){ $dbg[$k]['function'] = "--NULL--"; }
		if( !isset($v['line']) || is_null($v['line']) ){ $dbg[$k]['line'] = "--NULL--"; }
		}

	return( $dbg );
}
################################################################################
#	pcmd(). Get the print line for the backtrace given. ONLY ONE LINE!
################################################################################
private function pcmd( $dbg=null )
{
	if( is_null($dbg) ){ return false; }

	$cmd = "STACK	:	" . $dbg['file'] . " [" . $dbg['class'] . "] " .
		$dbg['function'] . " @ " . $dbg['line'] . "\n";

	return $cmd;
}
################################################################################
#	scmd(). Do a short filename pcmd.
################################################################################
private function scmd( $dbg=null )
{
	if( is_null($dbg) ){ return false; }

	$cmd = "STACK	:	" . basename($dbg['file']) . " [" . $dbg['class'] . "] " .
		$dbg['function'] . " @ " . $dbg['line'] . "\n";

	return $cmd;
}
#
################################################################################
#	getVariableName(). Function to return the variable's name
#	Notes:	Taken from
#		https://www.geeksforgeeks.org/how-to-get-a-variable-name-as-a-string-in-php/
################################################################################
private function getVariableName()
{
	$args = func_get_args();
	$args = $args[0];

	if( is_null($args) ){ return false; }

	foreach( $GLOBALS as $k=>$v ){
		if( ($v === $args) && !preg_match("/(_get|_post|_cookie|_file)/i", $k) ){
			return $k;
			}
		}
}
################################################################################
#	dump(). A simple function to dump some information.
################################################################################
public function dump()
{
	$args = func_get_args();
	if( is_array($args) && (count($args) < 2) ){ $args = $args[0]; }

	if( is_null($args) ){ return false; }

	$dbg = $this->get_backtrace( true, false );

if( false ){
#	foreach( $dbg as $k=>$v ){ echo "CLASS = " . $v['class'] . "\n"; }
	while( isset($dbg[0]) && preg_match("/debug/i", $dbg[0]['class']) ){
		echo "Getting rid of CLASS = " . $dbg[0]['class'] . "\n";
		array_shift( $dbg );
		}

	array_shift( $dbg );
}

	foreach( $dbg as $k=>$v ){
		echo $this->scmd( $v );
		}

	$line = $dbg[0]['line'];
	$name = $this->getVariableName( $args );
	if( strlen(trim($name)) < 1 ){ $name = "unknown"; }
	if( is_array($args) ){
		echo "$name @ Line : $line = ARRAY\n";
		foreach( $args as $k=>$v ){
			if( is_array($v) ){
				$s = sprintf( "%5d : ", $k );
				$o = "";
				foreach( $v as $k1=>$v1 ){
					if( is_string($v1) ){ $o .= wordwrap($v1, 75, "\n        " ); }
						elseif( is_float($v1) ){ $o .= sprintf( "%5f", $v1 ); }
						elseif( is_int($v1) ){ $o .= sprintf( "%5d", $v1 ); }
						else { $o .= "-UNK-"; }

					echo "$s$o\n";
					}
				}
			}
		}
		else {
			echo "$name @ Line : $line = $args\n";
			}
}
################################################################################
#	die(). Death subroutine.
#	kill(). Alternate function name.
################################################################################
public function die()
{
	$string= "\n\nProgram Terminated\n\n.";
	$this->out( func_get_args() );

	if( $this->debug_flag ){
		$this->get_logfile();
		fwrite( $this->fp, "$string\n" );
		}
		else {
			echo $string;
			}

	exit;
}
public function kill(){ $this->die( func_get_args() ); }
################################################################################
#	log(). Creates/Writes a log file
#
#	Notes:	You can send either an array or a list.
#
################################################################################
public function log( $log=null )
{

	if( $this->debug_flag ){
		if( is_array($log) ){ $log = implode( "\n", $log ); }

		$this->get_logfile();
		fwrite( $this->fp, "$log\n" );
		}

	return true;
}
################################################################################
#	get_logfile(). Checks to make sure the log file has been opened. Otherwise,
#		open it.
################################################################################
private function get_logfile()
{
	if( $this->debug_flag ){
		if( is_null($this->fp) ){
			$date = $this->get_time();
			$this->logfile = "./$date.log";
			$this->fp = fopen( $this->logfile, "w" );

			if( !is_resource($this->fp) ){
				$this->die( "FILE : $this->logfile - is not a file\n" );
				}

			fwrite( $this->fp, "Opening DEBUG file : $this->logfile\n" );
			}
		}
}
################################################################################
#	get_time(). Get a proper date/time with microseconds for whatever
################################################################################
private function get_time()
{
	$time = microtime(true);
	list( $ts, $ms ) = explode( '.', $time );
	return date( "Y-m-d_H-i-s.", $ts) . $ms;
}
################################################################################
#	__destruct(). Closes everything out.
################################################################################
public function __destruct()
{
	if( !is_null($this->fp) ){
		$this->log( "Closing DEBUG file : $this->logfile\n" );
		fclose( $this->fp );
		$this->fp = null;
		}
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['debug']) ){
		$GLOBALS['classes']['debug'] = new class_debug();
		}

?>
