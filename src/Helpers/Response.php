<?php
class Response
{
    public function errorResponse(Array $error)
    {
        $error_array = $this->prepareErrorResponse($error);
        echo json_encode($error_array);
        die();
    }

    private function prepareErrorResponse(Array $error) : Array
    {
        $response_array = array(
            'response_type' => 'error',
            'error'         =>  $error
        );

        return $response_array;
    }

    public function successResponse(Array $data_array)
    {
        $success_array = $this->prepareSuccessResponse($data_array);
        echo json_encode($success_array);
        die();
    }

    private function prepareSuccessResponse(Array $array) : Array
    {
        $response_array = array(
            'response_type' => 'success',
            'result'        =>  $array
        );

        return $response_array;
    }
}