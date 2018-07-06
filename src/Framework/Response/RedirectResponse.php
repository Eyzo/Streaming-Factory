<?php 
namespace Framework\Response;

use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response
{

	public function __construct(string $url)
	{
  parent::__construct( $status = 200,$headers = ['Location' => $url]);
	}


}