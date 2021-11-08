<?php declare(strict_types = 1);

namespace Tests;

use Codeception\Util\JsonType;
use Enso\Helpers\A;
use Enso\Enso;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\
    {Exception\ProcessFailedException, Process};
use Yiisoft\Json\Json;

/**
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class EnsoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testClassCanBeCreated()
    {
        $service = new Enso();

        static::assertInstanceOf(Enso::class, $service);
    }

    public function testCliDefaultRoute()
    {
        $process = new Process(['./enso', 'default/index']);

        $process->mustRun();
        $output = Json::decode($process->getOutput());

        static::assertTrue((new JsonType($output))->matches([
            'context' => [
                'sapi' => 'string',
                'swoole' => 'boolean',
            ],
            'before' => 'float:>0',
            'after' => 'float:>0',
            'taskDuration' => 'string',
            'preloadDuration' => 'string',
        ]));
    }

    public function testCliDefaultView()
    {
        $process = new Process(['./enso', 'default/view']);

        $process->mustRun();
        $output = Json::decode($process->getOutput());

        static::assertTrue((new JsonType($output))->matches([
            'work' => 'string',
            'before' => 'float:>0',
            'after' => 'float:>0',
            'taskDuration' => 'string',
            'preloadDuration' => 'string',
        ]));
    }

    public function testCliBadRoute()
    {
        $process = new Process(['./enso', 'some/bad/route']);

        $process->run();
        $output = Json::decode($process->getOutput());

        static::assertNotEquals((int) $process->getExitCode(), (int) 0);

        static::assertTrue((new JsonType($output))->matches([
            'class' => 'string',
            'file' => 'string',
            'line' => 'integer:>0',
            'message' => 'string',
        ]));
    }
}
