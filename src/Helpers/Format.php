<?php
class Format
{
    public $formatted_feilds = array();

    /**
     * Format param by type
     * @param   Array $param_array          GET value array
     * @param   Array $validation_fields    validation rule array
     * @return  Array Formatted value array
     */
    public function formatParam(array $param_array, array $validation_fields) : array 
    {
        foreach($validation_fields as $param_key => $rule)
        {
            if ( isset($param_array[$param_key]) && $param_array[$param_key] != '') {
                $this->formatValue($param_array[$param_key], $rule['type'], $param_key);
            } 
        }

        return $this->formatted_feilds;
    }

    /**
     * Format the value as per the type
     * @param   String  $value          Value to format
     * @param   String  $type           Formating type 
     * @param   String  $param_name     Param name 
     */
    public function formatValue($value, $type, $param_name)
    {
        if ($type == 'int') {
            $casted_value = (int) $value;
            $this->setFormatedArray($param_name, $casted_value);
        }

        if ($type == 'date') {
            $date = new DateTime($value);
            $this->setFormatedArray($param_name, $date);
        }

    }

    private function setFormatedArray(string $field_name, $value)
    {
        $this->formatted_feilds[$field_name] = $value;
    }


    public function convertDateToString(DateTime $date) : string
    {
        return date_format($date, 'Y-m-d');
    }

    public function errorResponse(Array $error)
    {
        $response_array = array(
            'error' =>  $error
        );

        http_response_code(500);
        return json_encode($response_array);
    }

    public function convertToNegativeNumber($num)
    {
        $num = -1 * abs($num);
        return $num; 
    }

    public function formatForDateObject($date)
    {
        $date = new DateTime($date);
        return $date;
    }
}