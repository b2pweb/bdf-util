<?php

namespace Bdf\Util\Console;

/**
 * ByteConverterExtension
 */
trait ByteConverterExtension
{
    /**
     * Convert the given string value in bytes.
     *
     * @param string $value
     *
     * @return int
     */
    public function convertToBytes(string $value): int
    {
        $value = strtolower(trim($value));
        $unit = substr($value, -1);
        $bytes = (int) $value;

        switch ($unit) {
            case 't': $bytes *= 1024;
            // no break
            case 'g': $bytes *= 1024;
            // no break
            case 'm': $bytes *= 1024;
            // no break
            case 'k': $bytes *= 1024;
        }

        return $bytes;
    }
}
