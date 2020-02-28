<?php

namespace Rescaler;

/**
 * Class Image
 * @package Rescaler
 */
class Image
{
    /**
     * Image size limit in pixels
     */
    const IMAGE_SIZE_LIMIT = 4000000;

    /**
     * @var int
     */
    private int $height;

    /**
     * @var int
     */
    private int $width;

    /**
     * Image constructor.
     *
     * @param int $height
     * @param int $width
     */
    public function __construct(int $height, int $width)
    {
        $this->height = $height;
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $param
     */
    public function setWidth(int $param)
    {
        $this->width = $param;
    }

    /**
     * @return array
     */
    public function ratio()
    {
        $divider = $this->greatestCommonDivider($this->height, $this->width);

        return [
            'height' => $this->height / $divider,
            'width' => $this->width / $divider,
            'divider' => $divider,
        ];
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function greatestCommonDivider($a, $b)
    {
        if ($a == 0 || $b == 0)
            return abs(max(abs($a), abs($b)));

        $r = $a % $b;
        return ($r != 0) ?
            $this->greatestCommonDivider($b, $r) :
            abs($b);
    }

    /**
     * @return int
     */
    public function area()
    {
        return $this->width * $this->height;
    }

    /**
     * @return Image
     */
    public function clone()
    {
        return clone($this);
    }

    /**
     * @return $this
     */
    public function rescale()
    {
        $ratio = $this->ratio();

        $magicNumber = sqrt(self::IMAGE_SIZE_LIMIT / ($ratio['height'] * $ratio['width']));

        $this->height = floor($magicNumber * $ratio['height']);
        $this->width = floor($magicNumber * $ratio['width']);

        return $this;
    }
}