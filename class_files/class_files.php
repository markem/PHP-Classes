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
#	class_files();
#
#-Description:
#
#	A class to handle my files.
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
#	Mark Manning			Simulacron I			Sun 07/07/2019 15:33:42.40 
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
class class_files
{
	private $all_exts = null;
	private $exts = null;
	private $algos = null;

	public $debug = null;
	public $temp_path = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['files']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['files']; }
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

	$this->exts = array();		#	File Extension RegExps
	$this->exts['png'] = "png|pngp";
	$this->exts['bmp'] = "bmp";
	$this->exts['gif'] = "gif";
	$this->exts['tif'] = "tif|tiff";
	$this->exts['jpg'] = "exif|jfif|jfi|jpg|jpeg";
	$this->exts['webp'] = "web|webp";

	$this->all_exts = "";
	foreach( $this->exts as $k=>$v ){
		$this->all_exts .= "$v|";
		}

	$this->algos = hash_algos();

	$this->temp_path = "c:/temp/files";
	if( !file_exists("c:/temp") ){ mkdir( "c:/temp" ); chmod( "c:/temp", 0777 ); }
	if( !file_exists("c:/temp/files") ){
		mkdir( "c:/temp/files" ); chmod( "c:/temp/files", 0777 );
		}

	$this->all_exts = substr( $this->all_exts, 0, -1 );
	$this->debug->out();
}

################################################################################
#	get_files(). A function to get a list of files from a directory
#		AND that directory's sub-directories.
################################################################################
function get_files( $top_dir=null, $regexp=null, $opt=null )
{
	$this->debug->in();

	if( is_null($top_dir) ){ $top_dir = "./"; }
	if( is_null($regexp) ){ $regexp = "/.*/"; }
	if( is_null($opt) ){ $opt = true; }
	if( !preg_match(";/;", $regexp) ){ $regexp = "/" . $regexp . "/"; }

	$this->debug->msg( "Here : " . __LINE__ . "\n" );
	$dirs[] = $top_dir;
	$bad = array();
	$files = array();
	$this->debug->log( "Line " . __LINE__ . " : TOP_DIR = $top_dir\n" );
	while( count($dirs) > 0 ){
		$this->debug->msg( $dirs );
		$this->debug->msg( "Here : " . __LINE__ . "\n" );
		$dir = array_pop( $dirs );
		$this->debug->msg( "Line " . __LINE__ . " : DIR = $dir\n" );
		if( strlen(trim($dir)) < 1 ){
			$this->debug->msg( "Line " . __LINE__ . " : Aborting - blank dir\n" );
			continue;
			}
#
#	See if we have permissions to read this.
#
		$this->debug->msg( "Line " . __LINE__ . " : Getting permissions from : $dir\n" );
		$perms = explode( ",", $this->get_perms($dir) );

		if( !is_array($perms) ){ $this->debug->die( "PERMS is not an array!" ); }
		if( count($perms) < 1 ){ $this->debug->msg( $perms ); $this->debug->die( "PERMS is blank!"); }
		if( !$perms[0] === 'd' ){ continue; }
		if( ($perms[1] === '-') || ($perms[2] === '-') ){ continue; }
		if( ($perms[4] === '-') || ($perms[5] === '-') ){ continue; }
		if( ($perms[7] === '-') || ($perms[8] === '-') ){ continue; }
		$this->debug->msg( "Here : " . __LINE__ . "\n" );

		if( ($dh = @opendir($dir)) ){
			if( !is_resource($dh) ){ continue; }
			while( ($file = readdir($dh)) !== false ){
				$curfile = "$dir/$file";
				$this->debug->msg( "FOUND : $file\n" );
#				$this->dump( "regexp", __LINE__, $regexp );
#				$this->dump( "file", __LINE__, $file );
				if( $file != "." && $file != ".." ){
					if( is_dir("$dir/$file") && $opt == true){ $dirs[] = "$dir/$file"; }
						else if( preg_match($regexp, $file) ){ $files[] = "$dir/$file"; }
						else { $bad[] = "$dir/$file"; }
					}
					else { $this->debug->msg( "Discarding '.' or '..'\n" ); }
				}

			closedir( $dh );
			}
		}

	foreach( $files as $k=>$v ){
		$l = str_replace("\\", "/", $v );
		$files[$k] = str_replace("//", "/", $l );
		}

	foreach( $files as $k=>$v ){
		if( !preg_match("/mac/i", PHP_OS) && preg_match("/__macosx/i", $v) ){
			$bad[] = $v;
			unset( $files[$k] );
			}
		}

	$files = array_reverse( array_reverse($files) );

	foreach( $bad as $k=>$v ){
		$l = str_replace("\\", "/", $v );
		$bad[$k] = str_replace("//", "/", $l );
		}

	$bad = array_reverse( array_reverse($bad) );

	$this->debug->out();

	return array( $files, $bad );
}
################################################################################
#	get_dirs(). A function to get a list of directories from a directory
#		AND that directory's sub-directories.
#	Notes:	Modified. It now returns an array[directory]=Number of files
################################################################################
function get_dirs( $top_dir=null, $regexp=null, $opt=null )
{
	$this->debug->in();

	if( is_null($top_dir) ){ $top_dir = "./"; }
	if( is_null($regexp) ){ $regexp = "/.*/"; }
	if( is_null($opt) ){ $opt = true; }

	$files = array();
	$dirs[] = $top_dir;
	while( count($dirs) > 0 ){
		$dir = array_pop( $dirs );
		$dir = str_replace( "\\", "/", $dir );
		$dir = str_replace( "//", "/", $dir );
		if( !file_exists($dir) ){
			echo "GET_DIRS : FILE does not exist ($dir)\n";
			return false;
			}
		$perms = $this->get_perms( $dir );
		if( !preg_match("/r,w,x$/i", $perms) ){ continue; }
		$files[$dir] = 0;
		if( ($dh = opendir($dir)) ){
			while( ($file = readdir($dh)) !== false ){
				$cur_dir = "$dir/$file";
				$this->debug->msg( "FOUND : $file\n" );
				if( $file != "." && $file != ".." ){
					if( preg_match($regexp, $cur_dir) ){
						if( is_dir($cur_dir) && $opt == true){ $files[$dir]++; $dirs[] = $cur_dir; }
							else { $files[$dir]++; }
						}
					}
				}

			closedir( $dh );
			}
			else {
				echo "PERMS = $perms\n";
				echo "Skipping $dir\n";
				continue;
				}
		}

	foreach( $files as $k=>$v ){
		$l = str_replace("\\", "/", $k );
		$l = str_replace("//", "/", $l );
		unset( $files[$k] );
		$files[$l] = $v;
		}

	$this->debug->out();

	return $files;
}
################################################################################
#	get_perms(). Gets the permissons of any file/directory sent to it.
#
#	Notes	:	Taken from : https://www.php.net/manual/en/function.fileperms.php
#
################################################################################
function get_perms( $file )
{
	if( !file_exists($file) ){
		$this->debug->msg( "Line " . __LINE__ . " : No such file : $file\n" );
		return false;
		}

	if( ($perms = fileperms($file)) === false ){
		$this->debug->msg( "Line " . __LINE__ . " : Fileperms returned an ERROR : $perms\n" );
		return false;
		}

	switch ($perms & 0xF000){
		case 0xC000: // socket
			$info = 's,';
			break;
		case 0xA000: // symbolic link
			$info = 'l,';
			break;
		case 0x8000: // regular
			$info = 'r,';
			break;
		case 0x6000: // block special
			$info = 'b,';
			break;
		case 0x4000: // directory
			$info = 'd,';
			break;
		case 0x2000: // character special
			$info = 'c,';
			break;
		case 0x1000: // FIFO pipe
			$info = 'p,';
			break;
		default: // unknown
			$info = 'u,';
		}
#
#	Owner
#
	$info .= (($perms & 0x0100) ? 'r,' : '-,');
	$info .= (($perms & 0x0080) ? 'w,' : '-,');
	$info .= (($perms & 0x0040) ?
		(($perms & 0x0800) ? 's,' : 'x,' ) :
		(($perms & 0x0800) ? 'S,' : '-,'));
#
#	Group
#
	$info .= (($perms & 0x0020) ? 'r,' : '-,');
	$info .= (($perms & 0x0010) ? 'w,' : '-,');
	$info .= (($perms & 0x0008) ?
		(($perms & 0x0400) ? 's,' : 'x,' ) :
		(($perms & 0x0400) ? 'S,' : '-,'));
#
#	World
#
	$info .= (($perms & 0x0004) ? 'r,' : '-,');
	$info .= (($perms & 0x0002) ? 'w,' : '-,');
	$info .= (($perms & 0x0001) ?
		(($perms & 0x0200) ? 't,' : 'x,' ) :
		(($perms & 0x0200) ? 'T,' : '-,'));
#
#	Remove the last comma
#
	$info = substr( $info, 0, -1 );

	$this->debug->out();
	return $info;
}
################################################################################
#	get_image(). A function to load in an image. Returns GD as a true color
#		with transparecy kept. This routine makes use of Erwin Bon's
#		ConvertBMP2GD() set of functions which will write a BMP file out to
#		a GD file and then immediately read it back in.
#
#		No copyright infringement is meant by using the above named routines.
#		The software still belongs to the original authors. Just using them
#		because they work! :-)
################################################################################
function get_image( $file )
{
	$this->debug->in();

	$c = 0;

	try {
		if( preg_match("/gif$/i", $file) ){
			$this->debug->msg( "Loading GIF file : $file\n" );
			$gd = imagecreatefromgif($file);
			}
			else if( preg_match("/gd$/i", $file) ){
				$this->debug->msg( "Loading GD file : $file\n" );
				$gd = imagecreatefromgd($file);
				}
			else if( preg_match("/gd2$/i", $file) ){
				$this->debug->msg( "Loading GD2 file : $file\n" );
				$gd = imagecreatefromgd2($file);
				}
			else if( preg_match("/(jpg|jpeg|exif|jfif|jfi)$/i", $file) ){
				$this->debug->msg( "Loading JPG file : $file\n" );
				$gd = imagecreatefromjpeg($file);
				}
			else if( preg_match("/wbmp$/i", $file) ){
				$this->debug->msg( "Loading WBMP file : $file\n" );
				$gd = imagecreatefromwbmp($file);
				}
			else if( preg_match("/bmp$/i", $file) ){
				$this->debug->msg( "Loading BMP file : $file\n" );
				$gd = imagecreatefrombmp($file);
				}
			else if( preg_match("/xbm$/i", $file) ){
				$this->debug->msg( "Loading XBM file : $file\n" );
				$gd = imagecreatefromxbm($file);
				}
			else if( preg_match("/xpm$/i", $file) ){
				$this->debug->msg( "Loading XPM file : $file\n" );
				$gd = imagecreatefromxpm($file);
				}
			else if( preg_match("/png$/i", $file) ){
				$this->debug->msg( "Loading PNG file : $file\n" );
				$gd = imagecreatefrompng( $file );
				}
			else if( preg_match("/(web|webp)$/i", $file) ){
				$this->debug->msg( "Loading WEBP file : $file\n" );
				$gd = imagecreatefromwebp($file);
				}
			else if( preg_match("/(tif|tiff)$/i", $file) ){
				$this->debug->msg( "DIE : GD does not do TIF : $file\n" );
				return false;
				}
			else {
				$this->debug->msg( "DIE : Unknown file format : $file\n" );
				return false;
				}
		}
		catch( exception $e ){
			$this->debug->msg( $e->getMessage(), true );
			}

	if( !is_resource($gd) ){
		$this->debug->msg( "DIE : GD is NOT a resource" );
		return false;
		}

#	if( $this->debug_flag ){ imagepng( $gd, "./" . __FUNCTION__ . "-image-$c.png" ); $c++; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$gd2 = imagecreatetruecolor( $w, $h );

	$color = imagecolorat( $gd, 0, 0 );
	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;
#
#	Get a unique color for the transparent color
#
	$transparent = $this->unique_color( $gd );
	$ta = ($color >> 24) & 0xff;
	$tr = ($color >> 16) & 0xff;
	$tg = ($color >> 8) & 0xff;
	$tb = $color & 0xff;
#
#	by sbeam on StackOverflow
#
	if (function_exists('imagecolorallocatealpha')) {
		imagealphablending($gd2, false);
		imagesavealpha($gd2, true);
		$transparent = imagecolorallocatealpha($gd2, $tr, $tg, $tb, $ta );
		imagefilledrectangle($gd2, 0, 0, $w, $h, $transparent);

		imagecopyresampled( $gd2, $gd, 0, 0, 0, 0, $w, $h, $w, $h );

#		if( $this->debug_flag ){ imagepng( $gd, "./" . __FUNCTION__ . "-image-$c.png" ); $c++; }
		imagedestroy( $gd );
#		if( $this->debug_flag ){ imagepng( $gd2, "./" . __FUNCTION__ . "-image-$c.png" ); $c++; }
		}

	$this->debug->out();

	return $gd2;
}
################################################################################
#	put_image(). A function to save an image.
#		old_imagebmp() is taken from the PHP documentation website and is written
#			by shd at earthling dot net.
#
#		No copyright infringement is meant by using the above named routines.
#		The software still belongs to the original authors. Just using them
#		because they work! :-)
#################################################################################
function put_image( $gd, $file, $del=true )
{
	$this->debug->in();

	$file = trim( $file );

	$flag = false;
	if( file_exists($file) ){
		$flag = true;
		$base = basename( $file );
		$tmp = explode( '.', $base );
		$path = dirname( $file );
		$old_file = $file;
		$string = bin2hex(openssl_random_pseudo_bytes(10)); // 20 chars
		$file = "$path/$string.$tmp[1]";
		}

	if( preg_match("/gif$/i", $file) ){ $ret = imagegif($gd, $file); }
		else if( preg_match("/gd$/i", $file) ){ $ret = imagegd($gd, $file); }
		else if( preg_match("/gd2$/i", $file) ){ $ret = imagegd2($gd, $file); }
		else if( preg_match("/(jpg|jpeg)$/i", $file) ){ $ret = imagejpeg($gd, $file); }
		else if( preg_match("/wbmp$/i", $file) ){ $ret = imagewbmp($gd, $file); }
		else if( preg_match("/bmp$/i", $file) ){ $ret = imagebmp($gd, $file); }
		else if( preg_match("/xbm$/i", $file) ){ $ret = imagexbm($gd, $file); }
		else if( preg_match("/(web|webp)$/i", $file) ){ $ret = imagewebp($gd, $file); }
		else if( preg_match("/png$/i", $file) ){ $ret = imagepng($gd, $file); }
		else { $this->debug->msg( "Unknown file format....aborting", true ); }

	if( $flag ){ rename( $file, $old_file ); }
	if( $del ){ imagedestroy( $gd ); }

	$this->debug->out();

	return $ret;
}
################################################################################
#	dup_image(). A function to duplicate an image.
################################################################################
function dup_image( $gd=null )
{
	$this->debug->in();

	if( is_null($gd) ){
		$this->debug->msg( "No image sent over...aborting.\n" );
		return false;
		}

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$gd2 = imagecreatetruecolor( $w, $h );
#
#	Get a unique color for the transparent color
#
	$transparent = $this->unique_color( $gd );
	$ta = ($color >> 24) & 0xff;
	$tr = ($color >> 16) & 0xff;
	$tg = ($color >> 8) & 0xff;
	$tb = $color & 0xff;

	if( function_exists('imagecolorallocatealpha') ){
		imagealphablending($gd2, false);
		imagesavealpha($gd2, true);
		$transparent = imagecolorallocatealpha( $gd2, $tr, $tg, $tb, $ta );
		imagefilledrectangle($gd2, 0, 0, $w, $h, $transparent);

		imagecopyresampled( $gd2, $gd, 0, 0, 0, 0, $w, $h, $w, $h );

#		if( $this->debug_flag ){ imagepng( $gd, "./" . __FUNCTION__ . "-image-$c.png" ); $c++; }

		imagedestroy( $gd );

#		if( $this->debug_flag ){ imagepng( $gd2, "./" . __FUNCTION__ . "-image-$c.png" ); $c++; }
		}

	$this->debug->out();

	return $gd2;
}
################################################################################
#	blank_image(). Send over an image and you get a blank image of the same size
#		back. If you provide an RGB color - it is set to that color.
#		Remember that zero(0) is the same as black in RGBA.
################################################################################
function blank_image( $gd=null, $rgb=null )
{
	$this->debug->in();

	if( is_null($gd) ){
		$this->debug->msg( "No image sent over...aborting.\n" );
		return false;
		}

	if( is_null($rgb) ){ $rgb = 0x7fffffff; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$gd2 = imagecreatetruecolor( $w, $h );

	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );

	if( !is_null($rgb) ){
		$a = ($rgb >> 24) & 0xff;
		$r = ($rgb >> 16) & 0xff;
		$g = ($rgb >> 8) & 0xff;
		$b = $rgb & 0xff;

		$c = imagecolorallocatealpha( $gd2, $r, $g, $b, $a );
		imagefilledrectangle($gd2, 0, 0, $w, $h, $c);
		}

	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	saale_image(). Scale an image for me.
#	Notes: I thought it would be nice to be able to do this three different ways.
################################################################################
function scale_image( $gd=null, $sx=null, $sy=null )
{
	$this->debug->in();
	$this->debug->msg( "SX = $sx, SY = $sy\n" );
#
#	This can come over as:
#
#	ARRAY	:	SX, SY (ex: array(4,5) )
#	STRING	:	"SXxSY" (ex: "$x10")
#	Two Separate Values (ie: $sx=5, $sy=99)
#
	if( is_array($sx) ){ $s1 = $sx[0]; $sy = $sx[1]; unset($sx); $sx = $s1; }
		else if( preg_match("/x/i", $sx) ){ $s = explode( 'x', $sx ); unset($sx); $sx = $s[0]; $sy = $s[1]; }

	if( is_null($gd) ){ $this->debug->msg( "GD is NULL", true ); }
	if( is_null($sx) ){ $this->debug->msg( "SX is NULL", true ); }
	if( is_null($sy) ){ $this->debug->msg( "SY is NULL", true ); }

#	$gd2 = imagescale( $gd, $sx, $sy,  IMG_BICUBIC );
	$gd2 = imagescale( $gd, $sx, $sy );
	imagealphablending($gd2, false);
	imagesavealpha($gd2, true);

	imagedestroy( $gd );

	$this->debug->out();

	return $gd2;
}
################################################################################
#	"Erwin Bon" <er...@verkerk.nl> wrote in message
#	news:3fc9d260$0$4671$1b62eedf@news.euronet.nl...
################################################################################
function ConvertBMP2GD($src, $dest = false)
{
	$this->debug->in();

	if (!($src_f = fopen($src, "rb"))) { return false; }
	if (!($dest_f = fopen($dest, "wb"))) { return false; }
	$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f, 14));
	$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
		fread($src_f, 40));

	extract($info);
	extract($header);

	if ($type != 0x4D42) {
#
#	signature "BM"
#
		return false;
		}

	$palette_size = $offset - 54;
	$ncolor = $palette_size / 4;
	$gd_header = "";
#
#	true-color vs. palette
#
	$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
	$gd_header .= pack("n2", $width, $height);
	$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
	if ($palette_size) { $gd_header .= pack("n", $ncolor); }
#
#	no transparency
#
	$gd_header .= "\xFF\xFF\xFF\xFF";

	fwrite($dest_f, $gd_header);

	if ($palette_size) {
		$palette = fread($src_f, $palette_size);
		$gd_palette = "";
		$j = 0;
		while ($j < $palette_size) {
			$b = $palette[$j++];
			$g = $palette[$j++];
			$r = $palette[$j++];
			$a = $palette[$j++];
			$gd_palette .= "$r$g$b$a";
			}

		$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
		fwrite($dest_f, $gd_palette);
		}

	$scan_line_size = (($bits * $width) + 7) >> 3;
	$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;

	for ($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
#
#	BMP stores scan lines starting from bottom
#
		fseek($src_f, $offset + (($scan_line_size + $scan_line_align) * $l));
		$scan_line = fread($src_f, $scan_line_size);
		if ($bits == 24) {
			$gd_scan_line = "";
			$j = 0;
			while ($j < $scan_line_size) {
				$b = $scan_line[$j++];
				$g = $scan_line[$j++];
				$r = $scan_line[$j++];
				$gd_scan_line .= "\x00$r$g$b";
				}
			}
			elseif( $bits == 8 ){
				$gd_scan_line = $scan_line;
				}
			elseif( $bits == 4 ){
				$gd_scan_line = "";
				$j = 0;
				while ($j < $scan_line_size) {
					$byte = ord($scan_line[$j++]);
					$p1 = chr($byte >> 4);
					$p2 = chr($byte & 0x0F);
					$gd_scan_line .= "$p1$p2";
					}

				$gd_scan_line = substr($gd_scan_line, 0, $width);
				}
				elseif( $bits == 1 ){
					$gd_scan_line = "";
					$j = 0;
					while ($j < $scan_line_size) {
						$byte = ord($scan_line[$j++]);
						$p1 = chr((int)(($byte & 0x80) != 0));
						$p2 = chr((int)(($byte & 0x40) != 0));
						$p3 = chr((int)(($byte & 0x20) != 0));
						$p4 = chr((int)(($byte & 0x10) != 0));
						$p5 = chr((int)(($byte & 0x08) != 0));
						$p6 = chr((int)(($byte & 0x04) != 0));
						$p7 = chr((int)(($byte & 0x02) != 0));
						$p8 = chr((int)(($byte & 0x01) != 0));
						$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
						}

					$gd_scan_line = substr($gd_scan_line, 0, $width);
					}

		fwrite($dest_f, $gd_scan_line);
		}

	fclose($src_f);
	fclose($dest_f);

	$this->debug->out();

	return $gd;
}
################################################################################
#	old_imagebmp(). Creates a BMP file.
#	 From : shd at earthling dot net
################################################################################
function old_imagebmp ($im, $fn = false)
{
	$this->debug->in();

	if( !is_resource($im) ) return false;

	if ($fn === false) $fn = 'php://output';
	$f = fopen ($fn, "w");
	if (!$f) return false;
#
#	Image dimensions
#
	$biWidth = imagesx ($im);
	$biHeight = imagesy ($im);
	$biBPLine = $biWidth * 3;
	$biStride = ($biBPLine + 3) & ~3;
	$biSizeImage = $biStride * $biHeight;
	$bfOffBits = 54;
	$bfSize = $bfOffBits + $biSizeImage;
#
#	BITMAPFILEHEADER
#
	fwrite ($f, 'BM', 2);
	fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));
#
#	BITMAPINFO (BITMAPINFOHEADER)
#
	fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));

	$numpad = $biStride - $biBPLine;
	for($y = $biHeight - 1; $y >= 0; --$y){
		for($x = 0; $x < $biWidth; ++$x){
			$col = imagecolorat ($im, $x, $y);
			fwrite ($f, pack ('V', $col), 3);
			}

		for ($i = 0; $i < $numpad; ++$i)
			fwrite ($f, pack ('C', 0));
		}

	fclose($f);

	$this->debug->out();

	return true;
}
################################################################################
#	old_imagecreatefrombmp(). Get a BMP image.
################################################################################
function old_imagecreatefrombmp($file)
{
	$this->debug->in();

	$tmp_name = tempnam("./temp_files", "GD");
	if (ConvertBMP2GD($file, $tmp_name)){
		$img = imagecreatefromgd($tmp_name);
		unlink($tmp_name);
		return $img;
		}

	$this->debug->out();

	return false;
}
################################################################################
#	find_box(). Finds the size of the box.
################################################################################
function find_box( $gd, $color, $offset=0 )
{
	$this->debug->in();

	if( !is_resource($gd) ){ return false; }

    $color = imagecolorat( $gd, 0, 0 );
    $w = imagesx( $gd );
    $h = imagesy( $gd );

    $top = 99999;
    $bot = -99999;

    $left = 99999;
    $right = -99999;

    for( $x=0; $x<$w; $x++ ){
        for( $y=0; $y<$h; $y++ ){
            $rgb = imagecolorat( $gd, $x, $y );
            if( ($color != $rgb) && ($y < $top) ){ $top = $y; }
            if( ($color != $rgb) && ($y > $bot) ){ $bot = $y; }
            if( ($color != $rgb) && ($x < $left) ){ $left = $x; }
            if( ($color != $rgb) && ($x > $right) ){ $right = $x; }
            }
        }

    if( $top == 99999 || $bot == -99999 || $left = 99999 || $right = -99999 ){ return null; }

    $top -= 5;
    $left -= 5;
    $right += 5;
    $bot += 5;

	$this->debug->out();

	return array( $top, $bot, $left, $right );
}
################################################################################
#
#	From:
#		https://stackoverflow.com/questions/7497733/how-can-i-use-php-to-check-if-a-directory-is-empty
#
#	Added $regexp - exclusion regexp. IE : Exclude these files from count.
#	NOTE : The "." and ".." items are immediately taken out of the item list.
#
################################################################################
function is_dir_empty( $dir, $regexp=null )
{
	$this->debug->in();

	if (!is_readable($dir)) return false;
	$scan = scandir( $dir );
	foreach( $scan as $k=>$v ){
		if( preg_match("/^(\.|\.\.)$/", $v) ){ unset( $scan[$k] ); }
		if( !is_null($regexp) && preg_match($regexp, $v) ){ unset( $scan[$k] ); }
		}

	$this->debug->out();

	return (count($scan) < 1 ? true : false);
}
################################################################################
#	trim_image(). A function to take an image and trim it down.
#	NOTES: You MUST leave the transparency color to be white. Any other color
#		does NOT work. Talk about weird!
#	VARS:	$os = $open_spaces around the image.
################################################################################
function trim_image( $gd )
{
	$this->debug->in();

	$fc = 0;

    $w = imagesx( $gd );
    $h = imagesy( $gd );

    $w2 = $w + 100;
    $h2 = $h + 100;

    $color = imagecolorat( $gd, 0, 0 );
	$this->debug->msg( "COLOR #1 = " . dechex($color) . "\n" );

	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;

    $gd2 = imagecreatetruecolor( $w2, $h2 );
#
#	by sbeam on StackOverflow
#
	if (function_exists('imagecolorallocatealpha')) {
		imagealphablending($gd2, false);
		imagesavealpha($gd2, true);
		$transparent = imagecolorallocatealpha($gd2, $r, $g, $b, $a );
		imagefilledrectangle($gd2, 0, 0, $w2, $h2, $transparent);

		imagecopyresampled( $gd2, $gd, 50, 50, 0, 0, $w, $h, $w, $h );

#		for( $x=0; $x<$w; $x++ ){
#			for( $y=0; $y<$h; $y++ ){
#				$color = imagecolorat( $gd, $x, $y );
#				imagesetpixel( $gd2, $x+50, $y+50, $color );
#				}
#			}

		imagedestroy( $gd );
		}
		else {
			$this->debug->msg( "NO imagecolorallocatealpha function", true );
			}

	if( is_resource($this->debug) ){
		$this->debug->msg( "W = $w, H = $h\n" );
		$this->debug->msg( "W2 = $w2, H2 = $h2\n" );
		$this->debug->msg( "Saving : ./" . __FUNCTION__ . "-image-$fc.png @ " . __LINE__ . "\n" );
		imagepng( $gd2, "./" . __FUNCTION__ . "-image-$fc.png" );
		$fc++;
		}

    $color = imagecolorat( $gd2, 0, 0 );
	$this->debug->msg( "COLOR #1 = " . dechex($color) . "\n" );

	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;

	$this->debug->msg( "A = $a, R = $r, G = $g, B = $b\n" );
	$this->debug->msg( "COLOR #2 = " . dechex($color) . "\n" );

    $w = imagesx( $gd2 );
    $h = imagesy( $gd2 );

	$top = $bot = $left = $right = 0;
#
#	Left
#
	$flag = false;
	$this->debug->msg( "Working on the LEFT part\n" );
    for( $x=0; $x<$w; $x++ ){
        for( $y=0; $y<$h; $y++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->msg( "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $left = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	$this->debug->msg( "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
#
#	Top
#
	$flag = false;
	$this->debug->msg( "Working on the TOP part\n" );
	for( $y=0; $y<$h; $y++ ){
		for( $x=0; $x<$w; $x++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->msg( "Top : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $top = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Right
#
	$flag = false;
	$this->debug->msg( "Working on the RIGHT part\n" );
	for( $x=($w-1); $x>0; $x-- ){
		for( $y=($h-1); $y>0; $y-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->msg( "Right : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $right = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Bottom
#
	$flag = false;
	$this->debug->msg( "Working on the BOTTOM part\n" );
	for( $y=($h-1); $y>0; $y-- ){
		for( $x=($w-1); $x>0; $x-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->msg( "Bottom : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $bot = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	$this->debug->msg( "Left = $left, Right = $right, Top = $top, Bottom = $bot\n" );

    if( ($top == 99999) || ($bot == -99999) || ($left == 99999) || ($right == -99999) ){ return null; }

	$os = 3;
    $w2 = abs($right - $left) + ($os * 2) + 1;
    $h2 = abs($bot - $top) + ($os * 2) + 1;

    $color = imagecolorat( $gd2, 0, 0 );
	$this->debug->msg( "COLOR #1 = " . dechex($color) . "\n" );

	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;


    $gd = imagecreatetruecolor( $w2, $h2 );
#
#	by sbeam on StackOverflow
#
	if (function_exists('imagecolorallocatealpha')) {
		imagealphablending($gd, false);
		imagesavealpha($gd, true);
		$transparent = imagecolorallocatealpha($gd, $r, $g, $b, $a );
		imagefilledrectangle($gd, 0, 0, $w2, $h2, $transparent);

		imagecopyresampled ( $gd, $gd2, $os, $os, $left, $top, $w2+$os, $h2+$os, $w2+$os, $h2+$os );
		imagedestroy( $gd2 );
		}
		else {
			$this->debug->msg( "NO imagecolorallocatealpha function", true );
			}

	if( is_resource($this->debug) ){
		$this->debug->msg( "W = $w, H = $h" );
		$this->debug->msg( "W2 = $w2, H2 = $h2" );
		$this->debug->msg( "Saving : ./" . __FUNCTION__ . "-image-$fc.png @ " );
		imagepng( $gd, "./" . __FUNCTION__ . "-image-$fc.png" );
		$fc++;
		}

	$this->debug->out();

    return $gd;
}
################################################################################
#	rotateX(). Rotates around the X axis
#	Author:	 anon at here dot com
#	NOTES:	Originally from www.php.net.
################################################################################
function rotateX($x, $y, $theta)
{
	$this->debug->in();
	$this->debug->out();
	return $x * cos($theta) - $y * sin($theta);
}
################################################################################
#	rotateY(). Rotates around the Y axis
#	Author:	 anon at here dot com
#	NOTES:	Originally from www.php.net.
################################################################################
function rotateY($x, $y, $theta)
{
	$this->debug->in();
	$this->debug->out();
	return $x * sin($theta) + $y * cos($theta);
}
################################################################################
#	rot_image(). Rotate an image X degrees
#	Author:	 anon at here dot com
#	NOTES:	Originally from www.php.net.
#	Unfinished imagerotate replacement. ignore_transparent is,
#	well, ignored. :) Also, should have some standard functions
#	for 90, 180 and 270 degrees, since they are quite easy to
#	implement faster.
################################################################################
function rot_image( &$srcImg=null, $angle=null, $bgcolor=null, $ignore_transparent=0 )
{
	$this->debug->in();
	if( is_null($srcImg) ){ $this->debug->msg( "GD is NULL", true ); }
	if( is_null($angle) ){ $this->debug->msg( "NO ANGLE GIVEN", true ); }
	if( is_null($bgcolor) ){ $bgcolor = imagecolorat( $srcImg, 0, 0 ); }
#
#	Get the RGBA parts of the background color
#
	$a = ($bgcolor >> 24) & 0xff;
	$r = ($bgcolor >> 16) & 0xff;
	$g = ($bgcolor >> 8) & 0xff;
	$b = $bgcolor & 0xff;

    $srcw = imagesx($srcImg);
    $srch = imagesy($srcImg);
 
    if($angle == 0) return $srcImg;
   
    // Convert the angle to radians
    $theta = deg2rad ($angle);

   
    // Calculate the width of the destination image.
    $temp = array (    $this->rotateX(0,     0, 0-$theta),
                    $this->rotateX($srcw, 0, 0-$theta),
                    $this->rotateX(0,     $srch, 0-$theta),
                    $this->rotateX($srcw, $srch, 0-$theta)
                );
    $minX = floor(min($temp));
    $maxX = ceil(max($temp));
    $width = $maxX - $minX;
   
    // Calculate the height of the destination image.
    $temp = array (    $this->rotateY(0,     0, 0-$theta),
                    $this->rotateY($srcw, 0, 0-$theta),
                    $this->rotateY(0,     $srch, 0-$theta),
                    $this->rotateY($srcw, $srch, 0-$theta)
                );
    $minY = floor(min($temp));
    $maxY = ceil(max($temp));
    $height = $maxY - $minY;
   
    $destimg = imagecreatetruecolor( $width, $height );
	if (function_exists('imagecolorallocatealpha')) {
		imagealphablending($destimg, false);
		imagesavealpha($destimg, true);
		$transparent = imagecolorallocatealpha($destimg, $r, $g, $b, $a );
		imagefilledrectangle($destimg, 0, 0, $width, $height, $transparent);
		}

    // sets all pixels in the new image
    for($x=$minX;$x<$maxX;$x++) {
        for($y=$minY;$y<$maxY;$y++)
        {
            // fetch corresponding pixel from the source image
            $srcX = round($this->rotateX($x, $y, $theta));
            $srcY = round($this->rotateY($x, $y, $theta));
            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch)
            {
                $color = imagecolorat($srcImg, $srcX, $srcY );
            }
            else
            {
                $color = $bgcolor;
            }
            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
        }
    }
   
	$this->debug->out();
    return $destimg;
}
################################################################################
#	rt_image(). Rotate and Trim an image
################################################################################
function rt_image( $gd )
{
	$this->debug->in();
	if( is_null($gd) ){ $this->debug->msg( "GD is NULL", true ); }

	$a = 0;
	$angle = 0;
	$w = imagesx( $gd );
	$h = imagesy( $gd );
	$aw = $w;
	$ah = $h;

	$trans = imagecolorat( $gd, 0, 0 );

	for( $i=15; $i<180; $i+=15 ){
		$gd2 = $this->rot_image( $gd, $i, $trans );
		$gd2 = $this->trim_image( $gd2 );
#
#	Get the width & height of both
#
		$w2 = imagesx( $gd2 );
		$h2 = imagesy( $gd2 );
#
#	Now check them
#
		$this->debug->msg( "I = $i, W = $w - $w2, H = $h - $h2\n" );
#		imagepng( $gd2, "./test-3.png" ); sleep( 1 );

		if( ($w2 * $h2) < ($aw * $ah) ){
			$angle = $i;
			$aw = $w2;
			$ah = $h2;
			}
		}

	if( $angle > 0 ){
		$this->debug->msg( "Returning NEW : Angle = $angle, W = $aw, H = $ah\n" );
		$gd2 = $this->rot_image( $gd, $angle );
		$gd2 = $this->trim_image( $gd2 );

		$this->debug->out();
		return $gd2;
		}

	$this->debug->msg( "Returning OLD\n" );
	$this->debug->out();
	return $gd;
}
################################################################################
#	unique_color(). Get a unique_color.
################################################################################
function unique_color( $gd )
{
	$this->debug->in();

	$a1 = 0;
	$r1 = 255;
	$g1 = 255;
	$b1 = 255;

	$w = imagesx( $gd );
	$h = imagesy( $gd );
	for( $i=0; $i<$w; $i++ ){
		for( $j=0; $j<$h; $j++ ){
			$color = imagecolorat( $gd, $i, $j );
			$a2 = ($color >> 24) & 0xff;
			$r2 = ($color >> 16) & 0xff;
			$g2 = ($color >> 8) & 0xff;
			$b2 = $color & 0xff;
			if( ($r1 == $r2) && ($g1 == $g2) && ($b1 == $b2) ){
				if( --$b1 < 0 ){
					$b1 = 255;
					if( --$g1 < 0 ){
						$g1 = 255;
						if( --$r1 < 0 ){
							$this->debug->msg("Couldn't find a color " );
							$this->debug->out();
							}
						}
					}
				}
			}
		}

	$color = ($a1 & 0xff) << 24;
	$color |= ($r1 & 0xff) << 16;
	$color |= ($g1 & 0xff) << 8;
	$color |= ($b1 & 0xff);

	$this->debug->out();

	return $color;
}
################################################################################
#	get_file_list(). Get the files to work with.
################################################################################
function get_file_list( $exts=null )
{
	$this->debug->in();
#
#	Get ONLY the graphic files found.
#
	if( is_null($file_list) ){ $exts = $this->all_exts; }

	$cwd = getcwd();
	$cwd = str_replace( "\\", "/", $cwd );
#
#	Get where to start
#
	$this->debug->msg( "Directory to work with? " );
	$dir = rtrim( stream_get_line(STDIN, 1024, PHP_EOL) );
	if( strlen($dir) < 1 ){ $dir = $cwd; }
	$dir = str_replace( "\\", "/", $dir );
	$this->debug->msg( "DIR = $dir\n" );
	chdir( $dir );

	list( $good, $bad ) = $this->get_files( $dir, "/$exts$/i" );

	$this->debug->out();

	return array( $good, $bad );
}
################################################################################
#	check_files(). A routine to check the status of files to make sure the names
#		are the same.
################################################################################
function check_files( $good )
{
	$this->debug->in();

	foreach( $good as $k=>$v ){
#
#	First, we need to get the first 1024 bytes from the file.
#	Nothing else is needed at this point.
#
		$this->debug->msg( "Working on : $v\n" );
		$fh = fopen( $v, "r" );
		$r = fread( $fh, 1024 );
		fclose( $fh );
#
#	Probably one time deal
#
		if( preg_match("/pngp$/i", $v) ){
			$f = preg_replace( "/pngp$/i", "png", $v );
			$this->debug->msg( "Renaming :	$v\nTo		: $f\n" );
			rename( $v, $f );
			$v = $f;
			}
#
#	Does the internal name match the file ending?
#
#	Check all of the names to make sure the right information is inside of them.
#
		$png = preg_match( "/png/i", substr($r, 1, 3) );
		if( $png && !preg_match("/png$/i", $v) ){
			$png = preg_replace( "/\.\w{3,4}$/i", ".png", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $png\n" );
			rename( $v, $png );
			$v = $png;
			}

		$gif = preg_match( "/gif/i", substr($r, 1, 3) );
		if( $gif && !preg_match("/gif$/i", $v) ){
			$gif = preg_replace( "/\.\w{3,4}$/i", ".gif", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $gif\n" );
			rename( $v, $gif );
			$v = $gif;
			}

		$bmp = preg_match( "/(bm|ba|ci|cp|ic|pt)/i", substr($r, 1, 2) );
		if( $bmp && !preg_match("/bmp$/i", $v) ){
			$bmp = preg_replace( "/\.\w{3,4}$/i", ".bmp", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $bmp\n" );
			rename( $v, $bmp );
			$v = $bmp;
			}

		$hs = 0x00;
		$jpg = preg_match( "/(exif|jfif|jfi|jpg|jpeg)$hs/i", substr($r, 6, 5) );
		if( $jpg && !preg_match("/(exif|jfif|jfi|jpg|jpeg)$/i", $v) ){
			$jpg = preg_replace( "/\.\w{3,4}$/i", ".jpg", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $jpg\n" );
			rename( $v, $jpg );
			$v = $jpg;
			}

		$tiff1 = "49492a00";
		$tiff2 = "4040002a";
		$hex = str_pad(bin2hex(substr($r,0,4)), 8, '0', STR_PAD_LEFT );
#		$hex = sprintf( "%08x", substr($r, 0, 4) );
#		$hex = bin2hex( substr($r,0,4) ); $hex = ((strlen($hex) % 2) > 0) ? "0" : "" . $hex;
		$tif = preg_match("/($tiff1|$tiff2)/i", $hex );
		if( $tif && !preg_match("/(tif|tiff)$/i", $v) ){
			$tif = preg_replace( "/\.\w{3,4}$/i", ".tif", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $tif\n" );
			rename( $v, $tif );
			$v = $tif;
			}

		$webp = ( preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) );
		if( $webp && !preg_match("/webp$/i", $v) ){
			$webp = preg_replace( "/\.\w{3,4}$/i", ".webp", $v );
			$this->debug->msg( "Saving	: $v\nTo	: $webp\n" );
			rename( $v, $webp );
			$v = $webp;
			}
#
#	Because we do not yet work with WEBP - we need to move it back to PNG.
#	Corrected - we NOW do work with WEBP - so no need to move it.
#
if( false ){
		if( preg_match("/(web|webp)$/i", $v) &&
			(preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) ) ){
			$this->debug->msg( "Moving $v : WEBP to PNG\n" );
			$gd = imagecreatefromwebp( $v );
			$png = preg_replace( "/(webp|web)$/i", "png", $v );
			$this->debug->msg( "Saving to $png\n" );
			imagepng( $gd, $png );
			unlink( $v );
			}
		}
}

	$this->debug->out();
}
################################################################################
#	get_ftype(). Get which type of file I am looking at.
################################################################################
function get_ftype( $image )
{
	$this->debug->in();
#
#	First, we need to get the first 1024 bytes from the file.
#	Nothing else is needed at this point.
#
	$r = substr( $image, 0, 1024 );
#
#	png file format header
#
	$id = preg_match( "/png/i", substr($r, 0, 3) );
	if( $id ){ return "png"; }
#
#	gif file format header
#
	$id = preg_match( "/gif/i", substr($r, 0, 3) );
	if( $id ){ return "gif"; }
#
#	BMP file format header
#
	$id = preg_match( "/(bm|ba|ci|cp|ic|pt)/i", substr($r, 0, 2) );
	if( $id ){ return "bmp"; }
#
#	jpg file format header
#
	$hs = ox00;
	$id = preg_match( "/(exif|jfif|jfi|jpg|jpeg)$hs/i", substr($r, 6, 5) );
	if( $id ){ return "jpg"; }
#
#	tiff file format header
#
	$tiff1 = "49492a00";
	$tiff2 = "4040002a";
	$hex = str_pad( bin2hex(substr($r,0,4)), 8, '0', STR_PAD_LEFT );
	$id = preg_match( "/($tiff1|$tiff2)/i", $hex );
	if( $id ){ return "tif"; }
#
#	Webp file format header
#
	$id = ( preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) );
	if( $id ){ return "webp"; }
#
#	psd file format header
#
	$id = preg_match( "/8bps/i", substr($r, 0, 4) );
	if( $id ){ return "psd"; }
#
#	Computer Eyes file format header
#
	$id = preg_match( "/eyes/i", substr($r, 0, 4) );
	if( $id ){ return "flm"; }

	$id = preg_match( "/(fedbh|fedch)/i", substr($r, 0, 5) );
	if( $id ){ return "seq"; }
#
#	Imagic Film Picture file format header
#
	$id = preg_match( "/imdc/i", substr($r, 0, 4) );
	if( $id ){ return "flm"; }
#
#	STAD file format header
#
	$id = preg_match( "/(pm86|pm85)/i", substr($r, 0, 4) );
	if( $id ){ return "stad"; }
#
#	AuotCAD DXF file format header
#
	$hs = chr( 0x0d ) . chr( 0x0a ) . chr( 0x1a ) . chr( 0x00 );
	$len = strlen( "/autocad binary dxf/i" ) + 4;
	$id = preg_match( "/autocad binary dxf$hs/i", substr($r, 0, $len) );
	if( $id ){ return "dxf"; }
#
#	AuotCAD DXB file format header
#
	$hs = chr( 0x0d ) . chr( 0x0a ) . chr( 0x1a ) . chr( 0x00 );
	$len = strlen( "/AutoCAD DXB 1.0/i" ) + 4;
	$id = preg_match( "/AutoCAD DXB 1.0$hs/i", substr($r, 0, $len) );
	if( $id ){ return "dxf"; }
#
#	BDF file format header
#
	$id = preg_match( "/startfont/i", substr($r, 0, 9) );
	if( $id ){ return "stad"; }
#
#	DPX file format header
#
	$id = preg_match( "/(sdpx|xpds)/i", substr($r, 0, 4) );
	if( $id ){ return "dpx"; }
#
#	Dr. Halo PAL file format header
#
	$id = preg_match( "/ah/i", substr($r, 0, 2) );
	if( $id ){ return "pal"; }
#
#	DVM file format header
#
	$id = preg_match( "/dvm/i", substr($r, 0, 3) );
	if( $id ){ return "dvm"; }
#
#	EPS v2.0 file format header
#
	$str = "%!PS-Adobe-2.0 EPSF-1.2";
	$len = strlen( $str );
	$id = preg_match( "/$str/i", substr($r, 0, $len) );
	if( $id ){ return "eps"; }
#
#	EPS v3.0 file format header
#
	$str = "%!PS-Adobe-3.0 EPSF-3.0";
	$len = strlen( $str );
	$id = preg_match( "/$str/i", substr($r, 0, $len) );
	if( $id ){ return "eps"; }
#
#	FLI file format header
#
	$hs = 0xaf11;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "fli"; }
#
#	FLC file format header
#
	$hs = 0xaf12;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "flc"; }
#
#	GEM VDI file format header
#
	$hs = 0xffff;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "vdi"; }
#
#	DVI file format header
#
	$hs = 0x56445649;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "dvi"; }
#
#	AVL file format header
#
	$hs = 0x41565353;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "avl"; }
#
#	AUDI file format header
#
	$hs = 0x41554449;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "audi"; }
#
#	CMIG file format header
#
	$id = preg_match( "/cmig/i", substr($r, 0, 4) );
	if( $id ){ return "cmig"; }
#
#	YCC file format header
#
	$hs = 0x5965600;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "ycc"; }

	$this->debug->out();

	return false;
}
################################################################################
#	convert_files(). A function to convert a file from one format to another.
################################################################################
function convert_files( $g=null, $file_ext=null )
{
	$this->debug->in();
	if( is_null($g) ){
		$this->debug->msg( "DIE : No file array given" );
		return false;
		}

	if( is_null($file_ext) ){
		$this->debug->msg( "DIE: No file extension given" );
		return false;
		}

	$ext = $this->exts[$file_ext];
	foreach( $g as $k=>$v ){
		if( !preg_match("/$file_ext$/i", $v) ){
			if( !preg_match("/(tif|tiff)$/i", $v) ){
				$gd = $this->get_image( $v );
				$chg = preg_replace( "/\w{3,4}$/i", $file_ext, $v );
				$this->debug->msg( "Moving	: $v\nTo	: $chg\n" );
				$this->put_image( $gd, $chg );

				$this->debug->msg( "Deleting	: $v\n" . str_repeat( '-', 80 ) . "\n" );
				if( unlink($v) === false ){ $this->debug->msg( "Line " . __LINE__ . " : CAN NOT DELETE : $v\n" ); }
				}
				else {
					if( preg_match("/tiff$/i", $v) ){
						$chg = preg_replace( "/\w{3,4}$/i", "tif", $v );
						$this->debug->msg( "Renaming	: $v\nTo	: $chg\n" );
						rename( $v, $chg );
						}
						else { $this->debug->msg( "File OK	: $v\n" ); }
					}
			}
			else { $this->debug->msg( "File OK	: $v\n" ); }
		}

	$this->debug->out();
}
################################################################################
#	remove_nonwords(). Remove non-words and replace them with underscores.
################################################################################
function remove_nonwords( $g=null )
{
	$this->debug->in();
	if( is_null($g) ){ $this->debug->msg( "List is empty", true ); }

	$a = array();
	foreach( $g as $k=>$v ){
		$base = trim( basename($v) );
		$base = preg_replace( "/\W+/", "_", $base );
		$base = substr( $base, 0, -4 ) . "." . substr( $base, -3, 3 );
		$f = dirname( $v ) . "/$base";
		$this->debug->msg( "Renaming	: $v\nTo	: $f\n" );
		rename( $v, $f );
		$a[] = $f;
		}

	$this->debug->out();
	return $a;
}
################################################################################
#	grey_out(). Remove all greys.
################################################################################
function grey_out( $g=null )
{
	$this->debug->in();

	if( is_null($g) ){ $this->debug->msg( "Array is NULL - aborting" ); }

	foreach( $g as $k=>$v ){
#
#	Get the image
#
		$gd = $this->get_image( $v );

		$w = imagesx( $gd );
		$h = imagesy( $gd );
		for( $x=0; $x<$w; $x++ ){
			for( $y=0; $y<$h; $y++ ){
				$color = imagecolorat( $gd, $x, $y );

				$r = (($color >> 16) & 0xff);
				$g = (($color >> 8) & 0xff);
				$b = ($color & 0xff);

				if( $r > 200 ){
					if( (($r == $g) && ($r == $b)) ||
					((abs($r - $g) < 20) && (abs($r - $b) < 20)) ){
						imagesetpixel( $gd, $x, $y, 0x7fffffff );
						}
					}
				}
			}
#
#	Change the name of the file.
#
		$this->put_image( $gd , $v );
		}

	$this->debug->out();
}
################################################################################
#	fget_csv(). Get a csv file
################################################################################
function fget_csv( $file=null, $sep=',' )
{
	$this->debug->in();

	if( is_null($file) ){ $this->debug->msg( "FILE is NULL", true ); }

	$array = array();
	if( ($fp = fopen( $file, "r" )) !== FALSE ){
		while( ($data = fgetcsv($fp, 1024, $sep)) !== FALSE ){ $array[] = $data; }
		}
		else { $this->debug->msg( "Could not read $file", true ); }

	fclose( $fp );

	$this->debug->out();

	return $array;
}
################################################################################
#	fput_csv(). Put a csv file
################################################################################
function fput_csv( $file=null, $array=null, $sep=',' )
{
	$this->debug->in();

	if( is_null($file) ){ $this->debug->msg( "FILE is NULL", true ); }
	if( is_null($array) ){ $this->debug->msg( "ARRAY is NULL", true ); }

	if( ($fp = fopen( $file, "w" )) !== FALSE ){
		foreach( $array as $k=>$v ){ fputcsv( $fp, $v, $sep ); }
		}
		else { $this->debug->msg( "Could not read $file", true ); }

	$this->debug->out();

	fclose( $fp );
}
################################################################################
#	get_hash(). Get the hash
#	Arguments	:	FILE and LEVEL.
#					FILE is the file to check
#					LEVEL is the type of hash you want. Default is SHA512.
#	Example:	:	$val = $cf->get_hash( "file=myfile.dat", "level=MD2" );
#	Notes	:	Types of hash information
#				MD2, MD4, MD5, SHA1, SHA256, SHA384, SHA512 (Default)
#
#				You only need the FIRST letter of the command.
#
#	Mark Manning			Simulacron I			Mon 03/01/2021 22:09:49.45 
#	---------------------------------------------------------------------------
#	Ok. The problem is that a path name becomes too long due to really freaky
#	filenames. This is why I copied the files to the RAM Drive first.
#
#	Mark Manning			Simulacron I			Sat 07/17/2021 18:16:42.15 
#	---------------------------------------------------------------------------
#	Switched to using the algorithms given in PHP and stored in $this->algos.
#
#	Mark Manning			Simulacron I			Wed 09/01/2021 22:10:16.41 
#	---------------------------------------------------------------------------
#	Remember that the SHA1_FILE() is the only one that will give you the unque
#	id for a file.
#
################################################################################
function get_hash( $file=null, $level=null, $ram=null )
{
	$this->debug->in();

	if( is_null($file) || !file_exists($file) || (strlen(trim($file)) < 1) ){
		$this->debug->msg( "Line " . __LINE__ . " : FILE is NULL\n" );
		$this->debug->msg( "Line " . __LINE__ . " : FILE = $file\n" );
		return false;
		}

	$pathinfo = pathinfo( $file );

	if( is_null($level) || (strlen(trim($level)) < 1) ){ $level = "sha512"; }
	if( is_null($ram) ){ $path = $this->temp_path; }
		else { $path = $ram; }

	if( is_null($level) ){ $level = "sha512"; }

	$flag = false;
	foreach( $this->algos as $k=>$v ){
		if( preg_match("/$level/i", $v) ){ $flag = true; break; }
		}

	if( !$flag ){
		$this->debug->msg( "Line " . __LINE__ . " : LEVEL = $level\n" );
		return false;
		}

	$f = file_get_contents( $file );

	$info = null;
	while( is_null($info = hash($level, $file))  ){
		$this->debug->msg( "Line " . __LINE__ . " : INFO is NULL ($info)\n" );
		sleep( 3 );
		}

	unset( $f );
	$this->debug->out();

	return $info;
}
################################################################################
#	find_dups(). Find all duplicat files in the directory given (and
#		subdirectories)
################################################################################
function find_dups( $dir=null, $opts=null )
{
}
################################################################################
#	rem_dups(). Remove duplicate files.
#	Notes:	If opt is TRUE then only get NUMERICALLY named files.
################################################################################
function rem_dups( $dir=null, $opts=null )
{
#
#	Get all of the files
#
	list( $g, $b ) = $this->get_files( $dir);
#
#	Now get the hashes of all of the files
#
	$a = [];
	foreach( $g as $k=>$v ){
		if( $opts === true ){
			$b = basename($v);
			if( is_numeric($b) ){ $a[$v] = sha1_file( $v ); }
				else { unset( $g[$k] ); }
			}
			else { $a[$v] = sha1_file( $v ); }
		}
#
#	Now reverse the array so we have all files for each of the hashes.
#
	$b = [];
	foreach( $a as $k=>$v ){
		if( !isset($b[$v]) ){ $b[$v] = "$k|"; }
			else { $b[$v] .= "k|"; }
		}
#
#	Now remove all duplicate files.
#
	foreach( $b as $k=>$v ){
#
#	Break up the information.
#
		$a = explode( "|", $v );
#
#	Keep the first file
#
		$c = array_shift( $a );
#
#	Get rid of all of the rest.
#
		foreach( $a as $k1=>$v1 ){
			if( (strlen(trim($v1)) > 1) && file_exists($v1) ){ unlink( $v1 ); }
			}
		}

	return true;
}
################################################################################
#	chmod_all(). 
################################################################################
function chmod_all( $dir )
{
#
#	Get all of the directories
#
	$cf = 0;
	$cd = 0;
	$dirs = $this->get_dirs( $dir );
	if( count($dirs) < 1 ){ return false; }

	foreach( $dirs as $k=>$v ){
		$this->debug->msg( "Line " . __LINE__ . " : CHMODing : $k\n" );
		chmod( $k, 0777 );
		$cd++;
#
#	Get all of the files
#
		list( $g, $b ) = $this->get_files( $k, null, false );
		foreach( $g as $k1=>$v1 ){
			$this->debug->msg( "Line " . __LINE__ . " : CHMODing : $v1\n" );
			chmod( $v1, 0777 );
			$cf++;
			}
		}

	return array( $cf, $cd );
}
################################################################################
#	del_empty_dirs(). Send it a high level directory and this will search throgh
#		and delete empty directories.
################################################################################
function del_empty_dirs( $dir )
{
#
#	Get all of the directories
#
	$cd = 0;
	$dirs = $this->get_dirs( $dir );
	if( count($dirs) < 1 ){ return false; }

	foreach( $dirs as $k=>$v ){
#
#	Get all of the files
#
		list( $g, $b ) = $this->get_files( $k, null, false );
		if( count($g) < 1 ){
			$this->debug->msg( "Line " . __LINE__ . " : Removing : $k\n" );
#			rmdir( $k );
			$cd++;
			}
		}

	return $cd;
}
################################################################################
#	move_files_up(). Scan through a directory and see if it can be moved up
#		one level. ONLY move something up if there is ONLY ONE file (usually
#		a subdirectory which has the same name as the directory itself OR
#		if a SINGLE FILE has the same name as the directory - then move it up
#		one level. The DIRECTORY's name MUST contain an archive names end
#		UNLESS the item is a directory. Directories can always be moved up.
################################################################################
function move_files_up( $dir )
{
	$this->debug->in();

	$dq = '"';
	$flag = 1;
#
#	Start of the loop. We go through and change everything so we
#	might wind up with an invalid directory later on in the list.
#	So this loop starts up, gets the list of directories and then
#	begins checking them to see if they need to be moved.
#
#	This DOES MEAN that this will run through the entire list of
#	directories AT LEAST ONE EXTRA TIME - IF - WE HAVE CHANGED
#	ANYTHING. Otherwise - it just goes through once and stops.
#
	while( $flag > 0 ){
		$flag = 0;
#
#	Get the list of directories
#
		$dirs = $this->get_dirs( $dir );
#
#	Now go through them and look for single files in a directory.
#
		if( !is_array($dirs) ){ return false; }
		if( count($dirs) < 1 ){ return false; }
		foreach( $dirs as $k=>$v ){
			echo "Looking at : $k\n";
#
#	Because we are REMOVING directories - we always have to check to see
#	if we have already done something to the directory and if so - we
#	have to wait until the next run through.
#
			if( file_exists($k) ){
				list( $g, $b ) = $this->get_files( $k, null, false );
				if( count($g) == 1 ){
#
#	Get information about the file.
#
					$pathinfo = pathinfo( $g[0] );
					$uniqid = uniqid( rand(), true );
					$path = $pathinfo['dirname'];
					$file = $pathinfo['basename'];
					$a = explode( '/', $path );
					$last_dir = array_pop( $a );
					$old_path = implode( '/', $a );
#
#	If the filename and last directory are the same - then
#	since there is only one file in the directory - we move it up
#	one level.
#
					if( $file === $last_dir ){
						$flag++;
						$new_dir = $last_dir . "-" . $uniqid;
						$new_path = "$old_path/$new_dir";
						echo "RENAME( $k, $dq$new_path$dq )\n";
						rename( $k, "$new_path" );
						sleep( 1 );
						echo "RENAME( $dq$new_path/$file$dq, $k );\n";
						rename( "$new_path/$file", $k );
						echo "---->DELETING $new_path\n";
						if( rmdir("$new_path") === false ){ $flag--; }
						echo str_repeat( '=', 80 ) . "\n";
						}
					}
					else if( count($g) < 1 ){
						$flag++;
						echo "---->DELETING $k\n";
						if( rmdir($k) === false ){ $flag--; }
						echo str_repeat( '=', 80 ) . "\n";
						}
				}
			}
		}
}
################################################################################
#	remdir(). Remove all files and directories.
#	NOTE	:	You CAN NOT delete a directory without first getting rid of
#				all of the files IN that directory.
#	Variables	:	$cur_dir is the directory we want to work with.
#					$opt is whether or not to delete the $cur_dir directory.
################################################################################
function remdir( $cur_dir=null, $opt=true )
{
	$this->debug->in();

	if( is_null($cur_dir) || !is_dir($cur_dir) ){ return false; }

	$c = 0;
	$dirs = [];
	$files = [];
	$dir_list = [];

	$pathinfo = pathinfo( $cur_dir );

	$dirs[] = $cur_dir;
	while( count($dirs) > 0 ){
		$dir = array_pop( $dirs );
		echo "Looking at : $dir\n";

		$dir_list[] = $dir;
		if( is_link($dir) ){
			$c++;
			echo "Deleting LINK DIRECTORY : $dir\n";
			if( file_exists($dir) ){ unlink( $dir ); }
			continue;
			}

		if( ($dh = @opendir($dir)) ){
			if( !is_resource($dh) ){ continue; }
			while( ($file = readdir($dh)) !== false ){
				$curfile = "$dir/$file";
				echo "Looking at : $curfile\n";
				if( $file != "." && $file != ".." ){
					if( is_dir($curfile) ){
						echo "Adding directory : $curfile\n";
						$dirs[] = $curfile;
						$dir_list[] = $curfile;
						}
						else {
							echo "Deleting : $curfile\n";
							if( file_exists($curfile) ){
								chmod( $curfile, 0777 );
								unlink( $curfile );
								}
							}
					}
				}

			closedir( $dh );
			}
		}
#
#	Now that we have gotten rid of all of the files
#	we need to get rid of the directories. But first - we
#	have to sort the strings in order to get the longest
#	strings FIRST.
#
	usort( $dir_list, "class_files::len_sort" );
	foreach( $dir_list as $k=>$v ){
		if( $opt && ($cur_dir === $v) ){
			echo "Skipping : $v\n";
			continue;
			}

		echo "Deleting REAL DIRECTORY : $v\n";
		if( file_exists($v) ){
			chmod( $v, 0777 );
			rmdir( $v );
			}
		}

	echo "All files are removed\n";
	$this->debug->out();
	return true;
}
################################################################################
#	len_sort(). Do a sort according to length of the string.
#	NOTE	:	This is useful because you can't get rid of the directories
#				if there is any kind of a file IN that directory. (Besides
#				the . and .. files.)
################################################################################
function len_sort( $a, $b )
{
	$a_len = strlen( $a );
	$b_len = strlen( $b );

	if( $a_len == $b_len ){ return 0; }
#
#	Reverse if b is shorter than a
#
	return ($b_len < $a_len) ? -1 : 1;
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
#	dump(). A short function to dump a file.
################################################################################
function dumpfile( $f=null, $l=null )
{
	$this->debug->in();

	if( is_null($f) ){
		$this->debug->msg( "DIE : No file given" );
		return false;
		}

	if( is_null($l) ){ $l = 32; }

	$fh = fopen($f, "r" );
	$r = fread( $fh, 1024 );
	fclose( $fh );

	$this->debug->msg( "Dump File	: " );
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
	if( !isset($GLOBALS['classes']['files']) ){
		$GLOBALS['classes']['files'] = new class_files();
		}

?>
