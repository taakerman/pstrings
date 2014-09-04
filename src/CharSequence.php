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

/**
 * CharSequence is a generalization over a string
 * and allows different implementations
 */
interface CharSequence {
    /**
     * Get the number of characters (UTF-8) in the string
     * 
     * @return int the number of characters
     */
    function length();
    
    /**
     * Get the character at position $i
     * 
     * @param int $i the position to get byte from
     * @return string containing the char
     * @throws Exception if out of bounds
     */
    function charAt($i);
    
    /**
     * Get the number of bytes in the string 
     * 
     * @return int the number of bytes
     */
    function byteCount();
}
