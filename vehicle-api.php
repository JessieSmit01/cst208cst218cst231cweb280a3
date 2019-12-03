<?php
/**************************************
 * File Name: Vehicle.php
 * User: cst208 Tara Epp, cst218 Carson Kearns, Jessie Smith
 * Date: 2019-11-11/27/2019
 * Project: CWEB280 A3
 *
 * the api for the vehicles-ui. Sends json and appropriate status codes
 *
 **************************************/
require_once "..\..\lib\Vehicle.php"; //will need to use the vehicle class in the lib folder
require_once "..\..\lib\ORM\Repository.php"; //will need to access the repository class in the lib/ORM folder

//$_REQUEST contains all params from $_GET and $_POST - we can check if any params exist in $_REQUEST if not use php://input
$requestData = empty($_REQUEST) ? json_decode(file_get_contents('php://input'), true) : $_REQUEST;

//instantiate repository object. point it to the Vehicle.db in this folder
$repo = new \ORM\Repository("Vehicle.db");

//switch case on the request method that has been received by the API
switch($_SERVER['REQUEST_METHOD']){
    case "GET": //we got a GET request
        $resultToJSONEncode = handleGET($repo, $requestData['searchfor']); //call this method to handle the GET request
        break;
    case "POST": //we got a POST request
        //deserialize JSON into Vehicle object
        // call handle post function
        //parse request data into Vehicle object
        //parse requestData into a new Vehicle object
        $vehicle = (new Vehicle())->parseArray($requestData);
        //handle the post method
        $resultToJSONEncode = handlePOST($vehicle, $repo);
        break;

    case "PUT": //we got a PUT request
        //parse the requestData into a new Vehicle object
        $vehicle = (new Vehicle())->parseArray($requestData);
        //call the method to handle the put request and assign the returned data to resultToJSONEncode
        $resultToJSONEncode = handlePUT($vehicle, $repo);
        break;
    default: //default case if the request method does not match any of the above
        $resultToJSONEncode = 'METHOD NOT SUPPORTED';
        header('http/1.1 405 Method Not Allowed');
}


//GET- ALL Vehicles from DB
/**
 * This method will handle the Get request. It will search the Vehicle.db for vehicles matching the search string
 * @param $repo - the current repo object
 * @param $searchString - string to search the db with
 * @return mixed - returns the vehicles matching the search string, else returns the last statement ran if an error occurred, otherwise returns null if no Vehicles match the criteria
 */
function handleGET($repo, $searchString)
{
    $searchString = htmlentities($searchString); //sanitise the search string

    $vehicle = new Vehicle();
    //if search string is empty or null - just pass in the empty vehicle
    //if the search string contains a value then add the wilcard characters and set the value to all text
    if(!empty($searchString))
    {
        $vehicle->make = '%' . $searchString . '%';
        $vehicle->model = '%' . $searchString . '%';
        $vehicle->type = '%' . $searchString . '%';
        $vehicle->year = '%' . $searchString . '%';
    }

    //put true so that the select will use OR instead of AND
    $result = $repo->select($vehicle, true); //empty string will return all Vehicles in the database
    if(!is_array($result)) //not an array so it means we got -2, -1, 0 - all of them are errors
    {
        header("http/1.1 418 I'm a Teapot");
        $result = $repo->getLastStatement();
    }elseif(empty($result)){ //no Vehicles meet the criteria
        header("http/1.1 404 Not Found");
    }
    //return the result of the search
    return $result;
}

/**
 * This method will handle the POST request.
 * The Vehicle taken in will be added in the database if no validation errors occur AND no SQL errors occur
 * @param $vehicle -the vehicle to add to the db
 * @param $repo - the current repo object
 * @return mixed - returns the vehicle added if successful, last sql statement ran if error occurred, or the validation errors if a validation error occurred.
 */
function handlePOST($vehicle, $repo){
    //Sanitise the vehicle variable values
    $vehicle->make = htmlentities($vehicle->make);
    $vehicle->model = htmlentities($vehicle->model);
    $vehicle->type = htmlentities($vehicle->type);
    $vehicle->year = htmlentities($vehicle->year);

    //validate all vehicle attributes
    $result = $vehicle->validate();
    if(count($result))//check if any validation errors occurred
    {
        //set the header as Unproccessable entity (because the object must meet all validation requirements)
        header("http/1.1 422 Unprocessable Entity");

    }
    elseif($repo->insert($vehicle) < 1) //database error if less than 1 is returned by insert function
    {
        //set the header to Im a Teapot
        header("http/1.1 418 I'm a Teapot");
        //return the last statement ran (for debugging)
        $result = $repo->getLastStatement();
    }
    else{ //the object was successfully created and added to the database
        //set the header to display that the object has been created
        header("http/1.1 201 Created");
        $result = $vehicle; //set the result to the newly added vehicle to return it back to the front end
    }
    return $result; //return the result of the post

}

/**
 * This method will handle the put request.
 * It will take in the vehicle to update and update the given vehicle in the db if no validation or sql errors occur
 * @param $vehicle - the vehicle to update
 * @param $repo - the current repo object
 * @return mixed - returns the vehicle if successful update, the last sql statement ran if an sql error occurred, or the validation errors if any fields violate the validation.
 */
function handlePUT($vehicle, $repo)
{
    //Sanitise the vehicle variable values
    $vehicle->make = htmlentities($vehicle->make);
    $vehicle->model = htmlentities($vehicle->model);
    $vehicle->type = htmlentities($vehicle->type);
    $vehicle->year = htmlentities($vehicle->year);

    //validate the vehicle taken
    $result = $vehicle->validate();
    if(count($result))//error occurred when validating
    {
        //set th header to Unprocessable Entity
        header("http/1.1 422 Unprocessable Entity");

    }
    elseif($repo->update($vehicle) < 1) //database error if less than 1 is returned by insert function
    {
        //set the header to I'm a Teapot
        header("http/1.1 418 I'm a Teapot");
        $result = $repo->getLastStatement(); //send back the last sql statement to help us debug the issue
    }
    else{ //the vehicle was successfully updated within the database
        //header("http/1.1 200 ok"); - default status code from most websites.
        $result = $vehicle; //to send back the edited vehicle as success indicator
    }
    //return the result
    return $result;
}

//let the API know that it needs to send back the content in JSON format
header('Content-type:application/json');

//send back the result of the request method.
//encoded in JSON.
echo json_encode($resultToJSONEncode);