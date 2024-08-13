<?php

namespace App\Service;

class ApiResponseService
{
    /**
     * @param mixed|null $data
     * @param string $message
     * @param int $status the http status code
     * @return \Illuminate\Http\JsonResponse
     *
     *
     */

public static function success($data=null,$message='op suc',$status): \Illuminate\Http\JsonResponse
{
      return response()->json([
          "status"=>'success',
          "message"=>trans($message),
          "data"=>$data
      ]  ,$status);
  }
    /**
     * @param string $message
     * @param int $status
     *  @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     *
     *
     */
    public static function error($message='opr fail',$status=400,$data=null)
    {
        return response()->json([
            "status"=>'success',
            "message"=>$message,
            "data"=>$data
        ]  ,$status);
    }
}
