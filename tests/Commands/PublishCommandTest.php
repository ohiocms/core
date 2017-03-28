<?php

use Mockery as m;
use Belt\Core\Commands\PublishCommand;
use Belt\Core\Services\PublishService;

class PublishCommandTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Commands\PublishCommand::handle
     */
    public function testHandle()
    {

        $cmd = $this->getMockBuilder(PublishCommand::class)
            ->setMethods(['getService', 'info', 'warn'])
            ->getMock();

        $service = $this->getMockBuilder(PublishService::class)
            ->setMethods(['publish'])
            ->getMock();

        $service->created = ['one', 'two', 'three'];
        $service->modified = ['one', 'two', 'three'];
        $service->ignored = ['one', 'two', 'three'];

        $service->expects($this->once())->method('publish');

        $cmd->expects($this->once())->method('getService')->willReturn($service);
        $cmd->expects($this->exactly(8))->method('info');
        $cmd->expects($this->exactly(4))->method('warn');

        $cmd->handle();
    }

    /**
     * @covers \Belt\Core\Commands\PublishCommand::service
     */
    public function testService()
    {
        $cmd = m::mock(PublishCommand::class . '[option]');
        $cmd->shouldReceive('option')->with('force')->andReturn(false);
        $cmd->shouldReceive('option')->with('path')->andReturn('test');

        $this->assertInstanceOf(PublishService::class, $cmd->service());
    }
}
