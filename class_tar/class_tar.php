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
#	class_tar();
#
#-Description:
#
#	A class to handle my tar files.
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
#	Mark Manning			Simulacron I			Sat 12/02/2023 12:59:38.28 
#	---------------------------------------------------------------------------
#		Original Program
#
#	Mark Manning			Simulacron I			Sun 12/03/2023 18:17:54.59 
#	---------------------------------------------------------------------------
#		As per the person at https://mort.coffee/home/tar/ says, A TAR file
#		is nothing more than a header of 512 bytes followed by X number of
#		512 blocks that comprise the file (so if you have a 1025 byte file you
#		would then have three 512 blocks of information. 512+512+(512)
#		Where the last 512 is really the last byte of the file plus 511 null
#		bytes (or blanks). So when you read the file back in - you MUST check
#		for nulls or blanks at the end of the file. In PHP, this is easy because
#		you just do a SUBSTR command starting at the first character (0 posiiton)
#		and just get everything UP TO the size of the file.
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
#		CLASS_TAR.PHP. A class to handle working with tar.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_tar
{
	private $in_fp = null;
	private $out_fp = null;
	private $dump_fp = null;

	private $in_dir = null;
	private $in_file = null;

	private $out_dir = null;
	private $out_file = null;

	private $dump_file = null;

	private $file_types = null;
	private $in_file_loc = null;
	private $out_file_loc = null;

	private	$last_loc = null;
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['tar']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['tar']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	Set up the file types
#
	$file_types = [];
	$file_types['Normal file'] = 0;
	$file_types['Hard Link'] = 1;
	$file_types['Symbolic Link'] = 2;
	$file_types['Directory'] = 5;

	$this->file_types = $file_types;
#
#	Create a temporary file name which can be used to save the output to
#
	$this->tmp = uniqid( "tar-" );
#
#	Get the current working directory
#
	$this->cwd = getcwd();
	$this->cwd = str_replace( "\\", "/", $this->cwd );

	$this->last_loc = 0;
	$this->in_file_loc = 0;
	$this->out_file_loc = 0;
	$this->dump_file = "$this->cwd/tar-output.dat";
#
#	tar Header Block, from POSIX 1003.1-1990.  
#
#	POSIX header.  
#
#	Notes : I have converted this to be an ARRAY
#	so it is easier to use. In this way, similar to
#	the STRUCT command originally used, you would
#	use $this->posix_header['name'] = X.
#
	$this->posix_header = [];
#															byte offset 
	$this->posix_header['name'] = null;		#	[100];			0 
	$this->posix_header['mode'] = null;		#	[8];			100 
	$this->posix_header['uid'] = null;		#	[8];			108 
	$this->posix_header['gid'] = null;		#	[8];			116 
	$this->posix_header['size'] = null;		#	[12];			124 
	$this->posix_header['mtime'] = null;	#	[12];			136 
	$this->posix_header['chksum'] = null;	#	[8];			148 
	$this->posix_header['typeflag'] = null;	#	156 
	$this->posix_header['linkname'] = null;	#	[100];			157 
	$this->posix_header['magic'] = null;	#	[6];			257 
	$this->posix_header['version'] = null;	#	[2];			263 
	$this->posix_header['uname'] = null;	#	[32];			265 
	$this->posix_header['gname'] = null;	#	[32];			297 
	$this->posix_header['devmajor'] = null;	#	[8];			329 
	$this->posix_header['devminor'] = null;	#	[8];			337 
	$this->posix_header['prefix'] = null;	#	[155];			345 
											#					500 
#
#	Descriptor for a single file hole.
#
	$this->sparse = [];
#														byte offset
	$this->sparse['offset'] = null;		#	[12];			0
	$this->sparse['numbytes'] = null;	#	[12];			12
										#					24
#
#	From : https://www.gnu.org/software/tar/manual/tar.html#Tar-Internals
#
	define( "TMAGIC", "ustar" );	#	ustar and a null 
	define( "TMAGLEN", 6 );
	define( "TVERSION", "00" );		#	00 and no null 
	define( "TVERSLEN", 2 );
#
#	Values used in typeflag field.
#
	define( "REGTYPE", '0' );		#	regular file 
	define( "AREGTYPE", '\0' );		#	regular file 
	define( "LNKTYPE", '1' );		#	link 
	define( "SYMTYPE", '2' );		#	reserved 
	define( "CHRTYPE", '3' );		#	character special 
	define( "BLKTYPE", '4' );		#	block special 
	define( "DIRTYPE", '5' );		#	directory 
	define( "FIFOTYPE", '6' );		#	FIFO special 
	define( "CONTTYPE", '7' );		#	reserved 

	define( "XHDTYPE", 'x' );		#	Extended header referring to the
									#	next file in the archive 
	define( "XGLTYPE", 'g' );		#	Global extended header 
#
#	Bits used in the mode "field", values in octal.  
#
	define( "TSUID", 04000 );		#	set UID on execution 
	define( "TSGID", 02000 );		#	set GID on execution 
	define( "TSVTX", 01000 );		#	reserved file permissions 
	define( "TUREAD", 00400 );		#	read by owner 
	define( "TUWRITE", 00200 );		#	write by owner 
	define( "TUEXEC", 00100 );		#	execute/search by owner 
	define( "TGREAD", 00040 );		#	read by group 
	define( "TGWRITE", 00020 );		#	write by group 
	define( "TGEXEC", 00010 );		#	execute/search by group 
	define( "TOREAD", 00004 );		#	read by other 
	define( "TOWRITE", 00002 );		#	write by other 
	define( "TOEXEC", 00001 );		#	execute/search by other 
#
#	Sparse files are not supported in POSIX ustar format.  For sparse files
#	with a POSIX header, a GNU extra header is provided which holds overall
#	sparse information and a few sparse descriptors.  When an old GNU header
#	replaces both the POSIX header and the GNU extra header, it holds some
#	sparse descriptors too.  Whether POSIX or not, if more sparse descriptors
#	are still needed, they are put into as many successive sparse headers as
#	necessary.  The following constants tell how many sparse descriptors fit
#	in each kind of header able to hold them.
#
	define( "SPARSES_IN_EXTRA_HEADER", 16 );
	define( "SPARSES_IN_OLDGNU_HEADER", 4 );
	define( "SPARSES_IN_SPARSE_HEADER", 21 );
}
################################################################################
#	in_file(). Get the tar file to read
################################################################################
function in_file( $file=null )
{
	if( is_null($file) ){ die( "***** ERROR : Input Filename is NULL" ); }
	$this->in_file = $file;
	$this->max_size = filesize( $file );
	$this->max_file_type = filetype( $file );
}
################################################################################
#	out_file(). Get the tar file to read
#	NOTES:	The "out" file is where all text is kept from running this class.
################################################################################
function out_file( $file=null )
{
	if( is_null($file) ){
#
#	Get the input file's name and make that the output filename
#	but without the ".tar" on it.
#
		$a = explode( '.', $this->in_file );
		array_pop( $a );
		$file = implode( '.', $a ) . ".dat";

		echo "***** INFO : Output File name is NULL";
		echo "***** INFO : Setting the File name to : $file\n";
		}

	$this->out_file = $file;
}
################################################################################
#	in_dir(). Where we will get the incoming file(s).
################################################################################
function in_dir( $dir=null )
{
#
#	Is the directory NULL? Fix it.
#
	if( is_null($dir) ){
		$dir = $this->cwd;
		echo "***** INFO : Input Directory is NULL.\n";
		echo "***** INFO : Setting the Input Directory to $dir\n";
		}
#
#	Make sure we only use the forward slash and not the backslash
#	separator.
#
	$dir = str_replace( "\\", "/", $dir );
#
#	Ok - does the directory exist AS IS? If so - save and exit.
#
	if( file_exists($dir) ){
		$this->in_dir = $dir;
		return true;
		}
#
#	Ok, NOW we have to test out where the directory might be. Since I
#	am writing this for Windows - I first need to see if the
#	disk drive ID was given (ie: c:/, d:/, e:/, etc...).
#
	$a = explode( '/', $dir );
	$b = explode( '/', $this->cwd );
#
#	Is this on the same path as our current location?
#
	if( strtolower($a[0]) === strtolower($b[0]) ){
		$this->in_dir = $dir;
		return true;
		}
		else if( $b[0] === '..' ){
			$c = explode( '/', $this->cwd );
			array_pop( $c );
			$c = implode( '/', $c );
			$d = explode( '/', $dir );
			array_shift( $dir );
			$d = implode( '/', $d );
			$this->in_dir = "$c/$d";
			return true;
			}
		else if( $b[0] === '.' ){
			$d = explode( '/', $dir );
			array_shift( $dir );
			$d = implode( '/', $d );
			$this->in_dir = "$this->cwd/$d";
			return true;
			}
#
#	Ok - we are at the worst possible part. We have an input directory
#	but we don't know where it is located. How about we try our current
#	directory.
#
	if( file_exists("$this->cwd/$dir") ){
		$this->in_dir = $dir;
		return true;
		}
#
#	Ok, so now we have a directory path which starts someplace
#	and we have to figure out WHERE that directory path is
#	located.
#
}
################################################################################
#	out_dir(). Where we will get the incoming file(s).
################################################################################
function out_dir( $dir=null )
{
	if( is_null($dir) ){ die( "***** ERROR : Directory is NULL" ); }
	$this->out_dir = $dir;
}
################################################################################
#	open_in_file(). Open the input file
################################################################################
function open_in_file()
{
	if( is_null($this->in_dir) || (strlen($this->in_dir) < 1) ){
		$this->in_dir = '.';
		}

	if( is_null($this->in_file) ){
		die( "***** ERROR : The Input File name is NULL" );
		}

	$this->in_fp = fopen( "$this->in_dir/$this->in_file", "rb" );
	if( $this->in_fp === false ){
		die( "***** ERROR : Could not open '$this->in_dir/$this->in_file'" );
		}

	$this->in_file_loc = 0;
	return true;
}
################################################################################
#	open_out_file(). Open the output file
################################################################################
function open_out_file()
{
#
#	If there isn't an output file name (for output and NOT the files
#	that are IN the TAR file.
#
	if( is_null($this->in_dir) || (strlen($this->in_dir) < 1) ){
		$a = explode( '.', $this->in_file );
		array_pop( $a );
		$a = implode( '.', $a );

		$this->out_dir = $a;
		if( !file_exists($a) ){ mkdir( $a, 0777, true ); }
		}

	$this->out_fp = fopen( "$this->out_dir/$this->out_file", "rb" );
	if( $this->out_fp === false ){
		die( "***** ERROR : Could not open '$this->out_dir/$this->out_file'" );
		}

	$this->out_file_loc = 0;
	return true;
}
################################################################################
#	open_dump_file(). Open the dump file.
################################################################################
function open_dump_file()
{
	if( ($this->dump_fp = fopen($this->dump_file, "w")) === false ){
		die( "***** ERROR : Could not open : $this->dump_file" );
		}

	return true;
}
################################################################################
#	get_header(). Gets the header of the TAR file
#
#	NOTES:	The following is taken from
#			https://mort.coffee/home/tar/
#
#	struct file_header {
#		char file_path[100];
#		char file_mode[8];
#		char owner_user_id[8];
#		char owner_group_id[8];
#		char file_size[12];
#		char file_mtime[12];
#		char header_checksum[8];
#		char file_type;
#		char link_path[100];
#	
#		char padding[255];
#
#		// New UStar fields
#		char magic_bytes[6];
#		char version[2];
#		char owner_user_name[32];
#		char owner_group_name[32];
#		char device_major_number[8];
#		char device_minor_number[8];
#		char prefix[155];
#
#		char padding[12];
#	};
#
################################################################################
function get_header()
{
	if( is_null($this->in_fp) ){ die( "***** ERROR : Input File Pointer is NULL" ); }
#
#	Get the header information
#
	echo "IN FILE LOC = $this->in_file_loc\n";
	echo "Last Location = $this->last_loc\n";
	if( $this->last_loc === $this->in_file_loc ){
		echo "*** INFO : We are at the right location\n";
		}
		else {
			echo "*** INFO : We are NOT at the right location\n";
			}
#
#	Save where we were
#
	$this->in_last_file_loc = $this->in_file_loc;
#
#	Now go on
#
	fseek( $this->in_fp, $this->in_file_loc, SEEK_SET );
	$this->buf = fread( $this->in_fp, 512 );
	for( $i=0; $i<512; $i++ ){
		printf( "%02x ", substr($this->buf, $i, 1) );
		if( ($i % 20) > 18 ){ echo "\n"; }
		}

	echo "\n\n";
#
#	Split it up
#
	$this->file_path = substr( $this->buf, 0, 100 );
	$this->file_mode = substr( $this->buf, 100, 8 );
	$this->owner_user_id = substr( $this->buf, 108, 8 );
	$this->owner_group_id = substr( $this->buf, 116, 8 );
	$this->file_size = substr( $this->buf, 124, 12 );
	$this->file_mtime = substr( $this->buf, 136, 12 );
	$this->header_checksum = substr( $this->buf, 148, 8 );
	$this->file_type = substr( $this->buf, 156, 1 );
	$this->link_path = substr( $this->buf, 157, 100 );
#
#	UStar addition - IF IT IS THERE
#
	$this->magic_bytes = substr( $this->buf, 257, 6 );
	$this->version = substr( $this->buf, 263, 6 );
	$this->owner_user_name = substr( $this->buf, 269, 32 );
	$this->owner_group_name = substr( $this->buf, 301, 32 );
	$this->device_major_number = substr( $this->buf, 333, 8 );
	$this->device_minor_number = substr( $this->buf, 341, 8 );
	$this->prefix = substr( $this->buf, 349, 155 );
	$this->padding = substr( $this->buf, 504, 12 );

	if( preg_match( "/\@longlink/i", $this->file_path) ){
		$size = floor( $this->file_size / 512 ) * 512;
		if( $size > 0 ){ $this->info = fread( $this->in_fp, $size ); }
			else { $this->info = ""; }
		}
		else { $size = 0; $this->info = ""; }
#
#	Remove any ^@'s in the incoming information
#
	$this->file_path = str_replace( " ", " ", $this->file_path );
	$this->file_mode = str_replace( " ", "", $this->file_mode );
	$this->owner_user_id = str_replace( " ", "", $this->owner_user_id );
	$this->owner_group_id = str_replace( " ", "", $this->owner_group_id );
	$this->file_size = str_replace( " ", "", $this->file_size );
	$this->file_mtime = str_replace( " ", "", $this->file_mtime );
	$this->header_checksum = str_replace( " ", "", $this->header_checksum );
	$this->file_type = str_replace( " ", "", $this->file_type );
	$this->link_path = str_replace( " ", " ", $this->link_path );
#
#	Ustar addition - IF IT IS THERE
#
	$this->magic_bytes = str_replace( " ", "", $this->magic_bytes );
	$this->version = str_replace( " ", "", $this->version );
	$this->owner_user_name = str_replace( " ", "", $this->owner_user_name );
	$this->owner_group_name = str_replace( " ", "", $this->owner_group_name );
	$this->device_major_number = str_replace( " ", "", $this->device_major_number );
	$this->device_minor_number = str_replace( " ", "", $this->device_minor_number );
	$this->prefix = str_replace( " ", "", $this->prefix );
	$this->padding = str_replace( " ", "", $this->padding );

	if( preg_match( "/\@longlink/i", $this->file_path) ){
		$this->info = str_replace( " ", "", $this->info );
		}
#
#	Convert the octal information to decimal
#
	$this->file_size = octdec( $this->file_size );
	$this->file_mtime = octdec( $this->file_mtime );
	$this->file_mode = octdec( $this->file_mode );

	$this->dump_header();

	if( preg_match( "/\@longlink/i", $this->file_path) ){
		$this->in_file_loc += 512 + $size;
		}
		else { $this->in_file_loc += 512; }

	if( feof($this->in_fp) ){
		echo "*** INFO : @ " . __LINE__ . " END OF FILE DETECTED\n";
		return false;
		}

	return true;
}
################################################################################
#	get_body(). Gets the the file that is after the header.
################################################################################
function get_body()
{
#
#	Calculate how big of a file we want to get
#	NOTES : ALWAYS add one block on even if there is only one byte.
#
	fseek( $this->in_fp, $this->in_file_loc, SEEK_SET );

	$size = ceil( $this->file_size / 512 );

	fprintf( $this->dump_fp, "get_body : Size = %s\n", $this->dec($size) );

	if( $size > 0 ){
		$this->file_buf = "";
		for( $i=0; $i<$size; $i++ ){
			$loc_1 = $i * 512;
			$loc_2 = $loc_1 + $this->in_file_loc;
			fprintf( $this->dump_fp, "get_body : Input File Location : %s\n", $this->dec($this->in_file_loc) );
			fprintf( $this->dump_fp, "get_body : LOC = %d, I = %d\n", $loc_1, $i );
			$s = "---Searching #$i for next header at : \n" .
				"\tStart Location(L1) : %s\n\tNext (L1 + File_Loc) : %s\n\n";
			$l1 = $this->dec( $loc_1 );
			$l2 = $this->dec( $loc_2 );
			fprintf( $this->dump_fp, $s, $l1, $l2 );

			if( ($s = fread($this->in_fp, 512)) === false ){
				echo "***** ERROR : There is some kind of a problem reading the file\n";
				echo "***** ERROR : We got a FALSE flag on the file read\n\n";
				echo "*** INFO : File Size = " . $this->dec( $this->max_size ) . "\n";
				echo "*** INFO : File Type = " . $this->dec( $this->max_file_type ) . "\n";
				}

			if( preg_match(";^marke/;i", $s) ){
				echo "***** ERROR : I think we are going past the next HEADER\n";
				echo "Continue ?";
				$s = rtrim( stream_get_line(STDIN, 1024, PHP_EOL) );
#				die( "***** ENDING PROGRAM" );
				$this->find_header();
				}
			}

		fprintf( $this->dump_fp, "Did not find a header - next record.\n\n" );
		}
		else { $this->file_buf = ""; }
#
#	Now remove EITHER null bytes or blanks from the end of the actual file.
#
	$this->file_body = substr( $this->file_buf, 0, $this->file_size );

	$size = $size * 512;
	$this->in_file_loc += $size;
	fprintf( $this->dump_fp, "get_body : Size = %s\n", $this->dec($size) );
	fprintf( $this->dump_fp, "get_body : File Size = %s\n", $this->dec($this->file_size) );

	$this->dump_body();

	if( feof($this->in_fp) ){
		echo "*** INFO : @ " . __LINE__ . " END OF FILE DETECTED\n";
		echo "*** INFO : AT " . $this->dec($this->in_file_loc) . "\n";
		return false;
		}

	return true;
}
################################################################################
#	find_header(). Find the next header.
################################################################################
function find_header()
{
#
#	First - go back to the beginning of the header and get the 
#	header. Then we just start looking for the next header. The difference
#	between the two will be used to reset the file size entry.
#
	$this->in_file_loc = $this->in_last_file_loc;
#
#	Now get the header again.
#
	$this->get_header();
	echo "File Size = " . $this->file_size . "\n";
	exit();
#
	return true;
}
################################################################################
#	put_header(). Put together the header and write it out.
################################################################################
function put_header()
{
	if( is_null($this->out_fp) ){ die( "***** ERROR : Output File Pointer is NULL" ); }
#
#	Put the header together
#
	fseek( $this->out_fp, $this->out_file_loc, SEEK_SET );
	$this->file_size = strlen( $file_body );
	if( is_link($this->file_path) ){ $this->file_type = 1; }
		else if( is_dir($this->file_path) ){ $this->file_type = 5; }
		else { $this->file_type = 0; }

	fprintf( $this->out_fp, "%100s", $this->file_path );
	fprintf( $this->out_fp, "%08o", $this->file_mode );
	fprintf( $this->out_fp, "%08d", $this->owner_user_id );
	fprintf( $this->out_fp, "%08d", $this->owner_group_id );
	fprintf( $this->out_fp, "%012o", $this->file_size );
	fprintf( $this->out_fp, "%012o", $this->file_mtime );
	fprintf( $this->out_fp, "%08d", $this->header_checksum );
	fprintf( $this->out_fp, "%1d", $this->file_type );
	fprintf( $this->out_fp, "%100s", $this->link_path );
#
#	And write it out (Commented out presently)
#
#	fwrite( $out_fp, $s, 512 );

	$this->out_file_loc += 512;
	return true;
}
################################################################################
#	put_body(). Put the file out to the TAR file.
################################################################################
function put_body()
{
	if( is_null($this->out_fp) ){ die( "***** ERROR : Output File Pointer is NULL" ); }

	fseek( $this->out_fp, $this->out_file_loc, SEEK_SET );
	$size = ceil( $this->file_size / 512 ) * 512;
	fprintf( $this->out_fp, "%" . $size . "s' ", $this->file_body );
#
#	And write it out (currently commented out)
#
#	fwrite( $out_fp, $s, $size );

	$this->out_file_loc += $size;
	return true;
}
################################################################################
#	dump_header(). Dump the header information.
################################################################################
function dump_header()
{
	$this->file_path = $this->file_path . str_repeat( " ", 100-strlen($this->file_path) );
	$this->link_path = $this->link_path . str_repeat( " ", 100-strlen($this->link_path) );

	fprintf( $this->dump_fp, "%s\n", str_repeat('-', 80) );
	fprintf( $this->dump_fp, "dump_header : File Path = %-100s\n", $this->file_path );
	fprintf( $this->dump_fp, "dump_header : File Mode = %08o\n", $this->file_mode );
	fprintf( $this->dump_fp, "dump_header : Owner User ID = %08d\n", $this->owner_user_id );
	fprintf( $this->dump_fp, "dump_header : Owner Group ID = %08d\n", $this->owner_group_id );
	fprintf( $this->dump_fp, "dump_header : File Size = %d(%0o)\n", $this->file_size, $this->file_size );
	fprintf( $this->dump_fp, "dump_header : File MTime = %012o\n", $this->file_mtime );
	fprintf( $this->dump_fp, "dump_header : Header Checksum = %08d\n", $this->header_checksum );
	fprintf( $this->dump_fp, "dump_header : File Type = %d(%0o)\n", $this->file_type, $this->file_type );
	fprintf( $this->dump_fp, "dump_header : Link Path = %-100s\n", $this->link_path );

	fprintf( $this->dump_fp, "dump_header : Magic Bytes = %6s\n", $this->magic_bytes );
	fprintf( $this->dump_fp, "dump_header : Version = %2s\n", $this->version );
	fprintf( $this->dump_fp, "dump_header : Owner User Name = %32s\n", $this->owner_user_name );
	fprintf( $this->dump_fp, "dump_header : Owner Group Name = %32s\n", $this->owner_group_name );
	fprintf( $this->dump_fp, "dump_header : Device Major Number = %8s\n", $this->device_major_number );
	fprintf( $this->dump_fp, "dump_header : Device Minor Number = %8s\n", $this->device_minor_number );
	fprintf( $this->dump_fp, "dump_header : Prefix = %-155s\n", $this->prefix );
	fprintf( $this->dump_fp, "dump_header : Padding = %-12s\n", $this->padding );

	if( preg_match( "/\@longlink/i", $this->file_path) ){
		$size = floor( $this->file_size / 512 ) * 512;
		fprintf( $this->dump_fp, "dump_header : Long Link = %-" . $size . "s\n\n", $this->info );
		}

	fprintf( $this->dump_fp, "dump_header : File Location = %s\n", $this->dec($this->in_file_loc) );
	fprintf( $this->dump_fp, "\n\n" );

	return true;
}
################################################################################
#	dump_body(). Dump the body (file) information.
################################################################################
function dump_body( $opt=true )
{
	fprintf( $this->dump_fp, "%s\n", str_repeat('=', 80) );
	fprintf( $this->dump_fp, "dump_body : File Size = %d(%0o)\n", $this->file_size, $this->file_size );
	fprintf( $this->dump_fp, "dump_body : Input File Location = %d\n", $this->in_file_loc );

	$size = ceil( $this->file_size / 512 ) * 512;

	fprintf( $this->dump_fp, "dump_body : Size = %d\n", $size );

	if( $opt ){
		fprintf( $this->dump_fp, "dump_body : Length of body = %d\n\n", $size );
		if( $size < 2048 ){
			fprintf( $this->dump_fp, "%" . $size . "s\n\n", $this->file_body );
			}
		}
		else { fprintf( $this->dump_fp, "%" . $size . "s\n\n", $this->file_body ); }

	fprintf( $this->dump_fp, "%s\n", str_repeat('#', 80) );

	return true;
}
################################################################################
#	close_in_file(). Close the input file
################################################################################
function close_in_file()
{
	fclose( $this->in_fp );

	return true;
}
################################################################################
#	close_out_file(). Close the output file
################################################################################
function close_out_file()
{
	fclose( $this->out_fp );

	return true;
}
################################################################################
#	close_dump_file(). Close the dump file
################################################################################
function close_dump_file()
{
	fclose( $this->dump_fp );

	return true;
}
################################################################################
#	dec(). Convert a Decimal value to a string
################################################################################
function dec( $i=null )
{
	if( is_null($i) ){ die( "***** ERROR : DEC = I is NULL" ); }

	$s = sprintf( "%d", $i );
	$n = strlen($s) + 1;
	$f = "[Dec = %0" . $n . "d( Oct = ";
#	echo "\nF = $f\n";
	$s = sprintf( "%o", $i );
	$n = strlen($s) + 1;
	$f .= "%0" . $n . "o, Hex = ";
#	echo "F = $f\n";
	$s = sprintf( "%x", $i );
	$n = strlen($s) + 1;
	$f .= "%0" . $n . "x )]";
#	echo "F = $f\n";

	return sprintf( $f, $i, $i, $i );
}
################################################################################
#	oct(). Convert an Octal value to a string
################################################################################
function oct( $i=null )
{
	if( is_null($i) ){ die( "***** ERROR : OCT = I is NULL" ); }

	$i = octdec( $i );
	return $this->dec( $i );
}
################################################################################
#	hex(). Convert a Hex value to a string.
################################################################################
function hex( $i=null )
{
	if( is_null($i) ){ die( "***** ERROR : OCT = I is NULL" ); }

	$i = hex2bin( $i );
	return $this->dec( $i );
}
################################################################################
#	__destruct(). Ending of the class function. Be sure to close all files.
################################################################################
function __destruct()
{
	if( is_resource($this->in_fp) ){ fclose( $this->in_fp ); }
	if( is_resource($this->out_fp) ){ fclose( $this->out_fp ); }
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['tar']) ){
		$GLOBALS['classes']['tar'] = new class_tar();
		}

?>
