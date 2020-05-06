<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/29 10:43
 */

namespace CodeSinging\Support\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;

class HelpersTest extends TestCase
{
    public function testLoadConfig()
    {
        $data = ['name' => 'helpers'];

        self::assertEquals($data, load_config(__DIR__ . '/data/config.php'));
        self::assertEquals($data, load_config(__DIR__ . "/data/config.json"));
        self::assertEquals($data, load_config(__DIR__ . "/data/config.ini"));

        if (function_exists('yaml_parse_file')) {
            self::assertEquals($data, load_config(__DIR__ . "/../data/config.yaml"));
        }
    }

    public function testBlank()
    {
        self::assertTrue(blank(null));
        self::assertTrue(blank(''));
        self::assertTrue(blank(' '));
        self::assertTrue(blank([]));
        self::assertTrue(blank(new CountableDemo()));

        self::assertFalse(blank(0));
        self::assertFalse(blank('0'));
        self::assertFalse(blank(false));
        self::assertFalse(blank([0]));
        self::assertFalse(blank(new CountableDemo(1)));
    }

    public function testFilled()
    {
        self::assertFalse(filled(null));
        self::assertFalse(filled(''));
        self::assertFalse(filled(' '));
        self::assertFalse(filled([]));
        self::assertFalse(filled(new CountableDemo()));

        self::assertTrue(filled(0));
        self::assertTrue(filled('0'));
        self::assertTrue(filled(false));
        self::assertTrue(filled([0]));
        self::assertTrue(filled(new CountableDemo(1)));
    }

    public function testClassBasename()
    {
        self::assertSame('Baz', class_basename('Foo\Bar\Baz'));
        self::assertSame('Baz', class_basename('Baz'));
    }

    public function testObjectGet()
    {
        $class = new stdClass();
        $class->name = new stdClass();
        $class->name->value = 'foo';

        self::assertSame('foo', object_get($class, 'name.value'));
    }

    public function testValue()
    {
        self::assertSame('foo', value('foo'));
        self::assertSame('foo', value(function () {
            return 'foo';
        }));
    }

    public function testWith()
    {
        self::assertSame(10, with(10));
        self::assertSame(10, with(5, function ($value) {
            return $value + $value;
        }));
    }
}

class CountableDemo implements \Countable
{
    private $_count;

    /**
     * CountableDemo constructor.
     * @param $count
     */
    public function __construct($count = 0)
    {
        $this->_count = $count;
    }


    public function count()
    {
        return $this->_count;
    }
}