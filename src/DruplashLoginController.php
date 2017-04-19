<?php


namespace Drupal\druplash;


use Mouf\Security\Controllers\LoginController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class DruplashLoginController implements LoginController
{

    /**
     * Displays a login page.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function loginPage(ServerRequestInterface $request): ResponseInterface
    {
        // TODO: find a way to display the Drupal login page.
        return new JsonResponse(['message' => 'danger', 'messageType' => 'not connected'], 401);
    }
}
