<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class FilterPageRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "_perPage"=>"integer|min:1|max:100",
            "_sortDir"=>"string|max:200",
            "_sortField"=>"string|max:200",
            "_filters"=>"json",
        ];
    }

    /**
     * Get perPage param from request.
     *
     * @return integer
     */
    public function getPerPage()
    {
        return $this->input("_perPage",10);
    }

    /**
     * Get sortDir param from request.
     *
     * @return String
     */
    public function getSortDir()
    {
        return $this->input("_sortDir","desc");
    }

    /**
     * Get sortField param from request.
     *
     * @return String
     */
    public function getSortField()
    {
        return $this->input("_sortField","id");
    }

    /**
     * Get filters param from request.
     *
     * @return String
     */
    public function getFilters()
    {
        return json_decode($this->input("_filters","{}"),true);
    }
}
