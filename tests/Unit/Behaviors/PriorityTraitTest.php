<?php namespace Tests\Belt\Core\Unit\Behaviors;

use Belt\Core\Behaviors\PriorityInterface;
use Belt\Core\Behaviors\PriorityTrait;
use Illuminate\Database\Eloquent\Model;

class PriorityTraitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers \Belt\Core\Behaviors\PriorityTrait::setPriorityAttribute
     */
    public function test()
    {
        $stub = new PriorityTraitStub();
        $stub->setPriorityAttribute(2);
        $this->assertEquals(2, $stub->priority);
    }

}

class PriorityTraitStub extends Model
    implements PriorityInterface
{
    use PriorityTrait;
}