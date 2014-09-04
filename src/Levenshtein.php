<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Webpixels\Aggregator\Domain\Model\String;

use Webpixels\Aggregator\Domain\Model\String\BasicString;

use Exception;

class Levenshtein {
    
    /**
     * <p>Find the Levenshtein distance between two Strings if it's less than or equal to a given
     * threshold.</p>
     *
     * <p>This is the number of changes needed to change one String into
     * another, where each change is a single character modification (deletion,
     * insertion or substitution).</p>
     *
     * <p>This implementation follows from Algorithms on Strings, Trees and Sequences by Dan Gusfield
     * and Chas Emerick's implementation of the Levenshtein distance algorithm from
     * <a href="http://www.merriampark.com/ld.htm">http://www.merriampark.com/ld.htm</a></p>
     *
     * <pre>
     * StringUtils.getLevenshteinDistance(null, *, *)             = IllegalArgumentException
     * StringUtils.getLevenshteinDistance(*, null, *)             = IllegalArgumentException
     * StringUtils.getLevenshteinDistance(*, *, -1)               = IllegalArgumentException
     * StringUtils.getLevenshteinDistance("","", 0)               = 0
     * StringUtils.getLevenshteinDistance("aaapppp", "", 8)       = 7
     * StringUtils.getLevenshteinDistance("aaapppp", "", 7)       = 7
     * StringUtils.getLevenshteinDistance("aaapppp", "", 6))      = -1
     * StringUtils.getLevenshteinDistance("elephant", "hippo", 7) = 7
     * StringUtils.getLevenshteinDistance("elephant", "hippo", 6) = -1
     * StringUtils.getLevenshteinDistance("hippo", "elephant", 7) = 7
     * StringUtils.getLevenshteinDistance("hippo", "elephant", 6) = -1
     * </pre>
     *
     * @param s  the first String, must not be null
     * @param t  the second String, must not be null
     * @param threshold the target threshold, must not be negative
     * @return result distance, or {@code -1} if the distance would be greater than the threshold
     * @throws IllegalArgumentException if either String input {@code null} or negative threshold
     */
    public function getDistance(BasicString $s, BasicString $t, $threshold) {
        if ($s == null || $t == null) {
            throw new Exception("Strings must not be null");
        }
        if ($threshold < 0) {
            throw new Exception("Threshold must not be negative");
        }

        /*
        This implementation only computes the distance if it's less than or equal to the
        threshold value, returning -1 if it's greater.  The advantage is performance: unbounded
        distance is O(nm), but a bound of k allows us to reduce it to O(km) time by only
        computing a diagonal stripe of width 2k + 1 of the cost table.
        It is also possible to use this to compute the unbounded Levenshtein distance by starting
        the threshold at 1 and doubling each time until the distance is found; this is O(dm), where
        d is the distance.

        One subtlety comes from needing to ignore entries on the border of our stripe
        eg.
        p[] = |#|#|#|*
        d[] =  *|#|#|#|
        We must ignore the entry to the left of the leftmost member
        We must ignore the entry above the rightmost member

        Another subtlety comes from our stripe running off the matrix if the strings aren't
        of the same size.  Since string s is always swapped to be the shorter of the two,
        the stripe will always run off to the upper right instead of the lower left of the matrix.

        As a concrete example, suppose s is of length 5, t is of length 7, and our threshold is 1.
        In this case we're going to walk a stripe of length 3.  The matrix would look like so:

           1 2 3 4 5
        1 |#|#| | | |
        2 |#|#|#| | |
        3 | |#|#|#| |
        4 | | |#|#|#|
        5 | | | |#|#|
        6 | | | | |#|
        7 | | | | | |

        Note how the stripe leads off the table as there is no possible way to turn a string of length 5
        into one of length 7 in edit distance of 1.

        Additionally, this implementation decreases memory usage by using two
        single-dimensional arrays and swapping them back and forth instead of allocating
        an entire n by m matrix.  This requires a few minor changes, such as immediately returning
        when it's detected that the stripe has run off the matrix and initially filling the arrays with
        large values so that entries we don't compute are ignored.

        See Algorithms on Strings, Trees and Sequences by Dan Gusfield for some discussion.
         */

        $n = $s->length(); // length of s
        $m = $t->length(); // length of t

        // if one string is empty, the edit distance is necessarily the length of the other
        if ($n == 0) {
            return $m <= $threshold ? $m : -1;
        } else if ($m == 0) {
            return $n <= $threshold ? $n : -1;
        }

        if ($n > $m) {
            // swap the two strings to consume less memory
            $tmp = $s;
            $s = $t;
            $t = $tmp;
            $n = $m;
            $m = $t->length();
        }

        $p = array(); // 'previous' cost array, horizontally
        $p_len = $n + 1;
        $d = array(); // cost array, horizontally
        $d_len = $n + 1;
        $_d = array(); // placeholder to assist in swapping p and d

        // fill in starting table values
        $boundary = min(array($n, $threshold)) + 1;
        for ($i = 0; $i < $boundary; $i++) {
            $p[$i] = $i;
        }
        // these fills ensure that the value above the rightmost entry of our
        // stripe will be ignored in following loop iterations
        //Arrays.fill(p, boundary, p.length, Integer.MAX_VALUE);
        $p = array_fill($boundary, $p_len, PHP_INT_MAX);
        //Arrays.fill(d, Integer.MAX_VALUE);
        $d = array_fill(0, $n+1, PHP_INT_MAX);

        // iterates through t
        for ($j = 1; $j <= $m; $j++) {
            $t_j = $t->charAt($j - 1); // jth character of t
            $d[0] = $j;

            // compute stripe indices, constrain to array size
            $min = max(array(1, $j - $threshold));
            $max = ($j > PHP_INT_MAX - $threshold) ? $n : min(array($n, $j + $threshold));

            // the stripe may lead off of the table if s and t are of different sizes
            if ($min > $max) {
                return -1;
            }

            // ignore entry left of leftmost
            if ($min > 1) {
                $d[$min - 1] = PHP_INT_MAX;
            }

            // iterates through [min, max] in s
            for ($i = $min; $i <= $max; $i++) {
                if ($s{$i - 1} == $t_j) {
                    // diagonally left and up
                    $d[$i] = $p[$i - 1];
                } else {
                    // 1 + minimum of cell to the left, to the top, diagonally left and up
                    $d[$i] = 1 + min(array(min(array($d[$i - 1], $p[$i])), $p[$i - 1]));
                }
            }

            // copy current distance counts to 'previous row' distance counts
            $_d = $p;
            $p = $d;
            $d = $_d;
        }

        // if p[n] is greater than the threshold, there's no guarantee on it being the correct
        // distance
        if ($p[$n] <= $threshold) {
            return $p[$n];
        }
        return -1;
    }
}
