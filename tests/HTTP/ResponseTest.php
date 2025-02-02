<?php declare (strict_types=1);

namespace Sabre\HTTP;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {

    function testConstruct() {

        $response = new Response(200, ['Content-Type' => 'text/xml']);
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('OK', $response->getStatusText());

    }

    function testSetStatus() {

        $response = new Response();
        $response->setStatus('402 Where\'s my money?');
        $this->assertEquals(402, $response->getStatus());
        $this->assertEquals('Where\'s my money?', $response->getStatusText());

    }

    function testInvalidStatus() {

        $this->expectException(InvalidArgumentException::class);

        $response = new Response(1000);

    }

    function testToString() {

        $response = new Response(200, ['Content-Type' => 'text/xml']);
        $response->setBody('foo');

        $expected = <<<HI
HTTP/1.1 200 OK\r
Content-Type: text/xml\r
\r
foo
HI;
        $this->assertEquals($expected, (string)$response);

    }

}
