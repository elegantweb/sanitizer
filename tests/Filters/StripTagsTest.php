<?php

use PHPUnit\Framework\TestCase;

class StripTagsTest extends TestCase
{
    use SanitizesData;

    public function test_trims_strings()
    {
        $data = [
            'name' => '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
        ];
        $rules = [
            'name' => 'strip_tags',
        ];
        $data = $this->sanitize($data, $rules);
        
        $this->assertEquals('Test paragraph. Other text', $data['name']);
    }
}
