<?php

namespace League\Plates\Template;

use PHPUnit\Framework\TestCase;

class FileExtensionTest extends TestCase
{
    private FileExtension $fileExtension;

    public function setUp(): void
    {
        $this->fileExtension = new FileExtension();
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(FileExtension::class, $this->fileExtension);
    }

    public function testSetFileExtension(): void
    {
        self::assertInstanceOf(FileExtension::class, $this->fileExtension->set('tpl'));
        self::assertEquals('tpl', $this->fileExtension->get());
    }

    public function testSetNullFileExtension(): void
    {
        self::assertInstanceOf(FileExtension::class, $this->fileExtension->set(null));
        self::assertEquals(null, $this->fileExtension->get());
    }

    public function testGetFileExtension(): void
    {
        self::assertEquals('php', $this->fileExtension->get());
    }
}
