<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class DigitTest extends TestCase
{
    use SanitizesData;

    public function test_string_to_digits()
    {
        $data = [
            'name' => '+08(096)90-123-45q',
        ];
        $filters = [
            'name' => 'digit',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('080969012345', $data['name']);
    }

    public function test_string_to_digits2()
    {
        $data = [
            'name' => 'Qwe-rty!:)',
        ];
        $filters = [
            'name' => 'digit',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('', $data['name']);
    }
}
