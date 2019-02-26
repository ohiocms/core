<?php

namespace Tests\Belt\Core\Unit\Services;

use Belt\Core\Services\PublishService;
use Tests\Belt\Core\BeltTestCase;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery as m;

class PublishServiceTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        app()['config']->set('belt.core.publish.history_path', 'test');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @covers \Belt\Core\Services\PublishService::createHistoryFromTable
     * @covers \Belt\Core\Services\PublishService::addHistory
     */
    public function testIsolated()
    {
        # createHistoryFromTable
        # addHistory
        $row = (object) ['path' => '/test', 'hash' => '12345z', 'updated_at' => date('Y-m-d H:i:s')];
        $service = m::mock(PublishService::class . '[writeHistoryToFile]');
        $service->shouldReceive('writeHistoryToFile')->once()->andReturnSelf();
        $schemaClass = m::mock('overload:' . Schema::class);
        $schemaClass->shouldReceive('hasTable')->once()->with('publish_history')->andReturn(true);
        DB::shouldReceive('table')->once()->with('publish_history')->andReturnSelf();
        DB::shouldReceive('orderBy')->once()->with('path')->andReturnSelf();
        DB::shouldReceive('get')->once()->andReturn([$row]);
        $service->createHistoryFromTable();
        $this->assertEquals(md5('12345z'), array_get($service->history, '/test.hash'));
    }

    /**
     * @covers \Belt\Core\Services\PublishService::__construct
     * @covers \Belt\Core\Services\PublishService::prune
     * @covers \Belt\Core\Services\PublishService::publish
     * @covers \Belt\Core\Services\PublishService::getHistoryFiles
     * @covers \Belt\Core\Services\PublishService::getPreviousHistoryContents
     * @covers \Belt\Core\Services\PublishService::readHistoryFromFile
     * @covers \Belt\Core\Services\PublishService::publishDir
     * @covers \Belt\Core\Services\PublishService::publishFiles
     * @covers \Belt\Core\Services\PublishService::createFile
     * @covers \Belt\Core\Services\PublishService::replaceFile
     * @covers \Belt\Core\Services\PublishService::putFile
     * @covers \Belt\Core\Services\PublishService::writeHistoryToFile
     * @covers \Belt\Core\Services\PublishService::getHistoryHash
     */
    public function test()
    {
        $options = [
            'key' => 'test',
            'force' => true,
            'prune' => true,
            'dirs' => [
                'foo',
                'bar',
            ],
            'files' => [
                'foo.txt',
                'bar.tst',
            ],
            'include' => 'foo,,',
            'exclude' => 'bar',
        ];

        # __construct
        $service = new PublishService($options);
        $this->assertEquals($options['key'], $service->key);
        $this->assertEquals($options['force'], $service->force);
        $this->assertEquals($options['prune'], $service->prune);
        $this->assertEquals($options['dirs'], $service->dirs);
        $this->assertEquals($options['files'], $service->files);

        # prune
        $files = [
            'test/core/20100101000001.txt',
            'test/core/20100101000002.txt',
            'test/core/20100101000003.txt',
            'test/core/20100101000004.txt',
            'test/core/20100101000005.txt',
            'test/core/20100102000001.txt',
            'test/core/20100102000002.txt',
            'test/core/20100102000003.txt',
            'test/core/20100103000001.txt',
            'test/core/20100103000002.txt',
            'test/core/20100103000003.txt',
            'test/core/20100103000004.txt',
            'test/core/20100104000001.txt',
            'test/core/20100105000001.txt',
            'test/core/20100106000001.txt',
        ];
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('allFiles')->once()->with('test/core')->andReturn($files);
        $disk->shouldReceive('delete')->once()->with('test/core/20100103000001.txt')->andReturnSelf();
        $disk->shouldReceive('delete')->once()->with('test/core/20100101000005.txt')->andReturnSelf();
        $disk->shouldReceive('delete')->once()->with('test/core/20100101000004.txt')->andReturnSelf();
        $disk->shouldReceive('delete')->once()->with('test/core/20100101000003.txt')->andReturnSelf();
        $disk->shouldReceive('delete')->once()->with('test/core/20100101000002.txt')->andReturnSelf();
        $disk->shouldReceive('delete')->once()->with('test/core/20100101000001.txt')->andReturnSelf();
        $service = m::mock(PublishService::class . '[disk]');
        $service->key = 'core';
        $service->shouldReceive('disk')->andReturn($disk);
        $service->prune();

        # publish
        $service = m::mock(PublishService::class . '[prune,readHistoryFromFile,publishDir,publishFiles,writeHistoryToFile]');
        $service->prune = true;
        $service->dirs = ['foo' => 'bar', 'hello' => 'world'];
        $service->files = $files = ['foo.txt', 'bar.txt'];
        $service->shouldReceive('prune')->once()->andReturnSelf();
        $service->shouldReceive('readHistoryFromFile')->once()->andReturnSelf();
        $service->shouldReceive('publishDir')->once()->with('foo', 'bar')->andReturnSelf();
        $service->shouldReceive('publishDir')->once()->with('hello', 'world')->andReturnSelf();
        $service->shouldReceive('publishFiles')->once()->with($files)->andReturnSelf();
        $service->shouldReceive('writeHistoryToFile')->once()->andReturnSelf();
        $service->publish();

        # getHistoryFiles
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('allFiles')->once()->with('test/core');
        $service = m::mock(PublishService::class . '[disk]');
        $service->key = 'core';
        $service->shouldReceive('disk')->andReturn($disk);
        $service->getHistoryFiles();

        # getPreviousHistoryContents 1
        $files = [
            'database/history/publish/core/20190218000001.txt',
            'database/history/publish/core/20190218000002.txt',
        ];
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('get')->once()->with($files[1]);
        $service = m::mock(PublishService::class . '[disk,getHistoryFiles]');
        $service->key = 'core';
        $service->shouldReceive('disk')->andReturn($disk);
        $service->shouldReceive('getHistoryFiles')->once()->andReturn($files);
        $service->getPreviousHistoryContents();

        # getPreviousHistoryContents 2
        $files = [
            'database/history/publish/core/20190218000001.txt',
        ];
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('get')->once()->with($files[0]);
        $service = m::mock(PublishService::class . '[disk,getHistoryFiles,createHistoryFromTable]');
        $service->key = 'core';
        $service->shouldReceive('disk')->andReturn($disk);
        $service->shouldReceive('getHistoryFiles')->once()->andReturn([]);
        $service->shouldReceive('getHistoryFiles')->once()->andReturn($files);
        $service->shouldReceive('createHistoryFromTable')->once();
        $service->getPreviousHistoryContents();

        # readHistoryFromFile
        $file_contents = file_get_contents(__DIR__ . '/../../assets/publish-service-1.txt');
        $service = m::mock(PublishService::class . '[getPreviousHistoryContents,addHistory]');
        $service->shouldReceive('getPreviousHistoryContents')->once()->andReturn($file_contents);
        $service->shouldReceive('addHistory')->once()->with('path/to/file1.php', 'hash1', '2019-02-01 00:00:01');
        $service->shouldReceive('addHistory')->once()->with('path/to/file2.php', 'hash2', '2019-02-01 00:00:02');
        $service->readHistoryFromFile();

        # publishDir
        $files = [
            'path/to/src/included/file1.php',
            'path/to/src/included/excluded-file2.php',
        ];
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('allFiles')->once()->with('path/to/src')->andReturn($files);
        $service = m::mock(PublishService::class . '[disk,evalFile]');
        $service->include = ['included'];
        $service->exclude = ['excluded'];
        $service->shouldReceive('disk')->andReturn($disk);
        $service->shouldReceive('evalFile')->once()->with('path/to/src/included/file1.php', 'path/to/target/included/file1.php');
        $service->publishDir('path/to/src', 'path/to/target');

        # publishFiles
        $files = [
            'path/to/src/included/file1.php' => 'path/to/target/included/file1.php',
            'path/to/src/included/excluded-file2.php' => 'path/to/target/included/excluded-file2.php',
        ];
        $service = m::mock(PublishService::class . '[evalFile]');
        $service->files = $files;
        $service->include = ['included'];
        $service->exclude = ['excluded'];
        $service->shouldReceive('evalFile')->once()->with('path/to/src/included/file1.php', 'path/to/target/included/file1.php');
        $service->publishFiles();

        # createFile
        $service = m::mock(PublishService::class . '[putFile]');
        $service->shouldReceive('putFile')->once()->with('path/to/target/file.php', 'foo')->andReturn(true);
        $this->assertEquals(true, $service->createFile('path/to/target/file.php', 'foo'));
        $this->assertEquals(['path/to/target/file.php'], $service->created);

        # replaceFile
        $service = m::mock(PublishService::class . '[putFile]');
        $service->shouldReceive('putFile')->once()->with('path/to/target/file.php', 'foo')->andReturn(true);
        $this->assertEquals(true, $service->replaceFile('path/to/target/file.php', 'foo'));
        $this->assertEquals(['path/to/target/file.php'], $service->modified);

        # putFile
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('put')->once()->with('path/to/target/file.php', 'foo')->andReturn(true);
        $service = m::mock(PublishService::class . '[disk,addHistory]');
        $service->shouldReceive('disk')->andReturn($disk);
        $service->shouldReceive('addHistory')->once()->with('path/to/target/file.php', 'foo');
        $this->assertEquals(true, $service->putFile('path/to/target/file.php', 'foo'));

        # writeHistoryToFile
        $file_contents = file_get_contents(__DIR__ . '/../../assets/publish-service-2.txt');
        $disk = m::mock(FilesystemAdapter::class);
        $disk->shouldReceive('put')->once()->with('test/core/20190201000001.txt', $file_contents)->andReturn(true);
        $service = m::mock(PublishService::class . '[disk]');
        $service->key = 'core';
        $service->force = true;
        $service->history = [
            'path/to/file1.php' => ['hash' => 'hash1', 'timestamp' => '2019-01-01 00:00:01'],
            'path/to/another/file2.php' => ['hash' => 'hash2', 'timestamp' => '2019-01-01 00:00:02'],
        ];
        $service->shouldReceive('disk')->andReturn($disk);
        Carbon::setTestNow(Carbon::create(2019, 2, 1, 0, 0, 1));
        $service->writeHistoryToFile();
        Carbon::setTestNow(null);

        # getHistoryHash
        $service = new PublishService($options);
        $service->history = [
            'path/to/file1.php' => ['hash' => 'hash1', 'timestamp' => '2019-01-01 00:00:01'],
            'path/to/another/file2.php' => ['hash' => 'hash2', 'timestamp' => '2019-01-01 00:00:02'],
        ];
        $this->assertEquals('hash1', $service->getHistoryHash('path/to/file1.php'));
        $this->assertNull($service->getHistoryHash('path/to/missing-file.php'));
    }

    /**
     * @covers \Belt\Core\Services\PublishService::evalFile
     */
    public function testEvalFile()
    {

        $disk = m::mock(FilesystemAdapter::class);

        $service = m::mock(PublishService::class . '[disk,putFile]');
        $service->shouldReceive('disk')->andReturn($disk);

        # file is exempt
        $this->assertNull($service->evalFile('path/to/.DS_Store', 'path/to/target/.DS_Store'));

        # file does not exist
        $disk->shouldReceive('get')->once()->with('path/to/file1.php')->andReturn('foo');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file1.php')->andReturn(false);
        $service->shouldReceive('putFile')->once()->with('path/to/target/file1.php', 'foo')->andReturn(true);
        $this->assertTrue($service->evalFile('path/to/file1.php', 'path/to/target/file1.php'));

        # file does exist, but force it
        $service->force = true;
        $disk->shouldReceive('get')->once()->with('path/to/file2.php')->andReturn('foo');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file2.php')->andReturn(true);
        $service->shouldReceive('putFile')->once()->with('path/to/target/file2.php', 'foo')->andReturn(true);
        $this->assertTrue($service->evalFile('path/to/file2.php', 'path/to/target/file2.php'));

        # file exists but history hash is null
        $service->force = false;
        $disk->shouldReceive('get')->once()->with('path/to/file3.php')->andReturn('foo');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file3.php')->andReturn(true);
        $disk->shouldReceive('get')->once()->with('path/to/target/file3.php')->andReturn('foo');
        $service->evalFile('path/to/file3.php', 'path/to/target/file3.php');
        $this->assertTrue(in_array('path/to/target/file3.php', $service->ignored));

        # file exists but history hash has changed (so don't overwrite it)
        $service->force = false;
        $service->history = ['path/to/target/file4.php' => ['hash' => md5('foo')]];
        $service->ignored = [];
        $disk->shouldReceive('get')->once()->with('path/to/file4.php')->andReturn('foo');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file4.php')->andReturn(true);
        $disk->shouldReceive('get')->once()->with('path/to/target/file4.php')->andReturn('bar');
        $service->evalFile('path/to/file4.php', 'path/to/target/file4.php');
        $this->assertTrue(in_array('path/to/target/file4.php', $service->ignored));

        # file exists, history and target hash match, so it's okay to overwrite
        $service->force = false;
        $service->history = ['path/to/target/file5.php' => ['hash' => md5('foo')]];
        $service->ignored = [];
        $service->modified = [];
        $disk->shouldReceive('get')->once()->with('path/to/file5.php')->andReturn('bar');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file5.php')->andReturn(true);
        $disk->shouldReceive('get')->once()->with('path/to/target/file5.php')->andReturn('foo');
        $service->shouldReceive('putFile')->once()->with('path/to/target/file5.php', 'bar')->andReturn(true);
        $service->evalFile('path/to/file5.php', 'path/to/target/file5.php');
        $this->assertTrue(in_array('path/to/target/file5.php', $service->modified));

        # file exists, history and target hash match, so it's okay to overwrite
        $service->force = false;
        $service->history = ['path/to/target/file5.php' => ['hash' => md5('foo')]];
        $service->ignored = [];
        $service->modified = [];
        $disk->shouldReceive('get')->once()->with('path/to/file5.php')->andReturn('bar');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file5.php')->andReturn(true);
        $disk->shouldReceive('get')->once()->with('path/to/target/file5.php')->andReturn('foo');
        $service->shouldReceive('putFile')->once()->with('path/to/target/file5.php', 'bar')->andReturn(true);
        $service->evalFile('path/to/file5.php', 'path/to/target/file5.php');
        $this->assertTrue(in_array('path/to/target/file5.php', $service->modified));

        # file exists, target and source are the same, nothing happens
        $service->force = false;
        $service->history = ['path/to/target/file6.php' => ['hash' => md5('foo')]];
        $service->ignored = [];
        $service->modified = [];
        $disk->shouldReceive('get')->once()->with('path/to/file6.php')->andReturn('foo');
        $disk->shouldReceive('exists')->once()->with('path/to/target/file6.php')->andReturn(true);
        $disk->shouldReceive('get')->once()->with('path/to/target/file6.php')->andReturn('foo');
        $service->evalFile('path/to/file6.php', 'path/to/target/file6.php');
        $this->assertEmpty($service->ignored);
        $this->assertEmpty($service->modified);
    }

    /**
     * @covers \Belt\Core\Services\PublishService::getPreviousHistoryContents
     */
    public function testGetPreviousHistoryContents3()
    {
        $this->expectExceptionMessage('missing history file for publish core');

        $service = m::mock(PublishService::class . '[getHistoryFiles,createHistoryFromTable]');
        $service->key = 'core';
        $service->shouldReceive('getHistoryFiles')->twice()->andReturn([]);
        $service->shouldReceive('createHistoryFromTable')->once();
        $service->getPreviousHistoryContents();
    }

}
