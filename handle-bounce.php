<?php
require_once 'bootstrap.php';

if (!defined('AWS_SES_FEEDBACK_ARN')) die("Define AWS_SES_FEEDBACK_ARN in the config file");

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

$message = Message::fromRawPostData();

$validator = new MessageValidator();

if ($validator->isValid($message)) {
if ($message['TopicArn'] === AWS_SES_FEEDBACK_ARN) {
if ($message['Type'] === 'SubscriptionConfirmation') {
file_get_contents($message['SubscribeURL']);
} elseif ($message['Type'] === 'Notification') {
$notification = json_decode($message['Message']);

if ($notification->notificationType === "Bounce") {
foreach ($notification->bounce->bouncedRecipients as $recipient) {
try {
$entry = new EmailBlacklist();
$entry->email = $recipient->emailAddress;
$entry->save();
} catch (Exception $e) {
error_log($e);
}
}
}
}
}
}
