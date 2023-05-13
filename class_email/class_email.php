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
	$this->cf = $GLOBALS['classes']['files'];

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
		echo "SUBFOLDER #2 = $subfolder\n";
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
	echo "SUBFOLDER #1 = $subfolder\n";
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
	echo "Opening $file and reading it.\n";
	while( !feof($fp) ){
		echo "Reading block : $c\n"; $c++;
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
	list( $g, $b ) = $this->cf->get_files( $dir );
#
#	Get the certificates. OK. It turns out that the sha1_file()
#	function is the ONLY ONE which can check to make sure the
#	files are the same.
#
	$certs = [];
	foreach( $g as $k=>$v ){
		$b = basename( $v );
		if( is_numeric($b) ){ $certs[$v] = sha1_file( $v ); }
		}
#
#	Create the list of files reversed.
#
	$list = [];
	foreach( $certs as $k=>$v ){
		if( !isset($list[$v]) ){ $list[$v] = "$k|"; }
			else { $list[$v] .= "$k|"; }
		}
#
#	Get rid of everything EXCEPT the first file of the duplicate files.
#
	foreach( $list as $k=>$v ){
		$a = explode( "|", $v );
		while( strlen($b = array_shift($a)) < 1 && (count($a) > 0) );
		echo "Keeping : $b\n";
		foreach( $a as $k1=>$v1 ){
			if( !is_null(trim($v1)) && (strlen($v1) > 0) ){
				echo "Unlinking : $v1\n";
				unlink( $v1 );
				}
			}
		}
#
#	Renumber all of the files for slypheed.
#	To do this we first have to get the list of directories.
#
	$dirs = $this->cf->get_dirs( $dir );
#
#	Then we ONLY WANT those files IN that directory.
#
	foreach( $dirs as $k=>$v ){
#
#	Get rid of these two files : .sylpheed_cache and .sylpheed_mark
#
#	if( file_exists("$v/.sylpheed_cache") ){ unlink( "$v/.sylpheed_cache"); }
#	if( file_exists("$v/.sylpheed_mark") ){ unlink( "$v/.sylpheed_mark"); }
#
#	BE SURE to just get the files IN this directory.
#
		list( $g, $b ) = $this->cf->get_files( $k, "/\d+$/", false );
		if( count($g) > 0 ){
			$nums = [];
			foreach( $g as $k1=>$v1 ){
				$nums[$v1] = basename( $v1 );
				}

			sort( $nums, SORT_NUMERIC );
			print_r( $nums );
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
#	Ex:	$this->dump( "NUM", __LINE__, $num );
################################################################################
function dump( $title=null, $line=null, $arg=null )
{
	$this->debug->in();

	if( is_null($title) ){ return false; }
	if( is_null($line) ){ return false; }
	if( is_null($arg) ){ return false; }

	if( is_array($arg) ){
		echo "$title @ Line : $line =\n";
		print_r( $arg );
		echo "\n";
		}
		else {
			echo "$title @ Line : $line = $arg\n";
			}

	$this->debug->out();
	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['email']) ){
		$GLOBALS['classes']['email'] = new class_email();
		}

?>
