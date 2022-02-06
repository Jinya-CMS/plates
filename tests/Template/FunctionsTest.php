<?php

namespace League\Plates\Template;

use LogicException;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    private Functions $functions;

    public function setUp(): void
    {
        $this->functions = new Functions();
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Functions::class, $this->functions);
    }

    public function testAddAndGetFunction(): void
    {
        self::assertInstanceOf(Functions::class, $this->functions->add('upper', 'strtoupper'));
        self::assertEquals('strtoupper', $this->functions->get('upper')->getCallback());
    }

    public function testAddFunctionConflict(): void
    {
        $this->expectException(LogicException::class);
        $this->functions->add('upper', 'strtoupper');
        $this->functions->add('upper', 'strtoupper');
    }

    public function testGetNonExistentFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->functions->get('foo');
    }

    public function testRemoveFunction(): void
    {
        $this->functions->add('upper', 'strtoupper');
        self::assertEquals(true, $this->functions->exists('upper'));
        $this->functions->remove('upper');
        self::assertEquals(false, $this->functions->exists('upper'));
    }

    public function testRemoveNonExistentFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->functions->remove('foo');
    }

    public function testFunctionExists(): void
    {
        self::assertEquals(false, $this->functions->exists('upper'));
        $this->functions->add('upper', 'strtoupper');
        self::assertEquals(true, $this->functions->exists('upper'));
    }
}
