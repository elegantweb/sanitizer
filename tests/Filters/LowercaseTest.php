<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class LowercaseTest extends TestCase
{
    use SanitizesData;

    public function test_lowercases_strings()
    {
        $data = [
            'name' => 'HellO EverYboDy',
        ];
        $filters = [
            'name' => 'lowercase',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('hello everybody', $data['name']);
    }

    public function test_lowercases_special_characters_strings()
    {
        $data = [
            'name' => 'Τάχιστη αλώπηξ',
        ];
        $filters = [
            'name' => 'lowercase',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('τάχιστη αλώπηξ', $data['name']);
    }
}
