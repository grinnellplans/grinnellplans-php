Grinnell Plans (a.k.a. Plans v2)
Copyright 2002 by Jonathan Kensler
Please email bug reports/comments/suggestions/etc. to plans@grinnell.edu

The terms under which you may use this code may be found in gpl.txt

---
Special Thanks:
John Stone- For allowing this web based plans system to be created and run 
at Grinnell. For putting up with some of the hastles, resource drain, etc. 
from the evolution of this project. For knowing how to run a 
network.

Rachel Heck- For the original Grinnell web based plans.

Vax-Gods- The original guys who maintained the cultural phenomena of plans 
at Grinnell.

---

8/10/2002
This is a series of PHP scripts that serve to replace the .plan files that 
were used in a social context on Grinnell College's VAX before 2000, but 
which has also evolved in it's own right.

To get things started, you will need to first have a directory that is 
viewable from the web, on a server that is running PHP (see php.net for 
details) and you will also need to have a MySQL database (see mysql.com 
for details) and have privileges to change it.

Once you've downloaded these files, you will first need to change a few 
lines in dbfunctions.php. Specifically in the db_connect function, you 
will need to change mysql_servername to the name of the server that hosts 
the database, for example "dbserver.cs.grinnell.edu". Next you will need 
to change mysql_username to the name with which you have an account under 
MySQL (ex.: kenslerj). Then you will need to change mysql_password to your 
password for your account under MySQL (ex.: twi.4Mt). Finally, you will need 
to change mysql_database to the name of the MySQL database that will 
contain the data for the pages to run (ex.: plans).

The next step is that you will need to run DBsetup.php. This will set up 
the structure of the database. 

Next you will have to add a row to the styles table of your 
database, telling where at least one of your style sheets is.

Next you will have to add a row to the interfaces table of your database, 
telling where at least one of your interfaces is.

And finally, you will need to add a row to the accounts table of your 
database, creating a user, along with their password, as well as 
specifying the style number and interface number from the previous steps.

---
