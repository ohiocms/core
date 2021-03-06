<?php namespace Tests\Belt\Core\Unit\Helpers;

use Belt\Core\Helpers\ArrayHelper;

class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers \Belt\Core\Helpers\ArrayHelper::isAssociative
     * @covers \Belt\Core\Helpers\ArrayHelper::last
     */
    public function testisJson()
    {

        # isAssociative
        $this->assertTrue(ArrayHelper::isAssociative([
            'foo' => 'bar',
        ]));

        $this->assertFalse(ArrayHelper::isAssociative([]));
        $this->assertFalse(ArrayHelper::isAssociative(['foo', 'bar']));

        # last
        $arr = ['foo', 'bar'];
        $this->assertEquals('bar', ArrayHelper::last($arr));
    }
}
