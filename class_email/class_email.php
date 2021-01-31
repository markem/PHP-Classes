<?php

	if( file_exists("../class_debug.php") ){
		include_once( "../class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( __FILE__ . ": Can not load CLASS_DEBUG" );
			}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_email();
#
#-Description:
#
#	Class to read/write emails.
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
#	Mark Manning			Simulacron I			Tue 12/31/2019 16:49:20.50 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:28:17.25 
#	---------------------------------------------------------------------------
#	This code is now under the MIT License.
#
#END DOC
################################################################################
class class_email
{
	private $debug = null;
	private $mboxes = null;
	private $num_mboxes = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct(){ $this->init( func_get_args() ); }
################################################################################
#	init(). Because we need to be able to call this to get started from
#		multiple locations.
################################################################################
function init()
{
#
#	Arguments are looked at HERE. Don't put them in!
#
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( $args );
	$this->debug->in();

	$this->mboxes = array();
	$this->debug->out();
}
################################################################################
#	load(). Load in a mail file and then break it up into messages
################################################################################
function load( $file )
{
	$this->debug->in();

	if( !file_exists($file) ){
		$this->debug->t( "DIE : The file does NOT exist\nFile : $file" );
		}

	$a = file_get_contents( $file );
	$b = explode( "\n", $a );
	unset( $a );

	$msg = "";
	$mboxes = array();
	foreach( $b as $k=>$v ){
#
#	From - Sun Dec  8 22:30:27 2019
#
		if( preg_match("/^from\s+-\s+\w+\s+\w+\s+\d+\s+\d+:\d+:\d+\s+\d+/i", $v) ){
			if( strlen($msg) > 0 ){ $mboxes[] = $msg; }
			$msg = "$v\n";
			}
			else { $msg .= "$v\n"; }
		}

	if( strlen($msg) > 0 ){ $mboxes[] = $msg; }
	$this->mboxes = $mboxes;
	$this->num_mboxes = count( $mboxes );

	$this->debug->out();
}
################################################################################
#	get(). Return all of the messages in the mbox
#	Notes:	If you send over a number - only that message is returned.
#			Otherwise send them all back.
################################################################################
function get( $num=null )
{
	$this->debug->in();

  if( !is_null($num) ){
	  if( ($num < 0) || ($num >= $this->num_mboxes) ){
		  $this->debug->out();
		  return false;
		  }

	  $this->debug->out();
	  return $this->mboxes[intval($num)];
	  }

  $this->debug->out();
  return $this->mboxes;
}
################################################################################
#	separate(). Break up an email message into components.
################################################################################
function sep( $num=null )
{
	$this->debug->in();

	if( is_null($num) ){
		$this->debug->t( "DIE : No message number given" );
		}

	if( ($num < 0) || ($num >= $this->num_mboxes) ){
		$this->debug-t( "DIE : Number given is wrong. NUM = $num" );
		}

	$cmd = "";
	$loc = 0;
	$msg = array();
	$mbox = $this->mboxes[$num];
	if( strlen(trim($mbox)) < 1 ){ return false; }

	$body = false;
	$mbox = explode( "\n", $mbox );
	foreach( $mbox as $k=>$v ){
#
#	Is this a command?
#
		$v = str_replace( "", "", $v );
		if( !$body && preg_match("/^\w+(-\w+)*:\s*/", $v) ){
			$cmd = preg_replace( "/^(\w+(-\w+)*:\s*)(.*$)/", "$1", $v );
			$cmd = trim( $cmd );
			$msg[$cmd][] = $v;
			}
			elseif( !$body && preg_match("/^from\s+-\s+/i", $v) ){
				$cmd = preg_replace( "/^(from\s+-\s+)(.*$)/i", "$1", $v );
				$cmd = trim( $cmd );
				$msg[$cmd][] = $v;
				}
			elseif( !$body && preg_match("/^\s+/", $v) ){
				$cnt = count( $msg[$cmd] ) - 1;
				$msg[$cmd][$cnt] .= "\n$v";
				}
			elseif( preg_match("/^--|<!doctype|<html/i", $v) ){
				$body = true;
				$cmd = "body";
				if( !isset($msg[$cmd]) ){ $msg[$cmd] = array(); $cnt = 0; }
					else { $cnt = count( $msg[$cmd] ) - 1; }

				if( isset($msg[$cmd][$cnt]) ){ $msg[$cmd][$cnt] .= "\n$v"; }
					else { $msg[$cmd][$cnt] = "\n$v"; }
				}
			else {
				if( preg_match("/from:/i", $cmd) && !preg_match("/@/", $v) ){
					$cmd = "body";
					}

				if( !isset($msg[$cmd]) ){ $msg[$cmd] = array(); $cnt = 0; }
					else { $cnt = count( $msg[$cmd] ) - 1; }

				if( isset($msg[$cmd][$cnt]) ){ $msg[$cmd][$cnt] .= "\n$v"; }
					else { $msg[$cmd][$cnt] = "\n$v"; }
				}
		}

	$this->debug->out();
	return $msg;
}
################################################################################
#	count(). Return how many messages there are in the mboxes array.
################################################################################
function count(){ return $this->num_mboxes; }
################################################################################
#	clear(). Clears the mail boxes.
################################################################################
function clear(){ $this->mboxes = array(); }
################################################################################
#	put(). Put a message back into the mail boxes.
################################################################################
function put( $msg )
{
	$this->mboxes = array();
	$this->mboxes[] = $msg;
	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['files']) ){ $GLOBALS['classes']['files'] = new class_files(); }

?>
