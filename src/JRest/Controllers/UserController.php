<?php

namespace JRest\Controllers;

use JRest\Helper\JResponse;
use JRest\Models\User;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

class UserController
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * BaseController constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->validator = $container->get('validator');
    }

    /**
     * Return List of Users
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
        $list = User::query();
        $query = $request->getQueryParams();
        
        $paging = isset($query['page']) ? true : false;
        $perpage = isset($query['per_page']) ? $query['per_page'] : 15;
        $offset = 0;
        $current_page = 1;

        foreach ($query  as $q => $v) {
            if ($q == 'search') {
                $list->where(function ($query) use ($v) {
                    $query->orWhere('username', 'like', '%' . $v . '%');
                });
            }
            if ($q == 'per_page') {
                $perpage = $v;
            }
            if ($q == 'page') {
                $offset = ($v - 1) * $perpage;
                $current_page = $v;
            }
        }
        $count = $list->count();
        !$paging ?: $list->offset($offset)->limit($perpage);
        $records = $list->get();
        $r_data = [
            'list' => $records,
            'paging' => [
                'page' => (int) $current_page,
                'count' => $count
            ]
        ];
        // $list =  User::all();

        return JResponse::success($response, $r_data, 'Get list success');
    }

    /**
     * Return A Record of User
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */

    public function detail(Request $request, Response $response, array $args)
    {
        // TODO 
        try {
            $record =  User::findOrFail($args['id']);
            return JResponse::success($response, $record, 'Get success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::error($response, 404, [], 'Resource not found');
        }
    }

    /**
     * Create A Record
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */

    public function create(Request $request, Response $response, array $args)
    {
        // TODO 
        $params = $request->getParsedBody();
        $validation = $this->validateSaveRequest($params);
        if ($validation->failed()) {
            return JResponse::error($response, 400, ['errors' => $validation->getErrors()], 'Invalid Params');
        }
        try {
            $record =  User::create($params);
            return JResponse::success($response, $record, 'Get success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::error($response, 400, $th, $th->getMessage());
        }
    }

    /**
     * Update A Record
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */

    public function update(Request $request, Response $response, array $args)
    {
        // TODO 
        $params = $request->getParsedBody();
        $validation = $this->validateSaveRequest($params);
        if ($validation->failed()) {
            return JResponse::error($response, '400', ['errors' => $validation->getErrors()], 'Invalid Params');
        }
        try {
            $update =  User::find($args['id'])->update($params);
            $r_data = User::find($args['id']);
            return JResponse::success($response, $r_data, 'Update success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::error($response, 500, $th, $th->getMessage());
        }
    }

    /**
     * Delete A Record
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */

    public function delete(Request $request, Response $response, array $args)
    {
        // TODO
        try {
            $delete =  User::find($args['id'])->delete();
            return JResponse::success($response, (int) $args['id'], 'Delete success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::error($response, 500, $th, $th->getMessage());
        }
    }


    /**
     * Delete Many Records
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */

    public function deleteMany(Request $request, Response $response, array $args)
    {
        // TODO
        $ids = $request->getParsedBody('ids');
        try {
            $delete =  User::whereIn('id', $ids)->delete();
            return JResponse::success($response, $ids, 'Delete success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::error($response, 500, $th, $th->getMessage());
        }
    }


    /**
     *
     * @param array
     * 
     * @return \JRest\Validation\Validator
     */
    protected function validateSaveRequest($values)
    {
        return $this->validator->validateArray(
            $values,
            [
                'email'    => V::noWhitespace()->notEmpty()->email()->existsInTable($this->db->table('users'), 'email'),
                'username' => V::noWhitespace()->notEmpty()->existsInTable($this->db->table('users'), 'username'),
                'password' => V::noWhitespace()->notEmpty(),
            ]
        );
    }
}
