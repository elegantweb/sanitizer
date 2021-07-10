<?php

namespace Elegant\Sanitizer\Tests;

use InvalidArgumentException;
use Elegant\Sanitizer\Laravel\Factory;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    use SanitizesData;

    public function test_combine_filters()
    {
        $data = [
            'name' => '  HellO EverYboDy   ',
        ];
        $filters = [
            'name' => 'trim|capitalize',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('Hello Everybody', $data['name']);
    }

    public function test_input_unchanged_if_no_filter()
    {
        $data = [
            'name' => '  HellO EverYboDy   ',
        ];
        $filters = [
            'name' => '',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('  HellO EverYboDy   ', $data['name']);
    }

    public function test_array_filters()
    {
        $data = [
            'name' => '  HellO EverYboDy   ',
        ];
        $filters = [
            'name' => ['trim', 'capitalize'],
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('Hello Everybody', $data['name']);
    }

    public function test_wildcard_filters()
    {
        $data = [
            'name' => [
                'first' => ' John ',
                'last'  => ' Doe ',
            ],
            'address' => [
                'street' => ' Some street ',
                'city'   => ' New York ',
            ],
        ];
        $filters = [
            'name.*' => 'trim',
            'address.city' => 'trim',
        ];
        $data = $this->sanitize($data, $filters);

        $sanitized = [
            'name' => ['first' => 'John', 'last' => 'Doe'],
            'address' => ['street' => ' Some street ', 'city' => 'New York'],
        ];

        $this->assertEquals($sanitized, $data);
    }

    public function test_throws_exception_if_non_existing_filter()
    {
        $this->expectException(InvalidArgumentException::class);

        $data = [
            'name' => '  HellO EverYboDy   ',
        ];
        $filters = [
            'name' => 'non-filter',
        ];
        $data = $this->sanitize($data, $filters);
    }

    public function test_should_only_sanitize_passed_data()
    {
        $data = [
            'title' => ' Hello WoRlD '
        ];
        $filters = [
            'title' => 'trim',
            'name' => 'trim|escape'
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertArrayNotHasKey('name', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals(1, count($data));
    }

    public function test_closure_filter()
    {
        $data = [
            'name' => ' Sina '
        ];
        $filters = [
            'name' => ['trim', fn ($value) => strtoupper($value)]
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('SINA', $data['name']);
    }

    public function test_removed_array_elements_are_persistent()
    {
        $actual = null;

        $data = [
            'users' => [
                ['name' => 'Mohammad', 'age' => 32],
                ['name' => 'Ali', 'age' => 25]
            ]
        ];
        $filters = [
            'users' => [function ($value) {
                unset($value[0]);
                return $value;
            }],
            'users.*.age' => [function ($value) use (&$actual) {
                $actual[] = $value;
                return $value;
            }]
        ];
        $data = $this->sanitize($data, $filters);

        $sanitized = [
            'users' => [
                1 => ['name' => 'Ali', 'age' => 25]
            ]
        ];

        $this->assertEquals(['25'], $actual);
        $this->assertEquals($sanitized, $data);
    }

    public function test_we_can_use_escape_in_filter_options()
    {
        $factory = new Factory;
        $factory->extend('explode', function ($value, array $options = []) {
            return array_map('trim', explode($options[0], $value));
        });

        $data = [
            'tags' => 'tag1, tag2, tag3'
        ];
        $filters = [
            'tags' => 'explode:","'
        ];
        $data = $factory->make($data, $filters)->sanitize();

        $sanitized = [
            'tags' => ['tag1', 'tag2', 'tag3']
        ];

        $this->assertEquals($sanitized, $data);
    }

    public function test_string_that_only_contains_whitespace_will_be_converted_to_null_with_trim_and_empty_string_to_null_filters()
    {
        $data = [
            'title' => "  \n "
        ];
        $filters = [
            'title' => 'trim|empty_string_to_null',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertArrayHasKey('title', $data);
        $this->assertNull($data['title']);
    }
}
