<?php
namespace Ohio\Core\Base\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BasePaginateRequest extends Request
{

    /**
     * @var integer
     */
    public $perPage = 20;

    /**
     * @var string
     */
    public $orderBy = 'id';

    /**
     * @var string
     */
    public $sortBy = 'asc';

    /**
     * @var array
     */
    public $sortable = [];

    /**
     * @var array
     */
    public $searchable = [];

    /**
     * @var array
     */
    public $meta = [
        'hello' => 'world'
    ];

    /**
     * @return string
     */
    public function needle()
    {
        $needle = $this->query('q');

        return (string) $needle;
    }

    /**
     * @return int
     */
    public function offset()
    {

        $perPage = $this->perPage();

        return (int) ($perPage * $this->page()) - $perPage;
    }

    /**
     * @return int $perPage
     */
    public function perPage()
    {

        $perPage = $this->query('perPage');

        if ($perPage && is_numeric($perPage) && $perPage > 0) {
            return (int) $perPage;
        }

        return (int) $this->perPage;
    }

    /**
     * @return int
     */
    public function page()
    {

        $page = $this->query('page');

        if ($page && is_numeric($page) && $page > 0) {
            return (int) $page;
        }

        return (int) 1;
    }

    /**
     * @return string
     */
    public function orderBy()
    {
        $orderBy = $this->query('orderBy');

        if ($orderBy && in_array($orderBy, $this->sortable)) {
            return (string) $orderBy;
        }

        return (string) $this->orderBy;
    }

    /**
     * @return string
     */
    public function sortBy()
    {
        $sortBy = strtolower($this->query('sortBy'));

        if ($sortBy && in_array($sortBy, ['asc', 'desc'])) {
            return (string) $sortBy;
        }

        return (string) $this->sortBy;
    }

    public function diff()
    {
        return array_diff($this->query(), ['page']);
    }

    public function meta()
    {
        return ['request' => $this->query()];
    }

    public function modifyQuery($query)
    {
    }

}