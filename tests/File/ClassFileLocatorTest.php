<?php

namespace Bdf\Util\File;

use PHPUnit\Framework\TestCase;

/**
 *
 */
class ClassFileLocatorTest extends TestCase
{
    /**
     *
     */
    public function test_constructor_expects_iterator()
    {
        $this->expectException(\InvalidArgumentException::class);

        $iterator = new ClassFileLocator("here");
    }

    /**
     *
     */
    public function test_basic_iteration()
    {
        $iterator = new ClassFileLocator(__DIR__);
        $thisClassShouldBeFound = false;
        $i = 0;

        foreach ($iterator as $classInfo) {
            $i++;

            $this->assertRegExp('/\.php$/', $classInfo->getFilename());

            if (__CLASS__ === $classInfo->getClass()) {
                $thisClassShouldBeFound = true;
            }
        }

        $this->assertTrue($thisClassShouldBeFound);
        $this->assertTrue(1 < $i);
    }

    /**
     *
     */
    public function test_filter()
    {
        $locator = new ClassFileLocator(__FILE__);

        $thisClassShouldBeFound = false;
        $i = 0;

        foreach ($locator as $classInfo) {
            $i++;

            if (__CLASS__ === $classInfo->getClass()) {
                $thisClassShouldBeFound = true;
            }
        }

        $this->assertTrue($thisClassShouldBeFound);
        $this->assertEquals(1, $i);
    }
}