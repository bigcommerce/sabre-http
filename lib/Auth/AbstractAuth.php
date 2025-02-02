<?php declare (strict_types=1);

namespace Sabre\HTTP\Auth;

use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

/**
 * HTTP Authentication base class.
 *
 * This class provides some common functionality for the various base classes.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
abstract class AbstractAuth {

    /**
     * Authentication realm
     *
     * @var string
     */
    protected $realm;

    /**
     * Request object
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Response object
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Creates the object
     */
    function __construct(?string $realm, RequestInterface $request, ResponseInterface $response) {

        $this->realm = $realm;
        $this->request = $request;
        $this->response = $response;

    }

    /**
     * This method sends the needed HTTP header and statuscode (401) to force
     * the user to login.
     *
     * @return void
     */
    abstract function requireLogin();

    /**
     * Returns the HTTP realm
     */
    function getRealm() : string {

        return $this->realm;

    }

}
