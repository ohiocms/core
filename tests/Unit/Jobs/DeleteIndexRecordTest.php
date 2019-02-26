<?php namespace Tests\Belt\Core\Unit\Jobs;

use Belt\Core\Jobs\DeleteIndexRecord;
use Belt\Core\Services\IndexService;
use Tests\Belt\Core;
use Mockery as m;

class DeleteIndexRecordTest extends \Tests\Belt\Core\BeltTestCase
{

    public function setUp()
    {
        parent::setUp();
        DeleteIndexRecordStub::unguard();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Jobs\DeleteIndexRecord::__construct
     * @covers \Belt\Core\Jobs\DeleteIndexRecord::handle
     */
    public function test()
    {
        $item = new DeleteIndexRecordStub(['id' => 123]);

        $service = m::mock(IndexService::class);
        $service->shouldReceive('delete')->with(123, 'test');

        $job = new DeleteIndexRecord($item);
        $job->service = $service;
        $job->handle();
    }

}

class DeleteIndexRecordStub extends \Tests\Belt\Core\Base\BaseModelStub
{
    public function getMorphClass()
    {
        return 'test';
    }
}