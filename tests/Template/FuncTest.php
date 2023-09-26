<?php

namespace League\Plates\Template;

use LogicException;
use PHPUnit\Framework\TestCase;

class FuncTest extends TestCase
{
    private Func $function;

    public function setUp(): void
    {
        $this->function = new Func('uppercase', function ($string) {
            return strtoupper($string);
        });
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Func::class, $this->function);
    }

    public function testSetAndGetName(): void
    {
        self::assertInstanceOf(Func::class, $this->function->setName('test'));
        self::assertEquals('test', $this->function->getName());
    }

    public function testSetInvalidName(): void
    {
        $this->expectException(LogicException::class);
        $this->function->setName('invalid-function-name');
    }

    public function testSetAndGetCallback(): void
    {
        self::assertInstanceOf(Func::class, $this->function->setCallback('strtolower'));
        self::assertEquals('strtolower', $this->function->getCallback());
    }

    public function testSetInvalidCallback(): void
    {
        $this->expectException(LogicException::class);
        $this->function->setCallback(null);
    }

    public function testFunctionCall(): void
    {
        self::assertEquals('JONATHAN', $this->function->call(null, ['Jonathan']));
    }
}
