<?php

namespace Jinya\Plates;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        vfsStream::setup('templates');

        $this->engine = new Engine(vfsStream::url('templates'));
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Engine::class, $this->engine);
    }

    public function testGetTemplatePath(): void
    {
        self::assertEquals('vfs://templates/template.phtml', $this->engine->path('template'));
    }

    public function testTemplateExists(): void
    {
        self::assertFalse($this->engine->exists('template'));

        vfsStream::create(
            [
                'template.phtml' => '',
            ]
        );

        self::assertTrue($this->engine->exists('template'));
    }

    public function testMakeTemplate(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '',
            ]
        );

        $this->engine->make('template');
        self::assertTrue(true);
    }

    public function testRenderTemplate(): void
    {
        vfsStream::create(
            [
                'template.phtml' => 'Hello!',
            ]
        );

        self::assertEquals('Hello!', $this->engine->render('template'));
    }
}
