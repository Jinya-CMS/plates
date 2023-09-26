<?php

namespace League\Plates\Extension;

use League\Plates\Engine;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase
{
    public function setUp(): void
    {
        vfsStream::setup('assets');
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Asset::class, new Asset(vfsStream::url('assets')));
        self::assertInstanceOf(Asset::class, new Asset(vfsStream::url('assets'), true));
        self::assertInstanceOf(Asset::class, new Asset(vfsStream::url('assets'), false));
    }

    public function testRegister(): void
    {
        $engine = new Engine();
        $extension = new Asset(vfsStream::url('assets'));
        $extension->register($engine);
        self::assertEquals(true, $engine->doesFunctionExist('asset'));
    }

    public function testCachedAssetUrl(): void
    {
        vfsStream::create(
            [
                'styles.css' => '',
            ]
        );

        $extension = new Asset(vfsStream::url('assets'));
        self::assertSame(
            $extension->cachedAssetUrl('styles.css'),
            'styles.css?v='.filemtime(
                vfsStream::url('assets/styles.css')
            )
        );
        self::assertSame(
            $extension->cachedAssetUrl('/styles.css'),
            '/styles.css?v='.filemtime(
                vfsStream::url('assets/styles.css')
            )
        );
    }

    public function testCachedAssetUrlInFolder(): void
    {
        vfsStream::create(
            [
                'folder' => [
                    'styles.css' => '',
                ],
            ]
        );

        $extension = new Asset(vfsStream::url('assets'));
        self::assertSame(
            $extension->cachedAssetUrl('/folder/styles.css'),
            '/folder/styles.css?v='.filemtime(
                vfsStream::url('assets/folder/styles.css')
            )
        );
    }

    public function testCachedAssetUrlUsingFilenameMethod(): void
    {
        vfsStream::create(
            [
                'styles.css' => '',
            ]
        );

        $extension = new Asset(vfsStream::url('assets'), true);
        self::assertSame(
            $extension->cachedAssetUrl('styles.css'),
            'styles.'.filemtime(
                vfsStream::url('assets/styles.css')
            ).'.css'
        );
    }

    public function testFileNotFoundException(): void
    {
        $this->expectException(LogicException::class);

        $extension = new Asset(vfsStream::url('assets'));
        $extension->cachedAssetUrl('styles.css');
    }
}
