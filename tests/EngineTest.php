<?php

namespace League\Plates;

use League\Plates\Extension\Asset;
use League\Plates\Extension\URI;
use League\Plates\Template\Func;
use LogicException;
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

    public function testSetDirectory(): void
    {
        $this->engine->setDirectory(vfsStream::url('templates'));
        self::assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory(): void
    {
        $this->engine->setDirectory(null);
        self::assertEquals(null, $this->engine->getDirectory());
    }

    public function testSetInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->engine->setDirectory(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory(): void
    {
        self::assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetFileExtension(): void
    {
        $this->engine->setFileExtension('tpl');
        self::assertEquals('tpl', $this->engine->getFileExtension());
    }

    public function testSetNullFileExtension(): void
    {
        $this->engine->setFileExtension(null);
        self::assertEquals(null, $this->engine->getFileExtension());
    }

    public function testGetFileExtension(): void
    {
        self::assertEquals('phtml', $this->engine->getFileExtension());
    }

    public function testAddFolder(): void
    {
        vfsStream::create(
            [
                'folder' => [
                    'template.phtml' => '',
                ],
            ]
        );

        $this->engine->addFolder('folder', vfsStream::url('templates/folder'));
        self::assertEquals('vfs://templates/folder', $this->engine->getFolders()->get('folder')->getPath());
    }

    public function testAddFolderWithNamespaceConflict(): void
    {
        $this->expectException(LogicException::class);
        $this->engine->addFolder('name', vfsStream::url('templates'));
        $this->engine->addFolder('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->engine->addFolder('namespace', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder(): void
    {
        vfsStream::create(
            [
                'folder' => [
                    'template.phtml' => '',
                ],
            ]
        );

        $this->engine->addFolder('folder', vfsStream::url('templates/folder'));
        self::assertEquals(true, $this->engine->getFolders()->exists('folder'));
        $this->engine->removeFolder('folder');
        self::assertEquals(false, $this->engine->getFolders()->exists('folder'));
    }

    public function testAddData(): void
    {
        $this->engine->addData(['name' => 'Jonathan']);
        $data = $this->engine->getData();
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testAddDataWithTemplate(): void
    {
        $this->engine->addData(['name' => 'Jonathan'], 'template');
        $data = $this->engine->getData('template');
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testAddDataWithTemplates(): void
    {
        $this->engine->addData(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->engine->getData('template1');
        self::assertEquals('Jonathan', $data['name']);
    }

    public function testRegisterFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?=$this->uppercase($name)?>',
            ]
        );

        $this->engine->registerFunction('uppercase', 'strtoupper');
        self::assertInstanceOf(Func::class, $this->engine->getFunction('uppercase'));
        self::assertEquals('strtoupper', $this->engine->getFunction('uppercase')->getCallback());
    }

    public function testDropFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        self::assertEquals(true, $this->engine->doesFunctionExist('uppercase'));
        $this->engine->dropFunction('uppercase');
        self::assertEquals(false, $this->engine->doesFunctionExist('uppercase'));
    }

    public function testDropInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->engine->dropFunction('some_function_that_does_not_exist');
    }

    public function testGetFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');

        self::assertEquals('uppercase', $function->getName());
        self::assertEquals('strtoupper', $function->getCallback());
    }

    public function testGetInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        self::assertEquals(true, $this->engine->doesFunctionExist('uppercase'));
    }

    public function testDoesFunctionNotExist(): void
    {
        self::assertEquals(false, $this->engine->doesFunctionExist('some_function_that_does_not_exist'));
    }

    public function testLoadExtension(): void
    {
        self::assertEquals(false, $this->engine->doesFunctionExist('uri'));
        $this->engine->loadExtension(new URI(''));
        self::assertEquals(true, $this->engine->doesFunctionExist('uri'));
    }

    public function testLoadExtensions(): void
    {
        self::assertEquals(false, $this->engine->doesFunctionExist('uri'));
        self::assertEquals(false, $this->engine->doesFunctionExist('asset'));
        $this->engine->loadExtensions(
            [
                new URI(''),
                new Asset('public'),
            ]
        );
        self::assertEquals(true, $this->engine->doesFunctionExist('uri'));
        self::assertEquals(true, $this->engine->doesFunctionExist('asset'));
    }

    public function testGetTemplatePath(): void
    {
        self::assertEquals('vfs://templates/template.phtml', $this->engine->path('template'));
    }

    public function testTemplateExists(): void
    {
        self::assertEquals(false, $this->engine->exists('template'));

        vfsStream::create(
            [
                'template.phtml' => '',
            ]
        );

        self::assertEquals(true, $this->engine->exists('template'));
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
