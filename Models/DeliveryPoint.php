<?php

namespace Models;

class DeliveryPoint extends DeliveryPointSet
{
    protected $delPointID, $recipient_name, $address1,$address2,
        $postcode, $deliverer, $lat, $long, $del_status, $del_photo,
    $_dbInstance, $_dbHandle;

    /**
     * @param $dbRow
     * Constructor for DeliveryPoint Object which takes the coloumns of a row of data and
     * initialises an object with the data
     */
    public function __construct($dbRow){
        $this->delPointID = $dbRow['delPointID'];
        $this->recipient_name = $dbRow['recipient_name'];
        $this->address1 = $dbRow['adress1'];
        $this->address2 = $dbRow['address2'];
        $this->postcode = $dbRow['postcode'];
        $this->deliverer = $dbRow['Deliverer'];
        $this->lat = $dbRow['lat'];
        $this->long = $dbRow['lng'];
        $this->del_status = $dbRow['Del_status'];
        //$this->del_photo = $dbRow[''];

        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

    }
    //accessor methods to access the field of the DeliveryPoint instance/object

    /**
     * @return mixed
     * return the id of the Delivery Point object
     */
    public function getDelPointID(){
        return $this->delPointID;
    }

    /**
     * @return mixed
     * return the name of the recipient of the delivery
     */
    public function getRecipientName(){
        return $this->recipient_name;
    }

    /**
     * @return mixed
     * return the first address line
     */
    public function getAddress1(){
        return $this->address1;
    }

    /**
     * @return mixed
     * return the second line of delivery address
     */
    public function getAddress2(){
        return $this->address2;
    }

    /**
     * @return mixed
     * return the postcode of delivery point object
     */
    public function getPostcode(){
        return $this->postcode;
    }

    /**
     * @return mixed
     * return the username of the Deliverer
     */
    public function getDeliverer(){
        return $this->fetchDeliverer($this->deliverer);
    }

    /**
     * @return mixed
     * return the latitude of the Delivery Point
     */
    public function getLatitude(){
        return $this->lat;
    }

    /**
     * @return mixed
     * return the longitude of the Delivery Point object
     */
    public function getLongitude(){
        return $this->long;
    }

    /**
     * @return mixed
     * return the status of the delivery
     */
    public function getDel_status(){
         return $this->fetchStatus($this->del_status);
    }

    /**
     * @return mixed
     * return the photo of status of the delivery(i.e Ordered, Shipped, Delivered)
     */
    public function getDelPhoto(){
        return $this->fetchStatusLink($this->del_status);
    }

}