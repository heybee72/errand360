<?php

namespace App\Services;

/**
 *  JSONRESPONSE
 * @method show( @param $message, @param $code, @param $status, @param $data,){
 * 	@return json
 * }
 */
class JsonResponse
{

	public static function show($message, $code , $status = true, $data = []){

		return response()->json([
				"status"=> $status, 
				"message"=> $message, 
				"data" => $data], 
			$code);

	}

}





