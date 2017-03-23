<?php

namespace PhpRpc\Server\Codec;

use PhpRpc\Server\ICodec;
use PhpRpc\Server\Exception\CodecException;

class Json implements ICodec
{
	public function decode($data)
	{
		$decoded = json_decode($data);

		$this->handleDecodeError();

		return $decoded;
	}

	private function handleDecodeError()
	{
		$message = 'Unknown error';

		switch(json_last_error()) {
			case JSON_ERROR_NONE:
				return;
			break;
		}

		throw new CodecException($message);
	}
}