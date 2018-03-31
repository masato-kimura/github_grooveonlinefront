<?php

/**
 * @group App
 * @author masato
 *
 */
class myinputfilters_Test extends TestCase
{
	public function test_check_encoding_SJIS文字列を検証すると例外が発生()
	{
		$this->setExpectedException(
			'HttpInvalidInputException'. 'Invalid input data'
		);

		$input = mb_convert_encoding('SJISの文字列です', 'SJIS');
		$test = MyInputFilters::check_encoding($input);
	}

	public function test_check_encoding_正常な文字列は検証をパスしその文字列が帰る()
	{
		$input = '正常なUTF-8の文字列です';
		$test = MyInputFilters::check_encoding($input);
		$expected = $input;

		$this->assertEquals($expected, $test);
	}
}