<?php

namespace Elegant\Sanitizer\Tests\Filters;

use Elegant\Sanitizer\Filters\Enum;
use Elegant\Sanitizer\Tests\SanitizesData;
use PHPUnit\Framework\TestCase;

if (PHP_VERSION_ID >= 80100) {
    require(__DIR__ . '/../Fixtures/enums.php');
}

/**
 * @requires PHP >= 8.1
 */
class EnumTest extends TestCase
{
    use SanitizesData;

    public function test_basic_enum()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sanitize(['name' => 'H'], ['name' => new Enum(\SampleBasicEnum::class)]);
    }

    public function test_backed_enum()
    {
        $result = $this->sanitize(['name' => 'H'], ['name' => new Enum(\SampleBackedEnum::class)]);
        $this->assertEquals(\SampleBackedEnum::Hearts, $result['name']);
    }
}
