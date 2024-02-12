<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public $status;
    public $message;
    public $resource;

    /**
     * _construct

   * @param mixed $status;
    *@param mixed $message;
    *@param mixed $resource;
    */

    public function __construct($status, $message, $resource)
    {
        parent::__construct( $resource);
        $this->status = $status;
        $this->message = $message;
    }

     /**
     * transform

   * @param Illuminate\Http\Request $Request 
    *@return array
    */

    public function toArray($request)
    {
        return[
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,

        ];
    }

}
