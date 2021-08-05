<?php
#
#	$lib is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	$lib = getenv( "my_libs");
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
#	Name					Company					Date
#	---------------------------------------------------------------------------
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:28:17.25 
#		These classes are now under the MIT License.  Any and all works
#		whether derivatives or extensions should be sent back to markem@sim1.us.
#		In this way, anything that makes these routines better
#		can be incorporated into them for the greater good
#		of mankind.  All additions and who made them should be
#		noted here in this file OR in a separate file to be called
#		the HISTORY.DAT file since, at some point in the future,
#		this list will get to be too big to store within the class
#		itself.  If there is a standard on such things - see the
#		MIT license file for details.  If you do not agree with the
#		license - then do NOT use these routines in any way, shape,
#		or form.  Failure to do so or using these routines in whole
#		or in part - constitutes a violation of the MIT licensing
#		terms and can and will result in prosecution under the law.
#
#	_MY_ Legal Statement follows:
#
#		<NAME OF PRODUCT>. <A SHORT STATEMENT OF WHAT IT DOES>
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the MIT License.
#
#		Here is the standard MIT disclaimer which should be included
#		in the program:
#
#--------------------------------------------------------------------------------
#
#	Copyright (c) <year> <copyright holders>
#	
#	Permission is hereby granted, free of charge, to any person obtaining a copy
#	of this software and associated documentation files (the "Software"), to deal
#	in the Software without restriction, including without limitation the rights
#	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
#	copies of the Software, and to permit persons to whom the Software is
#	furnished to do so, subject to the following conditions:
#	
#	The above copyright notice and this permission notice shall be included in all
#	copies or substantial portions of the Software.
#	
#	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
#	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
#	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
#	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
#	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
#	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
#	SOFTWARE.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows:
#
#		CLASS_EMAIL.PHP. A class to handle working with email.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the MIT License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
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
	$c = 0;

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
#	split(). Splits an email file into separate files in a subfolder.
#	NOTES	:	If no subfolder name is given then a random one is created
#		automatically.
################################################################################
function split( $file, $subfolder=null )
{
	$c = 0;
	$gb = 8589934592;
	if( is_null($subfolder) ){
		$curdir = dirname( __FILE__ );
		$subfolder = "$curdir/email-" . uniqid( rand() );
		if( !file_exists($subfolder) ){
			mkdir( $subfolder );
			sleep( 3 );
			chmod( $subfolder, 0777 );
			sleep( 3 );
			}
		}
		else {
			list( $g, $b ) = $this->get_files( $k, null, false );
			$c = count( $g );
			}

	if( ($fp = fopen($file, "r")) === false ){
		die( "Could not open : $file" );
		}

	$out = [];
	while( !feof($fp) ){
		if( ($buf = fread($fp, $gb)) != false ){
			$info = explode( "\n", $buf );
			foreach( $info as $k=>$v ){
				if( preg_match("/^from\s+-\s+\w+\s+\w+\s+\d+\s+\d+:\d+:\d+\s+\d+/i", $v) ){
					if( count($out) > 0 ){
						$out = implode( "\n", $out );
						$out_file = "$subfolder/" . $c++ . ".eml";
						file_put_contents( $out_file, $out );
						sleep( 3 );
						chmod( $out_file, 0777 );
						sleep( 3 );
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
	sleep( 3 );
	chmod( $out_file, 0777 );
	sleep( 3 );
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
