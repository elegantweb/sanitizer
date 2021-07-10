<?php


namespace Elegant\Sanitizer\Tests\Filters;


use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class EmptyStringToNullTest extends TestCase
{
    use SanitizesData;

    public function test_empty_string_to_null_filter_converts_empty_string_to_null()
    {
        $data = [
            'name' => '',
        ];
        $filters = [
            'name' => 'empty_string_to_null',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals(null, $data['name']);
    }

    public function test_empty_string_to_null_filter_ignores_string_that_only_contains_whitespace()
    {
        $data = [
            'name' => ' ',
        ];
        $filters = [
            'name' => 'empty_string_to_null',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals(' ', $data['name']);
    }

    public function test_empty_string_to_null_filter_ignores_non_empty_string_and_integer()
    {
        $data = [
            'name' => ' Test ',
            'number' => 0,
        ];
        $filters = [
            'name' => 'empty_string_to_null',
            'number' => 'empty_string_to_null',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals(' Test ', $data['name']);
        $this->assertEquals(0, $data['number']);
    }
}
