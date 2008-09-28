#!/bin/bash
echo Content-type: text/html
echo 
mysql -H -u plans --password='mypassword' plans <<< " select count(*) as 'Number of Plans Last Updated ', ceiling((unix_timestamp(now()) - unix_timestamp(changed)) / 86400) as 'This Many Days Ago' from accounts where changed != '0000-00-00 00:00:00' group by 'This Many Days Ago' order by 'This Many Days Ago' "
