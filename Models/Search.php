<?php

namespace Models;
require_once ('DeliveryPointSet.php');
class Search extends DeliveryPointSet
{
    public $searchInput;

    public function __construct(){
        $this->searchInput = "";
    }

    /**
     * @return void
     * handle all the search functionality when the search button is pressed
     * create the sInput session variable to be used in the search sql query
     */
    public function search(){
        $this->searchInput = htmlentities($_POST['searchInput']);
        $_SESSION['sInput'] = $this->searchInput;
        $_SESSION['hasSearched'] = true;
    }
}