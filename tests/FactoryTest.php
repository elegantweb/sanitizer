<?php

namespace Elegant\Sanitizer\Tests;

use Elegant\Sanitizer\Tests\Fixtures\Filters\CustomFilter;
use Elegant\Sanitizer\Laravel\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function test_custom_closure_filter()
    {
        $factory = new Factory;
        $factory->extend('hash', function ($value) {
            return sha1($value);
        });

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'hash',
        ];
        $newData = $factory->make($data, $rules)->sanitize();

        $this->assertEquals(sha1('TEST'), $newData['name']);
    }

    public function test_custom_class_filter()
    {
        $factory = new Factory;
        $factory->extend('custom', CustomFilter::class);

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'custom',
        ];
        $newData = $factory->make($data, $rules)->sanitize();

        $this->assertEquals('TESTTEST', $newData['name']);
    }

    public function test_replace_filter()
    {
        $factory = new Factory;
        $factory->extend('trim', function ($value) {
            return sha1($value);
        });

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'trim',
        ];
        $newData = $factory->make($data, $rules)->sanitize();

        $this->assertEquals(sha1('TEST'), $newData['name']);
    }
}
