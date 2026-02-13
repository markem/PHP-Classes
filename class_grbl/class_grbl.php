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
	private $cs = null;

	private $codes = null;
	private	$errorCodes = null;
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
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$this->cf = $cf = new class_files();
	$this->cs = $cf = new class_serial();
	$this->pr = $cf = new class_pr();

	$codes = [];
	$this->errorCodes = $this->errorCodes();
#
#	GRBL Code Parameters
#		Note: ### really means any number. Even ###.#####
#
#	X### Y### Z###	-	Distances or positions on the X Y Z axes
#	I### J### K###	-	same as above but used with G2 or G3 Arcs.
#	L###	-	Loop Cycle Count, supported but not used.
#	N###	-	Line Number, supported but not used.
#	R###	-	Arc radius for G2 and G#
#	P###	-	Multi-purpose parameter depends on command it is used in
#	T###	-	Tool selection, not used
#
#	G Code commands and what they do
#
	$codes['g0'] = 'Z1';		#	
	$codes['g1'] = 'F8000';		#	G1 F8000 (set speed)
	$codes['g20'] = false;		#	Use inches
	$codes['g21'] = true;		#	Use millimeters
	$codes['g28'] = true;		#	Auto homing
	$codes['g90'] = true;		#	Absolute positioning
	$codes['g92'] = 'X0 Y0';	#	Set Origin at whereever you currently are at
	$codes['g93'] = false;		#	Inverse time feed
#
#	Misc GRBL commands
#	


	$codes['001'] = "Grbl 1.1e ['$' for help]";	#	This is what you should get
	$codes['$'] = '$$ (Display Grbl Settings)';
	$codes['$-ret'] = '[HLP:$$ $# $G $I $N $x=val $Nx=line $J=line $SLP $C $X $H ~ ! ? ctrl-x]';
	$codes['$#'] = '$# (View GCode Parameters)';
	$codes['$$'] = true;
	$codes['$G'] = '$G (View GCode parser state)';
	$codes['$C'] = '$C (Toggle Check Gcode Mode)';
	$codes['$H'] = '$H (Run Homing Cycle)';
	$codes['$J'] = '$J=gcode (Run Jogging Motion)';
	$codes['$X'] = '$X (Kill Alarm Lock state)';
	$codes['$I'] = '$I (View Build Info)';
	$codes['$N'] = '$N (View saved start up code)';
	$codes['$Nx=line'] =
		'$N#=line (Save Start-up GCode line (x=0 or 1) Which are executed on a reset)';
	$codes['$RST=$'] = '$RST=$ (Restores the Grbl settings to defaults)';
	$codes['$RST=#'] =
		'$RST=# (Erases G54-G59 WCS offsets and G28/30 positions stored in EEPROM)';
	$codes['$RST=*'] = '$RST=* (Clear and Load all data from EEPROM)';
	$codes['$SLP'] = '$SLP (Enable Sleep mode)';
	$codes['Ctrl-x'] = 'Ctrl-x (Soft Reset)';
	$codes['?'] = '? (Status report query)';
	$codes['~'] = '~ (Cycle Start/Resume from Feed Hold, Door or Program pause)';
	$codes['!'] = '! (Feed Hold – Stop all motion)';

	$codes['$x=val'] = "\$x=\$val (Change Grbl Setting x to val)";
	$codes['$0']	= 10;		#	Step pulse, microseconds
	$codes['$1']	= 25;		#	Step idle delay, milliseconds.
	$codes['S'] = '#####';		#	Set Spindle speed in RPMs or Laser Power.
#
#	$1=255= Turn everything ON. $1=0 Turns everything OFF (DO NOT DO THIS!)
#
	$codes['$2']	= 0;		#	Step port invert, mask
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
	$codes['$3']	= 0;		#	Direction port invert, mask
#
#	Use the above table for $3 too.
#
	$codes['$4']	= 0;		#	Step enable invert, boolean
	$codes['$5']	= 0;		#	Limit pins invert, boolean
	$codes['$6']	= 0;		#	Probe pin invert, boolean
	$codes['$10']	= 0;		#	Status report, WCS position
	$codes['$10']	= 1;		#	Status report, Machine position
	$codes['$10']	= 2;		#	Status report, plan/buffer and WCS position
	$codes['$10']	= 3;		#	Status report, plan/buffer and Machine position
#
#--------------------------------------------------------------------------------
#		Report Type		Value	Description
#		------------------------------------------------------------
#		PositionType	0		Enable WPos: . Disable MPos: .
#		PositionType	1		Enable MPos: . Disable WPos: .
#		Buffer Data		2		Enabled Buf:
#									field appears with planner
#									and serial RX availablebuffer.
#	$ commands and what they mean. Taken from:
#
#		https://diymachining.com/grbl-feed-rate/
#--------------------------------------------------------------------------------
#
#		$0=10 (step pulse, usec)
#		$1=25 (step idle delay, msec)
#		$2=0 (step port invert mask:00000000)
#		$3=3 (dir port invert mask:00000011)
#		$4=0 (step enable invert, bool)
#		$5=0 (limit pins invert, bool)
#		$6=0 (probe pin invert, bool)
#		$10=3 (status report mask:00000011)
#		$11=0.010 (junction deviation, mm)
#		$12=0.002 (arc tolerance, mm)
#		$13=1 (report inches, bool)
#		$20=0 (soft limits, bool)
#		$21=0 (hard limits, bool)
#		$22=0 (homing cycle, bool)
#		$23=0 (homing dir invert mask:00000000)
#		$24=25.000 (homing feed, mm/min)
#		$25=500.000 (homing seek, mm/min)
#		$26=250 (homing debounce, msec)
#		$27=1.000 (homing pull-off, mm)
#		$100=314.960 (x, step/mm)
#		$101=314.960 (y, step/mm)
#		$102=78.740 (z, step/mm)
#		$110=800.000 (x max rate, mm/min)
#		$111=800.000 (y max rate, mm/min)
#		$112=350.000 (z max rate, mm/min)
#		$120=10.000 (x accel, mm/sec^2)
#		$121=10.000 (y accel, mm/sec^2)
#		$122=10.000 (z accel, mm/sec^2)
#		$130=200.000 (x max travel, mm)
#		$131=200.000 (y max travel, mm)
#		$132=200.000 (z max travel, mm)
#--------------------------------------------------------------------------------
#
	$codes['$11']	= 0.100;	#	Junction deviation, mm
	$codes['$12']	= 0.002;	#	Arc tolerance, mm
	$codes['$13']	= 0;		#	Report inches, boolean
	$codes['$20']	= 0;		#	Soft limits, boolean
	$codes['$21']	= 0;		#	Hard limits, boolean
	$codes['$22']	= 1;		#	Homing cycle, boolean
	$codes['$23']	= 0;		#	Homing dir invert, mask
	$codes['$24']	= 25.000;	#	Homing feed, mm/min
	$codes['$25']	= 500.00;	#	Homing seek, mm/min
	$codes['$26']	= 250;		#	Homing debounce, milliseconds
	$codes['$27']	= 1.000;	#	Homing pull-off, mm
	$codes['$30']	= 1000;		#	Max spindle speed, RPM
	$codes['$31.']	= 0.;		#	Min spindle speed, RPM
	$codes['$32']	= 0;		#	Laser mode, boolean
	$codes['$100']	= 25.000;	#	X steps/mm - NEEDS SOFT RESET
	$codes['$101']	= 25.000;	#	Y steps/mm - NEEDS SOFT RESET
	$codes['$102']	= 250.000;	#	Z steps/mm - NEEDS SOFT RESET
	$codes['$110']	= 5000.000;	#	X Max rate, mm/min
	$codes['$111']	= 5000.00;	#	Y Max rate, mm/min
	$codes['$112']	= 500.00;	#	Z Max rate, mm/min
	$codes['$120']	= 400.000;	#	X Acceleration, mm/sec^2
	$codes['$121']	= 400.000;	#	Y Acceleration, mm/sec^2
	$codes['$122']	= 10.000;	#	Z Acceleration, mm/sec^2
	$codes['$130']	= 1000.000;	#	X Max travel, mm
	$codes['$131']	= 1000.000;	#	Y Max travel, mm
	$codes['$132']	= 200.000;	#	Z Max travel, mm
	$codes['com'] = "com(3|4):115200,n,8,1,cs0,cd0,ds0,rs";
	$codes['MPos:'] = true;	#	Machine Position
	$codes['WPos:'] = true;	#	Work Position
#
#	Code		Description
#	-----------	-----------------------------------------
#	M0			Program Pause
#	M1			Same as M0 but only pauses if an optional stop switch is on
#	M2			Program End
#	M3			Start spindle clockwise. In Laser mode sets Constant power
#	M4			As M3, In Laser Mode sets Dynamic power
#	M5			Stop the Spindle
#	M6			Tool Change
#	M7			Coolant Control. Coolant on as a flood.
#	M8 			Coolant Control. Coolant on as a flood.
#	M9			Coolant Control. Coolant off
#	M19			Orient Spindle
#	M30			Same as M2
#	M48 M49		Feed & Spindle Overrides Enable/Disable
#	M50			Feed Override Control
#	M51			Spindle Override Control
#	M52			Adaptive Feed Control
#	M53			Feed Stop Control
#	M60			Pallet Change Pause
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
	$codes['M0'] = '?';
	$codes['M1'] = '?';
	$codes['M2'] = true;
	$codes['M3'] = '$###';
	$codes['M4'] = '$###';
	$codes['M5'] = '$###';
	$codes['M6'] = true;	#	Tool change
	$codes['M7'] = true;
	$codes['M8'] = true;
	$codes['M9'] = true;
	$codes['M19'] = true;
	$codes['M48'] = true;
	$codes['M30'] = true;
	$codes['M49'] = true;
	$codes['M50'] = true;
	$codes['M51'] = true;
	$codes['M52'] = true;
	$codes['M53'] = true;
	$codes['M60'] = true;
	$codes['M61'] = true;
	$codes['M62'] = true;
	$codes['M63'] = true;
	$codes['M64'] = true;
	$codes['M65'] = true;
	$codes['M66'] = true;
	$codes['M67'] = true;
	$codes['M68'] = true;
	$codes['M70'] = true;
	$codes['M71'] = true;
	$codes['M72'] = true;
	$codes['M73'] = true;
	$codes['M98'] = true;
	$codes['M99'] = true;
#
#	M100 - M199
#
	for( $i=100; $i<200; $i++ ){
		$str = sprintf( "M%03d", $i );
		$codes[$str] = true;
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
#	getCodes_1(). Convert the Grbl Supported GCodes
################################################################################
function getCodes_1( $code=null )
{
	$info = <<<EOD
F	Set Feed rate in Units/min (See G20/G21).
G0	A Rapid positioning move at the Rapid Feed Rate. In Laser mode Laser will be turned off.
G1	A Cutting move in a straight line. At the Current F rate.
G2	Cut a Clockwise arc.
G3	Cut an Anti-Clockwise arc.
G4	Pause command execution for the time in Pnnn. P specifies the time in seconds. Other systems use milliseconds as the pause time, if used unchanged this can result in VERY long pauses.
G10L2	Sets the offset for a saved origin using absolute machine coordinates.
G10L20	As G10 L2 but the XYZ parameters are offsets from the current position.
G17	Draw Arcs in the XY plane, default.
G18	Draw Arcs in the ZX plane.
G19	Draw Arcs in the YZ plane.
G20	All distances and positions are in Inches
G21	All distances and positions are in mm
G28	Go to safe position. NOTE: If you have not run a homing cycle and have set the safe position this is very ‘unsafe’ to use.
G28.1	Set Safe position using absolute machine coordinates.
G30	Go to the saved G30 position.
G30.1	Set Predefined position using absolute machine coordinates, a rapid G0 move to that position will be performed before the coordinates are saved.
G38.2	Probe towards the stock, error on a failure.
G38.3	As G38.2, no error on failure
G38.4	As G38.2 but move away, stop on a loss of contact.
G38.5	As G38.4, no error on failure.
G40	Cutter Compensation off. Grbl does not support cutter compensation.
G43.1	Dynamic Tool length offset, offsets Z end of tool position for subsequent moves.
G49	Cancel Tool length Offset.
G53	Use machine coordinates in this command.
G54	Activate the relevant saved origin.
G55	As G54, activates a different saved position
G56	As G54, activates a different saved position
G57	As G54, activates a different saved position
G58	As G54, activates a different saved position
G59	As G54, activates a different saved position
G61	Exact Path mode. Grbl does not support any other modes.
G80	Canned Cycle Cancel. Grbl does not support any of the canned cycle modes which this cancels so it does nothing.
G90	All distances and positions are Absolute values from the current origin.
G91	All distances and positions are Relative values from the current position.
G91.1	Sets Arc incremental position mode
G92	Sets the current coordinate point, used to set an origin point of zero, commonly known as the home position.
G92.1	Reset any G92 offsets in effect to zero and zero any saved values
G93	Inverse time motion mode.
G94	Units/min mode at the current F rate.
M0	Pause.
M1	As M0 but only pauses if an optional stop switch is on.
M2	Program End, turn off spindle/laser and stops the machine.
M3	Start spindle clockwise. In Laser mode sets Constant power.
M4	As M3, In Laser Mode sets Dynamic power.
M5	Stop the Spindle
M8	Coolant on as a flood. (Same as M7)
M9	Coolant off.
M30	Same as M2.
S	Set Spindle speed in RPM or Laser Power.
Notes	Codes can contain leading zeros, G0 and G00 are the same. There are loads more GCodes, these are the ones Grbl supports. A lot of commands are Modal meaning they are remembered and applied to subsequent commands. For example, G0 X1 followed by Z5 remembers the G0 Mode and applies it to the Z5. S is modal, remembered from the last command. Two commands in the same modal group cannot be on the same line.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_2(). Convert the Grbl Code Parameters
################################################################################
function getCodes_2( $code= null )
{
#
#	GRBL Code Parameters
#		Note: ### really means any number. Even ###.#####
#
#	X### Y### Z###	-	Distances or positions on the X Y Z axes
#	I### J### K###	-	same as above but used with G2 or G3 Arcs.
#	L###	-	Loop Cycle Count, supported but not used.
#	N###	-	Line Number, supported but not used.
#	R###	-	Arc radius for G2 and G#
#	P###	-	Multi-purpose parameter depends on command it is used in
#	T###	-	Tool selection, not used
#
	$info = <<<EOD
X	Distances or positions on the X Y Z axes.
Y	Distances or positions on the X Y Z axes.
Z	Distances or positions on the X Y Z axes.
I	Distances or positions for G2 and G3 Arcs. Correspond to the X axis respectively. These are always incremental coordinates regardless of G90/G91
J	Distances or positions for G2 and G3 Arcs. Correspond to the Y axis respectively. These are always incremental coordinates regardless of G90/G91
K	Distances or positions for G2 and G3 Arcs. Correspond to the Z axis respectively. These are always incremental coordinates regardless of G90/G91
L	Loop Cycle Count, supported but not used.
N	Line Number, supported but not used.
R	Arc radius for G2 and G3.
P	Multi-purpose parameter depends on command it is used in.
T	Tool selection, not used.
Notes	All parameters must be followed by a number.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_3(). Convert the Grbl States
################################################################################
function getCodes_3( $code= null )
{
	$info = <<<EOD
Alarm	Homing enabled but homing cycle not run or error has been detected such as limit switch activated. Home or unlock to resume.
Idle	Waiting for any command.
Jog	Performing jog motion, no new commands until complete, except Jog commands.
Homing	Performing a homing cycle, won’t accept new commands until complete.
Check	Check mode is enabled; all commands accepted but will only be parsed, not executed.
Cycle	Running GCode commands, all commands accepted, will go to Idle when commands are complete.
Hold	Pause is in operation, resume to continue.
Safety Door	The safety door switch has been activated, similar to a Hold but will resume on closing the door. You probably don’t have a safety door on your machine!
Sleep	Sleep command has been received and executed, sometimes used at the end of a job. Reset or power cycle to continue.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_4(). Convert the Grbl Error Codes
################################################################################
function getCodes_4( $code= null )
{
	$info = <<<EOD
1	GCode Command letter was not found.
2	GCode Command value invalid or missing.
3	Grbl '$' not recognized or supported.
4	Negative value for an expected positive value.
5	Homing fail. Homing not enabled in settings.
6	Min step pulse must be greater than 3usec.
7	EEPROM read failed. Default values used.
8	Grbl '$' command Only valid when Idle.
9	GCode commands invalid in alarm or jog state.
10	Soft limits require homing to be enabled.
11	Max characters per line exceeded. Ignored.
12	Grbl '$' setting exceeds the maximum step rate.
13	Safety door opened and door state initiated.
14	Build info or start-up line > EEPROM line length
15	Jog target exceeds machine travel, ignored.
16	Jog Cmd missing '=' or has prohibited GCode.
17	Laser mode requires PWM output.
20	Unsupported or invalid GCode command.
21	> 1 GCode command in a modal group in block.
22	Feed rate has not yet been set or is undefined.
23	GCode command requires an integer value.
24	> 1 GCode command using axis words found.
25	Repeated GCode word found in block.
26	No axis words found in command block.
27	Line number value is invalid.
28	GCode Cmd missing a required value word.
29	G59.x WCS are not supported.
30	G53 only valid with G0 and G1 motion modes.
31	Unneeded Axis words found in block.
32	G2/G3 arcs need >= 1 in-plane axis word.
33	Motion command target is invalid.
34	Arc radius value is invalid.
35	G2/G3 arcs need >= 1 in-plane offset word.
36	Unused value words found in block.
37	G43.1 offset not assigned to tool length axis.
38	Tool number greater than max value.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_5(). Convert the Grbl Alarm Codes
################################################################################
function getCodes_5( $code= null )
{
	$info = <<<EOD
1	Hard limit triggered. Position Lost.
2	Soft limit alarm, position kept. Unlock is Safe.
3	Reset while in motion. Position lost.
4	Probe fail. Probe not in expected initial state.
5	Probe fail. Probe did not contact the work.
6	Homing fail. The active homing cycle was reset.
7	Homing fail. Door opened during homing cycle.
8	Homing fail. Pull off failed to clear limit switch.
9	Homing fail. Could not find limit switch.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_6(). Convert the Grbl Non Gcode Commands
################################################################################
function getCodes_6( $code= null )
{
	$info = <<<EOD
$$	Display Grbl Settings.
$x=val	Change Grbl Setting x to val.
$#	View GCode Parameters.
$G	View GCode parser state.
$C	Toggle Check Gcode Mode
$H	Run Homing Cycle
$J=gcode	Run Jogging Motion.
$X	Kill Alarm Lock state.
$I	View Build Info
$N	View saved start up code
$Nx=line	Save Start-up GCode line (x=0 or 1) There are executed on a reset.
$RST=$	Restores the Grbl settings to defaults.
$RST=#	Erases G54-G59 WCS offsets and G28/30 positions stored in EEPROM.
$RST=*	Clear and Load all data from EEPROM.
$SLP	Enable Sleep mode.
Ctrl-x	Soft Reset
?	Status report query.
~	Cycle Start/Resume from Feed Hold, Door or Program pause.
!	Feed Hold – Stop all motion.
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	getCodes_7(). Convert the Grbl Settings
################################################################################
function getCodes_7( $code= null )
{
	$info = <<<EOD
$0	Step pulse, microseconds
$1	Step idle delay, milliseconds
$2	Step port invert, XYZmask*
$3	Direction port invert, XYZmask* The direction each axis moves.
$4	Step enable invert, (0=Disable, 1=Invert)
$5	Limit pins invert, (0=N-Open. 1=N-Close)
$6	Probe pin invert, (0=N-Open. 1=N-Close)
$10	Status report, ‘?’ status.  0=WCS position, 1=Machine position, 2= plan/buffer and WCS position, 3=plan/buffer and Machine position.
$11	Junction deviation, mm
$12	Arc tolerance, mm
$13	Report in inches, (0=mm. 1=Inches)**
$20	Soft limits, (0=Disable. 1=Enable, Homing must be enabled)
$21	Hard limits, (0=Disable. 1=Enable)
$22	Homing cycle, (0=Disable. 1=Enable)
$23	Homing direction invert, XYZmask* Sets which corner it homes to.
$24	Homing feed, mm/min
$25	Homing seek, mm/min
$26	Homing debounce, milliseconds
$27	Homing pull-off, mm
$30	Max spindle speed, RPM
$31	Min spindle speed, RPM
$32	Laser mode, (0=Off, 1=On)
$100	Number of X steps to move 1mm
$101	Number of Y steps to move 1mm
$102	Number of Z steps to move 1mm
$110	X Max rate, mm/min
$111	Y Max rate, mm/min
$112	Z Max rate, mm/min
$120	X Acceleration, mm/sec^2
$121	Y Acceleration, mm/sec^2
$122	Z Acceleration, mm/sec^2
$130	X Max travel, mm Only for Homing and Soft Limits.
$131	Y Max travel, mm Only for Homing and Soft Limits.
$132	Z Max travel, mm Only for Homing and Soft Limits.
Notes	* XYZmask is a value setting for the X Y and Z axes. Change if an axis is moving in the wrong direction. Value will be 0-7. ** Reporting units are independent of the units set in the Gcode!
EOD;

	if( is_null($code) ){ $code = []; }
	$a = explode( '	', $info );

	foreach( $a as $k=>$v ){ $code[$k] = wordwrap($v); }

	return $code;
}
################################################################################
#	set_device(). Sets which device to use.
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
#	get_ini(). Gets the VIGO.INI file and then updates the information
################################################################################
function get_ini( $file=null, $cs=null )
{
	if( is_null($file) ){ $file = "./vigo.ini"; }
	if( is_null($cs) ){ $cs = $this->cs; }

	$vigo = [];
	$vigo['$'] = [];
	$vigo['$#'] = [];
	$vigo['$$'] = [];
	$vigo['x'] = [];
	$vigo['y'] = [];
	$vigo['z'] = [];

	if( file_exists($file) ){ $a = file_get_contents( $file ); }
		else { return $vigo; }
#
#	If $a is a json file - then just return that.
#
	if( !is_null($b = json_decode($a, false)) ){ return $b; }

	$a = explode( "\n", $a );
	if( count($a) < 6 ){ return $vigo; }

	foreach( $a as $k=>$v ){
#
#	Split up the line. Remember there are lines like $$.0 = $0=10!
#
		$b = explode( '=', $v );
#
#	Handle the title
#	[HLP:$$ $# $G $I $N $x=val $Nx=line $J=line $SLP $C $X $H ~ ! ? ctrl-x]
#
		if( preg_match("/title/i", $v) ){ $vigo['title'] = $b[1]; }
#
#	Handle the '$' stuff
#
		if( preg_match("/^\$\./", $b[0]) ){
			if( preg_match("/HLP/", $v) ){
				$v = substr( $v, 0, -1 );
				$v = substr( $v, 1, strlen($v) );

				$vigo['$'] = [];
				$vigo['$']['HLP'] = [];
				$b = explode( ':', $v );
				$c = explode( ' ', $b[1] );
				foreach( $c as $k1=>$v1 ){
					$d = explode( '=', $v1 );
					if( count($d) < 2 ){ $vigo['$']['HLP'][$d[0]] = $d[0]; }
						else { $vigo['$']['HLP'][$d[0]] = $d[1]; }
					}
				}

			if( preg_match("/\$\#/", $v) ){ $vigo['$']['$#'] = null; }
			if( preg_match("/\$G/i", $v) ){ $vigo['$']['$G'] = null; }
			if( preg_match("/\$I/", $v) ){ $vigo['$']['$I'] = null; }
			if( preg_match("/\$N/", $v) ){ $vigo['$']['$N'] = null; }
			if( preg_match("/\$x/", $v) ){ $vigo['$']['$x'] = $b[1]; }
			if( preg_match("/\$Nx/i", $v) ){ $vigo['$']['$Nx'] = $b[1]; }
			if( preg_match("/\$J/i", $v) ){ $vigo['$']['$J'] = $b[1]; }
			if( preg_match("/\$slp/i", $v) ){ $vigo['$']['$slp'] = null; }
			if( preg_match("/\$c/i", $v) ){ $vigo['$']['$c'] = null; }
			if( preg_match("/\$X/", $v) ){ $vigo['$']['$X'] = null; }
			if( preg_match("/\$H/", $v) ){ $vigo['$']['$H'] = null; }
			if( preg_match("/\~/", $v) ){ $vigo['$']['~'] = null; }
			if( preg_match("/\!/", $v) ){ $vigo['$']['!'] = null; }
			if( preg_match("/\?/", $v) ){ $vigo['$']['?'] = null; }
			if( preg_match("/ctrl-x/", $v) ){ $vigo['$']['ctrl-x'] = null; }
			}
#
#	Handle the '$$' stuff
#
		if( preg_match("/^\$\$\./", $b[0]) ){
			$c = explode( '.', $b[1] );
			$vigo['$$'][$c[0]] = $c[1];
			}
#
#	Handle the '$#' stuff
#
		if( preg_match("/\$\#\./", $v) ){
			$b = explode( '=', $a[$k+$i] );
			$c = explode( '.', $b[0] );
			$d = explode( ',', $b[1] );
			$e = explode( ':', $d[0] );
			$e[0] = substr( $e[0], 1, 256 );
			$d[2] = substr( $d[2], 0, -1 );
			$vigo['$#'][$e[0]] = $e[1];
			$vigo['$#'][$e[0]] = $d[1];
			$vigo['$#'][$e[0]] = $d[2];
			}
#
#	Handle the X stuff (Min/Max)
#
		if( preg_match("/^x\./", $b[0]) ){
			$c = explode( '.', $b[0] );
			$vigo['x'][$c[1]] = $b[1];
			}
#
#	Handle the Y stuff (Min/Max)
#
		if( preg_match("/^y\./", $b[0]) ){
			$c = explode( '.', $b[0] );
			$vigo['y'][$c[1]] = $b[1];
			}
#
#	Handle the Z stuff (pen up/down)
#
		if( preg_match("/^z\./", $b[0]) ){
			$c = explode( '.', $b[0] );
			$vigo['z'][$c[1]] = $b[1];
			}
#
#--------------------------------------------------------------------------------
#	Put new things here
#--------------------------------------------------------------------------------
#
		}

	return $vigo;
}
################################################################################
#	upd_ini(). Updates all of the information to the most current information.
#--------------------------------------------------------------------------------
#	*** Fetching device state
#	ok
#	>>> $G
#	[GC:G0 G54 G17 G21 G90 G94 M5 M9 T0 F0 S0]
#	ok
#	*** Connected to GRBL 1.1f
#	
################################################################################
function upd_ini( $ini=null )
{
	$pr = $this->pr;
	$cs = $this->cs;

	$vigo = [];
	$vigo['$'] = [];
	$vigo['$#'] = [];
	$vigo['$$'] = [];
	$vigo['x'] = [];
	$vigo['y'] = [];
	$vigo['z'] = [];

	if( is_object($ini) ){ $ini = []; }
		else if( count($ini) < 6 ){ $ini = []; }
#
#	First we have to get the modes to work with
#
	$modes = $cs->modes();

	if( isset($modes['com3']) ){ $cs->set( $modes['com3'] ); }
		else if( isset($modes['com4']) ){ $cs->set( $modes['com4'] ); }
		else { die( "Unknown mode\n" ); }
#
#	*** Connecting to jserialcomm://COM4:115200
#
#	Open the communication line and the first thing we should get is the title.
#
	$cs->open();
#
#	Because I have taken the "Get the title" thing out of the serial class
#	and put it here instead. I'm doing this because I do not think the serial
#	class should do the thinking about what to do but instead - simply do
#	whatever the user wants it to do.
#
	$cs->write( "" );
	$ret = $cs->read();
#
#	The VigoWriter requires you to first send a return in order to get
#	the banner of the plotter.
#
	$vigo['title'] = $ret;
#
#	*** Fetching device status
#
#	According to the Universal GRBL System program - I need to get the '?'
#	command's information and I should get something like this:
#
#	<Idle|MPos:0.000,0.000,0.000|FS:0,0|WCO:-50.000,0.000,0.000>
#
#	Idle = Not doing anything
#	MPos = The Machine's position
#	FS = ???
#	WCO = ???
#
	$cs->write( "?" );
	$ret = $cs->read();
#
#	The VigoWriter requires you to first send a return in order to get
#	the banner of the plotter.
#
#
#	First we get rid of the angle brackets (<>)
#
	$ret = trim( $ret );
	$ret = substr( $ret, 1, strlen($ret) );
	$ret = substr( $ret, 0, -1 );
#
#	Now explode it
#
	$a = explode( '|', $ret );
	$vigo['?'] = [];
	foreach( $a as $k=>$v ){
		$b = explode( ':', $v );
		if( count($b) < 2 ){ $vigo['?'][$b[0]] = $b[0]; }
			else {
				$vigo['?'][$b[0]] = [];
				$c = explode( ',', $b[1] );
				foreach( $c as $k1=>$v1 ){
					$vigo['?'][$b[0]][$k1] = $v1;
					}
				}
		}
#
#	*** Fetching device version
#	>>> $I
#	[VER:1.1f.20170131:]
#	[OPT:VC,15,128]
#
	$cs->write( '$I' );
	$ret = $cs->read();
#
#	The VigoWriter requires you to first send a return in order to get
#	the banner of the plotter.
#
	$ret = explode( "\n", $ret );
#
#	Now we need to split up the incoming information
#
	foreach( $ret as $k=>$v ){
		$v = trim( $v );
		$a = substr( $v, 1, strlen($v) );
		$a = substr( $a, 0, -1 );
		if( preg_match("/ver:/i", $v) ){
			$a = explode( ':', $a );
			$vigo['$I'] = [];
			$vigo['$I']['VER'] = $a[1];
			}
			else if( preg_match("/opt:/i", $v) ){
				$a = explode( ':', $a );
				$vigo['$I']['OPT'] = [];
				$b = explode( ',', $a[1] );
				foreach( $b as $k1=>$v1 ){
					$vigo['$I']['OPT'][$k1] = $v1;
					}
				}
			else { die( "***** ERROR : Unknown command = $a\n" ); }
		}
#
#	Then we send a dollar sign ($) which gives us a status of the plotter
#
	$cs->write( '$' );
	$ret = $cs->read();
	$ret = trim( $ret );
	$ret = substr( $ret, 1, strlen($ret) );
	$ret = substr( $ret, 0, -1 );
	
	$a = explode( " ", $ret );

	foreach( $a as $k=>$v ){
#
#	Handle the '$' stuff
#
		if( preg_match("/HLP/", $v) ){
			$b = explode( ':', $v );
			$vigo['$'][$b[0]] = $b[1];
			}

		$b = explode( '=', $v );
		if( count($b) < 2 ){ $b[1] = null; }
		$vigo['$'][$b[0]] = $b[1];
		}
#
#	*** Fetching device settings
#
#	Then we send two dollar signs ($$) to the plotter which gives us even more
#	information about the plotter.
#
	$cs->write( '$$' );
	$ret = $cs->read();
	$a = explode( "\n", $ret );

	foreach( $a as $k=>$v ){
#
#	Handle the '$$' stuff
#
		$v = trim( $v );
		$b = explode( '=', $v );
		$vigo['$$'][$b[0]] = $b[1];
		}
#
#	Then we send a dollar sign - pound sign ($#) to the plotter
#	which gives us even more information about the plotter.
#
	$cs->write( '$#' );
	$ret = $cs->read();
	$a = explode( "\n", $ret );

	foreach( $a as $k=>$v ){
#
#	Handle the '$#' stuff
#	[TLO:0.000]
#
		$v = trim( $v );
		$v = substr( $v, 1, strlen($v) );
		$v = substr( $v, 0, -1 );

		$b = explode( ':', $v );
		$c = explode( ',', $b[1] );
		foreach( $c as $k=>$v ){
			$vigo['$#'][$b[0]][$k] = $v;
			}
		}
#
#	Handle the X stuff (Min/Max)
#	This is from my testing of the VigoWriter.
#
	if( isset($ini['x']) ){
		if( isset($ini['x']['min']) ){ $vigo['x']['min'] = $ini['x']['min']; }
			else { $vigo['x']['min'] = 0; }

		if( isset($ini['x']['max']) ){ $vigo['x']['max'] = $ini['x']['max']; }
			else { $vigo['x']['max'] = 0; }
		}
		else {
			$vigo['x']['min'] = 0;
			$vigo['x']['max'] = 1300;	#	Maximum we go on an 8.5" x 11" paper
			}
#
#	Handle the Y stuff (Min/Max)
#	This is from my testing of the VigoWriter.
#
	if( isset($ini['y']) ){
		if( isset($ini['y']['min']) ){ $vigo['y']['min'] = $ini['y']['min']; }
			else {$vigo['y']['min'] = 0; }

		if( isset($ini['y']['max']) ){ $vigo['y']['max'] = $ini['y']['max']; }
			else {$vigo['y']['max'] = 1750; }
		}
		else {
			$vigo['y']['min'] = 0;
			$vigo['y']['max'] = 1750;
			}
#
#	Handle the Z stuff (pen up/down)
#	This is from my testing of the VigoWriter.
#
	if( isset($ini['z']) ){
		if( isset($ini['z']['up']) ){ $vigo['z']['up'] = $ini['z']['up']; }
			else { $vigo['z']['up'] = 0; }

		if( isset($ini['z']['down']) ){ $vigo['z']['down'] = $ini['z']['down']; }
			else { $vigo['z']['down'] = 1000; }
		}
		else {
			$vigo['z']['up'] = 0;
			$vigo['z']['down'] = 1000;
			}
#
#	*** Fetching device state
#	[GC:G0 G54 G17 G21 G90 G94 M5 M9 T0 F0 S0]
#
	$cs->write( '$G' );
	$ret = $cs->read();
#
#	The VigoWriter requires you to first send a return in order to get
#	the banner of the plotter.
#
	$a = explode( "\n", $ret );

	foreach( $a as $k=>$v ){
		$v = trim( $v );
		$a = substr( $v, 1, strlen($v) );
		$a = substr( $a, 0, -1 );
		}
#
#--------------------------------------------------------------------------------
#	Put new things here
#--------------------------------------------------------------------------------
#

#
#	Now check against what we got originally and upgrade the $vigo array
#	IF NEED BE. Otherwise - we just send back the $vigo array.
#
	if( !is_null($ini) ){
		foreach( $ini as $k=>$v ){
			if( is_array($v) ){
				foreach( $v as $k1=>$v1 ){
					if( !isset($vigo[$k][$k1]) ){
						$vigo[$k][$k1] = $v1;
						}
					}
				}
				else if( !isset($vigo[$k]) ){
					$vigo[$k] = $ini[$k];
					}
			}
		}

	return $vigo;
}
################################################################################
#	put_ini(). Writes out the INI information.
################################################################################
function put_ini( $file=null, $ini=null )
{
	$a = json_encode( $ini );
	file_put_contents( $file, $a );
}
################################################################################
#	errorCodes(). Returns all of the error code meanings.
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
#	alarmCodes(). Returns all of the alarm code meanings.
################################################################################
function alarmCodes()
{
	$code = <<<EOD
1	Hard limit triggered. Machine position is likely lost due to sudden and immediate halt. Re-homing is highly recommended.
2	G-code motion target exceeds machine travel. Machine position safely retained. Alarm may be unlocked.
3	Reset while in motion. Grbl cannot guarantee position. Lost steps are likely. Re-homing is highly recommended.
4	Probe fail. The probe is not in the expected initial state before starting probe cycle, where G38.2 and G38.3 is not triggered and G38.4 and G38.5 is triggered.
5	Probe fail. Probe did not contact the workpiece within the programmed travel for G38.2 and G38.4.
6	Homing fail. Reset during active homing cycle.
7	Homing fail. Safety door was opened during active homing cycle.
8	Homing fail. Cycle failed to clear limit switch when pulling off. Try increasing pull-off setting or check wiring.
9	Homing fail. Could not find limit switch within search distance. Defined as 1.5 * max_travel on search and 5 * pulloff on locate phases.
10	Homing fail. On dual axis machines, could not find the second limit switch for self-squaring.
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
#	settingMeaning(). Returns all of the setting meaning meanings.
################################################################################
function settingMeaning()
{
	$code = <<<EOD
0	Step pulse time, microseconds
1	Step idle delay, milliseconds
2	Step pulse invert, mask
3	Step direction invert, mask
4	Invert step enable pin, boolean
5	Invert limit pins, boolean
6	Invert probe pin, boolean
10	Status report options, mask
11	Junction deviation, millimeters
12	Arc tolerance, millimeters
13	Report in inches, boolean
20	Soft limits enable, boolean
21	Hard limits enable, boolean
22	Homing cycle enable, boolean
23	Homing direction invert, mask
24	Homing locate feed rate, mm/min
25	Homing search seek rate, mm/min
26	Homing switch debounce delay, milliseconds
27	Homing switch pull-off distance, millimeters
30	Maximum spindle speed, RPM
31	Minimum spindle speed, RPM
32	Laser-mode enable, boolean
100	X-axis steps per millimeter
101	Y-axis steps per millimeter
102	Z-axis steps per millimeter
110	X-axis maximum rate, mm/min
111	Y-axis maximum rate, mm/min
112	Z-axis maximum rate, mm/min
120	X-axis acceleration, mm/sec^2
121	Y-axis acceleration, mm/sec^2
122	Z-axis acceleration, mm/sec^2
130	X-axis maximum travel, millimeters
131	Y-axis maximum travel, millimeters
132	Z-axis maximum travel, millimeters
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
#	settingDescription(). Returns the setting description's meaning.
################################################################################
function settingDescription()
{
	$code = <<<EOD
V	Variable spindle enabled
N	Line numbers enabled
M	Mist coolant enabled
C	CoreXY enabled
P	Parking motion enabled
Z	Homing force origin enabled
H	Homing single axis enabled
T	Two limit switches on axis enabled
A	Allow feed rate overrides in probe cycles
*	Restore all EEPROM disabled
$	Restore EEPROM $ settings disabled
#	Restore EEPROM parameter data disabled
I	Build info write user string disabled
E	Force sync upon EEPROM write disabled
W	Force sync upon work coordinate offset change disabled
L	Homing init lock sets Grbl into an alarm state upon power up
2	Dual axis motors with self-squaring enabled
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

function d()
{
}
?>
