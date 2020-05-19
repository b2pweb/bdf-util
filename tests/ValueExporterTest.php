<?php

namespace Bdf\Util;

use PHPUnit\Framework\TestCase;

/**
 *
 */
class ValueExporterTest extends TestCase
{
    /**
     *
     */
    public function test_scalar()
    {
        $this->assertSame("null", ValueExporter::export(null));
        $this->assertSame("true", ValueExporter::export(true));
        $this->assertSame("false", ValueExporter::export(false));
        $this->assertSame("0", ValueExporter::export(0));
        $this->assertSame("foo", ValueExporter::export("foo"));
    }

    /**
     *
     */
    public function test_array()
    {
        $this->assertSame("[]", ValueExporter::export([]));
        $this->assertSame("[foo => bar]", ValueExporter::export(["foo" => "bar"]));
    }

    /**
     *
     */
    public function test_deep_array()
    {
        $array = [
            "node" => [
                "foo" => "bar"
            ]
        ];

        $expected = <<<EOF
[
  node => [
    foo => bar
  ]
]
EOF;
        $this->assertSame($expected, ValueExporter::export($array));
    }

    /**
     *
     */
    public function test_loooooong_array()
    {
        $array = [
            "fooooooooooooooooooooooooooooooooooooooooo",
            "baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar",
        ];
        $expected = <<<EOF
[
  0 => fooooooooooooooooooooooooooooooooooooooooo,
  1 => baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar
]
EOF;

        $this->assertSame($expected, ValueExporter::export($array));
    }

    /**
     *
     */
    public function test_object()
    {
        $object = new \stdClass();
        $object->foo = "bar";

        $this->assertSame("Object(stdClass)", ValueExporter::export($object));
    }

    /**
     *
     */
    public function test_resource()
    {
        $file = fopen(__FILE__, 'r');
        $expected = sprintf('Resource(%s#%d)', get_resource_type($file), $file);

        $this->assertSame($expected, ValueExporter::export($file));
    }

    /**
     *
     */
    public function test_date_time()
    {
        $dateTime = new \DateTime('2014-06-10 07:35:40', new \DateTimeZone('UTC'));
        $this->assertSame('Object(DateTime) - 2014-06-10T07:35:40+00:00', ValueExporter::export($dateTime));
    }

    /**
     *
     */
    public function test_date_time_immutable()
    {
        $dateTime = new \DateTimeImmutable('2014-06-10 07:35:40', new \DateTimeZone('UTC'));
        $this->assertSame('Object(DateTimeImmutable) - 2014-06-10T07:35:40+00:00', ValueExporter::export($dateTime));
    }

    /**
     *
     */
    public function test_incomplete_class()
    {
        $foo = new \__PHP_Incomplete_Class();
        $array = new \ArrayObject($foo);
        $array['__PHP_Incomplete_Class_Name'] = 'AppBundle/Foo';
        $this->assertSame('__PHP_Incomplete_Class(AppBundle/Foo)', ValueExporter::export($foo));
    }
}
