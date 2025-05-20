<?php
#
#	This program (exCom.php) is an example of how to use the serial class.
#	The device is the VigoTec VigoWriter. So you will have to change this
#	to fit your device.
#
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

	include_once( "$lib/class_pr.php" );
	include_once( "$lib/class_serial.php" );

	$pr = $GLOBALS['classes']['pr'];
	$cs = $GLOBALS['classes']['serial'];

	$dq = '"';
	$cwd = getcwd();
	$cwd = str_replace( "\\", "/", $cwd );

	$vigo = [];

	$dir_images = '$cwd/In';
	$dir_images = str_replace( "\\", "/", $dir_images );

	$stderr = "$cwd/stderr.txt";

	$file = "vma-4-black-lotus-150dpi-halftone-line.bmp";
#
#	Section for the COM part
#
	$cs->cwd( $cwd );
	$cs->bas( "serial.bas" );
	$cs->exe( "serial32.exe" );
	$modes = $cs->modes();

$pr->pr( $modes, "Modes =" );

	if( isset($modes['com3']) ){ $cs->set( $modes['com3'] ); }
		else if( isset($modes['com4']) ){ $cs->set( $modes['com4'] ); }
		else { die( "Unknown mode\n" ); }
#
#	This is how you create the Communication software.
#	I am calling it "serial.bas". Once you have created the program
#	you will have to compile it. I use:
#
#	fbc32 -b serial.bas  -v -g -s console -x serial32.exe
#
#		Or
#
#	Use the QB64 compiler to compile the QuickBasic program. Of course,
#	the QuickBasic program was written to be compiled by QB64 and not
#	the QuickBasic compiler itself.
#
#	To make the program but you can use the graphics option instead of
#	the console part. That is - you just change "console" to "graphics"
#	and it will then use just a graphics window. However, you really do
#	need the console so I'd leave it alone.
#
	if( false ){
#
#	Make the FreeBasic Com program
#
		$cs->makeFBCom();
		exit;
		}
		else if( false ){
#
#	Make the QuickBasic Com program
#
			$cs->makeQBCom();
			exit;
			}
#
#	Open the VigoWriter
#
	$a = "n";
	$ret = $cs->open();
#
#	The VigoWriter requires you to first send a return in order to get
#	the banner of the plotter.
#
	$a = explode( "\n", $ret );
	$pr->pr( $a, "A = " );
	foreach( $a as $k=>$v ){
		if( strlen(trim($v)) < 2 ){ unset( $a[$k] ); }
		if( preg_match("/ok/i", $v) ){ unset( $a[$k] ); }
		}

	$vigo['title'] = $a[1];
#
#	Then we send a dollar sign ($) which gives us a status of the plotter
#
	$cs->write( "$" );
	$ret = $cs->read();
	$a = explode( "\n", $ret );
	$pr->pr( $a, "A = " );

	foreach( $a as $k=>$v ){
		if( strlen(trim($v)) < 2 ){ unset( $a[$k] ); }
		if( preg_match("/ok/i", $v) ){ unset( $a[$k] ); }
		}

	$vigo['$'] = [];
	$a = explode( " ", $a[0] );
	foreach( $a as $k=>$v ){
		$vigo['$'][$k] = $v;
		}
#
#	Then we send two dollar signs ($$) to the plotter which gives us even more
#	information about the plotter.
#
	$cs->write( "$$" );
	$ret = $cs->read();
	$a = explode( "\n", $ret );
	$pr->pr( $a, "A = " );

	foreach( $a as $k=>$v ){
		if( strlen(trim($v)) < 2 ){ unset( $a[$k] ); }
		if( preg_match("/ok/i", $v) ){ unset( $a[$k] ); }
		}

	$vigo['$$'] = [];
	foreach( $a as $k=>$v ){
		$vigo['$$'][$k] = $v;
		}
#
#	Now we have to set things up
#
	$ret = $cs->write( "G21 (programming in millimeters, mm)" );
	$ret = $cs->write( "G90 (programming in absolute positioning)" );
	$ret = $cs->write( "G28 (auto homing)" );
	$ret = $cs->write( "G1 F8000 (set speed)" );
	$ret = $cs->write( "G0 Z1" );
#
#	Now make sure the mouse or Z-axis will go down.
#
	$a = "";
	while( $a <> "y" ){
		echo "Verifying the Z-axis.\n";
		echo "Z-axis down all the way.\n";
		$cs->write( "m3 s1" );
		sleep( 1 );
		$cs->write( "m3 s1000" );
		echo "Did it go down? ";
		$a = strtolower( rtrim(stream_get_line(STDIN, 1024, PHP_EOL)) );
		}

	$vigo['z'] = [];
	$vigo['z']['down'] = "m3 s1000";
#
#	Now make sure the Z-axis will go up.
#
	$a = "";
	while( $a <> "y" ){
		echo "Verifying the Z-axis.\n";
		echo "Z-axis up all the way.\n";
		$cs->write( "m3 s1" );
		sleep( 1 );
		$cs->write( "m3 s1" );
		echo "Did it go up? ";
		$a = strtolower( rtrim(stream_get_line(STDIN, 1024, PHP_EOL)) );
		}

	$vigo['z']['up'] = "m3 s1";
#
#	Home the pen plotter
#
	$cs->write( "g28" );
	echo "Press return to continue - be sure to let the plotter move to the HOME position: ";
	$a = strtolower( rtrim(stream_get_line(STDIN, 1024, PHP_EOL)) );
#
#	Now start finding the outline of the drawing area.
#	G1 X###.# Y###.# is how you move the pen plotter's head.
#	We will also put the Z axis as far down as it will go.
#
	$a = "";
	$vigo['x'] = [];
	$vigo['y'] = [];

	$t = $b = $l = $r = 0;
	$x1 = $x2 = $y1 = $y2 = 100.0;
	$maxx = $maxy = -99999.0;
	$minx = $miny = 99999.0;
#
#	Put the pen up
#
	$cs->write( "m3 s1" );
	while( $a <> "y" ){
		echo "Looking for the upper-right corner.\n";
		$s = sprintf( "G1 X%.2f Y%.2f", $x1, $y1 );
		$cs->write( $s );
		echo "Type 't|r' if it has reached the upper-right corner: ";
		$a = strtolower( rtrim(stream_get_line(STDIN, 1024, PHP_EOL)) );
		if( preg_match("/t/i", $a) ){ $maxy = $y1; }
			else { $y1 += 100.0; }

		if( preg_match("/r/i", $a) ){ $maxx = $x1; }
			else { $x1 += 100.0; }

		echo "Looking for the bottom-left corner.\n";
		$s = sprintf( "G1 X%.2f Y%.2f", $x2, $y2 );
		$cs->write( $s );
		echo "Type 'b|l' if it has reached the upper-right corner: ";
		$a = strtolower( rtrim(stream_get_line(STDIN, 1024, PHP_EOL)) );
		if( preg_match("/b/i", $a) ){ $maxy = $y2; }
			else { $y2 -= 100.0; }

		if( preg_match("/l/i", $a) ){ $maxx = $x2; }
			else { $x2 -= 100.0; }
		}

	$vigo['x']['max'] = $maxx;
	$vigo['x']['min'] = $maxx;
	$vigo['y']['max'] = $maxx;
	$vigo['y']['min'] = $maxx;

	$fp = fopen( "./vigo.dat", "w" );
	foreach( $vigo as $k=>$v ){
		if( is_array($v) ){
			foreach( $v as $k1=>$v1 ){
				fprintf( $fp, "%s.%s = %s\n", $k, $k1, $v1 );
				}
			}
			else {
				fprintf( $fp, "%s = %s\n", $k, $v );
				}
		}

	fclose( $fp );
	echo "Finished!\n";
	exit;

