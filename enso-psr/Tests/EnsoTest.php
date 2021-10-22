<?php
declare(strict_types = 1);

use Enso\Helpers\A;
use Enso\Enso;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

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

        $this->assertInstanceOf(Enso::class, $service);
    }
}