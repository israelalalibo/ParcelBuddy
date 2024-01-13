<?php

namespace Models;
require_once ('Models/Database.php');

/**
 * This class handles oll the verification/checks on the login details of the user before logging in
 */
class LoginChecksDataSet
{
    private $username;
    private $password;
    protected $_dbHandle;
    private $_dbInstance;


    public function  __construct($uname, $pwd){
        $this->username = $uname; //
        $this->password = $pwd;
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getDbConnection();
    }

    /**
     * @return bool
     * Checks to see if the user has tried to log in without entering a field
     */
    public function isEmpty(){
        $result=null;
        if (empty($this->username) || empty($this->password)){
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;
    }

    /**
     * @return bool
     * Checks if username entered by user is in database
     *
     */
    public function checkUserName(){  //
        $result = null;
        $query = "SELECT * FROM site_user WHERE username LIKE '$this->username';";
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute();

        if ($statement->rowCount() > 0){ //if there is atleast one row of data returned
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;
    }

    /**
     * @return bool
     * true if password is found. False if password is not found
     */
    public function checkPassword(){  //checks if password is in database  passwd = '$this->password' AND

        $result = null;
        $query = "SELECT passwd FROM site_user WHERE  username = '$this->username';";
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute();

        $row = $statement->fetch();
        //$hashed_pwd_from_database = $row['passwd']; and password_verify($this->password, $row['passwd']) !== null

        if ($statement->rowCount() > 0 && password_verify($this->password, $row['passwd'])){
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;
    }

    /**
     * @return string
     * Checks if the user trying to log in is a Manager or a deliverer
     */
    public function checkUserType(){ //checks if user is a deliverer or manager
        $usertype = "";
        $query = "SELECT Usertype FROM site_user WHERE username LIKE '$this->username';";
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute();

        if ($statement->fetchColumn(0) == 1){
            $usertype = "1";
        }
        else{
            $usertype = "2";
        }
        //return 1 for manager or 2 for deliverer
        return $usertype;
    }

    /**
     * @return mixed
     * fetches the userID of a deliverer based on their username
     * checks if user is a deliverer or manager
     */
    public function getUserID()
    { //
        $userID = "";
        $query = "SELECT userID FROM site_user WHERE username LIKE '$this->username';";
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute();

        $row = $statement->fetch();
        //return 1 for manager or 2 for deliverer
        return $row['userID'];
    }
}