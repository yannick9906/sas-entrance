<?php
/**
 * Created by PhpStorm.
 * User: Janik
 * Date: 07.03.2016
 * Time: 23:21
 */

namespace Entrance;
use PDO;

const ESORTING = [
    "ascID"    => " ORDER BY eID ASC",
    "ascDate"  => " ORDER BY `timestamp` ASC",
    "descDate" => " ORDER BY `timestamp` DESC",
    "descID"   => " ORDER BY eID DESC",
    "" => ""
];

const EFILTERING = [
    ""          => "",
    "Alle"      => "",
    "INAlreadyCheckedIn"     => " WHERE errorcode = 1 ",
    "OUTAlreadyCheckedOut"   => " WHERE errorcode = 2 ",
    "OUTNoCheckOutYesterday" => " WHERE errorcode = 3 ",
    "INCitizenLocked"        => " WHERE errorcode = 4 ",
    "OUTCitizenLocked"       => " WHERE errorcode = 5 ",
    "INCitizenWanted"        => " WHERE errorcode = 6 ",
    "OUTCitizenWanted"       => " WHERE errorcode = 7 ",
    "INNoCitizenFound"       => " WHERE errorcode = 8 ",
    "OUTNoCitizenFound"      => " WHERE errorcode = 9 ",
];

class Error{
    private $eID, $cID, $timestamp, $errorcode, $active;

    /**
     * Error constructor.
     * @param $eID
     * @param $cID
     * @param $timestamp
     * @param $errorcode
     * @param $active
     */
    public function __construct($eID, $cID, $timestamp, $errorcode, $active) {
        $this->eID = $eID;
        $this->cID = $cID;
        $this->timestamp = $timestamp;
        $this->errorcode = $errorcode;
        $this->active = $active;
    }

    /**
     * @param $eID
     * @return Error
     */
    public static function fromEID($eID) {
        $pdo = new PDO_MYSQL();
        $res = $pdo->query("SELECT * FROM entrance_error WHERE eID = :eid", [":eid" => $eID]);
        return new Error($res -> eID, $res -> cID, $res -> timestamp, $res -> errorcode, $res -> active);
    }

    /**
     * @param string $sort
     * @param string $filter
     * @return Error[]
     */
    public static function getAllErrors($sort = "", $filter = ""){
        $pdo = new PDO_MYSQL();
        $stmt = $pdo->queryMulti("SELECT eID FROM entrance_error ORDER BY eID ".EFILTERING[$filter].ESORTING[$sort]);
        return $stmt->fetchAll(PDO::FETCH_FUNC, "\\Entrance\\Error::fromEID");
    }

    /**
     * @param $cID
     * @return Error[]
     */
    public static function getErrorsByCitizen($cID){
        $pdo = new PDO_MYSQL();
        $stmt = $pdo->queryMulti("SELECT eID FROM entrance_error WHERE cID = :cid ORDER BY eID", [":cid" => $cID]);
        return $stmt->fetchAll(PDO::FETCH_FUNC, "\\Entrance\\Error::fromEID");
    }

    /**
     * @param $cID int
     * @param $errorcode int
     */
    public static function createError($cID, $errorcode){
        $pdo = new PDO_MYSQL();
        $date = date("Y-m-d H:i:s");
        $pdo->query("INSERT INTO entrance_error(cID,`timestamp`, errorcode) VALUES (:cID, :timestamp, :errorcode)",
            [":cID" => $cID, ":timestamp" => $date, ":errorcode" => $errorcode]);
    }

    /**
     * @return mixed
     */
    public function delete(){
        $pdo = new PDO_MYSQL();
        return $pdo->query("DELETE FROM entrance_error WHERE eID = :eid", [":eid" => $this->eID]);
    }
    /**
     * @param $cID int
     */
    public static function correctError($cID){
        $pdo = new PDO_MYSQL();
        $pdo -> query("UPDATE entrance_error SET active = 0 WHERE cID = :cID", [":cID" => $cID]);
    }

    public function correctThisError(){
        $pdo = new PDO_MYSQL();
        $pdo -> query("UPDATE entrance_error SET active = 0 WHERE eID = :eID", [":eID" => $this -> eID]);
    }

    public function asArray() {
        return[
            "eID" => $this -> eID,
            "cID" => $this -> cID,
            "citizenName" => Citizen::fromCID($this->cID)->getFirstname()." ".Citizen::fromCID($this->cID)->getLastname(),
            "citizenClassLevel" => Citizen::fromCID($this->cID)->getClasslevel(),
            "errorStatus" => $this -> active,
            "timestamp" => date("d. M Y - H:i", strtotime($this->timestamp)),
            "errorCode" => $this -> errorcode
        ];
    }
    /**
     * @return mixed
     */
    public function getEID(){
        return $this->eID;
    }

    /**
     * @param mixed $eID
     */
    public function setEID($eID){
        $this->eID = $eID;
    }

    /**
     * @return mixed
     */
    public function getCID(){
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setCID($cID){
        $this->cID = $cID;
    }

    /**
     * @return mixed
     */
    public function getTimestamp(){
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp){
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getErrorcode(){
        return $this->errorcode;
    }

    /**
     * @param mixed $errorcode
     */
    public function setErrorcode($errorcode){
        $this->errorcode = $errorcode;
    }

    /**
     * @return mixed
     */
    public function getActive(){
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active){
        $this->active = $active;
    }



}