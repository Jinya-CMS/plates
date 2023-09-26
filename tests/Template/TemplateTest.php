<?php

namespace League\Plates\Template;

use League\Plates\Engine;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    private Template $template;

    public function setUp(): void
    {
        vfsStream::setup('templates');

        $engine = new Engine(vfsStream::url('templates'));
        $engine->registerFunction('uppercase', 'strtoupper');

        $this->template = new Template($engine, 'template');
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(Template::class, $this->template);
    }

    public function testCanCallFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->uppercase("jonathan") ?>',
            ]
        );

        self::assertEquals('JONATHAN', $this->template->render());
    }

    public function testAssignData(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $name ?>',
            ]
        );

        $this->template->data(['name' => 'Jonathan']);
        self::assertEquals('Jonathan', $this->template->render());
    }

    public function testGetData(): void
    {
        $data = ['name' => 'Jonathan'];

        $this->template->data($data);
        self::assertEquals($this->template->data(), $data);
    }

    public function testExists(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '',
            ]
        );

        self::assertEquals(true, $this->template->exists());
    }

    public function testDoesNotExist(): void
    {
        self::assertEquals(false, $this->template->exists());
    }

    public function testGetPath(): void
    {
        self::assertEquals('vfs://templates/template.phtml', $this->template->path());
    }

    public function testRender(): void
    {
        vfsStream::create(
            [
                'template.phtml' => 'Hello World',
            ]
        );

        self::assertEquals('Hello World', $this->template->render());
    }

    public function testRenderViaToStringMagicMethod(): void
    {
        vfsStream::create(
            [
                'template.phtml' => 'Hello World',
            ]
        );

        $actual = (string) $this->template;

        self::assertEquals('Hello World', $actual);
    }

    public function testRenderWithData(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $name ?>',
            ]
        );

        self::assertEquals('Jonathan', $this->template->render(['name' => 'Jonathan']));
    }

    public function testRenderDoesNotExist(): void
    {
        $this->expectException(LogicException::class);
        var_dump($this->template->render());
    }

    public function testRenderException(): void
    {
        $this->expectExceptionMessage('error');
        vfsStream::create(
            [
                'template.phtml' => '<?php throw new Exception("error"); ?>',
            ]
        );
        var_dump($this->template->render());
    }

    public function testLayout(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("layout") ?>',
                'layout.phtml' => 'Hello World',
            ]
        );

        self::assertEquals('Hello World', $this->template->render());
    }

    public function testSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("layout")?><?php $this->start("test") ?>Hello World<?php $this->stop() ?>',
                'layout.phtml' => '<?php echo $this->section("test") ?>',
            ]
        );

        self::assertEquals('Hello World', $this->template->render());
    }

    public function testReplaceSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("layout")?><?php $this->start("test") ?>Hello World<?php $this->stop() ?>',
                        '<?php $this->layout("layout")?><?php $this->start("test") ?>See this instead!<?php $this->stop() ?>',
                    ]
                ),
                'layout.phtml' => '<?php echo $this->section("test") ?>',
            ]
        );

        self::assertEquals('See this instead!', $this->template->render());
    }

    public function testStartSectionWithInvalidName(): void
    {
        $this->expectException(LogicException::class);

        vfsStream::create(
            [
                'template.phtml' => '<?php $this->start("content") ?>',
            ]
        );

        $this->template->render();
    }

    public function testNestSectionWithinAnotherSection(): void
    {
        $this->expectException(LogicException::class);

        vfsStream::create(
            [
                'template.phtml' => '<?php $this->start("section1") ?><?php $this->start("section2") ?>',
            ]
        );

        $this->template->render();
    }

    public function testStopSectionBeforeStarting(): void
    {
        $this->expectException(LogicException::class);

        vfsStream::create(
            [
                'template.phtml' => '<?php $this->stop() ?>',
            ]
        );

        $this->template->render();
    }

    public function testSectionDefaultValue(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->section("test", "Default value") ?>',
            ]
        );

        self::assertEquals('Default value', $this->template->render());
    }

    public function testNullSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("layout") ?>',
                'layout.phtml' => '<?php if (is_null($this->section("test"))) echo "NULL" ?>',
            ]
        );

        self::assertEquals('NULL', $this->template->render());
    }

    public function testPushSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("layout")?>',
                        '<?php $this->push("scripts") ?><script src="example1.js"></script><?php $this->end() ?>',
                        '<?php $this->push("scripts") ?><script src="example2.js"></script><?php $this->end() ?>',
                    ]
                ),
                'layout.phtml' => '<?php echo $this->section("scripts") ?>',
            ]
        );

        self::assertEquals(
            '<script src="example1.js"></script><script src="example2.js"></script>',
            $this->template->render()
        );
    }

    public function testPushWithMultipleSections(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("layout")?>',
                        '<?php $this->push("scripts") ?><script src="example1.js"></script><?php $this->end() ?>',
                        '<?php $this->start("test") ?>test<?php $this->stop() ?>',
                        '<?php $this->push("scripts") ?><script src="example2.js"></script><?php $this->end() ?>',
                    ]
                ),
                'layout.phtml' => implode(
                    '\n',
                    [
                        '<?php echo $this->section("test") ?>',
                        '<?php echo $this->section("scripts") ?>',
                    ]
                ),
            ]
        );

        self::assertEquals(
            'test\n<script src="example1.js"></script><script src="example2.js"></script>',
            $this->template->render()
        );
    }

    public function testFetchFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->fetch("fetched") ?>',
                'fetched.phtml' => 'Hello World',
            ]
        );

        self::assertEquals('Hello World', $this->template->render());
    }

    public function testInsertFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->insert("inserted") ?>',
                'inserted.phtml' => 'Hello World',
            ]
        );

        self::assertEquals('Hello World', $this->template->render());
    }

    public function testBatchFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->batch("Jonathan", "uppercase|strtolower") ?>',
            ]
        );

        self::assertEquals('jonathan', $this->template->render());
    }

    public function testBatchFunctionWithInvalidFunction(): void
    {
        $this->expectException(LogicException::class);

        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->batch("Jonathan", "function_that_does_not_exist") ?>',
            ]
        );

        $this->template->render();
    }

    public function testEscapeFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->escape("<strong>Jonathan</strong>") ?>',
            ]
        );

        self::assertEquals('&lt;strong&gt;Jonathan&lt;/strong&gt;', $this->template->render());
    }

    public function testEscapeFunctionBatch(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->escape("<strong>Jonathan</strong>", "strtoupper|strrev") ?>',
            ]
        );

        self::assertEquals('&gt;GNORTS/&lt;NAHTANOJ&gt;GNORTS&lt;', $this->template->render());
    }

    public function testEscapeShortcutFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->e("<strong>Jonathan</strong>") ?>',
            ]
        );

        self::assertEquals('&lt;strong&gt;Jonathan&lt;/strong&gt;', $this->template->render());
    }
}
