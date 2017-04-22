#!/bin/sh

USERNAME=username
PASSWORD=password

rm -f jar

url="http://www.grinnellplans.com/index.php"
postdata="username=$USERNAME&password=$PASSWORD&submit=Login&js_test_value=off"

FAILED=0
echo "Logging in..."
curl -L -s -c jar --data $postdata $url | grep -q "Invalid username or password." && FAILED=1

if [ $FAILED = 1 ]; then
	echo "Login failed!" >&2
	exit 1
fi

echo "Enumerating users..."
url="http://www.grinnellplans.com/listusers.php"
for i in `seq 97 122`; do
	curl -L -b jar -s "$url?letternum=$i" || (echo "failed to get users" >&2; exit 1)
done | sed -n -e '/class="autoreadentry/d
/justupdatedlink/d
1,$s/^.*searchname=\([^"]*\)".*$/\1/p' > users

rm -f blockedby

echo "Checking users plans visibilities..."
url="http://grinnellplans.com/api/1/index.php?task=read"
cat users | while read puid; do
	postdata="username=$puid&partial=true"
	msg=`curl -s -b jar --data $postdata $url$task 2>/dev/null | head -c64`

	if echo $msg | grep -q '{"message":"blocked"'; then
	    echo User $puid has you blocked.
	    echo $puid >> blockedby
	fi
done

echo

COUNT=$(cat blockedby | wc -l)
if [ $COUNT = 0 ]; then
	echo "You are not blocked by any users."
else
	echo "You are currently being blocked by $COUNT users, out of `wc -l users` total users."
fi
