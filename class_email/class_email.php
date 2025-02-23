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
		include_once( "$lib/class_files.php" );
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
#	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
#	---------------------------------------------------------------------------
#		This is now under the BSD Three Clauses Plus Patents License.
#		See the BSD-3-Patent.txt file.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows:
#
#		CLASS_EMAIL.PHP. A class to handle working with email.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_email
{
	private $cf = null;
	private $debug = null;
	private $mboxes = null;
	private $num_mboxes = null;
	private $temp_dir = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$this->cf = $GLOBALS['classes']['files'];
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['email']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['email']; }
}
################################################################################
#	init(). Because we need to be able to call this to get started from
#		multiple locations.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->temp_dir = "c:/temp/mail";

	$this->mboxes = array();
	$this->debug->out();
}
################################################################################
#	load(). Load in a mail file and then break it up into messages
################################################################################
function load( $file )
{
	$this->debug->in();
	$c = 0;

	if( !file_exists($file) ){
		$this->debug->msg( "DIE : The file does NOT exist\nFile : $file" );
		}

	$a = file_get_contents( $file );
	$b = explode( "\n", $a );
	unset( $a );
#	$this->dump( "Contents of $file", $b );

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
		$this->debug->msg( "DIE : No message number given" );
		}

	if( ($num < 0) || ($num >= $this->num_mboxes) ){
		$this->debug->msg( "DIE : Number given is wrong. NUM = $num" );
		}

	$cmd = "";
	$loc = 0;
	$msg = array();
	$mbox = $this->mboxes[$num];
	if( strlen(trim($mbox)) < 1 ){ return false; }
#
#	Remove all control-m's
#
	$mbox = str_replace( "", "", $mbox );

	$body = false;
	$mbox = explode( "\n", $mbox );
	foreach( $mbox as $k=>$v ){
#
#	Is this a command?
#
#		echo "K = $k, V = $v\n";
		if( !$body && preg_match("/^\w+(-\w+)*:\s*/", $v) ){
			$cmd = preg_replace( "/^(\w+(-\w+)*:\s*)(.*$)/", "$1", $v );
#			$this->dump( "CMD = ", $cmd );
			$cmd = trim( $cmd );
			$msg[$cmd][] = $v;
			}
			elseif( !$body && preg_match("/^from\s+-\s+/i", $v) ){
				$cmd = preg_replace( "/^(from\s+-\s+)(.*$)/i", "$1", $v );
				$cmd = trim( $cmd );
				$msg[$cmd][] = $v;
				}
			elseif( !$body && preg_match("/^reply-to\s+-\s+/i", $v) ){
				$cmd = preg_replace( "/^(reply-to\s+-\s+)(.*$)/i", "$1", $v );
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
					else if( is_array($msg[$cmd]) ){
#						$this->dump( $cmd, $msg[$cmd] );
						$cnt = count( $msg[$cmd] ) - 1;
						}

				if( isset($msg[$cmd][$cnt]) ){ $msg[$cmd][$cnt] .= "\n$v"; }
					else if( is_array($msg[$cmd]) ){ $msg[$cmd][$cnt] = "\n$v"; }
				}
			else {
				if( preg_match("/^from:/i", $cmd) && !preg_match("/@/", $v) ){
					$cmd = "body";
					}

				if( !isset($msg[$cmd]) ){ $msg[$cmd] = array(); $cnt = 0; }
					else if( is_array($msg[$cmd]) ){ $cnt = count( $msg[$cmd] ) - 1; }

				if( isset($msg[$cmd][$cnt]) ){ $msg[$cmd][$cnt] .= "\n$v"; }
					else if( is_array($msg[$cmd]) ){ $msg[$cmd][$cnt] = "\n$v"; }
				}
		}
#
#	Ok - yet another FIRST. Got a message that has TWO FROM: lines in it.
#	The second one is from the message which was being sent to someone (Scott).
#	Only keep the first entry. Get rid of the rest of them.
#
	while( count($msg['From:']) > 1 ){
#
#	Get how many there are
#
		$a = count( $msg['From:'] );
#
#	Now go through and pop off all of the extra FROM:s
#
		for( $i=1; $i<$a; $i++ ){
			array_pop( $msg['From:'] );
			}
		}
#
#	Ok - a new first. We have to make sure FROM: has the '<' and '>'
#
	if( !preg_match("/</", $msg['From:'][0]) ){
		$a = explode( ' ', $msg['From:'][0] );
		foreach( $a as $k1=>$v1 ){
			if( preg_match("/@/", $v1) ){
				$msg['From:'][0] = "From: <$v1>";
				}
			}
		}
#
#	If there isn't a "Reply-To:" use the From: entry.
#	If there STILL isn't a Reply-To: - try the In-Reply-To:
#	ALWAYS use the last entry. There might be more than one '<'.
#
	$this->dump( "From: ", $msg['From:'] );
	if( !isset($msg["Reply-To:"]) ){
		if( preg_match("/</", $msg['From:'][0]) ){
			$a = explode( '<', $msg['From:'][0] );
			}
			else if( preg_match("/@/", $msg['From:'][0]) ){
				$a = explode( ' ', $msg['From:'][0] );
				}
			else if( isset($msg['In-Reply-To:']) ){
				if( preg_match("/</", $msg['In-Reply-To:'][0]) ){
					$a = explode( '<', $msg['In-Reply-To:'][0] );
					}
					else if( preg_match("/@/", $msg['In-Reply-To:'][0]) ){
						$a = explode( ' ', $msg['In-Reply-To:'][0] );
						}
				}

		$msg['Reply-To:'] = array( "Reply-To: <" . $a[count($a)-1] );
		}

	$this->dump( "Reply-To:", $msg['Reply-To:'] );
#
#	Ok, now we have to look and see if the From: has no '<' or '>'.
#	Make a $from variable to do all of these tests.
#	Make a $reply_to variable to do all tests.
#
	$from = $msg['From:'][0];
	$reply_to = $msg['Reply-To:'][0];
#
#	First question - do we have an '@' in the FROM: variable?
#
	if( !preg_match("/@/", $from) ){
#
#	Do we have an '@' in the REPLY-TO: variable?
#
		if( preg_match("/@/", $reply_to) ){
#
#	Then break it apart
#
			$a = explode( ' ', $reply_to );
#
#	And set the FROM: variable to the new address
#
			$from = "From: " . $a[(count($a)-1)];
			if( !preg_match("/@/", $from) ){
				die( "From: does not have an address. $from" );
				}
			}
			else {
				die( "Both FROM: and REPLY-TO: do not have a " .
					"correct email address. FROM = $from, REPLY-TO = $reply_to." );
				}
		}
#
#	If, as we have found, there are FROM: lines with no space after the
#	colon - we need to put that in.
#
	if( preg_match("/from:\w/i", $from) ){
		$from = str_replace( ":", ": ", $from );
		}
#
#	If there are no "<" and ">", then we have to put them in. In this
#	case, for unknown reasons, you may get "From: My name is Joe abc@def.com
#	Fix it.
#
	if( !preg_match("/</", $from) ){
		$a = explode( ' ', $from );
		$a[1] = "<" . $a[count($a)-1] . ">";
		$from = implode( ' ', $a );
		}
#
#	Now try to remove weird stuff
#	First is - get rid of multiple email addresses. We only want the first one.
#
	if( preg_match("/,/", $from) ){
		$a = explode( ',', $from );
		foreach( $a as $k1=>$v1 ){
			if( preg_match("/@/", $v1) ){
#
#	Are there double quotes in there?
#
				if( preg_match('/"/', $v1) ){
					$v1 = str_replace( '"', '', $v1 );
					}
#
#	Does the email address have the '<' and '>' in it?
#
				if( !preg_match("/</", $v1) ){
					$a = explode( " ", $v1 );
					$v1 = "From: " . $a[count($a)-1];
					}

				$from = $v1;
#
#	If there is a FROM: in the line - then we are through
#
				if( !preg_match("/from:/i", $v1) ){
					$from = "From: $v1";
					}

				break;
				}
			}
		}

	$msg['From:'][0] = $from;
	$msg['Reply-To:'][0] = $reply_to;
#exit;
#
#	First, check out the Return-Path. If there is something wrong with the return address
#	we have to correct it.
#
	if( isset($msg['Return-Path:']) ){
		foreach( $msg['Return-Path:'] as $k=>$v ){
			if( preg_match("/=/", $v) ){
				$a = explode( '=', $v );
				$b = "Return-Path: <" . $a[count($a)-1];
				$msg['Return-Path:'][$k] = $b;
				}
			}
		}
		else if( isset($msg['From:']) ){
			$a = explode( '<', $msg['From:'][0] );
			$msg['Return-Path:'] = array( "Return-Path: <" . $a[count($a)-1] );
			}

#	$this->dump( "Return-Path is NOW: ", $msg['Return-Path:'] );
#
#	Because Received is so complex - we are not going to do anything about it.
#	$this->dump( "Message is = ", $msg );
	$this->debug->out();
	return $msg;
}
################################################################################
#	split(). Splits an email file into separate files in a subfolder.
#	NOTES	:	If no subfolder name is given then a random one is created
#		automatically.
################################################################################
function split( $file, $subfolder=null )
{
	$c = 0;
	$buffer_size = pow( 2, 19 );

	if( is_null($subfolder) ){
		$curdir = dirname( __FILE__ );
		$subfolder = "$curdir/email-" . uniqid( rand() );
		$subfolder = str_replace( "\\", "/", $subfolder );
#		echo "SUBFOLDER #2 = $subfolder\n";
		if( !file_exists($subfolder) ){
			mkdir( $subfolder, 0777, true );
			sleep( 3 );
			}
		}
		else {
			list( $g, $b ) = $this->cf->get_files( $subfolder, null, false );
			$c = count( $g );
			}

	$subfolder = str_replace( "\\", "/", $subfolder );
#	echo "SUBFOLDER #1 = $subfolder\n";
	if( strlen($subfolder) < 5 ){ exit; }

	if( ($fp = fopen($file, "r")) === false ){
		die( "Could not open : $file" );
		}

	$out = [];
	while( !feof($fp) ){
		if( ($buf = fread($fp, $buffer_size)) != false ){
			$info = explode( "\n", $buf );
			foreach( $info as $k=>$v ){
				if( preg_match("/^from\s+-\s+\w+\s+\w+\s+\d+\s+\d+:\d+:\d+\s+\d+/i", $v) ){
					if( count($out) > 0 ){
						$out = implode( "\n", $out );
						$out_file = "$subfolder/" . $c++ . ".eml";
						file_put_contents( $out_file, $out );
						chmod( $out_file, 0777 );
						}

					$out = [];
					}

				$out[] = $v;
				}
			}
		}

	$out = implode( "\n", $out );
	$out_file = "$subfolder/" . $c++ . ".eml";
	file_put_contents( $out_file, $out );
	sleep( 1 );
	chmod( $out_file, 0777 );
	sleep( 1 );
	fclose( $fp );

	return $subfolder;
}
################################################################################
#	search(). Search an email file for a given string.
#	Notes	:	-1 = No file given
#				-2 = Could not open the file
#				TRUE = Found the string in the file.
#				FALSE = Did NOT find the string in the file.
################################################################################
function search( $file=null, $preg=null )
{
	if( is_null($file) ){ return -1; }
	if( is_null($preg) ){ $preg = "/^from/i"; }

	$buf = "";
	$len = strlen( $preg ) * 1000;
	if( ($fp = fopen($file, "r")) === false ){ return -2; }

	$c = 0;
#	echo "Opening $file and reading it.\n";
	while( !feof($fp) ){
#		echo "Reading block : $c\n"; $c++;
		$buf .= fread( $fp, $len );
		if( preg_match($preg, $buf) ){
			fclose( $fp );
			return true;
			}

		$buf = substr( $buf, ($len / 2), $len );
		}

	fclose( $fp );
	return false;
}
################################################################################
#	rem_dup_msgs(). Remove duplicate messages. Leave the first one only.
################################################################################
function rem_dup_msgs( $dir )
{
	if( is_null($dir) || !file_exists($dir) ){ return false; }
#
#	Get the files
#
	list( $fg, $fb ) = $this->cf->get_files( $dir );
#
#	Get the certificates. OK. It turns out that the sha1_file()
#	function is the ONLY ONE which can check to make sure the
#	files are the same.
#
	$certs = [];
	$max_files = [];
	foreach( $fg as $fk=>$fv ){
		echo "Looking at : FK = $fk, FV = $fv\n";
		$basename = basename( $fv );
		if( is_numeric($basename) ){
			$sha = sha1_file( $fv );
			if( isset($certs[$sha]) ){
				$certs[$sha][] = $fv;
				}
				else {
					$certs[$sha] = [];
					$certs[$sha][] = $fv;
					}
			}
#
#--------------------------------------------------------------------------------
#	Use the realpath as the entry for the maximum number
#	the list of files has gotten to. Then we can renumber
#	the directory listing easily.
#--------------------------------------------------------------------------------
#
#	The problem with the realpath command is that it gives the ENTIRE
#	path back. We want the basename to have the file in the directory
#	while the path is everything else.
#
		$path = realpath( $fv );
		$path = str_replace( "\\", "/", $path );
		$a = explode( "/", $path );
		array_pop( $a );
		$path = implode( "/", $a );
#
#	Ok - FIRST - we have to make sure the basename is set
#
		if( is_null($basename) ){ die("Basename is NULL"); }
		echo "Basename = $basename\n";
		echo "Path = $path\n";
#
#	Ok - first - is the entry set?
#
		if( !is_numeric($basename) ){ continue; }
		if( !isset($max_files[$path]) ){ $max_files[$path] = $basename; }
#
#	Yes it is. So now get what the current number is and then
#	compare it to the new number. If the new number is bigger
#	then we replace the older number with the newer one.
#
			else {
				$old = ( is_numeric($max_files[$path]) ? $max_files[$path] : 0 );
				$new = ( is_numeric($basename) ? $basename : 0 );
				if( $new > $old ){ $max_files[$path] = $basename; }
				}
		}
#
#	$this->dump( "Max_Files", $max_files ); exit;
#

#
#	Sort the CERTS array
#
	foreach( $certs as $k=>$v ){
		if( count($v) > 1 ){
			$a = [];
#
#	Ok, we have a list of filenames. These names are always numbers.
#	So get the basename, use it as the new key and the list is automatically
#	created.
#
			foreach( $v as $k1=>$v1 ){
				$a[basename($v1)] = $v1;
				}
#
#	Ok - so now we should only have to make the certificate array to $a
#
			ksort( $a );
			$certs[$k] = [];
			foreach( $a as $k1=>$v1 ){
				$certs[$k][] = $v1;
				}
			}
		}
#
#	Ok - so now we need to delete the extra files
#
#	$this->dump( "Certs", $certs );
	$del_cnt = 0;
	$kep_cnt = 0;
	foreach( $certs as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			if( $k1 > 0 ){
				$del_cnt++;
				unlink( $v1 );
				echo "Deleting : $v1\n";
				}
				else {
					$kep_cnt++;
					echo "Keeping : $v1\n";
					}
			}
		}

	echo "\nWe DELETED $del_cnt files\n";
	echo "We are keeping $kep_cnt files\n\n";
#
#	Ok.  We have deleted the files. NOW we need to renumber the files
#
	$a = $max_files;
	foreach( $a as $k=>$v ){
		$list = [];
		echo "K = $k, V = $v\n"; 
		list( $fg, $fb ) = $this->cf->get_files( $k );
		foreach( $fg as $k1=>$v1 ){
			$path = dirname( $v1 );
			$basename = basename( $v1 );
			if( !is_numeric($basename) ){ continue; }
			echo "After : K1 = $k1, V1 = $v1\n";
			$list[] = $basename;
			}

		if( count($list) > 0 ){
			echo "Path = $path\n";

			sort( $list );
			$this->dump( "list", $list );

			$new_list = array_flip( $list );
			$this->dump( "New List", $new_list );

			foreach( $new_list as $k2=>$v2 ){
				$v2++;
				if( $k2 == $v2 ){ continue; }
				echo "rename '$path/$k2' to '$path/$v2'\n";
				rename( "$path/$k2", "$path/$v2" );
				}
			}
		}

	return true;
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
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", $num );
################################################################################
function dump( $title=null, $arg=null )
{
	$this->debug->in();
	echo "\n--->Entering DUMP\n";

	$opts = array( "function", "line", "file", "class", "object", "type", "args" );

	if( is_null($title) ){ return false; }
	if( is_null($arg) ){ return false; }

	$title = trim( $title );
#
#	Get the backtrace
#
	$dbg = debug_backtrace();
#	print_r( $dbg ); echo "\n"; exit;
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
	if( !isset($GLOBALS['classes']['email']) ){
		$GLOBALS['classes']['email'] = new class_email();
		}

?>
