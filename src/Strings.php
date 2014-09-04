<?php

/*
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE. 
 * 
 * You may copy and reuse as you please
 */

namespace Taakerman\PString;

use Taakerman\PString\String;
use Taakerman\PString\String;
use Taakerman\PString\Charsets;

/**
 * A utility class for working with strings
 */
class Strings {
    /**
     * Returns an empty string on null or returns the original string
     * 
     * @param \Taakerman\PString\String $s
     * @return \Taakerman\PString\String 
     */
    public static function nullToEmpty(String $s) {
        return ($s == null) ? new String("", Charsets::UTF8, true) : $s;
    }
    
    /**
     * Returns null if empty or the original string
     * 
     * @param \Taakerman\PString\String $s
     * @return \Taakerman\PString\String
     */
    public static function emptyToNull(String $s) {
        return ($s->length() == 0) ? null : $s;
    }
    
    /**
     * Null safe trim for string
     * 
     * @param \Taakerman\PString\String $s
     * @return \Taakerman\PString\String
     */
    public static function trim(String $s) {
        return self::nullToEmpty($s)->trim();
    }
}
