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
require_once "..\..\lib\Vehicle.php";
require_once "..\..\lib\ORM\Repository.php";

//$_REQUEST contains all params from $_GET and $_POST - we can check if any params exist in $_REQUEST if not use php://input
$requestData = empty($_REQUEST) ? json_decode(file_get_contents('php://input'), true) : $_REQUEST;

//instantiate repository object
$repo = new \ORM\Repository("Vehicle.db");

switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        $resultToJSONEncode = handleGET($repo, $requestData['searchfor']);
        break;
    case "POST":
        //deserialize JSON into Vehicle object
        // call handle post function
        //parse request data into Vehicle object
        $vehicle = (new Vehicle())->parseArray($requestData);
        //handle the post method
        $resultToJSONEncode = handlePOST($vehicle, $repo);
        break;

    case "PUT":
        //get student object from  db
        $vehicle = (new Vehicle())->parseArray($requestData);
        $resultToJSONEncode = handlePUT($student, $repo);
        break;
    default:
        $resultToJSONEncode = 'METHOD NOT SUPPORTED';
        header('http/1.1 405 Method Not Allowed');
}


//GET- ALL Students from DB
function handleGET($repo, $searchString)
{
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
    $result = $repo->select($vehicle, true); //empty vehicle will return all students in the database
    if(!is_array($result)) //not an array so it means we got -2, -1, 0 - all of them are errors
    {
        header("http/1.1 418 I'm a Teapot");
        $result = $repo->getLastStatement();
    }elseif(empty($result)){ //no students meet the criteria
        header("http/1.1 404 Not Found");
    }

    return $result;
}

//POST- New Student
function handlePOST($vehicle, $repo){

    //validate all vehicle attributes
    $result = $vehicle->validate();
    if(count($result))//error occurred
    {
        header("http/1.1 422 Unprocessable Entity");

    }
    elseif($repo->insert($vehicle) < 1) //database error if less than 1 is returned by insert function
    {
        header("http/1.1 418 I'm a Teapot");
        $result = $repo->getLastStatement();
    }
    else{
        header("http/1.1 201 Created");
        $result = $vehicle; //to send back the new generated id and username
    }
    return $result;

}

//PUT - Edit vehicle
function handlePUT($vehicle, $repo)
{

    $result = $vehicle->validate();
    if(count($result))//error occurred
    {
        header("http/1.1 422 Unprocessable Entity");

    }
    elseif($repo->update($vehicle) < 1) //database error if less than 1 is returned by insert function
    {
        header("http/1.1 418 I'm a Teapot");
        $result = $repo->getLastStatement(); //send back the last sql statement to help us debug the issue
    }
    else{
        //header("http/1.1 200 ok"); - default status code from most websites
        $result = $vehicle; //to send back the edited student as success indicator
    }
    return $result;
}



header('Content-type:application/json');
echo json_encode($resultToJSONEncode);
