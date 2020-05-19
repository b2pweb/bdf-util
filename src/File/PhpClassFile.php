<?php

namespace Bdf\Util\File;

use SplFileInfo;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * 
 * @link https://github.com/zendframework/zf2/blob/master/library/Zend/File/PhpClassFile.php
 */

/**
 * Locate files containing PHP classes, interfaces, abstracts or traits
 */
class PhpClassFile extends SplFileInfo
{
    /**
     * @var string[]
     */
    protected $classes = [];

    /**
     * @var string[]
     */
    protected $namespaces = [];


    /**
     * Get classe
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->classes[0] ?? '';
    }

    /**
     * Get classe short name
     *
     * @return string
     */
    public function getClassShortName(): string
    {
        $parts = explode('\\', $this->getClass());
        return array_pop($parts);
    }

    /**
     * Get classes
     *
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespaces[0] ?? '';
    }

    /**
     * Get namespaces
     *
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Add class
     *
     * @param string $class
     *
     * @return $this
     */
    public function addClass(string $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    /**
     * Add namespace
     *
     * @param string $namespace
     *
     * @return $this
     */
    public function addNamespace(string $namespace)
    {
        if (in_array($namespace, $this->namespaces)) {
            return $this;
        }

        $this->namespaces[] = $namespace;
        
        return $this;
    }
    
    /**
     * 
     */
    public function extractClassInfo()
    {
        if (!$this->isFile()) {
            return;
        }

        // If not a PHP file, skip
        if ($this->getBasename('.php') == $this->getBasename()) {
            return;
        }
        
        $contents = file_get_contents($this->getRealPath());
        $tokens   = token_get_all($contents);
        $count    = count($tokens);
        $t_trait  = defined('T_TRAIT') ? T_TRAIT : -1; // For preserve PHP 5.3 compatibility
        
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            
            if (!is_array($token)) {
                // single character token found; skip
                $i++;
                continue;
            }
            
            switch ($token[0]) {
                case T_NAMESPACE:
                    // Namespace found; grab it for later
                    $namespace = '';
                    for ($i++; $i < $count; $i++) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            if (';' === $token) {
                                $saveNamespace = false;
                                break;
                            }
                            if ('{' === $token) {
                                $saveNamespace = true;
                                break;
                            }
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        switch ($type) {
                            case T_STRING:
                            case T_NS_SEPARATOR:
                                $namespace .= $content;
                                break;
                        }
                    }
                    if ($saveNamespace) {
                        $savedNamespace = $namespace;
                    }
                    break;
                    
                case $t_trait:
                case T_CLASS:
                case T_INTERFACE:
                    // Abstract class, class, interface or trait found

                    // Get the classname
                    for ($i++; $i < $count; $i++) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        if (T_STRING == $type) {
                            // If a classname was found, set it in the object, and
                            // return boolean true (found)
                            if (!isset($namespace) || null === $namespace) {
                                if (isset($saveNamespace) && $saveNamespace) {
                                    $namespace = $savedNamespace;
                                } else {
                                    $namespace = null;
                                }

                            }
                            $class = (null === $namespace) ? $content : $namespace . '\\' . $content;
                            $this->addClass($class);
                            if ($namespace) {
                                $this->addNamespace($namespace);
                            }
                            $namespace = null;
                            break;
                        }
                    }
                    break;
                    
                default:
                    break;
            }
        }
    }
}
