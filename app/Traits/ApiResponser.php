<?php

namespace App\Traits;

use App\Enums\MessageCodeEnum;

trait ApiResponser
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    protected function responseSuccess($data = null, $messageCode = MessageCodeEnum::SUCCESS, $code = 200)
	{
		return response()->json([
			'status'        => 'success',
            'status_code'   => $code,
			'message_code'  => $messageCode,
			'data'          => $data
		], $code);
	}

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
	protected function responseError($message = null, $messageCode = null, $code = 400)
	{
		return response()->json([
			'status'        => 'error',
			'status_code'   => $code,
			'message_code'  => $messageCode,
			'data'          => $message
		], $code);
	}

}
