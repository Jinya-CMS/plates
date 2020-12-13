<?php

namespace League\Plates\Extension;

use League\Plates\Engine;
use LogicException;
use PHPUnit\Framework\TestCase;

class URITest extends TestCase
{
    private URI $extension;

    public function setUp():void
    {
        $this->extension = new URI('/green/red/blue');
    }

    public function testCanCreateInstance(): void
    {
        self::assertInstanceOf(URI::class, $this->extension);
    }

    public function testRegister(): void
    {
        $engine = new Engine();
        $extension = new URI('/green/red/blue');
        $extension->register($engine);
        self::assertEquals(true, $engine->doesFunctionExist('uri'));
    }

    public function testGetUrl(): void
    {
        self::assertSame($this->extension->runUri(), '/green/red/blue');
    }

    public function testGetSpecifiedSegment(): void
    {
        self::assertSame($this->extension->runUri(1), 'green');
        self::assertSame($this->extension->runUri(2), 'red');
        self::assertSame($this->extension->runUri(3), 'blue');
    }

    public function testSegmentMatch(): void
    {
        self::assertTrue($this->extension->runUri(1, 'green'));
        self::assertFalse($this->extension->runUri(1, 'red'));
    }

    public function testSegmentMatchSuccessResponse(): void
    {
        self::assertSame($this->extension->runUri(1, 'green', 'success'), 'success');
    }

    public function testSegmentMatchFailureResponse(): void
    {
        self::assertFalse($this->extension->runUri(1, 'red', 'success'));
    }

    public function testSegmentMatchFailureCustomResponse(): void
    {
        self::assertSame($this->extension->runUri(1, 'red', 'success', 'fail'), 'fail');
    }

    public function testRegexMatch(): void
    {
        self::assertTrue($this->extension->runUri('/[a-z]+/red/blue'));
    }

    public function testRegexMatchSuccessResponse(): void
    {
        self::assertSame($this->extension->runUri('/[a-z]+/red/blue', 'success'), 'success');
    }

    public function testRegexMatchFailureResponse(): void
    {
        self::assertFalse($this->extension->runUri('/[0-9]+/red/blue', 'success'));
    }

    public function testRegexMatchFailureCustomResponse(): void
    {
        self::assertSame($this->extension->runUri('/[0-9]+/red/blue', 'success', 'fail'), 'fail');
    }

    public function testInvalidCall(): void
    {
        $this->expectException(LogicException::class);

        $this->extension->runUri(array());
    }

    public function testFetchNonExistingUriIndex(): void
    {
        $engine = new Engine();
        $extension = new URI('/');
        $extension->register($engine);
        self::assertTrue(is_null($extension->runUri(2)));
    }

    public function testCompareNonExistingUriIndex(): void
    {
        $engine = new Engine();
        $extension = new URI('/hello');
        $extension->register($engine);
        self::assertFalse($extension->runUri(2, 'hello'));
    }
}
