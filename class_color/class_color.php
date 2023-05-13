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
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( __FILE__ . ": Can not load CLASS_DEBUG" );
			}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_color();
#
#-Description:
#
#	A class to handle all of my color needs.
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
#	Mark Manning			Simulacron I			Sat 11/21/2009 16:15:20.35 
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
#		CLASS_COLOR.PHP. A class to handle working with colors.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_color
{
	private $debug = null;
	private	$color_table = array();

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	__construct();
#
#-Description:
#
#	The constructor for this class.
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
#	Mark Manning			Simulacron I			Sat 11/21/2009 16:15:48.81 
#		Original Program.
#
#
#END DOC
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['color']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['color']; }
}
################################################################################
#	init(). A way to reinitialize this class.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->color_table = array(
		'0' => array( 'name' => 'alice blue', 'red' => '240', 'green' => '248', 'blue' => '255', 'hex' => 'F0F8FF' ),
		'1' => array( 'name' => 'aliceblue', 'red' => '240', 'green' => '248', 'blue' => '255', 'hex' => 'F0F8FF' ),
		'2' => array( 'name' => 'antique white', 'red' => '250', 'green' => '235', 'blue' => '215', 'hex' => 'FAEBD7' ),
		'3' => array( 'name' => 'antique white1', 'red' => '255', 'green' => '239', 'blue' => '219', 'hex' => 'FFEFDB' ),
		'4' => array( 'name' => 'antique white2', 'red' => '238', 'green' => '223', 'blue' => '204', 'hex' => 'EEDFCC' ),
		'5' => array( 'name' => 'antique white3', 'red' => '205', 'green' => '192', 'blue' => '176', 'hex' => 'CDC0B0' ),
		'6' => array( 'name' => 'antique white4', 'red' => '139', 'green' => '131', 'blue' => '120', 'hex' => '8B8378' ),
		'7' => array( 'name' => 'antiquewhite', 'red' => '250', 'green' => '235', 'blue' => '215', 'hex' => 'FAEBD7' ),
		'8' => array( 'name' => 'antiquewhite1', 'red' => '255', 'green' => '239', 'blue' => '219', 'hex' => 'FFEFDB' ),
		'9' => array( 'name' => 'antiquewhite2', 'red' => '238', 'green' => '223', 'blue' => '204', 'hex' => 'EEDFCC' ),
		'10' => array( 'name' => 'antiquewhite3', 'red' => '205', 'green' => '192', 'blue' => '176', 'hex' => 'CDC0B0' ),
		'11' => array( 'name' => 'antiquewhite4', 'red' => '139', 'green' => '131', 'blue' => '120', 'hex' => '8B8378' ),
		'12' => array( 'name' => 'aqua', 'red' => '0', 'green' => '255', 'blue' => '255', 'hex' => '00FFFF' ),
		'13' => array( 'name' => 'aquamarine', 'red' => '127', 'green' => '255', 'blue' => '212', 'hex' => '7FFFD4' ),
		'14' => array( 'name' => 'aquamarine1', 'red' => '127', 'green' => '255', 'blue' => '212', 'hex' => '7FFFD4' ),
		'15' => array( 'name' => 'aquamarine1 (aquamarine)', 'red' => '127', 'green' => '255', 'blue' => '212', 'hex' => '7FFFD4' ),
		'16' => array( 'name' => 'aquamarine2', 'red' => '118', 'green' => '238', 'blue' => '198', 'hex' => '76EEC6' ),
		'17' => array( 'name' => 'aquamarine3', 'red' => '102', 'green' => '205', 'blue' => '170', 'hex' => '66CDAA' ),
		'18' => array( 'name' => 'aquamarine3 (mediumaquamarine)', 'red' => '102', 'green' => '205', 'blue' => '170', 'hex' => '66CDAA' ),
		'19' => array( 'name' => 'aquamarine3, mediumaquamarine', 'red' => '102', 'green' => '205', 'blue' => '170', 'hex' => '66CDAA' ),
		'20' => array( 'name' => 'aquamarine4', 'red' => '69', 'green' => '139', 'blue' => '116', 'hex' => '458B74' ),
		'21' => array( 'name' => 'azure', 'red' => '240', 'green' => '255', 'blue' => '255', 'hex' => 'F0FFFF' ),
		'22' => array( 'name' => 'azure1', 'red' => '240', 'green' => '255', 'blue' => '255', 'hex' => 'F0FFFF' ),
		'23' => array( 'name' => 'azure1 (azure)', 'red' => '240', 'green' => '255', 'blue' => '255', 'hex' => 'F0FFFF' ),
		'24' => array( 'name' => 'azure2', 'red' => '224', 'green' => '238', 'blue' => '238', 'hex' => 'E0EEEE' ),
		'25' => array( 'name' => 'azure3', 'red' => '193', 'green' => '205', 'blue' => '205', 'hex' => 'C1CDCD' ),
		'26' => array( 'name' => 'azure4', 'red' => '131', 'green' => '139', 'blue' => '139', 'hex' => '838B8B' ),
		'27' => array( 'name' => "baker's chocolate", 'red' => '92', 'green' => '51', 'blue' => '23', 'hex' => '5C3317' ),
		'28' => array( 'name' => 'banana', 'red' => '227', 'green' => '207', 'blue' => '87', 'hex' => 'E3CF57' ),
		'29' => array( 'name' => 'beige', 'red' => '245', 'green' => '245', 'blue' => '220', 'hex' => 'F5F5DC' ),
		'30' => array( 'name' => 'bisque', 'red' => '255', 'green' => '228', 'blue' => '196', 'hex' => 'FFE4C4' ),
		'31' => array( 'name' => 'bisque1', 'red' => '255', 'green' => '228', 'blue' => '196', 'hex' => 'FFE4C4' ),
		'32' => array( 'name' => 'bisque1 (bisque)', 'red' => '255', 'green' => '228', 'blue' => '196', 'hex' => 'FFE4C4' ),
		'33' => array( 'name' => 'bisque2', 'red' => '238', 'green' => '213', 'blue' => '183', 'hex' => 'EED5B7' ),
		'34' => array( 'name' => 'bisque3', 'red' => '205', 'green' => '183', 'blue' => '158', 'hex' => 'CDB79E' ),
		'35' => array( 'name' => 'bisque4', 'red' => '139', 'green' => '125', 'blue' => '107', 'hex' => '8B7D6B' ),
		'36' => array( 'name' => 'black', 'red' => '0', 'green' => '0', 'blue' => '0', 'hex' => '000000' ),
		'37' => array( 'name' => 'black*', 'red' => '0', 'green' => '0', 'blue' => '0', 'hex' => '000000' ),
		'38' => array( 'name' => 'blanched almond', 'red' => '255', 'green' => '235', 'blue' => '205', 'hex' => 'FFEBCD' ),
		'39' => array( 'name' => 'blanchedalmond', 'red' => '255', 'green' => '235', 'blue' => '205', 'hex' => 'FFEBCD' ),
		'40' => array( 'name' => 'blue', 'red' => '0', 'green' => '0', 'blue' => '255', 'hex' => '0000FF' ),
		'41' => array( 'name' => 'blue violet', 'red' => '138', 'green' => '43', 'blue' => '226', 'hex' => '8A2BE2' ),
		'42' => array( 'name' => 'blue*', 'red' => '0', 'green' => '0', 'blue' => '255', 'hex' => '0000FF' ),
		'43' => array( 'name' => 'blue1', 'red' => '0', 'green' => '0', 'blue' => '255', 'hex' => '0000FF' ),
		'44' => array( 'name' => 'blue2', 'red' => '0', 'green' => '0', 'blue' => '238', 'hex' => '0000EE' ),
		'45' => array( 'name' => 'blue3', 'red' => '0', 'green' => '0', 'blue' => '205', 'hex' => '0000CD' ),
		'46' => array( 'name' => 'blue3 (mediumblue)', 'red' => '0', 'green' => '0', 'blue' => '205', 'hex' => '0000CD' ),
		'47' => array( 'name' => 'blue4', 'red' => '0', 'green' => '0', 'blue' => '139', 'hex' => '00008B' ),
		'48' => array( 'name' => 'blue4 (darkblue)', 'red' => '0', 'green' => '0', 'blue' => '139', 'hex' => '00008B' ),
		'49' => array( 'name' => 'blueviolet', 'red' => '138', 'green' => '43', 'blue' => '226', 'hex' => '8A2BE2' ),
		'50' => array( 'name' => 'brass', 'red' => '181', 'green' => '166', 'blue' => '66', 'hex' => 'B5A642' ),
		'51' => array( 'name' => 'brick', 'red' => '156', 'green' => '102', 'blue' => '31', 'hex' => '9C661F' ),
		'52' => array( 'name' => 'bright gold', 'red' => '217', 'green' => '217', 'blue' => '25', 'hex' => 'D9D919' ),
		'53' => array( 'name' => 'bronze', 'red' => '140', 'green' => '120', 'blue' => '83', 'hex' => '8C7853' ),
		'54' => array( 'name' => 'bronze ii', 'red' => '166', 'green' => '125', 'blue' => '61', 'hex' => 'A67D3D' ),
		'55' => array( 'name' => 'brown', 'red' => '166', 'green' => '42', 'blue' => '42', 'hex' => 'A62A2A' ),
		'56' => array( 'name' => 'brown1', 'red' => '255', 'green' => '64', 'blue' => '64', 'hex' => 'FF4040' ),
		'57' => array( 'name' => 'brown2', 'red' => '238', 'green' => '59', 'blue' => '59', 'hex' => 'EE3B3B' ),
		'58' => array( 'name' => 'brown3', 'red' => '205', 'green' => '51', 'blue' => '51', 'hex' => 'CD3333' ),
		'59' => array( 'name' => 'brown4', 'red' => '139', 'green' => '35', 'blue' => '35', 'hex' => '8B2323' ),
		'60' => array( 'name' => 'burlywood', 'red' => '222', 'green' => '184', 'blue' => '135', 'hex' => 'DEB887' ),
		'61' => array( 'name' => 'burlywood1', 'red' => '255', 'green' => '211', 'blue' => '155', 'hex' => 'FFD39B' ),
		'62' => array( 'name' => 'burlywood2', 'red' => '238', 'green' => '197', 'blue' => '145', 'hex' => 'EEC591' ),
		'63' => array( 'name' => 'burlywood3', 'red' => '205', 'green' => '170', 'blue' => '125', 'hex' => 'CDAA7D' ),
		'64' => array( 'name' => 'burlywood4', 'red' => '139', 'green' => '115', 'blue' => '85', 'hex' => '8B7355' ),
		'65' => array( 'name' => 'burntsienna', 'red' => '138', 'green' => '54', 'blue' => '15', 'hex' => '8A360F' ),
		'66' => array( 'name' => 'burntumber', 'red' => '138', 'green' => '51', 'blue' => '36', 'hex' => '8A3324' ),
		'67' => array( 'name' => 'cadet blue', 'red' => '95', 'green' => '159', 'blue' => '159', 'hex' => '5F9F9F' ),
		'68' => array( 'name' => 'cadet blue1', 'red' => '152', 'green' => '245', 'blue' => '255', 'hex' => '98F5FF' ),
		'69' => array( 'name' => 'cadet blue2', 'red' => '142', 'green' => '229', 'blue' => '238', 'hex' => '8EE5EE' ),
		'70' => array( 'name' => 'cadet blue3', 'red' => '122', 'green' => '197', 'blue' => '205', 'hex' => '7AC5CD' ),
		'71' => array( 'name' => 'cadet blue4', 'red' => '83', 'green' => '134', 'blue' => '139', 'hex' => '53868B' ),
		'72' => array( 'name' => 'cadetblue', 'red' => '95', 'green' => '158', 'blue' => '160', 'hex' => '5F9EA0' ),
		'73' => array( 'name' => 'cadetblue1', 'red' => '152', 'green' => '245', 'blue' => '255', 'hex' => '98F5FF' ),
		'74' => array( 'name' => 'cadetblue2', 'red' => '142', 'green' => '229', 'blue' => '238', 'hex' => '8EE5EE' ),
		'75' => array( 'name' => 'cadetblue3', 'red' => '122', 'green' => '197', 'blue' => '205', 'hex' => '7AC5CD' ),
		'76' => array( 'name' => 'cadetblue4', 'red' => '83', 'green' => '134', 'blue' => '139', 'hex' => '53868B' ),
		'77' => array( 'name' => 'cadmiumorange', 'red' => '255', 'green' => '97', 'blue' => '3', 'hex' => 'FF6103' ),
		'78' => array( 'name' => 'cadmiumyellow', 'red' => '255', 'green' => '153', 'blue' => '18', 'hex' => 'FF9912' ),
		'79' => array( 'name' => 'carrot', 'red' => '237', 'green' => '145', 'blue' => '33', 'hex' => 'ED9121' ),
		'80' => array( 'name' => 'chartreuse', 'red' => '127', 'green' => '255', 'blue' => '0', 'hex' => '7FFF00' ),
		'81' => array( 'name' => 'chartreuse1', 'red' => '127', 'green' => '255', 'blue' => '0', 'hex' => '7FFF00' ),
		'82' => array( 'name' => 'chartreuse1 (chartreuse)', 'red' => '127', 'green' => '255', 'blue' => '0', 'hex' => '7FFF00' ),
		'83' => array( 'name' => 'chartreuse2', 'red' => '118', 'green' => '238', 'blue' => '0', 'hex' => '76EE00' ),
		'84' => array( 'name' => 'chartreuse3', 'red' => '102', 'green' => '205', 'blue' => '0', 'hex' => '66CD00' ),
		'85' => array( 'name' => 'chartreuse4', 'red' => '69', 'green' => '139', 'blue' => '0', 'hex' => '458B00' ),
		'86' => array( 'name' => 'chocolate', 'red' => '210', 'green' => '105', 'blue' => '30', 'hex' => 'D2691E' ),
		'87' => array( 'name' => 'chocolate1', 'red' => '255', 'green' => '127', 'blue' => '36', 'hex' => 'FF7F24' ),
		'88' => array( 'name' => 'chocolate2', 'red' => '238', 'green' => '118', 'blue' => '33', 'hex' => 'EE7621' ),
		'89' => array( 'name' => 'chocolate3', 'red' => '205', 'green' => '102', 'blue' => '29', 'hex' => 'CD661D' ),
		'90' => array( 'name' => 'chocolate4', 'red' => '139', 'green' => '69', 'blue' => '19', 'hex' => '8B4513' ),
		'91' => array( 'name' => 'chocolate4 (saddlebrown)', 'red' => '139', 'green' => '69', 'blue' => '19', 'hex' => '8B4513' ),
		'92' => array( 'name' => 'cobalt', 'red' => '61', 'green' => '89', 'blue' => '171', 'hex' => '3D59AB' ),
		'93' => array( 'name' => 'cobaltgreen', 'red' => '61', 'green' => '145', 'blue' => '64', 'hex' => '3D9140' ),
		'94' => array( 'name' => 'coldgrey', 'red' => '128', 'green' => '138', 'blue' => '135', 'hex' => '808A87' ),
		'95' => array( 'name' => 'cool copper', 'red' => '217', 'green' => '135', 'blue' => '25', 'hex' => 'D98719' ),
		'96' => array( 'name' => 'copper', 'red' => '184', 'green' => '115', 'blue' => '51', 'hex' => 'B87333' ),
		'97' => array( 'name' => 'coral', 'red' => '255', 'green' => '127', 'blue' => '80', 'hex' => 'FF7F50' ),
		'98' => array( 'name' => 'coral1', 'red' => '255', 'green' => '114', 'blue' => '86', 'hex' => 'FF7256' ),
		'99' => array( 'name' => 'coral2', 'red' => '238', 'green' => '106', 'blue' => '80', 'hex' => 'EE6A50' ),
		'100' => array( 'name' => 'coral3', 'red' => '205', 'green' => '91', 'blue' => '69', 'hex' => 'CD5B45' ),
		'101' => array( 'name' => 'coral4', 'red' => '139', 'green' => '62', 'blue' => '47', 'hex' => '8B3E2F' ),
		'102' => array( 'name' => 'corn flower blue', 'red' => '66', 'green' => '66', 'blue' => '111', 'hex' => '42426F' ),
		'103' => array( 'name' => 'cornflower blue', 'red' => '100', 'green' => '149', 'blue' => '237', 'hex' => '6495ED' ),
		'104' => array( 'name' => 'cornflowerblue', 'red' => '100', 'green' => '149', 'blue' => '237', 'hex' => '6495ED' ),
		'105' => array( 'name' => 'cornsilk', 'red' => '255', 'green' => '248', 'blue' => '220', 'hex' => 'FFF8DC' ),
		'106' => array( 'name' => 'cornsilk1', 'red' => '255', 'green' => '248', 'blue' => '220', 'hex' => 'FFF8DC' ),
		'107' => array( 'name' => 'cornsilk1 (cornsilk)', 'red' => '255', 'green' => '248', 'blue' => '220', 'hex' => 'FFF8DC' ),
		'108' => array( 'name' => 'cornsilk2', 'red' => '238', 'green' => '232', 'blue' => '205', 'hex' => 'EEE8CD' ),
		'109' => array( 'name' => 'cornsilk3', 'red' => '205', 'green' => '200', 'blue' => '177', 'hex' => 'CDC8B1' ),
		'110' => array( 'name' => 'cornsilk4', 'red' => '139', 'green' => '136', 'blue' => '120', 'hex' => '8B8878' ),
		'111' => array( 'name' => 'crimson', 'red' => '220', 'green' => '20', 'blue' => '60', 'hex' => 'DC143C' ),
		'112' => array( 'name' => 'css gold', 'red' => '204', 'green' => '153', 'blue' => '0', 'hex' => 'CC9900' ),
		'113' => array( 'name' => 'cyan', 'red' => '0', 'green' => '255', 'blue' => '255', 'hex' => '00FFFF' ),
		'114' => array( 'name' => 'cyan/ aqua*', 'red' => '0', 'green' => '255', 'blue' => '255', 'hex' => '00FFFF' ),
		'115' => array( 'name' => 'cyan1', 'red' => '0', 'green' => '255', 'blue' => '255', 'hex' => '00FFFF' ),
		'116' => array( 'name' => 'cyan2', 'red' => '0', 'green' => '238', 'blue' => '238', 'hex' => '00EEEE' ),
		'117' => array( 'name' => 'cyan3', 'red' => '0', 'green' => '205', 'blue' => '205', 'hex' => '00CDCD' ),
		'118' => array( 'name' => 'cyan4', 'red' => '0', 'green' => '139', 'blue' => '139', 'hex' => '008B8B' ),
		'119' => array( 'name' => 'cyan4 (darkcyan)', 'red' => '0', 'green' => '139', 'blue' => '139', 'hex' => '008B8B' ),
		'120' => array( 'name' => 'dark blue', 'red' => '0', 'green' => '0', 'blue' => '139', 'hex' => '00008B' ),
		'121' => array( 'name' => 'dark brown', 'red' => '92', 'green' => '64', 'blue' => '51', 'hex' => '5C4033' ),
		'122' => array( 'name' => 'dark cyan', 'red' => '0', 'green' => '139', 'blue' => '139', 'hex' => '008B8B' ),
		'123' => array( 'name' => 'dark goldenrod', 'red' => '184', 'green' => '134', 'blue' => '11', 'hex' => 'B8860B' ),
		'124' => array( 'name' => 'dark goldenrod1', 'red' => '255', 'green' => '185', 'blue' => '15', 'hex' => 'FFB90F' ),
		'125' => array( 'name' => 'dark goldenrod2', 'red' => '238', 'green' => '173', 'blue' => '14', 'hex' => 'EEAD0E' ),
		'126' => array( 'name' => 'dark goldenrod3', 'red' => '205', 'green' => '149', 'blue' => '12', 'hex' => 'CD950C' ),
		'127' => array( 'name' => 'dark goldenrod4', 'red' => '139', 'green' => '101', 'blue' => '8', 'hex' => '8B658B' ),
		'128' => array( 'name' => 'dark green', 'red' => '47', 'green' => '79', 'blue' => '47', 'hex' => '2F4F2F' ),
		'129' => array( 'name' => 'dark green copper', 'red' => '74', 'green' => '118', 'blue' => '110', 'hex' => '4A766E' ),
		'130' => array( 'name' => 'dark grey', 'red' => '169', 'green' => '169', 'blue' => '169', 'hex' => 'A9A9A9' ),
		'131' => array( 'name' => 'dark khaki', 'red' => '189', 'green' => '183', 'blue' => '107', 'hex' => 'BDB76B' ),
		'132' => array( 'name' => 'dark magenta', 'red' => '139', 'green' => '0', 'blue' => '139', 'hex' => '8B008B' ),
		'133' => array( 'name' => 'dark olive green', 'red' => '85', 'green' => '107', 'blue' => '47', 'hex' => '556B2F' ),
		'134' => array( 'name' => 'dark olive green1', 'red' => '202', 'green' => '255', 'blue' => '112', 'hex' => 'CAFF70' ),
		'135' => array( 'name' => 'dark olive green2', 'red' => '188', 'green' => '238', 'blue' => '104', 'hex' => 'BCEE68' ),
		'136' => array( 'name' => 'dark olive green3', 'red' => '162', 'green' => '205', 'blue' => '90', 'hex' => 'A2CD5A' ),
		'137' => array( 'name' => 'dark olive green4', 'red' => '110', 'green' => '139', 'blue' => '61', 'hex' => '6E8B3D' ),
		'138' => array( 'name' => 'dark orange', 'red' => '255', 'green' => '140', 'blue' => '0', 'hex' => 'FF8C00' ),
		'139' => array( 'name' => 'dark orange1', 'red' => '255', 'green' => '127', 'blue' => '0', 'hex' => 'FF7F00' ),
		'140' => array( 'name' => 'dark orange2', 'red' => '238', 'green' => '118', 'blue' => '0', 'hex' => 'EE7600' ),
		'141' => array( 'name' => 'dark orange3', 'red' => '205', 'green' => '102', 'blue' => '0', 'hex' => 'CD6600' ),
		'142' => array( 'name' => 'dark orange4', 'red' => '139', 'green' => '69', 'blue' => '0', 'hex' => '8B4500' ),
		'143' => array( 'name' => 'dark orchid', 'red' => '153', 'green' => '50', 'blue' => '205', 'hex' => '9932CD' ),
		'144' => array( 'name' => 'dark orchid1', 'red' => '191', 'green' => '62', 'blue' => '255', 'hex' => 'BF3EFF' ),
		'145' => array( 'name' => 'dark orchid2', 'red' => '178', 'green' => '58', 'blue' => '238', 'hex' => 'B23AEE' ),
		'146' => array( 'name' => 'dark orchid3', 'red' => '154', 'green' => '50', 'blue' => '205', 'hex' => '9A32CD' ),
		'147' => array( 'name' => 'dark orchid4', 'red' => '104', 'green' => '34', 'blue' => '139', 'hex' => '68228B' ),
		'148' => array( 'name' => 'dark purple', 'red' => '135', 'green' => '31', 'blue' => '120', 'hex' => '871F78' ),
		'149' => array( 'name' => 'dark red', 'red' => '139', 'green' => '0', 'blue' => '0', 'hex' => '8B0000' ),
		'150' => array( 'name' => 'dark salmon', 'red' => '233', 'green' => '150', 'blue' => '122', 'hex' => 'E9967A' ),
		'151' => array( 'name' => 'dark sea green', 'red' => '143', 'green' => '188', 'blue' => '143', 'hex' => '8FBC8F' ),
		'152' => array( 'name' => 'dark sea green1', 'red' => '193', 'green' => '255', 'blue' => '193', 'hex' => 'C1FFC1' ),
		'153' => array( 'name' => 'dark sea green2', 'red' => '180', 'green' => '238', 'blue' => '180', 'hex' => 'B4EEB4' ),
		'154' => array( 'name' => 'dark sea green3', 'red' => '155', 'green' => '205', 'blue' => '155', 'hex' => '9BCD9B' ),
		'155' => array( 'name' => 'dark slate blue', 'red' => '36', 'green' => '24', 'blue' => '130', 'hex' => '241882' ),
		'156' => array( 'name' => 'dark slate gray', 'red' => '47', 'green' => '79', 'blue' => '79', 'hex' => '2F4F4F' ),
		'157' => array( 'name' => 'dark slate gray1', 'red' => '151', 'green' => '255', 'blue' => '255', 'hex' => '97FFFF' ),
		'158' => array( 'name' => 'dark slate gray2', 'red' => '141', 'green' => '238', 'blue' => '238', 'hex' => '8DEEEE' ),
		'159' => array( 'name' => 'dark slate gray3', 'red' => '121', 'green' => '205', 'blue' => '205', 'hex' => '79CDCD' ),
		'160' => array( 'name' => 'dark slate gray4', 'red' => '82', 'green' => '139', 'blue' => '139', 'hex' => '528B8B' ),
		'161' => array( 'name' => 'dark slate grey', 'red' => '47', 'green' => '79', 'blue' => '79', 'hex' => '2F4F4F' ),
		'162' => array( 'name' => 'dark tan', 'red' => '151', 'green' => '105', 'blue' => '79', 'hex' => '97694F' ),
		'163' => array( 'name' => 'dark turquoise', 'red' => '0', 'green' => '206', 'blue' => '209', 'hex' => '00CED1' ),
		'164' => array( 'name' => 'dark violet', 'red' => '148', 'green' => '0', 'blue' => '211', 'hex' => '9400D3' ),
		'165' => array( 'name' => 'dark wood', 'red' => '133', 'green' => '94', 'blue' => '66', 'hex' => '855E42' ),
		'166' => array( 'name' => 'darkgoldenrod', 'red' => '184', 'green' => '134', 'blue' => '11', 'hex' => 'B8860B' ),
		'167' => array( 'name' => 'darkgoldenrod1', 'red' => '255', 'green' => '185', 'blue' => '15', 'hex' => 'FFB90F' ),
		'168' => array( 'name' => 'darkgoldenrod2', 'red' => '238', 'green' => '173', 'blue' => '14', 'hex' => 'EEAD0E' ),
		'169' => array( 'name' => 'darkgoldenrod3', 'red' => '205', 'green' => '149', 'blue' => '12', 'hex' => 'CD950C' ),
		'170' => array( 'name' => 'darkgoldenrod4', 'red' => '139', 'green' => '101', 'blue' => '8', 'hex' => '8B6508' ),
		'171' => array( 'name' => 'darkgray', 'red' => '169', 'green' => '169', 'blue' => '169', 'hex' => 'A9A9A9' ),
		'172' => array( 'name' => 'darkgreen', 'red' => '0', 'green' => '100', 'blue' => '0', 'hex' => '006400' ),
		'173' => array( 'name' => 'darkkhaki', 'red' => '189', 'green' => '183', 'blue' => '107', 'hex' => 'BDB76B' ),
		'174' => array( 'name' => 'darkolivegreen', 'red' => '85', 'green' => '107', 'blue' => '47', 'hex' => '556B2F' ),
		'175' => array( 'name' => 'darkolivegreen1', 'red' => '202', 'green' => '255', 'blue' => '112', 'hex' => 'CAFF70' ),
		'176' => array( 'name' => 'darkolivegreen2', 'red' => '188', 'green' => '238', 'blue' => '104', 'hex' => 'BCEE68' ),
		'177' => array( 'name' => 'darkolivegreen3', 'red' => '162', 'green' => '205', 'blue' => '90', 'hex' => 'A2CD5A' ),
		'178' => array( 'name' => 'darkolivegreen4', 'red' => '110', 'green' => '139', 'blue' => '61', 'hex' => '6E8B3D' ),
		'179' => array( 'name' => 'darkorange', 'red' => '255', 'green' => '140', 'blue' => '0', 'hex' => 'FF8C00' ),
		'180' => array( 'name' => 'darkorange1', 'red' => '255', 'green' => '127', 'blue' => '0', 'hex' => 'FF7F00' ),
		'181' => array( 'name' => 'darkorange2', 'red' => '238', 'green' => '118', 'blue' => '0', 'hex' => 'EE7600' ),
		'182' => array( 'name' => 'darkorange3', 'red' => '205', 'green' => '102', 'blue' => '0', 'hex' => 'CD6600' ),
		'183' => array( 'name' => 'darkorange4', 'red' => '139', 'green' => '69', 'blue' => '0', 'hex' => '8B4500' ),
		'184' => array( 'name' => 'darkorchid', 'red' => '153', 'green' => '50', 'blue' => '204', 'hex' => '9932CC' ),
		'185' => array( 'name' => 'darkorchid1', 'red' => '191', 'green' => '62', 'blue' => '255', 'hex' => 'BF3EFF' ),
		'186' => array( 'name' => 'darkorchid2', 'red' => '178', 'green' => '58', 'blue' => '238', 'hex' => 'B23AEE' ),
		'187' => array( 'name' => 'darkorchid3', 'red' => '154', 'green' => '50', 'blue' => '205', 'hex' => '9A32CD' ),
		'188' => array( 'name' => 'darkorchid4', 'red' => '104', 'green' => '34', 'blue' => '139', 'hex' => '68228B' ),
		'189' => array( 'name' => 'darksalmon', 'red' => '233', 'green' => '150', 'blue' => '122', 'hex' => 'E9967A' ),
		'190' => array( 'name' => 'darkseagreen', 'red' => '143', 'green' => '188', 'blue' => '143', 'hex' => '8FBC8F' ),
		'191' => array( 'name' => 'darkseagreen1', 'red' => '193', 'green' => '255', 'blue' => '193', 'hex' => 'C1FFC1' ),
		'192' => array( 'name' => 'darkseagreen2', 'red' => '180', 'green' => '238', 'blue' => '180', 'hex' => 'B4EEB4' ),
		'193' => array( 'name' => 'darkseagreen3', 'red' => '155', 'green' => '205', 'blue' => '155', 'hex' => '9BCD9B' ),
		'194' => array( 'name' => 'darkseagreen4', 'red' => '105', 'green' => '139', 'blue' => '105', 'hex' => '698B69' ),
		'195' => array( 'name' => 'darkslateblue', 'red' => '72', 'green' => '61', 'blue' => '139', 'hex' => '483D8B' ),
		'196' => array( 'name' => 'darkslategray', 'red' => '47', 'green' => '79', 'blue' => '79', 'hex' => '2F4F4F' ),
		'197' => array( 'name' => 'darkslategray1', 'red' => '151', 'green' => '255', 'blue' => '255', 'hex' => '97FFFF' ),
		'198' => array( 'name' => 'darkslategray2', 'red' => '141', 'green' => '238', 'blue' => '238', 'hex' => '8DEEEE' ),
		'199' => array( 'name' => 'darkslategray3', 'red' => '121', 'green' => '205', 'blue' => '205', 'hex' => '79CDCD' ),
		'200' => array( 'name' => 'darkslategray4', 'red' => '82', 'green' => '139', 'blue' => '139', 'hex' => '528B8B' ),
		'201' => array( 'name' => 'darkturquoise', 'red' => '0', 'green' => '206', 'blue' => '209', 'hex' => '00CED1' ),
		'202' => array( 'name' => 'darkviolet', 'red' => '148', 'green' => '0', 'blue' => '211', 'hex' => '9400D3' ),
		'203' => array( 'name' => 'deep pink', 'red' => '255', 'green' => '20', 'blue' => '147', 'hex' => 'FF1493' ),
		'204' => array( 'name' => 'deep pink1', 'red' => '255', 'green' => '20', 'blue' => '147', 'hex' => 'FF1493' ),
		'205' => array( 'name' => 'deep pink2', 'red' => '238', 'green' => '18', 'blue' => '137', 'hex' => 'EE1289' ),
		'206' => array( 'name' => 'deep pink3', 'red' => '205', 'green' => '16', 'blue' => '118', 'hex' => 'CD1076' ),
		'207' => array( 'name' => 'deep pink4', 'red' => '139', 'green' => '10', 'blue' => '80', 'hex' => '8B0A50' ),
		'208' => array( 'name' => 'deep sky blue', 'red' => '0', 'green' => '191', 'blue' => '255', 'hex' => '00BFFF' ),
		'209' => array( 'name' => 'deep sky blue1', 'red' => '0', 'green' => '191', 'blue' => '255', 'hex' => '00BFFF' ),
		'210' => array( 'name' => 'deep sky blue2', 'red' => '0', 'green' => '178', 'blue' => '238', 'hex' => '00B2EE' ),
		'211' => array( 'name' => 'deep sky blue3', 'red' => '0', 'green' => '154', 'blue' => '205', 'hex' => '009ACD' ),
		'212' => array( 'name' => 'deep skyblue', 'red' => '4', 'green' => '0', 'blue' => '104', 'hex' => '139' ),
		'213' => array( 'name' => 'deeppink', 'red' => '255', 'green' => '20', 'blue' => '147', 'hex' => 'FF1493' ),
		'214' => array( 'name' => 'deeppink1', 'red' => '255', 'green' => '20', 'blue' => '147', 'hex' => 'FF1493' ),
		'215' => array( 'name' => 'deeppink1 (deeppink)', 'red' => '255', 'green' => '20', 'blue' => '147', 'hex' => 'FF1493' ),
		'216' => array( 'name' => 'deeppink2', 'red' => '238', 'green' => '18', 'blue' => '137', 'hex' => 'EE1289' ),
		'217' => array( 'name' => 'deeppink3', 'red' => '205', 'green' => '16', 'blue' => '118', 'hex' => 'CD1076' ),
		'218' => array( 'name' => 'deeppink4', 'red' => '139', 'green' => '10', 'blue' => '80', 'hex' => '8B0A50' ),
		'219' => array( 'name' => 'deepskyblue', 'red' => '0', 'green' => '191', 'blue' => '255', 'hex' => '00BFFF' ),
		'220' => array( 'name' => 'deepskyblue1', 'red' => '0', 'green' => '191', 'blue' => '255', 'hex' => '00BFFF' ),
		'221' => array( 'name' => 'deepskyblue1 (deepskyblue)', 'red' => '0', 'green' => '191', 'blue' => '255', 'hex' => '00BFFF' ),
		'222' => array( 'name' => 'deepskyblue2', 'red' => '0', 'green' => '178', 'blue' => '238', 'hex' => '00B2EE' ),
		'223' => array( 'name' => 'deepskyblue3', 'red' => '0', 'green' => '154', 'blue' => '205', 'hex' => '009ACD' ),
		'224' => array( 'name' => 'deepskyblue4', 'red' => '0', 'green' => '104', 'blue' => '139', 'hex' => '00688B' ),
		'225' => array( 'name' => 'dim grey', 'red' => '84', 'green' => '84', 'blue' => '84', 'hex' => '545454' ),
		'226' => array( 'name' => 'dimgray(gray 42)', 'red' => '105', 'green' => '105', 'blue' => '105', 'hex' => '696969' ),
		'227' => array( 'name' => 'dodger blue', 'red' => '30', 'green' => '144', 'blue' => '255', 'hex' => '1E90FF' ),
		'228' => array( 'name' => 'dodger blue1', 'red' => '30', 'green' => '144', 'blue' => '255', 'hex' => '1E90FF' ),
		'229' => array( 'name' => 'dodger blue2', 'red' => '28', 'green' => '134', 'blue' => '238', 'hex' => '1C86EE' ),
		'230' => array( 'name' => 'dodger blue3', 'red' => '24', 'green' => '116', 'blue' => '205', 'hex' => '1874CD' ),
		'231' => array( 'name' => 'dodger blue4', 'red' => '16', 'green' => '78', 'blue' => '139', 'hex' => '104E8B' ),
		'232' => array( 'name' => 'dodgerblue', 'red' => '30', 'green' => '144', 'blue' => '255', 'hex' => '1E90FF' ),
		'233' => array( 'name' => 'dodgerblue1', 'red' => '30', 'green' => '144', 'blue' => '255', 'hex' => '1E90FF' ),
		'234' => array( 'name' => 'dodgerblue1 (dodgerblue)', 'red' => '30', 'green' => '144', 'blue' => '255', 'hex' => '1E90FF' ),
		'235' => array( 'name' => 'dodgerblue2', 'red' => '28', 'green' => '134', 'blue' => '238', 'hex' => '1C86EE' ),
		'236' => array( 'name' => 'dodgerblue3', 'red' => '24', 'green' => '116', 'blue' => '205', 'hex' => '1874CD' ),
		'237' => array( 'name' => 'dodgerblue4', 'red' => '16', 'green' => '78', 'blue' => '139', 'hex' => '104E8B' ),
		'238' => array( 'name' => 'dodgerblue5', 'red' => '170', 'green' => '187', 'blue' => '204', 'hex' => 'AABBCC' ),
		'239' => array( 'name' => 'dusty rose', 'red' => '133', 'green' => '99', 'blue' => '99', 'hex' => '856363' ),
		'240' => array( 'name' => 'eggshell', 'red' => '252', 'green' => '230', 'blue' => '201', 'hex' => 'FCE6C9' ),
		'241' => array( 'name' => 'emeraldgreen', 'red' => '0', 'green' => '201', 'blue' => '87', 'hex' => '00C957' ),
		'242' => array( 'name' => 'feldspar', 'red' => '209', 'green' => '146', 'blue' => '117', 'hex' => 'D19275' ),
		'243' => array( 'name' => 'feldspara', 'red' => '204', 'green' => '51', 'blue' => '51', 'hex' => 'CC3333' ),
		'244' => array( 'name' => 'firebrick', 'red' => '142', 'green' => '35', 'blue' => '35', 'hex' => '8E2323' ),
		'245' => array( 'name' => 'firebrick1', 'red' => '255', 'green' => '48', 'blue' => '48', 'hex' => 'FF3030' ),
		'246' => array( 'name' => 'firebrick2', 'red' => '238', 'green' => '44', 'blue' => '44', 'hex' => 'EE2C2C' ),
		'247' => array( 'name' => 'firebrick3', 'red' => '205', 'green' => '38', 'blue' => '38', 'hex' => 'CD2626' ),
		'248' => array( 'name' => 'firebrick4', 'red' => '139', 'green' => '26', 'blue' => '26', 'hex' => '8B1A1A' ),
		'249' => array( 'name' => 'flesh', 'red' => '255', 'green' => '125', 'blue' => '64', 'hex' => 'FF7D40' ),
		'250' => array( 'name' => 'floral white', 'red' => '255', 'green' => '250', 'blue' => '240', 'hex' => 'FFFAF0' ),
		'251' => array( 'name' => 'floralwhite', 'red' => '255', 'green' => '250', 'blue' => '240', 'hex' => 'FFFAF0' ),
		'252' => array( 'name' => 'forest green', 'red' => '34', 'green' => '139', 'blue' => '34', 'hex' => '228B22' ),
		'253' => array( 'name' => 'forest green, khaki, medium aquamarine',
						'red' => '35', 'green' => '142', 'blue' => '35', 'hex' => '238E23' ),
		'254' => array( 'name' => 'forestgreen', 'red' => '34', 'green' => '139', 'blue' => '34', 'hex' => '228B22' ),
		'255' => array( 'name' => 'free speech aquamarine', 'red' => '2', 'green' => '157', 'blue' => '116', 'hex' => '029D74' ),
		'256' => array( 'name' => 'free speech blue', 'red' => '65', 'green' => '86', 'blue' => '197', 'hex' => '4156C5' ),
		'257' => array( 'name' => 'free speech green', 'red' => '9', 'green' => '249', 'blue' => '17', 'hex' => '09F911' ),
		'258' => array( 'name' => 'free speech grey', 'red' => '99', 'green' => '86', 'blue' => '136', 'hex' => '635688' ),
		'259' => array( 'name' => 'free speech magenta', 'red' => '227', 'green' => '91', 'blue' => '216', 'hex' => 'E35BD8' ),
		'260' => array( 'name' => 'free speech red', 'red' => '192', 'green' => '0', 'blue' => '0', 'hex' => 'C00000' ),
		'261' => array( 'name' => 'fuchsia', 'red' => '255', 'green' => '0', 'blue' => '255', 'hex' => 'FF00FF' ),
		'262' => array( 'name' => 'gainsboro', 'red' => '220', 'green' => '220', 'blue' => '220', 'hex' => 'DCDCDC' ),
		'263' => array( 'name' => 'ghost white', 'red' => '248', 'green' => '248', 'blue' => '255', 'hex' => 'F8F8FF' ),
		'264' => array( 'name' => 'ghostwhite', 'red' => '248', 'green' => '248', 'blue' => '255', 'hex' => 'F8F8FF' ),
		'265' => array( 'name' => 'gold', 'red' => '255', 'green' => '215', 'blue' => '0', 'hex' => 'FFD700' ),
		'266' => array( 'name' => 'gold1', 'red' => '255', 'green' => '215', 'blue' => '0', 'hex' => 'FFD700' ),
		'267' => array( 'name' => 'gold1 (gold)', 'red' => '255', 'green' => '215', 'blue' => '0', 'hex' => 'FFD700' ),
		'268' => array( 'name' => 'gold2', 'red' => '238', 'green' => '201', 'blue' => '0', 'hex' => 'EEC900' ),
		'269' => array( 'name' => 'gold3', 'red' => '205', 'green' => '173', 'blue' => '0', 'hex' => 'CDAD00' ),
		'270' => array( 'name' => 'gold4', 'red' => '139', 'green' => '117', 'blue' => '0', 'hex' => '8B7500' ),
		'271' => array( 'name' => 'goldenrod', 'red' => '218', 'green' => '165', 'blue' => '32', 'hex' => 'DAA520' ),
		'272' => array( 'name' => 'goldenrod1', 'red' => '255', 'green' => '193', 'blue' => '37', 'hex' => 'FFC125' ),
		'273' => array( 'name' => 'goldenrod2', 'red' => '238', 'green' => '180', 'blue' => '34', 'hex' => 'EEB422' ),
		'274' => array( 'name' => 'goldenrod3', 'red' => '205', 'green' => '155', 'blue' => '29', 'hex' => 'CD9B1D' ),
		'275' => array( 'name' => 'goldenrod4', 'red' => '139', 'green' => '105', 'blue' => '20', 'hex' => '8B6914' ),
		'276' => array( 'name' => 'gray*', 'red' => '128', 'green' => '128', 'blue' => '128', 'hex' => '808080' ),
		'277' => array( 'name' => 'gray1', 'red' => '3', 'green' => '3', 'blue' => '3', 'hex' => '030303' ),
		'278' => array( 'name' => 'gray10', 'red' => '26', 'green' => '26', 'blue' => '26', 'hex' => '1A1A1A' ),
		'279' => array( 'name' => 'gray11', 'red' => '28', 'green' => '28', 'blue' => '28', 'hex' => '1C1C1C' ),
		'280' => array( 'name' => 'gray12', 'red' => '31', 'green' => '31', 'blue' => '31', 'hex' => '1F1F1F' ),
		'281' => array( 'name' => 'gray13', 'red' => '33', 'green' => '33', 'blue' => '33', 'hex' => '212121' ),
		'282' => array( 'name' => 'gray14', 'red' => '36', 'green' => '36', 'blue' => '36', 'hex' => '242424' ),
		'283' => array( 'name' => 'gray15', 'red' => '38', 'green' => '38', 'blue' => '38', 'hex' => '262626' ),
		'284' => array( 'name' => 'gray16', 'red' => '41', 'green' => '41', 'blue' => '41', 'hex' => '292929' ),
		'285' => array( 'name' => 'gray17', 'red' => '43', 'green' => '43', 'blue' => '43', 'hex' => '2B2B2B' ),
		'286' => array( 'name' => 'gray18', 'red' => '46', 'green' => '46', 'blue' => '46', 'hex' => '2E2E2E' ),
		'287' => array( 'name' => 'gray19', 'red' => '48', 'green' => '48', 'blue' => '48', 'hex' => '303030' ),
		'288' => array( 'name' => 'gray2', 'red' => '5', 'green' => '5', 'blue' => '5', 'hex' => '050505' ),
		'289' => array( 'name' => 'gray20', 'red' => '51', 'green' => '51', 'blue' => '51', 'hex' => '333333' ),
		'290' => array( 'name' => 'gray21', 'red' => '54', 'green' => '54', 'blue' => '54', 'hex' => '363636' ),
		'291' => array( 'name' => 'gray22', 'red' => '56', 'green' => '56', 'blue' => '56', 'hex' => '383838' ),
		'292' => array( 'name' => 'gray23', 'red' => '59', 'green' => '59', 'blue' => '59', 'hex' => '3B3B3B' ),
		'293' => array( 'name' => 'gray24', 'red' => '61', 'green' => '61', 'blue' => '61', 'hex' => '3D3D3D' ),
		'294' => array( 'name' => 'gray25', 'red' => '64', 'green' => '64', 'blue' => '64', 'hex' => '404040' ),
		'295' => array( 'name' => 'gray26', 'red' => '66', 'green' => '66', 'blue' => '66', 'hex' => '424242' ),
		'296' => array( 'name' => 'gray27', 'red' => '69', 'green' => '69', 'blue' => '69', 'hex' => '454545' ),
		'297' => array( 'name' => 'gray28', 'red' => '71', 'green' => '71', 'blue' => '71', 'hex' => '474747' ),
		'298' => array( 'name' => 'gray29', 'red' => '74', 'green' => '74', 'blue' => '74', 'hex' => '4A4A4A' ),
		'299' => array( 'name' => 'gray3', 'red' => '8', 'green' => '8', 'blue' => '8', 'hex' => '080808' ),
		'300' => array( 'name' => 'gray30', 'red' => '77', 'green' => '77', 'blue' => '77', 'hex' => '4D4D4D' ),
		'301' => array( 'name' => 'gray31', 'red' => '79', 'green' => '79', 'blue' => '79', 'hex' => '4F4F4F' ),
		'302' => array( 'name' => 'gray32', 'red' => '82', 'green' => '82', 'blue' => '82', 'hex' => '525252' ),
		'303' => array( 'name' => 'gray33', 'red' => '84', 'green' => '84', 'blue' => '84', 'hex' => '545454' ),
		'304' => array( 'name' => 'gray34', 'red' => '87', 'green' => '87', 'blue' => '87', 'hex' => '575757' ),
		'305' => array( 'name' => 'gray35', 'red' => '89', 'green' => '89', 'blue' => '89', 'hex' => '595959' ),
		'306' => array( 'name' => 'gray36', 'red' => '92', 'green' => '92', 'blue' => '92', 'hex' => '5C5C5C' ),
		'307' => array( 'name' => 'gray37', 'red' => '94', 'green' => '94', 'blue' => '94', 'hex' => '5E5E5E' ),
		'308' => array( 'name' => 'gray38', 'red' => '97', 'green' => '97', 'blue' => '97', 'hex' => '616161' ),
		'309' => array( 'name' => 'gray39', 'red' => '99', 'green' => '99', 'blue' => '99', 'hex' => '636363' ),
		'310' => array( 'name' => 'gray4', 'red' => '10', 'green' => '10', 'blue' => '10', 'hex' => '0A0A0A' ),
		'311' => array( 'name' => 'gray40', 'red' => '102', 'green' => '102', 'blue' => '102', 'hex' => '666666' ),
		'312' => array( 'name' => 'gray42', 'red' => '107', 'green' => '107', 'blue' => '107', 'hex' => '6B6B6B' ),
		'313' => array( 'name' => 'gray43', 'red' => '110', 'green' => '110', 'blue' => '110', 'hex' => '6E6E6E' ),
		'314' => array( 'name' => 'gray44', 'red' => '112', 'green' => '112', 'blue' => '112', 'hex' => '707070' ),
		'315' => array( 'name' => 'gray45', 'red' => '115', 'green' => '115', 'blue' => '115', 'hex' => '737373' ),
		'316' => array( 'name' => 'gray46', 'red' => '117', 'green' => '117', 'blue' => '117', 'hex' => '757575' ),
		'317' => array( 'name' => 'gray47', 'red' => '120', 'green' => '120', 'blue' => '120', 'hex' => '787878' ),
		'318' => array( 'name' => 'gray48', 'red' => '122', 'green' => '122', 'blue' => '122', 'hex' => '7A7A7A' ),
		'319' => array( 'name' => 'gray49', 'red' => '125', 'green' => '125', 'blue' => '125', 'hex' => '7D7D7D' ),
		'320' => array( 'name' => 'gray5', 'red' => '13', 'green' => '13', 'blue' => '13', 'hex' => '0D0D0D' ),
		'321' => array( 'name' => 'gray50', 'red' => '127', 'green' => '127', 'blue' => '127', 'hex' => '7F7F7F' ),
		'322' => array( 'name' => 'gray51', 'red' => '130', 'green' => '130', 'blue' => '130', 'hex' => '828282' ),
		'323' => array( 'name' => 'gray52', 'red' => '133', 'green' => '133', 'blue' => '133', 'hex' => '858585' ),
		'324' => array( 'name' => 'gray53', 'red' => '135', 'green' => '135', 'blue' => '135', 'hex' => '878787' ),
		'325' => array( 'name' => 'gray54', 'red' => '138', 'green' => '138', 'blue' => '138', 'hex' => '8A8A8A' ),
		'326' => array( 'name' => 'gray55', 'red' => '140', 'green' => '140', 'blue' => '140', 'hex' => '8C8C8C' ),
		'327' => array( 'name' => 'gray56', 'red' => '143', 'green' => '143', 'blue' => '143', 'hex' => '8F8F8F' ),
		'328' => array( 'name' => 'gray57', 'red' => '145', 'green' => '145', 'blue' => '145', 'hex' => '919191' ),
		'329' => array( 'name' => 'gray58', 'red' => '148', 'green' => '148', 'blue' => '148', 'hex' => '949494' ),
		'330' => array( 'name' => 'gray59', 'red' => '150', 'green' => '150', 'blue' => '150', 'hex' => '969696' ),
		'331' => array( 'name' => 'gray6', 'red' => '15', 'green' => '15', 'blue' => '15', 'hex' => '0F0F0F' ),
		'332' => array( 'name' => 'gray60', 'red' => '153', 'green' => '153', 'blue' => '153', 'hex' => '999999' ),
		'333' => array( 'name' => 'gray61', 'red' => '156', 'green' => '156', 'blue' => '156', 'hex' => '9C9C9C' ),
		'334' => array( 'name' => 'gray62', 'red' => '158', 'green' => '158', 'blue' => '158', 'hex' => '9E9E9E' ),
		'335' => array( 'name' => 'gray63', 'red' => '161', 'green' => '161', 'blue' => '161', 'hex' => 'A1A1A1' ),
		'336' => array( 'name' => 'gray64', 'red' => '163', 'green' => '163', 'blue' => '163', 'hex' => 'A3A3A3' ),
		'337' => array( 'name' => 'gray65', 'red' => '166', 'green' => '166', 'blue' => '166', 'hex' => 'A6A6A6' ),
		'338' => array( 'name' => 'gray66', 'red' => '168', 'green' => '168', 'blue' => '168', 'hex' => 'A8A8A8' ),
		'339' => array( 'name' => 'gray67', 'red' => '171', 'green' => '171', 'blue' => '171', 'hex' => 'ABABAB' ),
		'340' => array( 'name' => 'gray68', 'red' => '173', 'green' => '173', 'blue' => '173', 'hex' => 'ADADAD' ),
		'341' => array( 'name' => 'gray69', 'red' => '176', 'green' => '176', 'blue' => '176', 'hex' => 'B0B0B0' ),
		'342' => array( 'name' => 'gray7', 'red' => '18', 'green' => '18', 'blue' => '18', 'hex' => '121212' ),
		'343' => array( 'name' => 'gray70', 'red' => '179', 'green' => '179', 'blue' => '179', 'hex' => 'B3B3B3' ),
		'344' => array( 'name' => 'gray71', 'red' => '181', 'green' => '181', 'blue' => '181', 'hex' => 'B5B5B5' ),
		'345' => array( 'name' => 'gray72', 'red' => '184', 'green' => '184', 'blue' => '184', 'hex' => 'B8B8B8' ),
		'346' => array( 'name' => 'gray73', 'red' => '186', 'green' => '186', 'blue' => '186', 'hex' => 'BABABA' ),
		'347' => array( 'name' => 'gray74', 'red' => '189', 'green' => '189', 'blue' => '189', 'hex' => 'BDBDBD' ),
		'348' => array( 'name' => 'gray75', 'red' => '191', 'green' => '191', 'blue' => '191', 'hex' => 'BFBFBF' ),
		'349' => array( 'name' => 'gray76', 'red' => '194', 'green' => '194', 'blue' => '194', 'hex' => 'C2C2C2' ),
		'350' => array( 'name' => 'gray77', 'red' => '196', 'green' => '196', 'blue' => '196', 'hex' => 'C4C4C4' ),
		'351' => array( 'name' => 'gray78', 'red' => '199', 'green' => '199', 'blue' => '199', 'hex' => 'C7C7C7' ),
		'352' => array( 'name' => 'gray79', 'red' => '201', 'green' => '201', 'blue' => '201', 'hex' => 'C9C9C9' ),
		'353' => array( 'name' => 'gray8', 'red' => '20', 'green' => '20', 'blue' => '20', 'hex' => '141414' ),
		'354' => array( 'name' => 'gray80', 'red' => '204', 'green' => '204', 'blue' => '204', 'hex' => 'CCCCCC' ),
		'355' => array( 'name' => 'gray81', 'red' => '207', 'green' => '207', 'blue' => '207', 'hex' => 'CFCFCF' ),
		'356' => array( 'name' => 'gray82', 'red' => '209', 'green' => '209', 'blue' => '209', 'hex' => 'D1D1D1' ),
		'357' => array( 'name' => 'gray83', 'red' => '212', 'green' => '212', 'blue' => '212', 'hex' => 'D4D4D4' ),
		'358' => array( 'name' => 'gray84', 'red' => '214', 'green' => '214', 'blue' => '214', 'hex' => 'D6D6D6' ),
		'359' => array( 'name' => 'gray85', 'red' => '217', 'green' => '217', 'blue' => '217', 'hex' => 'D9D9D9' ),
		'360' => array( 'name' => 'gray86', 'red' => '219', 'green' => '219', 'blue' => '219', 'hex' => 'DBDBDB' ),
		'361' => array( 'name' => 'gray87', 'red' => '222', 'green' => '222', 'blue' => '222', 'hex' => 'DEDEDE' ),
		'362' => array( 'name' => 'gray88', 'red' => '224', 'green' => '224', 'blue' => '224', 'hex' => 'E0E0E0' ),
		'363' => array( 'name' => 'gray89', 'red' => '227', 'green' => '227', 'blue' => '227', 'hex' => 'E3E3E3' ),
		'364' => array( 'name' => 'gray9', 'red' => '23', 'green' => '23', 'blue' => '23', 'hex' => '171717' ),
		'365' => array( 'name' => 'gray90', 'red' => '229', 'green' => '229', 'blue' => '229', 'hex' => 'E5E5E5' ),
		'366' => array( 'name' => 'gray91', 'red' => '232', 'green' => '232', 'blue' => '232', 'hex' => 'E8E8E8' ),
		'367' => array( 'name' => 'gray92', 'red' => '235', 'green' => '235', 'blue' => '235', 'hex' => 'EBEBEB' ),
		'368' => array( 'name' => 'gray93', 'red' => '237', 'green' => '237', 'blue' => '237', 'hex' => 'EDEDED' ),
		'369' => array( 'name' => 'gray94', 'red' => '240', 'green' => '240', 'blue' => '240', 'hex' => 'F0F0F0' ),
		'370' => array( 'name' => 'gray95', 'red' => '242', 'green' => '242', 'blue' => '242', 'hex' => 'F2F2F2' ),
		'371' => array( 'name' => 'gray97', 'red' => '247', 'green' => '247', 'blue' => '247', 'hex' => 'F7F7F7' ),
		'372' => array( 'name' => 'gray98', 'red' => '250', 'green' => '250', 'blue' => '250', 'hex' => 'FAFAFA' ),
		'373' => array( 'name' => 'gray99', 'red' => '252', 'green' => '252', 'blue' => '252', 'hex' => 'FCFCFC' ),
		'374' => array( 'name' => 'green', 'red' => '0', 'green' => '255', 'blue' => '0', 'hex' => '00FF00' ),
		'375' => array( 'name' => 'green copper', 'red' => '133', 'green' => '99', 'blue' => '99', 'hex' => '856363' ),
		'376' => array( 'name' => 'green yellow', 'red' => '173', 'green' => '255', 'blue' => '47', 'hex' => 'ADFF2F' ),
		'377' => array( 'name' => 'green*', 'red' => '0', 'green' => '128', 'blue' => '0', 'hex' => '008000' ),
		'378' => array( 'name' => 'green1', 'red' => '0', 'green' => '255', 'blue' => '0', 'hex' => '00FF00' ),
		'379' => array( 'name' => 'green1 (lime*)', 'red' => '0', 'green' => '255', 'blue' => '0', 'hex' => '00FF00' ),
		'380' => array( 'name' => 'green2', 'red' => '0', 'green' => '238', 'blue' => '0', 'hex' => '00EE00' ),
		'381' => array( 'name' => 'green3', 'red' => '0', 'green' => '205', 'blue' => '0', 'hex' => '00CD00' ),
		'382' => array( 'name' => 'green4', 'red' => '0', 'green' => '139', 'blue' => '0', 'hex' => '008B00' ),
		'383' => array( 'name' => 'greenyellow', 'red' => '173', 'green' => '255', 'blue' => '47', 'hex' => 'ADFF2F' ),
		'384' => array( 'name' => 'grey', 'red' => '190', 'green' => '190', 'blue' => '190', 'hex' => 'BEBEBE' ),
		'385' => array( 'name' => 'grey, silver', 'red' => '192', 'green' => '192', 'blue' => '192', 'hex' => 'C0C0C0' ),
		'386' => array( 'name' => 'grey0', 'red' => '0', 'green' => '0', 'blue' => '0', 'hex' => '000000' ),
		'387' => array( 'name' => 'grey1', 'red' => '3', 'green' => '3', 'blue' => '3', 'hex' => '030303' ),
		'388' => array( 'name' => 'grey10', 'red' => '26', 'green' => '26', 'blue' => '26', 'hex' => '1A1A1A' ),
		'389' => array( 'name' => 'grey100, white', 'red' => '255', 'green' => '255', 'blue' => '255', 'hex' => 'FFFFFF' ),
		'390' => array( 'name' => 'grey11', 'red' => '28', 'green' => '28', 'blue' => '28', 'hex' => '1C1C1C' ),
		'391' => array( 'name' => 'grey12', 'red' => '31', 'green' => '31', 'blue' => '31', 'hex' => '1F1F1F' ),
		'392' => array( 'name' => 'grey13', 'red' => '33', 'green' => '33', 'blue' => '33', 'hex' => '212121' ),
		'393' => array( 'name' => 'grey13a', 'red' => '34', 'green' => '34', 'blue' => '34', 'hex' => '222222' ),
		'394' => array( 'name' => 'grey14', 'red' => '36', 'green' => '36', 'blue' => '36', 'hex' => '242424' ),
		'395' => array( 'name' => 'grey15', 'red' => '38', 'green' => '38', 'blue' => '38', 'hex' => '262626' ),
		'396' => array( 'name' => 'grey16', 'red' => '41', 'green' => '41', 'blue' => '41', 'hex' => '292929' ),
		'397' => array( 'name' => 'grey17', 'red' => '43', 'green' => '43', 'blue' => '43', 'hex' => '2B2B2B' ),
		'398' => array( 'name' => 'grey18', 'red' => '46', 'green' => '46', 'blue' => '46', 'hex' => '2E2E2E' ),
		'399' => array( 'name' => 'grey19', 'red' => '48', 'green' => '48', 'blue' => '48', 'hex' => '303030' ),
		'400' => array( 'name' => 'grey2', 'red' => '5', 'green' => '5', 'blue' => '5', 'hex' => '050505' ),
		'401' => array( 'name' => 'grey20', 'red' => '51', 'green' => '51', 'blue' => '51', 'hex' => '333333' ),
		'402' => array( 'name' => 'grey21', 'red' => '54', 'green' => '54', 'blue' => '54', 'hex' => '363636' ),
		'403' => array( 'name' => 'grey22', 'red' => '56', 'green' => '56', 'blue' => '56', 'hex' => '383838' ),
		'404' => array( 'name' => 'grey23', 'red' => '59', 'green' => '59', 'blue' => '59', 'hex' => '3B3B3B' ),
		'405' => array( 'name' => 'grey24', 'red' => '61', 'green' => '61', 'blue' => '61', 'hex' => '3D3D3D' ),
		'406' => array( 'name' => 'grey25', 'red' => '64', 'green' => '64', 'blue' => '64', 'hex' => '404040' ),
		'407' => array( 'name' => 'grey26', 'red' => '66', 'green' => '66', 'blue' => '66', 'hex' => '424242' ),
		'408' => array( 'name' => 'grey27', 'red' => '69', 'green' => '69', 'blue' => '69', 'hex' => '454545' ),
		'409' => array( 'name' => 'grey28', 'red' => '71', 'green' => '71', 'blue' => '71', 'hex' => '474747' ),
		'410' => array( 'name' => 'grey29', 'red' => '74', 'green' => '74', 'blue' => '74', 'hex' => '4A4A4A' ),
		'411' => array( 'name' => 'grey3', 'red' => '8', 'green' => '8', 'blue' => '8', 'hex' => '080808' ),
		'412' => array( 'name' => 'grey30', 'red' => '77', 'green' => '77', 'blue' => '77', 'hex' => '4D4D4D' ),
		'413' => array( 'name' => 'grey31', 'red' => '79', 'green' => '79', 'blue' => '79', 'hex' => '4F4F4F' ),
		'414' => array( 'name' => 'grey32', 'red' => '82', 'green' => '82', 'blue' => '82', 'hex' => '525252' ),
		'415' => array( 'name' => 'grey33', 'red' => '84', 'green' => '84', 'blue' => '84', 'hex' => '545454' ),
		'416' => array( 'name' => 'grey33a', 'red' => '85', 'green' => '85', 'blue' => '85', 'hex' => '555555' ),
		'417' => array( 'name' => 'grey34', 'red' => '87', 'green' => '87', 'blue' => '87', 'hex' => '575757' ),
		'418' => array( 'name' => 'grey35', 'red' => '89', 'green' => '89', 'blue' => '89', 'hex' => '595959' ),
		'419' => array( 'name' => 'grey36', 'red' => '92', 'green' => '92', 'blue' => '92', 'hex' => '5C5C5C' ),
		'420' => array( 'name' => 'grey37', 'red' => '94', 'green' => '94', 'blue' => '94', 'hex' => '5E5E5E' ),
		'421' => array( 'name' => 'grey38', 'red' => '97', 'green' => '97', 'blue' => '97', 'hex' => '616161' ),
		'422' => array( 'name' => 'grey39', 'red' => '99', 'green' => '99', 'blue' => '99', 'hex' => '636363' ),
		'423' => array( 'name' => 'grey4', 'red' => '10', 'green' => '10', 'blue' => '10', 'hex' => '0A0A0A' ),
		'424' => array( 'name' => 'grey40', 'red' => '102', 'green' => '102', 'blue' => '102', 'hex' => '666666' ),
		'425' => array( 'name' => 'grey41', 'red' => '105', 'green' => '105', 'blue' => '105', 'hex' => '696969' ),
		'426' => array( 'name' => 'grey41, dimgrey', 'red' => '105', 'green' => '105', 'blue' => '105', 'hex' => '696969' ),
		'427' => array( 'name' => 'grey42', 'red' => '107', 'green' => '107', 'blue' => '107', 'hex' => '6B6B6B' ),
		'428' => array( 'name' => 'grey43', 'red' => '110', 'green' => '110', 'blue' => '110', 'hex' => '6E6E6E' ),
		'429' => array( 'name' => 'grey44', 'red' => '112', 'green' => '112', 'blue' => '112', 'hex' => '707070' ),
		'430' => array( 'name' => 'grey45', 'red' => '115', 'green' => '115', 'blue' => '115', 'hex' => '737373' ),
		'431' => array( 'name' => 'grey46', 'red' => '117', 'green' => '117', 'blue' => '117', 'hex' => '757575' ),
		'432' => array( 'name' => 'grey46a', 'red' => '119', 'green' => '119', 'blue' => '119', 'hex' => '777777' ),
		'433' => array( 'name' => 'grey47', 'red' => '120', 'green' => '120', 'blue' => '120', 'hex' => '787878' ),
		'434' => array( 'name' => 'grey48', 'red' => '122', 'green' => '122', 'blue' => '122', 'hex' => '7A7A7A' ),
		'435' => array( 'name' => 'grey49', 'red' => '125', 'green' => '125', 'blue' => '125', 'hex' => '7D7D7D' ),
		'436' => array( 'name' => 'grey5', 'red' => '13', 'green' => '13', 'blue' => '13', 'hex' => '0D0D0D' ),
		'437' => array( 'name' => 'grey50', 'red' => '127', 'green' => '127', 'blue' => '127', 'hex' => '7F7F7F' ),
		'438' => array( 'name' => 'grey51', 'red' => '130', 'green' => '130', 'blue' => '130', 'hex' => '828282' ),
		'439' => array( 'name' => 'grey52', 'red' => '133', 'green' => '133', 'blue' => '133', 'hex' => '858585' ),
		'440' => array( 'name' => 'grey53', 'red' => '135', 'green' => '135', 'blue' => '135', 'hex' => '878787' ),
		'441' => array( 'name' => 'grey53a', 'red' => '136', 'green' => '136', 'blue' => '136', 'hex' => '888888' ),
		'442' => array( 'name' => 'grey54', 'red' => '138', 'green' => '138', 'blue' => '138', 'hex' => '8A8A8A' ),
		'443' => array( 'name' => 'grey55', 'red' => '140', 'green' => '140', 'blue' => '140', 'hex' => '8C8C8C' ),
		'444' => array( 'name' => 'grey56', 'red' => '143', 'green' => '143', 'blue' => '143', 'hex' => '8F8F8F' ),
		'445' => array( 'name' => 'grey57', 'red' => '145', 'green' => '145', 'blue' => '145', 'hex' => '919191' ),
		'446' => array( 'name' => 'grey58', 'red' => '148', 'green' => '148', 'blue' => '148', 'hex' => '949494' ),
		'447' => array( 'name' => 'grey59', 'red' => '150', 'green' => '150', 'blue' => '150', 'hex' => '969696' ),
		'448' => array( 'name' => 'grey6', 'red' => '15', 'green' => '15', 'blue' => '15', 'hex' => '0F0F0F' ),
		'449' => array( 'name' => 'grey60', 'red' => '153', 'green' => '153', 'blue' => '153', 'hex' => '999999' ),
		'450' => array( 'name' => 'grey61', 'red' => '156', 'green' => '156', 'blue' => '156', 'hex' => '9C9C9C' ),
		'451' => array( 'name' => 'grey62', 'red' => '158', 'green' => '158', 'blue' => '158', 'hex' => '9E9E9E' ),
		'452' => array( 'name' => 'grey63', 'red' => '161', 'green' => '161', 'blue' => '161', 'hex' => 'A1A1A1' ),
		'453' => array( 'name' => 'grey64', 'red' => '163', 'green' => '163', 'blue' => '163', 'hex' => 'A3A3A3' ),
		'454' => array( 'name' => 'grey65', 'red' => '166', 'green' => '166', 'blue' => '166', 'hex' => 'A6A6A6' ),
		'455' => array( 'name' => 'grey66', 'red' => '168', 'green' => '168', 'blue' => '168', 'hex' => 'A8A8A8' ),
		'456' => array( 'name' => 'grey67', 'red' => '171', 'green' => '171', 'blue' => '171', 'hex' => 'ABABAB' ),
		'457' => array( 'name' => 'grey68', 'red' => '173', 'green' => '173', 'blue' => '173', 'hex' => 'ADADAD' ),
		'458' => array( 'name' => 'grey69', 'red' => '176', 'green' => '176', 'blue' => '176', 'hex' => 'B0B0B0' ),
		'459' => array( 'name' => 'grey7', 'red' => '18', 'green' => '18', 'blue' => '18', 'hex' => '121212' ),
		'460' => array( 'name' => 'grey70', 'red' => '179', 'green' => '179', 'blue' => '179', 'hex' => 'B3B3B3' ),
		'461' => array( 'name' => 'grey71', 'red' => '181', 'green' => '181', 'blue' => '181', 'hex' => 'B5B5B5' ),
		'462' => array( 'name' => 'grey72', 'red' => '184', 'green' => '184', 'blue' => '184', 'hex' => 'B8B8B8' ),
		'463' => array( 'name' => 'grey72a', 'red' => '187', 'green' => '187', 'blue' => '187', 'hex' => 'BBBBBB' ),
		'464' => array( 'name' => 'grey73', 'red' => '186', 'green' => '186', 'blue' => '186', 'hex' => 'BABABA' ),
		'465' => array( 'name' => 'grey74', 'red' => '189', 'green' => '189', 'blue' => '189', 'hex' => 'BDBDBD' ),
		'466' => array( 'name' => 'grey75', 'red' => '191', 'green' => '191', 'blue' => '191', 'hex' => 'BFBFBF' ),
		'467' => array( 'name' => 'grey76', 'red' => '194', 'green' => '194', 'blue' => '194', 'hex' => 'C2C2C2' ),
		'468' => array( 'name' => 'grey77', 'red' => '196', 'green' => '196', 'blue' => '196', 'hex' => 'C4C4C4' ),
		'469' => array( 'name' => 'grey78', 'red' => '199', 'green' => '199', 'blue' => '199', 'hex' => 'C7C7C7' ),
		'470' => array( 'name' => 'grey79', 'red' => '201', 'green' => '201', 'blue' => '201', 'hex' => 'C9C9C9' ),
		'471' => array( 'name' => 'grey8', 'red' => '20', 'green' => '20', 'blue' => '20', 'hex' => '141414' ),
		'472' => array( 'name' => 'grey80', 'red' => '204', 'green' => '204', 'blue' => '204', 'hex' => 'CCCCCC' ),
		'473' => array( 'name' => 'grey81', 'red' => '207', 'green' => '207', 'blue' => '207', 'hex' => 'CFCFCF' ),
		'474' => array( 'name' => 'grey82', 'red' => '209', 'green' => '209', 'blue' => '209', 'hex' => 'D1D1D1' ),
		'475' => array( 'name' => 'grey83', 'red' => '212', 'green' => '212', 'blue' => '212', 'hex' => 'D4D4D4' ),
		'476' => array( 'name' => 'grey84', 'red' => '214', 'green' => '214', 'blue' => '214', 'hex' => 'D6D6D6' ),
		'477' => array( 'name' => 'grey85', 'red' => '217', 'green' => '217', 'blue' => '217', 'hex' => 'D9D9D9' ),
		'478' => array( 'name' => 'grey86', 'red' => '219', 'green' => '219', 'blue' => '219', 'hex' => 'DBDBDB' ),
		'479' => array( 'name' => 'grey87', 'red' => '222', 'green' => '222', 'blue' => '222', 'hex' => 'DEDEDE' ),
		'480' => array( 'name' => 'grey87a', 'red' => '221', 'green' => '221', 'blue' => '221', 'hex' => 'DDDDDD' ),
		'481' => array( 'name' => 'grey88', 'red' => '224', 'green' => '224', 'blue' => '224', 'hex' => 'E0E0E0' ),
		'482' => array( 'name' => 'grey89', 'red' => '227', 'green' => '227', 'blue' => '227', 'hex' => 'E3E3E3' ),
		'483' => array( 'name' => 'grey9', 'red' => '23', 'green' => '23', 'blue' => '23', 'hex' => '171717' ),
		'484' => array( 'name' => 'grey90', 'red' => '229', 'green' => '229', 'blue' => '229', 'hex' => 'E5E5E5' ),
		'485' => array( 'name' => 'grey91', 'red' => '232', 'green' => '232', 'blue' => '232', 'hex' => 'E8E8E8' ),
		'486' => array( 'name' => 'grey92', 'red' => '235', 'green' => '235', 'blue' => '235', 'hex' => 'EBEBEB' ),
		'487' => array( 'name' => 'grey93', 'red' => '237', 'green' => '237', 'blue' => '237', 'hex' => 'EDEDED' ),
		'488' => array( 'name' => 'grey93a', 'red' => '238', 'green' => '238', 'blue' => '238', 'hex' => 'EEEEEE' ),
		'489' => array( 'name' => 'grey94', 'red' => '240', 'green' => '240', 'blue' => '240', 'hex' => 'F0F0F0' ),
		'490' => array( 'name' => 'grey95', 'red' => '242', 'green' => '242', 'blue' => '242', 'hex' => 'F2F2F2' ),
		'491' => array( 'name' => 'grey96', 'red' => '245', 'green' => '245', 'blue' => '245', 'hex' => 'F5F5F5' ),
		'492' => array( 'name' => 'grey97', 'red' => '247', 'green' => '247', 'blue' => '247', 'hex' => 'F7F7F7' ),
		'493' => array( 'name' => 'grey98', 'red' => '250', 'green' => '250', 'blue' => '250', 'hex' => 'FAFAFA' ),
		'494' => array( 'name' => 'grey99', 'red' => '252', 'green' => '252', 'blue' => '252', 'hex' => 'FCFCFC' ),
		'495' => array( 'name' => 'honeydew', 'red' => '240', 'green' => '255', 'blue' => '240', 'hex' => 'F0FFF0' ),
		'496' => array( 'name' => 'honeydew1', 'red' => '240', 'green' => '255', 'blue' => '240', 'hex' => 'F0FFF0' ),
		'497' => array( 'name' => 'honeydew1 (honeydew)', 'red' => '240', 'green' => '255', 'blue' => '240', 'hex' => 'F0FFF0' ),
		'498' => array( 'name' => 'honeydew2', 'red' => '224', 'green' => '238', 'blue' => '224', 'hex' => 'E0EEE0' ),
		'499' => array( 'name' => 'honeydew3', 'red' => '193', 'green' => '205', 'blue' => '193', 'hex' => 'C1CDC1' ),
		'500' => array( 'name' => 'honeydew4', 'red' => '131', 'green' => '139', 'blue' => '131', 'hex' => '838B83' ),
		'501' => array( 'name' => 'hot pink', 'red' => '255', 'green' => '105', 'blue' => '180', 'hex' => 'FF69B4' ),
		'502' => array( 'name' => 'hot pink1', 'red' => '255', 'green' => '110', 'blue' => '180', 'hex' => 'FF6EB4' ),
		'503' => array( 'name' => 'hot pink2', 'red' => '238', 'green' => '106', 'blue' => '167', 'hex' => 'EE6AA7' ),
		'504' => array( 'name' => 'hot pink3', 'red' => '205', 'green' => '96', 'blue' => '144', 'hex' => 'CD6090' ),
		'505' => array( 'name' => 'hot pink4', 'red' => '139', 'green' => '58', 'blue' => '98', 'hex' => '8B3A62' ),
		'506' => array( 'name' => 'hotpink', 'red' => '255', 'green' => '105', 'blue' => '180', 'hex' => 'FF69B4' ),
		'507' => array( 'name' => 'hotpink1', 'red' => '255', 'green' => '110', 'blue' => '180', 'hex' => 'FF6EB4' ),
		'508' => array( 'name' => 'hotpink2', 'red' => '238', 'green' => '106', 'blue' => '167', 'hex' => 'EE6AA7' ),
		'509' => array( 'name' => 'hotpink3', 'red' => '205', 'green' => '96', 'blue' => '144', 'hex' => 'CD6090' ),
		'510' => array( 'name' => 'hotpink4', 'red' => '139', 'green' => '58', 'blue' => '98', 'hex' => '8B3A62' ),
		'511' => array( 'name' => 'hunter green', 'red' => '142', 'green' => '35', 'blue' => '35', 'hex' => '8E2323' ),
		'512' => array( 'name' => 'indian red', 'red' => '205', 'green' => '92', 'blue' => '92', 'hex' => 'CD5C5C' ),
		'513' => array( 'name' => 'indian red1', 'red' => '255', 'green' => '106', 'blue' => '106', 'hex' => 'FF6A6A' ),
		'514' => array( 'name' => 'indian red2', 'red' => '238', 'green' => '99', 'blue' => '99', 'hex' => 'EE6363' ),
		'515' => array( 'name' => 'indian red3', 'red' => '205', 'green' => '85', 'blue' => '85', 'hex' => 'CD5555' ),
		'516' => array( 'name' => 'indian red4', 'red' => '139', 'green' => '58', 'blue' => '58', 'hex' => '8B3A3A' ),
		'517' => array( 'name' => 'indianred', 'red' => '205', 'green' => '92', 'blue' => '92', 'hex' => 'CD5C5C' ),
		'518' => array( 'name' => 'indianred1', 'red' => '255', 'green' => '106', 'blue' => '106', 'hex' => 'FF6A6A' ),
		'519' => array( 'name' => 'indianred2', 'red' => '238', 'green' => '99', 'blue' => '99', 'hex' => 'EE6363' ),
		'520' => array( 'name' => 'indianred3', 'red' => '205', 'green' => '85', 'blue' => '85', 'hex' => 'CD5555' ),
		'521' => array( 'name' => 'indianred4', 'red' => '139', 'green' => '58', 'blue' => '58', 'hex' => '8B3A3A' ),
		'522' => array( 'name' => 'indigo', 'red' => '75', 'green' => '0', 'blue' => '130', 'hex' => '4B0082' ),
		'523' => array( 'name' => 'iris blue', 'red' => '3', 'green' => '180', 'blue' => '200', 'hex' => '03B4C8' ),
		'524' => array( 'name' => 'ivory', 'red' => '255', 'green' => '255', 'blue' => '240', 'hex' => 'FFFFF0' ),
		'525' => array( 'name' => 'ivory1', 'red' => '255', 'green' => '255', 'blue' => '240', 'hex' => 'FFFFF0' ),
		'526' => array( 'name' => 'ivory1 (ivory)', 'red' => '255', 'green' => '255', 'blue' => '240', 'hex' => 'FFFFF0' ),
		'527' => array( 'name' => 'ivory2', 'red' => '238', 'green' => '238', 'blue' => '224', 'hex' => 'EEEEE0' ),
		'528' => array( 'name' => 'ivory3', 'red' => '205', 'green' => '205', 'blue' => '193', 'hex' => 'CDCDC1' ),
		'529' => array( 'name' => 'ivory4', 'red' => '139', 'green' => '139', 'blue' => '131', 'hex' => '8B8B83' ),
		'530' => array( 'name' => 'ivoryblack', 'red' => '41', 'green' => '36', 'blue' => '33', 'hex' => '292421' ),
		'531' => array( 'name' => 'khaki', 'red' => '240', 'green' => '230', 'blue' => '140', 'hex' => 'F0E68C' ),
		'532' => array( 'name' => 'khaki1', 'red' => '255', 'green' => '246', 'blue' => '143', 'hex' => 'FFF68F' ),
		'533' => array( 'name' => 'khaki2', 'red' => '238', 'green' => '230', 'blue' => '133', 'hex' => 'EEE685' ),
		'534' => array( 'name' => 'khaki3', 'red' => '205', 'green' => '198', 'blue' => '115', 'hex' => 'CDC673' ),
		'535' => array( 'name' => 'khaki4', 'red' => '139', 'green' => '134', 'blue' => '78', 'hex' => '8B864E' ),
		'536' => array( 'name' => 'lavender', 'red' => '230', 'green' => '230', 'blue' => '250', 'hex' => 'E6E6FA' ),
		'537' => array( 'name' => 'lavender blush', 'red' => '255', 'green' => '240', 'blue' => '245', 'hex' => 'FFF0F5' ),
		'538' => array( 'name' => 'lavender blush1', 'red' => '255', 'green' => '240', 'blue' => '245', 'hex' => 'FFF0F5' ),
		'539' => array( 'name' => 'lavender blush2', 'red' => '238', 'green' => '224', 'blue' => '229', 'hex' => 'EEE0E5' ),
		'540' => array( 'name' => 'lavender blush3', 'red' => '205', 'green' => '193', 'blue' => '197', 'hex' => 'CDC1C5' ),
		'541' => array( 'name' => 'lavender blush4', 'red' => '139', 'green' => '131', 'blue' => '134', 'hex' => '8B8386' ),
		'542' => array( 'name' => 'lavenderblush', 'red' => '255', 'green' => '240', 'blue' => '245', 'hex' => 'FFF0F5' ),
		'543' => array( 'name' => 'lavenderblush1', 'red' => '255', 'green' => '240', 'blue' => '245', 'hex' => 'FFF0F5' ),
		'544' => array( 'name' => 'lavenderblush1 (lavenderblush)', 'red' => '255', 'green' => '240', 'blue' => '245', 'hex' => 'FFF0F5' ),
		'545' => array( 'name' => 'lavenderblush2', 'red' => '238', 'green' => '224', 'blue' => '229', 'hex' => 'EEE0E5' ),
		'546' => array( 'name' => 'lavenderblush3', 'red' => '205', 'green' => '193', 'blue' => '197', 'hex' => 'CDC1C5' ),
		'547' => array( 'name' => 'lavenderblush4', 'red' => '139', 'green' => '131', 'blue' => '134', 'hex' => '8B8386' ),
		'548' => array( 'name' => 'lawn green', 'red' => '124', 'green' => '252', 'blue' => '0', 'hex' => '7CFC00' ),
		'549' => array( 'name' => 'lawngreen', 'red' => '124', 'green' => '252', 'blue' => '0', 'hex' => '7CFC00' ),
		'550' => array( 'name' => 'lemon chiffon', 'red' => '255', 'green' => '250', 'blue' => '205', 'hex' => 'FFFACD' ),
		'551' => array( 'name' => 'lemon chiffon1', 'red' => '255', 'green' => '250', 'blue' => '205', 'hex' => 'FFFACD' ),
		'552' => array( 'name' => 'lemon chiffon2', 'red' => '238', 'green' => '233', 'blue' => '191', 'hex' => 'EEE9BF' ),
		'553' => array( 'name' => 'lemon chiffon3', 'red' => '205', 'green' => '201', 'blue' => '165', 'hex' => 'CDC9A5' ),
		'554' => array( 'name' => 'lemon chiffon4', 'red' => '139', 'green' => '137', 'blue' => '112', 'hex' => '8B8970' ),
		'555' => array( 'name' => 'lemonchiffon', 'red' => '255', 'green' => '250', 'blue' => '205', 'hex' => 'FFFACD' ),
		'556' => array( 'name' => 'lemonchiffon1', 'red' => '255', 'green' => '250', 'blue' => '205', 'hex' => 'FFFACD' ),
		'557' => array( 'name' => 'lemonchiffon1 (lemonchiffon)', 'red' => '255', 'green' => '250', 'blue' => '205', 'hex' => 'FFFACD' ),
		'558' => array( 'name' => 'lemonchiffon2', 'red' => '238', 'green' => '233', 'blue' => '191', 'hex' => 'EEE9BF' ),
		'559' => array( 'name' => 'lemonchiffon3', 'red' => '205', 'green' => '201', 'blue' => '165', 'hex' => 'CDC9A5' ),
		'560' => array( 'name' => 'lemonchiffon4', 'red' => '139', 'green' => '137', 'blue' => '112', 'hex' => '8B8970' ),
		'561' => array( 'name' => 'light blue', 'red' => '205', 'green' => '127', 'blue' => '50', 'hex' => 'CD7F32' ),
		'562' => array( 'name' => 'light blue1', 'red' => '191', 'green' => '239', 'blue' => '255', 'hex' => 'BFEFFF' ),
		'563' => array( 'name' => 'light blue2', 'red' => '178', 'green' => '223', 'blue' => '238', 'hex' => 'B2DFEE' ),
		'564' => array( 'name' => 'light blue3', 'red' => '154', 'green' => '192', 'blue' => '205', 'hex' => '9AC0CD' ),
		'565' => array( 'name' => 'light blue4', 'red' => '104', 'green' => '131', 'blue' => '139', 'hex' => '68838B' ),
		'566' => array( 'name' => 'light coral', 'red' => '240', 'green' => '128', 'blue' => '128', 'hex' => 'F08080' ),
		'567' => array( 'name' => 'light cyan', 'red' => '224', 'green' => '255', 'blue' => '255', 'hex' => 'E0FFFF' ),
		'568' => array( 'name' => 'light cyan1', 'red' => '224', 'green' => '255', 'blue' => '255', 'hex' => 'E0FFFF' ),
		'569' => array( 'name' => 'light cyan2', 'red' => '209', 'green' => '238', 'blue' => '238', 'hex' => 'D1EEEE' ),
		'570' => array( 'name' => 'light cyan3', 'red' => '180', 'green' => '205', 'blue' => '205', 'hex' => 'B4CDCD' ),
		'571' => array( 'name' => 'light cyan4', 'red' => '122', 'green' => '139', 'blue' => '139', 'hex' => '7A8B8B' ),
		'572' => array( 'name' => 'light goldenrod', 'red' => '238', 'green' => '221', 'blue' => '130', 'hex' => 'EEDD82' ),
		'573' => array( 'name' => 'light goldenrod1', 'red' => '255', 'green' => '236', 'blue' => '139', 'hex' => 'FFEC8B' ),
		'574' => array( 'name' => 'light goldenrod2', 'red' => '238', 'green' => '220', 'blue' => '130', 'hex' => 'EEDC82' ),
		'575' => array( 'name' => 'light goldenrod3', 'red' => '205', 'green' => '190', 'blue' => '112', 'hex' => 'CDBE70' ),
		'576' => array( 'name' => 'light goldenrod4', 'red' => '139', 'green' => '129', 'blue' => '76', 'hex' => '8B814C' ),
		'577' => array( 'name' => 'light gray', 'red' => '211', 'green' => '211', 'blue' => '211', 'hex' => 'D3D3D3' ),
		'578' => array( 'name' => 'light grey', 'red' => '219', 'green' => '219', 'blue' => '112', 'hex' => 'DBDB70' ),
		'579' => array( 'name' => 'light pink', 'red' => '255', 'green' => '182', 'blue' => '193', 'hex' => 'FFB6C1' ),
		'580' => array( 'name' => 'light pink1', 'red' => '255', 'green' => '174', 'blue' => '185', 'hex' => 'FFAEB9' ),
		'581' => array( 'name' => 'light pink2', 'red' => '238', 'green' => '162', 'blue' => '173', 'hex' => 'EEA2AD' ),
		'582' => array( 'name' => 'light pink3', 'red' => '205', 'green' => '140', 'blue' => '149', 'hex' => 'CD8C95' ),
		'583' => array( 'name' => 'light pink4', 'red' => '139', 'green' => '95', 'blue' => '101', 'hex' => '8B5F65' ),
		'584' => array( 'name' => 'light salmon', 'red' => '255', 'green' => '160', 'blue' => '122', 'hex' => 'FFA07A' ),
		'585' => array( 'name' => 'light salmon1', 'red' => '255', 'green' => '160', 'blue' => '122', 'hex' => 'FFA07A' ),
		'586' => array( 'name' => 'light salmon2', 'red' => '238', 'green' => '149', 'blue' => '114', 'hex' => 'EE9572' ),
		'587' => array( 'name' => 'light salmon3', 'red' => '205', 'green' => '129', 'blue' => '98', 'hex' => 'CD8162' ),
		'588' => array( 'name' => 'light salmon4', 'red' => '139', 'green' => '87', 'blue' => '66', 'hex' => '8B5742' ),
		'589' => array( 'name' => 'light sea green', 'red' => '32', 'green' => '178', 'blue' => '170', 'hex' => '20B2AA' ),
		'590' => array( 'name' => 'light sky blue', 'red' => '135', 'green' => '206', 'blue' => '250', 'hex' => '87CEFA' ),
		'591' => array( 'name' => 'light sky blue1', 'red' => '176', 'green' => '226', 'blue' => '255', 'hex' => 'B0E2FF' ),
		'592' => array( 'name' => 'light sky blue2', 'red' => '164', 'green' => '211', 'blue' => '238', 'hex' => 'A4D3EE' ),
		'593' => array( 'name' => 'light sky blue3', 'red' => '141', 'green' => '182', 'blue' => '205', 'hex' => '8DB6CD' ),
		'594' => array( 'name' => 'light sky blue4', 'red' => '96', 'green' => '123', 'blue' => '139', 'hex' => '607B8B' ),
		'595' => array( 'name' => 'light slate gray', 'red' => '119', 'green' => '136', 'blue' => '153', 'hex' => '778899' ),
		'596' => array( 'name' => 'light slateblue', 'red' => '132', 'green' => '112', 'blue' => '255', 'hex' => '8470FF' ),
		'597' => array( 'name' => 'light steel blue', 'red' => '84', 'green' => '84', 'blue' => '84', 'hex' => '545454' ),
		'598' => array( 'name' => 'light steel blue1', 'red' => '202', 'green' => '225', 'blue' => '255', 'hex' => 'CAE1FF' ),
		'599' => array( 'name' => 'light steel blue2', 'red' => '188', 'green' => '210', 'blue' => '238', 'hex' => 'BCD2EE' ),
		'600' => array( 'name' => 'light steel blue3', 'red' => '162', 'green' => '181', 'blue' => '205', 'hex' => 'A2B5CD' ),
		'601' => array( 'name' => 'light steel blue4', 'red' => '110', 'green' => '123', 'blue' => '139', 'hex' => '6E7B8B' ),
		'602' => array( 'name' => 'light wood', 'red' => '133', 'green' => '99', 'blue' => '99', 'hex' => '856363' ),
		'603' => array( 'name' => 'light yellow', 'red' => '255', 'green' => '255', 'blue' => '224', 'hex' => 'FFFFE0' ),
		'604' => array( 'name' => 'light yellow1', 'red' => '255', 'green' => '255', 'blue' => '224', 'hex' => 'FFFFE0' ),
		'605' => array( 'name' => 'light yellow2', 'red' => '238', 'green' => '238', 'blue' => '209', 'hex' => 'EEEED1' ),
		'606' => array( 'name' => 'light yellow4', 'red' => '139', 'green' => '139', 'blue' => '122', 'hex' => '8B8B7A' ),
		'607' => array( 'name' => 'lightblue', 'red' => '173', 'green' => '216', 'blue' => '230', 'hex' => 'ADD8E6' ),
		'608' => array( 'name' => 'lightblue1', 'red' => '191', 'green' => '239', 'blue' => '255', 'hex' => 'BFEFFF' ),
		'609' => array( 'name' => 'lightblue2', 'red' => '178', 'green' => '223', 'blue' => '238', 'hex' => 'B2DFEE' ),
		'610' => array( 'name' => 'lightblue3', 'red' => '154', 'green' => '192', 'blue' => '205', 'hex' => '9AC0CD' ),
		'611' => array( 'name' => 'lightblue4', 'red' => '104', 'green' => '131', 'blue' => '139', 'hex' => '68838B' ),
		'612' => array( 'name' => 'lightcoral', 'red' => '240', 'green' => '128', 'blue' => '128', 'hex' => 'F08080' ),
		'613' => array( 'name' => 'lightcyan', 'red' => '224', 'green' => '255', 'blue' => '255', 'hex' => 'E0FFFF' ),
		'614' => array( 'name' => 'lightcyan1', 'red' => '224', 'green' => '255', 'blue' => '255', 'hex' => 'E0FFFF' ),
		'615' => array( 'name' => 'lightcyan1 (lightcyan)', 'red' => '224', 'green' => '255', 'blue' => '255', 'hex' => 'E0FFFF' ),
		'616' => array( 'name' => 'lightcyan2', 'red' => '209', 'green' => '238', 'blue' => '238', 'hex' => 'D1EEEE' ),
		'617' => array( 'name' => 'lightcyan3', 'red' => '180', 'green' => '205', 'blue' => '205', 'hex' => 'B4CDCD' ),
		'618' => array( 'name' => 'lightcyan4', 'red' => '122', 'green' => '139', 'blue' => '139', 'hex' => '7A8B8B' ),
		'619' => array( 'name' => 'lightgoldenrod', 'red' => '238', 'green' => '221', 'blue' => '130', 'hex' => 'EEDD82' ),
		'620' => array( 'name' => 'lightgoldenrod1', 'red' => '255', 'green' => '236', 'blue' => '139', 'hex' => 'FFEC8B' ),
		'621' => array( 'name' => 'lightgoldenrod2', 'red' => '238', 'green' => '220', 'blue' => '130', 'hex' => 'EEDC82' ),
		'622' => array( 'name' => 'lightgoldenrod3', 'red' => '205', 'green' => '190', 'blue' => '112', 'hex' => 'CDBE70' ),
		'623' => array( 'name' => 'lightgoldenrod4', 'red' => '139', 'green' => '129', 'blue' => '76', 'hex' => '8B814C' ),
		'624' => array( 'name' => 'lightgoldenrodyellow', 'red' => '250', 'green' => '250', 'blue' => '210', 'hex' => 'FAFAD2' ),
		'625' => array( 'name' => 'lightgray', 'red' => '211', 'green' => '211', 'blue' => '211', 'hex' => 'D3D3D3' ),
		'626' => array( 'name' => 'lightgreen', 'red' => '144', 'green' => '238', 'blue' => '144', 'hex' => '90EE90' ),
		'627' => array( 'name' => 'lightgrey', 'red' => '211', 'green' => '211', 'blue' => '211', 'hex' => 'D3D3D3' ),
		'628' => array( 'name' => 'lightpink', 'red' => '255', 'green' => '182', 'blue' => '193', 'hex' => 'FFB6C1' ),
		'629' => array( 'name' => 'lightpink1', 'red' => '255', 'green' => '174', 'blue' => '185', 'hex' => 'FFAEB9' ),
		'630' => array( 'name' => 'lightpink2', 'red' => '238', 'green' => '162', 'blue' => '173', 'hex' => 'EEA2AD' ),
		'631' => array( 'name' => 'lightpink3', 'red' => '205', 'green' => '140', 'blue' => '149', 'hex' => 'CD8C95' ),
		'632' => array( 'name' => 'lightpink4', 'red' => '139', 'green' => '95', 'blue' => '101', 'hex' => '8B5F65' ),
		'633' => array( 'name' => 'lightsalmon', 'red' => '255', 'green' => '160', 'blue' => '122', 'hex' => 'FFA07A' ),
		'634' => array( 'name' => 'lightsalmon1', 'red' => '255', 'green' => '160', 'blue' => '122', 'hex' => 'FFA07A' ),
		'635' => array( 'name' => 'lightsalmon1 (lightsalmon)', 'red' => '255', 'green' => '160', 'blue' => '122', 'hex' => 'FFA07A' ),
		'636' => array( 'name' => 'lightsalmon2', 'red' => '238', 'green' => '149', 'blue' => '114', 'hex' => 'EE9572' ),
		'637' => array( 'name' => 'lightsalmon3', 'red' => '205', 'green' => '129', 'blue' => '98', 'hex' => 'CD8162' ),
		'638' => array( 'name' => 'lightsalmon4', 'red' => '139', 'green' => '87', 'blue' => '66', 'hex' => '8B5742' ),
		'639' => array( 'name' => 'lightseagreen', 'red' => '32', 'green' => '178', 'blue' => '170', 'hex' => '20B2AA' ),
		'640' => array( 'name' => 'lightskyblue', 'red' => '135', 'green' => '206', 'blue' => '250', 'hex' => '87CEFA' ),
		'641' => array( 'name' => 'lightskyblue1', 'red' => '176', 'green' => '226', 'blue' => '255', 'hex' => 'B0E2FF' ),
		'642' => array( 'name' => 'lightskyblue2', 'red' => '164', 'green' => '211', 'blue' => '238', 'hex' => 'A4D3EE' ),
		'643' => array( 'name' => 'lightskyblue3', 'red' => '141', 'green' => '182', 'blue' => '205', 'hex' => '8DB6CD' ),
		'644' => array( 'name' => 'lightskyblue4', 'red' => '96', 'green' => '123', 'blue' => '139', 'hex' => '607B8B' ),
		'645' => array( 'name' => 'lightslateblue', 'red' => '132', 'green' => '112', 'blue' => '255', 'hex' => '8470FF' ),
		'646' => array( 'name' => 'lightslatebluea', 'red' => '153', 'green' => '204', 'blue' => '255', 'hex' => '99CCFF' ),
		'647' => array( 'name' => 'lightslategray', 'red' => '119', 'green' => '136', 'blue' => '153', 'hex' => '778899' ),
		'648' => array( 'name' => 'lightslategrey', 'red' => '119', 'green' => '136', 'blue' => '153', 'hex' => '778899' ),
		'649' => array( 'name' => 'lightsteelblue', 'red' => '176', 'green' => '196', 'blue' => '222', 'hex' => 'B0C4DE' ),
		'650' => array( 'name' => 'lightsteelblue1', 'red' => '202', 'green' => '225', 'blue' => '255', 'hex' => 'CAE1FF' ),
		'651' => array( 'name' => 'lightsteelblue2', 'red' => '188', 'green' => '210', 'blue' => '238', 'hex' => 'BCD2EE' ),
		'652' => array( 'name' => 'lightsteelblue3', 'red' => '162', 'green' => '181', 'blue' => '205', 'hex' => 'A2B5CD' ),
		'653' => array( 'name' => 'lightsteelblue4', 'red' => '110', 'green' => '123', 'blue' => '139', 'hex' => '6E7B8B' ),
		'654' => array( 'name' => 'lightyellow', 'red' => '255', 'green' => '255', 'blue' => '224', 'hex' => 'FFFFE0' ),
		'655' => array( 'name' => 'lightyellow1', 'red' => '255', 'green' => '255', 'blue' => '224', 'hex' => 'FFFFE0' ),
		'656' => array( 'name' => 'lightyellow1 (lightyellow)', 'red' => '255', 'green' => '255', 'blue' => '224', 'hex' => 'FFFFE0' ),
		'657' => array( 'name' => 'lightyellow2', 'red' => '238', 'green' => '238', 'blue' => '209', 'hex' => 'EEEED1' ),
		'658' => array( 'name' => 'lightyellow3', 'red' => '205', 'green' => '205', 'blue' => '180', 'hex' => 'CDCDB4' ),
		'659' => array( 'name' => 'lightyellow4', 'red' => '139', 'green' => '139', 'blue' => '122', 'hex' => '8B8B7A' ),
		'660' => array( 'name' => 'lime', 'red' => '0', 'green' => '255', 'blue' => '0', 'hex' => '00FF00' ),
		'661' => array( 'name' => 'lime green', 'red' => '50', 'green' => '205', 'blue' => '50', 'hex' => '32CD32' ),
		'662' => array( 'name' => 'limegreen', 'red' => '50', 'green' => '205', 'blue' => '50', 'hex' => '32CD32' ),
		'663' => array( 'name' => 'linen', 'red' => '250', 'green' => '240', 'blue' => '230', 'hex' => 'FAF0E6' ),
		'664' => array( 'name' => 'lt goldenrod yellow', 'red' => '250', 'green' => '250', 'blue' => '210', 'hex' => 'FAFAD2' ),
		'665' => array( 'name' => 'magenta', 'red' => '255', 'green' => '0', 'blue' => '255', 'hex' => 'FF00FF' ),
		'666' => array( 'name' => 'magenta(fuchsia*)', 'red' => '255', 'green' => '0', 'blue' => '255', 'hex' => 'FF00FF' ),
		'667' => array( 'name' => 'magenta1', 'red' => '255', 'green' => '0', 'blue' => '255', 'hex' => 'FF00FF' ),
		'668' => array( 'name' => 'magenta2', 'red' => '238', 'green' => '0', 'blue' => '238', 'hex' => 'EE00EE' ),
		'669' => array( 'name' => 'magenta3', 'red' => '205', 'green' => '0', 'blue' => '205', 'hex' => 'CD00CD' ),
		'670' => array( 'name' => 'magenta4', 'red' => '139', 'green' => '0', 'blue' => '139', 'hex' => '8B008B' ),
		'671' => array( 'name' => 'magenta4 (darkmagenta)', 'red' => '139', 'green' => '0', 'blue' => '139', 'hex' => '8B008B' ),
		'672' => array( 'name' => 'mandarian orange', 'red' => '142', 'green' => '35', 'blue' => '35', 'hex' => '8E2323' ),
		'673' => array( 'name' => 'manganeseblue', 'red' => '3', 'green' => '168', 'blue' => '158', 'hex' => '03A89E' ),
		'674' => array( 'name' => 'maroon', 'red' => '245', 'green' => '204', 'blue' => '176', 'hex' => 'F5CCB0' ),
		'675' => array( 'name' => 'maroon*', 'red' => '128', 'green' => '0', 'blue' => '0', 'hex' => '800000' ),
		'676' => array( 'name' => 'maroon1', 'red' => '255', 'green' => '52', 'blue' => '179', 'hex' => 'FF34B3' ),
		'677' => array( 'name' => 'maroon2', 'red' => '238', 'green' => '48', 'blue' => '167', 'hex' => 'EE30A7' ),
		'678' => array( 'name' => 'maroon3', 'red' => '205', 'green' => '41', 'blue' => '144', 'hex' => 'CD2990' ),
		'679' => array( 'name' => 'maroon4', 'red' => '139', 'green' => '28', 'blue' => '98', 'hex' => '8B1C62' ),
		'680' => array( 'name' => 'med spring green', 'red' => '0', 'green' => '250', 'blue' => '154', 'hex' => '00FA9A' ),
		'681' => array( 'name' => 'medium aquamarine', 'red' => '102', 'green' => '205', 'blue' => '170', 'hex' => '66CDAA' ),
		'682' => array( 'name' => 'medium blue', 'red' => '205', 'green' => '127', 'blue' => '50', 'hex' => 'CD7F32' ),
		'683' => array( 'name' => 'medium forest green', 'red' => '219', 'green' => '219', 'blue' => '112', 'hex' => 'DBDB70' ),
		'684' => array( 'name' => 'medium goldenrod', 'red' => '234', 'green' => '234', 'blue' => '174', 'hex' => 'EAEAAE' ),
		'685' => array( 'name' => 'medium orchid', 'red' => '186', 'green' => '85', 'blue' => '211', 'hex' => 'BA55D3' ),
		'686' => array( 'name' => 'medium orchid1', 'red' => '224', 'green' => '102', 'blue' => '255', 'hex' => 'E066FF' ),
		'687' => array( 'name' => 'medium orchid2', 'red' => '209', 'green' => '95', 'blue' => '238', 'hex' => 'D15FEE' ),
		'688' => array( 'name' => 'medium orchid3', 'red' => '180', 'green' => '82', 'blue' => '205', 'hex' => 'B452CD' ),
		'689' => array( 'name' => 'medium orchid4', 'red' => '122', 'green' => '55', 'blue' => '139', 'hex' => '7A378B' ),
		'690' => array( 'name' => 'medium purple', 'red' => '147', 'green' => '112', 'blue' => '219', 'hex' => '9370DB' ),
		'691' => array( 'name' => 'medium purple1', 'red' => '171', 'green' => '130', 'blue' => '255', 'hex' => 'AB82FF' ),
		'692' => array( 'name' => 'medium purple2', 'red' => '159', 'green' => '121', 'blue' => '238', 'hex' => '9F79EE' ),
		'693' => array( 'name' => 'medium purple3', 'red' => '137', 'green' => '104', 'blue' => '205', 'hex' => '8968CD' ),
		'694' => array( 'name' => 'medium purple4', 'red' => '93', 'green' => '71', 'blue' => '139', 'hex' => '5D478B' ),
		'695' => array( 'name' => 'medium sea green', 'red' => '60', 'green' => '179', 'blue' => '113', 'hex' => '3CB371' ),
		'696' => array( 'name' => 'medium slate blue', 'red' => '127', 'green' => '0', 'blue' => '255', 'hex' => '7F00FF' ),
		'697' => array( 'name' => 'medium spring green', 'red' => '127', 'green' => '255', 'blue' => '0', 'hex' => '7FFF00' ),
		'698' => array( 'name' => 'medium turquoise', 'red' => '72', 'green' => '209', 'blue' => '204', 'hex' => '48D1CC' ),
		'699' => array( 'name' => 'medium violet red', 'red' => '199', 'green' => '21', 'blue' => '133', 'hex' => 'C71585' ),
		'700' => array( 'name' => 'medium wood', 'red' => '166', 'green' => '128', 'blue' => '100', 'hex' => 'A68064' ),
		'701' => array( 'name' => 'mediumblue', 'red' => '0', 'green' => '0', 'blue' => '205', 'hex' => '0000CD' ),
		'702' => array( 'name' => 'mediumorchid', 'red' => '186', 'green' => '85', 'blue' => '211', 'hex' => 'BA55D3' ),
		'703' => array( 'name' => 'mediumorchid1', 'red' => '224', 'green' => '102', 'blue' => '255', 'hex' => 'E066FF' ),
		'704' => array( 'name' => 'mediumorchid2', 'red' => '209', 'green' => '95', 'blue' => '238', 'hex' => 'D15FEE' ),
		'705' => array( 'name' => 'mediumorchid3', 'red' => '180', 'green' => '82', 'blue' => '205', 'hex' => 'B452CD' ),
		'706' => array( 'name' => 'mediumorchid4', 'red' => '122', 'green' => '55', 'blue' => '139', 'hex' => '7A378B' ),
		'707' => array( 'name' => 'mediumpurple', 'red' => '147', 'green' => '112', 'blue' => '219', 'hex' => '9370DB' ),
		'708' => array( 'name' => 'mediumpurple1', 'red' => '171', 'green' => '130', 'blue' => '255', 'hex' => 'AB82FF' ),
		'709' => array( 'name' => 'mediumpurple2', 'red' => '159', 'green' => '121', 'blue' => '238', 'hex' => '9F79EE' ),
		'710' => array( 'name' => 'mediumpurple3', 'red' => '137', 'green' => '104', 'blue' => '205', 'hex' => '8968CD' ),
		'711' => array( 'name' => 'mediumpurple4', 'red' => '93', 'green' => '71', 'blue' => '139', 'hex' => '5D478B' ),
		'712' => array( 'name' => 'mediumseagreen', 'red' => '60', 'green' => '179', 'blue' => '113', 'hex' => '3CB371' ),
		'713' => array( 'name' => 'mediumslateblue', 'red' => '123', 'green' => '104', 'blue' => '238', 'hex' => '7B68EE' ),
		'714' => array( 'name' => 'mediumspringgreen', 'red' => '0', 'green' => '250', 'blue' => '154', 'hex' => '00FA9A' ),
		'715' => array( 'name' => 'mediumturquoise', 'red' => '72', 'green' => '209', 'blue' => '204', 'hex' => '48D1CC' ),
		'716' => array( 'name' => 'mediumvioletred', 'red' => '199', 'green' => '21', 'blue' => '133', 'hex' => 'C71585' ),
		'717' => array( 'name' => 'melon', 'red' => '227', 'green' => '168', 'blue' => '105', 'hex' => 'E3A869' ),
		'718' => array( 'name' => 'midnight blue', 'red' => '47', 'green' => '47', 'blue' => '79', 'hex' => '2F2F4F' ),
		'719' => array( 'name' => 'midnightblue', 'red' => '25', 'green' => '25', 'blue' => '112', 'hex' => '191970' ),
		'720' => array( 'name' => 'mint', 'red' => '189', 'green' => '252', 'blue' => '201', 'hex' => 'BDFCC9' ),
		'721' => array( 'name' => 'mint cream', 'red' => '245', 'green' => '255', 'blue' => '250', 'hex' => 'F5FFFA' ),
		'722' => array( 'name' => 'mintcream', 'red' => '245', 'green' => '255', 'blue' => '250', 'hex' => 'F5FFFA' ),
		'723' => array( 'name' => 'misty rose', 'red' => '255', 'green' => '228', 'blue' => '225', 'hex' => 'FFE4E1' ),
		'724' => array( 'name' => 'misty rose1', 'red' => '255', 'green' => '228', 'blue' => '225', 'hex' => 'FFE4E1' ),
		'725' => array( 'name' => 'misty rose2', 'red' => '238', 'green' => '213', 'blue' => '210', 'hex' => 'EED5D2' ),
		'726' => array( 'name' => 'misty rose3', 'red' => '205', 'green' => '183', 'blue' => '181', 'hex' => 'CDB7B5' ),
		'727' => array( 'name' => 'misty rose4', 'red' => '139', 'green' => '125', 'blue' => '123', 'hex' => '8B7D7B' ),
		'728' => array( 'name' => 'mistyrose', 'red' => '255', 'green' => '228', 'blue' => '225', 'hex' => 'FFE4E1' ),
		'729' => array( 'name' => 'mistyrose1', 'red' => '255', 'green' => '228', 'blue' => '225', 'hex' => 'FFE4E1' ),
		'730' => array( 'name' => 'mistyrose1 (mistyrose)', 'red' => '255', 'green' => '228', 'blue' => '225', 'hex' => 'FFE4E1' ),
		'731' => array( 'name' => 'mistyrose2', 'red' => '238', 'green' => '213', 'blue' => '210', 'hex' => 'EED5D2' ),
		'732' => array( 'name' => 'mistyrose3', 'red' => '205', 'green' => '183', 'blue' => '181', 'hex' => 'CDB7B5' ),
		'733' => array( 'name' => 'mistyrose4', 'red' => '139', 'green' => '125', 'blue' => '123', 'hex' => '8B7D7B' ),
		'734' => array( 'name' => 'moccasin', 'red' => '255', 'green' => '228', 'blue' => '181', 'hex' => 'FFE4B5' ),
		'735' => array( 'name' => 'navajo white', 'red' => '255', 'green' => '222', 'blue' => '173', 'hex' => 'FFDEAD' ),
		'736' => array( 'name' => 'navajo white1', 'red' => '255', 'green' => '222', 'blue' => '173', 'hex' => 'FFDEAD' ),
		'737' => array( 'name' => 'navajo white2', 'red' => '238', 'green' => '207', 'blue' => '161', 'hex' => 'EECFA1' ),
		'738' => array( 'name' => 'navajo white3', 'red' => '205', 'green' => '179', 'blue' => '139', 'hex' => 'CDB38B' ),
		'739' => array( 'name' => 'navajo white4', 'red' => '139', 'green' => '121', 'blue' => '94', 'hex' => '8B795E' ),
		'740' => array( 'name' => 'navajowhite', 'red' => '255', 'green' => '222', 'blue' => '173', 'hex' => 'FFDEAD' ),
		'741' => array( 'name' => 'navajowhite1', 'red' => '255', 'green' => '222', 'blue' => '173', 'hex' => 'FFDEAD' ),
		'742' => array( 'name' => 'navajowhite1 (navajowhite)', 'red' => '255', 'green' => '222', 'blue' => '173', 'hex' => 'FFDEAD' ),
		'743' => array( 'name' => 'navajowhite2', 'red' => '238', 'green' => '207', 'blue' => '161', 'hex' => 'EECFA1' ),
		'744' => array( 'name' => 'navajowhite3', 'red' => '205', 'green' => '179', 'blue' => '139', 'hex' => 'CDB38B' ),
		'745' => array( 'name' => 'navajowhite4', 'red' => '139', 'green' => '121', 'blue' => '94', 'hex' => '8B795E' ),
		'746' => array( 'name' => 'navy', 'red' => '0', 'green' => '0', 'blue' => '128', 'hex' => '000080' ),
		'747' => array( 'name' => 'navy blue', 'red' => '35', 'green' => '35', 'blue' => '142', 'hex' => '23238E' ),
		'748' => array( 'name' => 'navy*', 'red' => '0', 'green' => '0', 'blue' => '128', 'hex' => '000080' ),
		'749' => array( 'name' => 'navyblue', 'red' => '0', 'green' => '0', 'blue' => '128', 'hex' => '000080' ),
		'750' => array( 'name' => 'neon blue', 'red' => '77', 'green' => '77', 'blue' => '255', 'hex' => '4D4DFF' ),
		'751' => array( 'name' => 'neon pink', 'red' => '255', 'green' => '110', 'blue' => '199', 'hex' => 'FF6EC7' ),
		'752' => array( 'name' => 'new midnight blue', 'red' => '0', 'green' => '0', 'blue' => '156', 'hex' => '00009C' ),
		'753' => array( 'name' => 'new tan', 'red' => '235', 'green' => '199', 'blue' => '158', 'hex' => 'EBC79E' ),
		'754' => array( 'name' => 'old gold', 'red' => '207', 'green' => '181', 'blue' => '59', 'hex' => 'CFB53B' ),
		'755' => array( 'name' => 'old lace', 'red' => '253', 'green' => '245', 'blue' => '230', 'hex' => 'FDF5E6' ),
		'756' => array( 'name' => 'oldlace', 'red' => '253', 'green' => '245', 'blue' => '230', 'hex' => 'FDF5E6' ),
		'757' => array( 'name' => 'olive', 'red' => '128', 'green' => '128', 'blue' => '0', 'hex' => '808000' ),
		'758' => array( 'name' => 'olive drab', 'red' => '107', 'green' => '142', 'blue' => '35', 'hex' => '6B8E23' ),
		'759' => array( 'name' => 'olive drab1', 'red' => '192', 'green' => '255', 'blue' => '62', 'hex' => 'C0FF3E' ),
		'760' => array( 'name' => 'olive drab2', 'red' => '179', 'green' => '238', 'blue' => '58', 'hex' => 'B3EE3A' ),
		'761' => array( 'name' => 'olive drab3', 'red' => '154', 'green' => '205', 'blue' => '50', 'hex' => '9ACD32' ),
		'762' => array( 'name' => 'olive drab4', 'red' => '105', 'green' => '139', 'blue' => '34', 'hex' => '698B22' ),
		'763' => array( 'name' => 'olive*', 'red' => '128', 'green' => '128', 'blue' => '0', 'hex' => '808000' ),
		'764' => array( 'name' => 'olivedrab', 'red' => '107', 'green' => '142', 'blue' => '35', 'hex' => '6B8E23' ),
		'765' => array( 'name' => 'olivedrab1', 'red' => '192', 'green' => '255', 'blue' => '62', 'hex' => 'C0FF3E' ),
		'766' => array( 'name' => 'olivedrab2', 'red' => '179', 'green' => '238', 'blue' => '58', 'hex' => 'B3EE3A' ),
		'767' => array( 'name' => 'olivedrab3', 'red' => '154', 'green' => '205', 'blue' => '50', 'hex' => '9ACD32' ),
		'768' => array( 'name' => 'olivedrab3 (yellowgreen)', 'red' => '154', 'green' => '205', 'blue' => '50', 'hex' => '9ACD32' ),
		'769' => array( 'name' => 'olivedrab4', 'red' => '105', 'green' => '139', 'blue' => '34', 'hex' => '698B22' ),
		'770' => array( 'name' => 'orange', 'red' => '255', 'green' => '127', 'blue' => '0', 'hex' => 'FF7F00' ),
		'771' => array( 'name' => 'orange red', 'red' => '255', 'green' => '69', 'blue' => '0', 'hex' => 'FF4500' ),
		'772' => array( 'name' => 'orange red1', 'red' => '255', 'green' => '69', 'blue' => '0', 'hex' => 'FF4500' ),
		'773' => array( 'name' => 'orange red2', 'red' => '238', 'green' => '64', 'blue' => '0', 'hex' => 'EE4000' ),
		'774' => array( 'name' => 'orange red3', 'red' => '205', 'green' => '55', 'blue' => '0', 'hex' => 'CD3700' ),
		'775' => array( 'name' => 'orange red4', 'red' => '139', 'green' => '37', 'blue' => '0', 'hex' => '8B2500' ),
		'776' => array( 'name' => 'orange1', 'red' => '255', 'green' => '165', 'blue' => '0', 'hex' => 'FFA500' ),
		'777' => array( 'name' => 'orange1 (orange)', 'red' => '255', 'green' => '165', 'blue' => '0', 'hex' => 'FFA500' ),
		'778' => array( 'name' => 'orange2', 'red' => '238', 'green' => '154', 'blue' => '0', 'hex' => 'EE9A00' ),
		'779' => array( 'name' => 'orange3', 'red' => '205', 'green' => '133', 'blue' => '0', 'hex' => 'CD8500' ),
		'780' => array( 'name' => 'orange4', 'red' => '139', 'green' => '90', 'blue' => '0', 'hex' => '8B5A00' ),
		'781' => array( 'name' => 'orangered', 'red' => '255', 'green' => '69', 'blue' => '0', 'hex' => 'FF4500' ),
		'782' => array( 'name' => 'orangered1', 'red' => '255', 'green' => '69', 'blue' => '0', 'hex' => 'FF4500' ),
		'783' => array( 'name' => 'orangered1 (orangered)', 'red' => '255', 'green' => '69', 'blue' => '0', 'hex' => 'FF4500' ),
		'784' => array( 'name' => 'orangered2', 'red' => '238', 'green' => '64', 'blue' => '0', 'hex' => 'EE4000' ),
		'785' => array( 'name' => 'orangered3', 'red' => '205', 'green' => '55', 'blue' => '0', 'hex' => 'CD3700' ),
		'786' => array( 'name' => 'orangered4', 'red' => '139', 'green' => '37', 'blue' => '0', 'hex' => '8B2500' ),
		'787' => array( 'name' => 'orchid', 'red' => '218', 'green' => '112', 'blue' => '214', 'hex' => 'DA70D6' ),
		'788' => array( 'name' => 'orchid1', 'red' => '255', 'green' => '131', 'blue' => '250', 'hex' => 'FF83FA' ),
		'789' => array( 'name' => 'orchid2', 'red' => '238', 'green' => '122', 'blue' => '233', 'hex' => 'EE7AE9' ),
		'790' => array( 'name' => 'orchid3', 'red' => '205', 'green' => '105', 'blue' => '201', 'hex' => 'CD69C9' ),
		'791' => array( 'name' => 'orchid4', 'red' => '139', 'green' => '71', 'blue' => '137', 'hex' => '8B4789' ),
		'792' => array( 'name' => 'pale goldenrod', 'red' => '238', 'green' => '232', 'blue' => '170', 'hex' => 'EEE8AA' ),
		'793' => array( 'name' => 'pale green', 'red' => '152', 'green' => '251', 'blue' => '152', 'hex' => '98FB98' ),
		'794' => array( 'name' => 'pale green1', 'red' => '154', 'green' => '255', 'blue' => '154', 'hex' => '9AFF9A' ),
		'795' => array( 'name' => 'pale green2', 'red' => '144', 'green' => '238', 'blue' => '144', 'hex' => '90EE90' ),
		'796' => array( 'name' => 'pale green3', 'red' => '124', 'green' => '205', 'blue' => '124', 'hex' => '7CCD7C' ),
		'797' => array( 'name' => 'pale green4', 'red' => '84', 'green' => '139', 'blue' => '84', 'hex' => '548B54' ),
		'798' => array( 'name' => 'pale turquoise', 'red' => '175', 'green' => '238', 'blue' => '238', 'hex' => 'AFEEEE' ),
		'799' => array( 'name' => 'pale turquoise1', 'red' => '187', 'green' => '255', 'blue' => '255', 'hex' => 'BBFFFF' ),
		'800' => array( 'name' => 'pale turquoise2', 'red' => '174', 'green' => '238', 'blue' => '238', 'hex' => 'AEEEEE' ),
		'801' => array( 'name' => 'pale turquoise3', 'red' => '150', 'green' => '205', 'blue' => '205', 'hex' => '96CDCD' ),
		'802' => array( 'name' => 'pale turquoise4', 'red' => '102', 'green' => '139', 'blue' => '139', 'hex' => '668B8B' ),
		'803' => array( 'name' => 'pale violet red', 'red' => '219', 'green' => '112', 'blue' => '147', 'hex' => 'DB7093' ),
		'804' => array( 'name' => 'pale violet red1', 'red' => '255', 'green' => '130', 'blue' => '171', 'hex' => 'FF82AB' ),
		'805' => array( 'name' => 'pale violet red2', 'red' => '238', 'green' => '121', 'blue' => '159', 'hex' => 'EE799F' ),
		'806' => array( 'name' => 'pale violet red3', 'red' => '205', 'green' => '104', 'blue' => '137', 'hex' => 'CD6889' ),
		'807' => array( 'name' => 'pale violet red4', 'red' => '139', 'green' => '71', 'blue' => '93', 'hex' => '8B475D' ),
		'808' => array( 'name' => 'palegoldenrod', 'red' => '238', 'green' => '232', 'blue' => '170', 'hex' => 'EEE8AA' ),
		'809' => array( 'name' => 'palegreen', 'red' => '152', 'green' => '251', 'blue' => '152', 'hex' => '98FB98' ),
		'810' => array( 'name' => 'palegreen1', 'red' => '154', 'green' => '255', 'blue' => '154', 'hex' => '9AFF9A' ),
		'811' => array( 'name' => 'palegreen2', 'red' => '144', 'green' => '238', 'blue' => '144', 'hex' => '90EE90' ),
		'812' => array( 'name' => 'palegreen2 (lightgreen)', 'red' => '144', 'green' => '238', 'blue' => '144', 'hex' => '90EE90' ),
		'813' => array( 'name' => 'palegreen3', 'red' => '124', 'green' => '205', 'blue' => '124', 'hex' => '7CCD7C' ),
		'814' => array( 'name' => 'palegreen4', 'red' => '84', 'green' => '139', 'blue' => '84', 'hex' => '548B54' ),
		'815' => array( 'name' => 'paleturquoise', 'red' => '175', 'green' => '238', 'blue' => '238', 'hex' => 'AFEEEE' ),
		'816' => array( 'name' => 'paleturquoise1', 'red' => '187', 'green' => '255', 'blue' => '255', 'hex' => 'BBFFFF' ),
		'817' => array( 'name' => 'paleturquoise2', 'red' => '174', 'green' => '238', 'blue' => '238', 'hex' => 'AEEEEE' ),
		'818' => array( 'name' => 'paleturquoise2 (paleturquoise)', 'red' => '174', 'green' => '238', 'blue' => '238', 'hex' => 'AEEEEE' ),
		'819' => array( 'name' => 'paleturquoise3', 'red' => '150', 'green' => '205', 'blue' => '205', 'hex' => '96CDCD' ),
		'820' => array( 'name' => 'paleturquoise4', 'red' => '102', 'green' => '139', 'blue' => '139', 'hex' => '668B8B' ),
		'821' => array( 'name' => 'palevioletred', 'red' => '219', 'green' => '112', 'blue' => '147', 'hex' => 'DB7093' ),
		'822' => array( 'name' => 'palevioletred1', 'red' => '255', 'green' => '130', 'blue' => '171', 'hex' => 'FF82AB' ),
		'823' => array( 'name' => 'palevioletred2', 'red' => '238', 'green' => '121', 'blue' => '159', 'hex' => 'EE799F' ),
		'824' => array( 'name' => 'palevioletred3', 'red' => '205', 'green' => '104', 'blue' => '137', 'hex' => 'CD6889' ),
		'825' => array( 'name' => 'palevioletred4', 'red' => '139', 'green' => '71', 'blue' => '93', 'hex' => '8B475D' ),
		'826' => array( 'name' => 'papaya whip', 'red' => '255', 'green' => '239', 'blue' => '213', 'hex' => 'FFEFD5' ),
		'827' => array( 'name' => 'papayawhip', 'red' => '255', 'green' => '239', 'blue' => '213', 'hex' => 'FFEFD5' ),
		'828' => array( 'name' => 'peach puff', 'red' => '255', 'green' => '218', 'blue' => '185', 'hex' => 'FFDAB9' ),
		'829' => array( 'name' => 'peach puff1', 'red' => '255', 'green' => '218', 'blue' => '185', 'hex' => 'FFDAB9' ),
		'830' => array( 'name' => 'peach puff2', 'red' => '238', 'green' => '203', 'blue' => '173', 'hex' => 'EECBAD' ),
		'831' => array( 'name' => 'peach puff3', 'red' => '205', 'green' => '175', 'blue' => '149', 'hex' => 'CDAF95' ),
		'832' => array( 'name' => 'peach puff4', 'red' => '139', 'green' => '119', 'blue' => '101', 'hex' => '8B7765' ),
		'833' => array( 'name' => 'peachpuff', 'red' => '255', 'green' => '218', 'blue' => '185', 'hex' => 'FFDAB9' ),
		'834' => array( 'name' => 'peachpuff1', 'red' => '255', 'green' => '218', 'blue' => '185', 'hex' => 'FFDAB9' ),
		'835' => array( 'name' => 'peachpuff1 (peachpuff)', 'red' => '255', 'green' => '218', 'blue' => '185', 'hex' => 'FFDAB9' ),
		'836' => array( 'name' => 'peachpuff2', 'red' => '238', 'green' => '203', 'blue' => '173', 'hex' => 'EECBAD' ),
		'837' => array( 'name' => 'peachpuff3', 'red' => '205', 'green' => '175', 'blue' => '149', 'hex' => 'CDAF95' ),
		'838' => array( 'name' => 'peachpuff4', 'red' => '139', 'green' => '119', 'blue' => '101', 'hex' => '8B7765' ),
		'839' => array( 'name' => 'peacock', 'red' => '51', 'green' => '161', 'blue' => '201', 'hex' => '33A1C9' ),
		'840' => array( 'name' => 'peru', 'red' => '205', 'green' => '133', 'blue' => '63', 'hex' => 'CD853F' ),
		'841' => array( 'name' => 'pink', 'red' => '255', 'green' => '192', 'blue' => '203', 'hex' => 'FFC0CB' ),
		'842' => array( 'name' => 'pink1', 'red' => '255', 'green' => '181', 'blue' => '197', 'hex' => 'FFB5C5' ),
		'843' => array( 'name' => 'pink2', 'red' => '238', 'green' => '169', 'blue' => '184', 'hex' => 'EEA9B8' ),
		'844' => array( 'name' => 'pink3', 'red' => '205', 'green' => '145', 'blue' => '158', 'hex' => 'CD919E' ),
		'845' => array( 'name' => 'pink4', 'red' => '139', 'green' => '99', 'blue' => '108', 'hex' => '8B636C' ),
		'846' => array( 'name' => 'plum', 'red' => '221', 'green' => '160', 'blue' => '221', 'hex' => 'DDA0DD' ),
		'847' => array( 'name' => 'plum1', 'red' => '255', 'green' => '187', 'blue' => '255', 'hex' => 'FFBBFF' ),
		'848' => array( 'name' => 'plum2', 'red' => '238', 'green' => '174', 'blue' => '238', 'hex' => 'EEAEEE' ),
		'849' => array( 'name' => 'plum3', 'red' => '205', 'green' => '150', 'blue' => '205', 'hex' => 'CD96CD' ),
		'850' => array( 'name' => 'plum4', 'red' => '139', 'green' => '102', 'blue' => '139', 'hex' => '8B668B' ),
		'851' => array( 'name' => 'plum5', 'red' => '153', 'green' => '102', 'blue' => '204', 'hex' => '9966CC' ),
		'852' => array( 'name' => 'powder blue', 'red' => '176', 'green' => '224', 'blue' => '230', 'hex' => 'B0E0E6' ),
		'853' => array( 'name' => 'powderblue', 'red' => '176', 'green' => '224', 'blue' => '230', 'hex' => 'B0E0E6' ),
		'854' => array( 'name' => 'purple', 'red' => '160', 'green' => '32', 'blue' => '240', 'hex' => 'A020F0' ),
		'855' => array( 'name' => 'purple*', 'red' => '128', 'green' => '0', 'blue' => '128', 'hex' => '800080' ),
		'856' => array( 'name' => 'purple1', 'red' => '155', 'green' => '48', 'blue' => '255', 'hex' => '9B30FF' ),
		'857' => array( 'name' => 'purple2', 'red' => '145', 'green' => '44', 'blue' => '238', 'hex' => '912CEE' ),
		'858' => array( 'name' => 'purple3', 'red' => '125', 'green' => '38', 'blue' => '205', 'hex' => '7D26CD' ),
		'859' => array( 'name' => 'purple4', 'red' => '85', 'green' => '26', 'blue' => '139', 'hex' => '551A8B' ),
		'860' => array( 'name' => 'quartz', 'red' => '217', 'green' => '217', 'blue' => '243', 'hex' => 'D9D9F3' ),
		'861' => array( 'name' => 'raspberry', 'red' => '135', 'green' => '38', 'blue' => '87', 'hex' => '872657' ),
		'862' => array( 'name' => 'rawsienna', 'red' => '199', 'green' => '97', 'blue' => '20', 'hex' => 'C76114' ),
		'863' => array( 'name' => 'red', 'red' => '255', 'green' => '0', 'blue' => '0', 'hex' => 'FF0000' ),
		'864' => array( 'name' => 'red1', 'red' => '255', 'green' => '0', 'blue' => '0', 'hex' => 'FF0000' ),
		'865' => array( 'name' => 'red1 (red*)', 'red' => '255', 'green' => '0', 'blue' => '0', 'hex' => 'FF0000' ),
		'866' => array( 'name' => 'red2', 'red' => '238', 'green' => '0', 'blue' => '0', 'hex' => 'EE0000' ),
		'867' => array( 'name' => 'red3', 'red' => '205', 'green' => '0', 'blue' => '0', 'hex' => 'CD0000' ),
		'868' => array( 'name' => 'red4', 'red' => '139', 'green' => '0', 'blue' => '0', 'hex' => '8B0000' ),
		'869' => array( 'name' => 'red4 (darkred)', 'red' => '139', 'green' => '0', 'blue' => '0', 'hex' => '8B0000' ),
		'870' => array( 'name' => 'rich blue', 'red' => '89', 'green' => '89', 'blue' => '171', 'hex' => '5959AB' ),
		'871' => array( 'name' => 'rosy brown', 'red' => '188', 'green' => '143', 'blue' => '143', 'hex' => 'BC8F8F' ),
		'872' => array( 'name' => 'rosy brown1', 'red' => '255', 'green' => '193', 'blue' => '193', 'hex' => 'FFC1C1' ),
		'873' => array( 'name' => 'rosy brown2', 'red' => '238', 'green' => '180', 'blue' => '180', 'hex' => 'EEB4B4' ),
		'874' => array( 'name' => 'rosy brown3', 'red' => '205', 'green' => '155', 'blue' => '155', 'hex' => 'CD9B9B' ),
		'875' => array( 'name' => 'rosy brown4', 'red' => '139', 'green' => '105', 'blue' => '105', 'hex' => '8B6969' ),
		'876' => array( 'name' => 'rosybrown', 'red' => '188', 'green' => '143', 'blue' => '143', 'hex' => 'BC8F8F' ),
		'877' => array( 'name' => 'rosybrown1', 'red' => '255', 'green' => '193', 'blue' => '193', 'hex' => 'FFC1C1' ),
		'878' => array( 'name' => 'rosybrown2', 'red' => '238', 'green' => '180', 'blue' => '180', 'hex' => 'EEB4B4' ),
		'879' => array( 'name' => 'rosybrown3', 'red' => '205', 'green' => '155', 'blue' => '155', 'hex' => 'CD9B9B' ),
		'880' => array( 'name' => 'rosybrown4', 'red' => '139', 'green' => '105', 'blue' => '105', 'hex' => '8B6969' ),
		'881' => array( 'name' => 'royal blue', 'red' => '65', 'green' => '105', 'blue' => '225', 'hex' => '4169E1' ),
		'882' => array( 'name' => 'royal blue1', 'red' => '72', 'green' => '118', 'blue' => '255', 'hex' => '4876FF' ),
		'883' => array( 'name' => 'royal blue2', 'red' => '67', 'green' => '110', 'blue' => '238', 'hex' => '436EEE' ),
		'884' => array( 'name' => 'royal blue3', 'red' => '58', 'green' => '95', 'blue' => '205', 'hex' => '3A5FCD' ),
		'885' => array( 'name' => 'royal blue4', 'red' => '39', 'green' => '64', 'blue' => '139', 'hex' => '27408B' ),
		'886' => array( 'name' => 'royalblue', 'red' => '65', 'green' => '105', 'blue' => '225', 'hex' => '4169E1' ),
		'887' => array( 'name' => 'royalblue1', 'red' => '72', 'green' => '118', 'blue' => '255', 'hex' => '4876FF' ),
		'888' => array( 'name' => 'royalblue2', 'red' => '67', 'green' => '110', 'blue' => '238', 'hex' => '436EEE' ),
		'889' => array( 'name' => 'royalblue3', 'red' => '58', 'green' => '95', 'blue' => '205', 'hex' => '3A5FCD' ),
		'890' => array( 'name' => 'royalblue4', 'red' => '39', 'green' => '64', 'blue' => '139', 'hex' => '27408B' ),
		'891' => array( 'name' => 'royalblue5', 'red' => '0', 'green' => '34', 'blue' => '102', 'hex' => '002266' ),
		'892' => array( 'name' => 'saddle brown', 'red' => '139', 'green' => '69', 'blue' => '19', 'hex' => '8B4513' ),
		'893' => array( 'name' => 'saddlebrown', 'red' => '139', 'green' => '69', 'blue' => '19', 'hex' => '8B4513' ),
		'894' => array( 'name' => 'salmon', 'red' => '250', 'green' => '128', 'blue' => '114', 'hex' => 'FA8072' ),
		'895' => array( 'name' => 'salmon1', 'red' => '255', 'green' => '140', 'blue' => '105', 'hex' => 'FF8C69' ),
		'896' => array( 'name' => 'salmon2', 'red' => '238', 'green' => '130', 'blue' => '98', 'hex' => 'EE8262' ),
		'897' => array( 'name' => 'salmon3', 'red' => '205', 'green' => '112', 'blue' => '84', 'hex' => 'CD7054' ),
		'898' => array( 'name' => 'salmon4', 'red' => '139', 'green' => '76', 'blue' => '57', 'hex' => '8B4C39' ),
		'899' => array( 'name' => 'sandy brown', 'red' => '244', 'green' => '164', 'blue' => '96', 'hex' => 'F4A460' ),
		'900' => array( 'name' => 'sandybrown', 'red' => '244', 'green' => '164', 'blue' => '96', 'hex' => 'F4A460' ),
		'901' => array( 'name' => 'sapgreen', 'red' => '48', 'green' => '128', 'blue' => '20', 'hex' => '308014' ),
		'902' => array( 'name' => 'scarlet', 'red' => '140', 'green' => '23', 'blue' => '23', 'hex' => '8C1717' ),
		'903' => array( 'name' => 'sea green', 'red' => '46', 'green' => '139', 'blue' => '87', 'hex' => '2E8B57' ),
		'904' => array( 'name' => 'sea green1', 'red' => '84', 'green' => '255', 'blue' => '159', 'hex' => '54FF9F' ),
		'905' => array( 'name' => 'sea green2', 'red' => '78', 'green' => '238', 'blue' => '148', 'hex' => '4EEE94' ),
		'906' => array( 'name' => 'sea green3', 'red' => '67', 'green' => '205', 'blue' => '128', 'hex' => '43CD80' ),
		'907' => array( 'name' => 'sea green4', 'red' => '46', 'green' => '139', 'blue' => '87', 'hex' => '2E8B57' ),
		'908' => array( 'name' => 'seagreen, seagreen4', 'red' => '46', 'green' => '139', 'blue' => '87', 'hex' => '2E8B57' ),
		'909' => array( 'name' => 'seagreen1', 'red' => '84', 'green' => '255', 'blue' => '159', 'hex' => '54FF9F' ),
		'910' => array( 'name' => 'seagreen2', 'red' => '78', 'green' => '238', 'blue' => '148', 'hex' => '4EEE94' ),
		'911' => array( 'name' => 'seagreen3', 'red' => '67', 'green' => '205', 'blue' => '128', 'hex' => '43CD80' ),
		'912' => array( 'name' => 'seagreen4 (seagreen)', 'red' => '46', 'green' => '139', 'blue' => '87', 'hex' => '2E8B57' ),
		'913' => array( 'name' => 'seashell', 'red' => '255', 'green' => '245', 'blue' => '238', 'hex' => 'FFF5EE' ),
		'914' => array( 'name' => 'seashell1', 'red' => '255', 'green' => '245', 'blue' => '238', 'hex' => 'FFF5EE' ),
		'915' => array( 'name' => 'seashell1 (seashell)', 'red' => '255', 'green' => '245', 'blue' => '238', 'hex' => 'FFF5EE' ),
		'916' => array( 'name' => 'seashell2', 'red' => '238', 'green' => '229', 'blue' => '222', 'hex' => 'EEE5DE' ),
		'917' => array( 'name' => 'seashell3', 'red' => '205', 'green' => '197', 'blue' => '191', 'hex' => 'CDC5BF' ),
		'918' => array( 'name' => 'seashell4', 'red' => '139', 'green' => '134', 'blue' => '130', 'hex' => '8B8682' ),
		'919' => array( 'name' => 'semi-sweet chocolate', 'red' => '107', 'green' => '66', 'blue' => '38', 'hex' => '6B4226' ),
		'920' => array( 'name' => 'sepia', 'red' => '94', 'green' => '38', 'blue' => '18', 'hex' => '5E2612' ),
		'921' => array( 'name' => 'sgibeet', 'red' => '142', 'green' => '56', 'blue' => '142', 'hex' => '8E388E' ),
		'922' => array( 'name' => 'sgibrightgray', 'red' => '197', 'green' => '193', 'blue' => '170', 'hex' => 'C5C1AA' ),
		'923' => array( 'name' => 'sgichartreuse', 'red' => '113', 'green' => '198', 'blue' => '113', 'hex' => '71C671' ),
		'924' => array( 'name' => 'sgidarkgray', 'red' => '85', 'green' => '85', 'blue' => '85', 'hex' => '555555' ),
		'925' => array( 'name' => 'sgigray 12', 'red' => '30', 'green' => '30', 'blue' => '30', 'hex' => '1E1E1E' ),
		'926' => array( 'name' => 'sgigray 16', 'red' => '40', 'green' => '40', 'blue' => '40', 'hex' => '282828' ),
		'927' => array( 'name' => 'sgigray 32', 'red' => '81', 'green' => '81', 'blue' => '81', 'hex' => '515151' ),
		'928' => array( 'name' => 'sgigray 36', 'red' => '91', 'green' => '91', 'blue' => '91', 'hex' => '5B5B5B' ),
		'929' => array( 'name' => 'sgigray 52', 'red' => '132', 'green' => '132', 'blue' => '132', 'hex' => '848484' ),
		'930' => array( 'name' => 'sgigray 56', 'red' => '142', 'green' => '142', 'blue' => '142', 'hex' => '8E8E8E' ),
		'931' => array( 'name' => 'sgigray 72', 'red' => '183', 'green' => '183', 'blue' => '183', 'hex' => 'B7B7B7' ),
		'932' => array( 'name' => 'sgigray 76', 'red' => '193', 'green' => '193', 'blue' => '193', 'hex' => 'C1C1C1' ),
		'933' => array( 'name' => 'sgigray 92', 'red' => '234', 'green' => '234', 'blue' => '234', 'hex' => 'EAEAEA' ),
		'934' => array( 'name' => 'sgigray 96', 'red' => '244', 'green' => '244', 'blue' => '244', 'hex' => 'F4F4F4' ),
		'935' => array( 'name' => 'sgilightblue', 'red' => '125', 'green' => '158', 'blue' => '192', 'hex' => '7D9EC0' ),
		'936' => array( 'name' => 'sgilightgray', 'red' => '170', 'green' => '170', 'blue' => '170', 'hex' => 'AAAAAA' ),
		'937' => array( 'name' => 'sgiolivedrab', 'red' => '142', 'green' => '142', 'blue' => '56', 'hex' => '8E8E38' ),
		'938' => array( 'name' => 'sgisalmon', 'red' => '198', 'green' => '113', 'blue' => '113', 'hex' => 'C67171' ),
		'939' => array( 'name' => 'sgislateblue', 'red' => '113', 'green' => '113', 'blue' => '198', 'hex' => '7171C6' ),
		'940' => array( 'name' => 'sgiteal', 'red' => '56', 'green' => '142', 'blue' => '142', 'hex' => '388E8E' ),
		'941' => array( 'name' => 'sienna', 'red' => '160', 'green' => '82', 'blue' => '45', 'hex' => 'A0522D' ),
		'942' => array( 'name' => 'sienna1', 'red' => '255', 'green' => '130', 'blue' => '71', 'hex' => 'FF8247' ),
		'943' => array( 'name' => 'sienna2', 'red' => '238', 'green' => '121', 'blue' => '66', 'hex' => 'EE7942' ),
		'944' => array( 'name' => 'sienna3', 'red' => '205', 'green' => '104', 'blue' => '57', 'hex' => 'CD6839' ),
		'945' => array( 'name' => 'sienna4', 'red' => '139', 'green' => '71', 'blue' => '38', 'hex' => '8B4726' ),
		'946' => array( 'name' => 'silver', 'red' => '230', 'green' => '232', 'blue' => '250', 'hex' => 'E6E8FA' ),
		'947' => array( 'name' => 'silver*', 'red' => '192', 'green' => '192', 'blue' => '192', 'hex' => 'C0C0C0' ),
		'948' => array( 'name' => 'silver, grey', 'red' => '192', 'green' => '192', 'blue' => '192', 'hex' => 'C0C0C0' ),
		'949' => array( 'name' => 'sky blue', 'red' => '50', 'green' => '153', 'blue' => '204', 'hex' => '3299CC' ),
		'950' => array( 'name' => 'sky blue1', 'red' => '135', 'green' => '206', 'blue' => '255', 'hex' => '87CEFF' ),
		'951' => array( 'name' => 'sky blue2', 'red' => '126', 'green' => '192', 'blue' => '238', 'hex' => '7EC0EE' ),
		'952' => array( 'name' => 'sky blue3', 'red' => '108', 'green' => '166', 'blue' => '205', 'hex' => '6CA6CD' ),
		'953' => array( 'name' => 'sky blue4', 'red' => '74', 'green' => '112', 'blue' => '139', 'hex' => '4A708B' ),
		'954' => array( 'name' => 'skyblue', 'red' => '135', 'green' => '206', 'blue' => '235', 'hex' => '87CEEB' ),
		'955' => array( 'name' => 'skyblue1', 'red' => '135', 'green' => '206', 'blue' => '255', 'hex' => '87CEFF' ),
		'956' => array( 'name' => 'skyblue2', 'red' => '126', 'green' => '192', 'blue' => '238', 'hex' => '7EC0EE' ),
		'957' => array( 'name' => 'skyblue3', 'red' => '108', 'green' => '166', 'blue' => '205', 'hex' => '6CA6CD' ),
		'958' => array( 'name' => 'skyblue4', 'red' => '74', 'green' => '112', 'blue' => '139', 'hex' => '4A708B' ),
		'959' => array( 'name' => 'slate blue', 'red' => '106', 'green' => '90', 'blue' => '205', 'hex' => '6A5ACD' ),
		'960' => array( 'name' => 'slate blue1', 'red' => '131', 'green' => '111', 'blue' => '255', 'hex' => '836FFF' ),
		'961' => array( 'name' => 'slate blue2', 'red' => '122', 'green' => '103', 'blue' => '238', 'hex' => '7A67EE' ),
		'962' => array( 'name' => 'slate blue3', 'red' => '105', 'green' => '89', 'blue' => '205', 'hex' => '6959CD' ),
		'963' => array( 'name' => 'slate blue4', 'red' => '71', 'green' => '60', 'blue' => '139', 'hex' => '473C8B' ),
		'964' => array( 'name' => 'slate gray1', 'red' => '198', 'green' => '226', 'blue' => '255', 'hex' => 'C6E2FF' ),
		'965' => array( 'name' => 'slate gray2', 'red' => '185', 'green' => '211', 'blue' => '238', 'hex' => 'B9D3EE' ),
		'966' => array( 'name' => 'slate gray3', 'red' => '159', 'green' => '182', 'blue' => '205', 'hex' => '9FB6CD' ),
		'967' => array( 'name' => 'slate gray4', 'red' => '108', 'green' => '123', 'blue' => '139', 'hex' => '6C7B8B' ),
		'968' => array( 'name' => 'slate grey', 'red' => '112', 'green' => '128', 'blue' => '144', 'hex' => '708090' ),
		'969' => array( 'name' => 'slateblue', 'red' => '106', 'green' => '90', 'blue' => '205', 'hex' => '6A5ACD' ),
		'970' => array( 'name' => 'slateblue1', 'red' => '131', 'green' => '111', 'blue' => '255', 'hex' => '836FFF' ),
		'971' => array( 'name' => 'slateblue2', 'red' => '122', 'green' => '103', 'blue' => '238', 'hex' => '7A67EE' ),
		'972' => array( 'name' => 'slateblue3', 'red' => '105', 'green' => '89', 'blue' => '205', 'hex' => '6959CD' ),
		'973' => array( 'name' => 'slateblue4', 'red' => '71', 'green' => '60', 'blue' => '139', 'hex' => '473C8B' ),
		'974' => array( 'name' => 'slategray', 'red' => '112', 'green' => '128', 'blue' => '144', 'hex' => '708090' ),
		'975' => array( 'name' => 'slategray1', 'red' => '198', 'green' => '226', 'blue' => '255', 'hex' => 'C6E2FF' ),
		'976' => array( 'name' => 'slategray2', 'red' => '185', 'green' => '211', 'blue' => '238', 'hex' => 'B9D3EE' ),
		'977' => array( 'name' => 'slategray3', 'red' => '159', 'green' => '182', 'blue' => '205', 'hex' => '9FB6CD' ),
		'978' => array( 'name' => 'slategray4', 'red' => '108', 'green' => '123', 'blue' => '139', 'hex' => '6C7B8B' ),
		'979' => array( 'name' => 'snow', 'red' => '255', 'green' => '250', 'blue' => '250', 'hex' => 'FFFAFA' ),
		'980' => array( 'name' => 'snow1', 'red' => '255', 'green' => '250', 'blue' => '250', 'hex' => 'FFFAFA' ),
		'981' => array( 'name' => 'snow1 (snow)', 'red' => '255', 'green' => '250', 'blue' => '250', 'hex' => 'FFFAFA' ),
		'982' => array( 'name' => 'snow2', 'red' => '238', 'green' => '233', 'blue' => '233', 'hex' => 'EEE9E9' ),
		'983' => array( 'name' => 'snow3', 'red' => '205', 'green' => '201', 'blue' => '201', 'hex' => 'CDC9C9' ),
		'984' => array( 'name' => 'snow4', 'red' => '139', 'green' => '137', 'blue' => '137', 'hex' => '8B8989' ),
		'985' => array( 'name' => 'spicy pink', 'red' => '255', 'green' => '28', 'blue' => '174', 'hex' => 'FF1CAE' ),
		'986' => array( 'name' => 'spring green', 'red' => '0', 'green' => '255', 'blue' => '127', 'hex' => '00FF7F' ),
		'987' => array( 'name' => 'spring green1', 'red' => '0', 'green' => '255', 'blue' => '127', 'hex' => '00FF7F' ),
		'988' => array( 'name' => 'spring green2', 'red' => '0', 'green' => '238', 'blue' => '118', 'hex' => '00EE76' ),
		'989' => array( 'name' => 'spring green3', 'red' => '0', 'green' => '205', 'blue' => '102', 'hex' => '00CD66' ),
		'990' => array( 'name' => 'spring green4', 'red' => '0', 'green' => '139', 'blue' => '69', 'hex' => '008B45' ),
		'991' => array( 'name' => 'springgreen', 'red' => '0', 'green' => '255', 'blue' => '127', 'hex' => '00FF7F' ),
		'992' => array( 'name' => 'springgreen1', 'red' => '0', 'green' => '238', 'blue' => '118', 'hex' => '00EE76' ),
		'993' => array( 'name' => 'springgreen2', 'red' => '0', 'green' => '205', 'blue' => '102', 'hex' => '00CD66' ),
		'994' => array( 'name' => 'springgreen3', 'red' => '0', 'green' => '139', 'blue' => '69', 'hex' => '008B45' ),
		'995' => array( 'name' => 'springgreen4', 'red' => '0', 'green' => '139', 'blue' => '69', 'hex' => '008B45' ),
		'996' => array( 'name' => 'steel blue', 'red' => '35', 'green' => '107', 'blue' => '142', 'hex' => '236B8E' ),
		'997' => array( 'name' => 'steel blue1', 'red' => '99', 'green' => '184', 'blue' => '255', 'hex' => '63B8FF' ),
		'998' => array( 'name' => 'steel blue2', 'red' => '92', 'green' => '172', 'blue' => '238', 'hex' => '5CACEE' ),
		'999' => array( 'name' => 'steel blue3', 'red' => '79', 'green' => '148', 'blue' => '205', 'hex' => '4F94CD' ),
		'1000' => array( 'name' => 'steel blue4', 'red' => '54', 'green' => '100', 'blue' => '139', 'hex' => '36648B' ),
		'1001' => array( 'name' => 'steelblue', 'red' => '70', 'green' => '130', 'blue' => '180', 'hex' => '4682B4' ),
		'1002' => array( 'name' => 'steelblue1', 'red' => '99', 'green' => '184', 'blue' => '255', 'hex' => '63B8FF' ),
		'1003' => array( 'name' => 'steelblue2', 'red' => '92', 'green' => '172', 'blue' => '238', 'hex' => '5CACEE' ),
		'1004' => array( 'name' => 'steelblue3', 'red' => '79', 'green' => '148', 'blue' => '205', 'hex' => '4F94CD' ),
		'1005' => array( 'name' => 'steelblue4', 'red' => '54', 'green' => '100', 'blue' => '139', 'hex' => '36648B' ),
		'1006' => array( 'name' => 'steelblue5', 'red' => '51', 'green' => '102', 'blue' => '153', 'hex' => '336699' ),
		'1007' => array( 'name' => 'steelblue6', 'red' => '51', 'green' => '153', 'blue' => '204', 'hex' => '3399CC' ),
		'1008' => array( 'name' => 'steelblue7', 'red' => '102', 'green' => '153', 'blue' => '204', 'hex' => '6699CC' ),
		'1009' => array( 'name' => 'summer sky', 'red' => '56', 'green' => '176', 'blue' => '222', 'hex' => '38B0DE' ),
		'1010' => array( 'name' => 'tan', 'red' => '210', 'green' => '180', 'blue' => '140', 'hex' => 'D2B48C' ),
		'1011' => array( 'name' => 'tan1', 'red' => '255', 'green' => '165', 'blue' => '79', 'hex' => 'FFA54F' ),
		'1012' => array( 'name' => 'tan2', 'red' => '238', 'green' => '154', 'blue' => '73', 'hex' => 'EE9A49' ),
		'1013' => array( 'name' => 'tan3', 'red' => '205', 'green' => '133', 'blue' => '63', 'hex' => 'CD853F' ),
		'1014' => array( 'name' => 'tan3 (peru)', 'red' => '205', 'green' => '133', 'blue' => '63', 'hex' => 'CD853F' ),
		'1015' => array( 'name' => 'tan4', 'red' => '139', 'green' => '90', 'blue' => '43', 'hex' => '8B5A2B' ),
		'1016' => array( 'name' => 'teal', 'red' => '0', 'green' => '128', 'blue' => '128', 'hex' => '008080' ),
		'1017' => array( 'name' => 'teal*', 'red' => '0', 'green' => '128', 'blue' => '128', 'hex' => '008080' ),
		'1018' => array( 'name' => 'thistle', 'red' => '216', 'green' => '191', 'blue' => '216', 'hex' => 'D8BFD8' ),
		'1019' => array( 'name' => 'thistle1', 'red' => '255', 'green' => '225', 'blue' => '255', 'hex' => 'FFE1FF' ),
		'1020' => array( 'name' => 'thistle2', 'red' => '238', 'green' => '210', 'blue' => '238', 'hex' => 'EED2EE' ),
		'1021' => array( 'name' => 'thistle3', 'red' => '205', 'green' => '181', 'blue' => '205', 'hex' => 'CDB5CD' ),
		'1022' => array( 'name' => 'thistle4', 'red' => '139', 'green' => '123', 'blue' => '139', 'hex' => '8B7B8B' ),
		'1023' => array( 'name' => 'tomato', 'red' => '255', 'green' => '99', 'blue' => '71', 'hex' => 'FF6347' ),
		'1024' => array( 'name' => 'tomato1', 'red' => '255', 'green' => '99', 'blue' => '71', 'hex' => 'FF6347' ),
		'1025' => array( 'name' => 'tomato1 (tomato)', 'red' => '255', 'green' => '99', 'blue' => '71', 'hex' => 'FF6347' ),
		'1026' => array( 'name' => 'tomato2', 'red' => '238', 'green' => '92', 'blue' => '66', 'hex' => 'EE5C42' ),
		'1027' => array( 'name' => 'tomato3', 'red' => '205', 'green' => '79', 'blue' => '57', 'hex' => 'CD4F39' ),
		'1028' => array( 'name' => 'tomato4', 'red' => '139', 'green' => '54', 'blue' => '38', 'hex' => '8B3626' ),
		'1029' => array( 'name' => 'turquoise', 'red' => '64', 'green' => '224', 'blue' => '208', 'hex' => '40E0D0' ),
		'1030' => array( 'name' => 'turquoise1', 'red' => '0', 'green' => '245', 'blue' => '255', 'hex' => '00F5FF' ),
		'1031' => array( 'name' => 'turquoise2', 'red' => '0', 'green' => '229', 'blue' => '238', 'hex' => '00E5EE' ),
		'1032' => array( 'name' => 'turquoise3', 'red' => '0', 'green' => '197', 'blue' => '205', 'hex' => '00C5CD' ),
		'1033' => array( 'name' => 'turquoise4', 'red' => '0', 'green' => '134', 'blue' => '139', 'hex' => '00868B' ),
		'1034' => array( 'name' => 'turquoiseblue', 'red' => '0', 'green' => '199', 'blue' => '140', 'hex' => '00C78C' ),
		'1035' => array( 'name' => 'very dark brown', 'red' => '92', 'green' => '64', 'blue' => '51', 'hex' => '5C4033' ),
		'1036' => array( 'name' => 'very light grey', 'red' => '205', 'green' => '205', 'blue' => '205', 'hex' => 'CDCDCD' ),
		'1037' => array( 'name' => 'violet', 'red' => '238', 'green' => '130', 'blue' => '238', 'hex' => 'EE82EE' ),
		'1038' => array( 'name' => 'violet blue', 'red' => '159', 'green' => '95', 'blue' => '159', 'hex' => '9F5F9F' ),
		'1039' => array( 'name' => 'violet red', 'red' => '204', 'green' => '50', 'blue' => '153', 'hex' => 'CC3299' ),
		'1040' => array( 'name' => 'violet red1', 'red' => '255', 'green' => '62', 'blue' => '150', 'hex' => 'FF3E96' ),
		'1041' => array( 'name' => 'violet red2', 'red' => '238', 'green' => '58', 'blue' => '140', 'hex' => 'EE3A8C' ),
		'1042' => array( 'name' => 'violet red3', 'red' => '205', 'green' => '50', 'blue' => '120', 'hex' => 'CD3278' ),
		'1043' => array( 'name' => 'violet red4', 'red' => '139', 'green' => '34', 'blue' => '82', 'hex' => '8B2252' ),
		'1044' => array( 'name' => 'violetred', 'red' => '208', 'green' => '32', 'blue' => '144', 'hex' => 'D02090' ),
		'1045' => array( 'name' => 'violetred1', 'red' => '255', 'green' => '62', 'blue' => '150', 'hex' => 'FF3E96' ),
		'1046' => array( 'name' => 'violetred2', 'red' => '238', 'green' => '58', 'blue' => '140', 'hex' => 'EE3A8C' ),
		'1047' => array( 'name' => 'violetred3', 'red' => '205', 'green' => '50', 'blue' => '120', 'hex' => 'CD3278' ),
		'1048' => array( 'name' => 'violetred4', 'red' => '139', 'green' => '34', 'blue' => '82', 'hex' => '8B2252' ),
		'1049' => array( 'name' => 'warmgrey', 'red' => '128', 'green' => '128', 'blue' => '105', 'hex' => '808069' ),
		'1050' => array( 'name' => 'wheat', 'red' => '245', 'green' => '222', 'blue' => '179', 'hex' => 'F5DEB3' ),
		'1051' => array( 'name' => 'wheat1', 'red' => '255', 'green' => '231', 'blue' => '186', 'hex' => 'FFE7BA' ),
		'1052' => array( 'name' => 'wheat2', 'red' => '238', 'green' => '216', 'blue' => '174', 'hex' => 'EED8AE' ),
		'1053' => array( 'name' => 'wheat3', 'red' => '205', 'green' => '186', 'blue' => '150', 'hex' => 'CDBA96' ),
		'1054' => array( 'name' => 'wheat4', 'red' => '139', 'green' => '126', 'blue' => '102', 'hex' => '8B7E66' ),
		'1055' => array( 'name' => 'white', 'red' => '255', 'green' => '255', 'blue' => '255', 'hex' => 'FFFFFF' ),
		'1056' => array( 'name' => 'white smoke', 'red' => '245', 'green' => '245', 'blue' => '245', 'hex' => 'F5F5F5' ),
		'1057' => array( 'name' => 'white*', 'red' => '255', 'green' => '255', 'blue' => '255', 'hex' => 'FFFFFF' ),
		'1058' => array( 'name' => 'whitesmoke', 'red' => '245', 'green' => '245', 'blue' => '245', 'hex' => 'F5F5F5' ),
		'1059' => array( 'name' => 'whitesmoke (gray 96)', 'red' => '245', 'green' => '245', 'blue' => '245', 'hex' => 'F5F5F5' ),
		'1060' => array( 'name' => 'yellow', 'red' => '255', 'green' => '255', 'blue' => '0', 'hex' => 'FFFF00' ),
		'1061' => array( 'name' => 'yellow green', 'red' => '154', 'green' => '205', 'blue' => '50', 'hex' => '9ACD32' ),
		'1062' => array( 'name' => 'yellow1', 'red' => '255', 'green' => '255', 'blue' => '0', 'hex' => 'FFFF00' ),
		'1063' => array( 'name' => 'yellow1 (yellow*)', 'red' => '255', 'green' => '255', 'blue' => '0', 'hex' => 'FFFF00' ),
		'1064' => array( 'name' => 'yellow2', 'red' => '238', 'green' => '238', 'blue' => '0', 'hex' => 'EEEE00' ),
		'1065' => array( 'name' => 'yellow3', 'red' => '205', 'green' => '205', 'blue' => '0', 'hex' => 'CDCD00' ),
		'1066' => array( 'name' => 'yellow4', 'red' => '139', 'green' => '139', 'blue' => '0', 'hex' => '8B8B00' ),
		'1067' => array( 'name' => 'yellowgreen', 'red' => '154', 'green' => '205', 'blue' => '50', 'hex' => '9ACD32' )
		);

	$this->debug->out();
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	rgb2name();
#
#-Description:
#
#	Find a color name by its RGB value.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:18:30.41 
#		Original Program.
#
#
#END DOC
################################################################################
function rgb2name( $r, $g, $b )
{
	$this->debug->in();
#
#	Try to find the perfect match.
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( ($this->color_table[$i]['red'] == $r) &&
			($this->color_table[$i]['green'] == $g) &&
			($this->color_table[$i]['blue'] == $b) ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	Try to find a good match.
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( (($this->color_table[$i]['red'] == $r) &&
			($this->color_table[$i]['green'] == $g)) ||
			(($this->color_table[$i]['red'] == $r) &&
			($this->color_table[$i]['blue'] == $b)) ||
			(($this->color_table[$i]['green'] == $g) &&
			($this->color_table[$i]['blue'] == $b)) ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	Can we find anything at all?
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( ($this->color_table[$i]['red'] == $r) ||
			($this->color_table[$i]['green'] == $g) ||
			($this->color_table[$i]['blue'] == $b) ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	It is unlikely we will wind up here - but....
#
	$this->debug->out();

	return "Unknown";
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	hex2name();
#
#-Description:
#
#	A function to get the RGB name via the HEX value.  This function
#	can be called in two separate ways: 1)A single hex value, and
#	2)Separate RGB hex values.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:24:55.54 
#		Original Program.
#
#
#END DOC
################################################################################
function hex2name()
{
	$this->debug->in();

	if( func_num_args() == 1 ){
		$h = func_get_arg();
		if( preg_match("/^#/", $h) ){ $h = substr( $h, 1 ); }
		}
		elseif( func_num_args() == 3 ){
			list($r, $g, $b) = func_get_args();
			
			if( strlen($r) < 2 ){ $r = "0$r"; }
			if( strlen($g) < 2 ){ $g = "0$g"; }
			if( strlen($b) < 2 ){ $b = "0$b"; }

			$h = $r . $g . $b;
			}
		else { return array(null, null, null); }
#
#	Try to find a perfect match.
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( $this->color_table['hex'] == $h ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	Try to find a good match.
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( substr($this->color_table['hex'],0,4) == substr($h,0,4) ){
			return $this->color_table[$i]['name'];
			}

		if( substr($this->color_table['hex'],2,4) == substr($h,2,4) ){
			return $this->color_table[$i]['name'];
			}

		if( substr($this->color_table['hex'],0,2).substr($this->color_table['hex'],4,2) ==
			substr($h,0,2).substr($h,4,2) ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	Try to find a some kind of a match.
#
	for( $i=0; $i<count($this->color_table); $i++ ){
		if( substr($this->color_table['hex'],0,2) == substr($h,0,2) ){
			return $this->color_table[$i]['name'];
			}

		if( substr($this->color_table['hex'],2,2) == substr($h,2,2) ){
			return $this->color_table[$i]['name'];
			}

		if( substr($this->color_table['hex'],4,2) == substr($h,4,2) ){
			return $this->color_table[$i]['name'];
			}
		}
#
#	Here we are again...
#
	$this->debug->out();

	return "Unknown";
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	rgb2hex();
#
#-Description:
#
#	Function to return a hex value.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:35:42.32 
#		Original Program.
#
#
#END DOC
################################################################################
function rgb2hex( $r, $g, $b )
{
	$this->debug->in();

	$nr = dechex( $r ); if( strlen($nr) < 2 ){ $nr = "0$nr"; }
	$ng = dechex( $g ); if( strlen($ng) < 2 ){ $ng = "0$ng"; }
	$nb = dechex( $b ); if( strlen($nb) < 2 ){ $nb = "0$nb"; }

	$this->debug->out();

	return "$nr$ng$nb";
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	hex2rgb();
#
#-Description:
#
#	Function to return the RGB values.  This function can be called in
#	two separate ways: 1)A single hex value, or 2) separate rgb hex values.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:37:54.05 
#		Original Program.
#
#
#END DOC
################################################################################
function hex2rgb()
{
	$this->debug->in();

	$num_args = func_num_args();
	$func_args = func_get_args();

	if( $num_args == 1 ){ $h = $func_args[0]; }
		elseif( $num_args == 3 ){
			list($r, $g, $b) = $func_args[0];
			
			if( strlen($r) < 2 ){ $r = "0$r"; }
			if( strlen($g) < 2 ){ $g = "0$g"; }
			if( strlen($b) < 2 ){ $b = "0$b"; }

			$h = $r . $g . $b;
			}
		else { return array(null, null, null); }

	$nh = $h;
	if( substr($nh,0,1) == "#" ){ $nh = substr( $h, 1 ); }

	$nr = substr( $nh, 0, 2 );
	$ng = substr( $nh, 2, 2 );
	$nb = substr( $nh, 4, 2 );

	$nr = hexdec( $nr );
	$ng = hexdec( $ng );
	$nb = hexdec( $nb );

	$this->debug->out();

	return array($nr, $ng, $nb );
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	name2rgb();
#
#-Description:
#
#	Function to return the RGB value based upon the name of the color.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:41:01.80 
#		Original Program.
#
#
#END DOC
################################################################################
function name2rgb( $s=null )
{
	$this->debug->in();
	if( is_null($s) ){ $this->die("COLOR NAME is NULL", __LINE__ ); }

	for( $i=0; $i<count($this->color_table); $i++ ){
		if( preg_match("/$s/i", $this->color_table[$i]['name']) ){
			return array( $this->color_table[$i]['red'],
				$this->color_table[$i]['green'],
				$this->color_table[$i]['blue'] );
			}
		}

	$this->debug->out();
}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	name2hex();
#
#-Description:
#
#	Function to return the hex value based upon the name.
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
#	Mark Manning			Simulacron I			Thu 10/29/2009 23:41:53.85 
#		Original Program.
#
#
#END DOC
################################################################################
function name2hex( $s )
{
	$this->debug->in();

	for( $i=0; $i<count($this->color_table); $i++ ){
		if( preg_match("/$s/i", $this->color_table[$i]['name']) ){
			return $this->color_table[$i]['hex'];
			}
		}

	$this->debug->out();
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
################################################################################
#	die(). A simple function to print an error message and then die.
################################################################################
function die( $string=null, $line=null )
{
	$this->debug->in();

	if( is_null($string) ){ $string = "Program Aborted"; }
	if( is_null($string) ){ $line = __LINE__; }

	echo __FILE__ . ":" . __CLASS__ . ":" . __METHOD__ . ":" . __LINE__ . " = $string";

	$this->debug->out();

	exit(-1);
}
################################################################################
#	__destruct(). Do the clean-up necessary.
################################################################################
function __destruct()
{
	unset( $this->color_table );
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['color']) ){
		$GLOBALS['classes']['color'] = new class_color();
		}
?>
