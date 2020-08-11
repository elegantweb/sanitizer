<?php

use PHPUnit\Framework\TestCase;

class LowercaseTest extends TestCase
{
    use SanitizesData;

    public function test_lowercases_strings()
    {
        $data = [
            'name' => 'HellO EverYboDy',
        ];
        $rules = [
            'name' => 'lowercase',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('hello everybody', $data['name']);
    }

    public function test_lowercases_special_characters_strings()
    {
        $data = [
            'name' => 'Τάχιστη αλώπηξ',
        ];
        $rules = [
            'name' => 'lowercase',
        ];
        $data = $this->sanitize($data, $rules);
        
        $this->assertEquals('τάχιστη αλώπηξ', $data['name']);
    }
}
