<?php

class Validation
{
    public $error = array();

    /**
     * Validate param by type
     * @param   String  $value          Value to validate
     * @param   String  $type           Validate against type
     * @param   String  $param_name     Param name 
     */
    public function validate($value, string $type, string $param_name) : void
    {

        if ($type == 'int') {
            if ( !is_numeric($value) )
            {
                $error_fld = $param_name.' should be a '.$type;
                array_push($this->error, $error_fld);
            } 
        }

        if ($type == 'date') {
            if (!$this->validateDate($value)) {
                $error_fld = $param_name.' should be a '.$type;
                array_push($this->error, $error_fld);
            }
        }
        
    }

    /**
     * Validate date for the given format
     * @param   String  $date     Date to validate
     * @param   String  $format   Date format to validate
     * @return  Bool    true if value in expected format
     */
    public function validateDate($date, $format = 'Y-m-d') : bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Format param by type
     * @param   Array $param_array          GET value array
     * @param   Array $validation_fields    validation rule array
     */
    public function validateParam(array $param_array,array $validation_fields) : array 
    {
        foreach($validation_fields as $param_key => $rule)
        {
            if ( isset($param_array[$param_key]) && $param_array[$param_key] != '') {
                $this->validate($param_array[$param_key], $rule['type'], $param_key);
            } else {
                if ($rule['required'] === 'yes') {
                    $error_fld = $param_key.' is required';
                    array_push($this->error, $error_fld);
                }
            }
        }

        return $this->error;
    } 

    /**
     * Check if this is a valid route
     * @param GET   $get    Get request
     * @return bool True if available, false otherwise
     */
    public function isValidRoute($get) : bool
    {
        $valid_routes = array('availability','shortage');

        if (isset($get['route']) && in_array($get['route'], $valid_routes)) {
            return true;
        } else {
            return false;
        }
    }


}



