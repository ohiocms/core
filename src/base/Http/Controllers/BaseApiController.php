<?php

namespace Ohio\Core\Base\Http\Controllers;

use Ohio\Core\Base\Http\Exceptions;
use Ohio\Core\Base\Http\Requests\BasePaginateRequest;
use Ohio\Core\Base\Pagination\BaseLengthAwarePaginator;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseApiController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function abort($statusCode, $message = '')
    {
        if ($statusCode == 404) {
            throw new Exceptions\ApiNotFoundHttpException($message);
        }

        $exception = new Exceptions\ApiException();
        $exception->setStatusCode($statusCode);
        $exception->setMsg($message);

        throw $exception;
    }

    public function getPaginateRequest($class = BasePaginateRequest::class, $query = [])
    {

        if ($class == BasePaginateRequest::class || is_subclass_of($class, BasePaginateRequest::class)) {
            return new $class($query);
        }

        throw new \Exception('Invalid class for BaseApiController::getPaginateRequest');
    }

    public function paginator(Builder $qb, BasePaginateRequest $request)
    {
        return new BaseLengthAwarePaginator($qb, $request);
    }

    public function set($item, $input, $key)
    {
        if (is_array($key)) {
            foreach ($key as $_key) {
                $this->set($item, $input, $_key);
            }
        } elseif (isset($input[$key])) {
            $item->$key = $input[$key];
        }
    }

}