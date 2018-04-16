<?php
/**
 * Users are redirected to this page when they log into the testing server. We
 * make sure they understand that they are on a different server now.
 */
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$idcookie = User::id();
// initialize page classes
$thispage = new PlansPage('Beta', 'beta_warning', PLANSVNAME . ' - Beta', 'beta_warning.php');
// If user is not authorized, turn them away.
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to view this as a guest.', 'Access Denied');
    $thispage->append($denied);
} else {
    // If they've confirmed, send them along to home
    if (isset($_POST['accept_beta']) && $_POST['accept_beta'] == 1) {
        $_SESSION['accept_beta'] = true;
        Redirect('home.php');
        return;
    }
    populate_page($thispage, $dbh, $idcookie);
    $heading = new HeadingText('This is a testing version of Plans', 1);
    $warning = new AlertText('Any changes you make here will not take effect on the main Plans server. Feel free to play around.', "We're in beta");
    $thispage->append($heading);
    $thispage->append($warning);
    $betaform = new Form('beta', true);
    $thispage->append($betaform);
    $betaform->method = 'POST';
    $item = new HiddenInput('accept_beta', 1);
    $betaform->append($item);
    $confirm = new SubmitInput('I Understand');
    $betaform->append($confirm);
    //TODO hardcoded?
    $gohome = new Hyperlink('go_home', true, 'http://www.grinnellplans.com', 'Never mind, take me back to regular Plans!');
    $thispage->append($gohome);
}
interface_disp_page($thispage);
?>
