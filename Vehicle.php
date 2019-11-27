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
    public function validate_vehicleID()
    {
        $validationResult = [];
        //check to make sure that the vehicleID is an integer greater than 0
        //vehicleID must be null since the table will need to autoincrement the value of vehicleID from the previous record if one exists
        if(!is_int($this->vehicleID) || $this->vehicleID <= 0 || !$this->vehicleID == null) //vehicle id is not an int or less than or equal to zero or isn't null - invalid
        {
            $validationResult ['vehicleID'] = $this->getDisplayName('vehicleID') . ' must be a unique autoincrementing integer greater than zero';
        }
        return $validationResult;
    }
    public $vehicleID;
    //this will keep track of vehicle make
    //25 characters max, required field
    public function validate_make()
    {
        $validationResult = [];
        //check if make is empty
        if(empty(trim($this->make)))
        {
            //add the error message if empty
            $validationResult ['make'] = $this->getDisplayName('make') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 25 characters. If it is, add an error message
        else if(strlen($this->make) > 25) {$validationResult ['make'] = $this->getDisplayName('make') . ' cannot be greater than 25 characters';}
        //return any errors
        return $validationResult;
    }
    public $make;
    //this will keep track of the model
    //25 characters max, required field
    public function validate_model()
    {
        $validationResult = [];
        //check if model is empty
        if(empty(trim($this->model)))
        {
            //add the error message if empty
            $validationResult ['model'] = $this->getDisplayName('model') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 25 characters. If it is, add an error message
        else if(strlen($this->model) > 25) {$validationResult ['model'] = $this->getDisplayName('model') . ' cannot be greater than 25 characters';}
        //return any errors
        return $validationResult;
    }
    public $model;
    //keeps track of vehicle type (example: Sedan, Compact, Truck)
    //10 characters max.
    public function validate_type()
    {
        $validationResult = [];
        //check if type is empty
        if(empty(trim($this->type)))
        {
            //add the error message if type is empty
            $validationResult ['type'] = $this->getDisplayName('type') . ' cannot be empty or all spaces';
        }
        //check if the make it greater than 10 characters. If it is, add an error message
        else if(strlen($this->type) > 10) {$validationResult ['type'] = $this->getDisplayName('type') . ' cannot be greater than 10 characters';}
        //return any errors
        return $validationResult;
    }
    public $type;
    //Keeps track of vehicle year
    //is an int
    //must be less than current year plus 2
    public function validate_year()
    {
        $validationResult = [];

        //ensure that the year is an int and greater than 0
        if(!is_int($this->year) || $this->year < 0)
        {
            $validationResult ['year'] = $this->getDisplayName('year') .  ' must be an integer greater than 0';
        }
        //used https://www.w3schools.com/php/php_date.asp to understand the functions
        //mktime and strtotime
        //mktime takes in an integer for hour, minute, second, month, day, and year
        //1.since we only have the year, I set everything to null but the year
        //2.next we need to compare the given year to the current year to ensure the vehicle year
        //  is less than the current year plus 2 years (this is where strtostring comes handy
        //3.strtostring takes in a string and and a date
        //  it will use the string (in this case the string is '+2 years'
        //  and add two years to the date passed in.
        //4.Using those two date objects i can compare them and ensure the Vehicle year is valid
        else if(!mktime(null,null,null,null,null,$this->year) < strtotime("+2 Years", date("Y"))) //Used https://www.w3schools.com/php/php_date.asp to understand how to get the current date
        {                                                                                                                                       //call date() and pass in the format you would like so I passed in 'Y' to get the current year
            $validationResult ['year'] = $this->getDisplayName('year') . ' must be less than the current year plus 2 years';
        }

        return $validationResult;
    }
    public $year;


    public function __construct()
    {
        //here the developer can set any protected variables
        $this->addColumnDefinition("vehicleID", "INTEGER", "PRIMARY KEY AUTOINCREMENT"); //primary key
        $this->addColumnDefinition("make", "nvarchar(25)", "not null"); //required
        $this->addColumnDefinition("model", "nvarchar(25)", "not null"); //required
        $this->addColumnDefinition("type", "nvarchar(10)", "not null"); //required
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