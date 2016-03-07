<?php
/**
 * Created by PhpStorm.
 * User: yannick
 * Date: 06.03.2016
 * Time: 18:17
 *
 * Hier kommt alles an schönen Funktionen rein, die nicht in eine Klasse passen
 * Alle Funktionen bitte static machen
 */
namespace Entrance;

 class Util {

     /**
      * @return bool|\Entrance\User
      */
     public static function checkSession() {
         session_start();
         if(!isset($_SESSION["uID"])) {
             self::forwardTo("logon.php?badsession=1");
             exit;
         } else {
             $user = User::fromUID($_SESSION["uID"]);
             if($_GET["m"] == "debug") {
                 echo "<pre style='display: block; position: absolute'>\n";
                 echo "[0] Perm Array Information:\n";
                 var_dump($user->getPermAsArray());
                 echo "\n[1] Permission Information:\n";
                 self::printPermission($user);
                 echo "\n[2] User Information:\n";
                 echo $user->toString();
                 echo "\n[3] Client Information:\n";
                 echo "    Arguments: ".$_SERVER["REQUEST_URI"]."\n";
                 echo "    Req Time : ".$_SERVER["REQUEST_TIME"]."ns\n";
                 echo "    Remote IP: ".$_SERVER["REMOTE_ADDR"]."\n";
                 echo "    Usr Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n";
                 echo "</pre>\n";
             }

             return $user;
         }
     }

     /**
      * @param $user \Entrance\User
      */
     public static function printPermission($user) {
         $consts = self::returnConstants("PERM");
         foreach ($consts as $const) {
             echo "    ".$const.": ".(($user->isActionAllowed($const)) ? 'on' : 'off')."\n";
         }
     }

     /**
      * @param $prefix
      * @return array
      */
     public static function returnConstants($prefix) {
         foreach (get_defined_constants() as $key=>$value)
             if (substr($key,0,strlen($prefix))==$prefix)  $dump[$key] = $value;
         if(empty($dump)) { return "Error: No Constants found with prefix '".$prefix."'"; }
         else { return $dump; }
     }

     /**
      * Forwards the user to a specific url
      *
      * @param $url
      */
     public static function forwardTo($url) {
         echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\" />";
     }

     /**
      * @param $title String
      * @param $user \ICMS\User
      * @return array
      */
     public static function getEditorPageDataStub($title, $user) {
         return [
             "header" => [
                 "title" => $title,
                 "usrname" => $user->getUName(),
                 "usrchar" => substr($user->getUName(), 0, 1),
                 "uID" => $user->getUID(),
                 "level" => $user->getUPrefix(),
                 "perm" => $user->getPermAsArray()
             ],
             "perm" => $user->getPermAsArray()
         ];
     }
 }