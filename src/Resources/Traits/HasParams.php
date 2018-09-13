<?php

namespace Belt\Core\Resources\Traits;

use Belt\Core\Resources\BaseParam;
use Belt\Core\Resources\BaseParamGroup;
use Illuminate\Support\Collection;

/**
 * Trait HasParam
 * @package Belt\Core\Resources\Traits
 */
trait HasParams
{
    /**
     * @var Collection
     */
    protected $params;

    /**
     * @return array
     */
    public function params()
    {
        return [];
    }

    /**
     * @param Collection $params
     * @return $this
     */
    public function setParams(Collection $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParams()
    {
        return $this->params ?: $this->params = new Collection();
    }

    /**
     * @param BaseParam $param
     * @return $this
     */
    public function pushParam(BaseParam $param)
    {
        $this->getParams()->put($param->getKey(), $param);

        return $this;
    }

    /**
     * @return $this
     */
    public function makeParams()
    {
        $this->setParams(new Collection());

        foreach ($this->params() as $param) {
            $this->__makeParams($param);
        }

        return $this;
    }

    /**
     * @param $param
     */
    public function __makeParams($param)
    {
        if ($param instanceof BaseParamGroup) {
            foreach ($param->params() as $_param) {
                $_param->setGroup($param->getKey());
                $this->__makeParams($_param);
            }
        }

        if ($param instanceof BaseParam) {
            $this->pushParam($param);
        }

    }

}