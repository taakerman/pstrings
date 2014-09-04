<?php

namespace Webpixels\Aggregator\Domain\Model\String;

interface BasicString {
    /**
     * @return int the number of characters in the string (not the number of bytes)
     */
    function length();
    
    /**
     * 
     * @param int $i the position to get byte from
     * @return string containing the char
     * @throws Exception if 0 > $i or $i > length()
     */
    function charAt($i);
    
    /**
     * @return int the number of bytes in the string (not the number of characters)
     */
    function byteCount();
}
