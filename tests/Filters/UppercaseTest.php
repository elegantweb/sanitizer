<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class UppercaseTest extends TestCase
{
    use SanitizesData;

    public function test_uppercases_strings()
    {
        $data = [
            'name' => 'HellO EverYboDy',
        ];
        $rules = [
            'name' => 'uppercase',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('HELLO EVERYBODY', $data['name']);
    }

    public function test_uppercases_special_characters_strings()
    {
        $data = [
            'name' => 'Τάχιστη αλώπηξ',
        ];
        $rules = [
            'name' => 'uppercase',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('ΤΆΧΙΣΤΗ ΑΛΏΠΗΞ', $data['name']);
    }
}
