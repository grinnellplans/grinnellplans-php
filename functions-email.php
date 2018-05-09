<?php
require_once ('Plans.php');
require_once ('vendor/autoload.php');

function send_mail($to, $subject, $text, $from = MAILER_ADDRESS, $reply_to = ADMIN_ADDRESS) {
    if(USE_NATIVE_MAIL) {
        if (is_array($to)) $to = implode(', ',$to);
        return mail($to, $subject, $text, "From:$from\nReply-to:$reply_to");
    }
    else {
        if (!is_array($to)) $to = array($to);
	if (defined('AWS_KEY') && defined('AWS_SECRET_KEY')) {
		$credentials = Aws\Credentials\Credentials(AWS_KEY,AWS_SECRET_KEY);
	} else {
		$credentials = Aws\Credentials\CredentialProvider::defaultProvider();
	}
        $ses = new Aws\Ses\SesClient(['region'=>'us-east-1','version'=>'2010-12-01','credentials'=>$credentials]);
        $ret = $ses->sendEmail([
		'Destination'=>['ToAddresses'=>$to],
		'Message'=>[
			'Body'=>[
				'Text'=>['Charset'=>'utf-8','Data'=>$text]
			],
			'Subject'=>['Charset'=>'utf-8','Data'=>$subject]
		],
		'ReplyToAddresses'=>[$reply_to],
		'Source'=>$from
	]);
        return $ret;
    }
}
?>
