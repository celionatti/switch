<?php

declare(strict_types=1);

namespace Switch\Http\Tests;

use PHPUnit\Framework\TestCase;
use Switch\Http\Request;
use Switch\Http\Response;
use Switch\Http\ResponseFactory;
use Switch\Http\ServerRequest;
use Switch\Http\ServerRequestFactory;
use Switch\Http\Stream;
use Switch\Http\StreamFactory;
use Switch\Http\Uri;
use Switch\Http\UriFactory;

class HttpMessageTest extends TestCase
{
    public function testUriParsingAndModification(): void
    {
        $uri = new Uri('https://user:pass@example.com:8080/path/to/page?query=val#frag');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path/to/page', $uri->getPath());
        $this->assertEquals('query=val', $uri->getQuery());
        $this->assertEquals('frag', $uri->getFragment());
        $this->assertEquals('https://user:pass@example.com:8080/path/to/page?query=val#frag', (string) $uri);

        $modified = $uri->withHost('domain.com')->withPort(443)->withScheme('https');
        $this->assertEquals('domain.com', $modified->getHost());
        $this->assertNull($modified->getPort()); // 443 is standard port for https
    }

    public function testStreamReadWriteAndSeek(): void
    {
        $stream = Stream::create('Hello Framework');
        $this->assertEquals('Hello Framework', (string) $stream);
        $this->assertEquals(15, $stream->getSize());

        $stream->seek(0);
        $this->assertEquals('Hello', $stream->read(5));

        $stream->write(' World');
        $stream->rewind();
        $this->assertEquals('Hello Worldwork', $stream->getContents());
    }

    public function testRequestHeaderImmutability(): void
    {
        $request = new Request('GET', 'http://example.com/api', null, ['Content-Type' => 'application/json']);

        $this->assertTrue($request->hasHeader('content-type'));
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));

        $newRequest = $request->withHeader('X-Custom', 'HeaderValue');
        $this->assertNotSame($request, $newRequest);
        $this->assertFalse($request->hasHeader('X-Custom'));
        $this->assertTrue($newRequest->hasHeader('X-Custom'));
    }

    public function testResponseCreationAndFactories(): void
    {
        $factory = new ResponseFactory();
        $response = $factory->createResponse(201, 'Created');

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Created', $response->getReasonPhrase());

        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStream('{"status":"success"}');
        $response = $response->withBody($body)->withHeader('Content-Type', 'application/json');

        $this->assertEquals('{"status":"success"}', (string) $response->getBody());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testServerRequestAttributesAndGlobals(): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', 'http://localhost/users', ['REMOTE_ADDR' => '127.0.0.1']);

        $request = $request->withAttribute('user_id', 42)
            ->withParsedBody(['username' => 'alice']);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(42, $request->getAttribute('user_id'));
        $this->assertEquals(['username' => 'alice'], $request->getParsedBody());
        $this->assertEquals(['REMOTE_ADDR' => '127.0.0.1'], $request->getServerParams());
    }
}
