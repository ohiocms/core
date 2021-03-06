<?php namespace Tests\Belt\Core\Unit\Behaviors;

use Belt\Core\Behaviors\Teamable;
use Belt\Core\Tests;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery as m;

class TeamableTest extends Tests\BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Behaviors\Teamable::team
     */
    public function test()
    {
        $teamable = new TeamableStub();

        # team
        $this->assertInstanceOf(BelongsTo::class, $teamable->team());
    }

}

class TeamableStub extends Tests\BaseModelStub
{
    use Teamable;

    public function load($relations)
    {

    }

    public function getMorphClass()
    {
        return 'teamable-stubs';
    }

}