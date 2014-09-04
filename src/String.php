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

use Taakerman\PString\CharSequence;

use Joomla\String\String as JS;

/**
 * The String class is the main string class in the pstring library
 */
class String implements CharSequence {
    private $utf8;
    private $ascii;
    
    const INTERNAL_ENCODING = 'UTF-8';
    
    public function __construct($str, $encoding = null, $asciiOnly = null) {
        if ($encoding == null) {
            $encoding = mb_detect_encoding($str, mb_detect_order(), true);
        }
        
        if ($encoding != self::INTERNAL_ENCODING) {
            $this->utf8 = mb_convert_encoding($str, self::INTERNAL_ENCODING, $encoding);
        }
        
        $this->utf8 = mb_convert_encoding($str, self::INTERNAL_ENCODING);
        if ($asciiOnly == null) {
            $this->ascii = JS::is_ascii($this->utf8);
        } else {
            $this->ascii = $asciiOnly;
        }
    }
    
    private function from($str) {
        return new String($str, self::INTERNAL_ENCODING, $this->ascii);
    }
    
    public function isAscii() {
        return $this->ascii;
    }
    
    public function length() {
        if ($this->isAscii()) {
            return strlen($this->utf8);
        }
        
        // fallback to utf8
        return JS::strlen($this->utf8);
    }
    
    public function native($encoding = null) {
        if ($encoding == null) {
            $encoding = self::INTERNAL_ENCODING;
        }
        
        if ($encoding != self::INTERNAL_ENCODING) {
            return mb_convert_encoding($this->utf8, $encoding, self::INTERNAL_ENCODING);
        }
        
        return $this->utf8;
    }
    
    public function substr($start, $length) {
        if ($this->isAscii()) {
            return $this->from(substr($this->utf8, $start, $length));
        }
        
        // fallback to utf8
        return $this->from(JS::substr($this->utf8, $start, $length));
    }
    
    public function startsWith(String $str) {
        return 0 === mb_strpos($this->utf8, $str->native(), 0, self::INTERNAL_ENCODING);
    }
    
    public function endsWith(String $str) {
        return mb_strrpos($this->utf8, $str->native(), 0, self::INTERNAL_ENCODING)
            === $this->length() - $str->length();
    }
    
    public function copy() {
        return $this->from($this->utf8);
    }
    
    public function trim($charlist) {
        if ($charlist == null) {
            return $this->from(trim($this->utf8));
        } 
        
        // fallback to utf8
        return $this->from(JS::trim($this->utf8, $charlist));
    }
    
    public function toLower() {
        if ($this->isAscii()) {
            return $this->from(strtolower($this->utf8));
        }
        
        // fallback to utf8
        return $this->from(JS::strtolower($this->utf8));
    }
    
    public function toUpper() {
        if ($this->isAscii()) {
            return $this->from(strtoupper($this->utf8));
        }
        
        // fallback to utf8
        return $this->from(JS::strtoupper($this->utf8));
    }
    
    public function pos(String $str, $offset = 0) {
        if ($this->isAscii() && $str->isAscii()) {
            return strpos($this->utf8, $str->native(), $offset);
        }
        
        // fallback to utf8
        return JS::strpos($this->utf8, $str->native(), $offset);
    }
    
    public function rpos(String $str, $offset = 0) {
        if ($this->isAscii() && $str->isAscii()) {
            return strrpos($this->utf8, $str->native(), $offset);
        }
        
        // fallback to utf8
        return JS::strrpos($this->utf8, $str->native(), $offset);
    }

    public function byteCount() {
        return strlen($this->utf8);
    }

    public function charAt($i) {
        return $this->substr($i, 1);
    }

}