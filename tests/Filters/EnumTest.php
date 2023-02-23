<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Filters\Enum;
use Elegant\Sanitizer\Tests\Fixtures\Enums\BasicEnum;
use Elegant\Sanitizer\Tests\Fixtures\Enums\BackedEnum;
use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP >= 8.1
 */
class EnumTest extends TestCase
{
    use SanitizesData;

    public function test_basic_enum()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sanitize(['name' => 'H'], ['name' => new Enum(BasicEnum::class)]);
    }

    public function test_backed_enum()
    {
        $result = $this->sanitize(['name' => 'H'], ['name' => new Enum(BackedEnum::class)]);
        $this->assertEquals(BackedEnum::Hearts, $result['name']);
    }
}
