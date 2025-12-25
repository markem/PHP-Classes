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

	ini_set( 'memory_limit', -1 );
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

	include_once( "$lib/class_files.php" );
	include_once( "$lib/class_pr.php" );

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_serial();
#
#-Description:
#
#	A class to handle serial ports.
#	
#	Here is the output from doing a MODE COM4 command:
#
#	Status for device COM4:
#	-----------------------
#	    Baud:            19200
#	    Parity:          None
#	    Data Bits:       8
#	    Stop Bits:       1
#	    Timeout:         OFF
#	    XON/XOFF:        OFF
#	    CTS handshaking: OFF
#	    DSR handshaking: OFF
#	    DSR sensitivity: OFF
#	    DTR circuit:     ON
#	    RTS circuit:     ON
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
#	Mark Manning			Simulacron I			Tue 02/04/2025 20:51:19.98
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 07/17/2021 14:56:52.53 
#	---------------------------------------------------------------------------
#		REMEMBER! We are now following the PHP code of NOT killing the program
#		but instead always setting a DEBUG MESSAGE and returning FALSE. So I'm
#		getting rid of all of the DIE() calls.
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
#		CLASS_SERIAL.PHP. A class to handle working with serial ports.
#		Copyright (C) 2025-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_serial
{
	private $cr = null;
	private $pr = null;
	private $bauds = null;
	private $modes = null;
	private $devices = null;
	private $settings = null;
	private $baud = null;
	private $device = null;
	private $process = null;
	private $cmd = null;
	private $circuit = null;
	private $pipes = null;
	private $dirFile = null;
	private $env = null;
	private $length = 8192;
	private $wait = 5;
	private $count = 1;
	private $file = null;
	private $cwd = null;
	private $bas = null;
	private $exe = null;
	private $sleep = null;
	private $errorCodes = null;

#	    Baud:            19200
#	    Parity:          None
#	    Data Bits:       8
#	    Stop Bits:       1
#	    Timeout:         OFF
#	    XON/XOFF:        OFF
#	    CTS handshaking: OFF
#	    DSR handshaking: OFF
#	    DSR sensitivity: OFF
#	    DTR circuit:     ON
#	    RTS circuit:     ON

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['serial']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['serial']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
#	NOTES	:	This is what we are trying to do
#
#				com4:115200,n,8,1,cs0,cd0,ds0,rs
#
#--------------------------------------------------------------------------------
#
#	Taken from:
#		https://www.ibm.com/docs/hr/aix/7.2?topic=parameters-parity-bits
#
#		N or NONE	=	No parity.
#			Specifies that the local system must not create a
#			parity bit for data characters being transmitted. It also
#			indicates that the local system does not check for a parity
#			bit in data received from a remote host.
#
#			NOTE : YOU MUST ONLY HAVE AN "N". The word "NONE" does NOT WORK.
#
#		E or EVEN	=	Even parity (all 1s must add up to an even value)
#			Specifies that the total number of binary 1s, in a single
#			character, adds up to an even number. If they do not, the
#			parity bit must be a 1 to ensure that the total number of
#			binary 1s is even.
#
#		O or ODD	=	Odd parity (All 1s must add up to an odd value)
#			Operates under the same guidelines as even parity except
#			that the total number of binary 1s must be an odd number.
#
#		S or SPACE	=	See below
#			Specifies that the parity bit will always be a binary
#			zero. Another term used for space parity is bit filling,
#			which is derived from its use as a filler for seven-bit
#			data being transmitted to a device which can only accept
#			eight bit data. Such devices see the space parity bit as
#			an additional data bit for the transmitted character.
#
#		M or MARK	=	See below
#			Operates under the same guidelines as space parity except
#			that the parity bit is always a binary 1. The mark parity
#			bit acts only as a filler.
#
#	NOTES:	I trim the incoming information, then only take the first
#		letter, and then convert it to lowercase. After all - there is
#		no need to keep the entire word.
#
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$this->pr = $pr = $this->get_class( 'pr' );
	$this->cf = $cf = $this->get_class( 'files' );

	$bauds = [];
	$bauds[] = 300;
	$bauds[] = 600;
	$bauds[] = 1200;
	$bauds[] = 2400;
	$bauds[] = 4800;
	$bauds[] = 9600;
	$bauds[] = 14400;
	$bauds[] = 19200;
	$bauds[] = 38400;
	$bauds[] = 57600;
	$bauds[] = 115200;
	$bauds[] = 230400;
	$bauds[] = 460800;
	$this->bauds = $bauds;

	$this->modes = [];
#
#	These are the default settings. Because each version of basic you
#	have various items - it might seem to be a really long list. However,
#	you ONLY need to modify the ones for YOUR version of basic. I'm talking
#	about FreeBasic, FutureBasic, QB64, IWBasic, and so forth.
#
	$this->settings = [];
#
#	Make an array for the processes. You can ONLY HAVE ONE PROCESS.
#
	$this->process = null;
#
#	Entries that are in all of the different cases. If you want to change them
#	then send the information as a KEY=>VALUE. Ex: array("stop bits"=>1).
#	All keyword names ignore the case of the keyword.
#
	$this->settings['baud'] = 9600;
	$this->settings['device'] = "com1:";
	$this->settings['parity'] = 'n';
	$this->settings['data-bits'] = 8;
	$this->settings['stop-bits'] = 1;
#
#	From the IBM webpage about serial communication
#		https://www.ibm.com/docs/hr/aix/7.2?topic=parameters-parity-bits
#
	$this->settings['timeout'] = null;
	$this->settings['xon/xoff'] = null;
	$this->settings['cts handshaking'] = null;
	$this->settings['dsr handshaking'] = null;
	$this->settings['dsr sensitivity'] = null;
	$this->settings['dtr circuit'] = null;
	$this->settings['rts circuit'] = null;
#
#	Freebasic section
#
#	The '#' means you need to put a number in for this option
#		open com "com1:9600,n,8,1,cs0,cd0,ds0,rs" as #hfile
#
	$this->settings['cs#'] = null;
	$this->settings['ds#'] = null;
	$this->settings['cd#'] = null;
	$this->settings['op#'] = null;
	$this->settings['tb#'] = null;
	$this->settings['rb#'] = null;
	$this->settings['ir#'] = null;
#
#	These are all TRUE/FALSE. True = PUT THE WORD IN, False = Leave it off
#
	$this->settings['rs'] = null;
	$this->settings['lf'] = null;
	$this->settings['asc'] = null;
	$this->settings['bin'] = null;
	$this->settings['pe'] = null;
	$this->settings['dt'] = null;
	$this->settings['fe'] = null;
	$this->settings['me'] = null;
#
#	QB64 items used.
#	NOTE :	Speed is BAUD - so use baud.
#			For the RANDOM, BINARY, OUTPUT, and INPUT - simply pass in a TRUE or FALSE
#
#	OPEN "COMn: Speed, Parity, Bits, Stopbit, [Options]"
#		[FOR {RANDOM|BINARY|OUTPUT|INPUT}] AS #P [LEN = byteSize]
#
	$this->settings['random'] = null;
	$this->settings['binary'] = null;
	$this->settings['output'] = null;
	$this->settings['input'] = null;
	$this->settings['len'] = null;
	$this->settings['file-pointer'] = null;
	$this->settings['wait'] = 5;
	$this->settings['count'] = 1;
	$this->settings['length'] = 8192;
#
#	Set up the circuit so we can talk to the com.bas program.
#
	$this->cwd = getcwd();
	$this->cwd = str_replace( "\\", "/", $this->cwd );
	$this->bas = "com.bas";
	$this->exe = "com.exe";
	$this->sleep = 1;

	$circuit = array(
		0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
#		2 => array("file", "c:/tmp/stderr.txt", "w"),  // stderr is a pipe that the child will write to
		);

	$this->circuit = $circuit;

	if( !file_exists("c:/tmp") ){ mkdir( "c:/tmp" ); }

	if( !file_exists("c:/tmp/stderr.txt") ){
		$fp = fopen( "c:/tmp/stderr.txt", "w" );
		fwrite( $fp, "Standard Error File\n" );
		fclose( $fp );
		}

	$this->errorCodes = $this->errorCodes();

	return true;
}
################################################################################
#	modes(). For WINDOWS ONLY - executes the MODE command and create the
#		mode array.
################################################################################
function modes()
{
	$pr = $this->pr;
#
#	Use the MODE command to find all of the modes
#
	if( exec("mode", $out, $ret) === false ){
		die( "***** ERROR : Could not execute the MODE command - aboring.\n" );
		}
#
#	Status for device COM1:
#	-----------------------
#	    Baud:            9600
#	    Parity:          None
#	    Data Bits:       8
#	    Stop Bits:       2
#	    Timeout:         ON
#	    XON/XOFF:        OFF
#	    CTS handshaking: OFF
#	    DSR handshaking: OFF
#	    DSR sensitivity: OFF
#	    DTR circuit:     ON
#	    RTS circuit:     ON
#	
#	
#	Status for device COM4:
#	-----------------------
#	    Baud:            115200
#	    Parity:          None
#	    Data Bits:       8
#	    Stop Bits:       1
#	    Timeout:         OFF
#	    XON/XOFF:        OFF
#	    CTS handshaking: OFF
#	    DSR handshaking: OFF
#	    DSR sensitivity: OFF
#	    DTR circuit:     OFF
#	    RTS circuit:     OFF
#	
#	
#	Status for device CON:
#	----------------------
#	    Lines:          9001
#	    Columns:        120
#	    Keyboard rate:  31
#	    Keyboard delay: 1
#	    Code page:      437
#	
	$modes = [];
	$devices = [];
#
#	Get rid of the first line - it is always blank.
#
	array_shift( $out );
	foreach( $out as $k=>$v ){
		if( strlen(trim($v)) > 0 ){
#
#	Is this just the dashed line? Skip it.
#
			if( preg_match("/\-+/", $v) ){ continue; }
#
#	Is this the "Status for device XXX:" line?
#	Then get the device name. Remove the semicolon.
#
			if( preg_match("/status for device/i", $v) ){
				$a = explode( ' ', $v );
				$dev = array_pop( $a );
				while( strlen(trim($dev)) < 1 ){ $dev = array_pop( $a ); }
				$dev = strtolower( substr($dev, 0, -1) );
				$modes[$dev] = [];
				$devices[] = $dev;
				$modes[$dev]['device'] = $dev;
				continue;
				}
#
#	Ok, we should be in the data information area. Get those.
#
			$a = explode( ' ', $v );
			$info = array_pop( $a );
			while( strlen(trim($info)) < 1 ){ $info = array_pop( $a ); }
#
#	Now get the title. Get rid of the semicolon
#
			$title = array_pop( $a );
			while( strlen(trim($title)) < 1 ){ $title = array_pop( $a ); }
			$a[] = $title;
			$title = implode( ' ', $a );
			$title = substr( trim($title), 0, -1 );
			$modes[$dev][strtolower($title)] = strtolower( $info );
			}
		}
#
#	Now we have to fix the modes because modes has bad information in it
#	And you have do this for to ALL of the modes. Not just one or two!
#
	foreach( $modes as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			if( preg_match("/off/i", $v1) ){ $modes[$k][$k1] = false; }
				else if( preg_match("/on/i", $v1) ){ $modes[$k][$k1] = true; }

			if( preg_match("/none/i", $v1) ){ $modes[$k][$k1] = "n"; }
				else if( preg_match("/even/i", $v1) ){ $modes[$k][$k1] = "e"; }
				else if( preg_match("/odd/i", $v1) ){ $modes[$k][$k1] = "o"; }

			if( preg_match("/handshaking/i", $k1) ){
				if( preg_match("/off/i", $v1) ){ $v1 = false; }
					else { $v1 = true; }

				$modes[$k][$k1] = $v1;
				}

			if( preg_match("/circuit/i", $k1) ){
				if( preg_match("/off/i", $v1) ){ $v1 = false; }
					else { $v1 = true; }

				$modes[$k][$k1] = $v1;
				}

			if( preg_match("/sensitivity/i", $k1) ){
				if( preg_match("/off/i", $v1) ){ $v1 = false; }
					else { $v1 = true; }

				$modes[$k][$k1] = $v1;
				}
			}
#
#	Parity check
#
		if( isset($v['parity']) && preg_match("/n(one)*/i", $v['parity']) ){
if( isset($v['parity']) ){ $pr->pr( "Parity = " . $v['parity'] ); }
			$modes[$k][$k1] = 'n';
			}
			else if( isset($v['parity']) && preg_match("/y(es)*/i", $v['parity']) ){
if( isset($v['parity']) ){ $pr->pr( "Parity = " . $v['parity'] ); }
				$modes[$k][$k1] = "y";
				}
			else { $modes[$k][$k1] = 'n'; }
#
#	Fix a whole slew of other problems that the MODES command gives us
#	for the VigoTec VigoWriter. You can just put a IF(FALSE){} around this
#	if you do not have a VigoTec VigoWriter.
#
		if( preg_match("/com3/i", $k) ){
			$modes[$k]['baud'] = 115200;
			$modes[$k]['cs#'] = 0;
			$modes[$k]['cd#'] = 0;
			$modes[$k]['ds#'] = 0;
			$modes[$k]['rs'] = true;
			}
			else if( preg_match("/com4/i", $k) ){
				$modes[$k]['baud'] = 115200;
				$modes[$k]['cs#'] = 0;
				$modes[$k]['cd#'] = 0;
				$modes[$k]['ds#'] = 0;
				$modes[$k]['rs'] = true;
				}
		}

$pr->pr( $modes, "MODES = " );
#
#	Now be sure to put the device name INTO the modes array.
#
	$this->devices = $devices;
	$this->modes = $modes;

	return $modes;
}
################################################################################
#	get_modes(). Return all of the modes to the caller.
################################################################################
function get_modes()
{
	return $this->modes;
}
################################################################################
#	set(). Sets the settings. You can send a single line:
#
#		ex: $v->set( "baud", 9600 );
#
#		or you can send an array:
#
#		ex: $v->set( array("baud", 9600) );
#
#	NOTE :	You can send nonsensical options like:
#
#		ex: $v-set( "My Baby Does the Hanki-Panki", "Everyday" );
#
#		ALSO! Don't forget about the LENGTH setting. That is what you set
#		for the incoming information. Default = 8192 bytes
#
#		WAIT is for how long to wait while looking for information coming
#		back from the pipe. (You can also call it wait-time but wait is the
#		key word.)
#
#		COUNT how many times to wait for incoming information.
#
#		Note also that ALL keys are set to lowercase.
#
################################################################################
function set( $ary=null, $opt=null )
{
	$pr = $this->pr;
#
#	Change the incoming information into an array. But ONLY if the $OPT
#	is not NULL. If the $ARY is an array then stick the $OPT onto the
#	end of the array.
#
	if( !is_null($opt) ){
		if( is_array($ary) ){ $ary[] = $opt; }
			else { $ary = array( $ary, $opt ); }
		}

	foreach( $ary as $k=>$v ){
		$k = strtolower( $k );
		$this->settings[$k] = $v;
		}

	foreach( $this->settings as $k=>$v ){
		if( preg_match("/null/i", $v) || is_null($v) ){
			$this->settings[$k] = false;
			}
		}

$pr->pr( $this->settings, "Settings =" );
}
################################################################################
#	get(). Gets the settings. You should call this one first and then
#		set what you may need to change.
################################################################################
function get()
{
	return $this->settings;
}
################################################################################
#	cwd(). Set the working directory and file where you are
#	putting the basic program.
################################################################################
function cwd( $cwd )
{
	$pr = $this->pr;
	$cf = $this->cf;
#
#	Because they might send the entire pathway AND where the basic program is
#	OR where the executable might be - we have to take it apart and then put
#	it back together again.
#
	$pathinfo = $cf->pathinfo( $cwd );

	$this->cwd = $pathinfo['dirname'];
#
#	Ok, did they send us where the source code goes or where the executable
#	goes?
#
	if( preg_match("/\.bas$/i", $pathinfo['basename']) ){
		$this->bas = $pathinfo['basename'];
		}
		else if( preg_match("/\.exe$/i", $pathinfo['basename']) ){
			$this->exe = $pathinfo['basename'];
			}

	return true;
}
################################################################################
#	bas(). The name of the basic file
################################################################################
function bas( $bas )
{
	$this->bas = $bas;
	return true;
}
################################################################################
#	exe(). Set what the executable's name is
################################################################################
function exe( $exe )
{
	$this->exe = $exe;
	return true;
}
################################################################################
#	env(). Set the env command. These are environment variables in a key=>value
#		array. Usually you can leave this null because the proc_open uses the
#		environment variables which are already present.
################################################################################
function env( $env=null )
{
	$this->env = $env;
	return true;
}
################################################################################
#	sleep(). Set how long to sleep
################################################################################
function sleep( $sleep=1 )
{
	$this->sleep = $sleep;
	return true;
}
################################################################################
#	open(). Open up the communications. REMEMBER! You MUST already have
#		created the com.exe program from the com.bas program and called the
#		cwd() function and called the exe() function to set up where the
#		communication program (com.exe) is located.
################################################################################
function open()
{
	$dq = '"';
	$ret = "";
	$pr = $this->pr;
	$cwd = $this->cwd;
	$exe = $this->exe;
	$pipes = $this->pipes;
	$circuit = $this->circuit;

#$pr->pr( $cwd, "CWD = " );
#$pr->pr( $exe, "EXE = " );

	$env = $this->env;

	if( is_null("$cwd/$exe") || !file_exists("$cwd/$exe") ){
		die( "***** ERROR : No such file ('$cwd/$exe') or FILE is NULL\n" );
		}

	$cmd = "$dq$cwd/$exe$dq";
	$this->process = proc_open( $cmd, $circuit, $pipes, $cwd, $env );

	if( !is_resource($this->process) ){
		die( "***** ERROR : Could not open a process via PROC_OPEN - aborting.\n" );
		}

	foreach( $pipes as $k=>$v ){
		if( !is_resource($pipes[$k]) ){
			die( "***** ERROR : Did not get the pipe #$k - aborting.\n" );
			}
		}

	foreach( $this->settings as $k=>$v ){
		if( preg_match("/^len/i", $k) ){ $this->length = $v; }
		if( preg_match("/^wait/i", $k) ){ $this->wait = $v; }
		}

	$this->pipes = $pipes;
#
#	The serial class should not do anything other than just open the
#	communication line. Whoever is calling this open() function needs
#	to send a WRITE() command and then read whatever comes back.
##
##	If this is a communication port - since we are dealing with the pen plotter
##	we need to send it a return. Otherwise we do nothing more.
##
#	if( preg_match("/com/i", $this->settings['device']) ){
#		$this->write( "" );
#		$ret = $this->read();
##$pr->pr( $ret, "ret = " );
#		}

	return;
}
################################################################################
#	read(). Read from the serial port.
#
#	NOTES : Returns either the information from the pipe or FALSE.
################################################################################
function read( $pipe=1 )
{
	$pr = $this->pr;
	$errorCodes = $this->errorCodes;

	if( preg_match("/com/i", $this->settings['device']) ){
		$cnt = 0;
		while( true ){
			$fstat = fstat($this->pipes[$pipe]);
			if( $fstat['size'] > 0 ){ break; }
			if( $cnt++ > $this->count ){ return false; }
			echo "Reading...\n";
			sleep( $this->wait );
			}

		$a = fread( $this->pipes[$pipe], $this->length );
		$a = explode( "\n", $a );
		foreach( $a as $k=>$v ){
			$v = str_replace( "", "", $v );
			if( strlen(trim($v)) < 1 ){ unset( $a[$k] ); }
				else if( preg_match("/ok/i", $v) ){ unset( $a[$k] ); }
				else if( preg_match("/unable/i", $v) ){
					$pr->pr( "***** ERROR : $v\n" );
					$pr->pr();
					exit;
					}
				else if( preg_match("/error/i", $v) ){
					$a = explode( ':', $v );
					$pr->pr( "***** ERROR : $v\n" . $errorCodes[$a[1]] . "\n" );
					$pr->pr();
					exit;
					}
			}

		$a = implode( "\n", $a );
		return $a;
		}
#
#	Because there might be other devices I do not know of - we will
#	do another IF statement instead of just using an ELSE statement.
#
#	What this does is it just sits there and looks to see if the KEY.DAT
#	file has been created by the basic program. If so - it gets it and
#	then deletes it so we don't get duplicate key strokes.
#
		else if( preg_match("/con/i", $this->settings['device']) ){
			$cnt = 0;
			$info = "";
			$file = $this->cwd . "/key.dat";
			while( true ){
				if( file_exists($file) ){
					try {
						$info = file_get_contents( $file );
						unlink( $file );
						}
						catch( exception $e ){
							$pr->pr( $e->getMessage(), "Error = " );
							}
					}

				if( strlen($info) > 0 ){ break; }
				if( $cnt++ > $this->count ){ return false; }
				sleep( 1 );
				}

			return $info;
			}
}
################################################################################
#	write(). Write something to the serial port.
################################################################################
function write( $info="", $pipe=0 )
{
#
#	Only do this for a COM port
#
	if( preg_match("/com/i", $this->settings['device']) ){
		if( !is_resource($this->pipes[$pipe]) ){
			die( "***** ERROR : Did not get the pipe #$pipe - aborting.\n" );
			}

		$info = trim( $info ) . "\r\n";
		echo "Writing : $info";
		$ret = fwrite( $this->pipes[$pipe], $info );
		sleep( $this->sleep );
		return $ret;
		}
}
################################################################################
#	close(). Close the connection.
################################################################################
function close( $opt=true )
{
	$dq = '"';
	$pr = $this->pr;
	$exe = $this->exe;

	if( !is_array($this->pipes) ){
#		die( "Finished\n" );
		}
#
#	Because we can have several processes - we have to terminate each process
#	and under Windows - we have to stop each one of these
#
	if( is_resource($this->process) ){
#
#	First get rid of the pipes
#
		if( !is_null($this->pipes[0]) || is_resource($this->pipes[0]) ){
			echo "Closing pipe #0\n";
			@fclose( $this->pipes[0] );
			}

		if( !is_null($this->pipes[1]) || is_resource($this->pipes[1]) ){
			echo "Closing pipe #1\n";
			@fclose( $this->pipes[1] );
			}

		if( !is_null($this->pipes[2]) || isset($this->pipes[2]) && is_resource($this->pipes[2]) ){
			echo "Closing pipe #2\n";
			@fclose( $this->pipes[2] );
			}
#
#	We can close the pipes but we must NOT kill the process if we
#	want to continue doing things.
#
		if( $opt ){
#
#	Now get rid of the process
#
			echo "Closing Processes\n";
			$ret = proc_terminate( $this->process );

			echo "Proc_terminate returned value = $ret\n";
#
#	Under Windows - we have to physically kill the executable.
#
			$cmd = "tasklist /fi " . $dq . "imagename eq $exe" . $dq . '"';
			exec( $cmd, $output );
			if( !preg_match("/no tasks/i", $output[0]) ){
				if( preg_match("/win/i", PHP_OS) ){
					system( "taskkill /IM $exe /F" );
					}
				}

			$this->process = null;
			}
		}

	return;
}
################################################################################
#	makeFBCom(). Create the Freebasic program for use with this class.
#
#	NOTES :	Remember this ONLY generates the FreeBasic code THEN you have to
#		compile it with FreeBasic and THEN you can use the program to talk to
#		your serial device.
#
#	The following were taken from the FreeBasic Manual. Not my stuff but I need
#	it.
#
#	Condition Default number of stop bits 
#	baud rate <= 110 and data bits = 5 1.5 
#	baud rate <= 110 and data bits >= 6 2 
#	baud rate > 110 1 
#	
#	extended_options
#	
#	Miscellaneous options. (See table below)
#	
#	Option Action 
#	'CSn' Set the CTS duration (in ms) (n>=0), 0 = turn off, default = 1000 
#	'DSn' Set the DSR duration (in ms) (n>=0), 0 = turn off, default = 1000 
#	'CDn' Set the Carrier Detect duration (in ms) (n>=0), 0 = turn off 
#	'OPn' Set the 'Open Timeout' (in ms) (n>=0), 0 = turn off 
#	'TBn' Set the 'Transmit Buffer' size (n>=0), 0 = default, depends on platform 
#	'RBn' Set the 'Receive Buffer' size (n>=0), 0 = default, depends on platform 
#	'RS' Suppress RTS detection 
#	'LF' Communicate in ASCII mode (add LF to every CR) - Win32 doesn't support this one 
#	'ASC' same as 'LF' 
#	'BIN' The opposite of LF and it'll always work 
#	'PE' Enable 'Parity' check 
#	'DT' Keep DTR enabled after CLOSE 
#	'FE' Discard invalid character on error 
#	'ME' Ignore all errors 
#	'IRn' IRQ number for COM (only supported (?) on DOS) 
#
#	Note : $comPorts should be a string that goes "#,#" like "3,4".
#		The first number is the main com port to use. The second number is
#		whatever Windows uses when it forgets the com port and tries to use
#		a different com port. This is what is happening to me.
#
################################################################################
function makeFBCom( $comPorts=null )
{
	$basFile = "$this->cwd/$this->bas";
	$settings = $this->settings;
	$comPorts = explode( ',', $comPorts );	#	Get the list of com ports used.
#
#		open com "com1:9600,n,8,1,cs0,cd0,ds0,rs" as #hfile
#
	$baud = is_null($settings['baud']) ? "9600" : $settings['baud'];

	$parity = is_null($settings['parity']) ? ",n" : "," . substr($settings['parity'],0,1);
	$data_bits = is_null($settings['data-bits']) ? ",8" : "," . $settings['data-bits'];
	$stop_bits = is_null($settings['stop-bits']) ? ",1" : "," . $settings['stop-bits'];
	if( is_null($settings['timeout']) || preg_match("/off/i", $settings['timeout']) ){
		$timeout = "";
		}
		else { $timeout = "," . $settings['timeout']; }

	if( is_null($settings['cs#']) ){ $csn = ""; }
		else if( is_numeric($settings['cs#']) ){ $csn = ",cs" . $settings['cs#']; }
		else { $csn = ",cs0"; }

	if( is_null($settings['ds#']) ){ $dsn = ""; }
		else if( is_numeric($settings['ds#']) ){ $dsn = ",ds" . $settings['ds#']; }
		else { $dsn = ",ds0"; }

	if( is_null($settings['cd#']) ){ $cdn = ""; }
		else if( is_numeric($settings['cd#']) ){ $cdn = ",cd" . $settings['cd#']; }
		else { $cdn = ",cd0"; }

	if( is_null($settings['op#']) ){ $opn = ""; }
		else if( is_numeric($settings['op#']) ){ $opn = ",op" . $settings['op#']; }
		else { $opn = ",op0"; }

	if( is_null($settings['tb#']) ){ $tbn = ""; }
		else if( is_numeric($settings['tb#']) ){ $tbn = ",tb" . $settings['tb#']; }
		else { $tbn = ",tb0"; }

	if( is_null($settings['rb#']) ){ $rbn = ""; }
		else if( is_numeric($settings['rb#']) ){ $rbn = ",rb" . $settings['rb#']; }
		else { $rbn = ",rb0"; }

	if( is_null($settings['ir#']) ){ $irn = ""; }
		else if( is_numeric($settings['ir#']) ){ $irn = ",ir" . $settings['ir#']; }
		else { $irn = ",ir0"; }
#
#	These are all TRUE/FALSE. True = PUT THE WORD IN, False = Leave it off
#
	$rs = is_null($settings['rs']) ? "" : ",rs";
	$lf = is_null($settings['lf']) ? "" : ",lf";
	$asc = is_null($settings['asc']) ? "" : ",asc";
	$bin = is_null($settings['bin']) ? "" : ",bin";
	$pe = is_null($settings['pe']) ? "" : ",pe";
	$dt = is_null($settings['dt']) ? "" : ",dt";
	$fe = is_null($settings['fe']) ? "" : ",fe";
	$me = is_null($settings['me']) ? "" : ",me";
#
#	Create the communication setting which will be used in the following
#	basic program.
#
	$com1 = strtoupper( "com$comPorts[0]:$baud$parity$data_bits$stop_bits$timeout" .
			"$csn$cdn$dsn$opn$tbn$rbn$irn$rs$lf$asc$bin$pe$dt$fe$me" );

	$com2 = strtoupper( "com$comPorts[1]:$baud$parity$data_bits$stop_bits$timeout" .
			"$csn$cdn$dsn$opn$tbn$rbn$irn$rs$lf$asc$bin$pe$dt$fe$me" );

	$code = <<<EOD
'
'	COM.BAS is a simple Freebasic program that will
'	open, read/write, close a communication port.
'
'	Set it up for whatever port you want it to talk to
'	and THEN compile and run the program.
'
rem	+--------------------------------------------------------------------------------
rem	|BEGIN DOC
rem	|
rem	|-Calling Sequence:
rem	|
rem	|	main()
rem	|
rem	|-Description:
rem	|
rem	|	A simple program to open, read/write, and close a communication port.
rem	|	Please see the BSD-3-Patent.txt for more information.
rem	|
rem	|	I am using the following as an example
rem	|
rem	|	Status for device COM4:
rem	|	-----------------------
rem	|	    Baud:            115200
rem	|	    Parity:          None
rem	|	    Data Bits:       8
rem	|	    Stop Bits:       1
rem	|	    Timeout:         ON
rem	|	    XON/XOFF:        OFF
rem	|	    CTS handshaking: OFF
rem	|	    DSR handshaking: OFF
rem	|	    DSR sensitivity: OFF
rem	|	    DTR circuit:     OFF
rem	|	    RTS circuit:     OFF
rem	|	
rem	|-Inputs:
rem	|
rem	|	Options:
rem	|
rem	|-Outputs:
rem	|
rem	|	None.
rem	|
rem	|-Revisions:
rem	|
rem	|	Name					Company					Date
rem	|	---------------------------------------------------------------------------
rem	|	Mark Manning			Simulacron I			Fri 04/04/2025 16:33:01.02
rem	|		Original Program.
rem	|
rem	|	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
rem	|	---------------------------------------------------------------------------
rem	|		This is now under the BSD Three Clauses Plus Patents License.
rem	|		See the BSD-3-Patent.txt file.
rem	|
rem	|	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
rem	|	---------------------------------------------------------------------------
rem	|	Please note that _MY_ Legal notice _HERE_ is as follows:
rem	|
rem	|		COM.BAS. A program to handle working with serial ports.
rem	|		Copyright (C) 2025-NOW.  Mark Manning. All rights reserved
rem	|		except for those given by the above license.
rem	|
rem	|	Please place _YOUR_ legal notices _HERE_. Thank you.
rem	|
rem	|END DOC
rem	+--------------------------------------------------------------------------------
	dim a As string
	dim s As string
	dim c as string
	dim scrn as long
'
'	First open the console
'
	scrn = screen( 20, 80 )
	cls
'
'	Clear a, s, and c
'
	a = ""
	s = ""
	c = ""
'
'	Make the string we are going to use to open the communication port
'
'	Example:
'
'		open com "com1:9600,n,8,1,cs0,cd0,ds0,rs" as #hfile
'
'	The '?' returns the following from the VigoWriter Pen Plotter:
'
'	<Idle|MPos:0.000,0.000,0.000|Bf:15,126|FS:0,0|Ov:100,100,100|A:S>
'
'	G91 G0 X1
'
'	Taken from :
'		https://github.com/winder/Universal-G-Code-Sender/issues/1279
'
'	Yes, for example send "M3 S1000" for pen down and "M3 S1"
'	for pen up (or M4).  The correct values for S you need to
'	figure out.  If the servo needs some time for motion you
'	may add the command "G4 P1" after pen down/up command for
'	1 second delay
'
'	Original command I used to create this program :
'
'	s = "com4:115200,n,8,1,cs0,cd0,ds0,rs"
'
	s = "$com1"

'	print "Opening with : " & s
	If Open Com (s For Binary As #1) <> 0 Then
		s = "$com2"
		If Open Com (s For Binary As #1) <> 0 Then
			Print "Unable to open the serial port"
			End
			End If
		End If
'
'	Use the INPUT command to give us a place to input the request
'
	c = "start"
	while( c <> "q" )

		input "Send: ", s
'
'	Save the command
'
		c = s
'
'	Send it to the com port
'
'		print "Sending : " & s
		if trim(s) <> "q" then print #1, s
'
'	Now wait a few moments before we start checking for outgoing information
'
		sleep 2000,1
'
'	Clear the A)nswer and S)tring variables
'
		a = ""
		s = ""
'
'	Now look for something coming back
'
		While( LOC(1) > 0 )
			a = Input(LOC(1), 1)
			s = s & a
			Wend
'
'	Now send the string back
'
'		print "Output : >" & s & "<"
'
		print s
		wend
'
'	Ok - NOW we close the port and end
'
	Close #1
	end

EOD;

	echo "Now that the file has been made you must compile it with FreeBasic.\n";
	echo "AFTER COMPILING, you should wind up with something like 'serial.exe'\n";
	echo "What >I< wind up with is serial32.exe\n";
	echo "The source code is in file : $basFile\n";

	return file_put_contents( $basFile, $code );
}
################################################################################
#	makeFBKey(). Make the FreeBasic program to handle getting keys from the
#		console.
################################################################################
function makeFBKey()
{
	$basFile = "$this->cwd/$this->bas";
	$settings = $this->settings;

	$code = <<<EOD
'
'	This is taken directly from the FreeBasic CHM file and just modified to work here.
'
'	include fbgfx.bi for some useful definitions
'
#include "fbgfx.bi"

#if __FB_LANG__ = "fb"
Using fb ' constants and structures are stored in the FB namespace in lang fb
#endif

	Dim e As Event

	ScreenRes 200, 50

	Do
		If (ScreenEvent(@e)) Then
			Select Case e.type
				Case EVENT_KEY_PRESS
					If (e.scancode = SC_ESCAPE) Then
						End
						End If

					If (e.ascii > 0) Then
						open "key.dat" for append as #1
						Print #1, chr( e.ascii );
						close #1
						End If

				Case EVENT_KEY_RELEASE
				Case EVENT_KEY_REPEAT
					If (e.ascii > 0) Then
						open "key.dat" for append as #1
						Print #1, chr( e.ascii );
						close #1
						End If

				Case EVENT_MOUSE_MOVE
				Case EVENT_MOUSE_BUTTON_PRESS
				Case EVENT_MOUSE_BUTTON_RELEASE
				Case EVENT_MOUSE_DOUBLE_CLICK
				Case EVENT_MOUSE_WHEEL
				Case EVENT_MOUSE_ENTER
				Case EVENT_MOUSE_EXIT
				Case EVENT_WINDOW_GOT_FOCUS
				Case EVENT_WINDOW_LOST_FOCUS
				Case EVENT_WINDOW_CLOSE
					End

				Case EVENT_MOUSE_HWHEEL
				End Select
			End If

		Sleep 1
		Loop
EOD;

	echo "Now that the file has been made you must compile it with FreeBasic.\n";
	echo "AFTER COMPILING, you should wind up with something like 'inkey.exe'\n";
	echo "What >I< wind up with is inkey32.exe\n";
	echo "The source code is in file : $basFile\n";

	return file_put_contents( $basFile, $code );
}
################################################################################
#	makeQBCom(). Create the QB64 code.
#	NOTES :	Remember this ONLY generates the QB64 code THEN you have to
#		compile it with QB64 and THEN you can use the program to talk to
#		your serial device.
#
#	Note : $comPorts should be a string that goes "#,#" like "3,4".
#		The first number is the main com port to use. The second number is
#		whatever Windows uses when it forgets the com port and tries to use
#		a different com port. This is what is happening to me.
################################################################################
function makeQBCom( $comPorts=null )
{
	$pr = $this->pr;
	$basFile = "$this->cwd/$this->bas";
	$settings = $this->settings;
	$comPorts = explode( ',', $comPorts );	#	Get the list of com ports used.
#
#		open com "com1:9600,n,8,1,cs0,cd0,ds0,rs" as #hfile
#
	$baud = is_null($settings['baud']) ? "9600" : $settings['baud'];

	$parity = is_null($settings['parity']) ? ",n" : "," .$settings['parity'];
	$data_bits = is_null($settings['data-bits']) ? ",8" : "," .$settings['data-bits'];
	$stop_bits = is_null($settings['stop-bits']) ? ",1" : "," .$settings['stop-bits'];
	$timeout = ($settings['timeout'] === false) ? "" : "," .$settings['timeout'];

	if( $settings['cs#'] === false ){ $csn = ""; }
		else if( is_numeric($settings['cs#']) ){ $csn = ",cs" .$settings['cs#']; }
		else { $csn = ",cs0"; }

	if( $settings['ds#'] === false ){ $dsn = ""; }
		else if( is_numeric($settings['ds#']) ){ $dsn = ",ds" .$settings['ds#']; }
		else { $dsn = ",ds0"; }

	if( $settings['cd#'] === false ){ $cdn = ""; }
		else if( is_numeric($settings['cd#']) ){ $cdn = ",cd" .$settings['cd#']; }
		else { $cdn = ",cd0"; }

	if( $settings['op#'] === false ){ $opn = ""; }
		else if( is_numeric($settings['op#']) ){ $opn = ",op" .$settings['op#']; }
		else { $opn = ",op0"; }

	if( $settings['tb#'] === false ){ $tbn = ""; }
		else if( is_numeric($settings['tb#']) ){ $tbn = ",tb" .$settings['tb#']; }
		else { $tbn = ",tb0"; }

	if( $settings['rb#'] === false ){ $rbn = ""; }
		else if( is_numeric($settings['rb#']) ){ $rbn = ",rb" .$settings['rb#']; }
		else { $rbn = ",rb0"; }

	if( $settings['ir#'] === false ){ $irn = ""; }
		else if( is_numeric($settings['ir#']) ){ $irn = ",ir" .$settings['ir#']; }
		else { $irn = ",ir0"; }
#
#	These are all TRUE/FALSE. True = PUT THE WORD IN, False = Leave it off
#
	$rs = ($settings['rs'] === false) ? "" : ",rs";
	$lf = ($settings['lf'] === false) ? "" : ",lf";
	$asc = ($settings['asc'] === false) ? "" : ",asc";
	$bin = ($settings['bin'] === false) ? "" : ",bin";
	$pe = ($settings['pe'] === false) ? "" : ",pe";
	$dt = ($settings['dt'] === false) ? "" : ",dt";
	$fe = ($settings['fe'] === false) ? "" : ",fe";
	$me = ($settings['me'] === false) ? "" : ",me";
#
#	QB64 Info
#
#	This code originally had RB8192 at the end of it. I changed that
#	to use the RS command. The RB8192 is how many BYTES are read/written
#	to the port. RS says just send however many you have and that is all.
#
#	ser$ = COM4:115200,N,8,1,BIN,CD0,CS0,DS0,RS"
#
#	Let me WARN you - try to keep as close to the above as you can. I
#	can not be held responsible if you blow up your device. So test
#	each thing as you go.
#

#
#	Create the communication setting which will be used in the following
#	basic program.
#
	$com1 = strtoupper( "com$comPorts[0]:$baud$parity$data_bits$stop_bits$timeout" .
			"$csn$cdn$dsn$opn$tbn$rbn$irn$rs$lf$asc$bin$pe$dt$fe$me" );

	$com2 = strtoupper( "com$comPorts[1]:$baud$parity$data_bits$stop_bits$timeout" .
			"$csn$cdn$dsn$opn$tbn$rbn$irn$rs$lf$asc$bin$pe$dt$fe$me" );

	$code = <<<EOD
rem
rem Tab Settings are set to 4 (ts=4)
rem +--------------------------------------------------------------------------------
rem |BEGIN DOC
rem |
rem |-Calling Sequence:
rem |
rem |   myCom()
rem |
rem |-Description:
rem |
rem |   Taken from : https://qb64phoenix.com/forum/showthread.php?tid=342
rem |   User name : mdijkens 
rem |
rem |   Although the program presented by mdijkens works - it needed to be
rem |   modified by me to work with the VigoTEC Pen Plotter. The first
rem |   addition was the small program at the front of this script (ie:
rem |   From the "rem \$Debug" to the "end" statement. Secondly, I had to
rem |   figure out why the ErrorHandler simply refused to work. THIS problem
rem |   was caused by not having the main program mentioned above. Once the
rem |   "end" statement was inserted - the error message about the ErrorHandler
rem |   disappeared. Note that the one(1) second SLEEP command is VERY necessary
rem |   to allow the serial port to respond. Even though, IN MY CASE, it is
rem |   opened for 115200 speed - the Pen Plotter itself seems to go at 110 baud
rem |   in responding to commands. Last, I found out that the inclusion of a
rem |   carriage return (chr$(13)) REALLY had to be there or else some of the
rem |   VigoTEC commands just will not function properly. These (so far) are the
rem |   "$" and "$$" commands. Without the carriage return the VigoWriter (from
rem |   VigoTEC) simply ignores these commands. Also, the LINE INPUT command
rem |   does NOT return a carriage return at the end of the command. This is
rem |   gobbled up by the LINE INPUT COMMAND and all you get is just the letters
rem |   you typed in.
rem |
rem |   Because the original set of functions is not owned by me - this code can
rem |   be used however you want - unlike my FreeBasic code which is restricted
rem |   a little bit.
rem |
rem |-Inputs:
rem |
rem |   None.
rem |
rem |-Outputs:
rem |
rem |   None.
rem |-Revisions:
rem |
rem |   Name                    Company                 Date
rem |   ---------------------------------------------------------------------------
rem |   Mark Manning            Simulacron I            Tue 04/08/2025 22:56:12.70
rem |       Original Program.
rem |
rem |   Mark Manning            Simulacron I            Sat 05/13/2023 17:34:57.07 
rem |   ---------------------------------------------------------------------------
rem |       This is now under the BSD Three Clauses Plus Patents License.
rem |       See the BSD-3-Patent.txt file.
rem |
rem |   Mark Manning            Simulacron I            Wed 05/05/2021 16:37:40.51 
rem |   ---------------------------------------------------------------------------
rem |   Please note that _MY_ Legal notice _HERE_ is as follows:
rem |
rem |       myCom.BAS. A program to handle working with serial ports.
rem |       Copyright (C) 2025-NOW.  Mark Manning. All rights reserved
rem |       except for those given by the above license. Also, everything
rem |       from the ErrorHandler on down belongs to mdijkens and NOT me.
rem |
rem |   Please place _YOUR_ legal notices _HERE_. Thank you.
rem |
rem |END DOC
rem +--------------------------------------------------------------------------------
rem function myCom()

    try% = 1
    ret% = ser.open(try%)
    inp$ = ""
    while inp$ <> "q"
        print "Send: ";
        line input inp$
		inp$ = ltrim$( rtrim$(inp$) )
        if inp$ <> "q" then ser.send( inp$ )
        sleep 1
        print ser.read
        wend
    info$ = ser.close
    end

ErrorHandler:
    if try% < 2 then
        On Error GoTo 0
        try% = try% + 1
        ret% = ser.open(try%)
        resume next
        end if

    print errorNum
    close #88


Function ser.open%(try%) ' e.g. ser$="COM1:9600"
    On Error GoTo ErrorHandler
rem
rem ser$ = ser$ + ",N,8,1,BIN,CD0,CS0,DS0,RS" ' RS or RB8192
rem
    if try% = 1 then
        ser$ = "COM3:115200,N,8,1,CS0,CD0,DS0,RS"
        else
            ser$ = "COM4:115200,N,8,1,CS0,CD0,DS0,RS"
        end if

    Open ser$ For RANDOM As #88
    If errorNum = 0 Then serBytes$ = ser.read$
    On Error GoTo 0
    ser.open% = errorNum
End Function

Function ser.close$ ()
    ser.close$ = ser.read$
    Close #88
End Function

Sub ser.send (bytes$)
    Dim b As String * 1
    For i% = 1 To Len(bytes$)
        b = Mid$(bytes$, i%, 1)
        Put #88, , b
        Next i%

    byte$ = chr$(13)
    put #88, , byte$
    on error goto ErrorHandler
End Sub

Function ser.read$ ()
    Dim b As String * 1: resp$ = ""
    Do While Loc(88)
        Get #88, , b: resp$ = resp$ + b
        Loop

    ser.read$ = resp$
    on error goto ErrorHandler
End Function

EOD;

	echo "Now that the file has been made you must compile it with QB64.\n";
	echo "You should wind up with something like 'serial.exe'\n";
	echo "The output file is : $basFile\n";

	return file_put_contents( $basFile, $code );
}
################################################################################
#	makeQBKey(). Make the FreeBasic program to handle getting keys from the
#		console.
################################################################################
function makeQBKey()
{
	$basFile = "$this->cwd/$this->bas";
	$settings = $this->settings;

	$code = <<<EOD
'
'	Simple inkey$ program
'
'	input the escape character so we can test against it
'	IF you want to use the escape key - then just choose another key
'	and put it in this next line.
'
	esc$ = ""

	open "key.dat" for output as #1
	close #1

	while 1
		mykey$ = inkey$
		if mykey$ = esc$ then
			end
			endif

		if len(mykey$) > 0 then
			print mykey$;
			open "key.dat" for append as #1
			print #1, mykey$;
			close #1
			endif
		wend
	end

EOD;

	echo "Now that the file has been made you must compile it with FreeBasic.\n";
	echo "AFTER COMPILING, you should wind up with something like 'inkey.exe'\n";
	echo "What >I< wind up with is inkey32.exe\n";
	echo "The source code is in file : $basFile\n";

	return file_put_contents( $basFile, $code );
}
################################################################################
#	errorCodes(). Returns all of the error code meanings.
#	Notes : Taken from the class_grbl.php file.
################################################################################
function errorCodes()
{
	$code = <<<EOD
1	G-code words consist of a letter and a value. Letter was not found.
2	Numeric value format is not valid or missing an expected value.
3	Grbl '$' system command was not recognized or supported.
4	Negative value received for an expected positive value.
5	Homing cycle is not enabled via settings.
6	Minimum step pulse time must be greater than 3usec
7	EEPROM read failed. Reset and restored to default values.
8	Grbl '$' command cannot be used unless Grbl is IDLE. Ensures smooth operation during a job.
9	G-code locked out during alarm or jog state
10	Soft limits cannot be enabled without homing also enabled.
11	Max characters per line exceeded. Line was not processed and executed.
12	(Compile Option) Grbl '$' setting value exceeds the maximum step rate supported.
13	Safety door detected as opened and door state initiated.
14	(Grbl-Mega Only) Build info or startup line exceeded EEPROM line length limit.
15	Jog target exceeds machine travel. Command ignored.
16	Jog command with no '=' or contains prohibited g-code.
17	Laser mode requires PWM output.
20	Unsupported or invalid g-code command found in block.
21	More than one g-code command from same modal group found in block.
22	Feed rate has not yet been set or is undefined.
23	G-code command in block requires an integer value.
24	Two G-code commands that both require the use of the XYZ axis words were detected in the block.
25	A G-code word was repeated in the block.
26	A G-code command implicitly or explicitly requires XYZ axis words in the block, but none were detected.
27	N line number value is not within the valid range of 1 - 9,999,999.
28	A G-code command was sent, but is missing some required P or L value words in the line.
29	Grbl supports six work coordinate systems G54-G59. G59.1, G59.2, and G59.3 are not supported.
30	The G53 G-code command requires either a G0 seek or G1 feed motion mode to be active. A different motion was active.
31	There are unused axis words in the block and G80 motion mode cancel is active.
32	A G2 or G3 arc was commanded but there are no XYZ axis words in the selected plane to trace the arc.
33	The motion command has an invalid target. G2, G3, and G38.2 generates this error, if the arc is impossible to generate or if the probe target is the current position.
34	A G2 or G3 arc, traced with the radius definition, had a mathematical error when computing the arc geometry. Try either breaking up the arc into semi-circles or quadrants, or redefine them with the arc offset definition.
35	A G2 or G3 arc, traced with the offset definition, is missing the IJK offset word in the selected plane to trace the arc.
36	There are unused, leftover G-code words that aren't used by any command in the block.
37	The G43.1 dynamic tool length offset command cannot apply an offset to an axis other than its configured axis. The Grbl default axis is the Z-axis.
38	Tool number greater than max supported value.
EOD;

	$a = [];
	$b = explode( "\n", $code );
	foreach( $b as $k=>$v ){
		$c = explode( "	", $v );
		$a[$c[0]] = $c[1];
		}

	return $a;
}
################################################################################
#	get_class(). Returns a class specified on the call line.
#	Notes:	This is being done because I have too many re-entrant calls to my
#		classes. So now - you have to make sure you put include the class in
#		YOUR program so these can work properly.
################################################################################
function get_class( $name=null )
{
	if( is_null($name) ){
		die( "***** ERROR : Name is not given at " . __LINE__ . "\n" );
		}

	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );

	if( isset($GLOBALS['classes'][$name]) ){ return $GLOBALS['classes'][$name]; }
		else { die( "***** ERROR : You need to include $lib/class_rgb.php\n" ); }
}
################################################################################
#	__destruct(). Be sure to close everything.
################################################################################
function __destruct()
{
	$this->close();
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['serial']) ){
		$GLOBALS['classes']['serial'] = new class_serial();
		}

?>
