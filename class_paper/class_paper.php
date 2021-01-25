<?php

	include_once( "../class_debug.php" );
################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_paper();
#
#-Description:
#
#	A class that I can call to get paper size information. Feel free to add
#	new paper sizes. All information about paper sizes was gotten from
#	the internet. One in particular website:
#
#		https://www.papersizes.org/
#
#	Was most helpful. I checked their information with other websites
#	including governmental ones. They were all the same so just quoting
#	this one site becuase it had everything in it.
#
#	No claim of Copyright, Patent, or any othe4r kind of mark is made
#	on my behalf. I just compiled everything into this class so all paper
#	sizes can now be easily found and used.
#
#	Note that the paper sizes are either in millimeters or inches (or - in
#	some cases - a different measurement). If a measurement is in something
#	other than these two - you will have to do the math. There are two functioons
#	herein though that will convert any measurement to either inches or millimeters.
#	I suggest you use them.
#
#	Note that I made it so you can add new paper sizes if you find any - BUT!
#	Be sure to check first for the name. I use VIM and have it set so it
#	ignores case. So "mm" or "Mm" or "mM" or "MM" are all the same. What I am
#	saying here is - be sure to check for UPPER-lower case names. Ok - so you
#	can add new page/paper sizes. Be sure to ensure that you put your information
#	into the right location. So please do not mix and match low resolution with
#	high resolution information or put the size of the information in with one
#	of the other tables and please do NOT mix and match the resolution tables
#	with other tables.
#
#	Note also - I put in the "mm" and "in" into the first table. In this way it
#	makes no difference if millimeters are first or inches (or feet, yards, kilometers,
#	or what have you). The program is fairly self explanatory. Use your head.
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
#	Mark Manning			Simulacron I			Sat 06/15/2019 14:16:52.63 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 06/15/2019 14:17:38.11 
#	---------------------------------------------------------------------------
#		These classes are now under the GNU Greater License.  Any and all works
#		whether derivatives or extensios should be sent back to markem@sim1.us as
#		per the GNU Greater License.  In this way, anything that makes these
#		routines better can be incorporated into them for the greater
#		good of mankind.  All additions and who made them should be noted here
#		in this file OR in a separate file to be called the HISTORY.DAT file
#		since, at some point in the future, this list will get to be too big
#		to store within the class itself.  See the GNU license file for details
#		on the GNU license.  If you do not agree with the license - then do NOT
#		use these routines in any way, shape, or form.  Failure to do so or using
#		these routines in whole or in part - constitutes a violation of the GNU
#		licensing terms and can and will result in prosecution under the law.
#
#	Legal Statement follows:
#
#		class_color. A PHP class to handle color.
#		Copyright (C) 2001.  Mark Manning
#
#		This program is free software: you can redistribute it
#		and/or modify it under the terms of the MIT License.
#
#		This program is distributed in the hope that it will be useful,
#		but WITHOUT ANY WARRANTY; without even the implied warranty of
#		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#END DOC
################################################################################
class class_paper
{
	public $debug = false;
	private	$papers = [];

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
#	__construct(). Creates the paper tables. These are done here because in the
#		future there may be NEW papers (like from the Andromdea Galaxy) or
#		there might be changes to these sizes. Thus, the tables are put herein
#		so you can change them easily.
################################################################################
function __construct( $debug=false )
{
	$this->init( func_get_args() );
}
################################################################################
#	init(). Moved __construct() to here so it can be called outside of the
#		program to re-initialize everything.
#
#	Notes: s = standard, l = low, h = high, a_# = additional paper info
#		All letters are lowercase.
################################################################################
function init()
{
	$args = func_get_args();
	$this->debug = new class_debug( func_get_args() );
	$this->debug->in();
	$this->debug->out();

	$this->papers = array();
#
#	Original Width x Height table
#
#	Note that the first line is NOT the title line. It is just thrown out.
#	The names going down the left are where they go. Don't muck it up!
#	The "mm" and "in" are needed because THAT is where they go. Do a dump
#	and see what I mean.
#
	$a = <<<EOT
Size	Width x Height (mm)	Width x Height (in)
4A0	1682 x 2378 mm	66.2 x 93.6 in
2A0	1189 x 1682 mm	46.8 x 66.2 in
A0	841 x 1189 mm	33.1 x 46.8 in
A1	594 x 841 mm	23.4 x 33.1 in
A2	420 x 594 mm	16.5 x 23.4 in
A3	297 x 420 mm	11.7 x 16.5 in
A4	210 x 297 mm	8.3 x 11.7 in
A5	148 x 210 mm	5.8 x 8.3 in
A6	105 x 148 mm	4.1 x 5.8 in
A7	74 x 105 mm	2.9 x 4.1 in
A8	52 x 74 mm	2.0 x 2.9 in
A9	37 x 52 mm	1.5 x 2.0 in
A10	26 x 37 mm	1.0 x 1.5 in
B0	1000 x 1414 mm	39.4 x 55.7 in
B1	707 x 1000 mm	27.8 x 39.4 in
B2	500 x 707 mm	19.7 x 27.8 in
B3	353 x 500 mm	13.9 x 19.7 in
B4	250 x 353 mm	9.8 x 13.9 in
B5	176 x 250 mm	6.9 x 9.8 in
B6	125 x 176 mm	4.9 x 6.9 in
B7	88 x 125 mm	3.5 x 4.9 in
B8	62 x 88 mm	2.4 x 3.5 in
B9	44 x 62 mm	1.7 x 2.4 in
B10	31 x 44 mm	1.2 x 1.7 in
B1XL	750 x 1050 mm	29.5 x 41.3 in
B2+	530 x 750 mm	20.9 x 29.5 in
RB0	1025 x 1449 mm	40.4 x 57.0 in
RB1	725 x 1025 mm	28.5 x 40.4 in
RB2	513 x 725 mm	20.2 x 28.5 in
RB3	363 x 513 mm	14.3 x 20.2 in
RB4	257 x 363 mm	10.1 x 14.3 in
SRB0	1072 x 1516 mm	42.2 x 59.9 in
SRB1	758 x 1072 mm	29.8 x 42.2 in
SRB2	536 x 758 mm	21.1 x 29.8 in
SRB3	379 x 536 mm	14.9 x 21.1 in
SRB4	268 x 379 mm	10.6 x 14.9 in
C0	917 x 1297 mm	36.1 x 51.5 in
C1	648 x 917 mm	25.5 x 36.1 in
C2	458 x 648 mm	18.0 x 25.5 in
C3	324 x 458 mm	12.8 x 18.0 in
C4	229 x 324 mm	9.0 x 12.8 in
C5	162 x 229 mm	6.4 x 9.0 in
C6	114 x 162 mm	4.5 x 6.4 in
C7	81 x 114 mm	3.2 x 4.5 in
C8	57 x 81 mm	2.2 x 3.2 in
C9	40 x 57 mm	1.6 x 2.2 in
C10	28 x 40 mm	1.1 x 1.6 in
RA0	860 x 1220 mm	33.9 x 48.0 in
RA1	610 x 860 mm	24.0 x 33.9 in
RA2	430 x 610 mm	16.9 x 24.0 in
RA3	305 x 430 mm	12.0 x 16.9 in
RA4	215 x 305 mm	8.5 x 12.0 in
SRA0	900 x 1280 mm	35.4 x 50.4 in
SRA1	640 x 900 mm	25.2 x 35.4 in
SRA2	450 x 640 mm	17.7 x 25.2 in
SRA3	320 x 450 mm	12.6 x 17.7 in
SRA4	225 x 320 mm	8.9 x 12.6 in
SRA1+	660 x 920 mm	26.0 x 36.2 in
SRA2+	480 x 650 mm	18.9 x 25.6 in
SRA3+	320 x 460 mm	12.6 x 18.1 in
SRA3++	320 x 464 mm	12.6 x 18.3 in
Half_Letter	140 x 216 mm	5.5 x 8.5 in	1:1.5455
Letter	216 x 279 mm	8.5 x 11.0 in	1:1.2941
Legal	216 x 356 mm	8.5 x 14.0 in	1:1.6471
Junior_Legal	127 x 203 mm	5.0 x 8.0 in	1:1.6000
Ledger	279 x 432 mm	11.0 x 17.0 in	1:1.5455
Tabloid	279 x 432 mm	11.0 x 17.0 in	1:1.5455
A	216 x 279 mm	8.5 x 11.0 in	1:1.2941	A4
B	279 x 432 mm	11.0 x 17.0 in	1:1.5455	A3
C	432 x 559 mm	17.0 x 22.0 in	1:1.2941	A2
D	559 x 864 mm	22.0 x 34.0 in	1:1.5455	A1
E	864 x 1118 mm	34.0 x 44.0 in	1:1.2941	A0
Arch_A	229 x 305 mm	9.0 x 12.0 in	4:3
Arch_B	305 x 457 mm	12.0 x 18.0 in	3:2
Arch_C	457 x 610 mm	18.0 x 24.0 in	4:3
Arch_D	610 x 914 mm	24.0 x 36.0 in	3:2
Arch_E	914 x 1219 mm	36.0 x 48.0 in	4:3
Arch_E1	762 x 1067 mm	30.0 x 42.0 in	7:5
6_1/4	152.4 x 88.9 mm	6.0 x 3.5 in
6_3/4	165.1 x 92.1 mm	6.5 x 3.625 in
7	171.5 x 95.3 mm	6.75 x 3.75 in
7_3/4	190.5 x 98.4 mm	7.5 x 3.875 in
Monarch	190.5 x 98.4 mm	7.5 x 3.875 in
8_5/8	219.1 x 92.1 mm	8.625 x 3.625 in
9	225.4 x 98.4 mm	8.875 x 3.875 in
10	241.3 x 104.8 mm	9.5 x 4.125 in
11	263.5 x 114.3 mm	10.375 x 4.5 in
12	279.4 x 120.7 mm	11.0 x 4.75 in
14	292.1 x 127.0 mm	11.5 x 5.0 in
16	304.8 x 152.4 mm	12.0 x 6.0 in
Envelope_A1	92.1 x 130.2 mm	3.625 x 5.125 in
Envelope_A2	146.1 x 111.1 mm 5.75 x 4.375 in
Envelope_Lady	146.1 x 111.1 mm Grey	5.75 x 4.375 in
Envelope_A4	158.7 x 108.0 mm 	6.25 x 4.25 in
Envelope_A6	165.1 x 120.7 mm 6.5 x 4.75 in
Envelope_Thompson's_Standard	165.1 x 120.7 mm	6.5 x 4.75 in
Envelope_A7	184.2 x 133.4 mm 7.25 x 5.25 in
Envelope_Besselheim	184.2 x 133.4 mm	7.25 x 5.25 in
Envelope_A8	206.4 x 139.7 mm 8.125 x 5.5 in
Envelope_Carr's	206.4 x 139.7 mm	8.125 x 5.5 in
Envelope_A9	222.3 x 146.1 mm 8.75 x 5.75 in
Envelope_Diplomat	222.3 x 146.1 mm	8.75 x 5.75 in
Envelope_A10	241.3 x 152.4 mm 9.5 x 6.0 in
Envelope_Willow	241.3 x 152.4 mm	9.5 x 6.0 in
Envelope_A_Long	225.4 x 98.4 mm	8.875 x 3.875 in
Envelope_1	228.6 x 152.4 mm	9.0 x 6.0 in
Envelope_1_3/4	241.3 x 165.1 mm	9.5 x 6.5 in
Envelope_3	254.0 x 177.8 mm	10.0 x 7.0 in
Envelope_6	1266.7 x 190.5 mm	10.5 x 7.5 in
Envelope_8	285.8 x 209.6 mm	11.25 x 8.25 in
Envelope_9_3/4	285.8 x 222.3 mm	11.25 x 8.75 in
Envelope_10_1/2	304.8 x 228.6 mm	12.0 x 9.0 in
Envelope_12_1/2	317.5 x 241.3 mm	12.5 x 9.5 in
Envelope_13_1/2	330.2 x 254.0 mm	13.0 x 10.0 in
Envelope_14_1/2	368.3 x 292.1 mm	14.5 x 11.5 in
Envelope_15 381.0 x 254.0 mm	15.0 x 10.0	in
Envelope_15_1/2	393.7 x 304.8 mm	15.5 x 12.0 in
Candian_P1	560 x 860 mm	22.0 x 33.9 in	1:1.5357	D
Candian_P2	430 x 560 mm	16.9 x 22.0 in	1:1.3023	C
Candian_P3	280 x 430 mm	11.0 x 16.9 in	1:1.5357	B
Candian_P4	215 x 280 mm	8.5 x 11.0 in	1:1.3023	A
Candian_P5	140 x 210 mm	5.5 x 8.3 in	1:1.5	-
Candian_P6	105 x 140 mm	4.1 x 5.5 in	1:1.3333	-
Japanese_B0	1030 x 1456 mm	40.6 x 57.3 in
Japanese_B1	728 x 1030 mm	28.7 x 40.6 in
Japanese_B2	515 x 728 mm	20.3 x 28.7 in
Japanese_B3	364 x 515 mm	14.3 x 20.3 in
Japanese_B4	257 x 364 mm	10.1 x 14.3 in
Japanese_B5	182 x 257 mm	7.2 x 10.1 in
Japanese_B6	128 x 182 mm	5.0 x 7.2 in
Japanese_B7	91 x 128 mm	3.6 x 5.0 in
Japanese_B8	64 x 91 mm	2.5 x 3.6 in
Japanese_B9	45 x 64 mm	1.8 x 2.5 in
Japanese_B10	32 x 45 mm	1.3 x 1.8 in
Shirokuban_1	264 x 379 mm 	10.39 x 14.92 in 	1:1.4356
Shirokuban_2	189 x 262 mm 	7.44 x 10.31 in 	1:1.3862
Shirokuban_3	127 x 188 mm 	5.00 x 7.40 in 	1:1.4803
Kiku_1	227 x 306 mm 	8.94 x 12.05 in 	1:1.3480
Kiku_2	151 x 227 mm 	5.94 x 8.94 in 	1:1.5033
Japanese_AB 	210 x 257 mm 	8.27 x 10.12 in 	1:1.2238
Japanese_B40 	103 x 182 mm 	4.06 x 7.17 in 	1:1.7670
Japanese_Shikisen 	84 x 148 mm 	3.31 x 5.83 in 	1:1.7619
Bond	558.8 x 431.8 mm	22.0 x 17.0 in
Book	965.2 x 635.0 mm	38.0 x 25.0 in
Cover	660.4 x 508.0 mm	26.0 x 20.0 in
Index	774.7 x 647.7 mm	30.5 x 25.5 in
Newsprint	914.4 x 609.6 mm	36.0 x 24.0 in
Offset	965.2 x 635.0 mm	38.0 x 25.0 in
Text	965.2 x 635.0 mm	38.0 x 25.0 in
Tissue	914.4 x 609.6 mm	36.0 x 24.0 in
Broadsheet	600 x 750 mm	23.5 x 29.5 in
Berliner	315 x 470 mm	12.4 x 18.5 in
Midi	315 x 470 mm	12.4 x 18.5 in
Tabloid_Size	280 x 430 mm	11.0 x 16.9 in
Postcard_Maximum 	235 x 120 mm 	9.25 x 4.72 in
Postcard_Minimum 	140 x 90 mm 	5.51 x 3.54 in
US_PostCard_Maximum 	6.0 x 4.25 in 	152.4 x 107.9 mm 	0.016 in 	0.406 mm
US_PostCard_Minimum 	5.0 x 3.5 in 	127.0 x 88.9 mm 	0.007 in 	0.178 mm
Court_Cards	4.75 x 3.5 in	120.65 x 88.9 mm
British_Maximum	5.5 x 3.5 in	139.7 x 88.9 mm
British_Minimum	3.25 x 3.25 in	82.55 x 82.55 mm
British_Minimum_1906	4.0 x 2.75 in	101.6 x 69.85 mm
British_Maximum_1925	5.875 x 4.125 in	149.225 x 104.775 mm
ISO_Poster_2A0	1189 x 1682 mm	46.8 x 66.2 in
ISO_Poster_A0	841 x 1189 mm	33.1 x 46.8 in
ISO_Poster_A1	594 x 841 mm	23.4 x 33.1 in
ISO_Poster_A2	420 x 594 mm	16.5 x 23.4 in
ISO_Poster_A3	297 x 420 mm	11.7 x 16.5 in
ISO_Poster_A4	210 x 297 mm	8.3 x 11.7 in
British_Poster_1_Sheet	508 x 762 mm	20 x 30 in
British_Poster_2_Sheet	762 x 1016 mm	30 x 40 in
British_Poster_4_Sheet	1016 x 1524 mm	40 x 60 in
UK_Movie_Poster_Cards	203.2 x 254.0 mm	8 x 10 in
UK_Movie_Poster_Double_Crown	508 x 762 mm	20 x 30 in
UK_Movie_Poster_One_Sheet	685.8 x 1016 mm	27 x 40 in
UK_Movie_Poster_Quad	762 x 1016 mm	30 x 40 in
UK_Movie_Poster_Three_Sheet	1016 x 2057.4 mm	40 x 81 in
UK_Movie_Poster_Six_Sheet	2032 x 2057.4 mm	80 x 81 in
US_Poster_Letter	215.9 x 279.4 mm	8.5 x 11 in
US_Poster_Small	279.4 x 431.8 mm	11 x 17 in
US_Poster_Medium	457.2 x 609.6 mm	18 x 24 in
US_Poster_Large	609.6 x 914.4 mm	24 x 36 in
US_Movie_Poster_Lobby_Card	279.4 x 355.6 mm	11 x 14 in
US_Movie_Poster_Window_Card	355.6 x 558.8 mm	14 x 22 in
US_Movie_Poster_Insert	355.6 x 914.4 mm	14 x 36 in
US_Movie_Poster_Half_Sheet	558.8 x 711.2 mm	22 x 28 in
US_Movie_Poster_One_Sheet	685.8 x 1016 mm	27 x 40 in
US_Movie_Poster_Three_Sheet	1041.4 x 2057.4 mm	41 x 81 in
US_Movie_Poster_Six_Sheet	2057.4 x 2057.4 mm	81 x 81 in
US_Movie_Poster_30_x_40_Drive_In	762 x 1016 mm	30 x 40 in
US_Movie_Poster_40_x_60_Drive_In	1016 x 1524 mm	40 x 60 in
US_Movie_Poster_Door_Panels	508 x 1524 mm	20 x 60 in
French_Movie_Poster_Petite	400 x 600 mm	15.7 x 23.6 in
French_Movie_Poster_Moyenne	600 x 800 mm	23.6 x 31.5 in
French_Movie_Poster_Pantalon	600 x 1600 mm	23.6 x 63.0 in
French_Movie_Poster_Demi-Grande	800 x 1200 mm	31.5 x 47.2 in
French_Movie_Poster_Grande	1200 x 1600 mm	47.2 x 63.0 in
French_Movie_Poster_Double_Grande	1600 x 2400 mm	63.0 x 94.5 in
Italian_Movie_Poster_Un_Foglio	700 x 1000 mm	27.6 x 39.4 in
Italian_Movie_Poster_Due_Fogli	1000 x 1400 mm	39.4 x 55.1 in
Italian_Movie_Poster_Quattro_Fogli	1400 x 2000 mm	55.1 x 78.7 in
Italian_Movie_Poster_Locandina	330 x 700 mm	13.0 x 27.6 in
Italian_Movie_Poster_Photobusta	500 x 700 mm	19.7 x 27.6 in
Australian_Movie_Poster_Lobby_Card	279.4 x 355.6 mm	11 x 14 in
Australian_Movie_Poster_Daybill	660.4 x 762 mm	26 x 30 in
Australian_Movie_Poster_One_Sheet	685.8 x 1016 mm	27 x 40 in
Australian_Movie_Poster_Three_Sheet	1041.4 x 2057.4 mm	41 x 81 in
UK_Billboard_4_Sheet	1.02 x 1.52 m	40 x 60 in
UK_Billboard_6_Sheet	1.20 x 1.80 m	47.24 x 70.87 in
UK_Billboard_12_Sheet	3.05 x 1.52 m	120 x 60 in
UK_Billboard_16_Sheet	2.03 x 3.05 m	80 x 120 in
UK_Billboard_32_Sheet	4.06 x 3.05 m	160 x 120 in
UK_Billboard_48_Sheet	6.10 x 3.05 m	240 x 120 in
UK_Billboard_64_Sheet	8.13 x 3.05 m	320 x 120 in
UK_Billboard_96_Sheet	12.19 x 3.05 m	480 x 120 in
US_Billboard_8_Sheet	3.35 x 1.52 m	132 x 60 in
US_Billboard_30_Sheet	6.91 x 3.17 m	272 x 125 in
US_Billboard_12_x_6_ft	3.66 x 1.83 m	144 x 72 in
US_Billboard_12_x_8_ft	3.66 x 2.44 m	144 x 96 in
US_Billboard_22_x_10_ft	3.66 x 1.83 m	264 x 120 in
US_Billboard_24_x_10_ft	3.66 x 2.44 m	288 x 120 in
US_Billboard_25_x_12_ft	7.62 x 3.66 m	300 x 144 in
US_Billboard_36_x_10.5_ft	10.97 x 3.20 m	432 x 126 in
US_Billboard_40_x_12_ft	12.19 x 3.66 m	480 x 144 in
US_Billboard_48_x_14_ft	14.63 x 4.27 m	576 x 168 in
US_Billboard_50_x_20_ft	15.24 x 6.10 m	600 x 240 in
US_Billboard_60_x_16_ft	18.29 x 4.88 m	720 x 192 in
French_Billboard_Abribus 2m2	1.756 x 1.191 m	69.1 x 46.9 in
French_Billboard_12m2	4.00 x 3.00 m	157.5 x 118.1 in
German_Billboard_City_Star	3.56 x 2.52 m	140.2 x 99.2 in
German_Billboard_Superpostern	5.26 x 3.72 m	207.1 x 146.5 in
Plakatwand_City_Star	3.56 x 2.52 m	140.2 x 99.2 in
Plakatwand_Superpostern	5.26 x 3.72 m	207.1 x 146.5 in
Austrian_Billboard_Brandboard	1.50 x 2.00 m	59.1 x 78.7 in
Austrian_Billboard_Dachflache	10.00 x 2.00 m	393.7 x 78.7 in
Austrian_Billboard_Megaboard	8.00 x 5.00 m	315.0 x 196.9 in
Austrian_Billboard_Centerboard	10.00 x 4.78 m	393.7 x 188.2 in
Plakatwand_Brandboard	1.50 x 2.00 m	59.1 x 78.7 in
Plakatwand_Dachflache	10.00 x 2.00 m	393.7 x 78.7 in
Plakatwand_Megaboard	8.00 x 5.00 m	315.0 x 196.9 in
Plakatwand_Centerboard	10.00 x 4.78 m	393.7 x 188.2 in
Netherlands_Billboard	3.30 x 2.40 m 129.9 x 94.5 in;
Reclamebord	3.30 x 2.40 m 129.9 x 94.5 in;
British_Imperial_Cut_Writing_Paper_Albert 	4.0 x 6.0 in 	101.6 x 152.4 mm 	1:1.5
British_Imperial_Cut_Writing_Paper_Duchess 	4.5 x 6.0 in 	114.3 x 152.4 mm 	1:1.3333
British_Imperial_Cut_Writing_Paper_Duke 	5.5 x 7.0 in 	139.7 x 177.8 mm 	1:1.2727
British_Imperial_Cut_Writing_Paper_Foolscap_Quarto 	6.5 x 8.0 in 	152.4 x 203.2 mm 	1:1.2308
British_Imperial_Cut_Writing_Paper_Foolscap_Folio 	8.0 x 13.0 in 	203.2 x 330.2 mm 	1:1.625
British_Imperial_Cut_Writing_Paper_Small_Post_Octavo 	4.5 x 7.0 in 	114.3 x 177.8 mm 	1:1.5556
British_Imperial_Cut_Writing_Paper_Small_Post_Quarto 	7.0 x 9.0 in 	177.8 x 228.6 mm 	1:1.2857
British_Imperial_Cut_Writing_Paper_Large_Post_Octavo 	5.0 x 8.0 in 	127.0 x 203.2 mm 	1:1.6
British_Imperial_Cut_Writing_Paper_Large_Post_Quarto 	8.0 x 10.0 in 	203.2 x 254.0 mm 	1:1.25
British_Imperial_Uncut_Writing_Paper_Pott 	12.5 x 15.0 in 	317.5 x 381.0 mm 	1:1.2
British_Imperial_Uncut_Writing_Paper_Double_Pott 	15.0 x 25.0 in 	381.0 x 635.0 mm 	1:1.6667
British_Imperial_Uncut_Writing_Paper_Foolscap 	13.25 x 16.5 in 	336.6 x 419.1 mm 	1:1.2453
British_Imperial_Uncut_Writing_Paper_Double_Foolscap 	16.5 x 26.5 in 	419.1 x 673.1 mm 	1:1.6061
British_Imperial_Uncut_Writing_Paper_Foolscap_and_Third 	13.25 x 22.0 in 	336.6 x 558.8 mm 	1:1.6604
British_Imperial_Uncut_Writing_Paper_Foolscap_and_Half 	13.25 x 24.75 in 	336.6 x 628.7 mm 	1:1.8679
British_Imperial_Uncut_Writing_Paper_Pinched_Post 	14.5 x 18.5 in 	368.3 x 469.9 mm 	1:1.2759
British_Imperial_Uncut_Writing_Paper_Post 	15.25 x 19.0 in 	387.4 x 482.6 mm 	1:1.2459
British_Imperial_Uncut_Writing_Paper_Double_Post 	19.0 x 30.5 in 	482.6 x 774.7 mm 	1:1.6053
British_Imperial_Uncut_Writing_Paper_Large_Post 	16.5 x 20.75 in 	419.1 x 527.1 mm 	1:1.2576
British_Imperial_Uncut_Writing_Paper_Double_Large_Post 	20.75 x 33.0 in 	527.1 x 838.2 mm 	1:1.5904
British_Imperial_Uncut_Writing_Paper_Copy 	16.25 x 20.0 in 	412.8 x 508.0 mm 	1:1.2308
British_Imperial_Uncut_Writing_Paper_Medium 	18.0 x 20.5 in 	457.2 x 520.7 mm 	1:1.1389
Imperial_Uncut_Book_&_Drawing_Paper_Foolscap 	14.0 x 18.75 in 	355.6 x 476.3 mm 	1:1.3393
Imperial_Uncut_Book_&_Drawing_Paper_Demy 	15.5 x 20 in 	393.7 x 508.0 mm 	1:1.2903
Imperial_Uncut_Book_&_Drawing_Paper_Medium 	17.5 x 22.5 in 	444.5 x 571.5 mm 	1:1.2857
Imperial_Uncut_Book_&_Drawing_Paper_Royal 	19.0 x 24.0 in 	482.6 x 609.6 mm 	1:1.2632
Imperial_Uncut_Book_&_Drawing_Paper_Imperial 	22.0 x 30.25 in 	558.8 x 768.4 mm 	1:1.375
Imperial_Uncut_Book_&_Drawing_Paper_Elephant 	23.0 x 28.0 in 	584.2 x 711.2 mm 	1:1.2174
Imperial_Uncut_Book_&_Drawing_Paper_Double_Elephant 	26.5 x 40.0 in 	673.1 x 1016.0 mm 	1:1.5094
Imperial_Uncut_Book_&_Drawing_Paper_Atlas 	26.25 x 34.0 in 	666.75 x 863.6 mm 	1:1.2952
Imperial_Uncut_Book_&_Drawing_Paper_Columbier 	23.5 x 24.5 in 	596.9 x 622.3 mm 	1:1.0426
Imperial_Uncut_Book_&_Drawing_Paper_Antiquarian 	31.0 x 53.0 in 	787.4 x 1346.2 mm 	1:1.7097
Imperial_Uncut_Printing_Paper_Crown 	16.25 x 21.0 in 	412.8 x 533.4 mm 	1:1.2923
Imperial_Uncut_Printing_Paper_Double_Crown 	20.0 x 30.0 in 	508.0 x 762.0 mm 	1:1.5
Imperial_Uncut_Printing_Paper_Quad 	30.0 x 40.0 in 	762.0 x 1016.0 mm 	1:1.3333
Imperial_Uncut_Printing_Paper_Demy 	17.75 x 22.5 in 	450.9 x 571.5 mm 	1:1.2676
Imperial_Uncut_Printing_Paper_Double_Demy 	22.5 x 35.5 in 	571.5 x 901.7 mm 	1:1.5778
Imperial_Uncut_Printing_Paper_Medium 	18.25 x 23.0 in 	463.6 x 584.2 mm 	1:1.2603
Imperial_Uncut_Printing_Paper_Royal 	20.0 x 25.0 in 	508.0 x 635.0 mm 	1:1.25
Imperial_Uncut_Printing_Paper_Super_Royal 	21.0 x 27.0 in 	533.4 x 685.8 mm 	1:1.2857
Imperial_Uncut_Printing_Paper_Double_Pott 	15.0 x 25.0 in 	381.0 x 635.0 mm 	1:1.2459
Imperial_Uncut_Printing_Paper_Double_Post 	19.0 x 30.5 in 	482.6 x 774.7 mm 	1:1.6667
Imperial_Uncut_Printing_Paper_Foolscap 	13.5 x 17.0 in 	342.9 x 431.8 mm 	1:1.5882
Imperial_Uncut_Printing_Paper_Double_Foolscap 	17.0 x 27.0 in 	431.8 x 685.8 mm 	1:1.5882
EOT;

	$a = explode( "\n", $a );
	foreach( $a as $k=>$v ){
		if( !preg_match("/size/i", $v) ){
#
#	Explode it on the tabs FIRST.
#
			$b = explode( "	", $v );
#
#	Now remove the underscores from the name
#
			$b[0] = str_replace( "_", " ", $b[0] );
#
#	Now store everything into the papers array.
#
			for( $i=0; $i<count($b); $i++ ){
				$this->papers['s'][$k][$i] = $b[$i];
				}
			}
		}
#
#	Low Resolutions
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	72 PPI	96 PPI	150 PPI	300 PPI
4A0	4768 x 6741	6357 x 8988	9933 x 14043	19866 x 28087
2A0	3370 x 4768	4494 x 6357	7022 x 9933	14043 x 19866
A0	2384 x 3370	3179 x 4494	4967 x 7022	9933 x 14043
A1	1684 x 2384	2245 x 3179	3508 x 4967	7016 x 9933
A2	1191 x 1684	1587 x 2245	2480 x 3508	4960 x 7016
A3	842 x 1191	1123 x 1587	1754 x 2480	3508 x 4960
A4	595 x 842	794 x 1123	1240 x 1754	2480 x 3508
A5	420 x 595	559 x 794	874 x 1240	1748 x 2480
A6	298 x 420	397 x 559	620 x 874	1240 x 1748
A7	210 x 298	280 x 397	437 x 620	874 x 1240
A8	147 x 210	197 x 280	307 x 437	614 x 874
A9	105 x 147	140 x 197	219 x 307	437 x 614
A10	74 x 105	98 x 140	154 x 219	307 x 437
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['l'][$k][$i] = $b[$i];
			}
		}
#
#	Medium Resolutions
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	600 PPI	720 PPI	1200 PPI
4A0	39732 x 56173	47679 x 67408	79464 x 112346
2A0	28087 x 39732	33704 x 47679	56173 x 79464
A0	19866 x 28087	23839 x 33704	39732 x 56173
A1	14043 x 19866	16838 x 23839	28066 x 39732
A2	9933 x 14043	11906 x 16838	19842 x 28066
A3	7016 x 9933	8419 x 11906	14032 x 19842
A4	4960 x 7016	5953 x 8419	9921 x 14032
A5	3508 x 4960	4195 x 5953	6992 x 9921
A6	2480 x 3508	2976 x 4195	4961 x 6992
A7	1748 x 2480	2098 x 2976	3496 x 4961
A8	1228 x 1748	1474 x 2098	2456 x 3496
A9	874 x 1228	1049 x 1474	1748 x 2456
A10	614 x 874	737 x 1049	1229 x 1748
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#	Also again - the name (like 4A0 and even "SIZE") is both
#	the NAME of that part of the array as well as an entry
#	in the array.
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['m'][$k][$i] = $b[$i];
			}
		}
#
#	High Resolutions
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	1440 PPI	2400 PPI	2880 PPI
4A0	95357 x 134816	158928 x 224693	190715 x 269631
2A0	67408 x 95357	112346 x 158928	134816 x 190715
A0	47679 x 67408	79464 x 112346	95357 x 134816
A1	33676 x 47679	56131 x 79464	67351 x 95357
A2	23811 x 33676	39684 x 56131	47622 x 67351
A3	16838 x 23811	28063 x 39684	33676 x 47622
A4	11906 x 16838	19843 x 28063	23811 x 33676
A5	8391 x 11906	13985 x 19843	16781 x 23811
A6	5953 x 8391	9922 x 13985	11906 x 16781
A7	4195 x 5953	6991 x 9922	8391 x 11906
A8	2948 x 4195	4913 x 6991	5896 x 8391
A9	2098 x 2948	3508 x 4913	4195 x 5896
A10	1474 x 2098	2480 x 3508	2948 x 4195
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#	Also again - the name (like 4A0 and even "SIZE") is both
#	the NAME of that part of the array as well as an entry
#	in the array.
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['h'][$k][$i] = $b[$i];
			}
		}
#
#	Other formats
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	Pica	Point	HPGL
4A0	397.3 x 561.7	4768 x 6741	67280 x 95120
2A0	280.9 x 397.3	3370 x 4768	47560 x 67280
A0	198.7 x 280.9	2384 x 3370	33640 x 47560
A1	140.3 x 198.7	1684 x 2384	23760 x 33640
A2	99.2 x 140.3	1191 x 1684	16800 x 23760
A3	70.2 x 99.2	842 x 1191	11880 x 16800
A4	49.6 x 70.2	595 x 842	8400 x 11880
A5	35.0 x 49.6	420 x 595	5920 x 8400
A6	24.8 x 35.0	298 x 420	4200 x 5920
A7	17.5 x 24.8	210 x 298	2960 x 4200
A8	12.3 x 17.5	147 x 210	2080 x 2960
A9	8.7 x 12.3	105 x 147	1480 x 2080
A10	6.1 x 8.7	74 x 105	1040 x 1480
B0	236.2 x 334.0	2834 x 4008	40000 x 56560
B1	167.0 x 236.2	2004 x 2834	28280 x 40000
B2	118.1 x 167.0	1417 x 2004	20000 x 28280
B3	83.3 x 118.1	1001 x 1417	14120 x 20000
B4	59.1 x 83.3	709 x 1001	10000 x 14120
B5	41.6 x 59.1	499 x 709	7040 x 10000
B6	29.5 x 41.6	354 x 499	5000 x 7040
B7	20.8 x 29.5	249 x 354	3520 x 5000
B8	14.6 x 20.8	176 x 249	2480 x 3520
B9	10.4 x 14.6	125 x 176	1760 x 2480
B10	7.3 x 10.4	88 x 125	1240 x 1760
C0	216.6 x 306.4	2599 x 3677	36680 x 51880
C1	153.1 x 216.6	1837 x 2599	25920 x 36680
C2	108.2 x 153.1	1298 x 1837	18320 x 25920
C3	76.5 x 108.2	918 x 1298	12960 x 18320
C4	54.1 x 76.5	649 x 918	9160 x 12960
C5	38.3 x 54.1	459 x 649	6480 x 9160
C6	26.9 x 38.3	323 x 459	4560 x 6480
C7	19.1 x 26.9	230 x 323	3240 x 4560
C8	13.5 x 19.1	162 x 230	2280 x 3240
C9	9.4 x 13.5	113 x 162	1600 x 2280
C10	6.6 x 9.4	79 x 113	1120 x 1600
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#	Also again - the name (like 4A0 and even "SIZE") is both
#	the NAME of that part of the array as well as an entry
#	in the array (ie: other[SIZE]
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['a_1'][$k][$i] = $b[$i];
			}
		}
#
#	More formats
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	Micrometres	Millimetres	Centimetres	Metres
4A0	1682000 x 2378000 um	1682 x 2378 mm	168.2 x 237.8 cm	1.682 x 2.378 m
2A0	1189000 x 1682000 um	1189 x 1682 mm	118.9 x 168.2 cm	1.189 x 1.682 m
A0	841000 x 1189000 um	841 x 1189 mm	84.1 x 118.9 cm	0.841 x 1.189 m
A1	594000 x 841000 um	594 x 841 mm	59.4 x 84.1 cm	0.594 x 0.841 m
A2	420000 x 594000 um	420 x 594 mm	42.0 x 59.4 cm	0.420 x 0.594 m
A3	297000 x 420000 um	297 x 420 mm	29.7 x 42.0 cm	0.297 x 0.420 m
A4	210000 x 297000 um	210 x 297 mm	21.0 x 29.7 cm	0.210 x 0.297 m
A5	148000 x 210000 um	148 x 210 mm	14.8 x 21.0 cm	0.148 x 0.210 m
A6	105000 x 148000 um	105 x 148 mm	10.5 x 14.8 cm	0.105 x 0.148 m
A7	74000 x 105000 um	74 x 105 mm	7.4 x 10.5 cm	0.074 x 0.105 m
A8	52000 x 74000 um	52 x 74 mm	5.2 x 7.4 cm	0.052 x 0.074 m
A9	37000 x 52000 um	37 x 52 mm	3.7 x 5.2 cm	0.037 x 0.052 m
A10	26000 x 37000 um	26 x 37 mm	2.6 x 3.7 cm	0.026 x 0.037 m
B0	1000000 x 1414000 um	1000 x 1414 mm	100.0 x 141.4 cm	1.000 x 1.414 m
B1	707000 x 1000000 um	707 x 1000 mm	70.7 x 100.0 cm	0.707 x 1.000 m
B2	500000 x 707000 um	500 x 707 mm	50.0 x 70.7 cm	0.500 x 0.707 m
B3	353000 x 500000 um	353 x 500 mm	35.3 x 50.0 cm	0.353 x 0.500 m
B4	250000 x 353000 um	250 x 353 mm	25.0 x 35.3 cm	0.250 x 0.353 m
B5	176000 x 250000 um	176 x 250 mm	17.6 x 25.0 cm	0.176 x 0.250 m
B6	125000 x 176000 um	125 x 176 mm	12.5 x 17.6 cm	0.125 x 0.176 m
B7	88000 x 125000 um	88 x 125 mm	8.8 x 12.5 cm	0.088 x 0.125 m
B8	62000 x 88000 um	62 x 88 mm	6.2 x 8.8 cm	0.062 x 0.088 m
B9	44000 x 62000 um	44 x 62 mm	4.4 x 6.2 cm	0.044 x 0.062 m
B10	31000 x 44000 um	31 x 44 mm	3.1 x 4.4 cm	0.031 x 0.044 m
C0	917000 x 1297000 um	917 x 1297 mm	91.7 x 129.7 cm	0.917 x 1.297 m
C1	648000 x 917000 um	648 x 917 mm	64.8 x 91.7 cm	0.648 x 0.917 m
C2	458000 x 648000 um	458 x 648 mm	45.8 x 64.8 cm	0.458 x 0.648 m
C3	324000 x 458000 um	324 x 458 mm	32.4 x 45.8 cm	0.324 x 0.458 m
C4	229000 x 324000 um	229 x 324 mm	22.9 x 32.4 cm	0.229 x 0.324 m
C5	162000 x 229000 um	162 x 229 mm	16.2 x 22.9 cm	0.162 x 0.229 m
C6	114000 x 162000 um	114 x 162 mm	11.4 x 16.2 cm	0.114 x 0.162 m
C7	81000 x 114000 um	81 x 114 mm	8.1 x 11.4 cm	0.081 x 0.114 m
C8	57000 x 81000 um	57 x 81 mm	5.7 x 8.1 cm	0.057 x 0.081 m
C9	40000 x 57000 um	40 x 57 mm	4.0 x 5.7 cm	0.040 x 0.057 m
C10	28000 x 40000 um	28 x 40 mm	2.8 x 4.0 cm	0.028 x 0.040 m
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#	Also again - the name (like 4A0 and even "SIZE") is both
#	the NAME of that part of the array as well as an entry
#	in the array (ie: more_1[SIZE]
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['a_2'][$k][$i] = $b[$i];
			}
		}
#
#	Even MORE formats
#
#	Note that the first line is the title line. The names going down the left
#	are where they go. Don't muck it up!
#
	$a = <<<EOT
Size	Thou	Inches	Feet	Yards
4A0	66220 x 93622 th	66.220 x 93.622 in	5.518 x 7.802 ft	1.839 x 2.601 yd
2A0	46811 x 66220 th	46.811 x 66.220 in	3.901 x 5.518 ft	1.300 x 1.839 yd
A0	33110 x 46811 th	33.110 x 46.811 in	2.759 x 3.901 ft	0.920 x 1.300 yd
A1	23388 x 33110 th	23.388 x 33.110 in	1.949 x 2.759 ft	0.650 x 0.920 yd
A2	16535 x 23388 th	16.535 x 23.388 in	1.378 x 1.949 ft	0.459 x 0.650 yd
A3	11693 x 16535 th	11.693 x 16.535 in	0.974 x 1.378 ft	0.325 x 0.459 yd
A4	8268 x 11693 th	8.268 x 11.693 in	0.689 x 0.974 ft	0.230 x 0.325 yd
A5	5827 x 8268 th	5.827 x 8.268 in	0.486 x 0.689 ft	0.162 x 0.230 yd
A6	4134 x 5827 th	4.134 x 5.827 in	0.344 x 0.486 ft	0.115 x 0.162 yd
A7	2913 x 4134 th	2.913 x 4.134 in	0.243 x 0.344 ft	0.081 x 0.115 yd
A8	2047 x 2913 th	2.047 x 2.913 in	0.171 x 0.243 ft	0.057 x 0.081 yd
A9	1457 x 2047 th	1.457 x 2.047 in	0.121 x 0.171 ft	0.040 x 0.057 yd
A10	1024 x 1457 th	1.024 x 1.457 in	0.085 x 0.121 ft	0.028 x 0.040 yd
B0	39370 x 55669 th	39.370 x 55.669 in	3.281 x 4.639 ft	1.094 x 1.546 yd
B1	27835 x 39370 th	27.835 x 39.370 in	2.320 x 3.281 ft	0.773 x 1.094 yd
B2	19685 x 27835 th	19.685 x 27.835 in	1.640 x 2.320 ft	0.547 x 0.773 yd
B3	13898 x 19685 th	13.898 x 19.685 in	1.158 x 1.640 ft	0.386 x 0.547 yd
B4	9843 x 13898 th	9.843 x 13.898 in	0.820 x 1.158 ft	0.273 x 0.386 yd
B5	6929 x 9843 th	6.929 x 9.843 in	0.577 x 0.820 ft	0.192 x 0.273 yd
B6	4921 x 6929 th	4.921 x 6.929 in	0.410 x 0.577 ft	0.137 x 0.192 yd
B7	3465 x 4921 th	3.465 x 4.921 in	0.289 x 0.410 ft	0.096 x 0.137 yd
B8	2441 x 3465 th	2.441 x 3.465 in	0.203 x 0.289 ft	0.068 x 0.096 yd
B9	1732 x 2441 th	1.732 x 2.441 in	0.144 x 0.203 ft	0.048 x 0.068 yd
B10	1220 x 1732 th	1.220 x 1.732 in	0.102 x 0.144 ft	0.034 x 0.048 yd
C0	36102 x 51063 th	36.102 x 51.063 in	3.009 x 4.255 ft	1.003 x 1.418 yd
C1	25512 x 36102 th	25.512 x 36.102 in	2.126 x 3.009 ft	0.709 x 1.003 yd
C2	18031 x 25512 th	18.031 x 25.512 in	1.503 x 2.126 ft	0.501 x 0.709 yd
C3	12759 x 18031 th	12.759 x 18.031 in	1.063 x 1.503 ft	0.354 x 0.501 yd
C4	9016 x 12759 th	9.016 x 12.759 in	0.751 x 1.063 ft	0.250 x 0.354 yd
C5	6378 x 9016 th	6.378 x 9.016 in	0.531 x 0.751 ft	0.177 x 0.250 yd
C6	4488 x 6378 th	4.488 x 6.378 in	0.374 x 0.531 ft	0.125 x 0.177 yd
C7	3189 x 4488 th	3.189 x 4.488 in	0.266 x 0.374 ft	0.089 x 0.125 yd
C8	2244 x 3189 th	2.244 x 3.189 in	0.187 x 0.266 ft	0.062 x 0.089 yd
C9	1575 x 2244 th	1.575 x 2.244 in	0.131 x 0.187 ft	0.044 x 0.062 yd
C10	1102 x 1575 th	1.102 x 1.575 in	0.092 x 0.131 ft	0.031 x 0.044 yd
EOT;

	$a = explode( "\n", $a );

	foreach( $a as $k=>$v ){
#
#	Again, explode the line on the tabs.
#	Also again - the name (like 4A0 and even "SIZE") is both
#	the NAME of that part of the array as well as an entry
#	in the array (ie: more_2[SIZE]
#
		$v = strtolower( trim($v) );
		$b = explode( "	", $v );
		for( $i=0; $i<count($b); $i++ ){
			$this->papers['a_3'][$k][$i] = $b[$i];
			}
		}

	$this->debug->out();

	return true;
}
################################################################################
#	english(). English measurements.
################################################################################
public function english( $e )
{
	$this->debug->in();

	if( preg_match("/(mi|mile)/i", $e) ){ $ret = 5280 * 12; }					#	Mile
		else if( preg_match("/in|inch)/i", $e) ){ $ret = 1; }					#	Inch
		else if( preg_match("/(yd|yard)/i", $e) ){ $ret = 36; }					#	Yard
		else if( preg_match("/(ft|foot)/i", $e) ){ $ret = 12; }					#	Foot
		else if( preg_match("/thou/i", $e) ){ $ret = 0.001; }					#	Thou
		else if( preg_match("/line/i", $e) ){ $ret = 1.0 / 12.0; }				#	Line
		else if( preg_match("/league/i", $e) ){ $ret = (5280 * 3) * 12; }		#	League
		else if( preg_match("/fathom/i", $e) ){ $ret = 72; }					#	Fathom
		else {
			$out = __FILE__ . " > " . __CLASS__ . " @ " .
				__FUNCTION__ . " # " . __LINE__ .
				" : Unknown English Measurement : $e\n";

			echo $out;
			die( $out );
			}

	$this->debug->out();

	return false;
}
################################################################################
#	other(). Other measurements.
################################################################################
public function other( $o )
{
	$this->debug->in();

	if( preg_match("/angstrom/i", $o) ){ $ret = 10**-12 * 100; }			#	Angstrom
		else if( preg_match("/micron/i", $o) ){ $ret = 10**-6; }			#	Micron
		else if( preg_match("/x\sunit/i", $o) ){ $ret = 10**-12; }			#	X Unit
		else if( preg_match("/(mil|myriametre)/i", $o) ){ $ret = 10000; }	#	Norwegian/Swedish mil or myriametre
		else if( preg_match("/nautial mile/i", $o) ){
			$ret = 1852 * $this->metric('meter') * 25.0; }					#	Nautical Mile
		else {
			$out = __FILE__ . " > " . __CLASS__ . " @ " .
				__FUNCTION__ . " # " . __LINE__ .
				" : Unknown Other Measurement : $o\n";

			echo $out;
			die( $out );
			}

	$this->debug->out();

	return false;
}
################################################################################
#	metric(). Metric measurements.
################################################################################
public function metric( $m )
{
	$this->debug->in();

	if( preg_match("/(da|deca)/i", $m) ){ $exp = 1; }				#	Deca
		else if( preg_match("/(hm|hecto)/i", $m) ){ $exp = 2; }		#	Hecto
		else if( preg_match("/(km|kilo)/i", $m) ){ $exp = 3; }		#	Kilo
		else if( preg_match("/(Mm|mega)/i", $m) ){ $exp = 6; }		#	Mega
		else if( preg_match("/(gm|giga)/i", $m) ){ $exp = 9; }		#	Giga
		else if( preg_match("/(tm|tera)/i", $m) ){ $exp = 12; }		#	Tera
		else if( preg_match("/(Pm|peta)/i", $m) ){ $exp = 15; }		#	Peta
		else if( preg_match("/(Em|exa)/i", $m) ){ $exp = 18; }		#	Exa
		else if( preg_match("/(Zm|zetta/i", $m) ){ $exp = 21; }		#	Zetta
		else if( preg_match("/(Ym|yotta/i", $m) ){ $exp = 24; }		#	Yotta
		else if( preg_match("/(m|meter/i", $m) ){ $exp = 0; }		#	Meter
		else if( preg_match("/(dm|deci)/i", $m) ){ $exp = -1; }		#	Deci
		else if( preg_match("/(cm|centi)/i", $m) ){ $exp = -2; }	#	Centi
		else if( preg_match("/(mm|milli)/i", $m) ){ $exp = -3; }	#	Milli
		else if( preg_match("/(mu|micro)/i", $m) ){ $exp = -6; }	#	Micro
		else if( preg_match("/(nm|nano)/i", $m) ){ $exp = -9; }		#	Nano
		else if( preg_match("/(pm|pico)/i", $m) ){ $exp = -12; }	#	Pico
		else if( preg_match("/(fm|fento)/i", $m) ){ $exp = -15; }	#	Fento
		else if( preg_match("/(am|atto)/i", $m) ){ $exp = -18; }	#	Atto
		else if( preg_match("/(zm|zepto)/i", $m) ){ $exp = -21; }	#	Zepto
		else if( preg_match("/(ym|yocto)/i", $m) ){ $exp = -24; }	#	Yocto
		else if( preg_match("/(fm|fermi)/i", $m) ){ $exp = -15; }	#	Fermi
		else {
			$out = __FILE__ . " > " . __CLASS__ . " @ " .
				__FUNCTION__ . " # " . __LINE__ .
				" : Unknown Metric Measurement : $m\n";

			echo $out;
			die( $out );
			}
#
#	Convert to millimeters being at 10**0 power. We do this because most
#	sizes are either in inches or millimeters.
#
	$exp += 3;

	$this->debug->out();

	return 10**$exp;

}
################################################################################
#	name(). Send a paper's name - get the array back.
#	Notes:	You can send an array or just a string.
#	Notes:	You do not have to send a complete name.
################################################################################
public function name( $n )
{
	$this->debug->in();

	$paper = $this->papers;
#
#	First, make an array to hold everything.
#
	$info = [];
#
#	Go through the list of paper.
#
	foreach( $paper as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
#
#	Is the incoming variable ($n) an array? So they need several
#	types of paper information.
#
			if( is_array($n) ){
#
#	Look through the array. If a part of the name is in that paper - then
#	send it back. Who are we to decide what they do or do not need.
#
#	Remember - it looks like this: $this->papers['s'][number][info].
#
				foreach( $n as $k2=>$v2 ){
					if( preg_match("/$v2/i", $v1[0]) ){ $info[$k2] = $v1; }
					}
				}
#
#	Nope - just a string. See if we can find it.
#
				else {
					if( preg_match("/$n/i", $v1[0]) ){ $info[] = $v1; }
					}
			}
		}
#
#	Did we find something? Then send it back. Otherwise - we send FALSE.
#
	if( count($info) < 1 ){
		$this->debug->out();
		return false;
		}

	$this->debug->out();

	return $info;
}
################################################################################
#	size(). Send width and height - get the array back.
#	Notes: $s can be a single entry (ie: size(w,h)),
#		an array (w,h) or a string "WxH".
#		You can ONLY find one entry this way.
################################################################################
public function size( $s )
{
	$this->debug->in();

	$paper = $this->papers;
	$args = func_get_args();
#
#	Did they send us an array or just a string?
#
	foreach( $args as $k=>$v ){
		if( is_array($v) ){ $w = $v[0]; $h = $v[1]; break; }
			else if( is_numeric($v) ){
				$w = $v;
				$h = $args[$k+1];
				break;
				}
			else if( is_string($v) ){
				$a = explode( "x", $s );
				$w = $a[0];
				$h = $a[1];
				}
		}
#
#	Ok - go through the paper and look for something with the same 
#	width and height.
#
	foreach( $paper as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $v1 as $k2=>$v2 ){
				if( preg_match("/^$w$/", $v2) && preg_match("/^$h$/", $v2) ){
					return $v1;
					}
				}
			}
		}

	$this->debug->out();

	return false;
}
################################################################################
#	dump(). Dump the paper information out.
################################################################################
public function dump( $file=null )
{
	$this->debug->in();

	if( is_null($file) ){ $fp = fopen( "./papers.dat", "w" ); }
		else { $fp = fopen( $file, "w" ); }

	foreach( $this->papers as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $v1 as $k2=>$v2 ){
				fprintf( $fp, "	Papers[%s][%s][%s] = %s\n", $k, $k1, $k2, $v2 );
				}
			}

		fprintf( $fp, "\n" );
		}

	fclose( $fp );

	$this->debug->out();

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['paper']) ){ $GLOBALS['classes']['paper'] = new class_paper(); }

	$a = new class_paper(true);
	$out = $a->sizes(8.5, 11.0);
	if( is_array($out) ){ print_r( $out ); }
		else { echo "GOT FALSE!\n"; }

	$out = $a->name("postcard");
	if( is_array($out) ){ print_r( $out ); }
		else { echo "GOT FALSE!\n"; }

	$a->dump();

?>
