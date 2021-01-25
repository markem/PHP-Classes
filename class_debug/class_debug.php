<?php

	include_once( "../class_debug.php" );
################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_files();
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
#	Mark Manning			Simulacron I			Thu 10/29/2020 21:26:47.99 
#	---------------------------------------------------------------------------
#	These functions are under the MIT License laws. You can find the
#	documents for them online.
#
#	Mark Manning			Simulacron I			Mon 11/02/2020 12:55:46.41 
#	---------------------------------------------------------------------------
#	Added in a LOG file
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
function __construct(){ $this->init( func_get_args() ); }
################################################################################
#	init(). This is the __construct() function but made like this so you can
#		call it any time.
#
#	Notes: All ARGUMENTS are given as "<OPTION>"=>"<VALUE>".
#
#	Options:	debug = TRUE|FALSE
#
################################################################################
function init()
{
#
#	Arguments are looked at HERE. Don't put them in!
#
	$args = func_get_args();
	while( count($args) && is_array($args[0]) ){ $args = $args[0]; }

	foreach( $args as $k=>$v ){
		if( preg_match("/d(ebug)*/i", $v) ){
			$this->debug_flag = true;
			$this->in( $args );
			$this->out( $args );
			}
			else if( preg_match("/s(ave)*/i", $v) ){
				$this->save_info = true;
				}
		}

	$this->log();
}
################################################################################
#	in(). Display the ENTER comment and store it to the file.
#
#	Arguments:
#		<Your Message>	:	This will be displayed also and/or saved.
#
#	enter(). Alternate function name.
################################################################################
function in()
{
	if( $this->debug_flag || $this->local_flag ){
		$ary = debug_backtrace();
		$ary = array_reverse( $ary );

		foreach( $ary as $k=>$v ){
			$this->level++;
			if( !isset($v['file']) || is_null($v['file']) ){ $v['file'] = "--NULL--"; }
			if( !isset($v['class']) || is_null($v['class']) ){ $v['class'] = "--NULL--"; }
			if( !isset($v['function']) || is_null($v['function']) ){ $v['function'] = "--NULL--"; }
			if( !isset($v['line']) || is_null($v['line']) ){ $v['line'] = "--NULL--"; }

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
function enter(){ $this->in( func_get_args() ); }
################################################################################
#	out(). Display the EXIT comment and store it to the file.
#
#	Arguments:
#		<Your Message>	:	This will be displayed also and/or saved.
#
#	exit(). Alternate function name.
################################################################################
function out()
{
	if( $this->debug_flag || $this->local_flag ){
		$ary = debug_backtrace();

		foreach( $ary as $k=>$v ){
			$this->level--;
			if( !isset($v['file']) || is_null($v['file']) ){ $v['file'] = "--NULL--"; }
			if( !isset($v['class']) || is_null($v['class']) ){ $v['class'] = "--NULL--"; }
			if( !isset($v['function']) || is_null($v['function']) ){ $v['function'] = "--NULL--"; }
			if( !isset($v['line']) || is_null($v['line']) ){ $v['line'] = "--NULL--"; }

			$this->log( '<' . str_repeat('-', $this->level) . "Exiting	:	" .
				$v['file'] . " [" . $v['class'] . "] " .
				$v['function'] . " # " . $v['line'] . "\n" );
			}

		$args = func_get_args();
		while( count($args) && is_array($args[0]) ){ $args = $args[0]; }

		if( count($args) > 0 ){
			foreach( $args as $k=>$v ){
				$this->log( "OUT : $v\n" );
				}
			}
		}
}
function exit(){ $this->out( func_get_args() ); }
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
		$dbg = debug_backtrace();
		$dbg = array_reverse( $dbg );

		if( $this->debug_flag || $this->local_flag ){
			$this->log( "\n\n" );
			foreach( $dbg as $k=>$v ){
				if( !isset($v['file']) || is_null($v['file']) ){ $v['file'] = "--NULL--"; }
				if( !isset($v['class']) || is_null($v['class']) ){ $v['class'] = "--NULL--"; }
				if( !isset($v['function']) || is_null($v['function']) ){ $v['function'] = "--NULL--"; }
				if( !isset($v['line']) || is_null($v['line']) ){ $v['line'] = "--NULL--"; }

				$this->log( "STACK	:	" . $v['file'] . " [" . $v['class'] . "] " .
					$v['function'] . " # " . $v['line'] . "\n" );
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
function dump(){ $this->log( func_get_args() ); }
################################################################################
#	die(). Death subroutine.
#	kill(). Alternate function name.
################################################################################
private function die()
{
	$this->out( func_get_args() );

	echo "\n\nProgram Terminated\n\n.";
	exit;
}
function kill(){ $this->die( func_get_args() ); }
################################################################################
#	log(). Creates/Writes a log file
#
#	Notes:	You can send either an array or a list.
#
################################################################################
function log( $log=null )
{

	if( is_array($log) ){ $log = implode( "\n", $log ); }

	if( is_null($this->fp) ){
		$date = $this->get_time();
		$this->logfile = "./$date.log";
		$this->fp = fopen( $this->logfile, "w" );

		if( !is_resource($this->fp) ){
			$this->die( "FILE : $this->logfile - Is not a file\n" );
			}

		fwrite( $this->fp, "Opening DEBUG file : $this->logfile\n" );
		}
		else {
			fwrite( $this->fp, "$log\n" );
			}

	return true;
}
################################################################################
#	get_time(). Get a proper date/time with microseconds for whatever
################################################################################
function get_time()
{
	$time = microtime(true);
	list( $ts, $ms ) = explode( '.', $time );
	return date( "Y-m-d_H-i-s.", $ts) . $ms;
}
################################################################################
#	__destruct(). Closes everything out.
################################################################################
function __destruct()
{
	if( !is_null($this->fp) ){
		$this->log( "Closing DEBUG file : $this->logfile\n" );
		fclose( $this->fp );
		$this->fp = null;
		}
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['debug']) ){ $GLOBALS['classes']['debug'] = new class_debug(); }

?>
