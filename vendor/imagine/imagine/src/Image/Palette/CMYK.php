<?php

/*
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image\Palette;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\CMYK as CMYKColor;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Profile;
use Imagine\Image\ProfileInterface;

/**
 * The CMYK palette.
 */
class CMYK implements PaletteInterface
{
    /**
     * @var \Imagine\Image\Palette\ColorParser
     */
    private $parser;

    /**
     * @var \Imagine\Image\ProfileInterface|null
     */
    private $profile;

    /**
     * @var \Imagine\Image\Palette\Color\CMYK[]
     */
    private static $colors = array();

    public function __construct()
    {
        $this->parser = new ColorParser();
    }

    /**
     * {@inheritdoc}
     *
     */
    public function name()
    {
        return PaletteInterface::PALETTE_CMYK;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function pixelDefinition()
    {
        return array(
            ColorInterface::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE,
        );
    }

    /**
     * {@inheritdoc}
     *
     */
    public function supportsAlpha()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getChannelsMaxValue()
    {
        return 100;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function color($color, $alpha = null)
    {
        if ($alpha !== null && $alpha !== 100) {
            throw new InvalidArgumentException('CMYK palette does not support alpha');
        }

        $color = $this->parser->parseToCMYK($color);
        $index = sprintf('cmyk(%d, %d, %d, %d)', $color[0], $color[1], $color[2], $color[3]);

        if (array_key_exists($index, self::$colors) === false) {
            self::$colors[$index] = new CMYKColor($this, $color);
        }

        return self::$colors[$index];
    }

    /**
     * {@inheritdoc}
     *
     */
    public function blend(ColorInterface $color1, ColorInterface $color2, $amount)
    {
        if (!$color1 instanceof CMYKColor || !$color2 instanceof CMYKColor) {
            throw new RuntimeException('CMYK palette can only blend CMYK colors');
        }
        $max = $this->getChannelsMaxValue();

        return $this->color(array(
            min($max, $color1->getCyan() + $color2->getCyan() * $amount),
            min($max, $color1->getMagenta() + $color2->getMagenta() * $amount),
            min($max, $color1->getYellow() + $color2->getYellow() * $amount),
            min($max, $color1->getKeyline() + $color2->getKeyline() * $amount),
        ));
    }

    /**
     * {@inheritdoc}
     *
     */
    public function useProfile(ProfileInterface $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function profile()
    {
        if (!$this->profile) {
            $this->profile = Profile::fromPath(__DIR__ . '/../../resources/Adobe/CMYK/USWebUncoated.icc');
        }

        return $this->profile;
    }
}
