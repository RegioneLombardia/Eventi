<?php

/*
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Imagick;

use Imagine\Driver\InfoProvider;
use Imagine\Effects\EffectsInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Utils\Matrix;

/**
 * Effects implementation using the Imagick PHP extension.
 */
class Effects implements EffectsInterface, InfoProvider
{
    /**
     * @var \Imagick
     */
    private $imagick;

    /**
     * Initialize the instance.
     *
     * @param \Imagick $imagick
     */
    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

    /**
     * {@inheritdoc}
     *
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     */
    public function gamma($correction)
    {
        try {
            $this->imagick->gammaImage($correction, \Imagick::CHANNEL_ALL);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to apply gamma correction to the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function negative()
    {
        try {
            $this->imagick->negateImage(false, \Imagick::CHANNEL_ALL);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to negate the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function grayscale()
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_GRAYSCALEEFFECT);
        try {
            $this->imagick->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to grayscale the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function colorize(ColorInterface $color)
    {
        if (!$color instanceof RGB) {
            throw new NotSupportedException('Colorize with non-rgb color is not supported');
        }

        try {
            $this->imagick->colorizeImage((string) $color, new \ImagickPixel(sprintf('rgba(%d, %d, %d, 1)', $color->getRed(), $color->getGreen(), $color->getBlue())));
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to colorize the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function sharpen()
    {
        try {
            $this->imagick->sharpenImage(2, 1);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to sharpen the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function blur($sigma = 1)
    {
        try {
            $this->imagick->gaussianBlurImage(0, $sigma);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to blur the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function brightness($brightness)
    {
        $brightness = (int) round($brightness);
        if ($brightness < -100 || $brightness > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$brightness', -100, 100, $brightness));
        }
        try {
            if (method_exists($this->imagick, 'brightnesscontrastimage')) {
                // Available since Imagick 3.3.0
                $this->imagick->brightnesscontrastimage($brightness, 0);
            } else {
                // This *emulates* brightnesscontrastimage
                $sign = $brightness < 0 ? -1 : 1;
                $v = abs($brightness) / 100;
                $v = (1 / (sin(($v * .99999 * M_PI_2) + M_PI_2))) - 1;
                $this->imagick->modulateimage(100 + $sign * $v * 100, 100, 100);
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to brightness the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function convolve(Matrix $matrix)
    {
        if ($matrix->getWidth() !== 3 || $matrix->getHeight() !== 3) {
            throw new InvalidArgumentException(sprintf('A convolution matrix must be 3x3 (%dx%d provided).', $matrix->getWidth(), $matrix->getHeight()));
        }
        try {
            if (class_exists('ImagickKernel', false) && version_compare(static::getDriverInfo()->getEngineVersion(), '7.0.0') >= 0) {
                $kernel = \ImagickKernel::fromMatrix($matrix->getMatrix());
            } else {
                $kernel = $matrix->getValueList();
            }
            $this->imagick->convolveImage($kernel);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to convolve the image');
        }

        return $this;
    }
}
