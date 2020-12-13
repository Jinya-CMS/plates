<?php

namespace League\Plates\Template;

use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FoldersTest extends TestCase
{
    private Folders $folders;

    public function setUp(): void
    {
        vfsStream::setup('templates');

        $this->folders = new Folders();
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Folders::class, $this->folders);
    }

    public function testAddFolder(): void
    {
        self::assertInstanceOf(Folders::class, $this->folders->add('name', vfsStream::url('templates')));
        self::assertEquals('vfs://templates', $this->folders->get('name')->getPath());
    }

    public function testAddFolderWithNamespaceConflict(): void
    {
        $this->expectException(LogicException::class);
        $this->folders->add('name', vfsStream::url('templates'));
        $this->folders->add('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->folders->add('name', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder(): void
    {
        $this->folders->add('folder', vfsStream::url('templates'));
        self::assertEquals(true, $this->folders->exists('folder'));
        self::assertInstanceOf(Folders::class, $this->folders->remove('folder'));
        self::assertEquals(false, $this->folders->exists('folder'));
    }

    public function testRemoveFolderWithInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->folders->remove('name');
    }

    public function testGetFolder(): void
    {
        $this->folders->add('name', vfsStream::url('templates'));
        self::assertInstanceOf(Folder::class, $this->folders->get('name'));
        self::assertEquals($this->folders->get('name')->getPath(), vfsStream::url('templates'));
    }

    public function testGetNonExistentFolder(): void
    {
        $this->expectException(LogicException::class);
        self::assertInstanceOf(Folder::class, $this->folders->get('name'));
    }

    public function testFolderExists(): void
    {
        self::assertEquals(false, $this->folders->exists('name'));
        $this->folders->add('name', vfsStream::url('templates'));
        self::assertEquals(true, $this->folders->exists('name'));
    }
}
