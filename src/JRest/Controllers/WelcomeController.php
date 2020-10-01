<?php

namespace JRest\Controllers;

// use App\Helper\JOneSignal;
// use JRest\Helpers\JOneSignal;

use JRest\Helpers\JResponse;
use JRest\Models\Article;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class WelcomeController
{
    /** @var \Conduit\Validation\Validator */
    protected $validator;
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var \Conduit\Services\Auth\Auth */
    protected $auth;
    /** @var \League\Fractal\Manager */
    protected $fractal;
    /** @var \JRest\Services\Notification */
    protected $notification;

    public function __construct(ContainerInterface $container)
    {
        // $this->auth = $container->get('auth');
        // $this->fractal = $container->get('fractal');
        // $this->validator = $container->get('validator');
        // $this->db = $container->get('db');
        // $this->db = $container->get('settings');
        $this->notification = $container->get('notification');
    }

    /**
     * Return List of Articles
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        // TODO 

        // $list =  Article::all();
        // return $response->withJson(['articles' => $list, 'articlesCount' => 20]);
        // $jOnes = JOneSignal();
        // var_dump($this->notification);
        // $this->notification->trigger(['email1'], [32], null, null, false, [
        //     'email' => [
        //         'body' => 'ok123',
        //         'title' => 'ok',
        //         'subject' => 'oknhe'
        //     ]
        // ] );
        try {
            $this->notification->trigger([
                "channels" => ['push', 'email'],
                "receipents" => [32],
                "customs" => [
                    "push" => [
                        "body" => "body push 1",
                        "title" => "title push 1",
                        "subject" => "subject"
                    ],
                    "email" => [
                        "body" => "email body",
                        "title" => "email title",
                        "subject" => "email subject"
                    ]
                ]
            ]);
        } catch (\Throwable $th) {
            return JResponse::err500($response, $th, $th->getMessage());
        }
        return $response->getBody()->write('Welcome to JRest API');
    }
}
