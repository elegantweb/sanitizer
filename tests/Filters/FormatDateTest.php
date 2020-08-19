<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class FormatDateTest extends TestCase
{
    use SanitizesData;

    public function test_formats_dates()
    {
        $data = [
            'name' => '21/03/1983',
        ];
        $filters = [
            'name' => 'format_date:d/m/Y, Y-m-d',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('1983-03-21', $data['name']);
    }

    public function test_requires_two_arguments()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '21/03/1983',
        ];
        $filters = [
            'name' => 'format_date:d/m/Y',
        ];
        $data = $this->sanitize($data, $filters);
    }
}
