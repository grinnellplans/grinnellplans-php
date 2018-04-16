<?php
require_once ('Plans.php');
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'interfaces', PLANSVNAME . ' - Interfaces', 'interfaces.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} //end guest display
else
//allowed to edit
{
    populate_page($thispage, $dbh, $idcookie);
    $heading = new HeadingText('Interface Options', 2);
    $thispage->append($heading);
    $displaysettings = User::get()->Display;
    if (isset($_POST['interface']) && ($newinter = Doctrine_Query::create()->select("interface")->from("OutputInterface i")->where("interface = ?",$_POST['interface'])->fetchOne()))
    {
        $displaysettings->Interface = $newinter;
        $displaysettings->save();
        //Let user know the interface has been set
        $message = new InfoText('Interface set.', 'Success');
        $thispage->append($message);
    } else
    //if not submitted, give form
    {
        $currentinterface = User::get()->Display->interface;
        //begin the form
        $interfaceform = new Form('interfacesform', true);
        $thispage->append($interfaceform);
        $interfaces = Doctrine_Query::create()->select("interface,descr")->from("OutputInterface i")->execute();
        foreach ($interfaces as $interface)
        {
            $item = new RadioInput('interface', $interface->interface);
            $item->checked = ($currentinterface == $interface->interface);
            $item->description = $interface->descr;
            $interfaceform->append($item);
        }
        $item = new SubmitInput('Change');
        $interfaceform->append($item);
        //end form
        
    }
} //if is a valid user
interface_disp_page($thispage);
?>
