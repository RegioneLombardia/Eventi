<?php

/*
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image\Palette;

use Imagine\Exception\InvalidArgumentException;

class ColorParser
{
    /**
     * Parses a color to a RGB tuple.
     *
     * @param string|array|int $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return array
     */
    public function parseToRGB($color)
    {
        $color = $this->parse($color);

        if (count($color) === 4) {
            $color = array(
                255 * (1 - $color[0] / 100) * (1 - $color[3] / 100),
                255 * (1 - $color[1] / 100) * (1 - $color[3] / 100),
                255 * (1 - $color[2] / 100) * (1 - $color[3] / 100),
            );
        }

        return $color;
    }

    /**
     * Parses a color to a CMYK tuple.
     *
     * @param string|array|int $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return array
     */
    public function parseToCMYK($color)
    {
        $color = $this->parse($color);

        if (count($color) === 3) {
            $r = $color[0] / 255;
            $g = $color[1] / 255;
            $b = $color[2] / 255;

            $k = 1 - max($r, $g, $b);

            $color = array(
                $k === 1 ? 0 : round((1 - $r - $k) / (1 - $k) * 100),
                $k === 1 ? 0 : round((1 - $g - $k) / (1 - $k) * 100),
                $k === 1 ? 0 : round((1 - $b - $k) / (1 - $k) * 100),
                round($k * 100),
            );
        }

        return $color;
    }

    /**
     * Parses a color to a grayscale value.
     *
     * @param string|array|int $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return int[]
     */
    public function parseToGrayscale($color)
    {
        if (is_array($color) && count($color) === 1) {
            return array((int) round(array_pop($color)));
        }

        $color = array_unique($this->parse($color));

        if (count($color) !== 1) {
            throw new InvalidArgumentException('The provided color has different values of red, green and blue components. Grayscale colors must have the same values for these.');
        }

        return $color;
    }

    /**
     * Parses a color.
     *
     * @param string|array|int $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return int[]
     */
    private function parse($color)
    {
        if (!is_string($color) && !is_array($color) && !is_int($color)) {
            throw new InvalidArgumentException(sprintf('Color must be specified as a hexadecimal string, array or integer, %s given', gettype($color)));
        }

        if (is_array($color)) {
            if (count($color) === 3 || count($color) === 4) {
                return array_map(
                    function ($component) {
                        return (int) round($component);
                    },
                    array_values($color)
                );
            }
            throw new InvalidArgumentException('Color argument if array, must look like array(R, G, B), or array(C, M, Y, K) where R, G, B are the integer values between 0 and 255 for red, green and blue or cyan, magenta, yellow and black color indexes accordingly');
        }
        if (is_string($color)) {
            if (strpos($color, 'cmyk(') === 0) {
                $substrColor = substr($color, 5, strlen($color) - 6);

                $components = array_map(function ($component) {
                    return (int) round(trim($component, ' %'));
                }, explode(',', $substrColor));

                if (count($components) !== 4) {
                    throw new InvalidArgumentException(sprintf('Unable to parse color %s', $color));
                }

                return $components;
            } else {
                $color = ltrim($color, '#');

                if (strlen($color) !== 3 && strlen($color) !== 6) {
                    throw new InvalidArgumentException(sprintf('Color must be a hex value in regular (6 characters) or short (3 characters) notation, "%s" given', $color));
                }

                if (strlen($color) === 3) {
                    $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
                }

                $color = array_map('hexdec', str_split($color, 2));
            }
        }

        if (is_int($color)) {
            $color = array(255 & ($color >> 16), 255 & ($color >> 8), 255 & $color);
        }

        return $color;
    }
}
