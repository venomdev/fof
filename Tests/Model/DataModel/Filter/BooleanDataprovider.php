<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */


class BooleanDataprovider
{
	public static function getTestIsEmpty()
	{
		$data[] = [
			[
				'value' => '',
			],
			[
				'case'   => 'Empty string',
				'result' => true,
			],
		];

		$data[] = [
			[
				'value' => 123,
			],
			[
				'case'   => 'Integers',
				'result' => false,
			],
		];

		$data[] = [
			[
				'value' => [],
			],
			[
				'case'   => 'Empty array',
				'result' => false,
			],
		];

		$data[] = [
			[
				'value' => new stdClass(),
			],
			[
				'case'   => 'Object',
				'result' => false,
			],
		];

		$data[] = [
			[
				'value' => null,
			],
			[
				'case'   => 'Null value',
				'result' => true,
			],
		];

		return $data;
	}
}
