<?php

namespace League\Plates\Template;

use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private Directory $directory;

    public function setUp(): void
    {
        vfsStream::setup('templates');

        $this->directory = new Directory();
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Directory::class, $this->directory);
    }

    public function testSetDirectory(): void
    {
        self::assertInstanceOf(Directory::class, $this->directory->set(vfsStream::url('templates')));
        self::assertEquals($this->directory->get(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory(): void
    {
        self::assertInstanceOf(Directory::class, $this->directory->set(null));
        self::assertEquals(null, $this->directory->get());
    }

    public function testSetInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->directory->set(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory(): void
    {
        self::assertEquals(null, $this->directory->get());
    }
}
