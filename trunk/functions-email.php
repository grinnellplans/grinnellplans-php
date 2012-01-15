<?php
require_once ('Plans.php');
require_once ('lib/aws/sdk.class.php');

CFCredentials::set(array('default'=>array('key'=>AWS_KEY,'secret'=>AWS_SECRET_KEY)));

function send_mail($to, $subject, $text, $from, $reply_to) {
    if(USE_NATIVE_MAIL) {
        return mail($to, $subject, $text, "From:$from\nReply-to:$reply_to");
    }
    else {
        $message = array('Subject'=>array('Data'=>$subject),
                         'Body'=>array('Text'=>array('Data'=>$text)));
        $opt = array('ReplyToAddresses'=>$reply_to);
        $ses = new AmazonSES();
        $ret = $ses->send_email($from,array('ToAddresses'=>$to),$message,$opt);
        return $ret->isOK();
    }
}
?>
