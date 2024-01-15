<?php

namespace Jinya\Plates\Extension;

use Jinya\Plates\Engine;
use PHPUnit\Framework\TestCase;

class URITest extends TestCase
{
    private URI $extension;

    public function setUp(): void
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
        self::assertTrue($engine->functions->exists('uri'));
    }

    public function testGetUrl(): void
    {
        self::assertSame('/green/red/blue', $this->extension->runUri());
    }

    public function testGetSpecifiedSegment(): void
    {
        self::assertSame('green', $this->extension->runUri(1));
        self::assertSame('red', $this->extension->runUri(2));
        self::assertSame('blue', $this->extension->runUri(3));
    }

    public function testSegmentMatch(): void
    {
        self::assertTrue($this->extension->runUri(1, 'green'));
        self::assertFalse($this->extension->runUri(1, 'red'));
    }

    public function testSegmentMatchSuccessResponse(): void
    {
        self::assertSame('success', $this->extension->runUri(1, 'green', 'success'));
    }

    public function testSegmentMatchFailureResponse(): void
    {
        self::assertFalse($this->extension->runUri(1, 'red', 'success'));
    }

    public function testSegmentMatchFailureCustomResponse(): void
    {
        self::assertSame('fail', $this->extension->runUri(1, 'red', 'success', 'fail'));
    }

    public function testRegexMatch(): void
    {
        self::assertTrue($this->extension->runUri('/[a-z]+/red/blue'));
    }

    public function testRegexMatchSuccessResponse(): void
    {
        self::assertSame('success', $this->extension->runUri('/[a-z]+/red/blue', 'success'));
    }

    public function testRegexMatchFailureResponse(): void
    {
        self::assertFalse($this->extension->runUri('/[0-9]+/red/blue', 'success'));
    }

    public function testRegexMatchFailureCustomResponse(): void
    {
        self::assertSame('fail', $this->extension->runUri('/[0-9]+/red/blue', 'success', 'fail'));
    }

    public function testFetchNonExistingUriIndex(): void
    {
        $engine = new Engine();
        $extension = new URI('/');
        $extension->register($engine);
        self::assertNull($extension->runUri(2));
    }

    public function testCompareNonExistingUriIndex(): void
    {
        $engine = new Engine();
        $extension = new URI('/hello');
        $extension->register($engine);
        self::assertFalse($extension->runUri(2, 'hello'));
    }
}
