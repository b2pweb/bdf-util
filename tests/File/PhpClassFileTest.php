<?php

namespace Bdf\Util\File;

use PHPUnit\Framework\TestCase;

/**
 *
 */
class PhpClassFileTest extends TestCase
{
    /**
     * 
     */
    public function test_add_class()
    {
        $file = new PhpClassFile(__FILE__);
        $file->addClass('TestClass');
        $this->assertEquals('TestClass', $file->getClass());
    }
    
    /**
     * 
     */
    public function test_add_namespace()
    {
        $file = new PhpClassFile(__FILE__);
        $file->addNamespace('TestNamespace');
        $this->assertEquals('TestNamespace', $file->getNamespace());
    }

    /**
     *
     */
    public function test_add_existing_namespace()
    {
        $file = new PhpClassFile(__FILE__);
        $file->addNamespace('TestNamespace');
        $file->addNamespace('TestNamespace');

        $this->assertCount(1, $file->getNamespaces());
    }

    /**
     * 
     */
    public function test_basic_extract()
    {
        $file = new PhpClassFile(__FILE__);
        $file->extractClassInfo();
        
        $this->assertEquals('PhpClassFileTest', $file->getClassShortName());
        
        $this->assertEquals(__CLASS__, $file->getClass());
        $this->assertEquals([__CLASS__], $file->getClasses());
        $this->assertEquals(__NAMESPACE__, $file->getNamespace());
        $this->assertEquals([__NAMESPACE__], $file->getNamespaces());
    }
    
    /**
     * 
     */
    public function test_extract_info_only_on_file()
    {
        $file = new PhpClassFile(__DIR__);
        $file->extractClassInfo();
        
        $this->assertEquals('', $file->getClass());
    }
    
    /**
     * 
     */
    public function test_extract_info_only_on_php_file()
    {
        $file = new PhpClassFile(__DIR__.'/_files/empty.log');
        $file->extractClassInfo();
        
        $this->assertEquals('', $file->getClass());
    }
}