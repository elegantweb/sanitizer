<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class StripTagsTest extends TestCase
{
    use SanitizesData;

    public function test_trims_strings()
    {
        $data = [
            'name' => '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
        ];
        $filters = [
            'name' => 'strip_tags',
        ];
        $data = $this->sanitize($data, $filters);

        $this->assertEquals('Test paragraph. Other text', $data['name']);
    }
}
