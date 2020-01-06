<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2020/1/6 09:46
 */

namespace CodeSinging\Support\Tests;

use CodeSinging\Support\Configurable;
use PHPUnit\Framework\TestCase;

class ConfigurableTest extends TestCase
{
    public function testConstructor()
    {
        $array = ['name' => 'Support', 'age' => 25];
        $config = new Configurable($array);

        $this->assertEquals($array, $config->configs());
    }

    public function testSet()
    {
        $config = new Configurable();
        self::assertEquals([], $config->configs());

        $config->set(['name' => 'Support'])->set('age', 25);
        self::assertEquals(['name' => 'Support', 'age' => 25], $config->configs());
    }

    public function testGet()
    {
        $config = new Configurable(['name' => 'Support', 'age' => 25]);

        self::assertEquals('Support', $config->get('name'));
        self::assertEquals('Support', $config->name);
        self::assertEquals('Default', $config->get('address', 'Default'));
        self::assertNull($config->phone);
    }

    public function testArrayAccess()
    {
        $config = new Configurable(['name' => 'Support', 'age' => 25]);

        self::assertTrue(isset($config['name']));
        self::assertEquals('Support', $config['name']);
    }

    public function testMagicMethods()
    {
        $config = new Configurable();

        $config->name = 'Support';
        $config->status();
        $config->age(20);

        self::assertEquals('Support', $config->name);
        self::assertTrue($config->status);
        self::assertEquals(20, $config->age);
        self::assertInstanceOf(Configurable::class, $config->disabled());
    }

    public function testIsset()
    {
        $config = new Configurable(['name' => 'Support', 'age' => 25]);

        self::assertTrue(isset($config->name));
        self::assertTrue(isset($config['name']));

        unset($config->name);

        self::assertFalse(isset($config->name));
        self::assertFalse(isset($config['name']));
    }

    public function testConfigs()
    {
        $array = ['name' => 'Support', 'age' => 25];
        $config = new Configurable($array);

        self::assertEquals($array, $config->configs());
    }

    public function testToArray()
    {
        $array = ['name' => 'Support', 'age' => 25];
        $config = new Configurable($array);

        self::assertEquals($array, $config->toArray());
    }

    public function testToJson()
    {
        $config = $this->getMockBuilder(Configurable::class)->setMethods(['toArray'])->getMock();

        $config->expects(self::once())->method('toArray')->will(self::returnValue('foo'));
        $results = $config->toJson();

        self::assertJsonStringEqualsJsonString(json_encode('foo'), $results);
    }
}