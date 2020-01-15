<?php

/*
 * This file is a service to return a JSON 
 */

class JsonService
{

    public function response($msg=null, $data=[], $code = 1, $status = "ok")
    {
        echo $this->handleResponse($code, $msg, $status, $data);
        return;
    }

    protected function handleResponse($code, $msg, $status, $data) 
    {
        $resp = ['status' => $status, 'code' => $code, 'msg' => $msg, 'data' => $data];
        header('Content-Type: application/json');
        return json_encode($resp);
    }
}