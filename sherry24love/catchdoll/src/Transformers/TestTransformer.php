<?php
namespace  Sherrycin\Catchdoll\Transformers ;

use Apiato\Core\Abstracts\Transformers\Transformer;

class TestTransformer extends Transformer {
	
	
	/**
	 * @param $token
	 *
	 * @return  array
	 */
	public function transform($data )
	{
		$response = [
				'object'       => 'Administrator',
				'access_token' => $data ,
				'token_type'   => 'Bearer',
				'expires_in'   => '',
		];
	
		return $response;
	}
}