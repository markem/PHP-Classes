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
#	class_arcs();
#
#-Description:
#
#	Handles all archive formats AS LONG AS YOU HAVE IT INSTALLED!!!!
#	This class will ARCHIVE or UNarchive a file.
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
#	Mark Manning			Simulacron I			Thu 04/08/2021 23:15:30.23 
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
#		CLASS_ARCS.PHP. A class to handle working with archives.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_arcs
{
#
#	Put your paths to where these programs reside.
#	Remember to change all "\"s to "/"s.
#
	private $loc_zip	=	"C:/Program_Files/WinZip";
	private $loc_7zip	=	"C:/Program Files/7-Zip";
	private $loc_rar	=	"C:/Program Files/WinRAR";
	private $prg_zip	=	"WINZIP32.EXE";
	private $prg_7zip	=	"7z.exe";
	private $prg_rar	=	"Rar.exe";
	private $prg_unzip	=	"unzip.exe";
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['arcs']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['arcs']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->debug->out();
}
################################################################################
#	zip(). Lets me archive something to a ZIP file.
#	NOTES	:	You can ONLY send single letter options to this function.
################################################################################
function zip( $opt=null, $file=null, $suf=null )
{
	if( is_null($opt) ){ die( "NO OPTIONS GIVEN" ); }
	if( is_null($file) ){ die( "NO FILE GIVEN" ); }

	$cmd = "$loc_zip/$prg_zip $opt $file";
	echo "CMD = $cmd\n";

	return system( $cmd );
}
################################################################################
#	unzip(). Allows me to unzip a ZIP file.
################################################################################
function unzip( $opt=null, $file=null, $suf=null )
{
	if( is_null($opt) ){ die( "NO OPTIONS GIVEN" ); }
	if( is_null($file) ){ die( "NO FILE GIVEN" ); }

	$cmd = "$loc_zip/$prg_unzip $opt $file";
	echo "CMD = $cmd\n";

	return system( $cmd );
}
################################################################################
#	zip_help(). Prints out the help for the gzip command.
################################################################################
function zip_help()
{
	$out = <<<EOD
# 
################################################################################
#
#	gzip 1.2.4 (18 Aug 93)
#	usage: gzip [-acdfhlLnNrtvV19] [-S suffix] [file ...]
#	 -a --ascii       ascii text; convert end-of-lines using local conventions
#	 -c --stdout      write on standard output, keep original files unchanged
#	 -d --decompress  decompress
#	 -f --force       force overwrite of output file and compress links
#	 -h --help        give this help
#	 -l --list        list compressed file contents
#	 -L --license     display software license
#	 -n --no-name     do not save or restore the original name and time stamp
#	 -N --name        save or restore the original name and time stamp
#	 -q --quiet       suppress all warnings
#	 -r --recursive   operate recursively on directories
#	 -S .suf  --suffix .suf     use suffix .suf on compressed files
#	 -t --test        test compressed file integrity
#	 -v --verbose     verbose mode
#	 -V --version     display version number
#	 -1 --fast        compress faster
#	 -9 --best        compress better
#	 file...          files to (de)compress. If none given, use standard input.
#
################################################################################
#	
EOD;

	return $out;
}
################################################################################
#	unzip_help(). Prints help about the unzip program.
################################################################################
function unzip_help()
{
	$out = <<<EOD
#
################################################################################
#
#	UnZip 6.00 of 20 April 2009, by Info-ZIP.  Maintained by C. Spieler.  Send
#	bug reports using http://www.info-zip.org/zip-bug.html; see README for details.
#	
#	Usage: unzip [-Z] [-opts[modifiers]] file[.zip] [list] [-x xlist] [-d exdir]
#	  Default action is to extract files in list, except those in xlist, to exdir;
#	  file[.zip] may be a wildcard.  -Z => ZipInfo mode ("unzip -Z" for usage).
#	
#	  -p  extract files to pipe, no messages     -l  list files (short format)
#	  -f  freshen existing files, create none    -t  test compressed archive data
#	  -u  update files, create if necessary      -z  display archive comment only
#	  -v  list verbosely/show version info       -T  timestamp archive to latest
#	  -x  exclude files that follow (in xlist)   -d  extract files into exdir
#	modifiers:
#	  -n  never overwrite existing files         -q  quiet mode (-qq => quieter)
#	  -o  overwrite files WITHOUT prompting      -a  auto-convert any text files
#	  -j  junk paths (do not make directories)   -aa treat ALL files as text
#	  -C  match filenames case-insensitively     -L  make (some) names lowercase
#	  -$  label removables (-$$ => fixed disks)  -V  retain VMS version numbers
#	  -X  restore ACLs (-XX => use privileges)   -s  spaces in filenames => '_'
#	                                             -M  pipe through "more" pager
#	See "unzip -hh" or unzip.txt for more help.  Examples:
#	  unzip data1 -x joe   => extract all files except joe from zipfile data1.zip
#	  unzip -fo foo ReadMe => quietly replace existing ReadMe if archive file newer
#
################################################################################
#
EOD;

	return $out;
}
################################################################################
#	arc7z(). Lets me ARChive something to a 7Z file.
################################################################################
function arc7z( $opt=null, $switches=null, $arc=null, $file=null )
{
	if( is_null($opt) ){ die( "NO OPTIONS GIVEN" ); }

	$cmd = "$loc_7z/$prg_7z $opt $file";
	echo "CMD = $cmd\n";

	return system( $cmd );
}
################################################################################
#	arc7z_help(). Help on the 7zip function.
################################################################################
function arc7z_help()
{
	$out = <<<EOD
#
################################################################################
#	
#	7-Zip 9.13 beta  Copyright (c) 1999-2010 Igor Pavlov  2010-04-15
#	
#	Usage: 7z <command> [<switches>...] <archive_name> [<file_names>...]
#	       [<@listfiles...>]
#	
#	<Commands>
#	  a: Add files to archive
#	  b: Benchmark
#	  d: Delete files from archive
#	  e: Extract files from archive (without using directory names)
#	  l: List contents of archive
#	  t: Test integrity of archive
#	  u: Update files to archive
#	  x: eXtract files with full paths
#
#	<Switches>
#	  -ai[r[-|0]]{@listfile|!wildcard}: Include archives
#	  -ax[r[-|0]]{@listfile|!wildcard}: eXclude archives
#	  -bd: Disable percentage indicator
#	  -i[r[-|0]]{@listfile|!wildcard}: Include filenames
#	  -m{Parameters}: set compression Method
#	  -o{Directory}: set Output directory
#	  -p{Password}: set Password
#	  -r[-|0]: Recurse subdirectories
#	  -scs{UTF-8 | WIN | DOS}: set charset for list files
#	  -sfx[{name}]: Create SFX archive
#	  -si[{name}]: read data from stdin
#	  -slt: show technical information for l (List) command
#	  -so: write data to stdout
#	  -ssc[-]: set sensitive case mode
#	  -ssw: compress shared files
#	  -t{Type}: Set type of archive
#	  -u[-][p#][q#][r#][x#][y#][z#][!newArchiveName]: Update options
#	  -v{Size}[b|k|m|g]: Create volumes
#	  -w[{path}]: assign Work directory. Empty path means a temporary directory
#	  -x[r[-|0]]]{@listfile|!wildcard}: eXclude filenames
#	  -y: assume Yes on all queries
#
################################################################################
#	
EOD;

	return $out;
}
################################################################################
#	un7z(). Allows me to unzip a 7z file.
################################################################################
function un7z( $opt=null, $switches=null, $arc=null, $file=null )
{
	if( is_null($opt) ){ die( "NO OPTIONS GIVEN" ); }

	$cmd = "$loc_7z/$prg_7z $opt $file";
	echo "CMD = $cmd\n";

	return system( $cmd );
}
################################################################################
#	rar(). Instead of using RAR - I will use rar() instead.
################################################################################
function rar( $opt=null, $switches=null, $arc=null, $file=null )
{
	if( is_null($opt) ){ die( "NO OPTIONS GIVEN" ); }

	$cmd = "$loc_rar/$prg_rar $opt $file";
	echo "CMD = $cmd\n";

	return system( $cmd );
}
################################################################################
#	unrar(). Instead of using RAR - I will use the un7z instead.
################################################################################
function unrar()
{
	$this->un7z( func_get_args() );
}
################################################################################
#	rar_help(). Help file for rar.
################################################################################
function rar_help()
{
	$out = <<<EOD

#
################################################################################
#
#RAR 5.61 x64   Copyright (c) 1993-2018 Alexander Roshal   30 Sep 2018
#Trial version             Type 'rar -?' for help
#
#Usage:     rar <command> -<switch 1> -<switch N> <archive> <files...>
#               <@listfiles...> <path_to_extract\>
#
#<Commands>
#  a             Add files to archive
#  c             Add archive comment
#  ch            Change archive parameters
#  cw            Write archive comment to file
#  d             Delete files from archive
#  e             Extract files without archived paths
#  f             Freshen files in archive
#  i[par]=<str>  Find string in archives
#  k             Lock archive
#  l[t[a],b]     List archive contents [technical[all], bare]
#  m[f]          Move to archive [files only]
#  p             Print file to stdout
#  r             Repair archive
#  rc            Reconstruct missing volumes
#  rn            Rename archived files
#  rr[N]         Add data recovery record
#  rv[N]         Create recovery volumes
#  s[name|-]     Convert archive to or from SFX
#  t             Test archive files
#  u             Update files in archive
#  v[t[a],b]     Verbosely list archive contents [technical[all],bare]
#  x             Extract files with full path
#
#<Switches>
#  -             Stop switches scanning
#  @[+]          Disable [enable] file lists
#  ac            Clear Archive attribute after compression or extraction
#  ad            Append archive name to destination path
#  ag[format]    Generate archive name using the current date
#  ai            Ignore file attributes
#  ao            Add files with Archive attribute set
#  ap<path>      Set path inside archive
#  as            Synchronize archive contents
#  c-            Disable comments show
#  cfg-          Disable read configuration
#  cl            Convert names to lower case
#  cu            Convert names to upper case
#  df            Delete files after archiving
#  dh            Open shared files
#  dr            Delete files to Recycle Bin
#  ds            Disable name sort for solid archive
#  dw            Wipe files after archiving
#  e[+]<attr>    Set file exclude and include attributes
#  ed            Do not add empty directories
#  en            Do not put 'end of archive' block
#  ep            Exclude paths from names
#  ep1           Exclude base directory from names
#  ep2           Expand paths to full
#  ep3           Expand paths to full including the drive letter
#  f             Freshen files
#  hp[password]  Encrypt both file data and headers
#  ht[b|c]       Select hash type [BLAKE2,CRC32] for file checksum
#  id[c,d,p,q]   Disable messages
#  ieml[addr]    Send archive by email
#  ierr          Send all messages to stderr
#  ilog[name]    Log errors to file
#  inul          Disable all messages
#  ioff[n]       Turn PC off after completing an operation
#  isnd          Enable sound
#  iver          Display the version number
#  k             Lock archive
#  kb            Keep broken extracted files
#  log[f][=name] Write names to log file
#  m<0..5>       Set compression level (0-store...3-default...5-maximal)
#  ma[4|5]       Specify a version of archiving format
#  mc<par>       Set advanced compression parameters
#  md<n>[k,m,g]  Dictionary size in KB, MB or GB
#  ms[ext;ext]   Specify file types to store
#  mt<threads>   Set the number of threads
#  n<file>       Additionally filter included files
#  n@            Read additional filter masks from stdin
#  n@<list>      Read additional filter masks from list file
#  o[+|-]        Set the overwrite mode
#  oc            Set NTFS Compressed attribute
#  oh            Save hard links as the link instead of the file
#  oi[0-4][:min] Save identical files as references
#  ol[a]         Process symbolic links as the link [absolute paths]
#  oni           Allow potentially incompatible names
#  or            Rename files automatically
#  os            Save NTFS streams
#  ow            Save or restore file owner and group
#  p[password]   Set password
#  p-            Do not query password
#  qo[-|+]       Add quick open information [none|force]
#  r             Recurse subdirectories
#  r-            Disable recursion
#  r0            Recurse subdirectories for wildcard names only
#  ri<P>[:<S>]   Set priority (0-default,1-min..15-max) and sleep time in ms
#  rr[N]         Add data recovery record
#  rv[N]         Create recovery volumes
#  s[<N>,v[-],e] Create solid archive
#  s-            Disable solid archiving
#  sc<chr>[obj]  Specify the character set
#  sfx[name]     Create SFX archive
#  si[name]      Read data from standard input (stdin)
#  sl<size>      Process files with size less than specified
#  sm<size>      Process files with size more than specified
#  t             Test files after archiving
#  ta<date>      Process files modified after <date> in YYYYMMDDHHMMSS format
#  tb<date>      Process files modified before <date> in YYYYMMDDHHMMSS format
#  tk            Keep original archive time
#  tl            Set archive time to latest file
#  tn<time>      Process files newer than <time>
#  to<time>      Process files older than <time>
#  ts[m|c|a]     Save or restore file time (modification, creation, access)
#  u             Update files
#  v<size>[k,b]  Create volumes with size=<size>*1000 [*1024, *1]
#  vd            Erase disk contents before creating volume
#  ver[n]        File version control
#  vn            Use the old style volume naming scheme
#  vp            Pause before each volume
#  w<path>       Assign work directory
#  x<file>       Exclude specified file
#  x@            Read file names to exclude from stdin
#  x@<list>      Exclude files listed in specified list file
#  y             Assume Yes on all queries
#  z[file]       Read archive comment from file
#
################################################################################
#
EOD;

	return $out;
}
################################################################################
#	tar(). Lets me archive something to a TAR file.
################################################################################
function tar()
{
	$args = func_get_args();
}
################################################################################
#	untar(). Allows me to unzip a TAR file.
################################################################################
function untar()
{
	$args = func_get_args();
}
################################################################################
#	tar_help(). Prints the TAR help information.
################################################################################
function tar_help()
{
	$out = <<<EOD
#
################################################################################
#	
#	tar xvzf /dir/to/file.tar.gz -C /dir/to/output/
#
#	gzip -dc < file.gz > /somewhere/file
#
#	tar(bsdtar): manipulate archive files
#	First option must be a mode specifier:
#	  -c Create  -r Add/Replace  -t List  -u Update  -x Extract
#	Common Options:
#	  -b #  Use # 512-byte records per I/O block
#	  -f <filename>  Location of archive (default \\.\tape0)
#	  -v    Verbose
#	  -w    Interactive
#	Create: tar -c [options] [<file> | <dir> | @<archive> | -C <dir> ]
#	  <file>, <dir>  add these items to archive
#	  -z, -j, -J, --lzma  Compress archive with gzip/bzip2/xz/lzma
#	  --format {ustar|pax|cpio|shar}  Select archive format
#	  --exclude <pattern>  Skip files that match pattern
#	  -C <dir>  Change to <dir> before processing remaining files
#	  @<archive>  Add entries from <archive> to output
#	List: tar -t [options] [<patterns>]
#	  <patterns>  If specified, list only entries that match
#	Extract: tar -x [options] [<patterns>]
#	  <patterns>  If specified, extract only entries that match
#	  -k    Keep (don't overwrite) existing files
#	  -m    Don't restore modification times
#	  -O    Write entries to stdout, don't restore to disk
#	  -p    Restore permissions (including ACLs, owner, file flags)
#	bsdtar 3.3.2 - libarchive 3.3.2 zlib/1.2.5.f-ipp
#
################################################################################
#
EOD;

	return $out;
}
################################################################################
#	__destruct(). Closes out the class.
################################################################################
function __destruct()
{
	$this->debug->in();
	$this->debug->out();
}
################################################################################
#	dump(). A short function to dump a file.
################################################################################
function dump( $f=null, $l=null )
{
	$this->debug->in();

	if( is_null($f) ){ $this->debug->log( "DIE : No file given", true ); }
	if( is_null($l) ){ $l = 32; }

	$fh = fopen($f, "r" );
	$r = fread( $fh, 1024 );
	fclose( $fh );

	$this->debug->msg( "Dump	: " );
	for ($i = 0; $i < $l; $i++) {
		$this->debug->msg( str_pad(dechex(ord($r[$i])), 2, '0', STR_PAD_LEFT) );
		}

	$this->debug->msg( "\nHeader  : " );
	for ($i = 0; $i < 32; $i++) {
		$s = ord( $r[$i] );
		$s = ($s > 127) ? $s - 127 : $s;
		$s = ($s < 32) ? ord(" ") : $s;
		$this->debug->msg( chr( $s ) );
		}

	$this->debug->msg( "\n" );
	$this->debug->out();

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['arcs']) ){
		$GLOBALS['classes']['arcs'] = new class_arcs();
		}

?>
