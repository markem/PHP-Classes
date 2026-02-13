<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	  Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		throw new ErrorException($errstring, $errno, 0, $errfile, $errline);
		die( "Error #$errno IN $errfile
		@$errline\nContent: " . $errstring. "\n"
		); });

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

	$ary = [];
	$cwd = getcwd();
	$file = "./paperTypes.csv";

#	Most of the following page sizes came from
#
#		The PaperSizes website at
#		https://papersizes.io
#
	$a = <<<EOT
Size	Width x Height mm	Width x Height in
4A0	1682 x 2378 mm	66.2 x 93.6 in	A Paper
2A0	1189 x 1682 mm	46.8 x 66.2 in	A Paper
A0	841 x 1189 mm	33.1 x 46.8 in	A Paper
A1	594 x 841 mm	23.4 x 33.1 in	A Paper
A2	420 x 594 mm	16.5 x 23.4 in	A Paper
A3	297 x 420 mm	11.7 x 16.5 in	A Paper
A4	210 x 297 mm	8.3 x 11.7 in	A Paper
A5	148 x 210 mm	5.8 x 8.3 in	A Paper
A6	105 x 148 mm	4.1 x 5.8 in	A Paper
A7	74 x 105 mm	2.9 x 4.1 in	A Paper
A8	52 x 74 mm	2.0 x 2.9 in	A Paper
A9	37 x 52 mm	1.5 x 2.0 in	A Paper
A10	26 x 37 mm	1.0 x 1.5 in	A Paper
A11	18 × 26 mm	0.7 × 1 in	A Paper
A12	13 × 18 mm	0.5 × 0.7 in	A Paper
A13	9 × 13 mm	0.4 × 0.5 in	A Paper
A0+	914 × 1292 mm	36 × 50.9 in	A Paper
A1+	609 × 914 mm	24 × 36 in	A Paper
A3+	329 × 483 mm	13 × 19 in	A Paper
B0	1000 x 1414 mm	39.4 x 55.7 in	B Paper
B1	707 x 1000 mm	27.8 x 39.4 in	B Paper
B2	500 x 707 mm	19.7 x 27.8 in	B Paper
B3	353 x 500 mm	13.9 x 19.7 in	B Paper
B4	250 x 353 mm	9.8 x 13.9 in	B Paper
B5	176 x 250 mm	6.9 x 9.8 in	B Paper
B6	125 x 176 mm	4.9 x 6.9 in	B Paper
B7	88 x 125 mm	3.5 x 4.9 in	B Paper
B8	62 x 88 mm	2.4 x 3.5 in	B Paper
B9	44 x 62 mm	1.7 x 2.4 in	B Paper
B10	31 x 44 mm	1.2 x 1.7 in	B Paper
B11	22 × 31 mm	0.9 × 1.2 in	B Paper
B12	15 × 22 mm	0.6 × 0.9 in	B Paper
B13	11 × 15 mm	0.4 × 0.6 in	B Paper
B0+	1118 × 1580 mm	44 × 62.2 in	B Paper
B1+	720 × 1020 mm	28.3 × 40.2 in	B Paper
B2+	520 × 720 mm	20.5 × 28.3 in	B Paper
B1XL	750 x 1050 mm	29.5 x 41.3 in
B2+	530 x 750 mm	20.9 x 29.5 in	B Paper
RB0	1025 x 1449 mm	40.4 x 57.0 in	Unknown
RB1	725 x 1025 mm	28.5 x 40.4 in	Unknown
RB2	513 x 725 mm	20.2 x 28.5 in	Unknown
RB3	363 x 513 mm	14.3 x 20.2 in	Unknown
RB4	257 x 363 mm	10.1 x 14.3 in	Unknown
SRB0	1072 x 1516 mm	42.2 x 59.9 in	Unknown
SRB1	758 x 1072 mm	29.8 x 42.2 in	Unknown
SRB2	536 x 758 mm	21.1 x 29.8 in	Unknown
SRB3	379 x 536 mm	14.9 x 21.1 in	Unknown
SRB4	268 x 379 mm	10.6 x 14.9 in	Unknown
C0	917 x 1297 mm	36.1 x 51.5 in	C Envelope Paper
C1	648 x 917 mm	25.5 x 36.1 in	C Envelope Paper
C2	458 x 648 mm	18.0 x 25.5 in	C Envelope Paper
C3	324 x 458 mm	12.8 x 18.0 in	C Envelope Paper
C4	229 x 324 mm	9.0 x 12.8 in	C Envelope Paper
C5	162 x 229 mm	6.4 x 9.0 in	C Envelope Paper
C6	114 x 162 mm	4.5 x 6.4 in	C Envelope Paper
C7	81 x 114 mm	3.2 x 4.5 in	C Envelope Paper
C8	57 x 81 mm	2.2 x 3.2 in	C Envelope Paper
C9	40 x 57 mm	1.6 x 2.2 in	C Envelope Paper
C10	28 x 40 mm	1.1 x 1.6 in	C Envelope Paper
RA0	860 x 1220 mm	33.9 x 48.0 in	Unknown
RA1	610 x 860 mm	24.0 x 33.9 in	Unknown
RA2	430 x 610 mm	16.9 x 24.0 in	Unknown
RA3	305 x 430 mm	12.0 x 16.9 in	Unknown
RA4	215 x 305 mm	8.5 x 12.0 in	Unknown
SRA0	900 x 1280 mm	35.4 x 50.4 in	Unknown
SRA1	640 x 900 mm	25.2 x 35.4 in	Unknown
SRA2	450 x 640 mm	17.7 x 25.2 in	Unknown
SRA3	320 x 450 mm	12.6 x 17.7 in	Unknown
SRA4	225 x 320 mm	8.9 x 12.6 in	Unknown
SRA1+	660 x 920 mm	26.0 x 36.2 in	Unknown
SRA2+	480 x 650 mm	18.9 x 25.6 in	Unknown
SRA3+	320 x 460 mm	12.6 x 18.1 in	Unknown
SRA3++	320 x 464 mm	12.6 x 18.3 in	Unknown
Half Letter	140 x 216 mm	5.5 x 8.5 in	US Paper
Letter	216 x 279 mm	8.5 x 11.0 in	US Paper
Legal	216 x 356 mm	8.5 x 14.0 in	US Paper
Junior Legal	127 x 203 mm	5.0 x 8.0 in	US Paper
Ledger	279 x 432 mm	11.0 x 17.0 in	US Paper
Tabloid	279 x 432 mm	11.0 x 17.0 in	US Paper
A	216 x 279 mm	8.5 x 11.0 in	US Paper
B	279 x 432 mm	11.0 x 17.0 in	US Paper
C	432 x 559 mm	17.0 x 22.0 in	US Paper
D	559 x 864 mm	22.0 x 34.0 in	US Paper
E	864 x 1118 mm	34.0 x 44.0 in	US Paper
Arch A	229 x 305 mm	9.0 x 12.0 in	US Paper
Arch B	305 x 457 mm	12.0 x 18.0 in	US Paper
Arch C	457 x 610 mm	18.0 x 24.0 in	US Paper
Arch D	610 x 914 mm	24.0 x 36.0 in	US Paper
Arch E	914 x 1219 mm	36.0 x 48.0 in	US Paper
Arch E1	762 x 1067 mm	30.0 x 42.0 in	US Paper
6 1/4	152.4 x 88.9 mm	6.0 x 3.5 in	US Envelope Paper
6 3/4	165.1 x 92.1 mm	6.5 x 3.625 in	US Envelope Paper
7	171.5 x 95.3 mm	6.75 x 3.75 in	US Envelope Paper
7 3/4	190.5 x 98.4 mm	7.5 x 3.875 in	US Envelope Paper
Monarch	190.5 x 98.4 mm	7.5 x 3.875 in	US Envelope Paper
8 5/8	219.1 x 92.1 mm	8.625 x 3.625 in	US Envelope Paper
9	225.4 x 98.4 mm	8.875 x 3.875 in	US Envelope Paper
10	241.3 x 104.8 mm	9.5 x 4.125 in	US Envelope Paper
11	263.5 x 114.3 mm	10.375 x 4.5 in	US Envelope Paper
12	279.4 x 120.7 mm	11.0 x 4.75 in	US Envelope Paper
14	292.1 x 127.0 mm	11.5 x 5.0 in	US Envelope Paper
16	304.8 x 152.4 mm	12.0 x 6.0 in	US Envelope Paper
Envelope A1	92.1 x 130.2 mm	3.625 x 5.125 in	US Envelope Paper
Envelope A2	146.1 x 111.1 mm	5.75 x 4.375 in	US Envelope Paper
Envelope A2 Lady Grey	146.1 x 111.1 mm	5.75 x 4.375 in	US Envelope Paper
Envelope A4	158.7 x 108.0 mm	6.25 x 4.25 in	US Envelope Paper
Envelope A6	165.1 x 120.7 mm	6.5 x 4.75 in	US Envelope Paper
Envelope Thompsons Standard	165.1 x 120.7 mm	6.5 x 4.75 in	US Envelope Paper
Envelope A7	184.2 x 133.4 mm	7.25 x 5.25 in	US Envelope Paper
Envelope Besselheim	184.2 x 133.4 mm	7.25 x 5.25 in	US Envelope Paper
Envelope A8	206.4 x 139.7 mm	8.125 x 5.5 in	US Envelope Paper
Envelope Carrs	206.4 x 139.7 mm	8.125 x 5.5 in	US Envelope Paper
Envelope A9	222.3 x 146.1 mm	8.75 x 5.75 in	US Envelope Paper
Envelope Diplomat	222.3 x 146.1 mm	8.75 x 5.75 in	US Envelope Paper
Envelope A10	241.3 x 152.4 mm	9.5 x 6.0 in	US Envelope Paper
Envelope Willow	241.3 x 152.4 mm	9.5 x 6.0 in	US Envelope Paper
Envelope A Long	225.4 x 98.4 mm	8.875 x 3.875 in	US Envelope Paper
Envelope 1	228.6 x 152.4 mm	9.0 x 6.0 in	US Envelope Paper
Envelope 1 3/4	241.3 x 165.1 mm	9.5 x 6.5 in	US Envelope Paper
Envelope 3	254.0 x 177.8 mm	10.0 x 7.0 in	US Envelope Paper
Envelope 6	1266.7 x 190.5 mm	10.5 x 7.5 in	US Envelope Paper
Envelope 8	285.8 x 209.6 mm	11.25 x 8.25 in	US Envelope Paper
Envelope 9 3/4	285.8 x 222.3 mm	11.25 x 8.75 in	US Envelope Paper
Envelope 10 1/2	304.8 x 228.6 mm	12.0 x 9.0 in	US Envelope Paper
Envelope 12 1/2	317.5 x 241.3 mm	12.5 x 9.5 in	US Envelope Paper
Envelope 13 1/2	330.2 x 254.0 mm	13.0 x 10.0 in	US Envelope Paper
Envelope 14 1/2	368.3 x 292.1 mm	14.5 x 11.5 in	US Envelope Paper
Envelope 15	381.0 x 254.0 mm	15.0 x 10.0 in	US Envelope Paper
Envelope 15 1/2	393.7 x 304.8 mm	15.5 x 12.0 in	US Envelope Paper
Candian P1	560 x 860 mm	22.0 x 33.9 in	Unknown
Candian P2	430 x 560 mm	16.9 x 22.0 in	Unknown
Candian P3	280 x 430 mm	11.0 x 16.9 in	Unknown
Candian P4	215 x 280 mm	8.5 x 11.0 in	Unknown
Candian P5	140 x 210 mm	5.5 x 8.3 in	Unknown
Candian P6	105 x 140 mm	4.1 x 5.5 in	Unknown
Japanese B0	1030 x 1456 mm	40.6 x 57.3 in	Unknown
Japanese B1	728 x 1030 mm	28.7 x 40.6 in	Unknown
Japanese B2	515 x 728 mm	20.3 x 28.7 in	Unknown
Japanese B3	364 x 515 mm	14.3 x 20.3 in	Unknown
Japanese B4	257 x 364 mm	10.1 x 14.3 in	Unknown
Japanese B5	182 x 257 mm	7.2 x 10.1 in	Unknown
Japanese B6	128 x 182 mm	5.0 x 7.2 in	Unknown
Japanese B7	91 x 128 mm	3.6 x 5.0 in	Unknown
Japanese B8	64 x 91 mm	2.5 x 3.6 in	Unknown
Japanese B9	45 x 64 mm	1.8 x 2.5 in	Unknown
Japanese B10	32 x 45 mm	1.3 x 1.8 in	Unknown
Shirokuban 1	264 x 379 mm	10.39 x 14.92 in	Unknown
Shirokuban 2	189 x 262 mm	7.44 x 10.31 in	Unknown
Shirokuban 3	127 x 188 mm	5.00 x 7.40 in	Unknown
Kiku 1	227 x 306 mm	8.94 x 12.05 in	Unknown
Kiku 2	151 x 227 mm	5.94 x 8.94 in	Unknown
Japanese AB	210 x 257 mm	8.27 x 10.12 in	Unknown
Japanese B40	103 x 182 mm	4.06 x 7.17 in	Unknown
Japanese Shikisen	84 x 148 mm	3.31 x 5.83 in	Unknown
Bond	558.8 x 431.8 mm	22.0 x 17.0 in	Unknown
Book	965.2 x 635.0 mm	38.0 x 25.0 in	Unknown
Cover	660.4 x 508.0 mm	26.0 x 20.0 in	Unknown
Index	774.7 x 647.7 mm	30.5 x 25.5 in	Unknown
Newsprint	914.4 x 609.6 mm	36.0 x 24.0 in	Unknown
Offset	965.2 x 635.0 mm	38.0 x 25.0 in	Unknown
Text	965.2 x 635.0 mm	38.0 x 25.0 in	Unknown
Tissue	914.4 x 609.6 mm	36.0 x 24.0 in	Unknown
Broadsheet	600 x 750 mm	23.5 x 29.5 in	Unknown
Berliner	315 x 470 mm	12.4 x 18.5 in	Unknown
Midi	315 x 470 mm	12.4 x 18.5 in	Unknown
Tabloid Size	280 x 430 mm	11.0 x 16.9 in	Unknown
Postcard Maximum	235 x 120 mm	9.25 x 4.72 in	Unknown
Postcard Minimum	140 x 90 mm	5.51 x 3.54 in	Unknown
US PostCard Maximum	6.0 x 4.25 in	152.4 x 107.9 mm	Unknown
US PostCard Minimum	5.0 x 3.5 in	127.0 x 88.9 mm	Unknown
Court Cards	4.75 x 3.5 in	120.65 x 88.9 mm	Unknown
British Maximum	5.5 x 3.5 in	139.7 x 88.9 mm	British Paper
British Minimum	3.25 x 3.25 in	82.55 x 82.55 mm	British Paper
British Minimum 1906	4.0 x 2.75 in	101.6 x 69.85 mm	British Paper
British Maximum 1925	5.875 x 4.125 in	149.225 x 104.775 mm	British Paper
ISO Poster 2A0	1189 x 1682 mm	46.8 x 66.2 in	ISO Poster Paper
ISO Poster A0	841 x 1189 mm	33.1 x 46.8 in	ISO Poster Paper
ISO Poster A1	594 x 841 mm	23.4 x 33.1 in	ISO Poster Paper
ISO Poster A2	420 x 594 mm	16.5 x 23.4 in	ISO Poster Paper
ISO Poster A3	297 x 420 mm	11.7 x 16.5 in	ISO Poster Paper
ISO Poster A4	210 x 297 mm	8.3 x 11.7 in	ISO Poster Paper
British Poster 1 Sheet	508 x 762 mm	20 x 30 in	British Poster Paper
British Poster 2 Sheet	762 x 1016 mm	30 x 40 in	British Poster Paper
British Poster 4 Sheet	1016 x 1524 mm	40 x 60 in	British Poster Paper
UK Movie Poster Cards	203.2 x 254.0 mm	8 x 10 in	UK Movie Poster Paper
UK Movie Poster Double Crown	508 x 762 mm	20 x 30 in	UK Movie Poster Paper
UK Movie Poster One Sheet	685.8 x 1016 mm	27 x 40 in	UK Movie Poster Paper
UK Movie Poster Quad	762 x 1016 mm	30 x 40 in	UK Movie Poster Paper
UK Movie Poster Three Sheet	1016 x 2057.4 mm	40 x 81 in	UK Movie Poster Paper
UK Movie Poster Six Sheet	2032 x 2057.4 mm	80 x 81 in	UK Movie Poster Paper
US Poster Letter	215.9 x 279.4 mm	8.5 x 11 in	US Poster Paper
US Poster Small	279.4 x 431.8 mm	11 x 17 in	US Poster Paper
US Poster Medium	457.2 x 609.6 mm	18 x 24 in	US Poster Paper
US Poster Large	609.6 x 914.4 mm	24 x 36 in	US Poster Paper
US Movie Poster Lobby Card	279.4 x 355.6 mm	11 x 14 in	US Poster Paper
US Movie Poster Window Card	355.6 x 558.8 mm	14 x 22 in	US Poster Paper
US Movie Poster Insert	355.6 x 914.4 mm	14 x 36 in	US Poster Paper
US Movie Poster Half Sheet	558.8 x 711.2 mm	22 x 28 in	US Poster Paper
US Movie Poster One Sheet	685.8 x 1016 mm	27 x 40 in	US Poster Paper
US Movie Poster Three Sheet	1041.4 x 2057.4 mm	41 x 81 in	US Poster Paper
US Movie Poster Six Sheet	2057.4 x 2057.4 mm	81 x 81 in	US Poster Paper
US Movie Poster 30 x 40 Drive In	762 x 1016 mm	30 x 40 in	US Poster Paper
US Movie Poster 40 x 60 Drive In	1016 x 1524 mm	40 x 60 in	US Poster Paper
US Movie Poster Door Panels	508 x 1524 mm	20 x 60 in	US Poster Paper
French Movie Poster Petite	400 x 600 mm	15.7 x 23.6 in	French Poster Paper
French Movie Poster Moyenne	600 x 800 mm	23.6 x 31.5 in	French Poster Paper
French Movie Poster Pantalon	600 x 1600 mm	23.6 x 63.0 in	French Poster Paper
French Movie Poster Demi-Grande	800 x 1200 mm	31.5 x 47.2 in	French Poster Paper
French Movie Poster Grande	1200 x 1600 mm	47.2 x 63.0 in	French Poster Paper
French Movie Poster Double Grande	1600 x 2400 mm	63.0 x 94.5 in	French Poster Paper
Italian Movie Poster Un Foglio	700 x 1000 mm	27.6 x 39.4 in	Italian Poster Paper
Italian Movie Poster Due Fogli	1000 x 1400 mm	39.4 x 55.1 in	Italian Poster Paper
Italian Movie Poster Quattro Fogli	1400 x 2000 mm	55.1 x 78.7 in	Italian Poster Paper
Italian Movie Poster Locandina	330 x 700 mm	13.0 x 27.6 in	Italian Poster Paper
Italian Movie Poster Photobusta	500 x 700 mm	19.7 x 27.6 in	Italian Poster Paper
Australian Movie Poster Lobby Card	279.4 x 355.6 mm	11 x 14 in	Australian Poster Paper
Australian Movie Poster Daybill	660.4 x 762 mm	26 x 30 in	Australian Poster Paper
Australian Movie Poster One Sheet	685.8 x 1016 mm	27 x 40 in	Australian Poster Paper
Australian Movie Poster Three Sheet	1041.4 x 2057.4 mm	41 x 81 in	Australian Poster Paper
UK Billboard 4 Sheet	1.02 x 1.52 m	40 x 60 in	UK Billboard Paper
UK Billboard 6 Sheet	1.20 x 1.80 m	47.24 x 70.87 in	UK Billboard Paper
UK Billboard 12 Sheet	3.05 x 1.52 m	120 x 60 in	UK Billboard Paper
UK Billboard 16 Sheet	2.03 x 3.05 m	80 x 120 in	UK Billboard Paper
UK Billboard 32 Sheet	4.06 x 3.05 m	160 x 120 in	UK Billboard Paper
UK Billboard 48 Sheet	6.10 x 3.05 m	240 x 120 in	UK Billboard Paper
UK Billboard 64 Sheet	8.13 x 3.05 m	320 x 120 in	UK Billboard Paper
UK Billboard 96 Sheet	12.19 x 3.05 m	480 x 120 in	UK Billboard Paper
US Billboard 8 Sheet	3.35 x 1.52 m	132 x 60 in	US Billboard Paper
US Billboard 30 Sheet	6.91 x 3.17 m	272 x 125 in	US Billboard Paper
US Billboard 12 x 6 ft	3.66 x 1.83 m	144 x 72 in	US Billboard Paper
US Billboard 12 x 8 ft	3.66 x 2.44 m	144 x 96 in	US Billboard Paper
US Billboard 22 x 10 ft	3.66 x 1.83 m	264 x 120 in	US Billboard Paper
US Billboard 24 x 10 ft	3.66 x 2.44 m	288 x 120 in	US Billboard Paper
US Billboard 25 x 12 ft	7.62 x 3.66 m	300 x 144 in	US Billboard Paper
US Billboard 36 x 10.5 ft	10.97 x 3.20 m	432 x 126 in	US Billboard Paper
US Billboard 40 x 12 ft	12.19 x 3.66 m	480 x 144 in	US Billboard Paper
US Billboard 48 x 14 ft	14.63 x 4.27 m	576 x 168 in	US Billboard Paper
US Billboard 50 x 20 ft	15.24 x 6.10 m	600 x 240 in	US Billboard Paper
US Billboard 60 x 16 ft	18.29 x 4.88 m	720 x 192 in	US Billboard Paper
French Billboard Abribus 2m2	1.756 x 1.191 m	69.1 x 46.9 in	French Billboard Paper
French Billboard 12m2	4.00 x 3.00 m	157.5 x 118.1 in	French Billboard Paper
German Billboard City Star	3.56 x 2.52 m	140.2 x 99.2 in	German Billboard Paper
German Billboard Superpostern	5.26 x 3.72 m	207.1 x 146.5 in	German Billboard Paper
Plakatwand City Star	3.56 x 2.52 m	140.2 x 99.2 in	Unknown
Plakatwand Superpostern	5.26 x 3.72 m	207.1 x 146.5 in	Unknown
Austrian Billboard Brandboard	1.50 x 2.00 m	59.1 x 78.7 in	Austrian Billboard Paper
Austrian Billboard Dachflache	10.00 x 2.00 m	393.7 x 78.7 in	Austrian Billboard Paper
Austrian Billboard Megaboard	8.00 x 5.00 m	315.0 x 196.9 in	Austrian Billboard Paper
Austrian Billboard Centerboard	10.00 x 4.78 m	393.7 x 188.2 in	Austrian Billboard Paper
Plakatwand Brandboard	1.50 x 2.00 m	59.1 x 78.7 in	Unknown
Plakatwand Dachflache	10.00 x 2.00 m	393.7 x 78.7 in	Unknown
Plakatwand Megaboard	8.00 x 5.00 m	315.0 x 196.9 in	Unknown
Plakatwand Centerboard	10.00 x 4.78 m	393.7 x 188.2 in	Unknown
Netherlands Billboard	3.30 x 2.40 m	129.9 x 94.5 in;	Netherlands Billboard Paper
Reclamebord	3.30 x 2.40 m	129.9 x 94.5 in;	Unknown
British Imperial Cut Writing Paper Albert	4.0 x 6.0 in	101.6 x 152.4 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Duchess	4.5 x 6.0 in	114.3 x 152.4 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Duke	5.5 x 7.0 in	139.7 x 177.8 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Foolscap Quarto	6.5 x 8.0 in	152.4 x 203.2 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Foolscap Folio	8.0 x 13.0 in	203.2 x 330.2 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Small Post Octavo	4.5 x 7.0 in	114.3 x 177.8 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Small Post Quarto	7.0 x 9.0 in	177.8 x 228.6 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Large Post Octavo	5.0 x 8.0 in	127.0 x 203.2 mm	British Cut Writing Paper
British Imperial Cut Writing Paper Large Post Quarto	8.0 x 10.0 in	203.2 x 254.0 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Pott	12.5 x 15.0 in	317.5 x 381.0 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Double Pott	15.0 x 25.0 in	381.0 x 635.0 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Foolscap	13.25 x 16.5 in	336.6 x 419.1 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Double Foolscap	16.5 x 26.5 in	419.1 x 673.1 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Foolscap and Third	13.25 x 22.0 in	336.6 x 558.8 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Foolscap and Half	13.25 x 24.75 in	336.6 x 628.7 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Pinched Post	14.5 x 18.5 in	368.3 x 469.9 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Post	15.25 x 19.0 in	387.4 x 482.6 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Double Post	19.0 x 30.5 in	482.6 x 774.7 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Large Post	16.5 x 20.75 in	419.1 x 527.1 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Double Large Post	20.75 x 33.0 in	527.1 x 838.2 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Copy	16.25 x 20.0 in	412.8 x 508.0 mm	British Cut Writing Paper
British Imperial Uncut Writing Paper Medium	18.0 x 20.5 in	457.2 x 520.7 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Foolscap	14.0 x 18.75 in	355.6 x 476.3 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Demy	15.5 x 20 in	393.7 x 508.0 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Medium	17.5 x 22.5 in	444.5 x 571.5 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Royal	19.0 x 24.0 in	482.6 x 609.6 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Imperial	22.0 x 30.25 in	558.8 x 768.4 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Elephant	23.0 x 28.0 in	584.2 x 711.2 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Double Elephant	26.5 x 40.0 in	673.1 x 1016.0 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Atlas	26.25 x 34.0 in	666.75 x 863.6 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Columbier	23.5 x 24.5 in	596.9 x 622.3 mm	British Cut Writing Paper
Imperial Uncut Book & Drawing Paper Antiquarian	31.0 x 53.0 in	787.4 x 1346.2 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Crown	16.25 x 21.0 in	412.8 x 533.4 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Double Crown	20.0 x 30.0 in	508.0 x 762.0 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Quad	30.0 x 40.0 in	762.0 x 1016.0 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Demy	17.75 x 22.5 in	450.9 x 571.5 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Double Demy	22.5 x 35.5 in	571.5 x 901.7 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Medium	18.25 x 23.0 in	463.6 x 584.2 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Royal	20.0 x 25.0 in	508.0 x 635.0 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Super Royal	21.0 x 27.0 in	533.4 x 685.8 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Double Pott	15.0 x 25.0 in	381.0 x 635.0 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Double Post	19.0 x 30.5 in	482.6 x 774.7 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Foolscap	13.5 x 17.0 in	342.9 x 431.8 mm	British Cut Writing Paper
Imperial Uncut Printing Paper Double Foolscap	17.0 x 27.0 in	431.8 x 685.8 mm	British Cut Writing Paper
Junior Legal	127 × 203 mm	5 × 8 in	US Paper
Half Letter	140 × 216 mm	5.5 × 8.5 in	US Paper
Government Letter	203 × 267 mm	8 × 10.5 in	US Paper
Government Legal	216 × 330 mm	8.5 × 13 in	US Paper
ANSI A	216 × 279 mm	8.5 × 11 in	US Paper
ANSI B	279 × 432 mm	11 × 17 in	US Paper
ANSI C	432 × 559 mm	17 × 22 in	US Paper
ANSI D	559 × 864 mm	22 × 34 in	US Paper
ANSI E	864 × 1118 mm	34 × 44 in	US Paper
Arch E2	660 × 965 mm	26 × 38 in	US Paper
Arch E3	686 × 991 mm	27 × 39 in	US Paper
7	172 × 95 mm	6.8 × 3.7 in	US Envelope Paper
7 3/4 Monarch	191 × 98 mm	7.5 × 3.9 in	US Envelope Paper
9	225 × 98 mm	8.9 × 3.9 in	US Envelope Paper
10	241 × 104 mm	9.5 × 4.1 in	US Envelope Paper
11	264 × 114 mm	10.4 × 4.5 in	US Envelope Paper
12	279 × 121 mm	11 × 4.8 in	US Envelope Paper
14	292 × 127 mm	11.5 × 5 in	US Envelope Paper
16	305 × 152 mm	12 × 6 in	US Envelope Paper
A1	92 × 130 mm	3.6 × 5.1 in	US Envelope Paper
A2 Lady Grey	146 × 111 mm	5.7 × 4.4 in	US Envelope Paper
A4	159 × 108 mm	6.3 × 4.3 in	US Envelope Paper
A6 Thompsons Standard	165 × 121 mm	6.5 × 4.8 in	US Envelope Paper
A7 Besselheim	184 × 133 mm	7.2 × 5.2 in	US Envelope Paper
A8 Carrs	206 × 140 mm	8.1 × 5.5 in	US Envelope Paper
A9 Diplomat	222 × 146 mm	8.7 × 5.7 in	US Envelope Paper
A10 Willow	241 × 152 mm	9.5 × 6 in	US Envelope Paper
A Long	225 × 98 mm	8.9 × 3.9 in	US Envelope Paper
1	229 × 152 mm	9 × 6 in	US Envelope Paper
3	254 × 178 mm	10 × 7 in	US Envelope Paper
6	267 × 191 mm	10.5 × 7.5 in	US Envelope Paper
8	286 × 210 mm	11.3 × 8.3 in	US Envelope Paper
9 3/4	286 × 222 mm	11.3 × 8.7 in	US Envelope Paper
10 1/2	305 × 229 mm	12 × 9 in	US Envelope Paper
12 1/2	318 × 241 mm	12.5 × 9.5 in	US Envelope Paper
13 1/2	330 × 254 mm	13 × 10 in	US Envelope Paper
14 1/2	368 × 292 mm	14.5 × 11.5 in	US Envelope Paper
15	381 × 254 mm	15 × 10 in	US Envelope Paper
15 1/2	394 × 305 mm	15.5 × 12 in	US Envelope Paper
DL	110 × 220 mm	4.3 × 8.7 in	International Envelope Paper
B6	125 × 176 mm	4.9 × 6.9 in	International Envelope Paper
C3	324 × 458 mm	12.8 × 18 in	International Envelope Paper
C4	229 × 324 mm	9 × 12.8 in	International Envelope Paper
C4M	318 × 229 mm	12.5 × 9 in	International Envelope Paper
C5	162 × 229 mm	6.4 × 9 in	International Envelope Paper
C6/C5	114 × 229 mm	4.5 × 9 in	International Envelope Paper
C6	114 × 162 mm	4.5 × 6.4 in	International Envelope Paper
C64M	318 × 114 mm	12.5 × 4.5 in	International Envelope Paper
C7/C6	81 × 162 mm	3.2 × 6.4 in	International Envelope Paper
C7	81 × 114 mm	3.2 × 4.5 in	International Envelope Paper
CE4	229 × 310 mm	9 × 12.2 in	International Envelope Paper
CE64	114 × 310 mm	4.5 × 12.2 in	International Envelope Paper
E4	220 × 312 mm	8.7 × 12.3 in	International Envelope Paper
EC45	220 × 229 mm	8.7 × 9 in	International Envelope Paper
EC5	155 × 229 mm	6.1 × 9 in	International Envelope Paper
E5	115 × 220 mm	4.5 × 8.7 in	International Envelope Paper
E56	155 × 155 mm	6.1 × 6.1 in	International Envelope Paper
E6	110 × 155 mm	4.3 × 6.1 in	International Envelope Paper
E65	110 × 220 mm	4.3 × 8.7 in	International Envelope Paper
R7	120 × 135 mm	4.7 × 5.3 in	International Envelope Paper
S4	250 × 330 mm	9.8 × 13 in	International Envelope Paper
S5	185 × 255 mm	7.3 × 10 in	International Envelope Paper
S65	110 × 225 mm	4.3 × 8.9 in	International Envelope Paper
X5	105 × 216 mm	4.1 × 8.5 in	International Envelope Paper
EX5	155 × 216 mm	6.1 × 8.5 in	International Envelope Paper
Passport	35 × 45 mm	1.4 × 1.8 in	Photography Paper
2R	64 × 89 mm	2.5 × 3.5 in	Photography Paper
LD, DSC	89 × 119 mm	3.5 × 4.7 in	Photography Paper
3R, L	89 × 127 mm	3.5 × 5 in	Photography Paper
LW	89 × 133 mm	3.5 × 5.2 in	Photography Paper
KGD	102 × 136 mm	4 × 5.4 in	Photography Paper
4R, KG	102 × 152 mm	4 × 6 in	Photography Paper
2LD, DSCW	127 × 169 mm	5 × 6.7 in	Photography Paper
5R, 2L	127 × 178 mm	5 × 7 in	Photography Paper
2LW	127 × 190 mm	5 × 7.5 in	Photography Paper
6R	152 × 203 mm	6 × 8 in	Photography Paper
8R, 6P	203 × 254 mm	8 × 10 in	Photography Paper
S8R, 6PW	203 × 305 mm	8 × 12 in	Photography Paper
11R	279 × 356 mm	11 × 14 in	Photography Paper
A3+ Super B	330 × 483 mm	13 × 19 in	Photography Paper
Berliner	315 × 470 mm	12.4 × 18.5 in	Newspaper Paper
Broadsheet	597 × 749 mm	23.5 × 29.5 in	Newspaper Paper
US Broadsheet	381 × 578 mm	15 × 22.8 in	Newspaper Paper
British Broadsheet	375 × 597 mm	14.8 × 23.5 in	Newspaper Paper
South African Broadsheet	410 × 578 mm	16.1 × 22.8 in	Newspaper Paper
Ciner	350 × 500 mm	13.8 × 19.7 in	Newspaper Paper
Compact	280 × 430 mm	11 × 16.9 in	Newspaper Paper
Nordisch	400 × 570 mm	15.7 × 22.4 in	Newspaper Paper
Rhenish	350 × 520 mm	13.8 × 20.5 in	Newspaper Paper
Swiss	320 × 475 mm	12.6 × 18.7 in	Newspaper Paper
Tabloid	280 × 430 mm	11 × 16.9 in	Newspaper Paper
Canadian Tabloid	260 × 368 mm	10.2 × 14.5 in	Newspaper Paper
Norwegian Tabloid	280 × 400 mm	11 × 15.7 in	Newspaper Paper
New York Times	305 × 559 mm	12 × 22 in	Newspaper Paper
Wall Street Journal	305 × 578 mm	12 × 22.8 in	Newspaper Paper
Folio	304.8 × 482.6 mm	12 × 19 in	Book Paper
Quarto	241.3 × 304.8 mm	9.5 × 12 in	Book Paper
Imperial Octavo	209.55 × 292.1 mm	8.3 × 11.5 in	Book Paper
Super Octavo	177.8 × 279.4 mm	7 × 11 in	Book Paper
Royal Octavo	165.1 × 254 mm	6.5 × 10 in	Book Paper
Medium Octavo	165.1 × 234.95 mm	6.5 × 9.3 in	Book Paper
Octavo	152.4 × 228.6 mm	6 × 9 in	Book Paper
Crown Octavo	136.525 × 203.2 mm	5.4 × 8 in	Book Paper
12mo	127 × 187.325 mm	5 × 7.4 in	Book Paper
16mo	101.6 × 171.45 mm	4 × 6.8 in	Book Paper
18mo	101.6 × 165.1 mm	4 × 6.5 in	Book Paper
32mo	88.9 × 139.7 mm	3.5 × 5.5 in	Book Paper
48mo	63.5 × 101.6 mm	2.5 × 4 in	Book Paper
64mo	50.8 × 76.2 mm	2 × 3 in	Book Paper
A Format	110 × 178 mm	4.3 × 7 in	Book Paper
B Format	129 × 198 mm	5.1 × 7.8 in	Book Paper
C Format	135 × 216 mm	5.3 × 8.5 in	Book Paper
ISO 216	74 × 52 mm	2.9 × 2 in	Business Card Paper
US/Canada	88.9 × 50.8 mm	3.5 × 2 in	Business Card Paper
European	85 × 55 mm	3.3 × 2.2 in	Business Card Paper
Scandinavia	90 × 55 mm	3.5 × 2.2 in	Business Card Paper
China	90 × 54 mm	3.5 × 2.1 in	Business Card Paper
Japan	91 × 55 mm	3.6 × 2.2 in	Business Card Paper
Iran	85 × 48 mm	3.3 × 1.9 in	Business Card Paper
Hungary	90 × 50 mm	3.5 × 2 in	Business Card Paper
ISO 7810 ID-1	85.6 × 54 mm	3.4 × 2.1 in	Business Card Paper
RA0	860 × 1220 mm	33.9 × 48 in	Raw Paper
RA1	610 × 860 mm	24 × 33.9 in	Raw Paper
RA2	430 × 610 mm	16.9 × 24 in	Raw Paper
RA3	305 × 430 mm	12 × 16.9 in	Raw Paper
RA4	215 × 305 mm	8.5 × 12 in	Raw Paper
SRA0	900 × 1280 mm	35.4 × 50.4 in	Raw Paper
SRA1	640 × 900 mm	25.2 × 35.4 in	Raw Paper
SRA2	450 × 640 mm	17.7 × 25.2 in	Raw Paper
SRA3	320 × 450 mm	12.6 × 17.7 in	Raw Paper
SRA4	225 × 320 mm	8.9 × 12.6 in	Raw Paper
SRA1+	660 × 920 mm	26 × 36.2 in	Raw Paper
SRA2+	480 × 650 mm	18.9 × 25.6 in	Raw Paper
SRA3+	320 × 460 mm	12.6 × 18.1 in	Raw Paper
SRA3++	320 × 464 mm	12.6 × 18.3 in	Raw Paper
A0U	880 × 1230 mm	34.6 × 48.4 in	Raw Paper
A1U	625 × 880 mm	24.6 × 34.6 in	Raw Paper
A2U	450 × 625 mm	17.7 × 24.6 in	Raw Paper
A3U	330 × 450 mm	13 × 17.7 in	Raw Paper
A4U	240 × 330 mm	9.4 × 13 in	Raw Paper
1 Sheet	508 × 762 mm	20 × 30 in	Billboard Paper
2 Sheet	762 × 1016 mm	30 × 40 in	Billboard Paper
4 Sheet	1016 × 1524 mm	40 × 60 in	Billboard Paper
6 Sheet	1200 × 1800 mm	47.2 × 70.9 in	Billboard Paper
12 Sheet	3048 × 1524 mm	120 × 60 in	Billboard Paper
16 Sheet	2032 × 3048 mm	80 × 120 in	Billboard Paper
32 Sheet	4064 × 3048 mm	160 × 120 in	Billboard Paper
48 Sheet	6096 × 3048 mm	240 × 120 in	Billboard Paper
64 Sheet	8128 × 3048 mm	320 × 120 in	Billboard Paper
96 Sheet	12192 × 3048 mm	480 × 120 in	Billboard Paper
Cloche	300 × 400 mm	11.8 × 15.7 in	French Paper
Pot, écolier	310 × 400 mm	12.2 × 15.7 in	French Paper
Tellière	340 × 440 mm	13.4 × 17.3 in	French Paper
Couronne écriture	360 × 360 mm	14.2 × 14.2 in	French Paper
Couronne édition	370 × 470 mm	14.6 × 18.5 in	French Paper
Roberto	390 × 500 mm	15.4 × 19.7 in	French Paper
Écu	400 × 520 mm	15.7 × 20.5 in	French Paper
Coquille	440 × 560 mm	17.3 × 22 in	French Paper
Carré	450 × 560 mm	17.7 × 22 in	French Paper
Cavalier	460 × 620 mm	18.1 × 24.4 in	French Paper
Demi-raisin	325 × 500 mm	12.8 × 19.7 in	French Paper
Raisin	500 × 650 mm	19.7 × 25.6 in	French Paper
Double Raisin	650 × 1000 mm	25.6 × 39.4 in	French Paper
Jésus	560 × 760 mm	22 × 29.9 in	French Paper
Soleil	600 × 800 mm	23.6 × 31.5 in	French Paper
Colombier affiche	600 × 800 mm	23.6 × 31.5 in	French Paper
Colombier commercial	630 × 900 mm	24.8 × 35.4 in	French Paper
Petit Aigle	700 × 940 mm	27.6 × 37 in	French Paper
Grand Aigle	750 × 1050 mm	29.5 × 41.3 in	French Paper
Grand Monde	900 × 1260 mm	35.4 × 49.6 in	French Paper
Univers	1000 × 1130 mm	39.4 × 44.5 in	French Paper
JB0	1030 × 1456 mm	40.6 × 57.3 in	Japanese Paper
JB1	728 × 1030 mm	28.7 × 40.6 in	Japanese Paper
JB2	515 × 728 mm	20.3 × 28.7 in	Japanese Paper
JB3	364 × 515 mm	14.3 × 20.3 in	Japanese Paper
JB4	257 × 364 mm	10.1 × 14.3 in	Japanese Paper
JB5	182 × 257 mm	7.2 × 10.1 in	Japanese Paper
JB6	128 × 182 mm	5 × 7.2 in	Japanese Paper
JB7	91 × 128 mm	3.6 × 5 in	Japanese Paper
JB8	64 × 91 mm	2.5 × 3.6 in	Japanese Paper
JB9	45 × 64 mm	1.8 × 2.5 in	Japanese Paper
JB10	32 × 45 mm	1.3 × 1.8 in	Japanese Paper
JB11	22 × 32 mm	0.9 × 1.3 in	Japanese Paper
JB12	16 × 22 mm	0.6 × 0.9 in	Japanese Paper
Shiroku ban 4	264 × 379 mm	10.4 × 14.9 in	Japanese Paper
Shiroku ban 5	189 × 262 mm	7.4 × 10.3 in	Japanese Paper
Shiroku ban 6	127 × 188 mm	5 × 7.4 in	Japanese Paper
Kiku 4	227 × 306 mm	8.9 × 12 in	Japanese Paper
Kiku 5	151 × 227 mm	5.9 × 8.9 in	Japanese Paper
P1	560 × 860 mm	22 × 33.9 in	Canadian Paper
P2	430 × 560 mm	16.9 × 22 in	Canadian Paper
P3	280 × 430 mm	11 × 16.9 in	Canadian Paper
P4	215 × 280 mm	8.5 × 11 in	Canadian Paper
P5	140 × 215 mm	5.5 × 8.5 in	Canadian Paper
P6	107 × 140 mm	4.2 × 5.5 in	Canadian Paper
DIN D0	771 × 1090 mm	30.4 × 42.9 in	German Paper
DIN D1	545 × 771 mm	21.5 × 30.4 in	German Paper
DIN D2	385 × 545 mm	15.2 × 21.5 in	German Paper
DIN D3	272 × 385 mm	10.7 × 15.2 in	German Paper
DIN D4	192 × 272 mm	7.6 × 10.7 in	German Paper
DIN D5	136 × 192 mm	5.4 × 7.6 in	German Paper
DIN D6	96 × 136 mm	3.8 × 5.4 in	German Paper
DIN D7	68 × 96 mm	2.7 × 3.8 in	German Paper
DIN D8	48 × 68 mm	1.9 × 2.7 in	German Paper
SIS E0	878 × 1242 mm	34.6 × 48.9 in	German Paper
SIS E1	621 × 878 mm	24.4 × 34.6 in	German Paper
SIS E2	439 × 621 mm	17.3 × 24.4 in	German Paper
SIS E3	310 × 439 mm	12.2 × 17.3 in	German Paper
SIS E4	220 × 310 mm	8.7 × 12.2 in	German Paper
SIS E5	155 × 220 mm	6.1 × 8.7 in	German Paper
SIS E6	110 × 155 mm	4.3 × 6.1 in	German Paper
SIS E7	78 × 110 mm	3.1 × 4.3 in	German Paper
SIS E8	55 × 78 mm	2.2 × 3.1 in	German Paper
SIS E9	39 × 55 mm	1.5 × 2.2 in	German Paper
SIS E10	27 × 39 mm	1.1 × 1.5 in	German Paper
SIS F0	958 × 1354 mm	37.7 × 53.3 in	German Paper
SIS F1	677 × 958 mm	26.7 × 37.7 in	German Paper
SIS F2	479 × 677 mm	18.9 × 26.7 in	German Paper
SIS F3	339 × 479 mm	13.3 × 18.9 in	German Paper
SIS F4	239 × 339 mm	9.4 × 13.3 in	German Paper
SIS F5	169 × 239 mm	6.7 × 9.4 in	German Paper
SIS F6	120 × 169 mm	4.7 × 6.7 in	German Paper
SIS F7	85 × 120 mm	3.3 × 4.7 in	German Paper
SIS F8	60 × 85 mm	2.4 × 3.3 in	German Paper
SIS F9	42 × 60 mm	1.7 × 2.4 in	German Paper
SIS F10	30 × 42 mm	1.2 × 1.7 in	German Paper
SIS G0	1044 × 1477 mm	41.1 × 58.1 in	German Paper
SIS G1	738 × 1044 mm	29.1 × 41.1 in	German Paper
SIS G2	522 × 738 mm	20.6 × 29.1 in	German Paper
SIS G3	369 × 522 mm	14.5 × 20.6 in	German Paper
SIS G4	261 × 369 mm	10.3 × 14.5 in	German Paper
SIS G5	185 × 261 mm	7.3 × 10.3 in	German Paper
SIS G6	131 × 185 mm	5.2 × 7.3 in	German Paper
SIS G7	92 × 131 mm	3.6 × 5.2 in	German Paper
SIS G8	65 × 92 mm	2.6 × 3.6 in	German Paper
SIS G9	46 × 65 mm	1.8 × 2.6 in	German Paper
SIS G10	33 × 46 mm	1.3 × 1.8 in	German Paper
SIS D0	1091 × 1542 mm	43 × 60.7 in	German Paper
SIS D1	771 × 1091 mm	30.4 × 43 in	German Paper
SIS D2	545 × 771 mm	21.5 × 30.4 in	German Paper
SIS D3	386 × 545 mm	15.2 × 21.5 in	German Paper
SIS D4	273 × 386 mm	10.7 × 15.2 in	German Paper
SIS D5	193 × 273 mm	7.6 × 10.7 in	German Paper
SIS D6	136 × 193 mm	5.4 × 7.6 in	German Paper
SIS D7	96 × 136 mm	3.8 × 5.4 in	German Paper
SIS D8	68 × 96 mm	2.7 × 3.8 in	German Paper
SIS D9	48 × 68 mm	1.9 × 2.7 in	German Paper
SIS D10	34 × 48 mm	1.3 × 1.9 in	German Paper
B5	176 × 250 mm	6.9 × 9.8 in	German Paper
Carta	216 × 279 mm	8.5 × 11 in	Colombian Paper
Extra Tabloide	304 × 457.2 mm	12 × 18 in	Colombian Paper
Oficio	216 × 330 mm	8.5 × 13 in	Colombian Paper
1/8 pliego	250 × 350 mm	9.8 × 13.8 in	Colombian Paper
1/4 pliego	350 × 500 mm	13.8 × 19.7 in	Colombian Paper
1/2 pliego	500 × 700 mm	19.7 × 27.6 in	Colombian Paper
Pliego	700 × 1000 mm	27.6 × 39.4 in	Colombian Paper
D0	764 × 1064 mm	30.1 × 41.9 in	Chinese Paper
D1	532 × 760 mm	20.9 × 29.9 in	Chinese Paper
D2	380 × 528 mm	15 × 20.8 in	Chinese Paper
D3	264 × 376 mm	10.4 × 14.8 in	Chinese Paper
D4	188 × 260 mm	7.4 × 10.2 in	Chinese Paper
D5	130 × 184 mm	5.1 × 7.2 in	Chinese Paper
D6	92 × 126 mm	3.6 × 5 in	Chinese Paper
RD0	787 × 1092 mm	31 × 43 in	Chinese Paper
RD1	546 × 787 mm	21.5 × 31 in	Chinese Paper
RD2	393 × 546 mm	15.5 × 21.5 in	Chinese Paper
RD3	273 × 393 mm	10.7 × 15.5 in	Chinese Paper
RD4	196 × 273 mm	7.7 × 10.7 in	Chinese Paper
RD5	136 × 196 mm	5.4 × 7.7 in	Chinese Paper
RD6	98 × 136 mm	3.9 × 5.4 in	Chinese Paper
Antiquarian	787 × 1346 mm	31 × 53 in	Imperial Paper
Atlas	660 × 864 mm	26 × 34 in	Imperial Paper
Brief	343 × 406 mm	13.5 × 16 in	Imperial Paper
Broadsheet	457 × 610 mm	18 × 24 in	Imperial Paper
Cartridge	533 × 660 mm	21 × 26 in	Imperial Paper
Columbier	597 × 876 mm	23.5 × 34.5 in	Imperial Paper
Copy Draught	406 × 508 mm	16 × 20 in	Imperial Paper
Crown	381 × 508 mm	15 × 20 in	Imperial Paper
Demy	445 × 572 mm	17.5 × 22.5 in	Imperial Paper
Double Demy	572 × 902 mm	22.5 × 35.5 in	Imperial Paper
Quad Demy	889 × 1143 mm	35 × 45 in	Imperial Paper
Elephant	584 × 711 mm	23 × 28 in	Imperial Paper
Double Elephant	678 × 1016 mm	26.7 × 40 in	Imperial Paper
Emperor	1219 × 1829 mm	48 × 72 in	Imperial Paper
Foolscap	343 × 432 mm	13.5 × 17 in	Imperial Paper
Small Foolscap	337 × 419 mm	13.3 × 16.5 in	Imperial Paper
Grand Eagle	730 × 1067 mm	28.7 × 42 in	Imperial Paper
Imperial	559 × 762 mm	22 × 30 in	Imperial Paper
Medium	470 × 584 mm	18.5 × 23 in	Imperial Paper
Monarch	184 × 267 mm	7.2 × 10.5 in	Imperial Paper
Post	394 × 489 mm	15.5 × 19.3 in	Imperial Paper
Sheet, Half Post	495 × 597 mm	19.5 × 23.5 in	Imperial Paper
Pinched Post	375 × 470 mm	14.8 × 18.5 in	Imperial Paper
Large Post	394 × 508 mm	15.5 × 20 in	Imperial Paper
Double Large Post	533 × 838 mm	21 × 33 in	Imperial Paper
Double Post	483 × 762 mm	19 × 30 in	Imperial Paper
Pott	318 × 381 mm	12.5 × 15 in	Imperial Paper
Princess	546 × 711 mm	21.5 × 28 in	Imperial Paper
Quarto	229 × 279 mm	9 × 11 in	Imperial Paper
Royal	508 × 635 mm	20 × 25 in	Imperial Paper
Super Royal	483 × 686 mm	19 × 27 in	Imperial Paper
Dukes	140 × 178 mm	5.5 × 7 in	Traditional British Paper
Foolscap	203 × 330 mm	8 × 13 in	Traditional British Paper
Imperial	178 × 229 mm	7 × 9 in	Traditional British Paper
Kings	165 × 203 mm	6.5 × 8 in	Traditional British Paper
Quarto	203 × 254 mm	8 × 10 in	Traditional British Paper
PA0	840 × 1120 mm	33.1 × 44.1 in	Transitional Paper
PA1	560 × 840 mm	22 × 33.1 in	Transitional Paper
PA2	420 × 560 mm	16.5 × 22 in	Transitional Paper
PA3	280 × 420 mm	11 × 16.5 in	Transitional Paper
PA4	210 × 280 mm	8.3 × 11 in	Transitional Paper
PA5	140 × 210 mm	5.5 × 8.3 in	Transitional Paper
PA6	105 × 140 mm	4.1 × 5.5 in	Transitional Paper
PA7	70 × 105 mm	2.8 × 4.1 in	Transitional Paper
PA8	52 × 70 mm	2 × 2.8 in	Transitional Paper
PA9	35 × 52 mm	1.4 × 2 in	Transitional Paper
PA10	26 × 35 mm	1 × 1.4 in	Transitional Paper
F0	841 × 1321 mm	33.1 × 52 in	Transitional Paper
F1	660 × 841 mm	26 × 33.1 in	Transitional Paper
F2	420 × 660 mm	16.5 × 26 in	Transitional Paper
F3	330 × 420 mm	13 × 16.5 in	Transitional Paper
F4	210 × 330 mm	8.3 × 13 in	Transitional Paper
F5	165 × 210 mm	6.5 × 8.3 in	Transitional Paper
F6	105 × 165 mm	4.1 × 6.5 in	Transitional Paper
F7	82 × 105 mm	3.2 × 4.1 in	Transitional Paper
F8	52 × 82 mm	2 × 3.2 in	Transitional Paper
F9	41 × 52 mm	1.6 × 2 in	Transitional Paper
F10	26 × 41 mm	1 × 1.6 in	Transitional Paper
Writing Paper - Pott	* x * mm	12.5 x 15 in	Old English Paper
Writing Paper - Double Pott	* x * mm	15 x 25 in	Old English Paper
Writing Paper - Foolscap	* x * mm	13.25 x 16.5 in	Old English Paper
Writing Paper - Double Foolscap	* x * mm	16.5 x 26.5 in	Old English Paper
Writing Paper - Foolscap and Third	* x * mm	13.25 x 22 in	Old English Paper
Writing Paper - Foolscap and Half	* x * mm	13.25 x 24.75 in	Old English Paper
Writing Paper - Pinched Post	* x * mm	14.5 x 18.5 in	Old English Paper
Writing Paper - Post	* x * mm	15.25 x 19 in	Old English Paper
Writing Paper - Double Post	* x * mm	19 x 30.5 in	Old English Paper
Writing Paper - Large Post	* x * mm	16.5 x 20.75 in	Old English Paper
Writing Paper - Double Large Post	* x * mm	20.75 x 33 in	Old English Paper
Writing Paper - Copy	* x * mm	16.25 x 20 in	Old English Paper
Writing Paper - Medium	* x * mm	18 x 22.5 in	Old English Paper
Cut Writing Paper - Albert	* x * mm	6 x 4 in	Old English Paper
Cut Writing Paper - Duke	* x * mm	7 x 5.5 in	Old English Paper
Cut Writing Paper - Duchess	* x * mm	6 x 4.5 in	Old English Paper
Cut Writing Paper - Foolscap Folio	* x * mm	13 x 8 in	Old English Paper
Cut Writing Paper - Foolscap 4 quarto	* x * mm	8 x 6.5 in	Old English Paper
Cut Writing Paper - Large Post 4 quarto	* x * mm	10 x 8 in	Old English Paper
Cut Writing Paper - Large Post 8 octavo	* x * mm	8 x 5 in	Old English Paper
Cut Writing Paper - Small Post 4 quarto	* x * mm	9 x 7 in	Old English Paper
Cut Writing Paper - Small Post 8 octavo	* x * mm	7 x 4.5 in	Old English Paper
Book and Drawing Paper - Foolscap	* x * mm	14 x 18.75 in	Old English Paper
Book and Drawing Paper - Demy	* x * mm	15.5 x 20 in	Old English Paper
Book and Drawing Paper - Medium	* x * mm	17.5 x 22.5 in	Old English Paper
Book and Drawing Paper - Royal	* x * mm	19 x 24 in	Old English Paper
Book and Drawing Paper - Super Royal	* x * mm	19.25 x 27 in	Old English Paper
Book and Drawing Paper - Imperial	* x * mm	22 x 30.25 in	Old English Paper
Book and Drawing Paper - Elephant	* x * mm	23 x 28 in	Old English Paper
Book and Drawing Paper - Double Elephant	* x * mm	26.5 x 40 in	Old English Paper
Book and Drawing Paper - Atlas	* x * mm	26.25 x 34 in	Old English Paper
Book and Drawing Paper - Columbier	* x * mm	23.5 x 35 in	Old English Paper
Book and Drawing Paper - Antiquarian	* x * mm	31 x 53 in	Old English Paper
Printing Paper - Crown	* x * mm	16.25 x 21 in	Old English Paper
Printing Paper - Demy	* x * mm	17.75 x 22.5 in	Old English Paper
Printing Paper - Medium	* x * mm	18.25 x 23 in	Old English Paper
Printing Paper - Royal	* x * mm	20 x 25 in	Old English Paper
Printing Paper - Super Royal	* x * mm	21 x 27 in	Old English Paper
Printing Paper - Double Pott	* x * mm	15 x 25 in	Old English Paper
Printing Paper - Double Foolscap	* x * mm	17 x 27 in	Old English Paper
Printing Paper - Double Crown	* x * mm	20 x 30 in	Old English Paper
Printing Paper - Double Demy	* x * mm	22.5 x 35.5 in	Old English Paper
Cartridge Paper - Foolscap	* x * mm	14 x 18.75 in	Old English Paper
Cartridge Paper - Demy	* x * mm	17.75 x 22.5 in	Old English Paper
Cartridge Paper - Royal	* x * mm	19 x 24 in	Old English Paper
Cartridge Paper - Super Royal	* x * mm	19.25 x 27.5 in	Old English Paper
Cartridge Paper - Imperial	* x * mm	21 x 26 in	Old English Paper
Cartridge Paper - Elephant	* x * mm	23 x 28 in	Old English Paper
Board - Royal	* x * mm	20 x 25 in	Old English Paper
Board - Postal	* x * mm	22.5 x 28.5 in	Old English Paper
Board - Imperial	* x * mm	22 x 30 in	Old English Paper
Board - Large Imperial	* x * mm	22 x 32 in	Old English Paper
Board - Index	* x * mm	25.5 x 30.5 in	Old English Paper
EOT;

	$ary = [];
	$ary[] = array( "Type", "Name", "Width-mm", "Height-mm", "Width-in", "Height-in",
		"Width-MM2IN", "Height-MM2IN", "Width-IN2MM", "Height-IN2MM",
		"Ratio-MM", "Ratio-IN" );
#
#	Split everything up.
#
	$a = exptrim( "\n", $a );
#
#	Now we need to sort everything
#
	sort( $a );

	foreach( $a as $k=>$v ){
#
#	Get a line
#
		$v = str_replace( "", "", $v );
#
#	If this is the title line - skip it
#
		if( preg_match("/size/i", $v) ){ continue; }
#
#	Split up the incoming line and get the count of it
#
		$b = exptrim( "	", $v );
		if( count($b) < 4 ){ $b[] = "Unknown"; }

		$bc = count( $b );
#
#	Get the TYPE of paper this is
#
		$type = $b[3];
#
#	Get the name of the paper
#
		$name = $b[0];
#
#	Split up the first group of information and get the count of it
#
		$c = exptrim( ' ', $b[1] );
		$cc = count( $c );
		$mm2in = [];
		$in2mm = [];
#
#	Split up the second group of information and get the count of it
#
		$d = exptrim( ' ', $b[2] );
		$dc = count( $d );

		if( $c[0] == '*' ){
			$c[0] = $d[0] * 25.4;
			$c[2] = $d[2] * 25.4;
			}
#
#	Adjust the arrays so they are even
#
		while( $cc < $dc ){ $c[] = ""; $cc = count($c); }
		while( $dc < $cc ){ $d[] = ""; $dc = count($d); }
#
#	If the first line is for inches - exchange it with the millimeter one
#	Remember: Millimeters, Inches, MM2IN
#
		if( preg_match("/ in/i", $b[1]) ){
			for( $i=0; $i<$cc; $i++ ){
				$j = $c[$i]; $c[$i] = $d[$i]; $d[$i] = $j;
				}
			}
#
#	Do the Millimeter to Inches conversion.
#
		for( $i=0; $i<$cc; $i++ ){ $mm2in[$i] = $c[$i]; }
		if( is_numeric($mm2in[0]) ){ $mm2in[0] = $mm2in[0] / 25.4; }
		if( is_numeric($mm2in[2]) ){ $mm2in[2] = $mm2in[2] / 25.4; }
#
#	Now do the inches to millimeter conversion
#
		for( $i=0; $i<$cc; $i++ ){ $in2mm[$i] = $d[$i]; }
		if( is_numeric($in2mm[0]) ){ $in2mm[0] = $in2mm[0] * 25.4; }
		if( is_numeric($in2mm[2]) ){ $in2mm[2] = $in2mm[2] * 25.4; }
#
#	Now do the ratios
#
		$ratio_mm = $c[0] / $c[2];
		$ratio_in = $d[0] / $d[2];
#
#	Now remove the 'x's
#
		unset( $c[1] );
		unset( $d[1] );
		unset( $mm2in[1] );
		unset( $in2mm[1] );
#
#	Now remove the mm/in
#
		unset( $c[3] );
		unset( $d[3] );
		unset( $mm2in[3] );
		unset( $in2mm[3] );

		echo "Type:$type\n";
		echo "Name:$name\n";
		echo "Metric Measurements: ";
		foreach( $c as $k1=>$v1 ){ echo "$v1, "; }
		echo "\n";
		echo "Inch Measurements: ";
		foreach( $d as $k1=>$v1 ){ echo "$v1, "; }
		echo "\n";
		echo "Metric Converted to Inches Measurements: ";
		foreach( $mm2in as $k1=>$v1 ){ echo "$v1, "; }
		echo "\n";
		echo "Inches Converted to Millimeters Measurements: ";
		foreach( $in2mm as $k1=>$v1 ){ echo "$v1, "; }
		echo "\n";
		echo str_repeat( "-", 80 ) . "\n";

		$ary[] = array( $type, $name, $c[0], $c[2], $d[0], $d[2],
			$mm2in[0], $mm2in[2], $in2mm[0], $in2mm[2], $ratio_mm, $ratio_in );
		}
#
#	Sort the array
#
	$keep = array_shift( $ary );
	sort( $ary );
	array_unshift( $ary, $keep );
#
#	Write everything out to a file via PHP's CSV command.
#
	if( ($fp = fopen( $file, "w" )) !== FALSE ){
		foreach( $ary as $k=>$v ){
			if( is_null($v) ){ continue; }
			fputcsv( $fp, $v, "," );
			}
		}
		else { echo "Could not write to $file\n"; }

	fclose( $fp );

	echo "Finished\n";
	exit();
################################################################################
#	exptrim(). Expand and trim the incoming string. CHR is the character to use
#		when expanding the string (a).
################################################################################
function exptrim( $chr, $a )
{
	$b = explode( $chr, $a );
	foreach( $b as $k=>$v ){
		$b[$k] = trim( $v );
		}

	if( $b[count($b)-1] == "m" ){
		$b[0] = $b[0] * 1000;
		$b[2] = $b[2] * 1000;
		$b[count($b)-1] = "mm";
		}

	return $b;
}
?>

