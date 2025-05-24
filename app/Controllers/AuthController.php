<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Service\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;




class AuthController extends BaseController
{
    public function __construct(
        Twig $view,
        private AuthService $authService,
        private LoggerInterface $logger,
    ) {
        parent::__construct($view);
    }

    public function showRegister(Request $request, Response $response): Response
    {
        // TODO: you also have a logger service that you can inject and use anywhere; file is var/app.log
        $this->logger->info('Register page requested');

        return $this->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        // TODO: call corresponding service to perform user registration
        

        $data = $request->getParsedBody();
        $username=$data['username'] ?? '';
        $password=$data['password'] ?? '';
        

        if(strlen($username) <4 || strlen($password) <8 || !preg_match('/[0-9]/' , $password)){
            $_SESSION['error'] = "Username must have min 4 characters and password min 8 charachetrs and 1 number";
            return $response->withHeader('Location', '/register')->withStatus(302);
        
        }
        try{
            $this->authService->register($username, $password);
        }catch(\Exception $e){
            $_SESSION['error']= "Registration failed" . $e->getMessage();
            return $response->withHeader('Location', '/register')->withStatus(302);

        }
        
        

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function showLogin(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/login.twig');
    }

    public function login(Request $request, Response $response): Response
    {
        // TODO: call corresponding service to perform user login, handle login failures
        
    
    $data = $request->getParsedBody();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
     
    if($this->authService->attempt($username, $password)){
        return $response->withHeader('Location', '/')->withStatus(302);
    }else{
        $_SESSION['error'] = "Username or password incorrect";
        
    
        return $response->withHeader('Location', '/login')->withStatus(302);
     } 
}

    public function logout(Request $request, Response $response): Response
    {
        // TODO: handle logout by clearing session data and destroying session
       
        $_SESSION = [];

        session_destroy();
       
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
