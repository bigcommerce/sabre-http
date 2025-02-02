<?php declare (strict_types=1);

namespace Sabre\HTTP\Auth;

use PHPUnit\Framework\TestCase;
use Sabre\HTTP\Request;
use Sabre\HTTP\Response;

class BasicTest extends TestCase {

    function testGetCredentials() {

        $request = new Request('GET', '/', [
            'Authorization' => 'Basic ' . base64_encode('user:pass:bla')
        ]);

        $basic = new Basic('Dagger', $request, new Response());

        $this->assertEquals([
            'user',
            'pass:bla',
        ], $basic->getCredentials());

    }

    function testGetInvalidCredentialsColonMissing() {

        $request = new Request('GET', '/', [
            'Authorization' => 'Basic ' . base64_encode('userpass')
        ]);

        $basic = new Basic('Dagger', $request, new Response());

        $this->assertNull($basic->getCredentials());

    }

    function testGetCredentialsNoheader() {

        $request = new Request('GET', '/', []);
        $basic = new Basic('Dagger', $request, new Response());

        $this->assertNull($basic->getCredentials());

    }

    function testGetCredentialsNotBasic() {

        $request = new Request('GET', '/', [
            'Authorization' => 'QBasic ' . base64_encode('user:pass:bla')
        ]);
        $basic = new Basic('Dagger', $request, new Response());

        $this->assertNull($basic->getCredentials());

    }

    function testRequireLogin() {

        $response = new Response();
        $request = new Request('GET', '/');

        $basic = new Basic('Dagger', $request, $response);

        $basic->requireLogin();

        $this->assertEquals('Basic realm="Dagger"', $response->getHeader('WWW-Authenticate'));
        $this->assertEquals(401, $response->getStatus());

    }

}
