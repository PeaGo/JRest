<?php

use App\Helper\JResponse;
use App\Helper\Log;
use App\Model\_Example as _Example;
use Slim\Routing\RouteCollectorProxy;



$app->group('/example', function (RouteCollectorProxy $group) {
    $group->get('/list', function ($req, $res) {
        $table_name = (new _Example())->getTable() . '.';
        $filter = (array) $req->getParsedBody();

        // where condition
        $where = [];

        // sort condition
        $sort = [
            'attr' => 'id',
            'type' => 'asc',
        ];

        // whereIn condition
        $whereIn = [];

        // between condition [ from - to ]
        $compare = [];

        // search string
        $search = '';

        // paging
        $isPaging = true;
        $paging = [
            'count' => 0,
            'totalpage' => 0,
            'perpage' => 15,
            'page' => 1,
        ];

        if (!empty($filter['paging'])) {
            $paging['perpage'] = empty($filter['paging']['perpage']) ? $paging['perpage']
                : $filter['paging']['perpage'];
            $paging['page'] = $filter['paging']['page'];
        }
        $limit = $paging['perpage'];
        $offset = ($paging['page'] - 1) * $paging['perpage'];
        foreach ($filter as $attr => &$payload) {
            if ($attr == 'paging') {
                if ($payload == false) {
                    $isPaging = false;
                }
                continue;
            }
            if ($attr == 'search') {
                $search = $payload;
                continue;
            }
            if ($attr == 'sort') {
                if (!empty($payload['type'])) {
                    $sort['type'] = $payload['type'];
                }
                if (!empty($payload['attr'])) {
                    $sort['attr'] = $table_name . $payload['attr'];
                }
            }
            if (isset($payload['type']) && $payload['type'] == 'compare') {
                array_push($compare, array('attr' => $table_name . $attr, 'value' => $payload['value']));
                continue;
            }

            // check params is empty
            if (!empty($payload['value'])) {
                // check whereIn ?
                if (is_array($payload['value'])) {
                    array_push($whereIn, (array) [$table_name . $attr, $payload['value']]);
                    continue;
                } else {
                    //like condition
                    if (isset($payload['type']) && $payload['type'] == 'like') {
                        $payload['value'] = '%' . $payload['value'] . '%';
                        array_push($where, (array) [
                            $table_name . $attr,
                            'like',
                            $payload['value'],
                        ]);
                        continue;
                    }

                    // default
                    array_push($where, (array) [
                        $table_name . $attr,
                        // $payload['type'],
                        "=",
                        $payload['value'],
                    ]);
                }
            }
        }
        try {
            $query = _Example::where($where)
                ->orderBy($sort['attr'], $sort['type']);

            if (!empty($search)) {
                $query->where(
                    function ($q) use ($search, $table_name) {
                        $q->where($table_name . 'title', 'like', '%' . $search . '%');
                    }
                );
            }

            if (!empty($whereIn)) {
                foreach ($whereIn as $e) {
                    $query->WhereIn($e[0], $e[1]);
                }
            }

            if (!empty($compare)) {
                foreach ($compare as $e) {
                    if (!empty($e['value']['from'])) {
                        $query->where($e['attr'], '>=', $e['value']['from']);
                    }
                    if (!empty($e['value']['to'])) {
                        $query->where($e['attr'], '<=', $e['value']['to']);
                    }
                }
            }
            $count = $query->count();
            $isPaging && $query->offset($offset)->limit($limit);
            $list = $query->get();
            $totalPage = ceil($count / $paging['perpage']);

            $paging['count'] = $count;
            $paging['totalpage'] = $totalPage;

            $data = [
                'list' => $list,
                'paging' => $isPaging ? $paging : false,
            ];
            return setResponse(STATUS_SUCCESS, 200, 'Get list success', $data, $res);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [__FILE__, __LINE__]);
            return JResponse::err500($res, $th, $th->getMessage());
        }
    });

    // load by id
    $group->get('/detail/{id}', function ($req, $res, array $args) {
        $table_name = (new _Example())->getTable() . '.';
        if (!empty($args['id'])) {
            try {
                $record = _Example::where($table_name . 'id', $args['id'])
                    ->first();
                if ($record != null) {
                    return JResponse::success($res, $record, 'Get detail success');
                } else {
                    return JResponse::error($res, 404, [], 'Data not found');
                }
            } catch (\Throwable $th) {
                Log::error($th->getMessage(), [__FILE__, __LINE__]);
                return JResponse::err500($res, $th, $th->getMessage());
            }
        }
    });

    // save
    $group->post('/save', function ($req, $res) {
        $params = $req->getParsedBody();
        $require = [];
        $isValid = isValidParams($params, $require);
        try {
            if (empty($params['id'])) { //insert
                if (!$isValid['status']) {
                    return JResponse::error($res, 400, [], 'Missing params ' . $isValid['msg']);
                }
                $new = new _Example($params);
                $new->save();
                return JResponse::success($res, $new, 'Add record success');
            } else { //update
                $update = _Example::find($params['id']);
                $update->update($params);
                return JResponse::success($res, $update, 'Update record success');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [__FILE__, __LINE__]);
            return JResponse::err500($res, $th, $th->getMessage());
        }
    });

    //delete
    $group->post('/delete', function ($req, $res) {
        $params = $req->getParsedBody();
        try {
            if (!empty($params['id'])) {
                if (!is_array($params['id'])) {
                    $delete = _Example::find($params['id']);
                    $delete->delete();
                    return JResponse::success($res, $delete, 'Delete record success');
                } else {
                    $ids = _Example::whereIn('id', $params['id'])->delete();
                    return JResponse::success($res, $ids, 'Delete records success');
                }
            } else {
                Log::error('Id not found', [__FILE__, __LINE__]);
                return JResponse::error($res, 404, [], 'Data not found');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [__FILE__, __LINE__]);
            return JResponse::err500($res, $th, $th->getMessage());
        }
    });
});
