<?php

use PHPUnit\Framework\TestCase;

class TrimTest extends TestCase
{
    use SanitizesData;

    public function test_trims_strings()
    {
        $data = [
            'name' => '  Test  ',
        ];
        $rules = [
            'name' => 'trim',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('Test', $data['name']);
    }
}
