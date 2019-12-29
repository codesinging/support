<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/29 10:18
 */

namespace CodeSinging\Support\Tests;

use ArrayObject;
use CodeSinging\Support\Arr;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrTest extends TestCase
{
    public function testAccessible()
    {
        self::assertTrue(Arr::accessible([]));
        self::assertTrue(Arr::accessible([1, 2]));
        self::assertTrue(Arr::accessible(['a' => 1, 'b' => 2]));

        self::assertFalse(Arr::accessible(null));
        self::assertFalse(Arr::accessible('abc'));
        self::assertFalse(Arr::accessible(123));
        self::assertFalse(Arr::accessible(new stdClass()));
        self::assertFalse(Arr::accessible((object)['a' => 1, 'b' => 2]));
    }

    public function testAdd()
    {
        $array = Arr::add(['name' => 'Desk'], 'price', 100);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);

        $array = Arr::add(['name' => 'Desk', 'price' => 100], 'price', 200);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);

        $this->assertEquals(['name' => 'desk'], Arr::add([], 'name', 'desk'));
        $this->assertEquals(['product' => ['name' => 'desk']], Arr::add([], 'product.name', 'desk'));
    }

    public function testDivide()
    {
        list($keys, $values) = Arr::divide(['name' => 'Desk']);
        $this->assertEquals(['name'], $keys);
        $this->assertEquals(['Desk'], $values);
    }

    public function testDot()
    {
        $array = Arr::dot(['foo' => ['bar' => 'baz']]);
        $this->assertEquals(['foo.bar' => 'baz'], $array);

        $array = Arr::dot([]);
        $this->assertEquals([], $array);

        $array = Arr::dot(['foo' => []]);
        $this->assertEquals(['foo' => []], $array);

        $array = Arr::dot(['foo' => ['bar' => []]]);
        $this->assertEquals(['foo.bar' => []], $array);

        $array = Arr::dot(['name' => 'taylor', 'languages' => ['php' => true]]);
        $this->assertEquals($array, ['name' => 'taylor', 'languages.php' => true]);
    }

    public function testExcept()
    {
        $array = ['name' => 'taylor', 'age' => 26];
        $this->assertEquals(['age' => 26], Arr::except($array, ['name']));
        $this->assertEquals(['age' => 26], Arr::except($array, 'name'));

        $array = ['name' => 'taylor', 'framework' => ['language' => 'PHP', 'name' => 'Laravel']];
        $this->assertEquals(['name' => 'taylor'], Arr::except($array, 'framework'));
        $this->assertEquals(['name' => 'taylor', 'framework' => ['name' => 'Laravel']], Arr::except($array, 'framework.language'));
        $this->assertEquals(['framework' => ['language' => 'PHP']], Arr::except($array, ['name', 'framework.name']));
    }

    public function testExists()
    {
        $this->assertTrue(Arr::exists([1], 0));
        $this->assertTrue(Arr::exists([null], 0));
        $this->assertTrue(Arr::exists(['a' => 1], 'a'));
        $this->assertTrue(Arr::exists(['a' => null], 'a'));

        $this->assertFalse(Arr::exists([1], 1));
        $this->assertFalse(Arr::exists([null], 1));
        $this->assertFalse(Arr::exists(['a' => 1], 0));
    }

    public function testFirst()
    {
        $array = [100, 200, 300];

        $value = Arr::first($array, function ($value) {
            return $value >= 150;
        });

        $this->assertEquals(200, $value);
        $this->assertEquals(100, Arr::first($array));
    }

    public function testLast()
    {
        $array = [100, 200, 300];

        $last = Arr::last($array, function ($value) {
            return $value < 250;
        });
        $this->assertEquals(200, $last);

        $last = Arr::last($array, function ($value, $key) {
            return $key < 2;
        });
        $this->assertEquals(200, $last);

        $this->assertEquals(300, Arr::last($array));
    }

    public function testFlatten()
    {
        // Flat arrays are unaffected
        $array = ['#foo', '#bar', '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], Arr::flatten($array));

        // Nested arrays are flattened with existing flat items
        $array = [['#foo', '#bar'], '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], Arr::flatten($array));

        // Flattened array includes "null" items
        $array = [['#foo', null], '#baz', null];
        $this->assertEquals(['#foo', null, '#baz', null], Arr::flatten($array));

        // Sets of nested arrays are flattened
        $array = [['#foo', '#bar'], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], Arr::flatten($array));

        // Deeply nested arrays are flattened
        $array = [['#foo', ['#bar']], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], Arr::flatten($array));
    }

    public function testForget()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, null);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, []);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.desk');
        $this->assertEquals(['products' => []], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.desk.price');
        $this->assertEquals(['products' => ['desk' => []]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.final.price');
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['shop' => ['cart' => [150 => 0]]];
        Arr::forget($array, 'shop.final.cart');
        $this->assertEquals(['shop' => ['cart' => [150 => 0]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.desk.price.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50]]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.desk.final.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]], $array);

        $array = ['products' => ['desk' => ['price' => 50], null => 'something']];
        Arr::forget($array, ['products.amount.all', 'products.desk.price']);
        $this->assertEquals(['products' => ['desk' => [], null => 'something']], $array);

        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@example.com' => 'Jane'];
        Arr::forget($array, 'joe@example.com');
        $this->assertEquals(['jane@example.com' => 'Jane'], $array);

        // Does not work for nested keys
        $array = ['emails' => ['joe@example.com' => ['name' => 'Joe'], 'jane@localhost' => ['name' => 'Jane']]];
        Arr::forget($array, ['emails.joe@example.com', 'emails.jane@localhost']);
        $this->assertEquals(['emails' => ['joe@example.com' => ['name' => 'Joe']]], $array);
    }


    public function testGet()
    {
        $array = ['products.desk' => ['price' => 100]];
        $this->assertEquals(['price' => 100], Arr::get($array, 'products.desk'));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $value = Arr::get($array, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test null array values
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertNull(Arr::get($array, 'foo', 'default'));
        $this->assertNull(Arr::get($array, 'bar.baz', 'default'));

        // Test direct ArrayAccess object
        $array = ['products' => ['desk' => ['price' => 100]]];
        $arrayAccessObject = new ArrayObject($array);
        $value = Arr::get($arrayAccessObject, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing ArrayAccess object
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $array = ['child' => $arrayAccessChild];
        $value = Arr::get($array, 'child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing multiple nested ArrayAccess objects
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = Arr::get($array, 'parent.child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test missing ArrayAccess object field
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = Arr::get($array, 'parent.child.desk');
        $this->assertNull($value);

        // Test missing ArrayAccess object field
        $arrayAccessObject = new ArrayObject(['products' => ['desk' => null]]);
        $array = ['parent' => $arrayAccessObject];
        $value = Arr::get($array, 'parent.products.desk.price');
        $this->assertNull($value);

        // Test null ArrayAccess object fields
        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertNull(Arr::get($array, 'foo', 'default'));
        $this->assertNull(Arr::get($array, 'bar.baz', 'default'));

        // Test null key returns the whole array
        $array = ['foo', 'bar'];
        $this->assertEquals($array, Arr::get($array, null));

        // Test $array not an array
        $this->assertSame('default', Arr::get(null, 'foo', 'default'));

        // Test $array not an array and key is null
        $this->assertSame('default', Arr::get(null, null, 'default'));

        // Test $array is empty and key is null
        $this->assertEmpty(Arr::get([], null));
        $this->assertEmpty(Arr::get([], null, 'default'));

        // Test numeric keys
        $array = [
            'products' => [
                ['name' => 'desk'],
                ['name' => 'chair'],
            ],
        ];
        $this->assertSame('desk', Arr::get($array, 'products.0.name'));
        $this->assertSame('chair', Arr::get($array, 'products.1.name'));

        // Test return default value for non-existing key.
        $array = ['names' => ['developer' => 'taylor']];
        $this->assertSame('dayle', Arr::get($array, 'names.otherDeveloper', 'dayle'));
    }

    public function testHas()
    {
        $array = ['products.desk' => ['price' => 100]];
        $this->assertTrue(Arr::has($array, 'products.desk'));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(Arr::has($array, 'products.desk'));
        $this->assertTrue(Arr::has($array, 'products.desk.price'));
        $this->assertFalse(Arr::has($array, 'products.foo'));
        $this->assertFalse(Arr::has($array, 'products.desk.foo'));

        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));

        $array = new ArrayObject(['foo' => 10, 'bar' => new ArrayObject(['baz' => 10])]);
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));
        $this->assertFalse(Arr::has($array, 'xxx'));
        $this->assertFalse(Arr::has($array, 'xxx.yyy'));
        $this->assertFalse(Arr::has($array, 'foo.xxx'));
        $this->assertFalse(Arr::has($array, 'bar.xxx'));

        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));

        $array = ['foo', 'bar'];
        $this->assertFalse(Arr::has($array, null));

        $this->assertFalse(Arr::has(null, 'foo'));

        $this->assertFalse(Arr::has(null, null));
        $this->assertFalse(Arr::has([], null));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(Arr::has($array, ['products.desk']));
        $this->assertTrue(Arr::has($array, ['products.desk', 'products.desk.price']));
        $this->assertTrue(Arr::has($array, ['products', 'products']));
        $this->assertFalse(Arr::has($array, ['foo']));
        $this->assertFalse(Arr::has($array, []));
        $this->assertFalse(Arr::has($array, ['products.desk', 'products.price']));

        $array = [
            'products' => [
                ['name' => 'desk'],
            ],
        ];
        $this->assertTrue(Arr::has($array, 'products.0.name'));
        $this->assertFalse(Arr::has($array, 'products.0.price'));

        $this->assertFalse(Arr::has([], [null]));
        $this->assertFalse(Arr::has(null, [null]));

        $this->assertTrue(Arr::has(['' => 'some'], ''));
        $this->assertTrue(Arr::has(['' => 'some'], ['']));
        $this->assertFalse(Arr::has([''], ''));
        $this->assertFalse(Arr::has([], ''));
        $this->assertFalse(Arr::has([], ['']));
    }

    public function testIsAssoc()
    {
        $this->assertTrue(Arr::isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(Arr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(Arr::isAssoc(['a', 'b']));
    }

    public function testOnly()
    {
        $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
        $array = Arr::only($array, ['name', 'price']);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
        $this->assertEmpty(Arr::only($array, ['nonExistingKey']));
    }

    public function testPrepend()
    {
        $array = Arr::prepend(['one', 'two', 'three', 'four'], 'zero');
        $this->assertEquals(['zero', 'one', 'two', 'three', 'four'], $array);

        $array = Arr::prepend(['one' => 1, 'two' => 2], 0, 'zero');
        $this->assertEquals(['zero' => 0, 'one' => 1, 'two' => 2], $array);
    }

    public function testPull()
    {
        $array = ['name' => 'Desk', 'price' => 100];
        $name = Arr::pull($array, 'name');
        $this->assertSame('Desk', $name);
        $this->assertEquals(['price' => 100], $array);

        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane'];
        $name = Arr::pull($array, 'joe@example.com');
        $this->assertSame('Joe', $name);
        $this->assertEquals(['jane@localhost' => 'Jane'], $array);

        // Does not work for nested keys
        $array = ['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']];
        $name = Arr::pull($array, 'emails.joe@example.com');
        $this->assertNull($name);
        $this->assertEquals(['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']], $array);
    }

    public function testQuery()
    {
        $this->assertSame('', Arr::query([]));
        $this->assertSame('foo=bar', Arr::query(['foo' => 'bar']));
        $this->assertSame('foo=bar&bar=baz', Arr::query(['foo' => 'bar', 'bar' => 'baz']));
        $this->assertSame('foo=bar&bar=1', Arr::query(['foo' => 'bar', 'bar' => true]));
        $this->assertSame('foo=bar', Arr::query(['foo' => 'bar', 'bar' => null]));
        $this->assertSame('foo=bar&bar=', Arr::query(['foo' => 'bar', 'bar' => '']));
    }

    public function testRandom()
    {
        $random = Arr::random(['foo', 'bar', 'baz']);
        $this->assertContains($random, ['foo', 'bar', 'baz']);

        $random = Arr::random(['foo', 'bar', 'baz'], 0);
        $this->assertTrue(is_array($random));
        $this->assertCount(0, $random);

        $random = Arr::random(['foo', 'bar', 'baz'], 1);
        $this->assertTrue(is_array($random));
        $this->assertCount(1, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);

        $random = Arr::random(['foo', 'bar', 'baz'], 2);
        $this->assertTrue(is_array($random));
        $this->assertCount(2, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);
        $this->assertContains($random[1], ['foo', 'bar', 'baz']);

        $random = Arr::random(['foo', 'bar', 'baz'], '0');
        $this->assertTrue(is_array($random));
        $this->assertCount(0, $random);

        $random = Arr::random(['foo', 'bar', 'baz'], '1');
        $this->assertTrue(is_array($random));
        $this->assertCount(1, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);

        $random = Arr::random(['foo', 'bar', 'baz'], '2');
        $this->assertTrue(is_array($random));
        $this->assertCount(2, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);
        $this->assertContains($random[1], ['foo', 'bar', 'baz']);
    }

    public function testSet()
    {
        $array = [];

        Arr::set($array, null, ['product' => 'chair']);
        self::assertEquals(['product' => 'chair'], $array);

        Arr::set($array, 'product', 'desk');
        self::assertEquals(['product' => 'desk'], $array);

        Arr::set($array, 'product', ['desk' => ['price' => 100]]);
        self::assertEquals(['product' => ['desk' => ['price' => 100]]], $array);

        Arr::set($array, 'product.desk.price', 200);
        self::assertEquals(['product' => ['desk' => ['price' => 200]]], $array);
    }
//
//    public function testShuffle()
//    {
//        $this->assertEquals(
//            Arr::shuffle(range(0, 100, 10), 12345),
//            Arr::shuffle(range(0, 100, 10), 12345)
//        );
//    }

    public function testWhere()
    {
        $array = [100, '200', 300, '400', 500];

        $array = Arr::where($array, function ($value, $key) {
            return is_string($value);
        });

        $this->assertEquals([1 => '200', 3 => '400'], $array);

        $array = ['10' => 1, 'foo' => 3, 20 => 2];

        $array = Arr::where($array, function ($value, $key) {
            return is_numeric($key);
        });

        $this->assertEquals(['10' => 1, 20 => 2], $array);
    }

    public function testWrap()
    {
        $string = 'a';
        $array = ['a'];
        $object = new stdClass;
        $object->value = 'a';
        $this->assertEquals(['a'], Arr::wrap($string));
        $this->assertEquals($array, Arr::wrap($array));
        $this->assertEquals([$object], Arr::wrap($object));
        $this->assertEquals([], Arr::wrap(null));
        $this->assertEquals([null], Arr::wrap([null]));
        $this->assertEquals([null, null], Arr::wrap([null, null]));
        $this->assertEquals([''], Arr::wrap(''));
        $this->assertEquals([''], Arr::wrap(['']));
        $this->assertEquals([false], Arr::wrap(false));
        $this->assertEquals([false], Arr::wrap([false]));
        $this->assertEquals([0], Arr::wrap(0));

        $obj = new stdClass;
        $obj->value = 'a';
        $obj = unserialize(serialize($obj));
        $this->assertEquals([$obj], Arr::wrap($obj));
        $this->assertSame($obj, Arr::wrap($obj)[0]);
    }
}