<?php

namespace Bdf\Util\File;

use FilterIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * ClassFileLocator locates files containing PHP classes, interfaces, abstracts or traits
 */
class ClassFileLocator extends FilterIterator
{
    /**
     * File filters
     *
     * @var array
     */
    private $filters;

    /**
     * Create an instance of the locator iterator
     *
     * @param string $path
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(string $path = '.')
    {
        if (is_file($path)) {
            $this->addFilter($path);
            $path = dirname($path);
        } elseif (!is_dir($path)) {
            throw new InvalidArgumentException('Expected a valid directory name');
        }

        parent::__construct(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS)
            )
        );

        $this->setInfoClass(PhpClassFile::class);
    }

    /**
     * Filter for files containing PHP classes, interfaces, or abstracts
     *
     * @return bool
     */
    public function accept(): bool
    {
        $file = $this->getInnerIterator()->current();

        if ($this->shouldSkipFile($file) === true) {
            return false;
        }

        $file->extractClassInfo();
        
        $classes = $file->getClasses();
        
        // No class-type tokens found; return false
        return !empty($classes);
    }

    /**
     * Add a file filter
     *
     * @param string $file
     */
    public function addFilter(string $file)
    {
        $this->filters[$file] = true;
    }

    /**
     * Check whether the file should be skipped
     *
     * @param \SplFileInfo $file
     *
     * @return bool    True to skip the file
     */
    private function shouldSkipFile($file)
    {
        return $this->filters !== null && !isset($this->filters[$file->getPathname()]);
    }
}
