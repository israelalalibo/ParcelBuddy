<?php

session_start();

use Models\DeliveryPointSet;
use Models\LoginChecksDataSet;
use Models\Search;

require_once('Models/User.php');
require_once('Models/LoginChecksDataSet.php');
require_once ('Models/Search.php');

$view = new stdClass();
$view->pageTitle = 'Parcel Buddy Log In';

global $user;
$search = new Search();
$userSet = new DeliveryPointSet();

//values will be changed to the appropriate error messages if there is an error logging in
$view->username_error = null;
$view->password_error = null;
$view->emptyFields_error = null;

//$_SESSION['isLoggedIn'] = false;  //was used as a debugging tool
/**
 * if user is not logged in, encode the inputs to prevent cross-site-scripting
 * An instance of LoginChecksDataSet is created and its constructor is being passed the user log in details
 * The LoginChecksDataSet then carries out the various credibility checks via its methods
 *
 * and then require the login view
 */
if (isset($_POST["login"])) {
    $name = htmlentities($_POST['username']);
    $pwd = htmlentities($_POST['password']);

    $loginCheck = new LoginChecksDataSet($name, $pwd);
    if ($loginCheck->isEmpty()) {
        $view->emptyFields_error = "Fields cannot be empty";
    }
    else {
        if ($loginCheck->checkUserName()) {
            if ($loginCheck->checkPassword()) { //checks if username matches with password
                $_SESSION['username'] = $name;
                $_SESSION['pwd'] = $pwd;
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['id'] = $loginCheck->getUserID();
                $_SESSION['current_page_number'] = 1;
                if ($loginCheck->checkUserType() == "1") { //checks if user is a manager
                    $_SESSION['userType'] = "M";
                    $_SESSION['rows_per_page'] = 10;  //sets the amount of rows to display at a time
                    $_SESSION['start'] = 0;  //sets the initial OFFSET value to zero (to NOT skip first record)
                    require_once("Manager.php");
                } elseif ($loginCheck->checkUserType() == "2") { //checks if user is a delivery driver :)
                    $_SESSION['userType'] = "D";
                    $_SESSION['rows_per_page'] = 10;
                    $_SESSION['start'] = 0;
                    require_once("DeliveryUser.php");
                }
            } else {
                $view->password_error = "Password is incorrect";
                //echo "password does not match ";   //was used as a debugging tool
            }
        } else {
            $view->username_error = "Username not found";
            //echo "username not found";      //was used as a debugging tool
        }
    }
}

/**
 * Checks to see if the isLoggedIn session has been set to false
 * Shows the login page as false indicates the user is not logged in.
 */
if(!$_SESSION['isLoggedIn']){
    require('Views/index.phtml');
}

/**
 * Checks to see if the search button in the header has been pressed.
 * If it has been pressed the appropriate controller is required based on the identity of the user
 */
if (isset($_POST['searchButton'])){
    $search->search();
    if ($_SESSION['userType'] == "M"){
        require_once("Manager.php");
    }
    else {
        require_once("DeliveryUser.php");
    }
}

/**
 * Checks if the "Show all records" button has been pressed
 * If yes, all the previously displayed records are shown
 */
if(isset($_POST['resetSearch'])){
    unset($_SESSION['sInput']);
    unset($_SESSION['hasSearched']);

    if ($_SESSION['userType'] == "M") {
        require_once('Manager.php');
    } else {
        require_once('DeliveryUser.php');
    }
}

/**
 * Calls the AddEntry controller if and only if the Manager pressed the "AddEntry" button from
 * the header
 */
if (isset($_POST['preAddEntry'])){
    require_once ('AddEntry.php');
}

/**
 * Checks to see if the manager presses the add entry button after inputting the details for a new entry
 * Then grabs all the inputs and stores them in the database.
 * Since adding entries will lead to server interaction, the inputs are html encoded to
 * prevent cross-site-scripting attacks
 */
if (isset($_POST['addEntryBtn'])) {
    $receiver = htmlentities($_POST['receiver']);
    $add1 = htmlentities($_POST['address1']);
    $add2 = htmlentities($_POST['address2']);
    $postCode = htmlentities($_POST['postcode']);
    $status = 0;
    //Logic control to store the status selection from the dropdown menu
    if($_POST['status'] == "Ordered"){
        $status = 1;
    }
    if($_POST['status'] == "Packed"){
        $status = 2;
    }
    if($_POST['status'] == "In Transit"){
        $status = 3;
    }
    if($_POST['status'] == "Delivered"){
        $status = 4;
    }
    if($_POST['status'] == "Exception"){
        $status = 5;
    }
    $deliverer = $_POST['newDeliverer'];
    $lat = htmlentities($_POST['lat']);
    $lng = htmlentities($_POST['lng']);

    $userSet->addParcelEntry($receiver, $add1, $add2, $postCode, $deliverer, $lat, $lng, $status);
    $_SESSION['hasAddedEntry'] = true;
    require_once ('Manager.php'); //Call the Manager.php controller to show the edit changes made
}

/**
 * Calls the EditEntry controller when the manager presses edit button
 * from the list of parcel entries
 */
if (isset($_POST['editEntry'])){
    $_SESSION['ID_of_entry'] = $_POST['editEntry'];
    require_once ('EditEntry.php');
}

/**
 * Checks if the delete button is pressed from the list of parcel entries
 * If yes, then stored the ID of the entry to be deleted and calls the DeleteEntry controller
 */
if (isset($_POST['deleteEntry'])){
    $_SESSION['ID_of_entry'] = $_POST['deleteEntry'];
    require_once ('DeleteEntry.php');
}

/**
 * takes the manager back to the view of the parcel entries
 */
if (isset($_POST['returnFromEdit/Delete/Add'])){
    require_once ('Manager.php');
}

/**
 * Deletes the specified parcel entry and
 * goes back to the view of parcel entries
 */
if (isset($_POST['DELETE'])){
    $userSet->deleteSpecificEntry($_SESSION['ID_of_entry']);
    require_once ('Manager.php');
}

/**
 * checks to see if the manager has finished editing the parcel entry and
 * takes the logged in manager back to the view of parcel entries
 */
if (isset($_POST['editComplete'])){
    $receiver = htmlentities($_POST['receiver']);
    $add1 = htmlentities($_POST['address1']);
    $add2 = htmlentities($_POST['address2']);
    $postCode = htmlentities($_POST['postcode']);
    //Logic control to store the status selection from the dropdown menu
    $status = 0;
    if($_POST['status'] == "Ordered"){
        $status = 1;
    }
    if($_POST['status'] == "Packed"){
        $status = 2;
    }
    if($_POST['status'] == "In Transit"){
        $status = 3;
    }
    if($_POST['status'] == "Delivered"){
        $status = 4;
    }
    if($_POST['status'] == "Exception"){
        $status = 5;
    }
    $deliverer = $_POST['newDeliverer'];
    $lat = htmlentities($_POST['lat']);
    $lng = htmlentities($_POST['lng']);
    $userSet->editParcelEntry($receiver, $add1,$add2, $postCode, $deliverer,$lat, $lng, $status);

    require_once ('Manager.php');  //Call the Manager.php controller to show the edit changes made
}

/**
 * If a page number navigator button is pressed, the page number is stored in
 * the 'current_page_number' session variable
 * Some calculation is done to  determine the pagination logic
 * for all the potential pages of parcel entries
 * The results of this calculation is later used in the select
 * query to control how many rows of data is shown (LIMIT)
 * and what the OFFSET will be
 */
if(isset($_POST['page_nr'])){
    $_SESSION['current_page_number'] = $_POST['page_nr'];
    //calculating the OFFSET value
    $_SESSION['page'] = $_POST['page_nr'] - 1;
    $_SESSION['start'] = $_SESSION['rows_per_page'] * $_SESSION['page'];

    if ($_SESSION['userType'] == "M") {
        require_once('Manager.php');
    } else {
        require_once('DeliveryUser.php');
    }
}

/**
 * If the logout button has been pressed, unsets session then
 * sets the isLoggedIn session to false
 * (order of operations carried out is very important) then shows the login page
 */
if(isset($_POST['logout'])){
    session_unset();
    $_SESSION['isLoggedIn'] = false;
    require('Views/index.phtml');
}