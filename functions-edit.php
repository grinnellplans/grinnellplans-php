<?php
require_once ('Plans.php');
//////////
/*
*Get all of the plans that fall between two letters (usually the same one)
*/
function get_letters($dbh, $first_letter, $second_letter, $idcookie) {
    if (!$idcookie) {
        $guest = "AND webview=1";
    }
    $my_result = mysql_query("Select userid,username From accounts where username > '$first_letter' and username < '$second_letter' $guest ORDER BY username");
    while ($new_row = mysql_fetch_row($my_result)) {
        $all[] = $new_row;
    }
    return $all;
}
/*
*Handles the cleaning up of a plan, such as allowing only certain html links in
*/
function cleanText($plan, &$planlove = array()) {
    $plan = htmlspecialchars($plan); //take out html
    //fix the dollar sign error- by josh
    //$plan = preg_replace("(\|(\w\s)*)\$
    $plan = preg_replace("/((\[\w*\],?){8})(?=[^ ,])/s", "$1 ", $plan); //break groups of eight or more planlove in a row without a space to prevent page widening
    $plan = nl2br($plan); //use nl2br instead of a regex because it handles unix, mac, and windows line endings correctly.
    $plan = preg_replace("/\&lt\;hr\&gt\;/si", "</p><hr><p class=\"sub\">", $plan);
    // replace the first </p> that we just inserted erroneously
    //$plan = preg_replace("</p>", "", $plan, 1);
    $plan = '<p class="sub">' . $plan . '</p>';
    $plan = preg_replace("/\&lt\;b\&gt\;(.*?)\&lt\;\/b\&gt\;/si", "<b>\\1</b>", $plan); //allow stuff in the bold tag back in
    $plan = preg_replace("/\&lt\;tt\&gt\;(.*?)\&lt\;\/tt\&gt\;/si", "<tt>\\1</tt>", $plan);
    $plan = preg_replace("/\&lt\;pre\&gt\;(.*?)\&lt\;\/pre\&gt\;/si", "</p><pre class=\"sub\">\\1</pre><p class=\"sub\">", $plan);
    $plan = preg_replace("/\&lt\;strike\&gt\;(.*?)\&lt\;\/strike\&gt\;/si", "<span class=\"strike\">\\1</span><!--strike-->", $plan);
    $plan = preg_replace("/\&lt\;s\&gt\;(.*?)\&lt\;\/s\&gt\;/si", "<s>\\1</s>", $plan);
    $plan = preg_replace("/\&lt\;i\&gt\;(.*?)\&lt\;\/i\&gt\;/si", "<i>\\1</i>", $plan); //allow stuff in the italics tag back in
    $plan = preg_replace("/\&lt\;u\&gt\;(.*?)\&lt\;\/u\&gt\;/si", "<span class=\"underline\">\\1</span><!--u-->", $plan); //allow stuff in the underline tag back in
    $plan = preg_replace("/\&lt\;a.+?href=.&quot\;(.+?).&quot\;&gt\;(.+?)&lt\;\/a&gt\;/si", "<a href=\"\\1\" class=\"onplan\">\\2</a>", $plan);
    //$plan = preg_replace("/\&lt\;a.href=.&quot\;(.+).&quot\;/si", "EEE",$plan);
    preg_match_all("/\[([^\[\]]*?)\]/s", $plan, $mymatches); //get an array of everything in brackets
    foreach ($mymatches[1] as $mycheck) //do a loop to test whether everything in brackets is a valid user or not
    {
        //echo '<!-- ' ."/\[$mycheck\]/s" . ' -->' . "\n";
        if (!isset($checked[$mycheck])) //make sure current thing being checked has not already been checked
        {
            //check for plan with username
            $dbh = db_connect();
            if ($item = get_item($dbh, "username", "accounts", "username", $mycheck)) //see if is a valid user, if so also gets username
            {
                $planlove[] = $mycheck;
                $plan = preg_replace("/\[".preg_quote($mycheck,'/')."\]/s", "[<a href=\"read.php?searchname=$item\" class=\"planlove\">$mycheck</a>]", $plan); //change all occurences of person on plan
                
            } else {
                if (preg_match('/^\d+$/', $mycheck) && $item = get_item($dbh, "messageid", "subboard", "messageid", $mycheck)) {
                    $plan = preg_replace("/\[" . preg_quote($mycheck, "/") . "\]/s", "[<a href=\"board_messages.php?messagenum=$item#$item\" class=\"boardlink\">$mycheck</a>]", $plan);
                }
                if (preg_match("/^(http|https|mailto):/", $mycheck)) {
                    if (strrpos($mycheck, "|")) {
                        preg_match("/(.+?)\|(.+)/si", $mycheck, $love_replace);
                        // Here, we need to escape $'s so they don't get treated as back-references
                        $love_replace[2] = addcslashes($love_replace[2], "$");
                        $plan = preg_replace("/\[" . preg_quote($mycheck, "/") . "\]/s", "<a href=\"$love_replace[1]\" class=\"onplan\">$love_replace[2]</a>", $plan);
                    } else {
                        $plan = preg_replace("/\[" . preg_quote($mycheck, "/") . "\]/s", "<a href=\"$mycheck\" class=\"onplan\">$mycheck</a>", $plan);
                    }
                }
            }
            $checked[$mycheck] = 1; //mark checked values as checked, so don't have to check again
            
        } //if (!$checked[$mycheck])
        
    } //foreach ($mymatches[1] as $mycheck)
    $plan = trim($plan);
    return $plan;
}
?>
