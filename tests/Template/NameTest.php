<?php

namespace League\Plates\Template;

use League\Plates\Engine;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        vfsStream::setup('templates');
        vfsStream::create(
            [
                'template.phtml' => '',
                'fallback.phtml' => '',
                'folder' => [
                    'template.phtml' => '',
                ],
            ]
        );

        $this->engine = new Engine(vfsStream::url('templates'));
        $this->engine->addFolder('folder', vfsStream::url('templates/folder'), true);
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Name::class, new Name($this->engine, 'template'));
    }

    public function testGetEngine(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertInstanceOf(Engine::class, $name->getEngine());
    }

    public function testGetName(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertEquals('template', $name->getName());
    }

    public function testGetFolder(): void
    {
        $name = new Name($this->engine, 'folder::template');
        $folder = $name->getFolder();

        self::assertInstanceOf(Folder::class, $folder);
        self::assertEquals('folder', $name->getFolder()->getName());
    }

    public function testGetFile(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertEquals('template.phtml', $name->getFile());
    }

    public function testGetPath(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertEquals($name->getPath(), vfsStream::url('templates/template.phtml'));
    }

    public function testGetPathWithFolder(): void
    {
        $name = new Name($this->engine, 'folder::template');

        self::assertEquals($name->getPath(), vfsStream::url('templates/folder/template.phtml'));
    }

    public function testGetPathWithFolderFallback(): void
    {
        $name = new Name($this->engine, 'folder::fallback');

        self::assertEquals($name->getPath(), vfsStream::url('templates/fallback.phtml'));
    }

    public function testTemplateExists(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertEquals(true, $name->doesPathExist());
    }

    public function testTemplateDoesNotExist(): void
    {
        $name = new Name($this->engine, 'missing');

        self::assertEquals(false, $name->doesPathExist());
    }

    public function testParse(): void
    {
        $name = new Name($this->engine, 'template');

        self::assertEquals('template', $name->getName());
        self::assertEquals(null, $name->getFolder());
        self::assertEquals('template.phtml', $name->getFile());
    }

    public function testParseWithNoDefaultDirectory(): void
    {
        $this->expectException(LogicException::class);

        $this->engine->setDirectory(null);
        $name = new Name($this->engine, 'template');
        $name->getPath();
    }

    public function testParseWithEmptyTemplateName(): void
    {
        $this->expectException(LogicException::class);

        $name = new Name($this->engine, '');
    }

    public function testParseWithFolder(): void
    {
        $name = new Name($this->engine, 'folder::template');

        self::assertEquals('folder::template', $name->getName());
        self::assertEquals('folder', $name->getFolder()->getName());
        self::assertEquals('template.phtml', $name->getFile());
    }

    public function testParseWithFolderAndEmptyTemplateName(): void
    {
        $this->expectException(LogicException::class);

        $name = new Name($this->engine, 'folder::');
    }

    public function testParseWithInvalidName(): void
    {
        $this->expectException(LogicException::class);

        $name = new Name($this->engine, 'folder::template::wrong');
    }

    public function testParseWithNoFileExtension(): void
    {
        $this->engine->setFileExtension(null);

        $name = new Name($this->engine, 'template.phtml');

        self::assertEquals('template.phtml', $name->getName());
        self::assertEquals(null, $name->getFolder());
        self::assertEquals('template.phtml', $name->getFile());
    }
}
