<?php

namespace League\Plates\Template;

use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    private Folder $folder;

    public function setUp(): void
    {
        vfsStream::setup('templates');

        $this->folder = new Folder('folder', vfsStream::url('templates'));
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Folder::class, $this->folder);
    }

    public function testSetAndGetName(): void
    {
        $this->folder->setName('name');
        self::assertEquals('name', $this->folder->getName());
    }

    public function testSetAndGetPath(): void
    {
        vfsStream::create(
            array(
                'folder' => [],
            )
        );

        $this->folder->setPath(vfsStream::url('templates/folder'));
        self::assertEquals($this->folder->getPath(), vfsStream::url('templates/folder'));
    }

    public function testSetInvalidPath(): void
    {
        $this->expectException(LogicException::class);
        $this->folder->setPath(vfsStream::url('does/not/exist'));
    }

    public function testSetAndGetFallback(): void
    {
        self::assertEquals(false, $this->folder->getFallback());
        $this->folder->setFallback(true);
        self::assertEquals(true, $this->folder->getFallback());
    }
}
