<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/29 10:54
 */

namespace CodeSinging\Support\Tests;

use CodeSinging\Support\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /** @var Repository */
    protected $repo;

    /** @var array */
    protected $config = [
        'foo' => 'bar',
        'bar' => 'baz',
        'baz' => 'bat',
        'null' => null,
        'associate' => [
            'x' => 'xxx',
            'y' => 'yyy',
        ],
        'array' => [
            'aaa',
            'zzz',
        ],
        'x' => [
            'z' => 'zoo',
        ],
    ];

    protected function setUp()
    {
        $this->repo = new Repository($this->config);
    }

    public function testHas()
    {
        self::assertTrue($this->repo->has('foo'));
        self::assertTrue($this->repo->has('associate.x'));
        self::assertTrue($this->repo->has('array.0'));
        self::assertFalse($this->repo->has('not-exist'));
        self::assertFalse($this->repo->has('associate.z'));
    }

    public function testGet()
    {
        self::assertEquals('bar', $this->repo->get('foo'));
        self::assertEquals('xxx', $this->repo->get('associate.x'));
        self::assertEquals('zzz', $this->repo->get('array.1'));

        self::assertEquals('default', $this->repo->get('not-exists', 'default'));
    }

    public function testGetMany()
    {
        self::assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
            'none' => null,
        ], $this->repo->getMany([
            'foo',
            'bar',
            'none',
        ]));

        $this->assertSame([
            'x.y' => 'default',
            'x.z' => 'zoo',
            'bar' => 'baz',
            'baz' => 'bat',
        ], $this->repo->getMany([
            'x.y' => 'default',
            'x.z' => 'default',
            'bar' => 'default',
            'baz',
        ]));
    }

    public function testSet()
    {
        $this->repo->set('key', 'value');
        $this->assertSame('value', $this->repo->get('key'));

        $this->repo->set('associate.z', 'zzz');
        $this->assertSame('zzz', $this->repo->get('associate.z'));

        $this->repo->set([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
        $this->assertSame('value1', $this->repo->get('key1'));
        $this->assertSame('value2', $this->repo->get('key2'));
    }

    public function testPrepend()
    {
        $this->repo->prepend('array', 'xxx');
        $this->assertSame('xxx', $this->repo->get('array.0'));
    }

    public function testPush()
    {
        $this->repo->push('array', 'xxx');
        $this->assertSame('xxx', $this->repo->get('array.2'));
    }

    public function testAll()
    {
        $this->assertSame($this->config, $this->repo->all());
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->repo['foo']));
        $this->assertFalse(isset($this->repo['not-exist']));
    }

    public function testOffsetGet()
    {
        $this->assertNull($this->repo['not-exist']);
        $this->assertSame('bar', $this->repo['foo']);
        $this->assertSame([
            'x' => 'xxx',
            'y' => 'yyy',
        ], $this->repo['associate']);
    }

    public function testOffsetSet()
    {
        $this->assertNull($this->repo['key']);

        $this->repo['key'] = 'value';

        $this->assertSame('value', $this->repo['key']);
    }

    public function testOffsetUnset()
    {
        $this->assertArrayHasKey('associate', $this->repo->all());
        $this->assertSame($this->config['associate'], $this->repo->get('associate'));

        unset($this->repo['associate']);

        $this->assertArrayHasKey('associate', $this->repo->all());
        $this->assertNull($this->repo->get('associate'));
    }
}