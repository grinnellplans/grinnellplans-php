<?php
/*
* This is an example of how G2 can be wrapped into your own website
* If you only want to embed G2 visually in your website, you don't need GalleryEmbed (so this
* approach is not necessarily what you want). But if you want to embed G2 in your website,
* including a unified user management, a single login etc., then this is the correct file to
* start with.
*/
/*
* runGallery() exits if G2 tells it to so (by isDone = true). It's important that you don't
* output any html / anything before you call runGallery (which calls
* GalleryEmbed::handleRequest), else, G2 won't work correctly.
* Reason: G2 does a lot of redirects. E.g. when you login, it redirects to the next page, etc.
* and redirects won't work if there was already some output before the redirect call.
*/
$data = runGallery();
$data['title'] = (isset($data['title']) && !empty($data['title'])) ? $data['title'] : 'Gallery';
if (isset($data['bodyHtml'])) {
	print <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>{$data['title']}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
{$data['javascript']}
{$data['css']}
</head>

<body>
{$data['bodyHtml']}
</body>
</html>
EOF;
	
}
function runGallery()
{
	require_once ('embed.php');
	$data = array();
	// if anonymous user, set g2 activeUser to ''
	$uid = '';
	// initiate G2
	$ret = GalleryEmbed::init(array('g2Uri' => '/gallery2/', 'embedUri' => '/main.php', 'activeUserId' => $uid));
	if ($ret) {
		$data['bodyHtml'] = $ret->getAsHtml();
		return $data;
	}
	// user interface: you could disable sidebar in G2 and get it as separate HTML to put it into a block
	// GalleryCapabilities::set('showSidebarBlocks', false);
	//Something about content-type of page?
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8');
	}
	// handle the G2 request
	$g2moddata = GalleryEmbed::handleRequest();
	// show error message if isDone is not defined
	if (!isset($g2moddata['isDone'])) {
		$data['bodyHtml'] = 'isDone is not defined, something very bad must have happened.';
		return $data;
	}
	// exit if it was an immediate view / request (G2 already outputted some data)
	if ($g2moddata['isDone']) {
		exit;
	}
	// put the body html from G2 into the xaraya template
	$data['bodyHtml'] = isset($g2moddata['bodyHtml']) ? $g2moddata['bodyHtml'] : '';
	// get the page title, javascript and css links from the <head> html from G2
	$title = '';
	$javascript = array();
	$css = array();
	if (isset($g2moddata['headHtml'])) {
		list($data['title'], $css, $javascript) = GalleryEmbed::parseHead($g2moddata['headHtml']);
		$data['headHtml'] = $g2moddata['headHtml'];
	}
	/* Add G2 javascript  */
	$data['javascript'] = '';
	if (!empty($javascript)) {
		foreach($javascript as $script) {
			$data['javascript'].= "\n" . $script;
		}
	}
	/* Add G2 css  */
	$data['css'] = '';
	if (!empty($css)) {
		foreach($css as $style) {
			$data['css'].= "\n" . $style;
		}
	}
	// sidebar block
	if (isset($g2moddata['sidebarBlocksHtml']) && !empty($g2moddata['sidebarBlocksHtml'])) {
		$data['sidebarHtml'] = $g2moddata['sidebarBlocksHtml'];
	}
	// Add on-the-fly user creation
	//    $ret = GalleryEmbed::init(array('baseUri' => $baseUri, 'g2Uri' => $g2Uri, 'activeUserId' => $emAppUserId));
	//    if ($ret) {
	//     /* Error! */
	//     /* Did we get an error because the user doesn't exist in g2 yet? */
	//     $ret2 = GalleryEmbed::isExternalIdMapped($emAppUserId, 'GalleryUser');
	//     if ($ret2 && $ret2->getErrorCode & ERROR_MISSING_OBJECT) {
	//         /* The user does not exist in G2 yet. Create in now on-the-fly */
	//         $ret = GalleryEmbed::createUser($emAppUserId, array('username' => $emAppUser['username'],
	//                                                             'language' => $emAppUser['language'],));
	//         if ($ret) {
	//             /* An error during user creation. Not good, print an error or do whatever is appropriate
	//              * in your emApp when an error occurs */
	//             print "An error occurred during the on-the-fly user creation <br>";
	//             print $ret->getAsHtml();
	//             exit;
	//         }
	//     } else {
	//         /* The error we got wasn't due to a missing user, it was a real error */
	//         if ($ret2) {
	//             print "An error occurred while checking if a user already exists<br>";
	//             print $ret2->getAsHtml();
	//        }
	//         print "An error occurred while trying to initialize G2<br>";
	//         print $ret->getAsHtml();
	//        exit;
	//     }
	//    }
	//  /* At this point we know that either the user either existed already before or that it was just created
	//   * proceed with the normal request to G2 */
	//  $data = GalleryEmbed::handleRequest();
	//  /* print $data['bodyHtml'] etc.... */
	return $data;
}
?>
