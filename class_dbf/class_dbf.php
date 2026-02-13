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
#	class_dbf();
#
#-Description:
#
#	Rewrite of the database class. Ok. Here is how this works:
#
#	1.	Write a small program that loads this class and then
#		passes to the class the open information. This will
#		return the encrypted information.
#
#		So your HOST, your UID, your PWD, and your Database (DB).
#
#	2.	You then can use these by passing them in to the INIT()
#		function which will then insert them into the proper
#		variables as $host, $uid, $pwd, and $db. Which then
#		can be used through out the rest of the program.
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
#	Mark Manning			Simulacron I			1992-NOW
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
#		CLASS_DBF.PHP. A class to handle working with databases.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_dbf
{
	private $log_file = null;

	private $mysqli = null;
#
#	Put your encrypted passwords here. Using my encrypt & decrypt found in this
#	file.
#
	private $host = "<Put your ENCRYPTED host password here>";
	private $uid = "<Put your ENCRYPTED userID here>";
#
#	Example of an encrypted password:
#
#		"3x4834734941414141414141414379764b7a793842414676353942594541414141";
#
	private $pwd = "<Put your ENCRYPTED PWD here>";
	private $db = "<Put your ENCRYPTED DB here>";
#
#	Usually the DB is something like mysql, sqllite, etc....
#
#	The above values are used here like this. Look up the MYSQLI command
#	on the PHP website.
#
#	$this->mysqli = new mysqli( $host, $uid, $pwd, $db );
#


################################################################################
#	__construct().  Things to do when we start up.
#
#	Arguments:	Arguments can be in ANY order.
#
#		"host=AAAA"	Set the host
#		"uid=BBBB"	Set the uid
#		"pwd=CCCC"	Set the password
#		"db=DDDD"	Set the Database
#		"d(ebug)"	Turn on Debug
#		"s(ave)"	Turn on writing debug info to a file
#
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['db']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['db']; }
}
################################################################################
#	init(). Init function thus making it easier to redo the host, uid, pwd,
#		and db variables.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$host = $uid = $pwd = $db = null;

	if( is_array($args) ){
		foreach( $args as $k=>$v ){
			$a = explode( '=', $v );
			if( preg_match("/host/i", $a[0]) ){ $host = $a[1]; }
				elseif( preg_match("/uid/i", $a[0]) ){ $uid = $a[1]; }
				elseif( preg_match("/pwd/i", $a[0]) ){ $pwd = $a[1]; }
				elseif( preg_match("/db/i", $a[0]) ){ $db = $a[1]; }
			}
		}

	if( is_null($host) ){ $host = $this->decrypt( $this->host ); }
	if( is_null($uid) ){ $uid = $this->decrypt( $this->uid ); }
	if( is_null($pwd) ){ $pwd = $this->decrypt( $this->pwd ); }
	if( is_null($db) ){ $db = $this->decrypt( $this->db ); }

	$this->mysqli = new mysqli( $host, $uid, $pwd, $db );

	if( $this->mysqli->connect_errno ){
		$a = realpath( __FILE__ );
		$b = explode( "\\", $a );
		array_pop( $b );
		array_pop( $b );
		array_pop( $b );
		$this->log_file = implode( "/", $b ) . "/logs";
		if( !file_exists($this->log_file) ){ mkdir( $this->log_file, "0777" ); }

		$this->msg( "Opening \$this->log_file/mysql.log\n" );

		$fh = fopen( "$this->log_file/mysql.log", "a" );

		fwrite( $fh, "Could not connect: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error );

		fclose( $fh );

		echo "Closing \$this->log_file/mysql.log\n";
		die( "MySQL : Could not connect to database - Please try this again.  Thank you." );
		}
}
################################################################################
#	dosql(). Do the SQL command.  Check for {...} strings and replace them.
#		Put {} around fields you want extracted from $_SESSION.
################################################################################
function dosql( $sql, $flag = false )
{
#
#	Handle the {...} items
#
	if( preg_match("/\{/", $sql) ){
		$a = explode( "{", $sql );
		foreach( $a as $k=>$v ){
			if( preg_match("/\}/", $v) ){
				$b = explode( "}", $v );
				$c = isset($_SESSION[$b[0]]) ? $_SESSION[$b[0]] : "";
				$b[0] = "unhex('" . bin2hex($c) . "')";
				$a[$k] = implode( " ", $b );
				}
			}

		$sql = implode( " ", $a );
		}
#
#	Add this to the log
#
	$cmd = "insert into log (date,entry) values (NOW(), unhex('" . bin2hex($sql) . "'))";
	$this->mysqli->query( $cmd );
#
#	Do the SQL statement
#
	$res = $this->mysqli->query( "SET SESSION SQL_BIG_SELECTS=1;" );
	$res = $this->mysqli->query( $sql );
	if( !$res ){
		echo "ERROR : (" .  $this->mysqli->errno . ")\n" . $this->mysqli->error . "\n\n";
		echo "SQL = $sql\n";
		}
#
#	In mysqli - you don't always get the last inserted id number.
#	This is a KNOWN issue with MySQL which they have not fixed.
#
	if( preg_match("/insert /i", $sql) ){
		if( ($id = $this->mysqli->insert_id) < 1 ){
#
#	If we STILL do not have an id number, we have to take drastic action.
#
			if( ($id = mysqli_insert_id($this->mysqli)) < 1 ){
#
#	Find the INTO part of the statement.  The table name is right after it.
#
				$a = explode( ' ', strtolower($sql) );
				foreach( $a as $k=>$v ){
					if( preg_match("/^into$/i", trim($v)) ){ $table = $a[$k+1]; break; }
					}
#
#	Now that we know which table it is - get the columns and find the primary key.
#
				$field = "";
				$sql = "show columns from $table";
				$recs = $this->dosql( $sql );
				foreach( $recs as $k=>$v ){
					if( $v['Key'] != "" && $v['Extra'] == "auto_increment" ){
						$field = $v['Field'];
						break;
						}
					}
#
#	If we don't find a primary field with auto_increment then
#	look for one with the letters "id" in it.
#
				if( strlen($field) < 1 ){
					foreach( $recs as $k=>$v ){
						if( preg_match("/id/i", $v['Field']) ){
							$sql = "select max($v[Field]) as max from $table";
							$rec = $this->dosql( $sql, true );
							return $rec['max'];
							}
						}
#
#	Oh well.  We didn't find an ID field.  Just return.
#
					return null;
					}
#
#	Find out the max id number.
#
				$sql = "select max($field) as max from $table";
				$rec = $this->dosql( $sql, true );
				return $rec['max'];
				}
			}
#
#	Else - just return the id number.
#
		return $id;
		}

	if( preg_match("/delete/i", $sql) ){
		return null;
		}

	if( !is_object($res) ){
		return null;
		}

	$cnt = -1;
	$ary = array();
	$res->data_seek(0);
	while( $row = $res->fetch_assoc() ){
		$cnt++;
		foreach( $row as $k=>$v ){ $ary[$cnt][$k] = trim($v); }
		}

	if( $flag ){
		return (isset($ary[0]) ? $ary[0] : null);
		}

	return $ary;
}
################################################################################
#	decrypt(). Decrypt the incoming string. "3x" = encrypted.
################################################################################
function decrypt( $s )
{
	$cb = chr(2);
	if( substr($s,0,2) == "3x" ){
		$a = gzdecode( substr(base64_decode(pack("H*", substr($s,2))), 10, -8 ) );
#		$a = gzinflate( substr(base64_decode(pack("H*", substr($s,2))), 10, -8 ) );
		if( preg_match("/$cb/", $a) ){ $a = explode( $cb, $a ); }
		return $a;
		}
		else if( substr($s,0,2) == "0x" ){
			return pack("H*", substr($s,2));
			}
		else { $this->msg($s); }


	return null;
}
################################################################################
#	encrypt().  Encrypt the incoming string
#	Notes:	$CB means "Control-B".
################################################################################
function encrypt( $s )
{
	$cb = chr(2);
	if( is_array($s) ){ $s = implode($cb, $s ); }

	return "3x" . bin2hex( base64_encode(gzencode($s)) );
}
################################################################################
#	muck(). Generate a muck.
#	NOTES:	$chars added to allow people to exercise what they want as their
#			characters.
#			You can set $opt equal to NULL to not use it.
################################################################################
function muck( $len=20, $opt="A:a:n:s", $chars=null )
{
	$s = "";
	$c = "";
	$a = explode( ':', ((strlen(trim($opt)) > 0) ? $opt : "A:a:n:s") );

	if( !is_null($chars) ){ $c = $chars; }
	if( !is_null($opt) ){
		foreach( $a as $k=>$v ){
			if( $v === 'A' ){ $c .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; }
			if( $v === 'a' ){ $c .= "abcdefghijklmnopqrstuvwxyz"; }
			if( $v === 'n' ){ $c .= "0123456789"; }
			if( $v === 's' ){ $c .= "!@#$%^&*_-+="; }
			}
		}

	if( strlen($c) < 1 ){
		die( "ERROR : Call to function MUCK() with no characters selected." );
		}
#
#	Was : $j = mt_rand() % 52;	MEM 2021-05-05
#
#	Get first character of the muck. This is in case someone is trying
#	to be cute and send a length of minus some number.
#
	$j = mt_rand() % strlen($c);
	$s = substr($c, $j, 1);
#
#	Get the rest of the muck.
#
	for( $i = 1; $i < $len; $i++ ){
		$j = mt_rand() % strlen($c);
		$s .= substr($c, $j, 1);
		}

	return $s;
}
################################################################################
#	parse().  Encode and Decode passwords.
################################################################################
function parse($action, $string, $string_2=null )
{
	$output = false;
	$encrypt_method = "AES-256-CBC";

	// hash
	$key = hash('sha256', 'ou0Plt#&*#Wh3s3LeZgf-nFONIMGW@0L$trl3VAI*' );

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', "ARANDOMIV"), 0, 16);

	if($action == 'encrypt') {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			}
		else if( $action == "compare" ){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			if( trim($output) === trim($string_2) ){
				return true;
				}
				else { return false; }
			}

	return $output;
}
################################################################################
#	msg().  Displays a message.
################################################################################
function msg( $s,$color='white' )
{
	echo "<span style='font:12pt arial;color:$color;'>$s</span>";
}
################################################################################
#	encode() . Convert an array into a bin2hex string.
################################################################################
function encode( $a )
{
	$s = "";
	if( is_array($a) ){
		foreach( $a as $k=>$v ){
			if( is_array($v) ){ $s .= "a:" . $this->encode( $v ) . ","; }
				else if( is_object($v) ){ $s .= "o:" . base64_encode($v) . ","; }
				else if( is_float($v) ){ $s .= "f:" . base64_encode($v) . ","; }
				else if( is_numeric($v) ){ $s .= "n:" . base64_encode($v) . ","; }
				else if( is_bool($v) ){
					$t = (($v === false) ? "FALSE" : "TRUE");
					$s .= "s:" . base64_encode($t) . ",";
					}
				else { $s .= "s:" . base64_encode($v) . ","; }
			}
		}
		else {
			if ( is_object($a) ){ $s .= "o:" . base64_encode($a) . ","; }
				else { $s = "s:" . base64_encode($a) . ","; }
			}
#
#	Remove trailing comma
#
	$s = substr( $s, 0, -1 );

	return "a:" . bin2hex($s);
}
################################################################################
#	decode().  Convert the bin2hex string back into an array.
################################################################################
function decode( $a )
{
#
#	If this is an array (a:) then...
#
	if( substr($a,0,1) == "a" ){
		$a = pack( "H*", substr( $a, 2) );
		$b = explode( ',', $a );

		$s = array();
		foreach( $b as $k=>$v ){
			if( substr($v, 0, 1) == "a" ){ $s[] = $this->decode( substr($v, 2) ); }
				else { $s[] = base64_decode( substr($v, 2) ); }
			}
		}
#
#	else, if this is a string (s:) or an object (o:) then...
#
	return base64_decode(substr($a,2));
}
################################################################################
#	__destruct(). Close the mysqli link.
################################################################################
function __destruct()
{
	if( !mysqli_connect_errno() ){
		$thread_id = $this->mysqli->thread_id;

		mysqli_kill( $this->mysqli, $thread_id );
		mysqli_close( $this->mysqli );
		}
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['db']) ){
		$GLOBALS['classes']['db'] = new class_dbf();
		}

?>

