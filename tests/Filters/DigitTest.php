<?php

use PHPUnit\Framework\TestCase;

class DigitTest extends TestCase
{
    use SanitizesData;

    public function test_string_to_digits()
    {
        $data = [
            'name' => '+08(096)90-123-45q',
        ];
        $rules = [
            'name' => 'digit',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('080969012345', $data['name']);
    }

    public function test_string_to_digits2()
    {
        $data = [
            'name' => 'Qwe-rty!:)',
        ];
        $rules = [
            'name' => 'digit',
        ];
        $data = $this->sanitize($data, $rules);
        
        $this->assertEquals('', $data['name']);
    }
}
