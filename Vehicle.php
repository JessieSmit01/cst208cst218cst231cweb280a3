<?php
/**************************************
 * File Name: Vehicle.php
 * User: cst231
 * Date: 2019-11-27
 * Project: CWEB280
 *
 *
 **************************************/

//require once will allow this file to use code from another file
require_once 'ORM/Entity.php';

/**
 * Class Vehicle
 * This class will be used to store information on a Vehicle.
 * The information stored will include:
 *  -A vehicle ID
 *  -The vehicle make
 *  -The vehicle model
 *  -The vehicle type (Sedan, Compact, Cross Over, Truck
 *  -The vehicle year.
 *
 */
class Vehicle extends ORM\Entity
{
    //This vehicleID will be the primary key and unique identifier for the vehicle.
    //This will be auto incrementing
    /**
     *  This function will validate the vehicle id.
     * Vehicle id should be null when POSTING to database since the vehicleID will be an incrementing integer.
     * @return array - an array of errors
     *
     */
    public function validate_vehicleID()
    {
        $validationResult = [];
        //check to make sure that the vehicleID is an integer greater than 0
        //vehicleID must be null since the table will need to autoincrement the value of vehicleID from the previous record if one exists
        if($this->vehicleID != null && $this->vehicleID <= 0) //vehicle id is not an int or less than or equal to zero or isn't null - invalid
        {
            $validationResult ['vehicleID'] = $this->getDisplayName('vehicleID') . ' must be a unique autoincrementing integer greater than zero';
        }
        return $validationResult;
    }
    public $vehicleID;

    /**
     * This function will validate the vehicle make.
     * It will check for an empty string or null
     * it will also ensure the make is no longer than 25 characters long
     * @return array - an array or errors
     */
    public function validate_make()
    {
        $validationResult = [];
        //check if make is empty string or null
        if(empty(trim($this->make)) || $this->make === null)
        {
            //add the error message if empty
            $validationResult ['make'] = $this->getDisplayName('make') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 25 characters. If it is, add an error message
        else if(strlen($this->make) > 25) {$validationResult ['make'] = $this->getDisplayName('make') . ' cannot be greater than 25 characters';}
        //return any errors
        return $validationResult;
    }
    //this will keep track of vehicle make
    //25 characters max, required field
    public $make;

    /**
     * This function will validate the vehicle model
     * It will check for empty string value or null
     * it will also ensure that the length of the string is no longer than 25 characters
     * @return array - an array of errors
     */
    public function validate_model()
    {
        $validationResult = [];
        //check if model is empty
        if(empty(trim($this->model))|| $this->model === null)
        {
            //add the error message if empty
            $validationResult ['model'] = $this->getDisplayName('model') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 25 characters. If it is, add an error message
        else if(strlen($this->model) > 25) {$validationResult ['model'] = $this->getDisplayName('model') . ' cannot be greater than 25 characters';}
        //return any errors
        return $validationResult;
    }
    //this will keep track of the model
    //25 characters max, required field
    public $model;

    /**
     * This function will validate the vehicle type
     * It will check for empty string or null
     * It will validate that the make matches the list of valid types allowed
     *  if not an error will be returned
     * It will also ensure the type is no longer than 10 characters
     * @return array - an array of errors
     */
    public function validate_type()
    {
        $validationResult = [];
        $validTypes = ['Sedan', 'Compact', 'Cross Over', 'Truck'];
        //check if type is empty
        if(empty(trim($this->type)) || $this->type === null)
        {
            //add the error message if type is empty
            $validationResult ['type'] = $this->getDisplayName('type') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 10 characters. If it is, add an error message
        else if(!in_array($this->type, $validTypes)){$validationResult ['type'] = $this->getDisplayName('type') . ' must be either Sedan, Compact, Cross Over, or Truck';}
        else if(strlen($this->type) > 10) {$validationResult ['type'] = $this->getDisplayName('type') . ' cannot be greater than 10 characters';}
        //return any errors
        return $validationResult;
    }
    //keeps track of vehicle type (example: Sedan, Compact, Truck)
    //10 characters max.
    public $type;

    /**
     * This function will validate the vehicle year
     * Null values are allowed
     * Will check to make sure that the value for year turns into a valid date (if any character is entered besides an integer, vDate will equal false
     *  meaning the year was invalid
     * Also compares the year to the current year plus two years
     *  The vehicle year must be less than the current year plus 2 years.
     * @return array - an array of errors
     */
    public function validate_year()
    {
        $validationResult = [];
        if($this->year === null || $this->year === '') //check if year is null or empty - it is not required so just return no errors
        {
            return $validationResult;
        }
        //https://www.w3schools.com/php/php_date.asp - referenced this web site to help me in creating and comparing date time objects in PHP
        //strtotime takes in a string and turns the string into a date time
        $vDate = strtotime("January 1 " . $this->year);
        //the upper bound is equal to the current year plus to years
        //for the upperBoundDate - I used strtotime to take in the current time and the string of '+2 years' so that strtotime knows to create a
        //date of the current time and add 2 years to it.
        //turn the vehicle year into a date object with the year provided and have the month and day be January 1st of that year
        $upperBoundDate = strtotime("+2 Years", time());

        //when debugging I noticed that the datetime objects are turned to an int for comparing
        //ensure that the year is not null and greater than 0
        if( $vDate === false || $this->year < 0)
        {
            $validationResult ['year'] = $this->getDisplayName('year') .  ' must be an integer greater than or equal to 0';
        }

        //check to make sure that the vehicle year is less than the upperBoundDate (current year plus 2 years)
        else if(!($vDate < $upperBoundDate)) //Used https://www.w3schools.com/php/php_date.asp to understand how to get the current date
        {
            $validationResult ['year'] = $this->getDisplayName('year') . ' must be less than the current year plus 2 years';
        }

        return $validationResult;
    }
    //Keeps track of vehicle year
    //is an int
    //must be less than current year plus 2
    public $year;


    /**
     * Vehicle constructor. This will load all the column definitions for each variable in this object
     * It will also set the display names for this object
     */
    public function __construct()
    {
        //here the developer can set any protected variables
        //vehicleID will be stored as an integer, will be the primary key and also autoincrement
        $this->addColumnDefinition("vehicleID", "INTEGER", "PRIMARY KEY AUTOINCREMENT"); //primary key
        //make will be an nvarchar with a max length of 25 and must not be null
        $this->addColumnDefinition("make", "nvarchar(25)", "not null"); //required
        //model must be an nvarchar with max length of 25 and must not be null
        $this->addColumnDefinition("model", "nvarchar(25)", "not null"); //required
        //type must be an nvarchar with max length of 10 and not be null
        $this->addColumnDefinition("type", "nvarchar(10)", "not null"); //required
        //year must be an integer
        $this->addColumnDefinition("year", "INTEGER", ""); //not specified as required


        //add display names for each field
        $this->displayNames = [
            'vehicleID' => 'Vehicle ID',
            'make' => 'Make',
            'model' => 'Model',
            'type' => 'Type',
            'year' => 'Year',
        ];
    }


}