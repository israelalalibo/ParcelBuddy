<?php

namespace Models;
require_once ('Models/DeliveryPoint.php');
require_once ("Models/Database.php");
require_once ("Models/User.php");

use Exception;
use PDOException;

//handles getting the deliveryPoint rows and holding instances of the rows of data
class DeliveryPointSet
{
    protected $_dbHandle, $_dbInstance;

    public function __construct(){
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

    }

    /**
     * @return array
     *Shows ALL the delivery entries in the database to the manager (regardless of the Deliverer)
     */
    public function fetchAllDeliveryPoints($rows_per_page, $start){
        $sqlQuery = "SELECT * FROM delivery_point LIMIT $rows_per_page OFFSET $start ;";
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute(); //execute the PDO statement
        $dataset = [];
        while ($row = $statement->fetch()){
            $dataset[] = new DeliveryPoint($row); //stores instances of Delivery Point in array
        }
        return $dataset;
    }


    /**
     * @param $uID
     * @return int
     * returns the number of rows of entries allocated to a specifies user (Deliverer)
     */
    public function countAllUserDeliveryPoints($uID){
        $sqlQuery = 'SELECT * FROM delivery_point WHERE Deliverer = ?;';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(1, $uID);
        $statement->execute();

        $number_of_user_delivery_points = [];
        while ($rows = $statement->fetch()){
            $number_of_user_delivery_points[] = $rows;
        }
        return count($number_of_user_delivery_points);
    }

    /**
     * @return int
     * Counts all the currently existing delivery points in the database
     * Returns the int value to be used else where
     */
    public function countAllDeliveryPoints(){
        $sqlQuery = 'SELECT * FROM delivery_point;';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();

        $number_of_delivery_points = [];
        while ($rows = $statement->fetch()){
            $number_of_delivery_points[] = $rows;
        }
        return count($number_of_delivery_points);
    }

    /**
     * @param $uID
     * @param $start
     * @param $rows_per_page
     * @return array
     * selects the delivery points associated with the logged-in user (Deliverer)
     * used when limiting the rows of data to be displayed
     */
    public function fetchUserDeliveryPoints($uID, $rows_per_page, $start){
        $sqlQuery = "SELECT * FROM delivery_point WHERE Deliverer = ? LIMIT $rows_per_page OFFSET $start;";
        //$statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }

        $statement->bindParam(1, $uID);
        try{
            $statement->execute(); //execute the PDO statement
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }

        $dataset = [];
        while ($row = $statement->fetch()){
            $dataset[] = new DeliveryPoint($row); //stores instances of Delivery Point in array
        }
        return $dataset;
    }

    /**
     * @param $delPointID
     * @return array
     * Fetches a specific row of data of parcel entry to be edited by the manager
     */
    public function fetchSpecificEntry($delPointID){
        $sqlQuery = 'SELECT * FROM delivery_point WHERE delPointID = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute([$delPointID]); //execute the PDO statement

        $dataset = [];
        $row = $statement->fetch();
        $dataset[] = new DeliveryPoint($row); //stores instances of Delivery Point in array
        return $dataset;
    }

    /**
     * @param $delPointID
     * @return void
     * deletes a specified parcel entry from the Database
     */
    public function deleteSpecificEntry($delPointID){
        $sqlQuery = 'DELETE FROM delivery_point WHERE delPointID = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->bindParam(1, $delPointID);
        $statement->execute();
    }


    /**
     * @param $statusID
     * @return mixed
     * Fetches the delivery status of the parcel entry from the database
     */
    public function fetchStatus($statusID){
        $sqlQuery = 'SELECT status_text FROM delivery_point, delivery_status WHERE status_code = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute([$statusID]); //execute the PDO statement

        //$dataset = [];
        //while ($row = $statement->fetch()){
        //stores instances of Delivery Point in array
        //}
        $row = $statement->fetch();
        return $row['status_text']; //returns the description of the delivery status
    }

    /**
     * @return array
     * queries the database and returns an array of the deliverers stored
     * To be used in a dropdown of deliverers to enable selection
     */
    public function fetchAllDeliverers(){
        $sqlQuery = 'SELECT username,Usertype FROM site_user WHERE Usertype = 2;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute(); //execute the PDO statement

        $dataset = [];
        while ($row = $statement->fetch()){
            $dataset[] = new User($row);
        }
        return $dataset; //returns array of delivery drivers
    }

    /**
     * @param $delUser
     * @return mixed
     * queries the database for a specific deliverer ID using their username
     */
    public function fetchUserIDs($delUser){
        $sqlQuery = 'SELECT userID FROM site_user WHERE username = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }

        $statement->execute([$delUser]); //execute the PDO statement

        $row = $statement->fetch();
        return $row['userID'];
    }

    /**
     * @param $receiver
     * @param $add1
     * @param $add2
     * @param $postcode
     * @param $deliverer
     * @param $lat
     * @param $lng
     * @param $status
     * @return void
     *
     * inserts into the delivery points table the new parcel entries
     */
    public function addParcelEntry($receiver, $add1, $add2, $postcode,$deliverer, $lat, $lng, $status){
        $deliveryUserID = $this->fetchUserIDs($deliverer);
        $sqlQuery1 = "INSERT INTO delivery_point (recipient_name, adress1, address2, postcode, 
        Deliverer, lat, lng, Del_status) VALUES 
        (?,?,?,?,?,?,?,?);";

        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery1);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->bindParam(1, $receiver);
        $statement->bindParam(2, $add1);
        $statement->bindParam(3, $add2);
        $statement->bindParam(4, $postcode);
        $statement->bindParam(5, $deliveryUserID);
        $statement->bindParam(6, $lat);
        $statement->bindParam(7, $lng);
        $statement->bindParam(8, $status);
        try{
            $statement->execute(); //execute the PDO statement
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
    }

    /**
     * @param $receiver
     * @param $add1
     * @param $add2
     * @param $postcode
     * @param $deliverer
     * @param $lat
     * @param $lng
     * @param $status
     * @return void
     * Updates the delivery_point table based on the edits made by the Manager to the parcel entries
     */
    public function editParcelEntry($receiver, $add1, $add2, $postcode,$deliverer, $lat, $lng, $status){
        $delivererUserID = $this->fetchUserIDs($deliverer);
        $sqlQuery2 = "UPDATE delivery_point 
                    SET recipient_name = ?, adress1 = ?, address2 = ?, postcode = ?, Deliverer = ?, lat = ?, lng = ?, Del_status = ? 
                      WHERE delPointID = ?;";
        $statement2 = null;
        try{
            $statement2 = $this->_dbHandle->prepare($sqlQuery2);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement2->bindParam(1, $receiver);
        $statement2->bindParam(2, $add1);
        $statement2->bindParam(3, $add2);
        $statement2->bindParam(4, $postcode);
        $statement2->bindParam(5, $delivererUserID);
        $statement2->bindParam(6, $lat);
        $statement2->bindParam(7, $lng);
        $statement2->bindParam(8, $status);
        $statement2->bindParam(9, $_SESSION['ID_of_entry']);
        try{
            $statement2->execute(); //execute the PDO statement
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
    }

    /**
     * @throws Exception
     * Fetches the searched parcel entries by the Deliverer
     * Takes two parameters, One to pre-filter the entries associated with the particular deliverer and the other
     * to actually narrow down the search result based on the inputted
     */
    public function fetchSearchedDeliveryPointsD($searchItem, $deliverer){
        $sqlQuery = "SELECT DISTINCT delPointID, recipient_name, adress1, address2, postcode, Deliverer, 
        lat, lng, Del_status 
                     FROM delivery_point INNER JOIN site_user ON delivery_point.Deliverer=site_user.userID 
                     INNER JOIN delivery_status ON delivery_point.Del_status = delivery_status.status_code
                     WHERE delivery_point.recipient_name LIKE ? OR delivery_point.adress1 LIKE ? 
                     OR delivery_point.address2 LIKE ? ORDER BY recipient_name;";

        return $this->getSearchItem($sqlQuery, $searchItem);

    }

    /**
     * @throws Exception
     * Fetches the searched parcel entries by the manager
     * Takes only one parameter which is the searched Item. There is no need to pre-filter the entries based on
     * the Deliverer as the Manager should be able to see all entries regardless of the deliverer
     */
    public function fetchSearchedDeliveryPointsM($searchItem){
        $sqlQuery = "SELECT DISTINCT delPointID, recipient_name, adress1, address2, postcode, 
                Deliverer, lat, lng, Del_status
                    FROM delivery_point  
                    INNER JOIN delivery_status ON delivery_point.Del_status = delivery_status.status_code
                    WHERE delivery_point.recipient_name LIKE ? OR delivery_point.adress1 LIKE ? 
                    OR delivery_point.address2 LIKE ? ORDER BY recipient_name;";

        return $this->getSearchItem($sqlQuery, $searchItem);
    }

    /**
     * @param $sqlQuery
     * @param $searchItem
     * @return array|mixed
     * @throws Exception
     *
     * Modularisation of the search method that actually does the searching. To reduce repetition of code.
     */
    public function getSearchItem($sqlQuery, $searchItem)
    {
        $statement = null;
        try {
            if ($this->_dbHandle === null) {
                throw new Exception("Database connection is not established");
            }
            $statement = $this->_dbHandle->prepare($sqlQuery);
            $searchedItem = "%" . $searchItem . "%";
            $statement->bindParam(1, $searchedItem);
            $statement->bindParam(2, $searchedItem);
            $statement->bindParam(3, $searchedItem);
            $statement->execute(); //execute the PDO statement

            $dataset = [];
            while ($row = $statement->fetch()) {
                $dataset[] = new DeliveryPoint($row); //stores instances of Delivery Point in array
            }
            return $dataset;
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
        //return $searchItem;
    }

    /**
     * @param $uID
     * @return mixed
     *
     * queries the database for a specific deliverer username using their userID Primary Key
     */
    public function fetchDeliverer($uID){
        $sqlQuery = 'SELECT username FROM site_user WHERE userID = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute([$uID]); //execute the PDO statement

        $row = $statement->fetch();
        return $row['username']; //returns the username of the deliverer
    }

    /**
     * @param $statusID
     * @return mixed
     * Utilised to select the image url from the database based on the status ID passed as the parameter
     */
    public function fetchStatusLink($statusID){
        $sqlQuery = 'SELECT status_link FROM delivery_point, delivery_status WHERE status_code = ?;';
        $statement = null;
        try{
            $statement = $this->_dbHandle->prepare($sqlQuery);
        }catch(PDOException $exception){
            echo $exception->getMessage();
        }
        $statement->execute([$statusID]); //execute the PDO statement
        $row = $statement->fetch();
        return $row['status_link']; //returns the description of the delivery status
    }
}