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
	private $php_uname = null;
	private $all_exts = null;
	private $exts = null;
	private $algos = null;

	public $temp_path = null;

	private $bytes = null;
	private $kb = null;
	private $mb = null;
	private $gb = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
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
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->php_uname = php_uname();

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

	$this->bytes = 1;
	$this->kb = 1024;
	$this->mb = 1048576;
	$this->gb = 1073741824;

	$this->all_exts = substr( $this->all_exts, 0, -1 );
}

################################################################################
#	get_files(). A function to get a list of files from a directory
#		AND that directory's sub-directories.
################################################################################
function get_files( $top_dir=null, $regexp=null, $opt=null, $print=false )
{
#	$this->dump( "Top_dir", $top_dir );

	if( is_null($top_dir) ){ $top_dir = "./"; }
	if( is_null($regexp) ){ $regexp = "/.*/"; }
	if( is_null($opt) ){ $opt = true; }
	if( !preg_match(";/;", $regexp) ){ $regexp = "/" . $regexp . "/"; }

	echo "Here : " . __LINE__ . "\n";
	$dirs[] = $top_dir;
	$bad = array();
	$files = array();
	while( count($dirs) > 0 ){
		$dir = array_pop( $dirs );
#		print_r( $dir ); echo "\n";
#		$this->dump( "DIR", $dir );
		if( strlen(trim($dir)) < 1 ){
			$this->dump( "Aborting - blank DIR ", $dir);
			continue;
			}
#
#	See if we have permissions to read this.
#
		$a = $this->get_perms( $dir );
		if( $a === false ){
#			echo "A is FALSE\n";
			continue;
			}

		$perms = explode( ",", $a );

#		$this->dump( "DIR", $dir );
#		$this->dump( "Perms #1", $a );
#		$this->dump( "Perms #2", $perms );

		if( $perms === false ){ continue; }
		if( !is_array($perms) ){ die( "PERMS is not an array!\n" ); }
		if( count($perms) < 1 ){ echo "$perms\n"; die( "PERMS is blank!\n" ); }
		if( !$perms[0] === 'd' ){ continue; }
		if( (count($perms) > 0) && (($perms[1] === '-') || ($perms[2] === '-')) ){ continue; }
		if( (count($perms) > 0) && (($perms[4] === '-') || ($perms[5] === '-')) ){ continue; }
		if( (count($perms) > 0) && (($perms[7] === '-') || ($perms[8] === '-')) ){ continue; }

		$perms = $this->get_perms( $dir );
		if( $perms === false ){ continue; }
		$perms = explode( ',', $perms );
		if( $perms[1] == '-' && $perms[2] == '-' ){
#			print_r( $perms );
			continue;
			}

#		print_r( $perms ); echo "\n"; exit;
#
#	Break up the directory string
#
		$ms = array();
		$m = explode( "/", $dir );
		foreach( $m as $k=>$v ){
			if( isset($ms[$v]) ){ $ms[$v]++; }
				else { $ms[$v] = 0; }
			}
#
#	Now go through. There should only be one file with a given name in the list.
#	If there are multiple names - it is probably a link.
#
		$ms_flag = false;
		foreach( $ms as $k=>$v ){
			if( $v > 2 ){ $ms_flag = true; }
			}

		if( $this->get_stats($dir, 'l') || !is_readable($dir) || is_link($dir) || $ms_flag ){
			echo "Directory : $dir\nIs a LINK - Can not be opened...skipping\n";
			continue;
			}

		if( ($dh = @opendir($dir)) !== false ){
			if( !is_resource($dh) ){ continue; }
			while( ($file = readdir($dh)) !== false ){
				$curfile = "$dir/$file";
				if( $print){ echo "Looking at : $curfile\n"; }

				$a = explode( '/', $curfile );
				$count = 0;
				while( count($a) > 0 ){
					$b = array_shift( $a );
					if( preg_match("/application\s+data/i", $b) ){ $count++; }
					}

				if( $count > 1 ){
					if( $print ){ echo "Double Application Data link...skipping\n"; }
					continue;
					}

				if( $file != "." && $file != ".." ){
					if( is_dir("$dir/$file") && $opt == true){ $dirs[] = "$dir/$file"; }
						else if( is_link($file) ){ continue; }
						else if( preg_match($regexp, $file) ){ $files[] = "$dir/$file"; }
						else { $bad[] = "$dir/$file"; }
					}
					else { echo "Discarding '.' or '..'\n"; }
				}

			closedir( $dh );
			}
			else {
				echo "Directory : $dir\nCan not be opened...skipping\n";
				continue;
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

	return array( $files, $bad );
}
################################################################################
#	get_dirs(). A function to get a list of directories from a directory
#		AND that directory's sub-directories.
#	Notes:	Modified. It now returns an array[directory]=Number of files
################################################################################
function get_dirs( $top_dir=null, $regexp=null, $opt=null )
{
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
#				echo "Looking at : $file\n";
				$cur_dir = "$dir/$file";
				echo "FOUND : $file\n";
				if( $file != "." && $file != ".." ){
					if( preg_match($regexp, $cur_dir) ){
						if( is_dir($cur_dir) && $opt == true){
#							echo "Adding a Directory : $cur_dir\n";
							$files[$dir]++;
							$dirs[] = $cur_dir;
							}
							else {
#								echo "Adding a File : $dir\n";
								$files[$dir]++;
								}
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
		echo "Line " . __LINE__ . " : No such file : $file\n";
		return false;
		}

#	$this->dump( "FILE", $file );

	if( ($perms = fileperms($file)) === false ){
		echo "Line " . __LINE__ . " : Fileperms returned an ERROR : $perms\n";
		return false;
		}

#	$this->dump( "PERMS", $perms );
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

#	$this->dump( "Info", $info );
#
#	Group
#
	$info .= (($perms & 0x0020) ? 'r,' : '-,');
	$info .= (($perms & 0x0010) ? 'w,' : '-,');
	$info .= (($perms & 0x0008) ?
		(($perms & 0x0400) ? 's,' : 'x,' ) :
		(($perms & 0x0400) ? 'S,' : '-,'));

#	$this->dump( "Info", $info );
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

	return $info;
}
################################################################################
#	get_stats(). Gets the stat of whatever file you send over.
#		Tests for one or more of the different filetypes given below.
#
#	NOTES : You ONLY need to send the first character since the options
#		are all different.
#
#		If you want to send multiple types, use a dash or a comma or a NON-digit
#		character as a separator. Ex: s-l-r
################################################################################
function get_stats( $file=null, $opt=null, $print=false )
{
	$opts = preg_split( "/\W/", $opt );

	$filestat = stat( $file );
	if( $print ){
		foreach( $filestat as $k=>$v ){
			echo "Filestat[$k] = $v\n";
			}
		}

	$filetypes = array(
		's' => octdec('014'),
		'l' => octdec('012'),
		'r' => octdec('010'),
		'b' => octdec('006'),
		'd' => octdec('004'),
		'c' => octdec('002'),
		'f' => octdec('001'),
		);

	$filestat['mode'] = substr(decoct($filestat['mode']),0,-4);

	foreach( $opts as $k=>$v ){
		if( ($v === "l") && ($filestat['nlink'] > 0) ){ $nlink_flag = true; }
			else { $nlink_flag = false; }

		if( $nlink_flag && ($filestat['mode'] === $filetypes[$v]) ){ return true; }
		}

	return false;
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
	$c = 0;

	try {
		if( preg_match("/gif$/i", $file) ){
			echo "Loading GIF file : $file\n";
			$gd = imagecreatefromgif( $file );
			}
			else if( preg_match("/gd$/i", $file) ){
				echo "Loading GD file : $file\n";
				$gd = imagecreatefromgd( $file );
				}
			else if( preg_match("/gd2$/i", $file) ){
				echo "Loading GD2 file : $file\n";
				$gd = imagecreatefromgd2( $file );
				}
			else if( preg_match("/(jpg|jpeg|exif|jfif|jfi)$/i", $file) ){
				echo "Loading JPG file : $file\n";
				$gd = imagecreatefromjpeg( $file );
				}
			else if( preg_match("/wbmp$/i", $file) ){
				echo "Loading WBMP file : $file\n";
				$gd = imagecreatefromwbmp( $file );
				}
			else if( preg_match("/bmp$/i", $file) ){
				echo "Loading BMP file : $file\n";
				$gd = imagecreatefrombmp( $file );
				}
			else if( preg_match("/xbm$/i", $file) ){
				echo "Loading XBM file : $file\n";
				$gd = imagecreatefromxbm( $file );
				}
			else if( preg_match("/xpm$/i", $file) ){
				echo "Loading XPM file : $file\n";
				$gd = imagecreatefromxpm( $file );
				}
			else if( preg_match("/png$/i", $file) ){
				echo "Loading PNG file : $file\n";
				$gd = @imagecreatefrompng( $file );
				}
			else if( preg_match("/(web|webp)$/i", $file) ){
				echo "Loading WEBP file : $file\n";
				$gd = imagecreatefromwebp( $file );
				}
			else if( preg_match("/(tif|tiff)$/i", $file) ){
				echo "DIE : GD does not do TIF : $file\n";
				return false;
				}
			else {
				echo "DIE : Unknown file format : $file\n";
				return false;
				}
		}
		catch( exception $e ){
			echo $e->getMessage() . "\n";
			}

	if( !is_resource($gd) ){
		echo "DIE : GD is NOT a resource\n";
		return false;
		}

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

		imagedestroy( $gd );
		}

	return $gd2;
}
################################################################################
#	rem_iccp(). Removes the ICCP information on bad files
################################################################################
function rem_iccp( $dir )
{
	if( !file_exists($dir) ){ die("***** ERROR : DIR does not exist"); }

	$dq = '"';
	if( is_file($dir) ){
		$dir = dirname( $dir );
#		$pathinfo = $this->pathinfo( $dir );
#		$dir = $pathinfo['dirname'];
		}

	system( "magick.exe mogrify $dq$dir/*.png$dq" );
	return true;
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
	$ret = null;
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
		else { echo "Unknown file format....aborting\n"; }

	if( $flag ){ rename( $file, $old_file ); }
	if( $del ){ imagedestroy( $gd ); }

	return $ret;
}
################################################################################
#	dup_image(). A function to duplicate an image.
################################################################################
function dup_image( $gd=null, $opt=null )
{
	if( is_null($gd) ){
		echo "No image sent over...aborting.\n";
		return false;
		}

	if( is_null($opt) ){ $opt = false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$gd2 = imagecreatetruecolor( $w, $h );
#
#	Get a unique color for the transparent color
#
	$trans = $this->unique_color( $gd );
	$ta = ($trans >> 24) & 0xff;
	$tr = ($trans >> 16) & 0xff;
	$tg = ($trans >> 8) & 0xff;
	$tb = $trans & 0xff;

	if( function_exists('imagecolorallocatealpha') ){
		imagealphablending($gd2, false);
		imagesavealpha($gd2, true);
		$trans = imagecolorallocatealpha( $gd2, $tr, $tg, $tb, $ta );
		imagefilledrectangle($gd2, 0, 0, $w, $h, $trans);

		imagecopyresampled( $gd2, $gd, 0, 0, 0, 0, $w, $h, $w, $h );

		if( $opt ){ imagedestroy( $gd ); }
		}

	return $gd2;
}
################################################################################
#	get_colors(). Make a list of all colors in an image.
################################################################################
function get_colors( $gd=null, $opt=null )
{
	if( is_null($gd) ){ die( "GD is NULL" ); }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$colors = [];
	for( $x=0; $x<$w; $x++ ){
		for( $y=0; $y<$h; $y++ ){
			$point = imagecolorat( $gd, $x, $y );
			if( isset($colors[$point]) ){ $colors[$point]++; }
				else { $colors[$point] = 1; }
			}
		}
#
#	Now sort the array
#
	$a = [];
	foreach( $colors as $k=>$v ){
		$a[] = sprintf( "%015d-%015d", $v, $k );
		}

#print_r( $a );
	rsort( $a );
#print_r( $a );

	$colors = [];
	foreach( $a as $k=>$v ){
		$b = explode( '-', $v );
		if( $opt ){ $colors[$b[1]] = $b[0]; }
			else { $colors[$b[1]+0] = $b[0] + 0; }
		}

	return $colors;
}
################################################################################
#	blank_image(). Send over an image and you get a blank image of the same size
#		back. If you provide an RGB color - it is set to that color.
#		Remember that zero(0) is the same as black in RGBA.
################################################################################
function blank_image( $gd=null, $rgb=null, $opt=null )
{
	if( is_null($gd) ){
		echo "No image sent over...aborting.\n";
		return false;
		}

	if( is_null($rgb) ){ $rgb = 0x7fffffff; }
	if( is_null($opt) ){ $opt = false; }

	$w = imagesx( $gd );
	$h = imagesy( $gd );

	$gd2 = imagecreatetruecolor( $w, $h );

	imagealphablending( $gd2, false );
	imagesavealpha( $gd2, true );

	if( !is_null($rgb) ){
		$a = ($rgb >> 24) & 0xff;
#print_r( $a ); echo "\n\n";
		$r = ($rgb >> 16) & 0xff;
#print_r( $r ); echo "\n\n";
		$g = ($rgb >> 8) & 0xff;
#print_r( $g ); echo "\n\n";
		$b = $rgb & 0xff;
#print_r( $b ); echo "\n\n";

		$c = imagecolorallocatealpha( $gd2, $r, $g, $b, $a );
		imagefilledrectangle($gd2, 0, 0, $w, $h, $c);
		}

	if( $opt ){ imagedestroy( $gd ); }

	return $gd2;
}
################################################################################
#	saale_image(). Scale an image for me.
#	Notes: I thought it would be nice to be able to do this three different ways.
################################################################################
function scale_image( $gd=null, $sx=null, $sy=null )
{
	echo "SX = $sx, SY = $sy\n";
#
#	This can come over as:
#
#	ARRAY	:	SX, SY (ex: array(4,5) )
#	STRING	:	"SXxSY" (ex: "$x10")
#	Two Separate Values (ie: $sx=5, $sy=99)
#
	if( is_array($sx) ){ $s1 = $sx[0]; $sy = $sx[1]; unset($sx); $sx = $s1; }
		else if( preg_match("/x/i", $sx) ){ $s = explode( 'x', $sx ); unset($sx); $sx = $s[0]; $sy = $s[1]; }

	if( is_null($gd) ){ echo "GD is NULL\n"; }
	if( is_null($sx) ){ echo "SX is NULL\n"; }
	if( is_null($sy) ){ echo "SY is NULL\n"; }

#	$gd2 = imagescale( $gd, $sx, $sy,  IMG_BICUBIC );
	$gd2 = imagescale( $gd, $sx, $sy );
	imagealphablending($gd2, false);
	imagesavealpha($gd2, true);

	imagedestroy( $gd );

	return $gd2;
}
################################################################################
#	"Erwin Bon" <er...@verkerk.nl> wrote in message
#	news:3fc9d260$0$4671$1b62eedf@news.euronet.nl...
################################################################################
function ConvertBMP2GD($src, $dest = false)
{
	if (!($src_f = fopen($src, "rb"))) { return false; }
	if (!($dest_f = fopen($dest, "wb"))) { return false; }
	$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f, 14));
	$s = "Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant";
	$info = unpack($s, fread($src_f, 40));

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

	return $gd;
}
################################################################################
#	old_imagebmp(). Creates a BMP file.
#	 From : shd at earthling dot net
################################################################################
function old_imagebmp ($im, $fn = false)
{
	if( !is_resource($im) ) return false;

	if ($fn === false) $fn = 'php://output';
	$f = fopen ($fn, "w");
	if (!$f) return false;
#
#	Image dimensions
#
	$biWidth = imagesx( $im );
	$biHeight = imagesy( $im );
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

	return true;
}
################################################################################
#	old_imagecreatefrombmp(). Get a BMP image.
################################################################################
function old_imagecreatefrombmp($file)
{
	$tmp_name = tempnam("./temp_files", "GD");
	if (ConvertBMP2GD($file, $tmp_name)){
		$img = imagecreatefromgd($tmp_name);
		unlink($tmp_name);
		return $img;
		}

	return false;
}
################################################################################
#	find_box(). Finds the size of the box.
################################################################################
function find_box( $gd, $color, $offset=0 )
{
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
	if (!is_readable($dir)) return false;
	$scan = scandir( $dir );
	foreach( $scan as $k=>$v ){
		if( preg_match("/^(\.|\.\.)$/", $v) ){ unset( $scan[$k] ); }
		if( !is_null($regexp) && preg_match($regexp, $v) ){ unset( $scan[$k] ); }
		}

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
	$fc = 0;

    $w = imagesx( $gd );
    $h = imagesy( $gd );

    $w2 = $w + 100;
    $h2 = $h + 100;

    $color = imagecolorat( $gd, 0, 0 );
	echo "COLOR #1 = " . dechex($color) . "\n";

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
			echo "NO imagecolorallocatealpha function\n";
			}

    $color = imagecolorat( $gd2, 0, 0 );
	echo "COLOR #1 = " . dechex($color) . "\n";

	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;

	echo "A = $a, R = $r, G = $g, B = $b\n";
	echo "COLOR #2 = " . dechex($color) . "\n";

    $w = imagesx( $gd2 );
    $h = imagesy( $gd2 );

	$top = $bot = $left = $right = 0;
#
#	Left
#
	$flag = false;
	echo "Working on the LEFT part\n";
    for( $x=0; $x<$w; $x++ ){
        for( $y=0; $y<$h; $y++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			echo "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n";
            if( ($color != $rgb) ){ $left = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	echo "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n";
#
#	Top
#
	$flag = false;
	echo "Working on the TOP part\n";
	for( $y=0; $y<$h; $y++ ){
		for( $x=0; $x<$w; $x++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			echo "Top : X = $x, Y = $y, Color = $color, RGB = $rgb\n";
            if( ($color != $rgb) ){ $top = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Right
#
	$flag = false;
	echo "Working on the RIGHT part\n";
	for( $x=($w-1); $x>0; $x-- ){
		for( $y=($h-1); $y>0; $y-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			echo "Right : X = $x, Y = $y, Color = $color, RGB = $rgb\n";
            if( ($color != $rgb) ){ $right = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Bottom
#
	$flag = false;
	echo "Working on the BOTTOM part\n";
	for( $y=($h-1); $y>0; $y-- ){
		for( $x=($w-1); $x>0; $x-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			echo "Bottom : X = $x, Y = $y, Color = $color, RGB = $rgb\n";
            if( ($color != $rgb) ){ $bot = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	echo "Left = $left, Right = $right, Top = $top, Bottom = $bot\n";

    if( ($top == 99999) || ($bot == -99999) || ($left == 99999) || ($right == -99999) ){ return null; }

	$os = 3;
    $w2 = abs($right - $left) + ($os * 2) + 1;
    $h2 = abs($bot - $top) + ($os * 2) + 1;

    $color = imagecolorat( $gd2, 0, 0 );
	echo "COLOR #1 = " . dechex($color) . "\n";

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
			echo "NO imagecolorallocatealpha function\n";
			}

    return $gd;
}
################################################################################
#	rotateX(). Rotates around the X axis
#	Author:	 anon at here dot com
#	NOTES:	Originally from www.php.net.
################################################################################
function rotateX($x, $y, $theta)
{
	return $x * cos($theta) - $y * sin($theta);
}
################################################################################
#	rotateY(). Rotates around the Y axis
#	Author:	 anon at here dot com
#	NOTES:	Originally from www.php.net.
################################################################################
function rotateY($x, $y, $theta)
{
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
	if( is_null($srcImg) ){ echo "GD is NULL\n"; }
	if( is_null($angle) ){ echo "NO ANGLE GIVEN\n"; }
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
   
    return $destimg;
}
################################################################################
#	rt_image(). Rotate and Trim an image
################################################################################
function rt_image( $gd )
{
	if( is_null($gd) ){ echo "GD is NULL\n"; }

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
		echo "I = $i, W = $w - $w2, H = $h - $h2\n";
#		imagepng( $gd2, "./test-3.png" ); sleep( 1 );

		if( ($w2 * $h2) < ($aw * $ah) ){
			$angle = $i;
			$aw = $w2;
			$ah = $h2;
			}
		}

	if( $angle > 0 ){
		echo "Returning NEW : Angle = $angle, W = $aw, H = $ah\n";
		$gd2 = $this->rot_image( $gd, $angle );
		$gd2 = $this->trim_image( $gd2 );

		return $gd2;
		}

	echo "Returning OLD\n";
	return $gd;
}
################################################################################
#	unique_color(). Get a unique_color.
################################################################################
function unique_color( $gd )
{
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
							echo "Couldn't find a color\n";
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

	return $color;
}
################################################################################
#	get_file_list(). Get the files to work with.
################################################################################
function get_file_list( $exts=null )
{
#
#	Get ONLY the graphic files found.
#
	if( is_null($file_list) ){ $exts = $this->all_exts; }

	$cwd = getcwd();
	$cwd = str_replace( "\\", "/", $cwd );
#
#	Get where to start
#
	echo "Directory to work with?\n";
	$dir = rtrim( stream_get_line(STDIN, 1024, PHP_EOL) );
	if( strlen($dir) < 1 ){ $dir = $cwd; }
	$dir = str_replace( "\\", "/", $dir );
	echo "DIR = $dir\n";
	chdir( $dir );

	list( $good, $bad ) = $this->get_files( $dir, "/$exts$/i" );

	return array( $good, $bad );
}
################################################################################
#	check_files(). A routine to check the status of files to make sure the names
#		are the same.
################################################################################
function check_files( $good )
{
	foreach( $good as $k=>$v ){
#
#	First, we need to get the first 1024 bytes from the file.
#	Nothing else is needed at this point.
#
		echo "Working on : $v\n";
		$fh = fopen( $v, "r" );
		$r = fread( $fh, 1024 );
		fclose( $fh );
#
#	Probably one time deal
#
		if( preg_match("/pngp$/i", $v) ){
			$f = preg_replace( "/pngp$/i", "png", $v );
			echo "Renaming :	$v\nTo		: $f\n";
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
			echo "Saving	: $v\nTo	: $png\n";
			rename( $v, $png );
			$v = $png;
			}

		$gif = preg_match( "/gif/i", substr($r, 1, 3) );
		if( $gif && !preg_match("/gif$/i", $v) ){
			$gif = preg_replace( "/\.\w{3,4}$/i", ".gif", $v );
			echo "Saving	: $v\nTo	: $gif\n";
			rename( $v, $gif );
			$v = $gif;
			}

		$bmp = preg_match( "/(bm|ba|ci|cp|ic|pt)/i", substr($r, 1, 2) );
		if( $bmp && !preg_match("/bmp$/i", $v) ){
			$bmp = preg_replace( "/\.\w{3,4}$/i", ".bmp", $v );
			echo "Saving	: $v\nTo	: $bmp\n";
			rename( $v, $bmp );
			$v = $bmp;
			}

		$hs = 0x00;
		$jpg = preg_match( "/(exif|jfif|jfi|jpg|jpeg)$hs/i", substr($r, 6, 5) );
		if( $jpg && !preg_match("/(exif|jfif|jfi|jpg|jpeg)$/i", $v) ){
			$jpg = preg_replace( "/\.\w{3,4}$/i", ".jpg", $v );
			echo "Saving	: $v\nTo	: $jpg\n";
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
			echo "Saving	: $v\nTo	: $tif\n";
			rename( $v, $tif );
			$v = $tif;
			}

		$webp = ( preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) );
		if( $webp && !preg_match("/webp$/i", $v) ){
			$webp = preg_replace( "/\.\w{3,4}$/i", ".webp", $v );
			echo "Saving	: $v\nTo	: $webp\n";
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
			echo "Moving $v : WEBP to PNG\n";
			$gd = imagecreatefromwebp( $v );
			$png = preg_replace( "/(webp|web)$/i", "png", $v );
			echo "Saving to $png\n";
			imagepng( $gd, $png );
			unlink( $v );
			}
		}
}

}
################################################################################
#	get_ftype(). Get which type of file I am looking at.
################################################################################
function get_ftype( $image )
{
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

	return false;
}
################################################################################
#	convert_files(). A function to convert a file from one format to another.
################################################################################
function convert_files( $g=null, $file_ext=null )
{
	if( is_null($g) ){
		echo "DIE : No file array given\n";
		return false;
		}

	if( is_null($file_ext) ){
		echo "DIE: No file extension given\n";
		return false;
		}

	$ext = $this->exts[$file_ext];
	foreach( $g as $k=>$v ){
		if( !preg_match("/$file_ext$/i", $v) ){
			if( !preg_match("/(tif|tiff)$/i", $v) ){
				$gd = $this->get_image( $v );
				$chg = preg_replace( "/\w{3,4}$/i", $file_ext, $v );
				echo "Moving	: $v\nTo	: $chg\n";
				$this->put_image( $gd, $chg );

				echo "Deleting	: $v\n" . str_repeat( '-', 80 ) . "\n";
				if( unlink($v) === false ){ echo "Line " . __LINE__ . " : CAN NOT DELETE : $v\n"; }
				}
				else {
					if( preg_match("/tiff$/i", $v) ){
						$chg = preg_replace( "/\w{3,4}$/i", "tif", $v );
						echo "Renaming	: $v\nTo	: $chg\n";
						rename( $v, $chg );
						}
						else { echo "File OK	: $v\n"; }
					}
			}
			else { echo "File OK	: $v\n"; }
		}
}
################################################################################
#	remove_nonwords(). Remove non-words and replace them with underscores.
################################################################################
function remove_nonwords( $g=null )
{
	if( is_null($g) ){ echo "List is empty\n"; }

	$a = array();
	foreach( $g as $k=>$v ){
		$base = trim( basename($v) );
		$base = preg_replace( "/\W+/", "_", $base );
		$base = substr( $base, 0, -4 ) . "." . substr( $base, -3, 3 );
		$f = dirname( $v ) . "/$base";
		echo "Renaming	: $v\nTo	: $f\n";
		rename( $v, $f );
		$a[] = $f;
		}

	return $a;
}
################################################################################
#	grey_out(). Remove all greys.
################################################################################
function grey_out( $g=null )
{
	if( is_null($g) ){ echo "Array is NULL - aborting\n"; }

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

}
################################################################################
#	fget_csv(). Get a csv file
################################################################################
function fget_csv( $file=null, $sep=',' )
{
	$fileSize = filesize( $file );

	if( is_null($file) ){ echo "FILE is NULL\n"; }

	$array = array();
	if( ($fp = fopen( $file, "r" )) !== FALSE ){
		while( ($data = fgetcsv($fp, $fileSize, $sep)) !== FALSE ){ $array[] = $data; }
		}
		else { echo "Could not read $file\n"; }

	fclose( $fp );

	return $array;
}
################################################################################
#	fput_csv(). Put a csv file
################################################################################
function fput_csv( $file=null, $array=null, $sep=',' )
{
	if( is_null($file) ){ echo "FILE is NULL\n"; }
	if( is_null($array) ){ echo "ARRAY is NULL\n"; }

	if( ($fp = fopen( $file, "w" )) !== FALSE ){
		foreach( $array as $k=>$v ){ fputcsv( $fp, $v, $sep ); }
		}
		else { echo "Could not read $file\n"; }

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
	if( is_null($file) || !file_exists($file) || (strlen(trim($file)) < 1) ){
		echo "Line " . __LINE__ . " : FILE is NULL\n";
		echo "Line " . __LINE__ . " : FILE = $file\n";
		return false;
		}

	$pathinfo = $this->pathinfo( $file );

	if( is_null($level) || (strlen(trim($level)) < 1) ){ $level = "sha512"; }
	if( is_null($ram) ){ $path = $this->temp_path; }
		else { $path = $ram; }

	if( is_null($level) ){ $level = "sha512"; }

	$flag = false;
	foreach( $this->algos as $k=>$v ){
		if( preg_match("/$level/i", $v) ){ $flag = true; break; }
		}

	if( !$flag ){
		echo "Line " . __LINE__ . " : LEVEL = $level\n";
		return false;
		}

	$f = file_get_contents( $file );

	$info = null;
	while( is_null($info = hash($level, $file))  ){
		echo "Line " . __LINE__ . " : INFO is NULL ($info)\n";
		sleep( 3 );
		}

	unset( $f );

	return $info;
}
################################################################################
#	find_dups(). Find all duplicate files in the directory given (and
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
		echo "Line " . __LINE__ . " : CHMODing : $k\n";
		chmod( $k, 0777 );
		$cd++;
#
#	Get all of the files
#
		list( $g, $b ) = $this->get_files( $k, null, false );
		foreach( $g as $k1=>$v1 ){
			echo "Line " . __LINE__ . " : CHMODing : $v1\n";
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
			echo "Line " . __LINE__ . " : Removing : $k\n";
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
					$pathinfo = $this->pathinfo( $g[0] );
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
	if( is_null($cur_dir) || !is_dir($cur_dir) ){ return false; }

	$c = 0;
	$dirs = [];
	$files = [];
	$dir_list = [];

	$pathinfo = $this->pathinfo( $cur_dir );

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
#	pathinfo(). My version of pathinfo().
################################################################################
function pathinfo( $path=null, $fromString=null, $toString=null )
{
	if( is_null($path) ){ return false; }
#
#	If the user wants to convert the path string from one type to another
#	you do it here. Like from UTF-8 to ISO-8859-1.
#
	if( !is_null($fromString) && !is_null($toString) ){
		if( function_exists(mb_convert_encoding) ){
			$path = mb_convert_encoding( $path, $toString, $fromString );
			}
		}
#
#	Check for an fix Windows old c:\a\b\c.dat to the new way of c:/a/b/c.dat
#
	if( preg_match("/\\\\/", $path) ){
		$path = str_replace( "\\", "/", $path );
		}

	$pathinfo = pathinfo( $path );
#
#	If the given $path IS A DIRECTORY - then set $pathinfo to be just a
#	directory. This fixes the problem with pathinfo and WINDOWs where
#	the BASENAME, EXTENSION, and FILENAME would be incorrectly set if you
#	just sent a directory to pathinfo. Example:
#
#	c:/my/path/info
#
#	Would return 
#
#		$pathinfo['dirname'] = "c:/my/path";
#		$pathinfo['basename'] = "info";
#		$pathinfo['extension'] = "";
#		$pathinfo['filename'] = "info";
#
#	Now it returns:
#
#		$pathinfo['dirname'] = "c:/my/path/info";
#		$pathinfo['basename'] = "";
#		$pathinfo['extension'] = "";
#		$pathinfo['filename'] = "";
#
#	NOTE : If some idiot makes a directory like:
#
#	C:/my/path/info.txt
#
#	Or any other stupidly named directory - this function now handles it
#	properly.
#
	if( is_dir($path) ){
		$pathinfo['dirname'] = $path;
		$pathinfo['basename'] = "";
		$pathinfo['extension'] = "";
		$pathinfo['filename'] = "";
		}

	return $pathinfo;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['files']) ){
		$GLOBALS['classes']['files'] = new class_files();
		}

?>
