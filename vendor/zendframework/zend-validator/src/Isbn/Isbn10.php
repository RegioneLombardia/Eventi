<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 */

namespace Zend\Validator\Isbn;

class Isbn10
{
    /**
     * @param int|string $value
     * @return int|string
     */
    public function getChecksum($value)
    {
        $sum = $this->sum($value);
        return $this->checksum($sum);
    }

    /**
     * Calculate the value sum.
     *
     * @param int|string $value
     * @return int
     */
    private function sum($value)
    {
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * $value[$i];
        }

        return $sum;
    }

    /**
     * Calculate the checksum for the value's sum.
     *
     * @param int $sum
     * @return int|string
     */
    private function checksum($sum)
    {
        $checksum = 11 - ($sum % 11);

        if ($checksum == 11) {
            return '0';
        }

        if ($checksum == 10) {
            return 'X';
        }

        return $checksum;
    }
}
