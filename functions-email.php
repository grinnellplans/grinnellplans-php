<?php
require_once ('Plans.php');
require_once ('vendor/autoload.php');
use \Touhonoob\RateLimit\RateLimit;
use \Touhonoob\RateLimit\Adapter\APCu as RateLimitAdapterAPCu;

function send_mail($to, $subject, $text, $from = MAILER_ADDRESS, $reply_to = ADMIN_ADDRESS) {
    $adapter = new RateLimitAdapterAPCu();
    $ipRateLimit = new RateLimit("sendemailfromip", 10, 86400, $adapter); //10 emails per IP per day
    $totalRateLimit = new RateLimit("sendemail", 15, 60, $adapter); // 15 emails per minute total
    if ($ipRateLimit->check($_SERVER['REMOTE_ADDR']) <= 0 || $totalRateLimit->check('me') <= 0) {
    return false;
    }
    //check blacklist
    $blacklist = Doctrine_Query::create()
      ->select('COUNT(b.email) as count')
      ->from('EmailBlacklist b')
      ->where('b.email = ?',$to)
      ->fetchOne();
    if ($blacklist->count > 0) {
        return false;
    }
    if(USE_NATIVE_MAIL) {
        if (is_array($to)) $to = implode(', ',$to);
        return mail($to, $subject, $text, "From:$from\nReply-to:$reply_to");
    }
    else {
        if (!is_array($to)) $to = array($to);
	if (defined('AWS_KEY') && defined('AWS_SECRET_KEY')) {
		$credentials = array('key'=>AWS_KEY,'secret'=>AWS_SECRET_KEY);
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
