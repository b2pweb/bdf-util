<?php

namespace Bdf\Util;

use PHPUnit\Framework\TestCase;

/**
 *
 */
class ArrTest extends TestCase
{
    /**
     * 
     */
    public function test_add()
    {
        $array = Arr::add(['name' => 'Desk'], 'price', 100);
        
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
    }
    
    /**
     * 
     */
    public function test_add_deeply()
    {
        $array = [];
        
        $array = Arr::add($array, 'node.key', 'value');
        
        $this->assertEquals(['node' => ['key' => 'value']], $array);
    }
    
    /**
     * 
     */
    public function test_add_doesnot_change_existing_key()
    {
        $array = ['key' => 'value'];
        
        $array = Arr::add($array, 'key', 'new value');
        
        $this->assertEquals('value', $array['key']);
    }
    
    /**
     * 
     */
    public function test_build()
    {
        $array = ['key' => 'value'];
        
        // array flip
        $array = Arr::build($array, function($key, $value) {
            return [$value, $key];
        });
        
        $this->assertEquals(['value' => 'key'], $array);
    }
    
    /**
     * 
     */
    public function test_collapse()
    {
        $array = [
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'value2'],
            ['key3' => 'value3'],
        ];
        
        // array flip
        $array = Arr::collapse($array);
        
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'], $array);
    }
    
    /**
     * 
     */
    public function test_divide()
    {
        list($keys, $values) = Arr::divide(['name' => 'Desk']);
        
        $this->assertEquals(['name'], $keys);
        $this->assertEquals(['Desk'], $values);
    }

    /**
     * 
     */
    public function test_except()
    {
        $array = ['name' => 'Desk', 'price' => 100];
        
        $array = Arr::except($array, ['price']);
        $this->assertEquals(['name' => 'Desk'], $array);
    }

    /**
     * 
     */
    public function test_first()
    {
        $array = ['a' => 100, 'b' => 200, 'c' => 300];

        $this->assertEquals(100, Arr::first($array));
    }

    /**
     *
     */
    public function test_first_with_custom_callback()
    {
        $array = ['a' => 100, 'b' => 200, 'c' => 300];

        $value = Arr::first($array, function ($key, $value) {
            return $value >= 150;
        });

        $this->assertEquals(200, $value);
    }

    /**
     * 
     */
    public function test_first_default_value()
    {
        $array = ['a' => 100, 'b' => 200, 'c' => 300];

        $value = Arr::first($array, function ($key, $value) {
            return $value >= 10000;
        }, 1);

        $this->assertEquals(1, $value);
    }

    /**
     *
     */
    public function test_last()
    {
        $array = ['a' => 100, 'b' => 200, 'c' => 300];

        $this->assertEquals(300, Arr::last($array));
    }

    /**
     * 
     */
    public function test_last_with_custom_callback()
    {
        $array = ['a' => 100, 'b' => 200, 'c' => 300];
        
        $last = Arr::last($array, function () { return true; });
        $this->assertEquals(300, $last);
    }

    /**
     * 
     */
    public function test_flatten()
    {
        $array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];
        
        $array = Arr::flatten($array);
        $this->assertEquals(['Joe', 'PHP', 'Ruby'], $array);
    }

    /**
     *
     */
    public function test_prefix()
    {
        $array = [
            'name' => 'Joe',
            'languages' => ['PHP', 'Ruby'],
            'address' => [
                'city' => 'Snowcity',
                'zipcode' => [
                    'zone' => '13',
                    'value' => '13456',
                ]
            ]
        ];

        $expected = [
            'name' => 'Joe',
            'languages' => ['PHP', 'Ruby'],
            'address.city' => 'Snowcity',
            'address.zipcode.zone' => '13',
            'address.zipcode.value' => '13456',
        ];

        $this->assertEquals($expected, Arr::glue($array));
    }

    /**
     *
     */
    public function test_dot()
    {
        $array = [
            'key' => 'value',
            'node' => ['key' => 'value'],
        ];

        $expected = [
            'key' => 'value',
            'node.key' => 'value',
        ];

        $this->assertEquals($expected, Arr::dot($array));
    }

    /**
     * 
     */
    public function test_forget()
    {
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
    }
    
    /**
     * 
     */
    public function test_get()
    {
        $array = ['key1' => 'value1'];
        
        $this->assertTrue(Arr::has($array, 'key1'));
        $this->assertEquals('value1', Arr::get($array, 'key1'));
    }
    
    /**
     * 
     */
    public function test_get_null()
    {
        $array = ['key1' => 'value1'];
        
        $this->assertEquals($array, Arr::get($array, null));
    }
    
    /**
     * 
     */
    public function test_get_unknow_key()
    {
        $array = ['key1' => 'value1'];
        
        $this->assertFalse(Arr::has($array, 'key2'));
        $this->assertEquals('default', Arr::get($array, 'key2', 'default'));
    }
    
    /**
     * 
     */
    public function test_get_deeply()
    {
        $array = [
            'node' => ['key1' => 'value1'],
        ];
        
        $this->assertTrue(Arr::has($array, 'node.key1'));
        $this->assertEquals('value1', Arr::get($array, 'node.key1'));
    }
    
    /**
     * 
     */
    public function test_has()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        
        $this->assertTrue(Arr::has($array, 'products.desk'));
        $this->assertTrue(Arr::has($array, 'products.desk.price'));
        $this->assertFalse(Arr::has($array, 'products.foo'));
        $this->assertFalse(Arr::has($array, 'products.desk.foo'));
    }
    
    /**
     * 
     */
    public function test_has_null()
    {
        $array = ['key1' => 'value1'];
        
        $this->assertFalse(Arr::has($array, null));
    }
    
    /**
     * 
     */
    public function test_has_empty()
    {
        $array = [];
        
        $this->assertFalse(Arr::has($array, 'key'));
    }
    
    /**
     * 
     */
    public function test_is_associative()
    {
        $this->assertTrue(Arr::isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(Arr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(Arr::isAssoc(['a', 'b']));
    }
    
    
    /**
     * 
     */
    public function test_only()
    {
        $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
        
        $array = Arr::only($array, ['name', 'price']);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
    }

    /**
     * 
     */
    public function test_prepend()
    {
        $array = Arr::prepend(['one', 'two', 'three', 'four'], 'zero');
        
        $this->assertEquals(['zero', 'one', 'two', 'three', 'four'], $array);

        $array = Arr::prepend(['one' => 1, 'two' => 2], 0, 'zero');
        $this->assertEquals(['zero' => 0, 'one' => 1, 'two' => 2], $array);
    }

    /**
     * 
     */
    public function test_pull()
    {
        $array = ['name' => 'Desk', 'price' => 100];
        
        $name = Arr::pull($array, 'name');
        $this->assertEquals('Desk', $name);
        $this->assertEquals(['price' => 100], $array);
    }

    /**
     * 
     */
    public function test_set()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        
        Arr::set($array, 'products.desk.price', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => 200]]], $array);
    }

    /**
     * 
     */
    public function test_set_null()
    {
        $array = ['price' => 100];
        
        Arr::set($array, null, ['price' => 200]);
        $this->assertEquals(['price' => 200], $array);
    }
    
    /**
     * 
     */
    public function test_sort()
    {
        $array = [
            ['name' => 'Desk'],
            ['name' => 'Chair'],
        ];

        $array = array_values(Arr::sort($array, function ($value) {
            return $value['name'];
        }));

        $expected = [
            ['name' => 'Chair'],
            ['name' => 'Desk'],
        ];
        $this->assertEquals($expected, $array);
    }
    
    /**
     * 
     */
    public function test_sortRecursive()
    {
        $array = [
            'users' => [
                [
                    // should sort associative arrays by keys
                    'name' => 'joe',
                    'mail' => 'joe@example.com',
                    // should sort deeply nested arrays
                    'numbers' => [2, 1, 0],
                ],
                [
                    'name' => 'jane',
                    'age' => 25,
                ],
            ],
            'repositories' => [
                // should use weird `sort()` behavior on arrays of arrays
                ['id' => 1],
                ['id' => 0],
            ],
            // should sort non-associative arrays by value
            20 => [2, 1, 0],
            30 => [
                // should sort non-incrementing numerical keys by keys
                2 => 'a',
                1 => 'b',
                0 => 'c',
            ],
        ];

        $expect = [
            20 => [0, 1, 2],
            30 => [
                0 => 'c',
                1 => 'b',
                2 => 'a',
            ],
            'repositories' => [
                ['id' => 0],
                ['id' => 1],
            ],
            'users' => [
                [
                    'age' => 25,
                    'name' => 'jane',
                ],
                [
                    'mail' => 'joe@example.com',
                    'name' => 'joe',
                    'numbers' => [0, 1, 2],
                ],
            ],
        ];

        $this->assertEquals($expect, Arr::sortRecursive($array));
    }
    
    /**
     * 
     */
    public function test_where()
    {
        $array = [100, '200', 300, '400', 500];

        $array = Arr::where($array, function ($key, $value) {
            return is_string($value);
        });

        $this->assertEquals([1 => 200, 3 => 400], $array);
    }
}