<?php

namespace Bdf\Util\Console;

use PHPUnit\Framework\TestCase;

/**
 *
 */
class ByteConverterExtensionTest extends TestCase
{
    use ByteConverterExtension;

    /**
     *
     */
    public function test_convert()
    {
        $this->assertSame(1024, $this->convertToBytes('1k'));
        $this->assertSame(1024 * 1024, $this->convertToBytes('1M'));
        $this->assertSame(1024 * 1024 * 1024, $this->convertToBytes('1G'));
        $this->assertSame(1024 * 1024 * 1024 * 1024, $this->convertToBytes('1T'));
    }
}
