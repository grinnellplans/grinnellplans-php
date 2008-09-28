<?php
	require_once("Plans.php");
/*
Grinnell Plans. A web-based version of social .plans.
Copyright (C) 2002 by Jonathan Kensler

---

This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

---
If you need to contact me you may so at:
kenslerj@grinnell.edu

or

Jonathan Kensler
Box 07-04 Grinnell College
Grinnell, IA 50112

*/

//Issues the queries to set up the database



require("dbfunctions.php");
$mydbh = db_connect();

/*
mysql_query("CREATE TABLE accounts(
userid SMALLINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
username VARCHAR(16) NOT NULL,
UNIQUE INDEX (username),
created TIMESTAMP,
password VARCHAR(20),
email VARCHAR(64),
pseudo VARCHAR(64),
login DATETIME,
changed DATETIME,
plan TEXT,
poll TINYINT UNSIGNED,
group_bit CHAR(1),
spec_message VARCHAR(255),
grad_year CHAR(4)
)");

mysql_query("CREATE TABLE autofinger(
owner SMALLINT UNSIGNED NOT NULL,
INDEX (owner),
interest SMALLINT UNSIGNED NOT NULL,
INDEX (interest),
priority TINYINT UNSIGNED,
updated CHAR(1),
updtime DATETIME,
readtime DATETIME
)");



mysql_query("CREATE TABLE display(
userid SMALLINT UNSIGNED PRIMARY KEY,
text_color CHAR(6),
link_color CHAR(6),
visited_color CHAR(6),
bg_color CHAR(6),
panel_color CHAR(6),
header_style VARCHAR(16),
background VARCHAR(16),
panelside CHAR(1)
)");


mysql_query("CREATE TABLE opt_links(
userid SMALLINT UNSIGNED,
INDEX (userid),
linknum TINYINT UNSIGNED
)");



mysql_query("CREATE TABLE avail_links(
linknum TINYINT UNSIGNED PRIMARY KEY,
linkname VARCHAR(128),
descr TEXT,
html_code TINYTEXT
)");

mysql_query("CREATE TABLE system(
motd TEXT,
poll TEXT
)");

mysql_query("CREATE TABLE poll(
pollid TINYINT UNSIGNED PRIMARY KEY,
polltext TINYTEXT,
polltotal SMALLINT
)");



mysql_query("CREATE TABLE mainboard(
threadid SMALLINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
title VARCHAR(128),
created DATETIME,
lastupdated DATETIME,
userid SMALLINT UNSIGNED NOT NULL
)");

mysql_query("CREATE TABLE subboard(
messageid SMALLINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
threadid SMALLINT UNSIGNED NOT NULL,
INDEX (threadid),
created DATETIME,
userid SMALLINT UNSIGNED NOT NULL,
INDEX (userid),
title VARCHAR(128),
contents TEXT NOT NULL
)");

*/


mysql_query("CREATE TABLE stylesheet(
userid SMALLINT UNSIGNED PRIMARY KEY NOT NULL,
stylesheet tinytext
)");



//annihilate($mydbh, "accounts");
//annihilate($mydbh, "autofinger");
//annihilate($mydbh, "display");
//annihilate($mydbh, "opt_links");
//annihilate($mydbh, "avail_links");
//annihilate($mydbh, "system");
//annihilate($mydbh, "poll");
//annihilate($mydbh, "mainboard");
//annihilate($mydbh, "subboard");
//annihilate($mydbh, "stylesheet");

db_disconnect($mydbh);


?>
