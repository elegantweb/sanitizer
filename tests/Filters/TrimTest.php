<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class TrimTest extends TestCase
{
    use SanitizesData;

    public function test_trims_strings()
    {
        $data = [
            'name' => '  Test  ',
        ];
        $filters = [
            'name' => 'trim',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('Test', $data['name']);
    }
}
