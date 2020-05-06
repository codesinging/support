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

    public function testDataGet()
    {
        $object = (object)['users' => ['name' => ['Taylor', 'Well']]];
        $array = [(object)['users' => [(object)['name' => 'Taylor']]]];
        $dottedArray = ['users' => ['first.name' => 'Taylor', 'middle.name' => null]];

        self::assertSame('Taylor', data_get($object, 'users.name.0'));
        self::assertSame('Taylor', data_get($array, '0.users.0.name'));
        self::assertNull(data_get($array, '0.users.3'));
        self::assertSame('Not found', data_get($array, '0.users.3', 'Not found'));
        self::assertSame('Not found', data_get($array, '0.users.3', function () {
            return 'Not found';
        }));
        self::assertNull(data_get($dottedArray, 'users.first.name'));
        self::assertSame('Taylor', data_get($dottedArray, ['users', 'first.name']));
        self::assertSame('Not found', data_get($dottedArray, ['users', 'last.name'], 'Not found'));
    }

    public function testDataGetWithNestedArrays()
    {
        $array = [
            ['name' => 'taylor', 'email' => 'taylor@gmail.com'],
            ['name' => 'abigail'],
            ['name' => 'dayle'],
        ];

        self::assertEquals(['taylor', 'abigail', 'dayle'], data_get($array, '*.name'));
        self::assertEquals(['taylor@gmail.com', null, null], data_get($array, '*.email'));

        $array = [
            'users' => [
                ['first' => 'taylor', 'last' => 'otwell', 'email' => 'taylor@gmail.com'],
                ['first' => 'abigail', 'last' => 'otwell'],
                ['first' => 'dayle', 'last' => 'rees'],
            ],
            'posts' => null,
        ];

        self::assertEquals(['taylor', 'abigail', 'dayle'], data_get($array, 'users.*.first'));
        self::assertEquals(['taylor@gmail.com', null, null], data_get($array, 'users.*.email'));
        self::assertNull(data_get($array, 'posts.*.date'));
        self::assertEquals('not found', data_get($array, 'posts.*.date', 'not found'));
    }

    public function testDataGetWithDoubleNestedArraysCollapsesResult()
    {
        $array = [
            'posts' => [
                [
                    'comments' => [
                        ['author' => 'taylor', 'likes' => 4],
                        ['author' => 'abigail', 'likes' => 3],
                    ],
                ],
                [
                    'comments' => [
                        ['author' => 'abigail', 'likes' => 2],
                        ['author' => 'dayle'],
                    ],
                ],
                [
                    'comments' => [
                        ['author' => 'dayle'],
                        ['author' => 'taylor', 'likes' => 1],
                    ],
                ],
            ],
        ];

        $this->assertEquals(['taylor', 'abigail', 'abigail', 'dayle', 'dayle', 'taylor'], data_get($array, 'posts.*.comments.*.author'));
        $this->assertEquals([4, 3, 2, null, null, 1], data_get($array, 'posts.*.comments.*.likes'));
        $this->assertEquals([], data_get($array, 'posts.*.users.*.name', 'irrelevant'));
        $this->assertEquals([], data_get($array, 'posts.*.users.*.name'));
    }

    public function testDataSet()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'boom'],
            data_set($data, 'baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'kaboom'],
            data_set($data, 'baz', 'kaboom')
        );

        $this->assertEquals(
            ['foo' => [], 'baz' => 'kaboom'],
            data_set($data, 'foo.*', 'noop')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => 'kaboom'],
            data_set($data, 'foo.bar', 'boom')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => ['bar' => 'boom']],
            data_set($data, 'baz.bar', 'boom')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => ['bar' => ['boom' => ['kaboom' => 'boom']]]],
            data_set($data, 'baz.bar.boom.kaboom', 'boom')
        );
    }

    public function testDataSetWithStar()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => []],
            data_set($data, 'foo.*.bar', 'noop')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], []]],
            data_set($data, 'bar', [['baz' => 'original'], []])
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'boom'], ['baz' => 'boom']]],
            data_set($data, 'bar.*.baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => ['overwritten', 'overwritten']],
            data_set($data, 'bar.*', 'overwritten')
        );
    }

    public function testDataSetWithDoubleStar()
    {
        $data = [
            'posts' => [
                (object)[
                    'comments' => [
                        (object)['name' => 'First'],
                        (object)[],
                    ],
                ],
                (object)[
                    'comments' => [
                        (object)[],
                        (object)['name' => 'Second'],
                    ],
                ],
            ],
        ];

        data_set($data, 'posts.*.comments.*.name', 'Filled');

        $this->assertEquals([
            'posts' => [
                (object)[
                    'comments' => [
                        (object)['name' => 'Filled'],
                        (object)['name' => 'Filled'],
                    ],
                ],
                (object)[
                    'comments' => [
                        (object)['name' => 'Filled'],
                        (object)['name' => 'Filled'],
                    ],
                ],
            ],
        ], $data);
    }

    public function testDataFill()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar', 'baz' => 'boom'], data_fill($data, 'baz', 'boom'));
        $this->assertEquals(['foo' => 'bar', 'baz' => 'boom'], data_fill($data, 'baz', 'noop'));
        $this->assertEquals(['foo' => [], 'baz' => 'boom'], data_fill($data, 'foo.*', 'noop'));
        $this->assertEquals(
            ['foo' => ['bar' => 'kaboom'], 'baz' => 'boom'],
            data_fill($data, 'foo.bar', 'kaboom')
        );
    }

    public function testDataFillWithStar()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => []],
            data_fill($data, 'foo.*.bar', 'noop')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], []]],
            data_fill($data, 'bar', [['baz' => 'original'], []])
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], ['baz' => 'boom']]],
            data_fill($data, 'bar.*.baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], ['baz' => 'boom']]],
            data_fill($data, 'bar.*', 'noop')
        );
    }

    public function testDataFillWithDoubleStar()
    {
        $data = [
            'posts' => [
                (object)[
                    'comments' => [
                        (object)['name' => 'First'],
                        (object)[],
                    ],
                ],
                (object)[
                    'comments' => [
                        (object)[],
                        (object)['name' => 'Second'],
                    ],
                ],
            ],
        ];

        data_fill($data, 'posts.*.comments.*.name', 'Filled');

        $this->assertEquals([
            'posts' => [
                (object)[
                    'comments' => [
                        (object)['name' => 'First'],
                        (object)['name' => 'Filled'],
                    ],
                ],
                (object)[
                    'comments' => [
                        (object)['name' => 'Filled'],
                        (object)['name' => 'Second'],
                    ],
                ],
            ],
        ], $data);
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