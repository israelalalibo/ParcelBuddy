<?php

namespace Models;
require_once("Models/LoginChecksDataSet.php");

/**
 * Will be used to create an instance of a delivery user
 */
class User
{
    protected $username;
    public $_userID;

    public function __construct($dbRow){
        $this->username = $dbRow['username'];
    }


    /**
     * @return mixed
     * returns isLoggedIn value
     */
    public function getIsLoggedIn(){
        return $this->isLoggedIn;
    }

    public function getUserID(){
        return $this->_userID;
    }

    public function getUserName(){
        return $this->username;
    }
}