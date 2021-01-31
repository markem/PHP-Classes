<?php

	if( file_exists("../class_debug.php") ){
		include_once( "../class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( "Can not load CLASS_DEBUG" );
			}
		else {
			die( "Can not load CLASS_DEBUG" );
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:28:53.92 
#	---------------------------------------------------------------------------
#	This code is now under the MIT License.
#
#END DOC
################################################################################
class class_files
{
	private $all_exts = null;
	private $exts = null;

	public $debug = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct(){ $this->init( func_get_args() ); }
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
#
#	Arguments are looked at HERE. Don't put them in!
#
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( $args );
	$this->debug->in();

	$this->exts = [];		#	File Extension RegExps
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

	$dirs[] = $top_dir;
	$bad = array();
	$files = array();
	while( count($dirs) > 0 ){
		$dir = array_pop( $dirs );
#
#	See if we have permissions to read this.
#
		$perms = explode( ",", $this->get_perms($dir) );

		if( !$perms[0] === 'd' ){ continue; }
		if( ($perms[1] === '-') || ($perms[2] === '-') ){ continue; }
		if( ($perms[4] === '-') || ($perms[5] === '-') ){ continue; }
		if( ($perms[7] === '-') || ($perms[8] === '-') ){ continue; }

		if( ($dh = @opendir($dir)) ){
			if( !is_resource($dh) ){ continue; }
			while( ($file = readdir($dh)) !== false ){
				$curfile = "$dir/$file";
				$this->debug->log( "FOUND : $file\n" );
				if( $file != "." && $file != ".." ){
					if( is_dir("$dir/$file") && $opt == true){ $dirs[] = "$dir/$file"; }
						else if( preg_match($regexp, $file) ){ $files[] = "$dir/$file"; }
						else { $bad[] = "$dir/$file"; }
					}
				}

			closedir( $dh );
			}
		}

	foreach( $files as $k=>$v ){ $files[$k] = str_replace("//", "/", $v ); }
	foreach( $files as $k=>$v ){
		if( !preg_match("/mac/i", PHP_OS) && preg_match("/__macosx/i", $v) ){
			$bad[] = $v;
			unset( $files[$k] );
			}
		}

	$files = array_reverse( array_reverse($files) );

	foreach( $bad as $k=>$v ){ $bad[$k] = str_replace("//", "/", $v ); }

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

	$files = [];
	$dirs[] = $top_dir;
	while( count($dirs) > 0 ){
		$dir = array_pop( $dirs );
		$files[$dir] = 0;
		if( ($dh = opendir($dir)) ){
			while( ($file = readdir($dh)) !== false ){
				$cur_dir = "$dir/$file";
				$this->debug->log( "FOUND : $file\n" );
				if( $file != "." && $file != ".." ){
					if( preg_match($regexp, $cur_dir) ){
						if( is_dir($cur_dir) && $opt == true){ $files[$dir]++; $dirs[] = $cur_dir; }
							else { $files[$dir]++; }
						}
					}
				}

			closedir( $dh );
			}
		}

	foreach( $files as $k=>$v ){
		$l = str_replace("//", "/", $k );
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
		echo "No such file : $file\n";
		return false;
		}

	if( ($perms = fileperms($file)) === false ){
		echo "Fileperms returned an ERROR : $perms\n";
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
			$this->debug->log( "Loading GIF file : $file\n" );
			$gd = imagecreatefromgif($file);
			}
			else if( preg_match("/gd$/i", $file) ){
				$this->debug->log( "Loading GD file : $file\n" );
				$gd = imagecreatefromgd($file);
				}
			else if( preg_match("/gd2$/i", $file) ){
				$this->debug->log( "Loading GD2 file : $file\n" );
				$gd = imagecreatefromgd2($file);
				}
			else if( preg_match("/(jpg|jpeg|exif|jfif|jfi)$/i", $file) ){
				$this->debug->log( "Loading JPG file : $file\n" );
				$gd = imagecreatefromjpeg($file);
				}
			else if( preg_match("/wbmp$/i", $file) ){
				$this->debug->log( "Loading WBMP file : $file\n" );
				$gd = imagecreatefromwbmp($file);
				}
			else if( preg_match("/bmp$/i", $file) ){
				$this->debug->log( "Loading BMP file : $file\n" );
				$gd = imagecreatefrombmp($file);
				}
			else if( preg_match("/xbm$/i", $file) ){
				$this->debug->log( "Loading XBM file : $file\n" );
				$gd = imagecreatefromxbm($file);
				}
			else if( preg_match("/xpm$/i", $file) ){
				$this->debug->log( "Loading XPM file : $file\n" );
				$gd = imagecreatefromxpm($file);
				}
			else if( preg_match("/png$/i", $file) ){
				$this->debug->log( "Loading PNG file : $file\n" );
				$gd = imagecreatefrompng($file);
				}
			else if( preg_match("/(web|webp)$/i", $file) ){
				$this->debug->log( "Loading WEBP file : $file\n" );
				$gd = imagecreatefromwebp($file);
				}
			else if( preg_match("/(tif|tiff)$/i", $file) ){
				$this->debug->log( "DIE : GD does not do TIF : $file\n", true );
				return;
				}
			else {
				$this->debug->log( "DIE : Unknown file format : $file\n", true );
				}
		}
		catch( exception $e ){
			$this->debug->log( $e->getMessage(), true );
			}

	if( !is_resource($gd) ){
		$this->debug->log( "DIE : GD is NOT a resource", true );
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
		else { $this->debug->log( "Unknown file format....aborting", true); }

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

	if( is_null($gd) ){ die( "No image sent over...aborting.\n" ); }

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

	if( is_null($gd) ){ die( "No image sent over...aborting.\n" ); }
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
	$this->debug->log( "SX = $sx, SY = $sy\n" );
#
#	This can come over as:
#
#	ARRAY	:	SX, SY (ex: array(4,5) )
#	STRING	:	"SXxSY" (ex: "$x10")
#	Two Separate Values (ie: $sx=5, $sy=99)
#
	if( is_array($sx) ){ $s1 = $sx[0]; $sy = $sx[1]; unset($sx); $sx = $s1; }
		else if( preg_match("/x/i", $sx) ){ $s = explode( 'x', $sx ); unset($sx); $sx = $s[0]; $sy = $s[1]; }

	if( is_null($gd) ){ $this->debug->log( "GD is NULL", true ); }
	if( is_null($sx) ){ $this->debug->log( "SX is NULL", true ); }
	if( is_null($sy) ){ $this->debug->log( "SY is NULL", true ); }

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
			$b = $palette{$j++};
			$g = $palette{$j++};
			$r = $palette{$j++};
			$a = $palette{$j++};
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
				$b = $scan_line{$j++};
				$g = $scan_line{$j++};
				$r = $scan_line{$j++};
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
					$byte = ord($scan_line{$j++});
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
						$byte = ord($scan_line{$j++});
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
function find_box( $gd, $color, $offset=0, $opt=0 )
{
	$this->debug->in();

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
	$this->debug->log( "COLOR #1 = " . dechex($color) . "\n" );

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
			$this->debug->log( "NO imagecolorallocatealpha function", true );
			}

	if( is_resource($this->debug) ){
		$this->debug->m( "W = $w, H = $h\n" );
		$this->debug->m( "W2 = $w2, H2 = $h2\n" );
		$this->debug->m( "Saving : ./" . __FUNCTION__ . "-image-$fc.png @ " . __LINE__ . "\n" );
		imagepng( $gd2, "./" . __FUNCTION__ . "-image-$fc.png" );
		$fc++;
		}

    $color = imagecolorat( $gd2, 0, 0 );
	$this->debug->log( "COLOR #1 = " . dechex($color) . "\n" );

	$a = ($color >> 24) & 0xff;
	$r = ($color >> 16) & 0xff;
	$g = ($color >> 8) & 0xff;
	$b = $color & 0xff;

	$this->debug->log( "A = $a, R = $r, G = $g, B = $b\n" );

	$this->debug->log( "COLOR #2 = " . dechex($color) . "\n" );

    $w = imagesx( $gd2 );
    $h = imagesy( $gd2 );

	$top = $bot = $left = $right = 0;
#
#	Left
#
	$flag = false;
	$this->debug->log( "Working on the LEFT part\n" );
    for( $x=0; $x<$w; $x++ ){
        for( $y=0; $y<$h; $y++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->log( "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $left = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	$this->debug->log( "Left : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
#
#	Top
#
	$flag = false;
	$this->debug->log( "Working on the TOP part\n" );
	for( $y=0; $y<$h; $y++ ){
		for( $x=0; $x<$w; $x++ ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->log( "Top : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $top = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Right
#
	$flag = false;
	$this->debug->log( "Working on the RIGHT part\n" );
	for( $x=($w-1); $x>0; $x-- ){
		for( $y=($h-1); $y>0; $y-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->log( "Right : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $right = $x; $flag = true; break; }
			}

		if( $flag ){ break; }
		}
#
#	Bottom
#
	$flag = false;
	$this->debug->log( "Working on the BOTTOM part\n" );
	for( $y=($h-1); $y>0; $y-- ){
		for( $x=($w-1); $x>0; $x-- ){
            $rgb = imagecolorat( $gd2, $x, $y );
			$this->debug->log( "Bottom : X = $x, Y = $y, Color = $color, RGB = $rgb\n" );
            if( ($color != $rgb) ){ $bot = $y; $flag = true; break; }
			}

		if( $flag ){ break; }
		}

	$this->debug->log( "Left = $left, Right = $right, Top = $top, Bottom = $bot\n" );

    if( ($top == 99999) || ($bot == -99999) || ($left == 99999) || ($right == -99999) ){ return null; }

	$os = 3;
    $w2 = abs($right - $left) + ($os * 2) + 1;
    $h2 = abs($bot - $top) + ($os * 2) + 1;

    $color = imagecolorat( $gd2, 0, 0 );
	$this->debug->log( "COLOR #1 = " . dechex($color) . "\n" );

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
			$this->debug->log( "NO imagecolorallocatealpha function", true );
			}

	if( is_resource($this->debug) ){
		$this->debug->log( "W = $w, H = $h" );
		$this->debug->log( "W2 = $w2, H2 = $h2" );
		$this->debug->log( "Saving : ./" . __FUNCTION__ . "-image-$fc.png @ " );
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
	$this->debug->in();
	if( is_null($srcImg) ){ $this->debug->log( "GD is NULL", true ); }
	if( is_null($angle) ){ $this->debug->log( "NO ANGLE GIVEN", true ); }
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
	$this->debug->in();
	if( is_null($gd) ){ $this->debug->log( "GD is NULL", true ); }

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
#		$this->debug->m( "I = $i, W = $w - $w2, H = $h - $h2\n" );
#		imagepng( $gd2, "./test-3.png" ); sleep( 1 );

		if( ($w2 * $h2) < ($aw * $ah) ){
			$angle = $i;
			$aw = $w2;
			$ah = $h2;
			}
		}

	if( $angle > 0 ){
#		$this->debug->m( "Returning NEW : Angle = $angle, W = $aw, H = $ah\n" );
		$gd2 = $this->rot_image( $gd, $angle );
		$gd2 = $this->trim_image( $gd2 );

		$this->debug->out();
		return $gd2;
		}

#	$this->debug->m( "Returning OLD\n" );
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
							$this->debug->log("Couldn't find a color " );
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
	$this->debug->m( "Directory to work with? " );
	$dir = rtrim( stream_get_line(STDIN, 1024, PHP_EOL) );
	if( strlen($dir) < 1 ){ $dir = $cwd; }
	$dir = str_replace( "\\", "/", $dir );
	$this->debug->m( "DIR = $dir\n" );
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
		$this->debug->m( "Working on : $v\n" );
		$fh = fopen( $v, "r" );
		$r = fread( $fh, 1024 );
		fclose( $fh );
#
#	Probably one time deal
#
		if( preg_match("/pngp$/i", $v) ){
			$f = preg_replace( "/pngp$/i", "png", $v );
			$this->debug->m( "Renaming :	$v\nTo		: $f\n" );
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
			$this->debug->m( "Saving	: $v\nTo	: $png\n" );
			rename( $v, $png );
			$v = $png;
			}

		$gif = preg_match( "/gif/i", substr($r, 1, 3) );
		if( $gif && !preg_match("/gif$/i", $v) ){
			$gif = preg_replace( "/\.\w{3,4}$/i", ".gif", $v );
			$this->debug->m( "Saving	: $v\nTo	: $gif\n" );
			rename( $v, $gif );
			$v = $gif;
			}

		$bmp = preg_match( "/(bm|ba|ci|cp|ic|pt)/i", substr($r, 1, 2) );
		if( $bmp && !preg_match("/bmp$/i", $v) ){
			$bmp = preg_replace( "/\.\w{3,4}$/i", ".bmp", $v );
			$this->debug->m( "Saving	: $v\nTo	: $bmp\n" );
			rename( $v, $bmp );
			$v = $bmp;
			}

		$hs = 0x00;
		$jpg = preg_match( "/(exif|jfif|jfi|jpg|jpeg)$hs/i", substr($r, 6, 5) );
		if( $jpg && !preg_match("/(exif|jfif|jfi|jpg|jpeg)$/i", $v) ){
			$jpg = preg_replace( "/\.\w{3,4}$/i", ".jpg", $v );
			$this->debug->m( "Saving	: $v\nTo	: $jpg\n" );
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
			$this->debug->m( "Saving	: $v\nTo	: $tif\n" );
			rename( $v, $tif );
			$v = $tif;
			}

		$webp = ( preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) );
		if( $webp && !preg_match("/webp$/i", $v) ){
			$webp = preg_replace( "/\.\w{3,4}$/i", ".webp", $v );
			$this->debug->m( "Saving	: $v\nTo	: $webp\n" );
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
			$this->debug->m( "Moving $v : WEBP to PNG\n" );
			$gd = imagecreatefromwebp( $v );
			$png = preg_replace( "/(webp|web)$/i", "png", $v );
			$this->debug->m( "Saving to $png\n" );
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
	if( is_null($g) ){ $this->debug->log( "DIE : No file array given", true ); }
	if( is_null($file_ext) ){ $this->debug->log( "DIE: No file extension give", true ); }

	$ext = $this->exts[$file_ext];
	foreach( $g as $k=>$v ){
		if( !preg_match("/$file_ext$/i", $v) ){
			if( !preg_match("/(tif|tiff)$/i", $v) ){
				$gd = $this->get_image( $v );
				$chg = preg_replace( "/\w{3,4}$/i", $file_ext, $v );
				$this->debug->m( "Moving	: $v\nTo	: $chg\n" );
				$this->put_image( $gd, $chg );

				$this->debug->m( "Deleting	: $v\n" . str_repeat( '-', 80 ) . "\n" );
				unlink( $v );
				}
				else {
					if( preg_match("/tiff$/i", $v) ){
						$chg = preg_replace( "/\w{3,4}$/i", "tif", $v );
						$this->debug->m( "Renaming	: $v\nTo	: $chg\n" );
						rename( $v, $chg );
						}
						else { $this->debug->m( "File OK	: $v\n" ); }
					}
			}
			else { $this->debug->m( "File OK	: $v\n" ); }
		}

	$this->debug->out();
}
################################################################################
#	remove_nonwords(). Remove non-words and replace them with underscores.
################################################################################
function remove_nonwords( $g=null )
{
	if( is_null($g) ){ $this->debug->log( "List is empty", true ); }

	$a = [];
	foreach( $g as $k=>$v ){
		$base = trim( basename($v) );
		$base = preg_replace( "/\W+/", "_", $base );
		$base = substr( $base, 0, -4 ) . "." . substr( $base, -3, 3 );
		$f = dirname( $v ) . "/$base";
		$this->debug->m( "Renaming	: $v\nTo	: $f\n" );
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
	$this->debug->in();

	if( is_null($g) ){ $this->debug->log( "Array is NULL - aborting" ); }

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

	if( is_null($file) ){ $this->debug->log( "FILE is NULL", true ); }

	$array = [];
	if( ($fp = fopen( $file, "r" )) !== FALSE ){
		while( ($data = fgetcsv($fp, 1024, $sep)) !== FALSE ){ $array[] = $data; }
		}
		else { $this->debug->log( "Could not read $file", true ); }

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

	if( is_null($file) ){ $this->debug->log( "FILE is NULL", true ); }
	if( is_null($array) ){ $this->debug->log( "ARRAY is NULL", true ); }

	if( ($fp = fopen( $file, "w" )) !== FALSE ){
		foreach( $array as $k=>$v ){ fputcsv( $fp, $v, $sep ); }
		}
		else { $this->debug->log( "Could not read $file", true ); }

	$this->debug->out();

	fclose( $fp );
}
################################################################################
#	get_cert(). Get a certutil done.
#	Arguments	:	FILE and LEVEL.
#					FILE is the file to check
#					LEVEL is the type of cert you want. Default is SHA512.
#	Example:	:	$val = $cf->get_cert( "file=myfile.dat", "level=MD2" );
#	Notes	:	Types of hash information
#				MD2, MD4, MD5, SHA1, SHA256, SHA384, SHA512 (Default)
#
#				You only need the FIRST letter of the command.
#
################################################################################
function get_cert( $file=null, $level=null )
{
	$this->debug->in();

	if( is_null($file) || (strlen(trim($file)) < 1) ){
		$this->debug->log( "FILE is NULL" );
		return false;
		}

	if( is_null($level) || (strlen(trim($level)) < 1) ){ $level = "SHA512"; }

	$this->debug->log( "FILE = $file" );
	$this->debug->log( "LEVEL = $level" );

	if( is_null($file) || !file_exists($file) ){
		$this->debug->die( "FILE is NULL", true );
		}

	if( !preg_match("/(md2|md4|md5|sha1|sha256|sha384|sha512)/i", $level) &&
		!is_null($level) ){ $this->debug->die( "LEVEL is $level", true ); }
		elseif( is_null($level) ){ $level = "SHA512"; }

#echo "FILE #1 = $file\n";
	$file = str_replace( "!", "\!", $file );
	$file = str_replace( "(", "\(", $file );
	$file = str_replace( ")", "\)", $file );
#echo "FILE #2 = $file\n";

	$cmd = "certutil -hashfile \"$file\" $level";
#echo "CMD = $cmd\n\n" . str_repeat( '-', 80 ) . "\n\n";

	$fp = popen( $cmd, "r" );
	if( !is_resource($fp) ){ $this->debug->die( "FP is NOT a resource", true ); }

	$ret = fgets( $fp );
	$info = fgets( $fp );

	pclose( $fp );

	$this->debug->out();
	return trim( $info );
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

	$this->debug->m( "Dump	: " );
	for ($i = 0; $i < $l; $i++) {
		$this->debug->m( str_pad(dechex(ord($r[$i])), 2, '0', STR_PAD_LEFT) );
		}

	$this->debug->m( "\nHeader  : " );
	for ($i = 0; $i < 32; $i++) {
		$s = ord( $r[$i] );
		$s = ($s > 127) ? $s - 127 : $s;
		$s = ($s < 32) ? ord(" ") : $s;
		$this->debug->m( chr( $s ) );
		}

	$this->debug->m( "\n" );

	$this->debug->out();

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['files']) ){ $GLOBALS['classes']['files'] = new class_files(); }

?>
