<?php
/**
 * Created by PhpStorm.
 * User: yanni
 * Date: 07.03.2016
 * Time: 15:30
 */

error_reporting(E_ERROR);
ini_set("diplay_errors", "on");

require_once 'classes/PDO_MYSQL.php'; //DB Anbindung
require_once 'libs/dwoo/lib/Dwoo/Autoloader.php'; //Dwoo Laden
require_once 'classes/User.php';
require_once 'classes/Permissions.php';
require_once 'classes/Util.php';
require_once 'classes/Citizen.php';

$user = \Entrance\Util::checkSession();
$pdo = new \Entrance\PDO_MYSQL();
Dwoo\Autoloader::register();
$dwoo = new Dwoo\Core();

$action = $_GET['action'];
$cID    = $_GET['cID'];

if($action == "new") {
    if ($user->isActionAllowed(PERM_CITIZEN_CREATE)) {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/citizenNew.tpl", $pgdata);
        exit; //To not show the list
    } else {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
    }
} elseif($action == "edit" and is_numeric($cID)) {
    if ($user->isActionAllowed(PERM_CITIZEN_EDIT)) {
        $citizenToEdit = \Entrance\Citizen::fromCID($cID);
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $pgdata["edit"] = $citizenToEdit->asArray();
        $dwoo->output("tpl/citizenEdit.tpl", $pgdata);
        exit; //To not show the list
    } else {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
    }
} elseif($action == "postNew") {
    if ($user->isActionAllowed(PERM_CITIZEN_CREATE)) {
        $citizenToEdit = \Entrance\Citizen::createCitizen($_POST['firstname'], $_POST['lastname'], $_POST['classlevel'], $_POST['birthday'], $_POST['barcode']);
        \Entrance\Util::forwardTo("citizen.php");
        exit;
    } else {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
    }
} elseif($action == "postEdit" and is_numeric($cID)) {
    if($user->isActionAllowed(PERM_CITIZEN_EDIT)) {
        $citizenToEdit = \Entrance\Citizen::fromCID($cID);
        $citizenToEdit->setBarcode($_POST['barcode']);
        $citizenToEdit->setFirstname($_POST['firstname']);
        $citizenToEdit->setLastname($_POST['lastname']);
        $citizenToEdit->setBirthday($_POST['birthday']);
        $citizenToEdit->setClasslevel($_POST['classlevel']);

        $citizenToEdit->saveChanges();
        \Entrance\Util::forwardTo("citizen.php?action=edit&cID=".$cID);
        exit;
    } else {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
    }
} elseif($action == "del" and is_numeric($cID)) {
    if($user->isActionAllowed(PERM_CITIZEN_DELETE)) {
        $userToDelete = \Entrance\User::fromUID($cID);
        $userToDelete->delete();
        \Entrance\Util::forwardTo("citizen.php");
        exit;
    } else {
        $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
        $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
    }
}

if($user->isActionAllowed(PERM_CITIZEN_VIEW)) {
    $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
    $citizens = \Entrance\Citizen::getAllCitizen();
    for ($i = 0; $i < sizeof($citizens); $i++) {
        $pgdata["page"]["items"][$i] = $citizens[$i]->asArray();
    }

    $dwoo->output("tpl/citizenList.tpl", $pgdata);
} else {
    $pgdata = \Entrance\Util::getEditorPageDataStub("Schüler", $user);
    $dwoo->output("tpl/noPrivileges.tpl", $pgdata);
}