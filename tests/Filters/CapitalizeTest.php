<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class CapitalizeTest extends TestCase
{
    use SanitizesData;

    public function test_capitalizes_strings()
    {
        $result = $this->sanitize(['name' => ' jon snow 145'], ['name' => 'capitalize']);
        $this->assertEquals(' Jon Snow 145', $result['name']);
    }

    public function test_capitalizes_special_characters()
    {
        $result = $this->sanitize(['name' => 'Τάχιστη αλώπηξ'], ['name' => 'capitalize']);
        $this->assertEquals('Τάχιστη Αλώπηξ', $result['name']);
    }
}
