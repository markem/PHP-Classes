<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		die( "***** Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n" );
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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_ansi();
#
#-Description:
#
#	This class ONLY does the ANSI standard stuff for output. This will allow
#	PHP to do things like clear the screen, colors, etc....
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
#	Mark Manning			Simulacron I			Sun 12/12/2021 12:16:07.19 
#		Original Program.
#
#
################################################################################
#
#    The following escape sequences are recognised (see "sequences.txt" for a
#    more complete description).
#
#	\e]0;titleBEL		xterm: Set window's title (and icon, ignored)
#	\e]2;titleBEL		xterm: Set window's title
#	\e]4;...BEL		xterm: Change color(s)
#	\e]104;...BEL		xterm: Reset color(s)
#	\e[21t			xterm: Report window's title
#	\e[s			ANSI.SYS: Save Cursor Position
#	\e[u			ANSI.SYS: Restore Cursor Position
#	\e[1+h		ACFM	Flush Mode (flush immediately)
#	\e[1+l		ACFM	Flush Mode (flush when necessary)
#	BEL		BEL	Bell
#	\e[#Z		CBT	Cursor Backward Tabulation
#	\e[#G		CHA	Cursor Character Absolute
#	\e[#I		CHT	Cursor Forward Tabulation
#	\e[#E		CNL	Cursor Next Line
#	\e[#F		CPL	Cursor Preceding Line
#	\e[3h		CRM	Control Representation Mode (display controls)
#	\e[3l		CRM	Control Representation Mode (perform controls)
#	\e[#D		CUB	Cursor Left
#	\e[#B		CUD	Cursor Down
#	\e[#C		CUF	Cursor Right
#	\e[#;#H 	CUP	Cursor Position
#	\e[#A		CUU	Cursor Up
#	\e[c		DA	Device Attributes
#	\e[#P		DCH	Delete Character
#	\e[?7h		DECAWM	Autowrap Mode (autowrap)
#	\e[?7l		DECAWM	Autowrap Mode (no autowrap)
#	\e[?3h		DECCOLM Selecting 80 or 132 Columns per Page (132)
#	\e[?3l		DECCOLM Selecting 80 or 132 Columns per Page (prior)
#	\e[?95h 	DECNCSM No Clearing Screen On Column Change Mode (keep)
#	\e[?95l 	DECNCSM No Clearing Screen On Column Change Mode (clear)
#	\e[?6h		DECOM	Origin Mode (top margin)
#	\e[?6l		DECOM	Origin Mode (top line)
#	\e[#;#;#...,~	DECPS	Play Sound
#	\e8		DECRC	Restore Cursor
#	\e7		DECSC	Save Cursor
#	\e[?5W		DECST8C Set Tab at Every 8 Columns
#	\e[?5;#W	DECST8C Set Tab at Every # Columns (ANSI extension)
#	\e[#;#r 	DECSTBM Set Top and Bottom Margins
#	\e[!p		DECSTR	Soft Terminal Reset
#	\e[?25h 	DECTCEM Text Cursor Enable Mode (show cursor)
#	\e[?25l 	DECTCEM Text Cursor Enable Mode (hide cursor)
#	\e[#M		DL	Delete Line
#	\e[#n		DSR	Device Status Report
#	\e[#X		ECH	Erase Character
#	\e[#J		ED	Erase In Page
#	\e[#K		EL	Erase In Line
#	\e[#`		HPA	Character Position Absolute
#	\e[#j		HPB	Character Position Backward
#	\e[#a		HPR	Character Position Forward
#	HT		HT	Character Tabulation
#	\eH		HTS	Character Tabulation Set
#	\e[#;#f 	HVP	Character And Line Position
#	\e[#@		ICH	Insert Character
#	\e[#L		IL	Insert Line
#	\eD		IND	Index
#	\e[4h		IRM	Insertion Replacement Mode (insert)
#	\e[4l		IRM	Insertion Replacement Mode (replace)
#	SI		LS0	Locking-shift Zero (see below)
#	SO		LS1	Locking-shift One
#	\eE		NEL	Next Line
#	\e[#b		REP	Repeat
#	\eM		RI	Reverse Index
#	\ec		RIS	Reset to Initial State
#	\e(0		SCS	Select Character Set (DEC special graphics)
#	\e(B		SCS	Select Character Set (ASCII)
#	\e[#;#;#m	SGR	Select Graphic Rendition
#	\e[#T		SD	Scroll Down/Pan Up
#	\e[#S		SU	Scroll Up/Pan Down
#	\e[#g		TBC	Tabulation Clear
#	\e[#d		VPA	Line Position Absolute
#	\e[#k		VPB	Line Position Backward
#	\e[#e		VPR	Line Position Forward
#
#	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
#	---------------------------------------------------------------------------
#		This is now under the BSD Three Clauses Plus Patents License.
#		See the BSD-3-Patent.txt file.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows: (Example)
#
#		CLASS_ANSI.PHP. A class to handle working with ANSI terminal codes.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
#	class class_ansi(). A class to use the ansi program.
################################################################################
class class_ansi
{
#
#	Pipes for proc_open
#
	private $pipes = null;

	private $uniqid = null;
	private $out = null;
	private $err = null;
	private $cwd = null;

	private $buf = null;
	private $win = null;

	private $ver = null;
	private $na = null;
	private $clicolor = null;

	private $ansi_cmds = null;
	private $sgr_cmds = null;
	private $bit34 = null;
	private $ascii_codes = null;
	private $xterm_cmds = null;
	private $xterm_xt36 = null;

	private $width = null;
	private $max_width = null;

	private $who = null;
	private $display_1 = null;

#	private $ansi_handle = null;
#
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['ansi']) ){
		return $this->init( func_get_args() );
		}
		else{ return $GLOBALS['class']['ansi']; }
}
################################################################################
#	init(). Using the INIT function so you can call it multiple times.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

#
#	Arguments are looked at HERE. Don't put them in!
#
	if( !is_null($this->ansi_handle) ){
		echo "Closing all pipes\n";
		$this->close();
		}

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->cwd = getcwd();
	$this->cwd = str_replace( "\\", "/", $this->cwd );
#
#	Make sure we are flushing everything.
#
	@ini_set( "implicit_flush", 1 );
	ob_implicit_flush();
#
#	Get the CLICOLOR environment variable.
#
	$this->na = "N/A";
	$this->width = 50;
	$this->max_width = 850;
	$this->clicolor = getenv( "CLICOLOR" );
#
#	List of ASCII codes from http://www.injosoft.se/kontakta.asp
#
#	DEC 	OCT 	HEX 	BIN 	Symbol 	HTML Number 	HTML Name 	Description
#

	$this->ascii_codes = [
		[ "0", "000", "00", "00000000", "NUL", "&amp;#000;", null, "Null char" ],
		[ "1", "001", "01", "00000001", "SOH", "&amp;#001;", null, "Start of Heading" ],
		[ "2", "002", "02", "00000010", "STX", "&amp;#002;", null, "Start of Text" ],
		[ "3", "003", "03", "00000011", "ETX", "&amp;#003;", null, "End of Text" ],
		[ "4", "004", "04", "00000100", "EOT", "&amp;#004;", null, "End of Transmission" ],
		[ "5", "005", "05", "00000101", "ENQ", "&amp;#005;", null, "Enquiry" ],
		[ "6", "006", "06", "00000110", "ACK", "&amp;#006;", null, "Acknowledgment" ],
		[ "7", "007", "07", "00000111", "BEL", "&amp;#007;", null, "Bell" ],
		[ "8", "010", "08", "00001000", "BS", "&amp;#008;", null, "Back Space" ],
		[ "9", "011", "09", "00001001", "HT", "&amp;#009;", null, "Horizontal Tab" ],
		[ "10", "012", "0A", "00001010", "LF", "&amp;#010;", null, "Line Feed" ],
		[ "11", "013", "0B", "00001011", "VT", "&amp;#011;", null, "Vertical Tab" ],
		[ "12", "014", "0C", "00001100", "FF", "&amp;#012;", null, "Form Feed" ],
		[ "13", "015", "0D", "00001101", "CR", "&amp;#013;", null, "Carriage Return" ],
		[ "14", "016", "0E", "00001110", "SO", "&amp;#014;", null, "Shift Out / X-On" ],
		[ "15", "017", "0F", "00001111", "SI", "&amp;#015;", null, "Shift In / X-Off" ],
		[ "16", "020", "10", "00010000", "DLE", "&amp;#016;", null, "Data Line Escape" ],
		[ "17", "021", "11", "00010001", "DC1", "&amp;#017;", null, "Device Control 1 (oft. XON)" ],
		[ "18", "022", "12", "00010010", "DC2", "&amp;#018;", null, "Device Control 2" ],
		[ "19", "023", "13", "00010011", "DC3", "&amp;#019;", null, "Device Control 3 (oft. XOFF)" ],
		[ "20", "024", "14", "00010100", "DC4", "&amp;#020;", null, "Device Control 4" ],
		[ "21", "025", "15", "00010101", "NAK", "&amp;#021;", null, "Negative Acknowledgement" ],
		[ "22", "026", "16", "00010110", "SYN", "&amp;#022;", null, "Synchronous Idle" ],
		[ "23", "027", "17", "00010111", "ETB", "&amp;#023;", null, "End of Transmit Block" ],
		[ "24", "030", "18", "00011000", "CAN", "&amp;#024;", null, "Cancel" ],
		[ "25", "031", "19", "00011001", "EM", "&amp;#025;", null, "End of Medium" ],
		[ "26", "032", "1A", "00011010", "SUB", "&amp;#026;", null, "Substitute" ],
		[ "27", "033", "1B", "00011011", "ESC", "&amp;#027;", null, "Escape" ],
		[ "28", "034", "1C", "00011100", "FS", "&amp;#028;", null, "File Separator" ],
		[ "29", "035", "1D", "00011101", "GS", "&amp;#029;", null, "Group Separator" ],
		[ "30", "036", "1E", "00011110", "RS", "&amp;#030;", null, "Record Separator" ],
		[ "31", "037", "1F", "00011111", "US", "&amp;#031;", null, "Unit Separator" ],
		[ "32", "040", "20", "00100000", " ", "&amp;#32;", " ", "Space" ],
		[ "33", "041", "21", "00100001", "!", "&amp;#33;", "&amp;excl;", "Exclamation mark" ],
		[ "34", "042", "22", "00100010", '"', "&amp;#34;", "&amp;quot;", "Double quotes (or speech marks)" ],
		[ "35", "043", "23", "00100011", "#", "&amp;#35;", "&amp;num;", "Number" ],
		[ "36", "044", "24", "00100100", "$", "&amp;#36;", "&amp;dollar;", "Dollar" ],
		[ "37", "045", "25", "00100101", "%", "&amp;#37;", "&amp;percnt;", "Per cent sign" ],
		[ "38", "046", "26", "00100110", "&amp;", "&amp;#38;", "&amp;amp;", "Ampersand" ],
		[ "39", "047", "27", "00100111", "'", "&amp;#39;", "&amp;apos;", "Single quote or Apostrophe" ],
		[ "40", "050", "28", "00101000", "(", "&amp;#40;", "&amp;lpar;",
			"round brackets or parentheses, opening round bracket" ],
		[ "41", "051", "29", "00101001", ")", "&amp;#41;", "&amp;rpar;",
			"parentheses or round brackets, closing parentheses" ],
		[ "42", "052", "2A", "00101010", "*", "&amp;#42;", "&amp;ast;", "Asterisk" ],
		[ "43", "053", "2B", "00101011", "+", "&amp;#43;", "&amp;plus;", "Plus sign" ],
		[ "44", "054", "2C", "00101100", ",", "&amp;#44;", "&amp;comma;", "Comma" ],
		[ "45", "055", "2D", "00101101", "-", "&amp;#45;", "-", "Hyphen" ],
		[ "46", "056", "2E", "00101110", ".", "&amp;#46;", "&amp;period;", "Period, dot or full stop" ],
		[ "47", "057", "2F", "00101111", "/", "&amp;#47;", "&amp;frasl;",
			"Slash , forward slash , fraction bar , division slash" ],
		[ "48", "060", "30", "00110000", "0", "&amp;#48;", "0", "Zero" ],
		[ "49", "061", "31", "00110001", "1", "&amp;#49;", "1", "One" ],
		[ "50", "062", "32", "00110010", "2", "&amp;#50;", "2", "Two" ],
		[ "51", "063", "33", "00110011", "3", "&amp;#51;", "3", "Three" ],
		[ "52", "064", "34", "00110100", "4", "&amp;#52;", "4", "Four" ],
		[ "53", "065", "35", "00110101", "5", "&amp;#53;", "5", "Five" ],
		[ "54", "066", "36", "00110110", "6", "&amp;#54;", "6", "Six" ],
		[ "55", "067", "37", "00110111", "7", "&amp;#55;", "7", "Seven" ],
		[ "56", "070", "38", "00111000", "8", "&amp;#56;", "8", "Eight" ],
		[ "57", "071", "39", "00111001", "9", "&amp;#57;", "9", "Nine" ],
		[ "58", "072", "3A", "00111010", ":", "&amp;#58;", "&amp;colon;", "Colon" ],
		[ "59", "073", "3B", "00111011", ";", "&amp;#59;", "&ampsemi;", "Semicolon" ],
		[ "60", "074", "3C", "00111100", "<", "&amp;#60;", "&amp;lt;", "Less than (or open angled bracket)" ],
		[ "61", "075", "3D", "00111101", "=", "&amp;#61;", "=", "Equals" ],
		[ "62", "076", "3E", "00111110", ">", "&amp;#62;", "&amp;gt;", "Greater than (or close angled bracket)" ],
		[ "63", "077", "3F", "00111111", "?", "&amp;#63;", "&amp;quest;", "Question mark" ],
		[ "64", "100", "40", "01000000", "@", "&amp;#64;", "&amp;commat;", "At symbol" ],
		[ "65", "101", "41", "01000001", "A", "&amp;#65;", "A", "Uppercase A" ],
		[ "66", "102", "42", "01000010", "B", "&amp;#66;", "B", "Uppercase B" ],
		[ "67", "103", "43", "01000011", "C", "&amp;#67;", "C", "Uppercase C" ],
		[ "68", "104", "44", "01000100", "D", "&amp;#68;", "D", "Uppercase D" ],
		[ "69", "105", "45", "01000101", "E", "&amp;#69;", "E", "Uppercase E" ],
		[ "70", "106", "46", "01000110", "F", "&amp;#70;", "F", "Uppercase F" ],
		[ "71", "107", "47", "01000111", "G", "&amp;#71;", "G", "Uppercase G" ],
		[ "72", "110", "48", "01001000", "H", "&amp;#72;", "H", "Uppercase H" ],
		[ "73", "111", "49", "01001001", "I", "&amp;#73;", "I", "Uppercase I" ],
		[ "74", "112", "4A", "01001010", "J", "&amp;#74;", "J", "Uppercase J" ],
		[ "75", "113", "4B", "01001011", "K", "&amp;#75;", "K", "Uppercase K" ],
		[ "76", "114", "4C", "01001100", "L", "&amp;#76;", "L", "Uppercase L" ],
		[ "77", "115", "4D", "01001101", "M", "&amp;#77;", "M", "Uppercase M" ],
		[ "78", "116", "4E", "01001110", "N", "&amp;#78;", "N", "Uppercase N" ],
		[ "79", "117", "4F", "01001111", "O", "&amp;#79;", "O", "Uppercase O" ],
		[ "80", "120", "50", "01010000", "P", "&amp;#80;", "P", "Uppercase P" ],
		[ "81", "121", "51", "01010001", "Q", "&amp;#81;", "Q", "Uppercase Q" ],
		[ "82", "122", "52", "01010010", "R", "&amp;#82;", "R", "Uppercase R" ],
		[ "83", "123", "53", "01010011", "S", "&amp;#83;", "S", "Uppercase S" ],
		[ "84", "124", "54", "01010100", "T", "&amp;#84;", "T", "Uppercase T" ],
		[ "85", "125", "55", "01010101", "U", "&amp;#85;", "U", "Uppercase U" ],
		[ "86", "126", "56", "01010110", "V", "&amp;#86;", "V", "Uppercase V" ],
		[ "87", "127", "57", "01010111", "W", "&amp;#87;", "W", "Uppercase W" ],
		[ "88", "130", "58", "01011000", "X", "&amp;#88;", "X", "Uppercase X" ],
		[ "89", "131", "59", "01011001", "Y", "&amp;#89;", "Y", "Uppercase Y" ],
		[ "90", "132", "5A", "01011010", "Z", "&amp;#90;", "Z", "Uppercase Z" ],
		[ "91", "133", "5B", "01011011", "[", "&amp;#91;", "&amp;lbrack;",
			"square brackets or box brackets, opening bracket" ],
		[ "92", "134", "5C", "01011100", "\\", "&amp;#92;", "&amp;bsol;", "Backslash, reverse slash" ],
		[ "93", "135", "5D", "01011101", "]", "&amp;#93;", "&amp;rbrack;",
			"box brackets or square brackets, closing bracket" ],
		[ "94", "136", "5E", "01011110", "^", "&amp;#94;", "&amp;Hat;", "Circumflex accent or Caret" ],
		[ "95", "137", "5F", "01011111", "_", "&amp;#95;", "&amp;lowbar;",
			"underscore , understrike , underbar or low line" ],
		[ "96", "140", "60", "01100000", "`", "&amp;#96;", "&amp;grave;", "Grave accent" ],
		[ "97", "141", "61", "01100001", "a", "&amp;#97;", "a", "Lowercase a" ],
		[ "98", "142", "62", "01100010", "b", "&amp;#98;", "b", "Lowercase b" ],
		[ "99", "143", "63", "01100011", "c", "&amp;#99;", "c", "Lowercase c" ],
		[ "100", "144", "64", "01100100", "d", "&amp;#100;", "d", "Lowercase d" ],
		[ "101", "145", "65", "01100101", "e", "&amp;#101;", "e", "Lowercase e" ],
		[ "102", "146", "66", "01100110", "f", "&amp;#102;", "f", "Lowercase f" ],
		[ "103", "147", "67", "01100111", "g", "&amp;#103;", "g", "Lowercase g" ],
		[ "104", "150", "68", "01101000", "h", "&amp;#104;", "h", "Lowercase h" ],
		[ "105", "151", "69", "01101001", "i", "&amp;#105;", "i", "Lowercase i" ],
		[ "106", "152", "6A", "01101010", "j", "&amp;#106;", "j", "Lowercase j" ],
		[ "107", "153", "6B", "01101011", "k", "&amp;#107;", "k", "Lowercase k" ],
		[ "108", "154", "6C", "01101100", "l", "&amp;#108;", "l", "Lowercase l" ],
		[ "109", "155", "6D", "01101101", "m", "&amp;#109;", "m", "Lowercase m" ],
		[ "110", "156", "6E", "01101110", "n", "&amp;#110;", "n", "Lowercase n" ],
		[ "111", "157", "6F", "01101111", "o", "&amp;#111;", "o", "Lowercase o" ],
		[ "112", "160", "70", "01110000", "p", "&amp;#112;", "p", "Lowercase p" ],
		[ "113", "161", "71", "01110001", "q", "&amp;#113;", "q", "Lowercase q" ],
		[ "114", "162", "72", "01110010", "r", "&amp;#114;", "r", "Lowercase r" ],
		[ "115", "163", "73", "01110011", "s", "&amp;#115;", "s", "Lowercase s" ],
		[ "116", "164", "74", "01110100", "t", "&amp;#116;", "t", "Lowercase t" ],
		[ "117", "165", "75", "01110101", "u", "&amp;#117;", "u", "Lowercase u" ],
		[ "118", "166", "76", "01110110", "v", "&amp;#118;", "v", "Lowercase v" ],
		[ "119", "167", "77", "01110111", "w", "&amp;#119;", "w", "Lowercase w" ],
		[ "120", "170", "78", "01111000", "x", "&amp;#120;", "x", "Lowercase x" ],
		[ "121", "171", "79", "01111001", "y", "&amp;#121;", "y", "Lowercase y" ],
		[ "122", "172", "7A", "01111010", "z", "&amp;#122;", "z", "Lowercase z" ],
		[ "123", "173", "7B", "01111011", "{", "&amp;#123;", "&amp;lbrace;",
			"braces or curly brackets, opening braces" ],
		[ "124", "174", "7C", "01111100", "|", "&amp;#124;", "&amp;vert;",
			"vertical-bar, vbar, vertical line or vertical slash" ],
		[ "125", "175", "7D", "01111101", "}", "&amp;#125;", "&amp;rbrace;",
			"curly brackets or braces, closing curly brackets" ],
		[ "126", "176", "7E", "01111110", "~", "&amp;#126;", "&amp;tilde;", "Tilde ; swung dash" ],
		[ "127", "177", "7F", "01111111", chr(127), "&amp;#127;", "DEL", "Delete" ],
		[ "128", "200", "80", "10000000", "€", "&amp;#128;", "&amp;euro;", "Euro sign" ],
		[ "129", "201", "81", "10000001", chr(129), "&amp;#129;", "&amp;uuml;",
			"letter u with umlaut or diaeresis , u-umlaut" ],
		[ "130", "202", "82", "10000010", "‚", "&amp;#130;", "&amp;sbquo;", "Single low-9 quotation mark" ],
		[ "131", "203", "83", "10000011", "ƒ", "&amp;#131;", "&amp;fnof;", "Latin small letter f with hook" ],
		[ "132", "204", "84", "10000100", "„", "&amp;#132;", "&amp;bdquo;", "Double low-9 quotation mark" ],
		[ "133", "205", "85", "10000101", "…", "&amp;#133;", "&amp;hellip;", "Horizontal ellipsis" ],
		[ "134", "206", "86", "10000110", "†", "&amp;#134;", "&amp;dagger;", "Dagger" ],
		[ "135", "207", "87", "10000111", "‡", "&amp;#135;", "&amp;Dagger;", "Double dagger" ],
		[ "136", "210", "88", "10001000", "ˆ", "&amp;#136;", "&amp;circ;", "Modifier letter circumflex accent" ],
		[ "137", "211", "89", "10001001", "‰", "&amp;#137;", "&amp;permil;", "Per mille sign" ],
		[ "138", "212", "8A", "10001010", "Š", "&amp;#138;", "&amp;Scaron;", "Latin capital letter S with caron" ],
		[ "139", "213", "8B", "10001011", "‹", "&amp;#139;", "&amp;lsaquo;", "Single left-pointing angle quotation" ],
		[ "140", "214", "8C", "10001100", "Œ", "&amp;#140;", "&amp;OElig;", "Latin capital ligature OE" ],
		[ "141", "215", "8D", "10001101", chr(141), "&amp;#141;", "&amp;igrave;", "letter i with grave accent" ],
		[ "142", "216", "8E", "10001110", "Ž", "&amp;#142;", "&Auml;", "letter A with umlaut or diaeresis ; A-umlaut" ],
		[ "143", "217", "8F", "10001111", chr(143), "&amp;#143;", "&amp;Aring;", "Capital letter A with a ring" ],
		[ "144", "220", "90", "10010000", chr(144), "&amp;#144;", "&amp;Eacute;",
			"Capital letter E with acute accent or E-acute" ],
		[ "145", "221", "91", "10010001", "‘", "&amp;#145;", "&amp;lsquo;", "Left single quotation mark" ],
		[ "146", "222", "92", "10010010", "’", "&amp;#146;", "&amp;rsquo;", "Right single quotation mark" ],
		[ "147", "223", "93", "10010011", "“", "&amp;#147;", "&amp;ldquo;", "Left double quotation mark" ],
		[ "148", "224", "94", "10010100", "”", "&amp;#148;", "&amp;rdquo;", "Right double quotation mark" ],
		[ "149", "225", "95", "10010101", "•", "&amp;#149;", "&amp;bull;", "Bullet" ],
		[ "150", "226", "96", "10010110", "–", "&amp;#150;", "&amp;ndash;", "En dash" ],
		[ "151", "227", "97", "10010111", "—", "&amp;#151;", "&amp;mdash;", "Em dash" ],
		[ "152", "230", "98", "10011000", "˜", "&amp;#152;", "&amp;tilde;", "Small tilde" ],
		[ "153", "231", "99", "10011001", "™", "&amp;#153;", "&amp;trade;", "Trade mark sign" ],
		[ "154", "232", "9A", "10011010", "š", "&amp;#154;", "&amp;scaron;", "Latin small letter S with caron" ],
		[ "155", "233", "9B", "10011011", "›", "&amp;#155;", "&amp;rsaquo; ", "Single right-pointing angle quotation mark" ],
		[ "156", "234", "9C", "10011100", "œ", "&amp;#156;", "&amp;oelig;", "Latin small ligature oe" ],
		[ "157", "235", "9D", "10011101", chr(157), "&amp;#157;", "&amp;Oslash;", "Uppercase slashed zero or empty set" ],
		[ "158", "236", "9E", "10011110", "ž", "&amp;#158;", "&amp;times;", "Multiplication sign" ],
		[ "159", "237", "9F", "10011111", "Ÿ", "&amp;#159;", "&amp;Yuml;", "Latin capital letter Y with diaeresis" ],
		[ "160", "240", "A0", "10100000", chr(160), "&amp;#160;", "&amp;aacute;",
			"Lowercase letter a with acute accent or a-acute" ],
		[ "161", "241", "A1", "10100001", "¡", "&amp;#161;", "&amp;iexcl;", "Inverted exclamation mark" ],
		[ "162", "242", "A2", "10100010", "¢", "&amp;#162;", "&amp;cent;", "Cent sign" ],
		[ "163", "243", "A3", "10100011", "£", "&amp;#163;", "&amp;pound;", "Pound sign" ],
		[ "164", "244", "A4", "10100100", "¤", "&amp;#164;", "&amp;curren;", "Currency sign" ],
		[ "165", "245", "A5", "10100101", "¥", "&amp;#165;", "&amp;yen;", "Yen sign" ],
		[ "166", "246", "A6", "10100110", "¦", "&amp;#166;", "&amp;brvbar;", "Pipe, Broken vertical bar" ],
		[ "167", "247", "A7", "10100111", "§", "&amp;#167;", "&amp;sect;", "Section sign" ],
		[ "168", "250", "A8", "10101000", "¨", "&amp;#168;", "&amp;uml;", "Spacing diaeresis - umlaut" ],
		[ "169", "251", "A9", "10101001", "©", "&amp;#169;", "&amp;copy;", "Copyright sign" ],
		[ "170", "252", "AA", "10101010", "ª", "&amp;#170;", "&amp;ordf;", "Feminine ordinal indicator" ],
		[ "171", "253", "AB", "10101011", "«", "&amp;#171;", "&amp;laquo;", "Left double angle quotes" ],
		[ "172", "254", "AC", "10101100", "¬", "&amp;#172;", "&amp;not;", "Not sign" ],
		[ "173", "255", "AD", "10101101", "­", "&amp;#173;", "&amp;shy;", "Soft hyphen" ],
		[ "174", "256", "AE", "10101110", "®", "&amp;#174;", "&amp;reg;", "Registered trade mark sign" ],
		[ "175", "257", "AF", "10101111", "¯", "&amp;#175;", "&amp;macr;", "Spacing macron - overline" ],
		[ "176", "260", "B0", "10110000", "°", "&amp;#176;", "&amp;deg;", "Degree sign" ],
		[ "177", "261", "B1", "10110001", "±", "&amp;#177;", "&amp;plusmn;", "Plus-or-minus sign" ],
		[ "178", "262", "B2", "10110010", "²", "&amp;#178;", "&amp;sup2;", "Superscript two - squared" ],
		[ "179", "263", "B3", "10110011", "³", "&amp;#179;", "&amp;sup3;", "Superscript three - cubed" ],
		[ "180", "264", "B4", "10110100", "´", "&amp;#180;", "&amp;acute;", "Acute accent - spacing acute" ],
		[ "181", "265", "B5", "10110101", "µ", "&amp;#181;", "&amp;micro;", "Micro sign" ],
		[ "182", "266", "B6", "10110110", "¶", "&amp;#182;", "&amp;para;", "Pilcrow sign - paragraph sign" ],
		[ "183", "267", "B7", "10110111", "·", "&amp;#183;", "&amp;middot;", "Middle dot - Georgian comma" ],
		[ "184", "270", "B8", "10111000", "¸", "&amp;#184;", "&amp;cedil;", "Spacing cedilla" ],
		[ "185", "271", "B9", "10111001", "¹", "&amp;#185;", "&amp;sup1;", "Superscript one" ],
		[ "186", "272", "BA", "10111010", "º", "&amp;#186;", "&amp;ordm;", "Masculine ordinal indicator" ],
		[ "187", "273", "BB", "10111011", "»", "&amp;#187;", "&amp;raquo;", "Right double angle quotes" ],
		[ "188", "274", "BC", "10111100", "¼", "&amp;#188;", "&amp;frac14;", "Fraction one quarter" ],
		[ "189", "275", "BD", "10111101", "½", "&amp;#189;", "&amp;frac12;", "Fraction one half" ],
		[ "190", "276", "BE", "10111110", "¾", "&amp;#190;", "&amp;frac34;", "Fraction three quarters" ],
		[ "191", "277", "BF", "10111111", "¿", "&amp;#191;", "&amp;iquest;", "Inverted question mark" ],
		[ "192", "300", "C0", "11000000", "À", "&amp;#192;", "&amp;Agrave;", "Latin capital letter A with grave" ],
		[ "193", "301", "C1", "11000001", "Á", "&amp;#193;", "&amp;Aacute;", "Latin capital letter A with acute" ],
		[ "194", "302", "C2", "11000010", "Â", "&amp;#194;", "&amp;Acirc;", "Latin capital letter A with circumflex" ],
		[ "195", "303", "C3", "11000011", "Ã", "&amp;#195;", "&amp;Atilde;", "Latin capital letter A with tilde" ],
		[ "196", "304", "C4", "11000100", "Ä", "&amp;#196;", "&amp;Auml;", "Latin capital letter A with diaeresis" ],
		[ "197", "305", "C5", "11000101", "Å", "&amp;#197;", "&amp;Aring;", "Latin capital letter A with ring above" ],
		[ "198", "306", "C6", "11000110", "Æ", "&amp;#198;", "&amp;AElig;", "Latin capital letter AE" ],
		[ "199", "307", "C7", "11000111", "Ç", "&amp;#199;", "&amp;Ccedil;", "Latin capital letter C with cedilla" ],
		[ "200", "310", "C8", "11001000", "È", "&amp;#200;", "&amp;Egrave;", "Latin capital letter E with grave" ],
		[ "201", "311", "C9", "11001001", "É", "&amp;#201;", "&amp;Eacute;", "Latin capital letter E with acute" ],
		[ "202", "312", "CA", "11001010", "Ê", "&amp;#202;", "&amp;Ecirc;", "Latin capital letter E with circumflex" ],
		[ "203", "313", "CB", "11001011", "Ë", "&amp;#203;", "&amp;Euml;", "Latin capital letter E with diaeresis" ],
		[ "204", "314", "CC", "11001100", "Ì", "&amp;#204;", "&amp;Igrave;", "Latin capital letter I with grave" ],
		[ "205", "315", "CD", "11001101", "Í", "&amp;#205;", "&amp;Iacute;", "Latin capital letter I with acute" ],
		[ "206", "316", "CE", "11001110", "Î", "&amp;#206;", "&amp;Icirc;", "Latin capital letter I with circumflex" ],
		[ "207", "317", "CF", "11001111", "Ï", "&amp;#207;", "&amp;Iuml;", "Latin capital letter I with diaeresis" ],
		[ "208", "320", "D0", "11010000", "Ð", "&amp;#208;", "&amp;ETH;", "Latin capital letter ETH" ],
		[ "209", "321", "D1", "11010001", "Ñ", "&amp;#209;", "&amp;Ntilde;", "Latin capital letter N with tilde" ],
		[ "210", "322", "D2", "11010010", "Ò", "&amp;#210;", "&amp;Ograve;", "Latin capital letter O with grave" ],
		[ "211", "323", "D3", "11010011", "Ó", "&amp;#211;", "&amp;Oacute;", "Latin capital letter O with acute" ],
		[ "212", "324", "D4", "11010100", "Ô", "&amp;#212;", "&amp;Ocirc;", "Latin capital letter O with circumflex" ],
		[ "213", "325", "D5", "11010101", "Õ", "&amp;#213;", "&amp;Otilde;", "Latin capital letter O with tilde" ],
		[ "214", "326", "D6", "11010110", "Ö", "&amp;#214;", "&amp;Ouml;", "Latin capital letter O with diaeresis" ],
		[ "215", "327", "D7", "11010111", "×", "&amp;#215;", "&amp;times;", "Multiplication sign" ],
		[ "216", "330", "D8", "11011000", "Ø", "&amp;#216;", "&amp;Oslash;", "Latin capital letter O with slash" ],
		[ "217", "331", "D9", "11011001", "Ù", "&amp;#217;", "&amp;Ugrave;", "Latin capital letter U with grave" ],
		[ "218", "332", "DA", "11011010", "Ú", "&amp;#218;", "&amp;Uacute;", "Latin capital letter U with acute" ],
		[ "219", "333", "DB", "11011011", "Û", "&amp;#219;", "&amp;Ucirc;", "Latin capital letter U with circumflex" ],
		[ "220", "334", "DC", "11011100", "Ü", "&amp;#220;", "&amp;Uuml;", "Latin capital letter U with diaeresis" ],
		[ "221", "335", "DD", "11011101", "Ý", "&amp;#221;", "&amp;Yacute;", "Latin capital letter Y with acute" ],
		[ "222", "336", "DE", "11011110", "Þ", "&amp;#222;", "&amp;THORN;", "Latin capital letter THORN" ],
		[ "223", "337", "DF", "11011111", "ß", "&amp;#223;", "&amp;szlig;", "Latin small letter sharp s - ess-zed" ],
		[ "224", "340", "E0", "11100000", "à", "&amp;#224;", "&amp;agrave;", "Latin small letter a with grave" ],
		[ "225", "341", "E1", "11100001", "á", "&amp;#225;", "&amp;aacute;", "Latin small letter a with acute" ],
		[ "226", "342", "E2", "11100010", "â", "&amp;#226;", "&amp;acirc;", "Latin small letter a with circumflex" ],
		[ "227", "343", "E3", "11100011", "ã", "&amp;#227;", "&amp;atilde;", "Latin small letter a with tilde" ],
		[ "228", "344", "E4", "11100100", "ä", "&amp;#228;", "&amp;auml;", "Latin small letter a with diaeresis" ],
		[ "229", "345", "E5", "11100101", "å", "&amp;#229;", "&amp;aring;", "Latin small letter a with ring above" ],
		[ "230", "346", "E6", "11100110", "æ", "&amp;#230;", "&amp;aelig;", "Latin small letter ae" ],
		[ "231", "347", "E7", "11100111", "ç", "&amp;#231;", "&amp;ccedil;", "Latin small letter c with cedilla" ],
		[ "232", "350", "E8", "11101000", "è", "&amp;#232;", "&amp;egrave;", "Latin small letter e with grave" ],
		[ "233", "351", "E9", "11101001", "é", "&amp;#233;", "&amp;eacute;", "Latin small letter e with acute" ],
		[ "234", "352", "EA", "11101010", "ê", "&amp;#234;", "&amp;ecirc;", "Latin small letter e with circumflex" ],
		[ "235", "353", "EB", "11101011", "ë", "&amp;#235;", "&amp;euml;", "Latin small letter e with diaeresis" ],
		[ "236", "354", "EC", "11101100", "ì", "&amp;#236;", "&amp;igrave;", "Latin small letter i with grave" ],
		[ "237", "355", "ED", "11101101", "í", "&amp;#237;", "&amp;iacute;", "Latin small letter i with acute" ],
		[ "238", "356", "EE", "11101110", "î", "&amp;#238;", "&amp;icirc;", "Latin small letter i with circumflex" ],
		[ "239", "357", "EF", "11101111", "ï", "&amp;#239;", "&amp;iuml;", "Latin small letter i with diaeresis" ],
		[ "240", "360", "F0", "11110000", "ð", "&amp;#240;", "&amp;eth;", "Latin small letter eth" ],
		[ "241", "361", "F1", "11110001", "ñ", "&amp;#241;", "&amp;ntilde;", "Latin small letter n with tilde" ],
		[ "242", "362", "F2", "11110010", "ò", "&amp;#242;", "&amp;ograve;", "Latin small letter o with grave" ],
		[ "243", "363", "F3", "11110011", "ó", "&amp;#243;", "&amp;oacute;", "Latin small letter o with acute" ],
		[ "244", "364", "F4", "11110100", "ô", "&amp;#244;", "&amp;ocirc;", "Latin small letter o with circumflex" ],
		[ "245", "365", "F5", "11110101", "õ", "&amp;#245;", "&amp;otilde;", "Latin small letter o with tilde" ],
		[ "246", "366", "F6", "11110110", "ö", "&amp;#246;", "&amp;ouml;", "Latin small letter o with diaeresis" ],
		[ "247", "367", "F7", "11110111", "÷", "&amp;#247;", "&amp;divide;", "Division sign" ],
		[ "248", "370", "F8", "11111000", "ø", "&amp;#248;", "&amp;oslash;", "Latin small letter o with slash" ],
		[ "249", "371", "F9", "11111001", "ù", "&amp;#249;", "&amp;ugrave;", "Latin small letter u with grave" ],
		[ "250", "372", "FA", "11111010", "ú", "&amp;#250;", "&amp;uacute;", "Latin small letter u with acute" ],
		[ "251", "373", "FB", "11111011", "û", "&amp;#251;", "&amp;ucirc;", "Latin small letter u with circumflex" ],
		[ "252", "374", "FC", "11111100", "ü", "&amp;#252;", "&amp;uuml;", "Latin small letter u with diaeresis" ],
		[ "253", "375", "FD", "11111101", "ý", "&amp;#253;", "&amp;yacute;", "Latin small letter y with acute" ],
		[ "254", "376", "FE", "11111110", "þ", "&amp;#254;", "&amp;thorn;", "Latin small letter thorn" ],
		[ "255", "377", "FF", "11111111", "ÿ", "&amp;#255;", "&amp;yuml;", "Latin small letter y with diaeresis" ]
		];

	$c = 0;
	$bell = chr( 7 );

	$this->ansi_cmds = [
#
#		array( <Return>, <Function Name>, <Beginning Text>, <Options>, <Ending Text>, <Info>, <Text>, <Return Type> )
#
#	Notes:
#
#		These functions are called as $<var>->f###(<arguments>).
#
#		<options> = TRUE/FALSE. True = there is something to print
#
		[ false,  "f" . $c++,  "\e]0;", "%s", $bell, "xterm:", "Set window's title (and icon, ignored)" ],
		[ false,  "f" . $c++,  "\e]2;", "%s", $bell, "xterm:", "Set window's title" ],
		[ false,  "f" . $c++,  "\e]4;", "%d;%d;%d", $bell, "xterm:", "Change color(s)" ],
		[ false,  "f" . $c++,  "\e]104;...", "%d;%d;%d", $bell, "xterm:", "Reset color(s)" ],
		[ true,  "f" . $c++,  "\e[21t", null, null, "xterm:", "Report window's title", "%s" ],
		[ false,  "f" . $c++,  "\e[s", null, null, "ANSI.SYS:", "Save Cursor Position" ],
		[ false,  "f" . $c++,  "\e[u", null, null, "ANSI.SYS:", "Restore Cursor Position" ],
		[ false,  "f" . $c++,  "\e[1+h", null, null, "ACFM", "Flush Mode (flush immediately)" ],
		[ false,  "f" . $c++,  "\e[1+l", null, null, "ACFM", "Flush Mode (flush when necessary)" ],
		[ false,  "f" . $c++,  "\e[", "%d", "Z", "CBT", "Cursor Backward Tabulation" ],
		[ false,  "f" . $c++,  "\e[", "%d", "G", "CHA", "Cursor Character Absolute" ],
		[ false,  "f" . $c++,  "\e[", "%d", "I", "CHT", "Cursor Forward Tabulation" ],
		[ false,  "f" . $c++,  "\e[", "%d", "E", "CNL", "Cursor Next Line" ],
		[ false,  "f" . $c++,  "\e[", "%d", "F", "CPL", "Cursor Preceding Line" ],
		[ false,  "f" . $c++,  "\e[3h", null, null, "CRM", "Control Representation Mode (display controls)" ],
		[ false,  "f" . $c++,  "\e[3l", null, null, "CRM", "Control Representation Mode (perform controls)" ],
		[ false,  "f" . $c++,  "\e[", "%d", "D", "CUB", "Cursor Left" ],
		[ false,  "f" . $c++,  "\e[", "%d", "B", "CUD", "Cursor Down" ],
		[ false,  "f" . $c++,  "\e[", "%d", "C", "CUF", "Cursor Right" ],
		[ false,  "f" . $c++,  "\e[", "%d;%d", "H", "CUP", "Cursor Position - 'Y;X'" ],
		[ false,  "f" . $c++,  "\e[", "%d", "A", "CUU", "Cursor Up" ],
		[ true,  "f" . $c++,  "\e[c", null, null, "DA", "Device Attributes", "%s" ],
		[ false,  "f" . $c++,  "\e[", "%d", "P", "DCH", "Delete Character" ],
		[ false,  "f" . $c++,  "\e[?7h", null, null, "DECAWM", "Autowrap Mode (autowrap)" ],
		[ false,  "f" . $c++,  "\e[?7l", null, null, "DECAWM", "Autowrap Mode (no autowrap)" ],
		[ false,  "f" . $c++,  "\e[?3h", null, null, "DECCOLM", "Selecting 80 or 132 Columns per Page (132)" ],
		[ false,  "f" . $c++,  "\e[?3l", null, null, "DECCOLM", "Selecting 80 or 132 Columns per Page (prior)" ],
		[ false,  "f" . $c++,  "\e[?95h", null, null, "DECNCSM", "No Clearing Screen On Column Change Mode (keep)" ],
		[ false,  "f" . $c++,  "\e[?95l", null, null, "DECNCSM", "No Clearing Screen On Column Change Mode (clear)" ],
		[ false,  "f" . $c++,  "\e[?6h", null, null, "DECOM", "Origin Mode (top margin)" ],
		[ false,  "f" . $c++,  "\e[?6l", null, null, "DECOM", "Origin Mode (top line)" ],
		[ false,  "f" . $c++,  "\e[", "%d;[%d,...]", '~', "DECPS", "Play Sound - '#;#;#...'" ],
		[ false,  "f" . $c++,  "\e8", null, null, "DECRC", "Restore Cursor" ],
		[ false,  "f" . $c++,  "\e7", null, null, "DECSC", "Save Cursor" ],
		[ false,  "f" . $c++,  "\e[?5W", null, null, "DECST8C", "Set Tab at Every 8 Columns" ],
		[ false,  "f" . $c++,  "\e[?5;", "%d", "W", "DECST8C", "Set Tab at Every # Columns (ANSI extension)" ],
		[ false,  "f" . $c++,  "\e[", "%d;%d", "r", "DECSTBM", "Set Top and Bottom Margins - '#;#'" ],
		[ false,  "f" . $c++,  "\e[!p", null, null, "DECSTR", "Soft Terminal Reset" ],
		[ false,  "f" . $c++,  "\e[?25h", null, null, "DECTCEM", "Text Cursor Enable Mode (show cursor)" ],
		[ false,  "f" . $c++,  "\e[?25l", null, null, "DECTCEM", "Text Cursor Enable Mode (hide cursor)" ],
		[ false,  "f" . $c++,  "\e[", "%d", "M", "DL", "Delete Line" ],
		[ true,  "f" . $c++,  "\e[", "%d", "n", "DSR", "Device Status Report", "%s" ],
		[ false,  "f" . $c++,  "\e[", "%d", "X", "ECH", "Erase Character" ],
		[ false,  "f" . $c++,  "\e[", "%d", "J", "ED", "Erase In Display" ],
		[ false,  "f" . $c++,  "\e[", "%d", "K", "EL", "Erase In Line" ],
		[ false,  "f" . $c++,  "\e[", "%d", '`', "HPA", "Character Position Absolute" ],
		[ false,  "f" . $c++,  "\e[", "%d", "j", "HPB", "Character Position Backward" ],
		[ false,  "f" . $c++,  "\e[", "%d", "a", "HPR", "Character Position Forward" ],
		[ false,  "f" . $c++,  "\eH", null, null, "HTS", "Character Tabulation Set" ],
		[ false,  "f" . $c++,  "\e[", "%d;%d", "f", "HVP", "Character And Line Position - '#;#'" ],
		[ false,  "f" . $c++,  "\e[", "%d", "@", "ICH", "Insert Character" ],
		[ false,  "f" . $c++,  "\e[", "%d", "L", "IL", "Insert Line" ],
		[ false,  "f" . $c++,  "\eD", null, null, "IND", "Index" ],
		[ false,  "f" . $c++,  "\e[4h", null, null, "IRM", "Insertion Replacement Mode (insert)" ],
		[ false,  "f" . $c++,  "\e[4l", null, null, "IRM", "Insertion Replacement Mode (replace)" ],
		[ false,  "f" . $c++,  "\eE", null, null, "NEL", "Next Line" ],
		[ false,  "f" . $c++,  "\e[", "%d", "b", "REP", "Repeat" ],
		[ false,  "f" . $c++,  "\eM", null, null, "RI", "Reverse Index" ],
		[ false,  "f" . $c++,  "\ec", null, null, "RIS", "Reset to Initial State" ],
		[ false,  "f" . $c++,  "\e(0", null, null, "SCS", "Select Character Set (DEC special graphics)" ],
		[ false,  "f" . $c++,  "\e(B", null, null, "SCS", "Select Character Set (ASCII)" ],
		[ false,  "f" . $c++,  "\e[", "%d;%d;%d", "m", "SGR", "Select Graphic Rendition - 'FG;BG;O'" ],
		[ false,  "f" . $c++,  "\e[", "%d", "T", "SD", "Scroll Down/Pan Up" ],
		[ false,  "f" . $c++,  "\e[", "%d", "S", "SU", "Scroll Up/Pan Down" ],
		[ false,  "f" . $c++,  "\e[", "%d", "g", "TBC", "Tabulation Clear" ],
		[ false,  "f" . $c++,  "\e[", "%d", "d", "VPA", "Line Position Absolute" ],
		[ false,  "f" . $c++,  "\e[", "%d", "k", "VPB", "Line Position Backward" ],
		[ false,  "f" . $c++,  "\e[", "%d", "e", "VPR", "Line Position Forward" ],
		[ false,  "f" . $c++,  "\e5i", null, null, "NEL", "Aux Port On" ],
		[ false,  "f" . $c++,  "\e4i", null, null, "NEL", "Aux Port Off" ],
		[ true,  "f" . $c++,  "\e[6n", null, null, "DSR", "Device Status Report #6(CPR)", "\x1b[%d;%dR" ],
		[ false, "f" . $c++, "\e[", '%d;"%s"', "p", null, "Define Key to STRING" ],
		[ false, "f" . $c++, "\e#6", null, null, "DECDWL", "Double Width Line" ],
		[ false, "f" . $c++, "\e#3", null, null, "DECDHL", "Double Height Line - top half" ],
		[ false, "f" . $c++, "\e#4", null, null, "DECDHL", "Double Height Line - bottom half" ],
		[ true, "f" . $c++, "\eZ", null, null, null, "Identify Terminal", "%s" ],
		[ false, "f" . $c++, "\e[0;4;5m", null, null, null, "Turn OFF all character attributes and turn ON SGR" ],
#
#	DEC Terminal Commands
#
#	This is a really weird command. The text says "ESC [ Pn c" but it does NOT tell you what "Pn" is.
#	By sending the above you get a variety of answers back. These are:
#
#	Option Present					Sequence Sent
#	No options 						ESC [?1;0c
#	Processor option (STP)		 	ESC [?1;1c
#	Advanced video option (AVO) 	ESC [?1;2c
#	AVO and STP 					ESC [?1;3c
#	Graphics option (GPO) 			ESC [?1;4c
#	GPO and STP 					ESC [?1;5c
#	GPO and AVO 					ESC [?1;6c
#	GPO, STP and AVO 				ESC [?1;7c
#
#	You will have to play with this to find out HOW to call it and what it gives back.
#
#		array( <Return>, <Function Name>, <Beginning Text>, <Options>, <Ending Text>, <Info>, <Text>, <Return Type> )
#
		[ true, "f" . $c++, "\e[", "%d", "c", "DA", "Device Attributes", "\e[?%d;%dc" ],	#	Version #1
		[ true, "f" . $c++, "\e[?1;", "%d", "c", "DA", "Device Attributes", "\e[?%d;%dc" ],	#	Version #2
		[ false, "f" . $c++, "\e#8", null, null, "DECALN", "Screen Alignment Display" ],
		[ true, "f" . $c++, "\e[P", "%s", null, null, "DECTerminal SET Mode" ],
		[ true, "f" . $c++, "\e[Ps?", "%d", null, null, "DEC Private Modes(0-9) - Manual" ],
		[ false, "f" . $c++, "\e[38;2;", "%d;%d;%d", "m", "RGB Foreground" ],
		[ false, "f" . $c++, "\e[48;2;", "%d;%d;%d", "m", "RGB Background" ],
		[ true, "f" . $c++, "\e[P", "%d;%d", "R", "CPR", "Cursor Position Report", "\e[?%d;%dc" ],	#	Special!!!!
		[ false, "f" . $c++, "\e8", null, null, "DECRC", "Restore Cursor (DEC Private)" ],
		[ true, "f" . $c++, "\e[;;;;;;x", null, null, "DECREPTPARM", "Report Terminal Parameters" ],
		[ true, "f" . $c++, "\e[<sol>", "%d", null, "DECREQTPARM",
			"<sol> option for Request Terminal Parameters", "unknown" ],
		[ true, "f" . $c++, "\e[<par>", "%d", null, "DECREQTPARM",
			"<par> option for Request Terminal Parameters", "unknown"],
		[ true, "f" . $c++, "\e[<nbits>", "%d", "c", null,
			"<nbits> option for Request Terminal Parameters", "unknown" ],
		[ true, "f" . $c++, "\e[<xspeed>,|", "%d", "|", null,
			"<xspeed> option for Request Terminal Parameters", "unknown" ],
		[ true, "f" . $c++, "\e[<clkmul>", "%d", null, null,
			"<clkmul> option for Request Terminal Parameters", "unknown" ],
		[ true, "f" . $c++, "\e[<flag>", "%d", null, null,
			"<flags> option for Request Terminal Parameters", "unknown" ],
		[ false,  "f" . $c++,  "", "%s", null, "Blank Input", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e", "%s", null, "Plain Escape", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e#", "%s", null, "ESC #", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e[#", "%s", null, "ESC [ #", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e[", "%s", null, "ESC [", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e]", "%s", null, "ESC ]", "Used for testing purposes", "Mixed" ],
		[ false,  "f" . $c++,  "\e[?", "%s", null, "ESC [ ?", "Used for testing purposes", "Mixed" ],
		[ false, "f" . $c++, "\e[8;", "%d;%d", "t", null, "Change a window's size (Y,X)", "unknown" ],
		];

#
#	C1 (8-Bit) Control Characters
#
	$ind = "\eD";
	$nel = "\eE";
	$hts = "\eH";
	$ri = "\eM";
	$ss2 = "\eN";
	$ss3 = "\eO";
	$dcs = "\eP";
	$spa = "\eV";
	$epa = "\eW";
	$sos = "\eX";
	$decid = "\eZ";
	$csi = "\e[";
	$st = "\e\\";
	$osc = "\e]";
	$pm = "\e^";
	$apc = "\e_";


	$c = 1;
	$xterm_036 = [
			[ "VT320 provides these:", "", "", "", 1, 1 ] ,
			[ "Send<br>This", "", "What is sent", "What it does", 1, 0 ] ,
			[ $c++, "=>", "DCS 0 ! u % 5 ST", "DEC Supplemental Graphic", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u A ST", "ISO Latin-1 supplemental", 0, 0 ] ,
			[ "VT510&nbsp;adds&nbsp;these:", "", "", "", 1, 1 ] ,
			[ $c++, "=>", 'DCS 0 ! u " ? ST', "DEC Greek", 0, 0 ] ,
			[ $c++, "=>", 'DCS 0 ! u " 4 ST', "DEC Hebrew", 0, 0 ] ,
			[ $c++, "=>", "DCS 0 ! u % 0 ST", "DEC Turkish", 0, 0 ] ,
			[ $c++, "=>", "DCS 0 ! u & 4 ST", "DEC Cyrillic", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u B ST", "ISO Latin-2 Supplemental", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u F ST", "ISO Greek Supplemental", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u H ST", "ISO Hebrew Supplemental", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u M ST", "ISO Latin-5 Supplemental", 0, 0 ] ,
			[ $c++, "=>", "DCS 1 ! u L ST", "ISO Latin-Cyrillic", 0, 0 ] ,
			[ "VT520 accepts a few others (undocumented);<br>xterm adds these:", "", "", "", 1, 1 ] ,
			[ $c++, "=>", "DCS 0 ! u B ST", "United States (USASCII).", 0, 0, ] ,
			[ $c++, "=>", "DCS 0 ! u 0 ST", "DEC Special Character and Line Drawing", 0, 0 ] ,
			[ "Set", "", "", "", 1, 1 ] ,
			[ $c++, "=>", "DCS 0 ! u > ST", "DEC Technical.", 0, 0 ]
		];

	$xterm_xt36 = [
		"",
		"0!u%5",
		"1!uA",
		'0!u"?',
		'0!u"4',
		"0!u%0",
		"0!u&4",
		"1!uB",
		"1!uF",
		"1!uH",
		"1!uM",
		"1!uL",
		"0!uB",
		"0!u0",
		"0!u>"
		];

	$xterm_table_36 = $this->inlineTable( $xterm_036 );

	$c = 1;
	$xterm_037 = [
		[ $c++, 'm', "=>", "SGR", 0, 0 ],
		[ $c++, '" p', "=>", "DECSCL", 0, 0 ],
		[ $c++, ' q', "=>", "DECSCUSR", 0, 0 ],
		[ $c++, '" q', "=>", "DECSCA", 0, 0 ],
		[ $c++, 'r', "=>", "DECSTBM", 0, 0 ],
		[ $c++, 's', "=>", "DECSLRM", 0, 0 ],
		[ $c++, 't', "=>", "DECSLPP", 0, 0 ],
		[ $c++, '$ |', "=>", "DECSCPP", 0, 0 ],
		[ $c++, '$ }', "=>", "DECSASD", 0, 0 ],
		[ $c++, '$ ~', "=>", "DECSSDT", 0, 0 ],
		[ $c++, '* x', "=>", "DECSACE", 0, 0 ],
		[ $c++, '* |', "=>", "DECSNLS", 0, 0 ],
		[ $c++, ', |', "=>", "DECAC (VT525 only)", 0, 0 ],
		[ $c++, ', }', "=>", "DECATC (VT525 only)", 0, 0 ],
		[ $c++, '> %c[;%c...] m', "=>", "XTQMODKEYS (xterm)", 0, 0 ],
		];

	$xterm_xt37 = [
		'm',
		'"p',
		' q',
		'"q',
		'r',
		's',
		't',
		'$|',
		'$}',
		'$~',
		'*x',
		'*|',
		',|',
		',}',
		'>%cm'
		];

	$xterm_table_37 = $this->inlineTable( $xterm_037 );

	$c = 0;
	$ba = "<a href='https://invisible-island.net/xterm/ctlseqs/ctlseqs.html#h2-Definitions'>website</a>";
	$this->xterm_cmds = [
#
#		array( <Return>, <Function Name>, <Beginning Text>, <Options>, <Ending Text>, <Info>, <Text>, <Return Type> )
#
#	Notes:
#
#		These functions are called as $<var>->xt###(<arguments>).
#
#		<options> = TRUE/FALSE. True = there is something to print
#
		[ false,  "xt" . $c++,  "\e ", null, "F", "xterm:",
			"Tells the terminal to send C1 control characters as 7-bit sequences" ],
		[ false,  "xt" . $c++,  "\e ", null, "G", "xterm:",
			"Tells the terminal to send C1 control characters as 8-bit sequences" ],
		[ false,  "xt" . $c++,  "\e ", null, "L", "xterm:", "Set ANSI conformance level 1, ECMA-43" ],
		[ false,  "xt" . $c++,  "\e ", null, "M", "xterm:", "Set ANSI conformance level 2, ECMA-43" ],
		[ false,  "xt" . $c++,  "\e ", null, "N", "xterm:", "Set ANSI conformance level 3, ECMA-43" ],
		[ false,  "xt" . $c++,  "\e#", null, "3", "xterm:", "DEC double-height line, top half (DECDHL), VT100" ],
		[ false,  "xt" . $c++,  "\e#", null, "4", "xterm:", "DEC double-height line, bottom half (DECDHL), VT100" ],
		[ false,  "xt" . $c++,  "\e#", null, "5", "xterm:", "DEC single-width line (DECSWL), VT100" ],
		[ false,  "xt" . $c++,  "\e#", null, "6", "xterm:", "DEC double-width line (DECSWL), VT100" ],
		[ false,  "xt" . $c++,  "\e#", null, "8", "xterm:", "DEC Screen Alignment Test (DECALN), VT100" ],
		[ false,  "xt" . $c++,  "\e%", null, "@", "xterm:",
			"Select default character set. That is ISO 8859-1 (ISO 2022)" ],

		[ false,  "xt" . $c++,  "\e%", null, "G", "xterm:", "Select UTF-8 character set, ISO 2022" ],
		[ false,  "xt" . $c++,  "\e(", "%s", "A", "xterm:",
			"Designate G0 Character Set, VT100, ISO 2022. " .
			"Please check the XTerm $ba for each language." ],

		[ false,  "xt" . $c++,  "\e)", "%s", "A", "xterm:", "Designate G1 Character Set, ISO 2022, VT100" ],
		[ false,  "xt" . $c++,  "\e*", "%s", "A", "xterm:", "Designate G2 Character Set, ISO 2022, VT100" ],
		[ false,  "xt" . $c++,  "\e+", "%s", "A", "xterm:", "Designate G3 Character Set, ISO 2022, VT100" ],
		[ false,  "xt" . $c++,  "\e-", "%s", "A", "xterm:",
			"Designate G3 Character Set, ISO 2022, VT220. " .
			"Please check the XTerm $ba for each language." ],

		[ false,  "xt" . $c++,  "\e.", "%s", "A", "xterm:", "Designate G2 Character Set, VT300" ],
		[ false,  "xt" . $c++,  "\e/", "%s", "A", "xterm:", "Designate G3 Character Set, VT300" ],
		[ false,  "xt" . $c++,  "\e", null, 6, "xterm:", "Back Index (DECBI), VT420 and up" ],
		[ false,  "xt" . $c++,  "\e", null, 7, "xterm:", "Save Cursor (DECSC), VT100" ],
		[ false,  "xt" . $c++,  "\e", null, 8, "xterm:", "Restore Cursor (DECRC), VT100" ],
		[ false,  "xt" . $c++,  "\e", null, 9, "xterm:", "Forward Index (DECFI), VT420 and up" ],
		[ false,  "xt" . $c++,  "\e", null, "=", "xterm:", "Application Keypad (DECKPAM)" ],
		[ false,  "xt" . $c++,  "\e", null, ">", "xterm:", "Normal Keypad (DECKPNM), VT100" ],
		[ false,  "xt" . $c++,  "\e", null, "F", "xterm:", "Cursor to lower left corner of screen" ],
		[ false,  "xt" . $c++,  "\e", null, "c", "xterm:", "Full Reset (RIS), VT100" ],
		[ false,  "xt" . $c++,  "\e", null, "l", "xterm:",
			"Memory Lock (per HP terminals). Locks memory above the cursor." ],

		[ false,  "xt" . $c++,  "\e", null, "m", "xterm:", "Memory Unlock (per HP terminals)" ],
		[ false,  "xt" . $c++,  "\e", null, "n", "xterm:", "Invoke the G2 Character Set as GL (LS2)" ],
		[ false,  "xt" . $c++,  "\e", null, "o", "xterm:", "Invoke the G3 Character Set as GL (LS3)" ],
		[ false,  "xt" . $c++,  "\e", null, "|", "xterm:", "Invoke the G3 Character Set as GR (LS3R)" ],
		[ false,  "xt" . $c++,  "\e", null, "}", "xterm:", "Invoke the G2 Character Set as GR (LS2R)" ],
		[ false,  "xt" . $c++,  "\e", null, "~", "xterm:", "Invoke the G1 Character Set as GR (LS1R), VT100" ],
		[ false,  "xt" . $c++,  $apc . "A", null, "ST", "xterm:",
			"None.  xterm implements no APC functions; Pt is ignored. " .
			"Pt need not be printable characters." ],

		[ false,  "xt" . $c++,  $dcs, '"%s"; ["%s" | "%s/%s;..."]', "ST", "xterm:",
			"User-Defined Keys (DECUDK), VT220 and up. ALWAYS check the $ba " .
			"to see what you should send to this function." ],

		[ false,  "xt" . $c++,  $dcs, "%d", "ST", "xterm:",
			"Assigning User-Preferred Supplemental Sets (DECAUPSS), VT320, " .
			"VT510.  ALWAYS check the $ba to see what you should send to this function.<p>" .
			"You can use the following table to send what you need to the function:" .
			$xterm_table_36
			],

		[ false,  "xt" . $c++,  $dcs . "", "%s", "ST", "xterm:",
			"Request Status String (DECRQSS), VT420 and up." .
			"Use the following table to send a command:" . $xterm_table_37, "Mixed"
			],

		[ false,  "xt" . $c++,  $dcs . "", "%s", "ST", "xterm:",
			"Restore presentation status (DECRSPS), VT320 and up.  The " .
			"control can be converted from a response from DECCIR or " .
			'DECTABSR by changing the first "u" to a "t". As always, be sure to' .
			"check the $ba to see what you should send."
			],

		[ false,  "xt" . $c++,  $dcs . "", "+Q%s[;%s...]", "ST", "xterm:",
			"Request resource values (XTGETXRES), xterm.  The string " .
			"following the 'Q' is a list of names encoded in hexadecimal (2 " .
			"digits per character) separated by ; which correspond to xterm " .
			"resource names.", "Mixed"
			],

		[ false,  "xt" . $c++,  $dcs . "", "+p%s", "ST", "xterm:",
			"Set Termcap/Terminfo Data (XTSETTCAP), xterm.  The string " .
			"following the 'p' is encoded in hexadecimal.  After decoding " .
			"it, xterm will use the name to retrieve data from the terminal " .
			"database.  If successful, that overrides the termName resource " .
			"when handling the 'tcap' keyboard configuration's function- " .
			"and special-keys, as well as by the Request Termcap/Terminfo " .
			"String control. "
			],

		[ false,  "xt" . $c++,  $dcs . "", "+q%s", "ST", "xterm:",
			"Request Termcap/Terminfo String (XTGETTCAP), xterm.  The " .
			"string following the 'q' is a list of names encoded in " .
			"hexadecimal (2 digits per character) separated by ; which " .
			"correspond to termcap or terminfo key names. " .
			"A few special features are also recognized, which are not key " .
			"names. Be sure to check the $ba for more information.", "Mixed"
			],

		[ false,  "xt" . $c++,  $csi, "%d", "@", "xterm:",
			"Insert Ps (Blank) Character(s) (default = 1) (ICH)." ],

		[ false,  "xt" . $c++,  $csi, "%d", " @", "xterm:",
			"Shift left Ps columns(s) (default = 1) (SL), ECMA-48." ],

		[ false,  "xt" . $c++,  $csi, "%d", "A", "xterm:",
			"Cursor Up Ps Times (default = 1) (CUU)." ],

		[ false,  "xt" . $c++,  $csi, "%d", " A", "xterm:",
			"Shift right Ps columns(s) (default = 1) (SR), ECMA-48." ],

		[ false,  "xt" . $c++,  $csi, "%d", "B", "xterm:",
			"Cursor Down Ps Times (default = 1) (CUD)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "C", "xterm:",
			"Cursor Forward Ps Times (default = 1) (CUF)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "D", "xterm:",
			"Cursor Backward Ps Times (default = 1) (CUB)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "E", "xterm:",
			"Cursor Next Line Ps Times (default = 1) (CNL)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "F", "xterm:",
			"Cursor Preceding Line Ps Times (default = 1) (CPL)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "G", "xterm:",
			"Cursor Character Absolute  [column] (default = [row,1]) (CHA)." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d", "H", "xterm:",
			"Cursor Position [row;column] (default = [1,1]) (CUP)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "I", "xterm:",
			"Cursor Forward Tabulation Ps tab stops (default = 1) (CHT)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "J", "xterm:",
			"Erase in Display (ED), VT100." ],

		[ false,  "xt" . $c++,  $csi, "?%d", "J", "xterm:",
			"Erase in Display (DECSED), VT220." ],

		[ false,  "xt" . $c++,  $csi, "%d", "K", "xterm:",
			"Erase in Line (EL), VT100." ],

		[ false,  "xt" . $c++,  $csi, "?%d", "K", "xterm:",
			"Erase in Line (DECSEL), VT220." ],

		[ false,  "xt" . $c++,  $csi, "%d", "L", "xterm:",
			"Insert Ps Line(s) (default = 1) (IL)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "M", "xterm:",
			"Delete Ps Line(s) (default = 1) (DL)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "P", "xterm:",
			"Delete Ps Character(s) (default = 1) (DCH)." ],

		[ false,  "xt" . $c++,  $csi, "%d", "P", "xterm:",
			"Push current dynamic- and ANSI-palette colors onto stack " .
			"(XTPUSHCOLORS), xterm.  Parameters (integers in the range 1 " .
			"through 10, since the default 0 will push) may be used to " .
			"store the palette into the stack without pushing. " ],

		[ false,  "xt" . $c++,  $csi, "%c[;%c;]%d", "P", "xterm:",
			"Push current dynamic- and ANSI-palette colors onto stack " .
			"(XTPUSHCOLORS), xterm.  Parameters (integers in the range 1 " .
			"through 10, since the default 0 will push) may be used to " .
			"store the palette into the stack without pushing. " ],

		[ false,  "xt" . $c++,  $csi, "%d", "Q", "xterm:",
			"Pop stack to set dynamic- and ANSI-palette colors " .
			"(XTPOPCOLORS), xterm.  Parameters (integers in the range 1 " .
			"through 10, since the default 0 will pop) may be used to " .
			"restore the palette from the stack without popping. " ],

		[ false,  "xt" . $c++,  $csi, "%c[%c;...]%d", "Q", "xterm:",
			"Pop stack to set dynamic- and ANSI-palette colors " .
			"(XTPOPCOLORS), xterm.  Parameters (integers in the range 1 " .
			"through 10, since the default 0 will pop) may be used to " .
			"restore the palette from the stack without popping." ],

		[ false,  "xt" . $c++,  $csi, "%d", "R", "xterm:",
			"Report the current entry on the palette stack, and the number " .
			"of palettes stored on the stack, using the same form as " .
			"XTPOPCOLOR (default = 0) (XTREPORTCOLORS), xterm." ],

		[ false,  "xt" . $c++,  $csi, "%d", "S", "xterm:",
			"Scroll up Ps lines (default = 1) (SU), VT420, ECMA-48." ],

		[ false,  "xt" . $c++,  $csi, "?%d;%d;%d[;%d]", "S", "xterm:",
			"Set or request graphics attribute (XTSMGRAPHICS), xterm.  If " .
			"configured to support either Sixel Graphics or ReGIS Graphics, " .
			"xterm accepts a three-parameter control sequence, where Pi, Pa " .
			"and Pv are the item, action and value. " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi, "%s", "T", "xterm:",
			"Scroll down Ps lines (default = 1) (SD), VT420." ],

		[ false,  "xt" . $c++,  $csi, "%s;%d;%d;%d;%d", "T", "xterm:",
			"Initiate highlight mouse tracking (XTHIMOUSE), xterm. " .
			"Parameters are [func;startx;starty;firstrow;lastrow].  See the " .
			"section Mouse Tracking. " ],

		[ false,  "xt" . $c++,  $csi . ">", "%d[;%d...]", "T", "xterm:",
			"Reset title mode features to default value (XTRMTITLE), xterm. " .
			"Normally, 'reset' disables the feature.  It is possible to " .
			"disable the ability to reset features by compiling a different " .
			"default for the title modes into xterm. " ],

		[ false,  "xt" . $c++,  $csi . "?5", null, "W", "xterm:",
			"Reset tab stops to start with column 9, every 8 columns (DECST8C), VT510." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "X", "xterm:",
			"Erase Ps Character(s) (default = 1) (ECH)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "Z", "xterm:",
			"Cursor Backward Tabulation Ps tab stops (default = 1) (CBT)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "^", "xterm:",
			"Scroll down Ps lines (default = 1) (SD), ECMA-48. " .
			"This was a publication error in the original ECMA-48 5th " .
			"edition (1991) corrected in 2003. " ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "`", "xterm:",
			"Character Position Absolute  [column] (default = [row,1]) (HPA)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "a", "xterm:",
			"Character Position Relative  [columns] (default = [row,col+1]) (HPR)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "b", "xterm:",
			"Repeat the preceding graphic character Ps times (REP)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "c", "xterm:",
			"Send Device Attributes (Primary DA). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . "=", "%s[;%s...]", "c", "xterm:",
			"Send Device Attributes (Tertiary DA)." ],

		[ false,  "xt" . $c++,  $csi . ">", "%s[;%s...]", "c", "xterm:",
			"Send Device Attributes (Secondary DA). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "d", "xterm:",
			"Line Position Absolute  [row] (default = [1,column]) (VPA)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "e", "xterm:",
			"Line Position Relative  [rows] (default = [row+1,column]) (VPR)." ],

		[ false,  "xt" . $c++,  $csi, "[%s;...][;%s...]", "f", "xterm:",
			"Horizontal and Vertical Position [row;column] (default = [1,1]) (HVP)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "g", "xterm:",
			"Tab Clear (TBC).  ECMA-48 defines additional codes, but the " .
			"VT100 user manual notes that it ignores other codes.  DEC's " .
			"later terminals (and xterm) do the same, for compatibility. " ],

		[ false,  "xt" . $c++,  $csi, "%d[;%d...]", "h", "xterm:", "Set Mode (SM)." ],

		[ false,  "xt" . $c++,  $csi . "?", "%d[;%d...]", "h", "xterm:",
			"DEC Private Mode Set (DECSET). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "i", "xterm:",
			"Media Copy (MC)." ],

		[ false,  "xt" . $c++,  $csi . "?", "%s[;%s...]", "i", "xterm:",
			"Media Copy (MC), DEC-specific." ],

		[ false,  "xt" . $c++,  $csi, "%d[;%d...]", "l", "xterm:", 
			"Reset Mode (RM)." ],

		[ false,  "xt" . $c++,  $csi . "?", "%d[;%d...]", "l", "xterm:", 
			"DEC Private Mode Reset (DECRST). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi, "%d[;%d...]", "m", "xterm:", 
			"Character Attributes (SGR). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . ">", "[%d;...];[%d;...]", "m", "xterm:", 
			"Set/reset key modifier options (XTMODKEYS), xterm.  Set or " .
			"reset resource-values used by xterm to decide whether to " .
			"construct escape sequences holding information about the " .
			"modifiers pressed with a given key. " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . ">", "[%d;...]", "m", "xterm:", 
			"Set/reset key modifier options (XTMODKEYS), xterm.  Set or " .
			"reset resource-values used by xterm to decide whether to " .
			"construct escape sequences holding information about the " .
			"modifiers pressed with a given key. " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . "?", "[%d;...]", "m", "xterm:", 
			"Query key modifier options (XTQMODKEYS), xterm." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "n", "xterm:", 
			"Device Status Report (DSR)." ],

		[ false,  "xt" . $c++,  $csi . ">", "%s[;%s...]", "n", "xterm:", 
			"Disable key modifier options, xterm.  These modifiers may be " .
			"enabled via the CSI > Pm m sequence.  This control sequence " .
			"corresponds to a resource value of '-1', which cannot be set " .
			"with the other sequence. " ],

		[ false,  "xt" . $c++,  $csi . "?", "%s[;%s...]", "n", "xterm:", 
			"Device Status Report (DSR, DEC-specific). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . ">", "%s[;%s...]", "p", "xterm:", 
			"Set resource value pointerMode (XTSMPOINTER), xterm.  This is " .
			"used by xterm to decide whether to hide the pointer cursor as " .
			"the user types. " ],

		[ false,  "xt" . $c++,  $csi . "!", null, "p", "xterm:", 
			"Soft terminal reset (DECSTR), VT220 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d%d;%d", '"p', "xterm:", 
			"Set conformance level (DECSCL), VT220 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "\$p", "xterm:", 
			"Request ANSI mode (DECRQM)." ],

		[ false,  "xt" . $c++,  $csi . "?", "%s[;%s...]", "\$p", "xterm:", 
			"Request DEC private mode (DECRQM)." ],

		[ false,  "xt" . $c++,  $csi . "#", null, 'p', "xterm:", 
			"Push video attributes onto stack (XTPUSHSGR), xterm." ],

		[ false,  "xt" . $c++,  $csi, "%d%d;%d", '#p', "xterm:", 
			"Push video attributes onto stack (XTPUSHSGR), xterm." ],

		[ false,  "xt" . $c++,  $csi . ">0", null, 'q', "xterm:", 
			"Ps = 0  ¿  Report xterm name and version (XTVERSION). " .
			"The response is a DSR sequence identifying the version: " .
			"DCS > | text ST " ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "q", "xterm:", 
			"Load LEDs (DECLL), VT100." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", " q", "xterm:", 
			"Set cursor style (DECSCUSR), VT520." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", '"q', "xterm:", 
			"Select character protection attribute (DECSCA), VT220.  Valid " .
			"values for the parameter: " ],

		[ false,  "xt" . $c++,  $csi, null, '#q', "xterm:", 
			"Pop video attributes from stack (XTPOPSGR), xterm.  This is an " .
			"alias for CSI # } , used to work around language limitations " .
			"of C# " ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "r", "xterm:", 
			"Set Scrolling Region [top;bottom] (default = full size of " .
			"window) (DECSTBM), VT100. " ],

		[ false,  "xt" . $c++,  $csi . "?", "%d[;%d...]", "r", "xterm:", 
			"Restore DEC Private Mode Values (XTRESTORE), xterm.  The value " .
			"of Ps previously saved is restored.  Ps values are the same as " .
			"for DECSET. " ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "\$r", "xterm:", 
			"Change Attributes in Rectangular Area (DECCARA), VT400 and up." ],

		[ false,  "xt" . $c++,  $csi, null, "s", "xterm:", 
			"Save cursor, available only when DECLRMM is disabled (SCOSC, " .
			"also ANSI.SYS). " ],

		[ false,  "xt" . $c++,  $csi, "%d;%d", "s", "xterm:", 
			"Set left and right margins (DECSLRM), VT420 and up.  This is " .
			"available only when DECLRMM is enabled. " ],

		[ false,  "xt" . $c++,  $csi . ">", "%s[;%s...]", "s", "xterm:", 
			"Set/reset shift-escape options (XTSHIFTESCAPE), xterm.  This " .
			"corresponds to the shiftEscape resource. " ],

		[ false,  "xt" . $c++,  $csi . "?", "%s[;%s...]", "s", "xterm:", 
			"Save DEC Private Mode Values (XTSAVE), xterm.  Ps values are " .
			"the same as for DECSET. " ],

		[ false,  "xt" . $c++,  $csi, "[%s;%s...];%s[;%s...];%s[;%s...]", "t", "xterm:", 
			"Window manipulation (XTWINOPS), dtterm, extended by xterm. " .
			"These controls may be disabled using the allowWindowOps resource. " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi . ">", "%d[;%d...]", "t", "xterm:", 
			"This xterm control sets one or more features of the title " .
			"modes (XTSMTITLE), xterm.  Each parameter enables a single " .
			"feature. " ],

		[ false,  "xt" . $c++,  $csi, "%d[;%d...]", " t", "xterm:", 
			"Set warning-bell volume (DECSWBV), VT520." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "\$t", "xterm:", 
			"Reverse Attributes in Rectangular Area (DECRARA), VT400 and up." ],

		[ false,  "xt" . $c++,  $csi, null, "u", "xterm:", 
			"Restore cursor (SCORC, also ANSI.SYS)." ],

		[ false,  "xt" . $c++,  $csi, null, "&u", "xterm:", 
			"User-Preferred Supplemental Set (DECRQUPSS), VT320, VT510. " .
			"Response is DECAUPSS. " ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", " u", "xterm:", 
			"Set margin-bell volume (DECSMBV), VT520." ],

		[ false,  "xt" . $c++,  $csi, null, '"v', "xterm:", 
			"Request Displayed Extent (DECRQDE), VT340, VT420." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d;%d;%d;%d", "\$v", "xterm:", 
			"Copy Rectangular Area (DECCRA), VT400 and up" ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "\$w", "xterm:", 
			"Request presentation state report (DECRQPSR), VT320 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "'w", "xterm:", 
			"Enable Filter Rectangle (DECEFR), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "x", "xterm:", 
			"Request Terminal Parameters (DECREQTPARM)." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "*x", "xterm:", 
			"Select Attribute Change Extent (DECSACE), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d;%d", "\$x", "xterm:", 
			"Fill Rectangular Area (DECFRA), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "#y", "xterm:", 
			"Select checksum extension (XTCHECKSUM), xterm.  The bits of Ps " .
			"modify the calculation of the checksum returned by DECRQCRA: " ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d;%d;%d", "*x", "xterm:", 
			"Request Checksum of Rectangular Area (DECRQCRA), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d", "'z", "xterm:", 
			"Enable Locator Reporting (DECELR)." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "\$z", "xterm:", 
			"Erase Rectangular Area (DECERA), VT400 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d", "'{", "xterm:", 
			"Select Locator Events (DECSLE)." ],

		[ false,  "xt" . $c++,  $csi, null, "#{", "xterm:", 
			"Push video attributes onto stack (XTPUSHSGR), xterm.  The " .
			"optional parameters correspond to the SGR encoding for video " .
			"attributes, except for colors (which do not have a unique SGR " .
			"code): " ],

		[ false,  "xt" . $c++,  $csi, "%d", "#{", "xterm:", 
			"Push video attributes onto stack (XTPUSHSGR), xterm.  The " .
			"optional parameters correspond to the SGR encoding for video " .
			"attributes, except for colors (which do not have a unique SGR code): " ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "\${", "xterm:", 
			"Selective Erase Rectangular Area (DECSERA), VT400 and up." ],

		[ false,  "xt" . $c++,  $csi, "%d;%d;%d;%d", "#|", "xterm:", 
			"Report selected graphic rendition (XTREPORTSGR), xterm." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "\$|", "xterm:", 
			"Select columns per page (DECSCPP), VT340." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "'|", "xterm:", 
			"Request Locator Position (DECRQLP). " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "*|", "xterm:", 
			"Select number of lines per screen (DECSNLS), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, null, "#}", "xterm:", 
			"Pop video attributes from stack (XTPOPSGR), xterm." ],

		[ false,  "xt" . $c++,  $csi, "%s;%d;%d", ",|", "xterm:", 
			"Assign Color (DECAC), VT525 only." ],

		[ false,  "xt" . $c++,  $csi, "%s;%d;%d", ",}", "xterm:", 
			"Alternate Text Color (DECATC), VT525 only." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "'}", "xterm:", 
			"Insert Ps Column(s) (default = 1) (DECIC), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "\$}", "xterm:", 
			"Select active status display (DECSASD), VT320 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "'~", "xterm:", 
			"Delete Ps Column(s) (default = 1) (DECDC), VT420 and up." ],

		[ false,  "xt" . $c++,  $csi, "%s[;%s...]", "\$~", "xterm:", 
			"Select status line type (DECSSDT), VT320 and up." ],

		[ false,  "xt" . $c++,  $osc, "%s;%s[;%s...]", $bell, "xterm:",
			"Ring the bell" ],

		[ false,  "xt" . $c++,  $osc, "%s;%s[;%s...]", $st, "xterm:",
			"Set Text Parameters, xterm. " .
			"Be sure to check the $ba for more information." ],

		[ false,  "xt" . $c++,  $osc, "%s[;%s...]", $st, "xterm:",
			"xterm implements no PM" ],

		[ false,  "xt" . $c++,  $csi . "1;", null, "t", "xterm:", "De-iconify window" ],
		[ false,  "xt" . $c++,  $csi . "2;", null, "t", "xterm:", "Iconify window" ],
		[ false,  "xt" . $c++,  $csi . "3;", "%d;%d", "t", "xterm:", "Move window to [ x, y ]" ],
		[ false,  "xt" . $c++,  $csi . "4;", "%d;%d", "t", "xterm:",
			"Resize the xterm window to height and width in pixels" ],
		];

#
#	SGR Commands
#
#	Code 	Effect 	Note
#
	$this->sgr_cmds = [
		[ 0, "Reset / Normal", "All attributes off" ],
		[ 1, "Bold or increased intensity",
			"As with faint, the color change is a PC (SCO / CGA) invention." ],
		[ 2, "Faint or decreased intensity",
			"aka Dim (with a saturated color). May be implemented as a light font weight like bold." ],
		[ 3, "Italic", "Not widely supported. Sometimes treated as inverse or blink." ],
		[ 4, "Underline", "Style extensions exist for Kitty, VTE, mintty and iTerm2." ],
		[ 5, "Slow Blink", "less than 150 per minute" ],
		[ 6, "Rapid Blink", "MS-DOS ANSI.SYS, 150+ per minute; not widely supported" ],
		[ 7, "Reverse video", "swap foreground and background colors, aka invert; inconsistent emulation[31]" ],
		[ 8, "Conceal", "aka Hide, not widely supported." ],
		[ 9, "Crossed-out", "aka Strike, characters legible, but marked for deletion." ],
		[ 10, "Primary", "(default) font" ],
		[ 11, "Alternative font", "Select alternative font n - 10" ],
		[ 12, "Alternative font", "Select alternative font n - 10" ],
		[ 13, "Alternative font", "Select alternative font n - 10" ],
		[ 14, "Alternative font", "Select alternative font n - 10" ],
		[ 15, "Alternative font", "Select alternative font n - 10" ],
		[ 16, "Alternative font", "Select alternative font n - 10" ],
		[ 17, "Alternative font", "Select alternative font n - 10" ],
		[ 18, "Alternative font", "Select alternative font n - 10" ],
		[ 19, "Alternative font", "Select alternative font n - 10" ],
		[ 20, "Fraktur", "Rarely supported" ],
		[ 21, "Doubly underline or Bold off", "Double-underline per ECMA-48.[5]:8.3.117 See discussion" ],
		[ 22, "Normal color or intensity", "Neither bold nor faint" ],
		[ 23, "Not italic, not Fraktur", "" ],
		[ 24, "Underline off", "Not singly or doubly underlined" ],
		[ 25, "Blink off", "" ],
		[ 26, "(Proportional spacing)", "ITU T.61 and T.416, not known to be used on terminals" ],
		[ 27, "Reverse/invert off", "" ],
		[ 28, "Reveal", "conceal off" ],
		[ 29, "Not crossed out", "" ],
		[ 31, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 32, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 33, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 34, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 35, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 36, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 37, "Set foreground color", "See color tables<sup>1</sup>" ],
		[ 38, "Set foreground color", "Next arguments are 5;n or 2;r;g;b, see below" ],
		[ 39, "Default foreground color", "implementation defined (according to standard)" ],
		[ 40, "Set background color", "See color tables<sup>1</sup>" ],
		[ 41, "Set background color", "See color tables<sup>1</sup>" ],
		[ 42, "Set background color", "See color tables<sup>1</sup>" ],
		[ 43, "Set background color", "See color tables<sup>1</sup>" ],
		[ 44, "Set background color", "See color tables<sup>1</sup>" ],
		[ 45, "Set background color", "See color tables<sup>1</sup>" ],
		[ 46, "Set background color", "See color tables<sup>1</sup>" ],
		[ 47, "Set background color", "See color tables<sup>1</sup>" ],
		[ 48, "Set background color", "Next arguments are 5;n or 2;r;g;b, see below" ],
		[ 49, "Default background color", "implementation defined (according to standard)" ],
		[ 50, "(Disable proportional spacing)", "T.61 and T.416" ],
		[ 51, "Framed", "" ],
		[ 52, "Encircled", "Implemented as 'emoji variation selector' in mintty." ],
		[ 53, "Overlined", "" ],
		[ 54, "Not framed or encircled", "" ],
		[ 55, "Not overlined", "" ],
		[ 58, "Underline color", "Kitty, VTE, mintty, and iTerm2. (not in standard)" ],
		[ 60, "ideogram underline or right side line", "Rarely supported" ],
		[ 61, "ideogram double underline or double line on the right side", "" ],
		[ 62, "ideogram overline or left side line", "" ],
		[ 63, "ideogram double overline or double line on the left side", "" ],
		[ 64, "ideogram stress marking", "" ],
		[ 65, "ideogram attributes off", "reset the effects of all of 60-64" ],
		[ 73, "superscript", "mintty (not in standard)" ],
		[ 74, "subscript", "" ],
		[ 90, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 91, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 92, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 93, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 94, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 95, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 96, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 97, "	Set bright foreground color", "aixterm (not in standard)" ],
		[ 100, "Set bright background color", "" ],
		[ 101, "Set bright background color", "" ],
		[ 102, "Set bright background color", "" ],
		[ 103, "Set bright background color", "" ],
		[ 104, "Set bright background color", "" ],
		[ 105, "Set bright background color", "" ],
		[ 106, "Set bright background color", "" ],
		[ 107, "Set bright background color", "" ]
		];

#
#	Taken from : https://en.wikipedia.org/wiki/ANSI_escape_code#Escape_sequences
#
#	Name 	FG Code 	BG Code 	VGA[nb 2] 	Windows Console[nb 3] 	Windows PowerShell[nb 4] 	Visual Studio Code 
#		Windows 10 Console Terminal.app 	PuTTY 	mIRC 	xterm 	Ubuntu[nb 6]
#

	$this->bit34 = [
		[ "Black", "30", "40", "0,0,0", "", "", "", "12,12,12", "0,0,0", "", "", "", "1,1,1" ],
		[ "Red", "31", "41", "170,0,0", "128,0,0", "", "205, 49, 49", "197,15,31",
			"194,54,33", "187,0,0", "127,0,0", "205,0,0", "222,56,43" ],
		[ "Green", "32", "42", "0,170,0", "0,128,0", "", "13, 188, 121", "19,161,14",
			"37,188,36", "0,187,0", "0,147,0", "0,205,0", "57,181,74" ],
		[ "Yellow", "33", "43", "170,85,0", "128,128,0", "238,237,240",
			"229, 229, 16", "193,156,0", "173,173,39", "187,187,0", "252,127,0", "205,205,0", "255,199,6" ],
		[ "Blue", "34", "44", "0,0,170", "0,0,128", "", "36, 114, 200", "0,55,218",
			"73,46,225", "0,0,187", "0,0,127", "0,0,238[33]", "0,111,184" ],
		[ "Magenta", "35", "45", "170,0,170", "128,0,128", "1,36,86", "188, 63, 188",
			"136,23,152", "211,56,211", "187,0,187", "156,0,156", "205,0,205", "118,38,113" ],
		[ "Cyan", "36", "46", "0,170,170", "0,128,128", "", "17, 168, 205", "58,150,221",
			"51,187,200", "0,187,187", "0,147,147", "0,205,205", "44,181,233" ],
		[ "White", "37", "47", "170,170,170", "192,192,192", "", "229, 229, 229", "204,204,204",
			"203,204,205", "187,187,187", "210,210,210", "229,229,229", "204,204,204" ],
		[ "Bright Black", "90", "100", "85,85,85", "128,128,128", "", "102, 102, 102", "118,118,118",
			"129,131,131", "85,85,85", "127,127,127", "127,127,127", "128,128,128" ],
		[ "Bright Red", "91", "101", "255,85,85", "255,0,0", "", "241, 76, 76", "231,72,86",
			"252,57,31", "255,85,85", "255,0,0", "255,0,0", "255,0,0" ],
		[ "Bright Green", "92", "102", "85,255,85", "0,255,0", "", "35, 209, 139", "22,198,12",
			"49,231,34", "85,255,85", "0,252,0", "0,255,0", "0,255,0" ],
		[ "Bright Yellow", "93", "103", "255,255,85", "255,255,0", "", "245, 245, 67", "249,241,165",
			"234,236,35", "255,255,85", "255,255,0", "255,255,0", "255,255,0" ],
		[ "Bright Blue", "94", "104", "85,85,255", "0,0,255", "", "59, 142, 234", "59,120,255",
			"88,51,255", "85,85,255", "0,0,252", "92,92,255[34]", "0,0,255" ],
		[ "Bright Magenta", "95", "105", "255,85,255", "255,0,255", "", "214, 112, 214",
			"180,0,158", "249,53,248", "255,85,255", "255,0,255", "255,0,255", "255,0,255" ],
		[ "Bright Cyan", "96", "106", "85,255,255", "0,255,255", "", "41, 184, 219",
			"97,214,214", "20,240,240", "85,255,255", "0,255,255", "0,255,255", "0,255,255" ],
		[ "Bright White", "97", "107", "255,255,255", "255,255,255", "", "229, 229, 229",
			"242,242,242", "233,235,235", "255,255,255", "255,255,255", "255,255,255", "255,255,255 " ]
		];

#
#	--------------------------------------------------------------------------------
#
#	Some popular private sequences
#
#	Code 	Effect
#	CSI s 	SCP/SCOSC:	Save Current Cursor Position. Saves the cursor
#						position/state in SCO console mode.[23] In vertical split
#						screen mode, instead used to set (as CSI n ; n s) or reset
#						left and right margins.[24]
#
#	CSI u 	RCP/SCORC:	Restore Saved Cursor Position. Restores the cursor position/state in SCO console mode.[25]
#	CSI ? 25 h 	DECTCEM Shows the cursor, from the VT320.
#	CSI ? 25 l 	DECTCEM Hides the cursor.
#	CSI ? 1049 h 	Enable alternative screen buffer
#	CSI ? 1049 l 	Disable alternative screen buffer
#	CSI ? 2004 h	Turn on bracketed paste mode. Text pasted into the
#					terminal will be surrounded by ESC [200~ and ESC [201~,
#					and characters in it should not be treated as commands
#					(for example in Vim).[26] From Unix terminal emulators.
#
#	CSI ? 2004 l 	Turn off bracketed paste mode. 

#
#	8-bit colors are:
#
#	ESC[ 38;5;(n) m Select foreground color
#	ESC[ 48;5;(n) m Select background color
#
#	  0-  7:  standard colors (as in ESC [ 30–37 m)
#	  8- 15:  high intensity colors (as in ESC [ 90–97 m)
#	 16-231:  6 × 6 × 6 cube (216 colors): 16 + 36 × r + 6 × g + b (0 = r, g, b = 5)
#	232-255:  grayscale from black to white in 24 steps
#
#	--------------------------------------------------------------------------------
#
#	The ITU's T.416 Information technology - Open Document
#	Architecture (ODA) and interchange format: Character
#	content architectures[35] uses ':' as separator characters
#	instead:
#
#	ESC[ 38:5:(n) m Select foreground color
#	ESC[ 48:5:(n) m Select background color
#
#	--------------------------------------------------------------------------------
#
#	24-bit
#
#	As "true color" graphic cards with 16 to 24 bits of color
#	became common, Xterm,[19] KDE's Konsole,[36] iTerm, as
#	well as all libvte based terminals[37] (including GNOME
#	Terminal) support 24-bit foreground and background color
#	setting.[better source needed][38]
#
#	ESC[ 38;2;(r);(g);(b) m Select RGB foreground color
#	ESC[ 48;2;(r);(g);(b) m Select RGB background color
#
#
#	--------------------------------------------------------------------------------
#
#	If the terminating character is '~', the first number must
#	be present and is a keycode number, the second number is
#	an optional modifier value. If the terminating character
#	is a letter, the letter is the keycode value, and the
#	optional number is the modifier value.
#	
#	The modifier value defaults to 1, and after subtracting
#	1 is a bitmap of modifier keys being pressed:
#	Meta-Ctrl-Alt-Shift. So, for example, <esc>[4;2~ is
#	Shift-End, <esc>[20~ is function key 9, <esc>[5C is
#	Ctrl-Right.
#	
#	vt sequences:
#
#	<esc>[1~    - Home        <esc>[16~   -             <esc>[31~   - F17
#	<esc>[2~    - Insert      <esc>[17~   - F6          <esc>[32~   - F18
#	<esc>[3~    - Delete      <esc>[18~   - F7          <esc>[33~   - F19
#	<esc>[4~    - End         <esc>[19~   - F8          <esc>[34~   - F20
#	<esc>[5~    - PgUp        <esc>[20~   - F9          <esc>[35~   - 
#	<esc>[6~    - PgDn        <esc>[21~   - F10         
#	<esc>[7~    - Home        <esc>[22~   -             
#	<esc>[8~    - End         <esc>[23~   - F11         
#	<esc>[9~    -             <esc>[24~   - F12         
#	<esc>[10~   - F0          <esc>[25~   - F13         
#	<esc>[11~   - F1          <esc>[26~   - F14         
#	<esc>[12~   - F2          <esc>[27~   -             
#	<esc>[13~   - F3          <esc>[28~   - F15         
#	<esc>[14~   - F4          <esc>[29~   - F16         
#	<esc>[15~   - F5          <esc>[30~   -             
#	
#	xterm sequences:
#	<esc>[A     - Up          <esc>[K     -             <esc>[U     -
#	<esc>[B     - Down        <esc>[L     -             <esc>[V     -
#	<esc>[C     - Right       <esc>[M     -             <esc>[W     -
#	<esc>[D     - Left        <esc>[N     -             <esc>[X     -
#	<esc>[E     -             <esc>[O     -             <esc>[Y     -
#	<esc>[F     - End         <esc>[1P    - F1          <esc>[Z     -
#	<esc>[G     - Keypad 5    <esc>[1Q    - F2       
#	<esc>[H     - Home        <esc>[1R    - F3       
#	<esc>[I     -             <esc>[1S    - F4       
#	<esc>[J     -             <esc>[T     - 

#
#	Who all are we referencing?
#
	$who = <<<EOD
<h2 class='font'>Also, A LOT of this was taken from :</h2>
<font class='e3'>https://en.wikipedia.org/wiki/ANSI_escape_code#Escape_sequences</font><br>
<font class='e3'>https://www.ascii-code.com/</font><br>
<font class='e3'>http://www.termsys.demon.co.uk/vtansi.htm/</font><br>
<font class='e3'>https://vt100.net/docs/vt100-ug/chapter3.html#S3.3.3</font><br>
<font class='e3'>http://ascii-table.com/ansi-escape-sequences.php</font><br>
<font class='e3'>http://www.injosoft.se/kontakta.asp</font><br>
<font class='e3'>Paul Flo Williams (paul-AT-frixxon.co.uk)</font><br>
<font class='e3'>https://theasciicode.com.ar/extended-ascii-code/lowercase-letter-a-acute-accent-ascii-code-160.html</font></br>
<font class='e3'>https://invisible-island.net/xterm/ctlseqs/ctlseqs.html</font></br>
EOD;

	$this->who = $who;

	$this->display_1 = [
		[ " ", "<b>Main Commands</b>" ],
		[ "0", "Reset all attributes" ],
		[ "1", "Bright" ],
		[ "2", "Dim" ],
		[ "4", "Underscore" ],
		[ "5", "Blink" ],
		[ "7", "Reverse" ],
		[ "8", "Hidden" ],
		[ " ", "<b>Foreground Colours</b>" ],
		[ "30", "Black" ],
		[ "31", "Red" ],
		[ "32", "Green" ],
		[ "33", "Yellow" ],
		[ "34", "Blue" ],
		[ "35", "Magenta" ],
		[ "36", "Cyan" ],
		[ "37", "White" ],
		[ " ", "<b>Background Colours</b>" ],
		[ "40", "Black" ],
		[ "41", "Red" ],
		[ "42", "Green" ],
		[ "43", "Yellow" ],
		[ "44", "Blue" ],
		[ "45", "Magenta" ],
		[ "46", "Cyan" ],
		[ "47", "White" ]
		];
}
################################################################################
#	__call(). Allows any function to be executed.
#
#	Notes:
#
#		array( <Return>, <Function name>, <Beginning text>,
#			<options>, <Ending text>, <info>, <text> )
#
################################################################################
function __call( $cmdline, $arguments )
{
#
#	The NAME is the number from the ANSI_CMDS array or ANSI documentation.
#
#	The NAME is comprised of the letter "f" followed by the id number in the
#	above documentation. No spaces. No funky characters. Just a simple "f###".
#	So like f70() would call THAT function. Or f41(6) would call THAT function
#	and pass in the number six(6). The value six can be just 6 or '6' or "6".
#	It is YOUR choice which to pass in to the function. Being lazy - I'll probably
#	use just f41(6).
#
	$dq = '"';
	if( preg_match("/^f/i", $cmdline) ){
		$name = substr( $cmdline, 0, 1 );					#	This gets the "F"
		$number = substr( $cmdline, 1, strlen($cmdline) );	#	This gets the ID
		}
		else {
			$name = substr( $cmdline, 0, 2 );					#	This get the "XT"
			$number = substr( $cmdline, 2, strlen($cmdline) );	#	This gets the ID
			}

	if( $name == "f" ){ $ansi_cmd = $this->ansi_cmds[$number]; }
		else { $ansi_cmd = $this->xterm_cmds[$number]; }
#
#	Used to use POPEN for this
#
#	$ansi_handle = $this->ansi_handle;

	if( !is_array($ansi_cmd) ){ die( "***** ERROR : Unknown command - $number" ); }

	$b = $ansi_cmd[2];	//	Beginning of the string
	$e = $ansi_cmd[4];	//	Ending of the string
#
#	The ARGUMENTS variable will contain ALL of the arguments.
#	So we need to put it all together in one string. The arguments
#	can come in many different formats like "#" or "#;#" or
#	arraY(#,#) as ints or strings or even a mixture of them.
#
#	Move all options into INFO.
#	This gets rid of everything - strings, numbers, etc...
#
	if( $ansi_cmd[3] && ($name == "f") ){
#
#	Certain of the DEC PRIVATE commands require specific ways of putting them
#	together. For instance - the "\e[P" command requires TWO arguments and
#	they have to be put together like this: "\e[Pn;PnR".
#
		if( preg_match("/f71/i", $cmdline) ){ $info = "$arguments[0];$dq$arguments[1]$dq"; }
			else { $info = implode( ";", $arguments ); }

		$info = str_replace( ";;", ";", $info );	//	Remove duplicate semi_colons
		$info = preg_replace( "/;$/", "", $info );	//	Remove trailing semi_colons
		}
		else if( $ansi_cmd[3] && ($name == "xt") ){
			if( preg_match("/xt36/i", $cmdline) ){
				$info = $this->xterm_xt36[$arguments[0]];	#	Add in the number from 1-14
				}
				else if( preg_match("/xt37/i", $cmdline) ){
					$info = $this->xterm_xt37[$arguments[0]];	#	Add in the number from 1-14
					}
				else { $info = implode( ";", $arguments ); }

			$info = str_replace( ";;", ";", $info );	//	Remove duplicate semi_colons
			$info = preg_replace( "/;$/", "", $info );	//	Remove trailing semi_colons
			}
		else { $info = ""; }
#
#	Now create the command and do it
#
	$cmd = $b . $info . $e;
	echo $cmd;
#
#	Are we supposed to send something back?
#
#		if( isset($ansi_cmd[0]) && $ansi_cmd[0] ){
#			$ciso = "$this->path_ciso/$this->prog_ciso";
#			if( isset($ansi_cmd[7]) ){ $a = $ansi_cmd[7]; }
#				else { $a = "%s"; }
#	
#			$handle = popen( $ciso, 'r' );
#			$ret = fscanf( $handle, $a );
#			pclose( $handle );
#	
#			return $ret;
#			}

	return true;
}
################################################################################
#	docs().	Generate all of the HTML documentation
################################################################################
public function docs( $file=null )
{
	$width = $this->width;
	$width2 = $width + 100;
	$max_width = $this->max_width;
	if( isset($this->buf) ){
		$buf = $this->buf[0] . "x" . $this->buf[1];
		}
		else {
			$buf = "80x60";
			}

	if( isset($this->win) ){
		$win = $this->win[0] . "x" . $this->win[1];
		}
		else {
			$win = "80x60";
			}

	$td = "align='right' width='$width2'";
	$border = "style='border:thin solid black;'";
	$version = $this->ver;
	$version = str_replace( '..', '.', $version );

	if( is_null($file) ){ $file = "./ansi_docs.htm"; }
#
#	Some spacing standards
#
	$space_20px = "20px";
	$space_25px = "25px";
	$space_40px = "40px";

	$doc = <<<EOD
<html>
<head>
<title>ANSI Documentation</title>
<style>
h1.font { font: 36pt normal serif; }
h1.border {border-top: 1px solid black; border-bottom: 1px solid black; width: 800px; }
h2.font { font: 30pt normal serif; }
h2.border {border-top: 1px solid black; border-bottom: 1px solid black; width: 800px; }
h2.font { font: 24pt normal serif; }
h3.border {border-top: 1px solid black; border-bottom: 1px solid black; width: 800px; }
h2.font { font: 18pt normal serif; }
h4.border {border-top: 1px solid black; border-bottom: 1px solid black; width: 800px; }
h2.font { font: 16pt normal serif; }
h5.border {border-top: 1px solid black; border-bottom: 1px solid black; width: 800px; }
ol.width { width: 800px; }
table.w1 { width: 250; }
table.w90 { width: 90%; }
table.w1200 { width: 1200px; }
hr.width { width: 800px; max-width: 800px; }
hr.m0	{ margin: 0px; }
td.l1 { text-align:left; }
td.c1 { text-align:center; }
td.w1 { width : 75%; }
td.w2 { width : 25%; }
td.w3 { width : $space_20px; }
td.w4 { width : 50%; }
td.t1 { font: normal 12pt serif; }
td.t2 { font: normal 14pt serif; }
td.t3 { font: normal 18pt serif; }
td.t4 { font: normal 24pt serif; }
td.t5 { font: normal 36pt serif; }
td.m1 { font: normal 12pt monospace; }
td.m2 { font: normal 14pt monospace; }
td.m3 { font: normal 18pt monospace; }
td.m4 { font: normal 24pt monospace; }
td.m5 { font: normal 36pt monospace; }
td.bold { font-weight: bold; }
td.serif { font: bold 12pt serif; }
p.m1 { margin-left: $space_20px; inline-size: 800px; overflow-wrap: break-word; }
p.m2 { margin-left: $space_20px; display: table; margin-right : auto; }
p.l1 { text-align:left; }
p.nobr { white-space: nowrap; }
tr.odd { background-color: #f0f0f0; }
tr.even { background-color: #ddddff; }
td.nowrap { white-space: nowrap; }
td.wrap { white-space: normal; }
td.line { border: 0px; }
td.pad5 { padding-left: 5px;padding-right: 5px; }
td.pad10 { padding-left: 10px;padding-right: 10px; }
table.tblb { border: 0px solid black;border-collapse:collapse; }
table.tblw { border: 0px solid white;border-collapse:collapse; }
table.tbln { border: 0px solid white;border-collapse:collapse; }
tr.trb { border: 1px solid black; border-collapse:collapse; }
tr.trb0 { border: 0px solid black; border-collapse:collapse; }
tr.trw { border: 1px solid white; border-collapse:collapse; }
tr.trn { border: 0px solid white; border-collapse:collapse; }
td.tdb { border: 1px solid black; border-collapse:collapse; }
td.tdb0 { border: 0px solid black; border-collapse:collapse; }
td.tdw { border: 1px solid white; border-collapse:collapse; }
td.tdw0 { border: 0px solid white; border-collapse:collapse; }
td.tdn {
		border: 0px solid white;
		border-collapse:collapse;
		color:yellow;
		background-color:red;
		font-weight:bold;
		width:$space_20px;
		height: $space_25px;
		vertical-align: middle;
		}

td.tds {
		border: 0px solid white;
		border-collapse:collapse;
		color:black;
		background-color:white;
		font-weight:normal;
		height: $space_25px;
		vertical-align: middle;
		}

font.e1 { font: normal 14px monospace; padding-left: $space_20px; }
font.e2 { font: normal 14px monospace; padding-left: $space_40px; }
font.e3 { font: normal 14px monospace; padding-left: $space_40px; }
font.t1 { font: bold 12pt serif; }
font.t2 { font: bold 14pt serif; }
font.t3 { font: bold 18pt serif; }
font.t4 { font: bold 24pt serif; }
font.t5 { font: bold 36pt serif; }
font.m1 { font: bold 12pt monospace; }
font.m2 { font: bold 14pt monospace; }
font.m3 { font: bold 18pt monospace; }
font.m4 { font: bold 24pt monospace; }
font.m5 { font: bold 36pt monospace; }
</style>
</head>
<body width="800px">
<p style="page-break-before: always">
<h1 class='border font'>ANSI Information</h1>
<TABLE CELLPADDING="3" CELLSPACING="0" class="tblw w1"><tbody>
<tr class='trw'><td width='$width'> </td>
	<td class='tdb l1 w1'><b>VERSION</b></td>
	<td class='tdb c1 w2'>$version</td></tr>
<tr class='trw'><td width='$width'> </td>
	<td class='tdb l1 w1'><b>CLICOLOR</b></td>
	<td class='tdb c1 w2'>$this->clicolor</td></tr>
<tr class='trw'><td width='$width'> </td>
	<td class='tdb l1 w1'><b>Buffer Size</b></td>
	<td class='tdb c1 w2'>$buf</td></tr>
<tr class='trw'><td width='$width'> </td>
	<td class='tdb l1 w1'><b>Window Size</b></td>
	<td class='tdb c1 w2'>$win</td></tr>
</tbody></table>
<p>
EOD;

	$doc .= $this->docs_bit34();
	$doc .= $this->docs_ansi();
	$doc .= $this->docs_sgr();
	$doc .= $this->docs_ascii();
	$doc .= $this->docs_display_1();
	$doc .= $this->docs_vt100();
	$doc .= $this->docs_xterm();

	$doc .= <<<EOD
</body>
</html>
EOD;

	file_put_contents( $file, $doc );
}
################################################################################
#	docs_bit34(). Dump the bit34 documentation as html.
################################################################################
private function docs_bit34()
{
	$width = $this->width;
	$max_width = $this->max_width;
#
#	Because the array is a variable length = we have to find out how many
#	cells are in it.
#
	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>BIT 34 Documentation</h1>

$this->who
<p>
EOD;

	$title = [ "Name", "FG Code", "BG Code", "VGA", "Windows Console", 
		"Windows PowerShell", 'Visual Studio Code<br>Debug Console<br>(Default Dark+ Theme)',
		"Windows 10 Console<br>PowerShell 6", 'Terminal.app',
		"PuTTY", "mIRC", "xterm", "Ubuntu" ];

	$doc .= $this->createTable( "BIT 34", $title, $this->bit34, 4 );

	return $doc;
}
################################################################################
#	docs_ansi(). Dump the ansi documentation as html.
################################################################################
private function docs_ansi()
{
	$width = $this->width;
	$max_width = $this->max_width;

	$doc = <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>ANSI Documentation</h1>

$this->who

<h2 class='font'>Instructions</h2>

<p class='m1'>The way this program works is easy. Really - really easy.</p>

<p class='m1'>First - you declare a variable and it becomes a pointer
to the class. Like so:</p>

<font class='e1'>\$var = new class_ansi();</font><p>

<font class='e1'>or</font><p>

<font class='e1'>\$var = \$GLOBALS['class']['ansi'];</font><p>

EOD;

	$doc .= $this->createNote( null, "ALL of my classes create a GLOBALS entry under the" .
		"CLASS entry (as above). This is so you don't have to keep" .
		"using the NEW command and instead can just set a variable" .
		"to the class. Also, the GLOBALS array is really globally" .
		"defined which means you can use the class in functions" .
		"without having to pass it in to the function itself.", 800 );

	$doc .= <<<EOD
<p>

<p class='m1'>Then you look up the funcion ID number in the ANSI
table. (That is the column with the "f###" number in
it. You then call THAT function. The reason I'm using the
"f###" id numbers is because then you don't have to remember
the hundreds of function names.</p><p>

<p class='m1'>For example, let us say you want to move the cursor
to position (5,5). You would do this with the f19
function. Like so:<p>

<font class='e1'>\$var->f19(5,5);</font><p>

<p class='m1'>This would make the cursor move to the (5,5)
location.</p><p>

<p class='m1'>Let us say you need to get a value back. This is
extremely simple. All you do is to call the proper function
(again - look at the list to find it) and then just call
that function and give some place for the information to
go. Like this:</p><p>

<font class='e1'>\$info = \$var->f70();</font><p>

<p class='m1'>This returns where the cursor currenly is located. This
SPECIAL function already has the proper string to look for
and so it does the fscanf() function on it to return an
array with two entries in it. (In other words : (Y,X).) So
the above returns:</p><p>

<font class='e1'>\$info[0] and \$info[1] which are X and Y.</font><p>

<p class='m1'>If, instead, you wanted to use function f41 and send
over the correct string, THEN you would get just a string
back. The same string found on f70. So:</p><p>

<font class='e1'>\$info = \$var->f41(6);</font><p>

<font class='e1'>or</font><p>

<font class='e1'>\$info = \$var->f41('6');</font><p>

<font class='e1'>or</font><p>

<font class='e1'>\$info = \$var->f41("6");</font><p>

<p class='m1'>Will only just return a string (%s) and you
would have to use fscanf to get the information out of
the string. Now - you are probably going "Well, is the
program smart enough to know that f41(6) is the same as
f70()? No. It doesn't. All the program does is to call
whatever function you ask it to call and that is all. Or
to answer that in another way - there are no "smarts"
to the class. All it does is to look up in the table what
it should do and it does just that and returns. It either
returns TRUE or it returns whatever value it might have
gotten. Thus, function f70 is just as dumb as the f41()
function. Function f41 says to return a string (%s)
and that is what it does. Function f70 says to return
the "ESC[%d;%dR" string's value and that is what it
does. Period. Full Stop.<p>

<h2 class='font'>Notes</h2>

<p class='m1>All information being passed INTO these functions are
STRINGS. These 'strings' are either going to be a number
(like '5') or a string-string (like 'Hi!'). IT IS UP TO
YOU to figure out what you need to send. It is not hard. In
other words - read the documentation.</p><p>

<p class='m1'>Also note
that numeric strings can JUST BE PASSED IN AS
NUMBERS. PHP will recognize the number 5 as a string
also. (So it is both a 5 and a '5' in the function
itself.)</p><p>

<p class='m1'>Note that function f43() has three separate ways
to clear the screen.  Also remember these are for MY
computer and should work the same on YOUR computer. Check
the VT100 documentation on how it should operate. Here is
what they are supposed to do.  Option 0 clears the screen
from where the cursor is to the end of the screen.  Option
1 can clear from the location where the cursor resides to
the top of the screen but the cursor does not move. Option
2 moves the cursor to the top left corner (sometimes called
the ORIGIN) and the entire screen is cleared.</p><p>

<p class='m1'>YOU (and anyone else who uses this class) should update
the documentation in this program (changes, updates,
corrections) as you can and then send it back to me at
markem-AT-sim1.us. I am only human and know I make mistakes
so I will welcome any/all changes and will incorporate them
into the class as quickly as I can OR send you a message
about why I did not. So I wish to thank you ahead of time
for sending them to me. Thank you very much.</p>

<h2 class='font'>Examples</h2>
<font class='e2'>\$ansi-&gt;f16(5);</font><br>
<font class='e2'>\$ansi-&gt;f16('5');</font><br>
<font class='e2'>\$ansi-&gt;f16("5");</font><br>
<font class='e2'>\$ansi-&gt;f2(128,128,128);</font><p>

EOD;

	$doc .= $this->createNote( null, "YOU are just passing in the arguments NOT the " .
		"semicolons, commas, or anything else <b>UNLESS</b> you " .
		"are using the %s type of input - THEN you can put whatever " .
		"you want into the string.", 800 );

	$title = [ "Return", "Function Name", "Beginning Text",
		"Pass Something In?", "Ending Text", "Info", "Text", "Return Type" ];

	$doc .= $this->createTable( "Ansi", $title, $this->ansi_cmds, 4, true );

	return $doc;
}
################################################################################
#	docs_sgr(). Dump the sgr documentation as html.
################################################################################
private function docs_sgr()
{
	$width = $this->width;
	$max_width = $this->max_width;

	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>SGR Command Documentation</h1>

$this->who

<h2 class='font'>Notes</h2>

<p class='m1'>All information being shown are STRINGS.</p>

<p>
EOD;

	$title = [ "Code", "Effect", "Note" ];

	$doc .= $this->createTable( "SGR", $title, $this->sgr_cmds, 4, true );

	return $doc;
}
################################################################################
#	docs_ascii(). Dump the ascii_codes documentation as html.
################################################################################
private function docs_ascii()
{
	$width = $this->width;
	$height = "20";
	$max_width = $this->max_width;

	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>ASCII Code - The extended ASCII table</h1>

$this->who

<h2 class='font'>Notes</h2>

<p class='m1'>This part of the document comes directly from Injosoft
located at http://www.injosoft.se/kontakta.asp. This is
an excellently laid out ASCII table.</p><p>

<p class='m1'>ASCII, stands for American Standard Code for
Information Interchange. It's a 7-bit character code
where every single bit represents a unique character. On
this webpage you will find 8 bits, 256 characters,
ASCII table according to Windows-1252 (code page 1252)
which is a superset of ISO 8859-1 in terms of printable
characters. In the range 128 to 159 (hex 80 to 9F), ISO/IEC
8859-1 has invisible control characters, while Windows-1252
has writable characters. Windows-1252 is probably the
most-used 8-bit character encoding in the world.</p><p style="text-indent:20px;">
From : Injosoft (Sweden) - Webbdesign & Systemveckling</p><p>

<h3 class='font'>ASCII control characters (character code 0-31)</h3>

<p class='m1'>The first 32 characters in the ASCII-table are
unprintable control codes and are used to control
peripherals such as printers.</p><p>

<h3 class='font'>ASCII printable characters (character code 32-127)</h3>

<p class='m1'>Codes 32-127 are common for all the different variations
of the ASCII table, they are called printable characters,
represent letters, digits, punctuation marks, and a few
miscellaneous symbols. You will find almost every character
on your keyboard. Character 127 represents the command DEL.</p><p>

<h3 class='font'>The extended ASCII codes (character code 128-255)</h3>

<p class='m1'>There are several different variations of the 8-bit
ASCII table. The table below is according to Windows-1252
(CP-1252) which is a superset of ISO 8859-1, also called
ISO Latin-1, in terms of printable characters, but
differs from the IANA's ISO-8859-1 by using displayable
characters rather than control characters in the 128 to
159 range. Characters that differ from ISO-8859-1 is marked
by light blue color.</p><p>

<p class='m1'>PS : You may want to go visit their website
to learn more</p><p>

<p>
EOD;

	$title = [ "DEC", "OCT", "HEX", "BIN", "Symbol", "HTML Number",
		"HTML Name", "Description" ];

	$doc .= $this->createTable( "ASCII", $title, $this->ascii_codes, 4, true );

	return $doc;
}
################################################################################
#	docs_display_1(). Dump the first set of standard display commands.
################################################################################
private function docs_display_1()
{
	$width = $this->width;
	$height = "20";
	$max_width = $this->max_width;

	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>First Standard Display Formats</h1>

$this->who
<p>

<p class='m1'>Use these commands to send the appropriate id number to a terminal
So you get all of the colors and blink letters and such. This is
used with the ANSI Table "f61" command found above.</p>
<p class='m1'>
<table class='tbln' width='885px'><tbody><tr class='trn'>
<td class='tdn pad10'>NOTE</td>
<td class='tds'>&nbsp;</td>
<td class='tds' width='885px'>You need to specify these as
FG, BG, and OC. Or, to put that another way, the ForeGround
color, the BackGround color, and the Optional Command
(Bright, Dim, Underscore, Blink, Reverse, and Hidden). To reset everything back to the default simply means
that you use "0,0,0" which will reset all three.
</tr></tbody></table>
</td></tr></tbody></table>
</p><p>

EOD;

	$title = [ "ID", "Meaning" ];
	$doc .= $this->createTable( "First Standard Display", $title, $this->display_1, 2 );

	return $doc;
}
################################################################################
#	docs_display_2(). Dump the first set of standard display commands.
################################################################################
private function docs_vt100()
{
	$width = $this->width;
	$height = "20";
	$max_width = $this->max_width;

	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>VT100 Escape Control Sequences<br>
Valid ANSI Mode Control Sequences</h1>
$this->who
<h2 class='font'>Definitions</h2>
EOD;

	$doc .= $this->createNote( null,
		"The DEC information that follows is, in some cases, very " .
		"convoluted.  They are like a worm burrowing through the " .
		"earth. So you go HERE and then THERE and then someplace " .
		"else to put the command together.  So if you get confused " .
		"- believe me - I've been there. You will have to read - " .
		"re-read - and re-read it again in order to make sense " .
		"of it. For instance, the:", 800 );

	$doc .=  <<<EOD
<p>

<h4 class='font'>DECARM - Auto Repeat Mode (DEC Private)</h4>

<p class='m1'>function requires you to look for HOW to
do these commands by going through the documentation and
then figuring out how to use it.</p><p>

<p class='m1'>The following listing defines the basic elements of
the ANSI mode control sequences. A more complete listing
appears in Appendix A. This document was produced by Paul
Flo Williams (paul-AT-frixxon.co.uk) and is copyrighted
1998-2020. It is referenced here because it is the most
complete document on the VT100 terminal which ansi
emulates.</p><p>

<h3 class='font'>Control Sequence Introducer (CSI)</h3>

<p class='m1'>An escape sequence that provides supplementary controls
and is itself a prefix affecting the interpretation of a
limited number of contiguous characters. In the VT100 the
CSI is ESC [.</p><p>

<h4 class='font'>Parameter</h4>

<p class='m1'>A string of zero or more decimal characters which represent
a single value. Leading zeroes are ignored. The decimal
characters have a range of 0 (608) to 9 (718).</p><p>

<p class='m1'>The value so represented.</p><p>

<h4 class='font'>Numeric Parameter</h4>

<p class='m1'>A parameter that represents a number, designated by Pn.</p><p>

<h4 class='font'>Selective Parameter</h4>

<p class='m1'>A parameter that selects a subfunction from a specified
list of subfunctions, designated by Ps. In general, a
control sequence with more than one selective parameter
causes the same effect as several control sequences,
each with one selective parameter, e.g., CSI Psa; Psb;
Psc F is identical to CSI Psa F CSI Psb F CSI Psc F.</p><p>

<h4 class='font'>Parameter String</h4>

<p class='m1'>A string of parameters separated by a semicolon (738).</p><p>

<h4 class='font'>Default</h4>

<p class='m1'>A function-dependent value that is assumed when no explicit
value, or a value of 0, is specified.</p><p>

<h4 class='font'>Final character</h4>

<p class='m1'>A character whose bit combination terminates an escape or
control sequence.</p><p>

<h4 class='font'>Examples:</h4>

<p class='m1'>Control sequence for double-width line (DECDWL) ESC # 6</p><p>

<h3 class='font'>Control Sequences</h3>

<p class='m1'>All of the following escape and control sequences are
transmitted from the host computer to the VT100 unless
otherwise noted. All of the control sequences are a subset
of those specified in ANSI X3.64-1977 and ANSI X3.41-1974.</p><p>

<h3 class='font'>CPR – Cursor Position Report – VT100 to Host</h3>

<font class='e1'>ESC [ Pn ; Pn R 	default value: 1</font><p>

<p class='m1'>The CPR sequence reports the active position by means of
the parameters. This sequence has two parameter values,
the first specifying the line and the second specifying the
column. The default condition with no parameters present,
or parameters of 0, is equivalent to a cursor at home
position.</p><p>

<p class='m1'>The numbering of lines depends on the state of the Origin
Mode (DECOM).</p><p>

<p class='m1'>This control sequence is solicited by a device status
report (DSR) sent from the host.</p><p>

<h3 class='font'>CUB – Cursor Backward – Host to VT100 and VT100 to Host</h3>

<font class='e1'>ESC [ Pn D 	default value: 1</font><p>

<p class='m1'>The CUB sequence moves the active position to the left. The
distance moved is determined by the parameter. If the
parameter value is zero or one, the active position is
moved one position to the left. If the parameter value
is n, the active position is moved n positions to the
left. If an attempt is made to move the cursor to the
left of the left margin, the cursor stops at the left
margin. Editor Function</p><p>

<h3 class='font'>CUD – Cursor Down – Host to VT100 and VT100 to Host</h3>

<font class='e1'>ESC [ Pn B 	default value: 1</font><p>

<p class='m1'>The CUD sequence moves the active position downward without
altering the column position. The number of lines moved is
determined by the parameter. If the parameter value is zero
or one, the active position is moved one line downward. If
the parameter value is n, the active position is moved n
lines downward. In an attempt is made to move the cursor
below the bottom margin, the cursor stops at the bottom
margin. Editor Function</p><p>

<h3 class='font'>CUF – Cursor Forward – Host to VT100 and VT100 to Host</h3>

<font class='e1'>ESC [ Pn C 	default value: 1</font><p>

<p class='m1'>The CUF sequence moves the active position to the
right. The distance moved is determined by the parameter. A
parameter value of zero or one moves the active position
one position to the right. A parameter value of n moves the
active position n positions to the right. If an attempt is
made to move the cursor to the right of the right margin,
the cursor stops at the right margin. Editor Function</p><p>

<h3 class='font'>CUP – Cursor Position</h3>

<font class='e1'>ESC [ Pn ; Pn H 	default value: 1</font><p>

<p class='m1'>The CUP sequence moves the active position to the position
specified by the parameters. This sequence has two
parameter values, the first specifying the line position
and the second specifying the column position. A parameter
value of zero or one for the first or second parameter
moves the active position to the first line or column
in the display, respectively. The default condition with
no parameters present is equivalent to a cursor to home
action. In the VT100, this control behaves identically
with its format effector counterpart, HVP. Editor Function</p><p>

<p class='m1'>The numbering of lines depends on the state of the Origin
Mode (DECOM).  CUU – Cursor Up – Host to VT100 and VT100
to Host ESC [ Pn A	default value: 1</p><p>

<p class='m1'>Moves the active position upward without altering the
column position. The number of lines moved is determined
by the parameter. A parameter value of zero or one moves
the active position one line upward. A parameter value of
n moves the active position n lines upward. If an attempt
is made to move the cursor above the top margin, the cursor
stops at the top margin. Editor Function</p><p>

<h3 class='font'>DA – Device Attributes</h3><p>

<font class='e1'>ESC [ Pn c 	default value: 0</font><p>

<ol class='width'>
<li>The host requests the VT100 to send a device attributes
(DA) control sequence to identify itself by sending the DA
control sequence with either no parameter or a parameter
of 0.</li><p>

<li>Response to the request described above (VT100 to host)
is generated by the VT100 as a DA control sequence with
the numeric parameters as follows:</li></ol><p>

EOD;

	$title = [ "ID", "Meaning" ];

	$table = [
		[ "No options", "ESC [?1;0c" ],
		[ "Processor option (STP)", "ESC [?1;1c" ],
		[ "Advanced video option (AVO)", "ESC [?1;2c" ],
		[ "AVO and STP", "ESC [?1;3c" ],
		[ "Graphics option (GPO)", "ESC [?1;4c" ],
		[ "GPO and STP", "ESC [?1;5c" ],
		[ "GPO and AVO", "ESC [?1;6c" ],
		[ "GPO, STP and AVO", "ESC [?1;7c" ]
		];

	$doc .= $this->createTable( "First DA Control Sequences", $title, $table, 1 );

	$doc .= <<<EOD
<p>
<h3 class='font'>DECALN – Screen Alignment Display (DEC Private)</h3>

<font class='e1'>ESC # 8</font><p>

<p class='m1'>This command fills the entire screen area with uppercase
Es for screen focus and alignment. This command is used
by DEC manufacturing and Field Service personnel.</p><p>

<h3 class='font'>DECANM – ANSI/VT52 Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode
(SM) and reset mode (RM) control sequences. The reset
state causes only VT52 compatible escape sequences to be
interpreted and executed. The set state causes only ANSI
"compatible" escape and control sequences to be interpreted
and executed.</p><p>

<h3 class='font'>DECARM – Auto Repeat Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. The reset state
causes no keyboard keys to auto-repeat. The set state
causes certain keyboard keys to auto-repeat.</p><p>

<h3 class='font'>DECAWM – Autowrap Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. The reset state
causes any displayable characters received when the cursor
is at the right margin to replace any previous characters
there. The set state causes these characters to advance to
the start of the next line, doing a scroll up if required
and permitted.</p><p>

<h3 class='font'>DECCKM – Cursor Keys Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. This mode is only
effective when the terminal is in keypad application mode
(see DECKPAM) and the ANSI/VT52 mode (DECANM) is set (see
DECANM). Under these conditions, if the cursor key mode
is reset, the four cursor function keys will send ANSI
cursor control commands. If cursor key mode is set, the
four cursor function keys will send application functions.</p><p>

<h3 class='font'>DECCOLM – Column Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. The reset state
causes a maximum of 80 columns on the screen. The set
state causes a maximum of 132 columns on the screen.</p><p>

<h3 class='font'>DECDHL – Double Height Line (DEC Private)</h3>

<font class='e1'>Top Half: ESC # 3</font><p>

<font class='e1'>Bottom Half: ESC # 4</font><p>

<p class='m1'>These sequences cause the line containing the
active position to become the top or bottom half of a
double-height double-width line. The sequences must be used
in pairs on adjacent lines and the same character output
must be sent to both lines to form full double-height
characters. If the line was single-width single-height,
all characters to the right of the center of the screen are
lost. The cursor remains over the same character position
unless it would be to the right of the right margin,
in which case it is moved to the right margin.</p><p>

EOD;

	$doc .= $this->createNote( null,
		"The use of double-width characters reduces the number " .
		"of characters per line by half.", 800 );

	$doc .= <<<EOD
<h3 class='font'>DECDWL – Double-Width Line (DEC Private)</h3>

<font class='e1'>ESC # 6</font><p>

<p class='m1'>This causes the line that contains the active position
to become double-width single-height. If the line was
single-width single-height, all characters to the right
of the screen are lost. The cursor remains over the
same character position unless it would be to the right
of the right margin, in which case, it is moved to the
right margin.</p><p>

EOD;

	$doc .= $this->createNote( null,
		"The use of double-width characters reduces the number " .
		"of characters per line by half.", 800 );

	$doc .= <<<EOD

<h3 class='font'>DECID – Identify Terminal (DEC Private)</h3>

<font class='e1'>ESC Z</font><p>

<p class='m1'>This sequence causes the same response as the ANSI device
attributes (DA). This sequence will not be supported in
future DEC terminals, therefore, DA should be used by any
new software.</p><p>

<h3 class='font'>DECINLM – Interlace Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. The reset state
(non-interlace) causes the video processor to display 240
scan lines per frame. The set state (interlace) causes the
video processor to display 480 scan lines per frame. There
is no increase in character resolution.</p><p>

<h3 class='font'>DECKPAM – Keypad Application Mode (DEC Private)</h3>

<font class='e1'>ESC = 	 <p>

<p class='m1'>The auxiliary keypad keys will transmit control sequences
as defined in Tables 3-7 and 3-8.</p><p>

<h3 class='font'>DECKPNM – Keypad Numeric Mode (DEC Private)</h3>

<font class='e1'>ESC ></font><p>

<p class='m1'>The auxiliary keypad keys will send ASCII codes
corresponding to the characters engraved on the keys.</p><p>

<h3 class='font'>DECLL – Load LEDS (DEC Private)</h3>

<font class='e1'>ESC [ Ps q 	default value: 0</font><p>

<p class='m1'>Load the four programmable LEDs on the keyboard according
to the parameter(s).</p><p>

EOD;

	$title = [ "Parameter", "Parameter Meaning" ];

	$table = [
		[ "0", "Clear LEDs L1 through L4" ],
		[ "1", "Light L1" ],
		[ "2", "Light L2" ],
		[ "3", "Light L3" ],
		[ "4", "Light L4" ]
		];

	$doc .= $this->createTable( "DECLL", $title, $table, 1 );

	$doc .= <<<EOD
<p>

<font class='e1'>LED numbers are indicated on the keyboard.</font><p>

<h3 class='font'>DECOM – Origin Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to
set mode (SM) and reset mode (RM) control sequences. The
reset state causes the origin to be at the upper-left
character position on the screen. Line and column numbers
are, therefore, independent of current margin settings. The
cursor may be positioned outside the margins with a cursor
position (CUP) or horizontal and vertical position (HVP)
control.</p><p>

<p class='m1'>The set state causes the origin to be at the upper-left
character position within the margins. Line and column
numbers are therefore relative to the current margin
settings. The cursor is not allowed to be positioned
outside the margins.</p><p>

<p class='m1'>The cursor is moved to the new home position when this
mode is set or reset.</p><p>

<p class='m1'>Lines and columns are numbered consecutively, with the
origin being line 1, column 1.</p><p>

<h3 class='font'>DECRC – Restore Cursor (DEC Private)</h3>

<font class='e1'>ESC 8</font><p>

<p class='m1'>This sequence causes the previously saved cursor position,
graphic rendition, and character set to be restored.</p><p>

<h3 class='font'>DECREPTPARM – Report Terminal Parameters</h3>

<font class='e1'>ESC [ &lt;sol&gt;; &lt;par&gt;; &lt;nbits&gt;; &lt;xspeed&gt;; &lt;rspeed&gt;; &lt;clkmul&gt;;
&lt;flags&gt; x</font><p>

<p class='m1'>These sequence parameters are explained below in the
DECREQTPARM sequence.</p><p>

<h3 class='font'>DECREQTPARM – Request Terminal Parameters</h3>

<font class='e1'>ESC [ <sol> x</font><p>

<p class='m1'>The sequence DECREPTPARM is sent by the terminal controller
to notify the host of the status of selected terminal
parameters. The status sequence may be sent when requested
by the host or at the terminal’s discretion. DECREPTPARM is
sent upon receipt of a DECREQTPARM. On power-up or reset,
the VT100 is inhibited from sending unsolicited reports.</p><p>

<p class='m1'>The meanings of the sequence parameters are:</p><p>

EOD;

	$title = [ "Parameter", "Value", "Meaning" ];

	$table = [
		[ "&lt;sol&gt;", "0 or none", "This message is a request (DECREQTPARM) and the " .
			"terminal will be allowed to send unsolicited reports. (Unsolicited reports " .
			"are sent when the terminal exits the SET-UP mode)." ],
		[ " ", 1, "This message is a request; from now on the terminal may only report " .
			"in response to a request." ],
		[ " ", 2, "This message is a report (DECREPTPARM)." ],
		[ " ", 3, "This message is a report and the terminal is only reporting on request." ],
		[ "&lt;par&gt;", 1, "No parity set" ],
		[ " ", 4, "Parity is set and odd" ],
		[ " ", 5, "Parity is set and even" ],
		[ "&lt;nbits&gt;", 1, "8 bits per character" ],
		[ " ", 2, "7 bits per character" ],
		[ "<tborder=5>", "<tborder=5>", "<tborder=5>" ],
		[ " ", "&lt;xspeed&gt;", "&lt;rspeed&gt;" ],
		[ " ",  0, "50 - Bits per second" ],
		[ " ",  8, "75 - Bits per second" ],
		[ " ",  16, "110 - Bits per second" ],
		[ " ",  24, "134.5 - Bits per second" ],
		[ " ",  32, "150 - Bits per second" ],
		[ " ",  40, "200 - Bits per second" ],
		[ " ",  48, "300 - Bits per second" ],
		[ " ",  56, "600 - Bits per second" ],
		[ " ",  64, "1200 - Bits per second" ],
		[ " ",  72, "1800 - Bits per second" ],
		[ " ",  80, "2000 - Bits per second" ],
		[ " ",  88, "2400 - Bits per second" ],
		[ " ",  96, "3600 - Bits per second" ],
		[ " ",  104, "4800 - Bits per second" ],
		[ " ",  112, "9600 - Bits per second" ],
		[ " ",  120, "19200 - Bits per second" ],
		[ "<tborder=5>", "<tborder=5>", "<tborder=5>" ],
		[ "&lt;clkmul&gt;", 1, "The bit rate multiplier is 16." ],
		[ "&lt;flags&gt;", "0-15", "This value communicates the four switch " .
			"values in block 5 of SET UP B, which are only visible" .
			"to the user when an STP option is installed. These bits" .
			"may be assigned for an STP device. The four bits are a" .
			"decimal-encoded binary number." ]
		];

	$doc .= $this->createTable( "DECREQTPARM", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3 class='font'>DECSC – Save Cursor (DEC Private)</h3>

<font class='e1'>ESC 7</font><p>

<p class='m1'>This sequence causes the cursor position, graphic
rendition, and character set to be saved. (See DECRC).</p><p>

<h3 class='font'>DECSCLM – Scrolling Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM)
and reset mode (RM) control sequences. The reset state
causes scrolls to "jump" instantaneously. The set state
causes scrolls to be "smooth" at a maximum rate of six
lines per second.</p><p>

<h3 class='font'>DECSCNM – Screen Mode (DEC Private)</h3>

<p class='m1'>This is a private parameter applicable to set mode (SM) and
reset mode (RM) control sequences. The reset state causes
the screen to be black with white characters. The set
state causes the screen to be white with black characters.</p><p>

<h3 class='font'>DECSTBM – Set Top and Bottom Margins (DEC Private)</h3>

<font class='e1'>ESC [ Pn; Pn r 	default values: see below</font><p>

<p class='m1'>This sequence sets the top and bottom margins to define
the scrolling region. The first parameter is the line
number of the first line in the scrolling region; the
second parameter is the line number of the bottom line
in the scrolling region. Default is the entire screen (no
margins). The minimum size of the scrolling region allowed
is two lines, i.e., the top margin must be less than the
bottom margin. The cursor is placed in the home position
(see Origin Mode DECOM).</p><p>

<h3 class='font'>DECSWL – Single-width Line (DEC Private)</h3>

<font class='e1'>ESC # 5</font><p>

<p class='m1'>This causes the line which contains the active position to
become single-width single-height. The cursor remains on
the same character position. This is the default condition
for all new lines on the screen.</p><p>

<h3 class='font'>DECTST – Invoke Confidence Test</h3>

<font class='e1'>ESC [ 2 ; Ps y</font><p>

<p class='m1'>Ps is the parameter indicating the test to be done. Ps is
computed by taking the weight indicated for each desired
test and adding them together. If Ps is 0, no test is
performed but the VT100 is reset.</p><p>

EOD;

	$title = [ "Test", "Weight" ];

	$table = [
		[ "Power up self-test (ROM check sum, RAM, NVR<br>keyboard and AVO if installed)", 1 ],
		[ "Data Loop Back", "2<br>(loop back connector required)" ],
		[ "EIA modem control test", "4<br>(loop back connector required)" ],
		[ "Repeat Selected Test(s)", "" ],
		[ "indefinitely (until failure or power off)", 8 ]
		];

	$doc .= $this->createTable( "DECTST", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3 class='font'>DSR – Device Status Report</h3>

<font class='e1'>ESC [ Ps n 	default value: 0</font><p>

<p class='m1'>Requests and reports the general status of the VT100
according to the following parameter(s).</p><p>

EOD;

	$title = [ "Parameter" , "Parameter Meaning" ];

	$table = [
		[ 0, "Response from VT100 - Ready,No malfunctions detected (default)" ],
		[ 3, "Response from VT100 - Malfunction - retry" ],
		[ 5, "Command from host - Please report<br>status (using a DSR control sequence)" ],
		[ 6, "Command from host - Please report<br>active position (using a CPR control sequence)" ]
		];

	$doc .= $this->createTable( "DSR", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>DSR with a parameter value of 0 or 3 is always sent as a
response to a requesting DSR with a parameter value of 5.</p><p>

<h3 class='font'>ED – Erase In Display</h3><p>

<font class='e1'>ESC [ Ps J 	default value: 0</font><p>

<p class='m1'>This sequence erases some or all of the characters in
the display according to the parameter. Any complete line
erased by this sequence will return that line to single
width mode. Editor Function</p><p>

EOD;

	$title = [ "Parameter" , "Parameter Meaning" ];

	$table = [
		[ 0, "Erase from the active position to the end of the screen, inclusive (default)" ],
		[ 1, "Erase from start of the screen to the active position, inclusive" ],
		[ 2, "Erase all of the display - all lines are erased, " .
			"changed to single-width, and the cursor does not move." ]
		];

	$doc .= $this->createTable( "ED", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3 class='font'>EL – Erase In Line</h3>

<font class='e1'>ESC [ Ps K 	default value: 0</font><p>

<p class='m1'>Erases some or all characters in the active line according
to the parameter. Editor Function</p><p>

EOD;

	$title = [ "Parameter" , "Parameter Meaning" ];

	$table = [
		[ 0, "Erase from the active position to the end of the line, inclusive (default)" ],
		[ 1, "Erase from the start of the screen to the active position, inclusive" ],
		[ 2, "Erase all of the line, inclusive" ]
		];

	$doc .= $this->createTable( "EL", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3 class='font'>HTS – Horizontal Tabulation Set</h3>

<font class='e1'>ESC H</font><p>

<p class='m1'>Set one horizontal stop at the active position. Format
Effector</p><p>

<h3 class='font'>HVP – Horizontal and Vertical Position</h3>

<font class='e1'>ESC [ Pn ; Pn f 	default value: 1</font><p>

<p class='m1'>Moves the active position to the position specified by
the parameters. This sequence has two parameter values,
the first specifying the line position and the second
specifying the column. A parameter value of either zero or
one causes the active position to move to the first line or
column in the display, respectively. The default condition
with no parameters present moves the active position to
the home position. In the VT100, this control behaves
identically with its editor function counterpart, CUP. The
numbering of lines and columns depends on the reset or
set state of the origin mode (DECOM). Format Effector</p><p>

<h3 class='font'>IND – Index</h3>

<font class='e1'>ESC D</font><p>

<p class='m1'>This sequence causes the active position to move downward
one line without changing the column position. If the
active position is at the bottom margin, a scroll up is
performed. Format Effector</p><p>

<h3 class='font'>LNM – Line Feed/New Line Mode</h3>

<p class='m1'>This is a parameter applicable to set mode (SM) and reset
mode (RM) control sequences. The reset state causes the
interpretation of the line feed (LF), defined in ANSI
Standard X3.4-1977, to imply only vertical movement
of the active position and causes the RETURN key (CR)
to send the single code CR. The set state causes the LF
to imply movement to the first position of the following
line and causes the RETURN key to send the two codes (CR,
LF). This is the New Line (NL) option.</p><p>

<p class='m1'>This mode does not affect the index (IND), or next line
(NEL) format effectors.</p><p>

<h3 class='font'>NEL – Next Line</h3>

<font class='e1'>ESC E</font><p>

<p class='m1'>This sequence causes the active position to move to
the first position on the next line downward. If the
active position is at the bottom margin, a scroll up is
performed. Format Effector</p><p>

<h3 class='font'>RI – Reverse Index</h3>

<font class='e1'>ESC M</font><p>

<p class='m1'>Move the active position to the same horizontal position
on the preceding line. If the active position is at the
top margin, a scroll down is performed. Format Effector</p><p>

<h3 class='font'>RIS – Reset To Initial State</h3>

<font class='e1'>ESC c</font><p>

<p class='m1'>Reset the VT100 to its initial state, i.e., the state it
has after it is powered on. This also causes the execution
of the power-up self-test and signal INIT H to be asserted
briefly.</p><p>

<h3 class='font'>RM – Reset Mode</h3>

<font class='e1'>ESC [ Ps ; Ps ; . . . ; Ps l 	default value: none</font><p>

<p class='m1'>Resets one or more VT100 modes as specified by each
selective parameter in the parameter string. Each mode
to be reset is specified by a separate parameter. [See
Set Mode (SM) control sequence]. (See Modes following
this section).</p><p>

<h3 class='font'>SCS – Select Character Set</h3>

<p class='m1'>The appropriate G0 and G1 character sets are designated
from one of the five possible character sets. The G0 and
G1 sets are invoked by the codes SI and SO (shift in and
shift out) respectively.</p><p>

EOD;

	$title = [ "G0 Sets Sequence", "G1 Sets Sequence", "Meaning" ];

	$table = [
		[ "ESC ( A", "ESC ) A", "United Kingdom Set" ],
		[ "ESC ( B", "ESC ) B", "ASCII Set" ],
		[ "ESC ( 0", "ESC ) 0", "Special Graphics" ],
		[ "ESC ( 1", "ESC ) 1", "Alternate Character ROM Standard Character Set" ],
		[ "ESC ( 2", "ESC ) 2", "Alternate Character ROM Special Graphics" ]
		];

	$doc .= $this->createTable( "SCS", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>The United Kingdom and ASCII sets conform to the "ISO
international register of character sets to be used
with escape sequences". The other sets are private
character sets. Special graphics means that the graphic
characters for the codes 1378 to 1768 are replaced with
other characters. The specified character set will be used
until another SCS is received.</p><p>

EOD;

	$doc .= $this->createNote( null,
		"Additional information concerning the SCS escape " .
		"sequence may be obtained in ANSI standard X3.41-1974.", 800 );

	$doc .= <<<EOD

<h3 class='font'>SGR – Select Graphic Rendition</h3>

<font class='e1'>ESC [ Ps ; . . . ; Ps m 	default value: 0</font><p>

<p class='m1'>Invoke the graphic rendition specified by the
parameter(s). All following characters transmitted to
the VT100 are rendered according to the parameter(s)
until the next occurrence of SGR. Format Effector</p><p>

EOD;

	$title = [ "Parameter" , "Parameter Meaning" ];

	$table = [
		[ 0, "Attributes off" ],
		[ 1, "Bold or increased intensity" ],
		[ 4, "Underscore" ],
		[ 5, "Blink" ],
		[ 7, "Negative (reverse) image" ]
		];

	$doc .= $this->createTable( "SGR", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>All other parameter values are ignored.</p><p>

<p class='m1'>With the Advanced Video Option, only one type of
character attribute is possible as determined by the cursor
selection; in that case specifying either the underscore or
the reverse attribute will activate the currently selected
attribute. (See cursor selection in Chapter 1).</p><p>

<h3 class='font'>SM – Set Mode</h3>

<font class='e1'>ESC [ Ps ; . . . ; Ps h 	default value: none</font><p>

<p class='m1'>Causes one or more modes to be set within
the VT100 as specified by each selective parameter in the
parameter string. Each mode to be set is specified by a
separate parameter. A mode is considered set until it is
reset by a reset mode (RM) control sequence.</p><p>

<h3 class='font'>TBC – Tabulation Clear</h3>

<font class='e1'>ESC [ Ps g 	default value: 0</font><p>

EOD;

	$title = [ "Parameter" , "Parameter Meaning" ];

	$table = [
		[ 0, "Clear the horizontal tab stop at the active position (the default case)." ],
		[ 3, "Clear all horizontal tab stops." ]
		];

	$doc .= $this->createTable( "TBC", $title, $table, 2 );


	$doc .= <<<EOD
<p>
<p class='m1'>Any other parameter values are ignored. Format Effector
Modes</p><p>

<p class='m1'>The following is a list of VT100 modes which may be changed
with set mode (SM) and reset mode (RM) controls.</p>

<h3 class='font'>ANSI Specified Modes</h3>

EOD;

	$title = [ "Parameter" , "Mode Mnemonic", "Mode Function" ];

	$table = [
		[ 0, " ", "Error (ignored)" ],
		[ 20, "LNM", "Line feed new line mode" ]
		];

	$doc .= $this->createTable( "ANSI Specified Modes", $title, $table, 2 );


	$doc .= <<<EOD
<p>

<h3 class='font'>DEC Private Modes</h3>

<p class='m1'>If the first character in the parameter string is ? (778),
the parameters are interpreted as DEC private parameters
according to the following:</p><p>

EOD;

	$title = [ "Parameter", "Mode Mnemonic", "Mode Function" ];

	$table = [
		[ 0, " ", "Error (ignored)" ],
		[ 1, "DECCKM", "Cursor key" ],
		[ 2, "DECANM", "ANSI/VT52" ],
		[ 3, "DECCOLM", "Column" ],
		[ 4, "DECSCLM", "Scrolling" ],
		[ 5, "DECSCNM", "Screen" ],
		[ 6, "DECOM", "Origin" ],
		[ 7, "DECAWM", "Auto wrap" ],
		[ 8, "DECARM", "Auto repeating" ],
		[ 9, "DECINLM", "Interlace" ]
		];

	$doc .= $this->createTable( "DEC Private Modes", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Any other parameter values are ignored.</p><p>

<p class='m1'>The following modes, which are specified
in the ANSI X3.64-1977 standard, may be considered to be
permanently set, permanently reset, or not applicable,
as noted. Refer to that standard for further information
concerning these modes.</p><p>

EOD;

	$title = [ "Mode Mnemonic", "Mode Function", "State" ];

	$table = [
		[ "CRM", "Control representation", "Reset" ],
		[ "EBM", "Editing boundary", "Reset" ],
		[ "ERM", "Erasure", "Set" ],
		[ "FEAM", "Format effector action", "Reset" ],
		[ "FETM", "Format effector transfer", "Reset" ],
		[ "GATM", "Guarded area transfer", null ],
		[ "HEM", "Horizontal editing", null ],
		[ "IRM", "Insertion-replacement", "Reset" ],
		[ "KAM", "Keyboard action", "Reset" ],
		[ "MATM", "Multiple area transfer", null ],
		[ "PUM", "Positioning unit", "Reset" ],
		[ "SATM", "Selected area transfer", null ],
		[ "SRTM", "Status reporting transfer", "Reset" ],
		[ "TSM", "Tabulation stop", "Reset" ],
		[ "TTM", "Transfer termination", null ],
		[ "VEM", "Vertical editing", null ]
		];

	$doc .= $this->createTable( "Other Parameter Value", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3 class='font'>Valid VT52 Mode Control Sequences</h3>

<h4 class='font'>Cursor Up</h4>

<font class='e1'>ESC A 	.</font><p>

<p class='m1'>Move the active position upward one position without
altering the horizontal position. If an attempt is made
to move the cursor above the top margin, the cursor stops
at the top margin.</p><p>

<h4 class='font'>Cursor Down</h4>

<font class='e1'>ESC B 	.</font><p>

<p class='m1'>Move the active position downward one position without
altering the horizontal position. If an attempt is made
to move the cursor below the bottom margin, the cursor
stops at the bottom margin.</p><p>

<h4 class='font'>Cursor Right</h4>

<font class='e1'>ESC C 	.</font><p>

<p class='m1'>Move the active position to the right. If an attempt is
made to move the cursor to the right of the right margin,
the cursor stops at the right margin.</p><p>

<h4 class='font'>Cursor Left</h4>

<font class='e1'>ESC D 	.</font><p>

<p class='m1'>Move the active position one position to the left. If an
attempt is made to move the cursor to the left of the left
margin, the cursor stops at the left margin.</p><p>

<h4 class='font'>Enter Graphics Mode</h4>

<font class='e1'>ESC F 	.</font><p>

<p class='m1'>Causes the special graphics character set to be used.</p><p>

EOD;

	$doc .= $this->createNote( null,
		"The special graphics characters on the VT100 are " .
		"different from those on the VT52.", 800 );

	$doc .= <<<EOD

<h4 class='font'>Exit Graphics Mode</h4>

<font class='e1'>ESC G 	.</font><p>

<p class='m1'>This sequence causes the standard ASCII character set to
be used.</p><p>

<h4 class='font'>Cursor to Home</h4>

<font class='e1'>ESC H 	.</font><p>

<p class='m1'>Move the cursor to the home position.</p><p>

<h4 class='font'>Reverse Line Feed</h4>

<font class='e1'>ESC I 	.</font><p>

<p class='m1'>Move the active position upward one position without
altering the column position. If the active position is
at the top margin, a scroll down is performed.</p><p>

<h4 class='font'>Erase to End of Screen</h4>

<font class='e1'>ESC J 	.</font><p>

<p class='m1'>Erase all characters from the active position to the end
of the screen. The active position is not changed.</p><p>

<h4 class='font'>Erase to End of Line</h4>

<font class='e1'>ESC K 	.</font><p>

<p class='m1'>Erase all characters from the active position to the end
of the current line. The active position is not changed.</p><p>

<h4 class='font'>Direct Cursor Address</h4>

<font class='e1'>ESC Y line column.</font><p>

<p class='m1'>Move the cursor to the specified line and column. The line
and column numbers are sent as ASCII codes whose values are
the number plus 0378; e.g., 0408 refers to the first line
or column, 0508 refers to the eighth line or column, etc.</p><p>

<h4 class='font'>Identify</h4>

<font class='e1'>ESC .</font><p>

<p class='m1'>This sequence causes the terminal to send its identifier
escape sequence to the host.<p>

<font class='e1'>ESC / .</font><p>

<p class='m1'>Used to end the identifier escape sequence.<p>

EOD;

	$doc .= $this->createNote( null,
		"So you have to send BOTH the starting string of 'ESC " .
		".' followed by the ending sequence of 'ESC / .' in order " .
		"to use this command. ", 800 );

	$doc .= <<<EOD

<h4 class='font'>Enter Alternate Keypad Mode</h4>

<font class='e1'>ESC .</font><p>

<p class='m1'>The optional auxiliary keypad keys will send unique
identifiable escape sequences for use by applications
programs.</p><p>

EOD;

	$doc .= $this->createNote( null,
		"Information regarding options must be obtained " .
		"in ANSI mode, using the device attributes (DA) control " .
		"sequences. ", 800 );

	$doc .= <<<EOD


<h4 class='font'>Exit Alternate Keypad Mode</h4>

<font class='e1'>ESC &gt.</font><p>

<p class='m1'>The optional auxiliary keypad keys send the ASCII codes
for the functions or characters engraved on the key.</p><p>

<h4 class='font'>Enter ANSI Mode</h4>

<font class='e1'>ESC &lt.</font><p>

<p class='m1'>All subsequent escape sequences will be interpreted
according to ANSI Standards X3.64-1977 and X3.41-1974. The
VT52 escape sequence designed in this section will not
be recognized.</p><p>

<h4 class='font'>Control Sequence Summary</h4>

<p class='m1'>The following is a summary of the VT100 control sequences.</p><p>

<h3 class='font'>ANSI Compatible Mode</h3>

<h4 class='font'>Cursor Movement Commands</h4>

EOD;

	$title = [ "Command", "Sequence" ];

	$table = [
		[ "Cursor up", "ESC [ Pn A" ],
		[ "Cursor down", "ESC [ Pn B" ],
		[ "Cursor forward (right)", "ESC [ Pn C" ],
		[ "Cursor backward (left)", "ESC [ Pn D" ],
		[ "Direct cursor addressing", "ESC [ Pl<sup>1</sup> ; Pc<sup>2</sup> H or" ],
		[ "Direct cursor addressing", "ESC [ Pl<sup>1</sup> ; Pc<sup>2</sup> f" ],
		[ "Index", "ESC D" ],
		[ "New line", "ESC E" ],
		[ "Reverse index", "ESC M" ],
		[ "Save cursor and attributes", "ESC 7" ],
		[ "Restore cursor and attributes", "ESC 8" ],
		[ "Note Pl<sup>1</sup>", "Pl = line number;" ],
		[ "Note Pc<sup>2</sup>", "Pc = column number" ]
		];

	$doc .= $this->createTable( "Cursor Movement", $title, $table, 2 );

	$doc .= "<p>\n";

	$doc .= $this->createNote( null,
		"Pn refers to a decimal parameter expressed as a " .
		"string of ASCII digits. Multiple parameters are separated " .
		"by the semicolon character (0738). If a parameter is " .
		"omitted or specified to be 0 the default parameter value " .
		"is used. For the cursor movement commands, the default " .
		"parameter value is 1. ", 800 );

	$doc .= "<p>\n";

	$title = [ "Line Size (Double-Height and Double-Width)", "Sequence" ];

	$table = [
		[ "Change this line to double-height top half", "ESC # 3" ],
		[ "Change this line to double-height bottom half", "ESC # 4" ],
		[ "Change this line to single-width single-height", "ESC # 5" ],
		[ "Change this line to double-width single-height", "ESC # 6" ]
		];

	$doc .= $this->createTable( "Double Cursor Movement", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>Character Attributes</h4><p>

<font class='e1'>ESC [ Ps;Ps;Ps;...;Ps m</font><p>

<p class='m1'>Ps refers to a selective parameter. Multiple parameters are
separated by the semicolon character (0738). The parameters
are executed in order and have the following meanings:</p><p>

EOD;

	$title = [ "Parameter", "Meaning" ];

	$table = [
		[ "0 or None", "All Attributes Off" ],
		[ "1", "Bold on" ],
		[ "4", "Underscore on" ],
		[ "5", "Blink on" ],
		[ "7", "Reverse video on" ]
		];

	$doc .= $this->createTable( "Character Abilities", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Any other parameter values are ignored.</p><p>

<h4>Erasing</h4><p>

EOD;

	$title = [ "Meaning", "Sequence" ];

	$table = [
		[ "From cursor to end of line", "ESC [ K or ESC [ 0 K" ],
		[ "From beginning of line to cursor", "ESC [ 1 K" ],
		[ "Entire line containing cursor", "ESC [ 2 K" ],
		[ "From cursor to end of screen", "ESC [ J or ESC [ 0 J" ],
		[ "From beginning of screen to cursor", "ESC [ 1 J" ],
		[ "Entire screen", "ESC [ 2 J" ]
		];

	$doc .= $this->createTable( "Erasing", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>Programmable LEDs</h4><p>

<font class='e1'>ESC [ Ps;Ps;...Ps q</font><p>

<p class='m1'>Ps are selective parameters separated by semicolons (0738)
and executed in order, as follows:</p><p>

EOD;

	$title = [ "ID", "Meaning" ];

	$table = [
		[ "0 or None", "All LEDs Off" ],
		[ 1, "L1 On" ],
		[ 2, "L2 On" ],
		[ 3, "L3 On" ],
		[ 4, "L4 On" ]
		];

	$doc .= $this->createTable( "LED Programming", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Any other parameter values are ignored.</p><p>

<h4>Character Sets (G0 and G1 Designators)</h4><p>

<p class='m1'>The G0 and G1 character sets are designated as follows:</p><p>

EOD;

	$title = [ "Character set", "G0 designator", "G1 designator" ];

	$table = [
		[ "United Kingdom (UK)", "ESC ( A", "ESC ) A" ],
		[ "United States (USASCII)", "ESC ( B", "ESC ) B" ],
		[ "Special graphics characters and line drawing set", "ESC ( 0", "ESC ) 0" ],
		[ "Alternate character ROM", "ESC ( 1", "ESC ) 1" ],
		[ "Alternate character ROM special graphics characters", "ESC ( 2", "ESC ) 2" ]
		];

	$doc .= $this->createTable( "G0 and G1", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>Scrolling Region</h4><p>

<font class='e1'>ESC [ Pt ; Pb r</font><p>

<p class='m1'>Pt is the number of the top line of the scrolling region;</p>
<p class='m1'>Pb is the number of the bottom line of the scrolling region
and must be greater than Pt.</p><p>

<h4>Tab Stops</h4><p>

EOD;

	$title = [ "Item", "Parameter" ];

	$table = [
		[ "Set tab at current column", "ESC H" ],
		[ "Clear tab at current column", "ESC [ g or ESC [ 0 g" ],
		[ "Clear All Tabs", "ESC [ 3 g" ]
		];

	$doc .= $this->createTable( "Tab Stops", $title, $table, 2 );

	$doc .= $this->createNote( null,
		"Note that you MUST FIRST MOVE to where you want to either " .
		"SET or CLEAR a tab BEFORE DOING SO. Except for the Clear " .
		"All Tabs line.", 350 );

	$doc .= <<<EOD
<p>

<h4>Modes</h4><p>

EOD;

	$title = [
		"Mode Name", "<colspan=2>", "To Set", "<colspan=2>", "To Reset" ];

	$table = [
		[ " ", "Mode", "Sequence", "Mode", "Sequence" ],
		[ "Line feed/new line", "New line", "ESC [20h", "Line feed", "ESC [20l*" ],
		[ "Cursor key mode", "Application", "ESC [?1h", "Cursor", "ESC [?1l*" ],
		[ "ANSI/VT52 mode", "ANSI", "N/A", "VT52", "ESC [?2l*" ],
		[ "Column mode", "132 Col", "ESC [?3h", "80 Col", "ESC [?3l*" ],
		[ "Scrolling mode", "Smooth", "ESC [?4h", "Jump", "ESC [?4l*" ],
		[ "Screen mode", "Reverse", "ESC [?5h", "Normal", "ESC [?5l*" ],
		[ "Origin mode", "Relative", "ESC [?6h", "Absolute", "ESC [?6l*" ],
		[ "Wraparound", "On", "ESC [?7h", "Off", "ESC [?7l*" ],
		[ "Auto repeat", "On", "ESC [?8h", "Off", "ESC [?8l*" ],
		[ "Interlace", "On", "ESC [?9h", "Off", "ESC [?9l*" ],
		[ "Keypad mode", "Application", "ESC =", "Numeric", "ESC >" ],
		[ "<colspan=5>", "* The last character of the sequence is a lowercase L (1548).", ]
		];

	$doc .= $this->createTable( "Modes", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h3>Reports</h3>

<h4>Cursor Position Report</h4><p>

EOD;

	$title = [ "Information", "Sequence" ];

	$table = [
		[ "Invoked by", "ESC [ 6 n" ],
		[ "Response is", "ESC [ Pl<sup>1</sup> ; Pc R<sup>2</sup>" ],
		[ "Note <sup>1</sup>", "Pl = line number" ],
		[ "Note <sup>2</sup>", "Pc = column number" ]
		];

	$doc .= $this->createTable( "Cursor Position", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>Status Report</h4><p>

EOD;

	$title = [ "Information", "Sequence" ];

	$table = [
		[ "Invoked by", "ESC [ 5 n" ],
		[ "Response is", "ESC [ 0 n (terminal ok)" ],
		[ " ", "ESC [ 3 n (terminal not ok)" ]
		];

	$doc .= $this->createTable( "Status Report", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>What Are You</h4><p>

EOD;

	$title = [ "Information", "Sequence" ];

	$table = [
		[ "Invoked by", "ESC [ c or ESC [ 0 c" ],
		[ "Response is", "ESC [ ? 1 ; Ps c" ]
		];

	$doc .= $this->createTable( "What Are You?", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Ps is the "option present" parameter with the following meaning:</p><p>

EOD;

	$title = [ "Ps", "Meaning" ];

	$table = [
		[ "0", "Base VT100, no options" ],
		[ "1", "Processor options (STP)" ],
		[ "2", "Advanced video option (AVO)" ],
		[ "3", "AVO and STP" ],
		[ "4", "Graphics processor option (GPO)" ],
		[ "5", "GPO and STP" ],
		[ "6", "GPO and AVO" ],
		[ "7", "GPO, STP, and AVO" ]
		];

	$doc .= $this->createTable( "Options Present", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Alternatively invoked by ESC Z (not recommended). Response
is the same.</p><p>

<h4>Reset</h4><p>

<p class='m1>Reset causes the power-up reset routine to be executed.</p><p>

<font class='e1'>ESC c</font><p>

<h4>Confidence Tests</h4><p>

EOD;

	$title = [ "Command", "Sequence" ];

	$table = [
		[ "Fill Screen with 'Es'", "ESC # 8" ],
		[ "Invoke Test(s)", "ESC [ 2 ; Ps y" ]
		];

	$doc .= $this->createTable( "Confidence Test", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<p class='m1'>Ps is the parameter indicating the test to be done and is
a decimal number computed by taking the "weight" indicated
for each desired test and adding them together.</p><p>

EOD;

	$title = [ "Test", "Weight" ];

	$table = [
		[ "Power-up self test (ROM checksum,<br>RAM, NVR, keyboard and AVO if installed)", "1" ],
		[ "Data Loop Back", "2<br>(loop back connector required)" ],
		[ "EIA modem control test", "4<br>(loop back connector required)" ],
		[ "Repeat selected test(s) indefinitely<br>(until failure or power off)", "8" ]
		];

	$doc .= $this->createTable( "PS Test", $title, $table, 2 );

	$doc .= <<<EOD
<p>

<h4>VT52 Compatible Mode</h4><p>

<p class='m1'>The following is a summary of the VT100 control sequences.</p><p>

EOD;

	$title = [ "Test", "Weight", "Notes" ];

	$table = [
		[ "Cursor Up", "ESC A", " " ],
		[ "Cursor Down", "ESC B", " " ],
		[ "Cursor Right", "ESC C", " " ],
		[ "Cursor Left", "ESC D", " " ],
		[ "Select Special Graphics character set", "ESC F", " " ],
		[ "Select ASCII character set", "ESC G", " " ],
		[ "Cursor to home", "ESC H", " " ],
		[ "Reverse line feed", "ESC I", " " ],
		[ "Erase to end of screen", "ESC J", " " ],
		[ "Erase to end of line", "ESC K", " " ],
		[ "Direct cursor address", "ESC Y l c", "(see note 1)" ],
		[ "Identify", "ESC Z", "(see note 2)" ],
		[ "Enter alternate keypad mode", "ESC =", " " ],
		[ "Exit alternate keypad mode", "ESC >", " " ],
		[ "Enter ANSI mode", "ESC <", " " ],
		[ "NOTE 1", "<colspan=2>", "Line and column numbers for direct " .
			"cursor address are single character codes whose values " .
			"are the desired number plus 378. Line and column numbers " .
			"start at 1." ],
		[ "NOTE 2", "<colspan=2>", "Response to ESC Z is ESC / Z." ]
		];

	$doc .= $this->createTable( "VT100", $title, $table, 2 );

	$doc .= <<<EOD
<p>

</tbody></table>
EOD;

	return $doc;
}
################################################################################
#	docs_xterm(). Has all of the XTERM documentation taken from:
#
#		https://invisible-island.net/xterm/ctlseqs/ctlseqs.html
#
#	Notes: I do NOT claim ANY ownership at all. This all came from 
################################################################################
private function docs_xterm()
{
	$width = $this->width;
	$height = "20";
	$max_width = $this->max_width;

	$doc =  <<<EOD
<p style="page-break-before: always">
<h1 class='border font'>XTerm</h1>
$this->who
<h2 class='font'>Notice</h2>
EOD;

	$doc .= $this->createNote( null,
		"Due to the very-very large amount of documentation on XTerm " .
		"I have decided to just include the hyperlink to where the " .
		"documentation resides on the internet. Please use that site's " .
		"information on how to use the XTerm commands. ALL XTerm " .
		"commands begin with 'xt'. So you can look at the table for " .
		"of the XTerm commands but the explanations are on the website. " .
		"<p>The Xterm website is at : <p style='text-indent:20px;'>" .
		"https://invisible-island.net/xterm/ctlseqs/ctlseqs.html", 800 );

	$title = [ "Return", "Function Name", "Beginning Text",
		"Pass Something In?", "Ending Text", "Info", "Text", "Return Type" ];

	$doc .= $this->createTable( "XTerm", $title, $this->xterm_cmds, 4, null, null, "w1200" );

	$doc .= "<p>";

	return $doc;
}

################################################################################
#	cvt_ascii(). Converts ascii character into their alpha representation.
################################################################################
private function cvt_ascii( $a )
{
	$ascii = $this->ascii_codes;
#
#	Check for TRUE and FALSE
#
	if( $a === true ){ $a = "True"; }
		else if( $a === false ){ $a = "False"; }
#
#	Do lower ASCII codes
#
	for( $j=0; $j<strlen($a); $j++ ){
		for( $i=0; $i<32; $i++ ){
			$ansi = $ascii[$i];
			$c = chr( $i );
			if( $i < 1 ){ $a = str_replace( $c, $ansi[4], $a ); }
				else if( !is_null($ansi[4]) && preg_match("/$c/", $a) ){
					$a = str_replace( $c, $ansi[4], $a );
					}
				else if( !is_null($ansi[7]) && preg_match("/$c/", $a) ){
					$a = str_replace( $c, $ansi[7], $a );
					}
				else {
					$a = str_replace( $c, " ", $a );
					}
			}
#
#	Do UPPER ASCII codes
#
		for( $i=127; $i<256; $i++ ){
			$ansi = $ascii[$i];
			$c = chr( $i );
			if( !is_null($ansi[4]) && preg_match("/$c/", $a) ){
				$a = str_replace( $c, $ansi[6], $a );
				}
				else if( !is_null($ansi[7]) && preg_match("/$c/", $a) ){
					$a = str_replace( $c, $ansi[7], $a );
					}
				else {
					$a = str_replace( $c, " ", $a );
					}
			}
		}

	return $a;
}
################################################################################
#	__set(). Get a path.
################################################################################
public function __set( $name, $value )
{
}
################################################################################
#	__get(). Set a path.
################################################################################
public function __get( $name )
{
}
################################################################################
#	makePrintable(). A simple function to make invisible character visiable.
#	Notes:	Remember that you can always pass in NULL to skip an option.
#		$string			=	The string to be converted. Non-destructable.
#		$leftBracket	=	If NULL then use "[".
#		$rightBracket	=	If NULL then use "]".
#		$prefix			=	If you want something BEFORE the left bracket (optional)
#		$suffix			=	If you want to change the separator after the word.
#		$case			=	Output words like CONTROL all in uppercase.
#			Default		=	All characters are converted to UPPERCASE.
#			Options : These are the options for $case. ONLY the first letter is used.
#				U)pper	=	Convert all words to UPPERCASE
#				L)ower	=	Convert all words to lowercase
#				F)irst	=	ONLY the first letter will be converted to UPPERCASE
#				B)owed	=	ONLY the first letter will be converted to lowercase
#							the REST OF THE STRING will be in UPPERCASE
#				M)ixed	=	Makes every other letter UPPERCASE
#				N)ixed	=	Opposite of Mixed option. (I know - silly)
#
#		$show			=	If you would rather have the letter shown rather
#							than the HTML code put TRUE either. Default is FALSE.
#			NOTES : $show ONLY WORKS on the 126 and higher values.
################################################################################
function makePrintable( $string=null, $leftBracket=null, $rightBracket=null,
	$prefix=null, $suffix=null, $case=null, $show=null )
{
	if( is_null($string) ){ return true; }
	if( is_null($leftBracket) ){ $leftBracket = "["; }
	if( is_null($rightBracket) ){ $rightBracket = "["; }
	if( is_null($prefix) ){ $prefix = ""; }
	if( is_null($suffix) ){ $suffix = "-"; }
	if( is_null($show) || $show === false ){ $show = false; } else { $show = true; }
	if( is_null($case) ){ $case = "u"; }
	if( !preg_match("/(u|l|f|b|m|n)+/i", $case) ){
		echo "Unknown CASE type - Aborting\n";
		return false;
		}

	$words = [];
	$words[] = "control";
	$words[] = "letter";
#
#	Ok - make the changes to the words
#
	foreach( $words as $k=>$v ){
		if( preg_match("/u/i", $case) ){ $words[$k] = strToUpper($v); }
			else if( preg_match("/l/i", $case) ){ $words[$k] = strToLower($v); }
			else if( preg_match("/f/i", $case) ){ $words[$k] = ucfirst( strToLower($v) ); }
			else if( preg_match("/b/i", $case) ){ $words[$k] = lcfirst( strToUpper($v) ); }
			else if( preg_match("/m/i", $case) ){
				$new_string = "";
				$str = strToLower($v);
				$len_word = strlen( $words[$k] );
				for( $i=0; $i<$len_word; $i+=2 ){
					$new_string .= strToUpper( substr($str, $i, 1) );
					if( strlen(substr($str, $i+1, 1)) > 0 ){
						$new_string .= substr($str, $i+1, 1);
						}
					}

				$words[$k] = $new_string;
				}
			else if( preg_match("/n/i", $case) ){
				$new_string = "";
				$str = strToUpper($v);
				$len_word = strlen( $words[$k] );
				for( $i=0; $i<$len_word; $i+=2 ){
					$new_string .= strToLower( substr($str, $i, 1) );
					if( strlen(substr($str, $i+1, 1)) > 0 ){
						$new_string .= substr($str, $i+1, 1);
						}
					}

				$words[$k] = $new_string;
				}

		}

	$let_a = ord( "A" );

	$new_string = "";
	$strlen = strlen( $string );

	for( $i=0; $i<$strlen; $i++ ){
		$ord = ord( $string[$i] );
		if( $ord < 32 ){
			$new_string .= $prefix . $leftBracket . $words[0] . $suffix .
				chr(ord(substr($string, $i, 1))) . $rightBracket;
			}
			else if( $ord > 126 ){
				if( $show ){ $html = chr( $ord ); }
					else { $html = "&#" . $ord; }

				$new_string .= $prefix . $leftBracket . $words[1] . $suffix .
					$html. ";" . $rightBracket;
				}
		}
}
################################################################################
#	createTable(). Create a table.
#	Options:
#		name	=	Name of the table (Table Title)
#		title	=	Titles across the top of the table
#		table	=	Contents of the table
#		size	=	Font size. This can be a number from one to five (1-5).
#					1 = 12pt, 2 = 14pt, 3 = 18pt, 4 = 24pt, and 5 = 36pt
#		wrap	=	Whether to wrap lines or not (Default is FALSE)
#	NOTES:	The GD function imageftbbox() does NOT need GD in order to work.
#			UGH. Ok. the simple "<line" command is now the "<Dborder=\d+>" command.
#				This means the "D" is either T)op, B)ottom, L)eft, or R)ight.
#				The "command" part is the "line" saying you want to make a line
#					on one of the FOUR sides of the rectangle.
#				The "\d+" is how BIG you want that line to be. Default is one(1).
#			You can ALSO send in a "<colspan=###>" which puts in a COLSPAN equal to ###.
#			(Not - at this time - doing ROWSPAN)
################################################################################
function createTable( $name=null, $title=null, $table=null, $size=null,
	$wrap=null, $inline=null, $width=null )
{
	if( is_null($name) ){ return false; }
	if( is_null($title) ){ return false; }
	if( is_null($table) ){ return false; }
	if( is_null($wrap) || ($wrap === false) ){ $wrap = ""; }
		else { $wrap = "nowrap"; }
#
#	Get the graphic stuff
#
	$na = $this->na;
	$titles = count( $title );

	$doc = <<<EOD
<TABLE CELLPADDING="3" CELLSPACING="0" class='tblw $width'><tbody>
<tr><td colspan=$titles class='c1 t$size bold nowrap'>$name Table</td></tr>
EOD;

#	$cols = [];
	if( !is_null($title[0]) ){
		$colspan = 1;
		$doc .= "<tr>\n";
		foreach( $title as $k=>$v ){
			if( preg_match("/<colspan=\d+>/i", $v) ){
				$v = str_replace( ">", "", $v );
				$v = str_replace( "<", "", $v );
				$a = explode( "=", $v );
				$colspan = $a[1];
				}
				else {
#					$bbox = imageftbbox( 12, 0, "C:/Windows/Fonts/times.ttf", $v );
#					print_r( $bbox ); echo "\n";
#					$cols[$k] = $bbox[2] - $bbox[0];

					$doc .= "<td colspan=$colspan class='tdb c1 pad5 m1 nowrap'>$v</td>\n";
					}
			}

		$doc .= "</tr>\n";
		}

	$even_odd = 0;
	$flag = false;
	$colspan = 1;
	$border = "";
	$border_flag = false;
	foreach( $table as $k=>$v ){
		$even_odd = ($even_odd + 1) % 2;
		if( $even_odd < 1 ){ $doc .= "<tr class='trb even'>\n"; }
			else { $doc .= "<tr class='trb odd'>\n"; }

		foreach( $v as $k1=>$v1 ){
			if( !preg_match("/<.border=\d+>/i", $v1) ){
				$v1 = $this->cvt_ascii( $v1 );
				if( is_null($v1) || strlen($v1) < 1 ){ $flag = true; $v1 = "$na"; }

				if( !preg_match("/<colspan=\d+>/i", $v1) ){
					$blen = 0;
					$blen_1 = 0;
					$blen_2 = 0;
					$blen_3 = 0;
#					if( preg_match("/\{/i", $v1) ){
#						$b = explode( "{", $v1 );
#	print_r( $b ); echo "\n";
#						$c = explode( "}", $b[1] );
#	print_r( $c ); echo "\n";
#						$blen_1 = strlen( $b[0] );
#						$blen_2 = strlen( $c[1] );
#						$blen_3 = strlen( $b[2] );
#						$blen = ($blen_1 + $blen_2 + $blen_3) % 80;
#						}

					$v1 = str_replace( "{", "<", $v1 );
					$v1 = str_replace( "}", "> ", $v1 );
					$v1 = $this->htmlwrap( $v1 );

					if( is_array($inline) ){ $v1 .= $this->inlineTable( $inline ); }
					$doc .= "<td colspan=$colspan class='tdb c1 pad5 nowrap' $border>$v1</td>\n";
					$colspan = 1;
					}
					else {
						$v1 = str_replace( ">", "", $v1 );
						$a = explode( "=", $v1 );
						$colspan = $a[1];
						}
				}
				else {
					$border_flag = true;
					$v1 = str_replace( ">", "", $v1 );		#	Make it <Xborder=###
					$v1 = str_replace( "<", "", $v1 );		#	Make it Xborder=###
					$v1 = str_replace( "border", "", $v1 );	#	This should just leave the X=###.
					$a = explode( "=", $v1 );

					if( preg_match("/l/i", $a[0]) ){ $border = "style='border-left-width: $a[1]px;'"; }
						else if( preg_match("/r/i", $a[0]) ){ $border = "style='border-right-width: $a[1]px;'"; }
						else if( preg_match("/t/i", $a[0]) ){ $border = "style='border-top-width: $a[1]px;'"; }
						else if( preg_match("/b/i", $a[0]) ){ $border = "style='border-bottom-width: $a[1]px;'"; }
					}
			}

		if( $colspan == $titles ){ $colspan = 1; continue; }

		for( $i=$k1+1; $i<$titles; $i++ ){
			$flag = true;

#			$bbox = imageftbbox( 12, 0, "C:/Windows/Fonts/times.ttf", $na );
#			print_r( $bbox ); echo "\n";
#			$len = $bbox[2] - $bbox[0];

#			if( !isset($cols[$i]) ){ $cols[$i] = 0; }
#			if( $len > $cols[$i] ){ $cols[$i] = $len; }

			$na = str_replace( "{", "<", $na );
			$na = str_replace( "}", ">", $na );
			$doc .= "<td class='tdb c1 pad5 nowrap' style='$border'>$na</td>\n";
			$colspan = 1;
			}

		$doc .= "</tr>\n";
		if( $border_flag ){ $border_flag = false; }
			else { $border = ""; }
		}

	if( $flag ){
		$len = 0;
		$doc .= "</tbody></table>\n";
		$doc .= $this->createNote( $titles, null, null );
#		$doc .= <<<EOD
#<p>
#<TABLE CELLPADDING="3" CELLSPACING="0" class='tblw'><tbody>
#<tr><td colspan=3 class='c1 t$size w4 bold'>$name Table Notes</td></tr>
#EOD;
#		for( $i=0; $i<$titles; $i++ ){
#			$even_odd = ($even_odd + 1) % 2;
#			if( $even_odd < 1 ){ $doc .= "<tr class='trb even'>\n"; }
#				else { $doc .= "<tr class='trb odd'>\n"; }
#
#			$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$name</td>\n";
#			$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>LEN</td>\n";
#			$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$cols[$i]</td>\n";
#			$doc .= "</tr>\n";
#			$len += $cols[$i];
#			}

		$len = $len + ($titles * 9);
#		if( $len > 900 ){ $len = $len - floor( $len / 10 ); }
#		$even_odd = ($even_odd + 1) % 2;
#		if( $even_odd < 1 ){ $doc .= "<tr class='trb even'>\n"; }
#			else { $doc .= "<tr class='trb odd'>\n"; }
#
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$name</td>\n";
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>SUBTOTAL</td>\n";
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$len</td>\n";
#		$doc .= "</tr>\n";
#
#		$even_odd = ($even_odd + 1) % 2;
#		if( $even_odd < 1 ){ $doc .= "<tr class='trb even'>\n"; }
#			else { $doc .= "<tr class='trb odd'>\n"; }
#
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$name</td>\n";
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>TOTAL</td>\n";
#		$doc .= "<td class='tdb c1 pad5 $wrap' style='$border'>$len</td>\n";
		$doc .= "</tr>\n";
		$doc .= "</tbody></table>\n";
		}
		else {
			$doc .= "</tbody></table>\n";
			$doc .= $this->createNote( $titles, null, null );
			}

	return $doc;
}
################################################################################
#	createNote(). Creates a NOTE statement.
#	$colspan	=	The number of colums to span (if any)
#	$info		=	What to put in the note
#	$width		=	Can be TWO values
#		NULL	=	Make the width equal "width='100%'"		-	In a TABLE
#		640		=	Make the width equal "width='640px'"	-	Stand aline
################################################################################
function createNote( $colspan=null, $info=null, $width=null )
{
	if( !is_null($colspan) ){ $colspan = "colspan='$colspan'"; }
	if( is_null($width) ){ $width = "width='800px'"; }
		else if( preg_match("/%/", $width) ){ $width = "Width"; }
		else { $width = "width='" . $width . "px'"; }

	if( is_null($info) ){
		$info = <<<EOD
The '$this->na' means NOT APPLICABLE. This can mean several
things such as "Do not put anything into this area" or just
"This is not something you can do".
EOD;
		}

	$doc = <<<EOD
<tr><td $colspan>
<table class='tbln' $width><tbody><tr class='trn'>
<td class='tdn pad10'>NOTE</td>
<td class='tds'>&nbsp;</td>
<td class='tds' $width>$info</td>
</tr></tbody></table>
</td></tr>
EOD;

	return $doc;
}
################################################################################
#	inlineTable(). Create an inline table. Mainly used within a table.
#
#	NOTES: Here is how this works:
#		1.	Put all of your columns to display FIRST. (Example: 1,2,3,4...)
#		2.	Make the row BOLD? 1/0 (True/False)
#		3.	Combine COLUMNS? (1/0). (Almost always used with TITLES.)
#
################################################################################
function inlineTable( $table )
{
#
#	Start the table
#
	$evenodd = 0;
	$doc = "<center><table class='tbln w90 nowrap'><tbody>";

	foreach( $table as $k=>$v ){
		$evenodd = ($evenodd + 1) % 2;
		if( $evenodd > 0 ){ $opt = "even"; }
			else { $opt = "odd"; }

		$doc .= "<tr class='trn nowrap $opt'>";
#
#	Get how many columns there are
#
		$c = count( $v ) - 1;
#
#	Are we making this line BOLD?
#
		if( $v[$c-1] > 0 ){ $bs = "<b>"; $be = "</b>"; }
			else { $bs = ""; $be = ""; }
#
#	If we want to combine the columns, it is ALWAYS the count minus two.
#	Make a title. Titles are always centered.
#
		if( $v[$c] > 0 ){
			$colspan = $c-1;
			$doc .= "<td colspan=$colspan class='tdb c1 pad5 nowrap'>$bs$v[0]$be</td>";
			}
#
#	Make a regular line. These are left justified.
#
			else {
				for( $i=0; $i<($c-1); $i++ ){
					$v[$i] = str_replace( " ", "&nbsp;", $v[$i] );
					$doc .= "<td colspan=1 class='tdb pad5 c1 nowrap'>$bs$v[$i]$be</td>";
					}
				}

		$doc .= "</tr>";
		}

	$doc .= "</tbody></table></center>";

	return $doc;
}
################################################################################
#	htmlwrap(). Does what word wrap does but excludes any HTML commands.
################################################################################
function htmlwrap( $string=null, $width=60, $break="<br>", $cut_long_words=false )
{
	if( is_null($string) ){ die( "***** ERROR : STRING is NULL\n" ); }
#
#	$oldchar is the former character we got. Used to test for "\<" kinds of things.
#
	$oldchar = "";
#
#	$newstr is our new string we will return.
#
	$newstr = "";
#
#	$tmpstr is our TEMPORARY string we are building to put into $newstr.
#
	$tmpstr = "";
#
#	$htmlflag is whether or not we are in an HTML command.
#
	$htmlflag = false;
#
#	$htmlstr is the current HTML string we are trying to exclude
#
	$htmlstr = "";
#
#	$strlen is how long the current string is.
#
	$strlen = strlen( $string );
	for( $i=0; $i<$strlen; $i++ ){
		$c1 = substr( $string, $i, 1 );
		$c2 = substr( $string, $i, 2 );
		$c3 = substr( $string, $i, 3 );
#
#	If $c1 is a backslash then the next character will actually be the character. So we
#	just store the backslash into the $oldchar variable and go on to the next character.
#	Also, backslashes are not included into length of the string BUT we do move the backslash
#	over to the temp string ($tmpstr).
#
		if( $c1 == "\\" ){ $oldchar = $c1; $tmpstr .= $c1; continue; }
#
#	If the $oldchar has a backslash '\' in it we need to see if $c1 has either a '<' or a '>'
#	in it. If it is then just store the character into $oldchar and add it to $tmpstr
#	and go to the next character.
#
		if( $oldchar == "\\" ){
			if( ($c1 == '<') || ($c1 == '>') ){ $oldchar = $c1; $tmpstr .= $c1; continue; }
			}
#
#	Ok! So now - is $c1 an HTML angle bracket? (ie: '<' or '>') Then we turn on or off the
#	$htmlflag.
#
#
#	NEW : $htmlstr is the HTML string we are finding out about. This string has to be subtracted
#		from the length of the temporary string. Also! $htmlstr is NOT cleared until we clear
#		the $tmpstr.
#
		if( preg_match("/<\w/", $c2) ){
			$htmlflag = true;
			$oldchar = $c1;
			$tmpstr .= $c1;
			$htmlstr .= $c1;
			$htmlflag = true;
			continue;
			}
			else if( preg_match("/td>/i", $c3) ){
				$tmpstr .= $c3;
				$i += 2;
				$c1 = substr( $string, $i, 1 );
#				$newstr .= $tmpstr . " TD[" . strlen($tmpstr) . "," .
#					strlen($htmlstr) . "," . $tmpstr . "]" . $break;
				$newstr .= $tmpstr;
				$oldchar = ">";
				$tmpstr = "";
				$htmlstr = $c2;
				$htmlflag = false;
				continue;
				}
			else if( preg_match("/\w>/", $c2) ){
				$i++;
				$c1 = substr( $string, $i, 1 );
				$htmlflag = false;
				$oldchar = "";
				$tmpstr .= $c2;
				$htmlstr .= $c2;
				$htmlflag = false;
				continue;
				}
#
#	Now is $c1 a white space variable AND is the $htmlflag is set to FALSE?
#
		if( preg_match("/\s/", $c1) && ($htmlflag === false) ){
#
#	If the temporary string $tmpstr is greater than or equal to the width given on the
#	call line then insert the break given on the call line.
#
#	NEW : Remember to subtract the $htmlstr's length from the $tmpstr length because
#		this is what we are trying to keep track of.
#
			if( (strlen($tmpstr) - strlen($htmlstr)) >= $width ){
#				$newstr .= $tmpstr . " REG-1[" . strlen($tmpstr) . "," .
#					strlen($htmlstr) . "," . $tmpstr . "]" . $break;
				$newstr .= $tmpstr . $break;
				$oldchar = $c1;
				$tmpstr = "";
				$htmlstr = "";
				$htmlflag = false;
				continue;
				}
			}
			else if( ($cut_long_words == true) && ($htmlflag === false) &&
				((strlen($tmpstr) - strlen($htmlstr)) >= $width) && preg_match("/\s/", $c1) ){
#				$newstr .= $tmpstr . " REG-2[" . strlen($tmpstr) . "," . strlen($htmlstr) . "]" . $break;
				$newstr .= $tmpstr . $break;
				$oldchar = $c1;
				$tmpstr = "";
				$htmlstr = "";
				$htmlflag = false;
				continue;
				}

		$tmpstr .= $c1;
		$oldchar = $c1;
		if( $htmlflag ){ $htmlstr .= $c1; }
		}

	$newstr .= $tmpstr;
	return $newstr;
}
################################################################################
#	close(). Closes the proc created area we have been writing to.
################################################################################
function close()
{
	if( !is_null($this->ansi_handle) ){
		fclose( $this->pipes[0] );
		fclose( $this->pipes[1] );
		fclose( $this->pipes[2] );
		proc_close( $this->ansi_handle );

		$this->pipes = null;
		$this->ansi_handle = null;
		}

	return true;
}
################################################################################
#	__destruct(). Close everything and stop.
################################################################################
function __destruct()
{
	if( is_resource($this->ansi_handle) ){
		fclose( $this->pipes[0] );
		fclose( $this->pipes[1] );
		fclose( $this->pipes[2] );

		proc_close( $this->ansi_handle );
		}
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['ansi']) ){
		$GLOBALS['classes']['ansi'] = new class_ansi();
		}

?>
