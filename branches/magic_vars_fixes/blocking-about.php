<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'blocks', PLANSVNAME . ' - Blocking FAQ', 'blocking-about.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
} else {
    populate_page($thispage, $dbh, $idcookie);
}

$heading = new HeadingText('Blocking FAQ', 2);
$thispage->append($heading);

$faq = <<<EOT
<dl>
<dt>
What does blocking mean on Plans?
</dt>
<dd>
Blocking is a one-directional action. You may continue to read a plan of someone you have blocked unless you are blocked back. Selecting "Block" applies to reading your plan, searching for your plan, and seeing quicklove or planwatch updates.
</dd>

<dt>How does it work?</dt>
<dd>
If [hatfield] blocks [mccoyran], then [mccoyran] can no longer read [hatfield]. If [mccoyran] tries to visit the URL, [mccoyran] will see

“[hatfield] has enabled the block feature. This plan is not available.”

No listing of [hatfield] will show up when [mccoyran] searches, neither will see any [planlove] by the other, and any updates either make will not show up on each other’s planwatch.

Blocked & blocking users also will not appear in the "Do you read [xyz], who just updated?" message.

Blocking someone will not hide your Notes activity nor prevent them from interacting with you on Notes.
</dd>

<dt>How do I block someone?</dt>
<dd>
At the bottom of each plan next to the autoread/autofinger levels is new button labeled “Block”. Click the button. You will be re-directed to a confirmation page. The confirmation page reads:

 "You have Blocked [username]. Blocking a user is one-directional. Selecting "Block" renders the contents of your plan unavailable to this user. Neither will see any [planlove] by the other, and any updates either make will not show up on each other’s planwatch.

If this block was made in error, please click here to un-do.”
</dd>

<dt>So I can unblock someone?</dt>
<dd>
Yes. We encourage revisiting your block list periodically and assessing your list.
</dd>

<dt>Wait, where is this blocked list?</dt>
<dd>
Your <a href="/blocks.php">blocked list</a> can be found in "Preferences" under "Blocking". Each username has an “Unblock” button. Only one username may be unblocked at a time.
</dd>

<dt>Can someone circumvent the block?</dt>
<dd>
Yes. The blocking feature does not disable the guest readable function on plans. However, the ToS states that Plans Administrators have the right to “Terminate or suspend Users who intentionally and maliciously circumvent any security or authentication measures or any features intended to block or limit contact between Users.”

Plans administrators will also be able to read your plan regardless of whether you have blocked them.
</dd>

<dt>What is the guest readable function, and where can I find it?</dt>
<dd>
The <a href="/webview.php">guest readable function</a> can be found under “Preferences”. Your options are:
“Make plan viewable to guests.” OR  “Make plan unviewable to guests.”
</dd>

<dt>Who made this blocking feature, anyway?</dt>
<dd>
This feature was created in January 2015 by a group of volunteer Plans coders.
</dd>

<dt>What was Aretha Franklin’s first number 1 hit?</dt>
<dd>
We’re glad you asked! It was <cite>Respect</cite>. On that note, please respect the needs of other users. Attempt to resolve conflicts before resorting to blocking, however, if an individual feels the need to block, respect their choice.
</dd>

<dt>Where do I send my comments or questions?</dt>
<dd>
Email <a href="mailto:grinnellplans@gmail.com">grinnellplans@gmail.com</a>.
</dd>
</dl>
EOT;
$thispage->append(new RegularText($faq));

interface_disp_page($thispage);
db_disconnect($dbh);
?>
