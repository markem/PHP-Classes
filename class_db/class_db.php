<?php

	include_once( "../class_debug.php" );
################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_db();
#
#-Description:
#
#	Rewrite of the database class
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:26:33.79 
#	---------------------------------------------------------------------------
#	This code is now under the MIT License.
#
#END DOC
################################################################################
class class_db
{
	private $debug = false;

	private $mysqli = null;
#
#	Put your encrypted passwords here. Using my encrypt & decrypt found in this
#	file.
#
	private $host = <Put your ENCRYPTED password here>;
	private $uid = <Put your ENCRYPTED UID here>;
#
#	Example of an encrypted password:
#
#		"3x4834734941414141414141414379764b7a793842414676353942594541414141";
#
	private $pwd = <Put your ENCRYPTED PWD here>;
	private $db = <Put your ENCRYPTED DB here>;
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
	$args = func_get_args();
	$this->debug = new class_debug( func_get_args() );
	$this->debug->in();

	$host = $uid = $pwd = $db = null;

	foreach( $args as $k=>$v ){
		$a = explode( '=', $v );
		if( preg_match("/host/i", $a[0]) ){ $host = $a[1]; }
			elseif( preg_match("/uid/i", $a[0]) ){ $uid = $a[1]; }
			elseif( preg_match("/pwd/i", $a[0]) ){ $pwd = $a[1]; }
			elseif( preg_match("/db/i", $a[0]) ){ $db = $a[1]; }
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
		$site_path = implode( "/", $b );

		$fh = fopen( "$site_path/logs/mysql.log", "a" );

		fwrite( $fh, "Could not connect: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error );

		fclose( $fh );

		die( "PostgresSQL : Could not connect to remote database - Please try this again.  Thank you." );
		}

	$this->debug->out();
}
################################################################################
#	dosql(). Do the SQL command.  Check for {...} strings and replace them.
#		Put {} around fields you want extracted from $_SESSION.
################################################################################
function dosql( $sql, $flag = false )
{
	$this->debug->in();
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
		$this->debug( "ERROR : (" .  $this->mysqli->errno . ")\n" . $this->mysqli->error . "\n\n" );
		$this->debug( "SQL = $sql\n", true );
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
					if( trim($v) == "into" ){ $table = $a[$k+1]; break; }
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
							$this->debug->out();
							return $rec['max'];
							}
						}
#
#	Oh well.  We didn't find an ID field.  Just return.
#
					$this->debug->out();
					return null;
					}
#
#	Find out the max id number.
#
				$sql = "select max($field) as max from $table";
				$rec = $this->dosql( $sql, true );
				$this->debug->out();
				return $rec['max'];
				}
			}
#
#	Else - just return the id number.
#
		$this->debug->out();
		return $id;
		}

	if( preg_match("/delete/i", $sql) ){
		$this->debug->out();
		return null;
		}

	if( !is_object($res) ){
		$this->debug->out();
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
		$this->debug->out();
		return (isset($ary[0]) ? $ary[0] : null);
		}

	$this->debug->out();

	return $ary;
}
################################################################################
#	decrypt(). Decrypt the incoming string. "3x" = encrypted.
################################################################################
function decrypt( $s )
{
	$this->debug->in();

	$cb = chr(2);
	if( substr($s,0,2) == "3x" ){
		$a = gzinflate( substr(base64_decode(pack("H*", substr($s,2))), 10, -8 ) );
		if( preg_match("/$cb/", $a) ){ $a = explode( $cb, $a ); }
		$this->debug->out();
		return $a;
		}
		else if( substr($s,0,2) == "0x" ){
			$this->debug->out();
			return pack("H*", substr($s,2));
			}
		else { $this->msg($s); }

	$this->debug->out();

	return null;
}
################################################################################
#	encrypt().  Encrypt the incoming string
################################################################################
function encrypt( $s )
{
	$this->debug->in();

	$cb = chr(2);
	if( is_array($s) ){ $s = implode($cb, $s ); }

	$this->debug->out();

	return "3x" . bin2hex( base64_encode(gzencode($s)) );
}
################################################################################
#	muck(). Generate a muck.
################################################################################
function muck( $len=20, $opt="A:a:n:s" )
{
	$this->debug->in();

	$s = "";
	$c = "";
	$a = explode( ':', ((strlen(trim($opt)) > 0) ? $opt : "A:a:n:s") );

	foreach( $a as $k=>$v ){
		if( $v === 'A' ){ $c .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; }
		if( $v === 'a' ){ $c .= "abcdefghijklmnopqrstuvwxyz"; }
		if( $v === 'n' ){ $c .= "0123456789"; }
		if( $v === 's' ){ $c .= "!@#$%^&*_-+="; }
		}

	$j = mt_rand() % 52;
	$s = substr($c, $j, 1);

	for( $i = 0; $i < $len; $i++ ){
		$j = mt_rand() % strlen($c);
		$s .= substr($c, $j, 1);
		}

	$this->debug->out();

	return $s;
}
################################################################################
#	parse().  Encode and Decode passwords.
################################################################################
function parse($action, $string, $string_2=null )
{
	$this->debug->in();

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
				$this->debug->out();
				return true;
				}
				else { return false; }
			}

	$this->debug->out();

	return $output;
}
################################################################################
#	msg().  Displays a message.
################################################################################
function msg( $s,$color='white' )
{
	$this->debug->in();

	echo "<span style='font:12pt arial;color:$color;'>$s</span>";

	$this->debug->out();
}
################################################################################
#	curse() . Convert an array into a bin2hex string.
################################################################################
function curse( $a )
{
	$this->debug->in();

	$s = "";
	if( is_array($a) ){
		foreach( $a as $k=>$v ){
			if( is_array($v) ){ $s .= "a:" . $this->curse( $v ) . ","; }
				else if ( is_object($v) ){ $s .= "o:" . base64_encode($v) . ","; }
				else { $s .= "s:" . base64_encode($a) . ","; }
			}
		}
		else {
			if ( is_object($a) ){ $s .= "o:" . base64_encode($a) . ","; }
				else { $s = "s:" . base64_encode($a) . ","; }
			}

	$s = substr( $s, 0, -1 );

	$this->debug->out();

	return "a:" . bin2hex($s);
}
################################################################################
#	uncurse().  Convert the bin2hex string back into an array.
################################################################################
function uncurse( $a )
{
	$this->debug->in();

	if( substr($a,0,1) == "s" ){
		$this->debug->out();
		return base64_decode(substr($a,2));
		}
		if( substr($a,0,1) == "o" ){
			$this->debug->out();
			return base64_decode(substr($a,2));
			}

	if( substr($a,0,1) == "a" ){
		$a = pack( "H*", substr( $a, 2) );
		$b = explode( ',', $a );

		$s = array();
		foreach( $b as $k=>$v ){
			if( substr($v, 0, 1) == "a" ){ $s[] = $this->uncurse( substr($v, 2) ); }
				else { $s[] = base64_decode( substr($v, 2) ); }
			}
		}

	$this->debug->out();
}
################################################################################
#	__destruct(). Close the mysqli link.
################################################################################
function __destruct()
{
	$thread_id = $this->mysqli->thread_id;

	mysqli_kill( $this->mysqli, $thread_id );
	mysqli_close( $this->mysqli );
}

}

if( !isset($GLOBALS['classes']) ){ global $classes; }
if( !isset($GLOBALS['classes']['db']) ){ $GLOBALS['classes']['db'] = new class_db(); }

?>

