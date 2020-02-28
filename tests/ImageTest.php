<?php

namespace Tests;

use TypeError;
use Generator;
use Rescaler\Image;
use ArgumentCountError;

/**
 * Class ImageTest
 * @package Tests
 */
class ImageTest extends TestCase
{
    /** @test
     * @noinspection PhpParamsInspection
     */
    public function a_image_needs_dimensions()
    {
        $this->expectException(ArgumentCountError::class);
        new Image();
    }

    /** @test */
    public function a_image_with_dimensions_can_be_instantiated()
    {
        $image = new Image(30, 50);
        $this->assertInstanceOf(Image::class, $image);
    }

    /** @test */
    public function numeric_values_are_rounded()
    {
        $image = new Image('123.5', 23.5656);
        $this->assertIsInt($image->getHeight());
        $this->assertIsInt($image->getWidth());
        $this->assertEquals(123, $image->getHeight());
        $this->assertEquals(23, $image->getWidth());
    }

    /** @test */
    public function non_numeric_values_cannot_be_dimension()
    {
        $this->expectException(TypeError::class);
        new Image('abc', 10);
    }

    /**
     * @dataProvider ratioGenerators
     * @test
     *
     * @param $height
     * @param $width
     * @param $result
     */
    public function a_image_has_a_ratio($height, $width, $result)
    {
        $image = new Image($height, $width);
        $this->assertEquals($result, $image->ratio());
    }

    /**
     * @return Generator
     */
    public function ratioGenerators()
    {
        yield([ 20, 30, [ 'height' => 2, 'width' => 3, 'divider' => 10 ] ]);
        yield([ 640, 480, [ 'height' => 4, 'width' => 3, 'divider' => 160 ] ]);
        yield([ 1024, 1200, [ 'height' => 64, 'width' => 75, 'divider' => 16 ] ]);
        yield([ 1920, 1200, [ 'height' => 8, 'width' => 5, 'divider' => 240 ] ]);
        yield([ 1920, 1024, [ 'height' => 15, 'width' => 8, 'divider' => 128 ] ]);
        yield([ 3840, 2560, [ 'height' => 3, 'width' => 2, 'divider' => 1280 ] ]);
    }

    /** @test */
    public function an_image_has_an_area()
    {
        $image = new Image(30, 30);
        $this->assertEquals(900, $image->area());
    }

    /** @test */
    public function an_image_can_be_cloned()
    {
        $image = new Image(200, 300);
        $cloned = $image->clone();
        $this->assertInstanceOf(Image::class, $cloned);
        $this->assertEquals($image, $cloned);
    }

    /** @test */
    public function image_can_be_rescaled()
    {
        $image = new Image(300, 200);

        $newImage = $image->rescale();

        $this->assertInstanceOf(Image::class, $newImage);
    }

    /**
     * @dataProvider smallAreas
     *
     * @param $height
     * @param $width
     * @param $result
     */
    public function a_rescale_doesnt_change_small_dimensions($height, $width, $result)
    {
        $image = new Image($height, $width);

        $cloned = $image->clone();
        $rescaled = $cloned->rescale();

        $this->assertEquals($image->area(), $rescaled->area());
        $this->assertEquals($image->area(), $result);
        $this->assertLessThanOrEqual($image::IMAGE_SIZE_LIMIT, $rescaled->area());
    }

    /**
     * @return Generator
     */
    public function smallAreas()
    {
        yield([ 10, 20, 200 ]);
        yield([ 500, 20, 10000 ]);
        yield([ 1000, 2000, 2000000 ]);
        yield([ 1024, 1920, 1966080 ]);
        yield([ 3840, 2400, 9216000 ]);
        yield([ 1234, 6543, 8074062 ]);
        yield([ 7899, 123, 971577 ]);
    }

    /**
     * @dataProvider bigAreas
     * @test
     *
     * @param $height
     * @param $width
     */
    public function a_rescaler_converts_big_areas($height, $width, $rescaledHeight, $rescaledWidth)
    {
        $image = new Image($height, $width);
        $cloned = $image->clone();

        $this->assertGreaterThanOrEqual($image::IMAGE_SIZE_LIMIT, $cloned->area());
        $cloned->rescale();
        $this->assertLessThanOrEqual($image::IMAGE_SIZE_LIMIT, $cloned->area());

        $this->assertNotEquals($image->getHeight(), $cloned->getHeight());
        $this->assertNotEquals($image->getWidth(), $cloned->getWidth());

        $this->assertEquals($rescaledHeight, $cloned->getHeight());
        $this->assertEquals($rescaledWidth, $cloned->getWidth());
    }

    /**
     * @return Generator
     */
    public function bigAreas()
    {
        yield([ 5000, 4000, 2236, 1788 ]);
        yield([ 15800, 14500, 2087, 1915 ]);
        yield([ 8000000, 4000, 89442, 44 ]);
    }
}