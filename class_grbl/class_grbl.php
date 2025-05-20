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
#	class_grbl();
#
#-Description:
#
#	A class to handle the pen plotter via GERBIL or GRBL.
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
#		CLASS_FILES.PHP. A class to handle working with files.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_grbl
{
	public $temp_path = null;

	private $cf = null;
	private $pr = null;
	private $cp = null;

	private $mcode = null;
	private $gcode = null;
	private $misc_cmds = null;
	private $device = null;
	private $mode = null;
	private $default_init = null;
	private	$com = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['grbl']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['grbl']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
#	NOTES	:	This is what we are trying to do
#
#				com4:115200,n,8,1,cs0,cd0,ds0,rs
#
################################################################################
function init()
{
	$this->pr = $pr = new class_pr();
	$this->cf = $cf = new class_files();

	$mcode = [];
	$gcode = [];
	$xcode = [];
#
#	G Code commands and what they do
#
	$gcode['g20'] = false;	#	Use inches
	$gcode['g21'] = true;	#	Use millimeters
	$gcode['g93'] = false;	#	Inverse time feed
#
#	Misc GRBL commands
#
	$xcode['001'] = "Grbl 1.1e ['$' for help]";	#	This is what you should get
	$xcode['$'] = true;	#	Tell me who you are (vigowriter)
	$xcode['?'] = true;	#	Status report
	$xcode['$-ret'] = '[HLP:$$ $# $G $I $N $x=val $Nx=line $J=line $SLP $C $X $H ~ ! ? ctrl-x]';
	$xcode['$$'] = true;
	$xcode['x=val'] = true;
	$xcode['$0']	= 10;		#	Step pulse, microseconds
	$xcode['$1']	= 25;		#	Step idle delay, milliseconds.
#
#	$1=255= Turn everything ON. $1=0 Turns everything OFF (DO NOT DO THIS!)
#
	$xcode['$2']	= 0;		#	Step port invert, mask
#
#	Table for $2
#
#		(Use this #)
#		Setting Value	Mask		Invert X	Invert Y	Invert Z
#		0				00000000	N			N			N
#		1				00000001	Y			N			N
#		2				00000010	N			Y			N
#		3				00000011	Y			Y			N
#		4				00000100	N			N			Y
#		5				00000101	Y			N			Y
#		6				00000110	N			Y			Y
#		7				00000111	Y			Y			Y
#
	$xcode['$3']	= 0;		#	Direction port invert, mask
#
#	Use the above table for $3 too.
#
	$xcode['$4']	= 0;		#	Step enable invert, boolean
	$xcode['$5']	= 0;		#	Limit pins invert, boolean
	$xcode['$6']	= 0;		#	Probe pin invert, boolean
	$xcode['$10']	= 1;		#	Status report, mask for Machine Position
	$xcode['$10']	= 2;		#	Status report, mask for Work Position
#
#--------------------------------------------------------------------------------
#		Report Type		Value	Description
#		------------------------------------------------------------
#		PositionType	0		Enable WPos: . Disable MPos: .
#		PositionType	1		Enable MPos: . Disable WPos: .
#		Buffer Data		2		Enabled Buf:
#									field appears with planner
#									and serial RX availablebuffer.
#--------------------------------------------------------------------------------
#
	$xcode['$11']	= 0.010;	#	Junction deviation, mm
	$xcode['$12']	= 0.002;	#	Arc tolerance, mm
	$xcode['$13']	= 0;		#	Report inches, boolean
	$xcode['$20']	= 0;		#	Soft limits, boolean
	$xcode['$21']	= 0;		#	Hard limits, boolean
	$xcode['$22']	= 1;		#	Homing cycle, boolean
	$xcode['$23']	= 0;		#	Homing dir invert, mask
	$xcode['$24']	= 25.000;	#	Homing feed, mm/min
	$xcode['$25']	= 500.00;	#	Homing seek, mm/min
	$xcode['$26']	= 250;		#	Homing debounce, milliseconds
	$xcode['$27']	= 1.000;	#	Homing pull-off, mm
	$xcode['$30']	= 1000;		#	Max spindle speed, RPM
	$xcode['$31.']	= 0.;		#	Min spindle speed, RPM
	$xcode['$32']	= 0;		#	Laser mode, boolean
	$xcode['$100']	= 250.000;	#	X steps/mm - NEEDS SOFT RESET
	$xcode['$101']	= 250.000;	#	Y steps/mm - NEEDS SOFT RESET
	$xcode['$102']	= 250.000;	#	Z steps/mm - NEEDS SOFT RESET
	$xcode['$110']	= 500.000;	#	X Max rate, mm/min
	$xcode['$111']	= 500.00;	#	Y Max rate, mm/min
	$xcode['$112']	= 500.00;	#	Z Max rate, mm/min
	$xcode['$120']	= 10.000;	#	X Acceleration, mm/sec^2
	$xcode['$121']	= 10.000;	#	Y Acceleration, mm/sec^2
	$xcode['$122']	= 10.000;	#	Z Acceleration, mm/sec^2
	$xcode['$130']	= 200.000;	#	X Max travel, mm
	$xcode['$131']	= 200.000;	#	Y Max travel, mm
	$xcode['$132']	= 200.000;	#	Z Max travel, mm
	$xcode['com'] = "com4:115200,n,8,1,cs0,cd0,ds0,rs";
	$xcode['MPos:'] = true;	#	Machine Position
	$xcode['WPos:'] = true;	#	Work Position
#
#	Code		Description
#	-----------	-----------------------------------------
#	M0 M1		Program Pause
#	M2 M30		Program End
#	M60			Pallet Change Pause
#	M3 M4 M5	Spindle Control
#	M6			Tool Change
#	M7 M8 M9	Coolant Control
#	M19			Orient Spindle
#	M48 M49		Feed & Spindle Overrides Enable/Disable
#	M50			Feed Override Control
#	M51			Spindle Override Control
#	M52			Adaptive Feed Control
#	M53			Feed Stop Control
#	M61			Set Current Tool Number
#	m62-m65		Output Control
#	M66			Input Control
#	M67			Analog Output Control
#	M68			Analog Output Control
#	M70			Save Modal State
#	M71			Invalidate Stored Modal State
#	M72			Restore Modal State
#	M73			Save Autorestore Modal State
#	M98	M99		Call and Return From Subprogram
#	M100-M199	User Defined M-Codes
#
	$mcode['M0'] = '?';
	$mcode['M1'] = '?';
	$mcode['M2'] = true;
	$mcode['M30'] = true;
	$mcode['M60'] = true;
	$mcode['M3'] = '$###';
	$mcode['M4'] = '$###';
	$mcode['M5'] = '$###';
	$mcode['M6'] = true;
	$mcode['M7'] = true;
	$mcode['M8'] = true;
	$mcode['M9'] = true;
	$mcode['M19'] = true;
	$mcode['M48'] = true;
	$mcode['M49'] = true;
	$mcode['M50'] = true;
	$mcode['M51'] = true;
	$mcode['M52'] = true;
	$mcode['M53'] = true;
	$mcode['M61'] = true;
	$mcode['M62'] = true;
	$mcode['M63'] = true;
	$mcode['M64'] = true;
	$mcode['M65'] = true;
	$mcode['M66'] = true;
	$mcode['M67'] = true;
	$mcode['M68'] = true;
	$mcode['M70'] = true;
	$mcode['M71'] = true;
	$mcode['M72'] = true;
	$mcode['M73'] = true;
	$mcode['M98'] = true;
	$mcode['M99'] = true;
#
#	M100 - M199
#
	for( $i=100; $i<200; $i++ ){
		$str = sprintf( "M%03d", $i );
		$mcode[$str] = true;
		}
#
#	From the manual. They say to put this in
#
	$this->default_init = "G17 G21 G40 G49 G54 G80 G90 G94";
#
#	Use the MODE command to find all of the modes
#
	if( exec("mode", $out, $ret) === false ){
		die( "***** ERROR : Could not execute the MODE command - aboring.\n" );
		}
#	
#	Status for device COM1:
#	-----------------------
#	    Baud:            1200
#	    Parity:          None
#	    Data Bits:       7
#	    Stop Bits:       1
#	    Timeout:         OFF
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
#
#	Make the MODE array.
#
	$mode = [];
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
				$mode[$dev] = [];
				$devices[] = $dev;
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
			$title = substr( $title, 0, -1 );
			$mode[$dev][strtolower($title)] = strtolower( $info );
			}
		}

	$this->mode = $mode;
	return $devices;
}
################################################################################
#	set_device(). Sets which mode to use.
################################################################################
function set_device( $dev )
{
	$this->device = $dev;
	return true;
}
################################################################################
#	get_devices(). Get the list of devices the MODE command found.
################################################################################
function get_devices()
{
	$devices = [];
	foreach( $this->modes as $k=>$v ){
		$devices[] = $k;
		}

	return $devices;
}
################################################################################
#	com_setup(). Set up all of the parts of the communication information.
################################################################################
function com_setup( $com=null )
{
	$pr = $this->pr;

	$dev = [];
	$mode = $this->mode;
#
#	If nothing was sent - set COM to the fastest device
#
	if( is_null($com) ){
		$baud = 0;
		foreach( $mode as $k=>$v ){
			if( isset($v['baud']) ){
				if( $v['baud'] > $baud ){
					$com = $k;
					$baud = $v['baud'];
					}
				}
			}
		}
#
#	Now get all of the information set
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
#				com4:115200,n,8,1,cs0,cd0,ds0,rs
#
#	Set the com info
#
	$dev['com'] = $com;
#
#	Get the baud rate
#
	$dev['baud'] = $mode[$com]['baud'];
#
#	Get the parity
#
	if( isset($mode[$com]['parity']) ){
		$parity = substr( $mode[$com]['parity'], 0, 1 );
		}
		else { $parity = "n"; }

	$dev['parity'] = $parity;
#
#	Get the data bits
#
	if( isset($mode[$com]['data bits']) ){
		$data_bits = $mode[$com]['data bits'];
		}
		else { $data_bits = "8"; }

	$dev['data_bits'] = $data_bits;
#
#	Get the stop bits
#
	if( isset($mode[$com]['stop bits']) ){
		$stop_bits = $mode[$com]['stop_bits bits'];
		}
		else { $stop_bits = "1"; }

	$dev['stop_bits'] = $stop_bits;
#
#	NOTE : All of the following is taken from the FreeBasic manual.
#		Also, ALL of these are optional - BUT you should always use
#		the csn, dsn, csn, and rs. THOSE work.
#
#	'CSn' Set the CTS duration (in ms) (n>=0), 0 = turn off, default = 1000 
#
	$dev['csn'] = "cs0";
#
#	'DSn' Set the DSR duration (in ms) (n>=0), 0 = turn off, default = 1000 
#
	$dev['dsn'] = "ds0";
#
#	'CDn' Set the Carrier Detect duration (in ms) (n>=0), 0 = turn off 
#
	$dev['cdn'] = "cd0";
#
#	'OPn' Set the 'Open Timeout' (in ms) (n>=0), 0 = turn off 
#
	$dev['opn'] = "op0";	#	Optional
#
#	'TBn' Set the 'Transmit Buffer' size (n>=0), 0 = default, depends on platform 
#
	$dev['tbn'] = "tb0";	#	Optional
#
#	'RBn' Set the 'Receive Buffer' size (n>=0), 0 = default, depends on platform 
#
	$dev['rbn'] = "rb0";	#	Optional
#
#	'RS' Suppress RTS detection 
#
	$dev['rs'] = "rs";
#
#	'LF' Communicate in ASCII mode (add LF to every CR) - Win32 doesn't support this one 
#
	$dev['lf'] = "lf";
#
#	'ASC' same as 'LF' 
#
	$dev['asc'] = "asc";
#
#	'BIN' The opposite of LF and it'll always work 
#
	$dev['bin'] = "bin";
#
#	'PE' Enable 'Parity' check 
#
	$dev['pe'] = "pe";
#
#	'DT' Keep DTR enabled after CLOSE 
#
	$dev['dt'] = "dt";
#
#	'FE' Discard invalid character on error 
#
	$dev['fe'] = "fe";
#
#	'ME' Ignore all errors 
#
	$dev['me'] = "me";
#
#	'IRn' IRQ number for COM (only supported (?) on DOS) 
#
	$dev['irn'] = "ir0";

	return $dev;
}
################################################################################
#	__destruct(). Be sure to close everything.
################################################################################
function __destruct()
{
	if( is_resource($this->cp) ){ close( $this->cp ); }
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['grbl']) ){
		$GLOBALS['classes']['grbl'] = new class_grbl();
		}

?>
