<?php declare (strict_types=1);

namespace Sabre\HTTP;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    function testConstruct() {

        $request = new Request('GET', '/foo', [
            'User-Agent' => 'Evert',
        ]);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/foo', $request->getUrl());
        $this->assertEquals([
            'User-Agent' => ['Evert'],
        ], $request->getHeaders());

    }

    function testGetQueryParameters() {

        $request = new Request('GET', '/foo?a=b&c&d=e');
        $this->assertEquals([
            'a' => 'b',
            'c' => null,
            'd' => 'e',
        ], $request->getQueryParameters());

    }

    function testGetQueryParametersNoData() {

        $request = new Request('GET', '/foo');
        $this->assertEquals([], $request->getQueryParameters());

    }

    /**
     * @backupGlobals
     */
    function testCreateFromPHPRequest() {

        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $request = Sapi::getRequest();
        $this->assertEquals('PUT', $request->getMethod());

    }

    function testGetAbsoluteUrl() {

        $r = new Request('GET', '/foo', [
            'Host' => 'sabredav.org',
        ]);

        $this->assertEquals('http://sabredav.org/foo', $r->getAbsoluteUrl());

        $s = [
            'HTTP_HOST'      => 'sabredav.org',
            'REQUEST_URI'    => '/foo',
            'REQUEST_METHOD' => 'GET',
            'HTTPS'          => 'on',
        ];

        $r = Sapi::createFromServerArray($s);

        $this->assertEquals('https://sabredav.org/foo', $r->getAbsoluteUrl());

    }

    function testGetPostData() {

        $post = [
            'bla' => 'foo',
        ];
        $r = new Request('POST', '/');
        $r->setPostData($post);
        $this->assertEquals($post, $r->getPostData());

    }

    function testGetPath() {

        $request = new Request('GET', '/foo/bar/');
        $request->setBaseUrl('/foo');
        $request->setUrl('/foo/bar/');

        $this->assertEquals('bar', $request->getPath());

    }

    function testGetPathStrippedQuery() {

        $request = new Request('GET', '/foo/bar?a=B');
        $request->setBaseUrl('/foo');

        $this->assertEquals('bar', $request->getPath());

    }

    function testGetPathMissingSlash() {

        $request = new Request('GET', '/foo');
        $request->setBaseUrl('/foo/');

        $this->assertEquals('', $request->getPath());

    }

    function testGetPathOutsideBaseUrl() {
        $this->expectException(\LogicException::class);

        $request = new Request('GET', '/bar/');
        $request->setBaseUrl('/foo/');

        $request->getPath();

    }

    function testToString() {

        $request = new Request('PUT', '/foo/bar', ['Content-Type' => 'text/xml']);
        $request->setBody('foo');

        $expected = <<<HI
PUT /foo/bar HTTP/1.1\r
Content-Type: text/xml\r
\r
foo
HI;
        $this->assertEquals($expected, (string)$request);

    }

    function testToStringAuthorization() {

        $request = new Request('PUT', '/foo/bar', ['Content-Type' => 'text/xml', 'Authorization' => 'Basic foobar']);
        $request->setBody('foo');

        $expected = <<<HI
PUT /foo/bar HTTP/1.1\r
Content-Type: text/xml\r
Authorization: Basic REDACTED\r
\r
foo
HI;
        $this->assertEquals($expected, (string)$request);

    }

}
