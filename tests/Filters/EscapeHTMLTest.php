<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

class EscapeHTMLTest extends TestCase
{
    use SanitizesData;

    public function test_escapes_strings()
    {
        $data = [
            'name' => '<h1>Hello! Unicode chars as Ñ are not escaped.</h1> <script>Neither is content inside HTML tags</script>',
        ];
        $rules = [
            'name' => 'escape',
        ];
        $data = $this->sanitize($data, $rules);

        $this->assertEquals('Hello! Unicode chars as Ñ are not escaped. Neither is content inside HTML tags', $data['name']);
    }
}
