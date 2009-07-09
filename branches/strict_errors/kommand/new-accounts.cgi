#!/usr/bin/perl 

use strict;
use DBI;
use CGI;
my $q = CGI->new();
print $q->header('text/plain');
my $dbh = db_connect();
my $months = fetch_plan_dates();


my $updated = "P";
my $expired = "-";
my $never_updated = "_";
print "Each $updated, $expired and $never_updated is a Plan that was created during a particular month.\n";
print "$never_updated means it has never been updated (yet).\n$expired means it was updated, but not in the last year.\n$updated refers to all others.\n\n\n";

foreach my $month (@$months) {
	my $name = $month->[0];
	my $total = $month->[1];
	my $empty = $month->[2];
	my $recent = $month->[3];
	printf("%-16s %-5s%s%s%s\n","$name:", "($total)", $never_updated x ($empty), $expired x ($total - $recent - $empty), $updated x ($recent));
}


print "\n\n(Older data is not usable.)";



# db_connect: Connect to the DB
sub db_connect() {
    my $dbh = DBI->connect("dbi:mysql:plans:127.0.0.1","plans",'M>e4oV') or die "Could not connect $!";
    return $dbh;
}

sub fetch_plan_dates() {
	my $months = [];
    my $userids;
	my $sql = '
		select date_format(created, "%M, %Y") m,
		count(*) total,
		sum(if(changed = "0000-00-00 00:00:00", 1, 0)) empty,
		sum(if(changed > date_sub(now(), interval 1 year), 1, 0)) recent
--		sum(if(changed > date_sub(now(), interval 6 month), 1, 0)) recent
		from accounts 
		where 
		created > "2002-10-01" 
		group by m 
		order by created desc;  
	';
    my $sth = $dbh->prepare($sql);
    $sth->execute();
    while (my $row = $sth->fetchrow_hashref()) {
		push @$months, [$row->{'m'}, $row->{'total'}, $row->{'empty'}, $row->{'recent'}];
    }
	return $months;
}

