<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;
    protected $token;
    protected $message;
    public function __construct($resource, $token = null, $message = "Success")

    {
        parent::__construct($resource);
        $this->token = $token;
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "response" =>[   
            "message" => $this->message,
            "status"  => 200,
            "data" =>[ 
                'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status'=> $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            ],
"token" => $this->when($this->token !== null, $this->token),        ],
    ];
}
    /**
     * Extra data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    // public function with(Request $request): array
    // {
    //     return [
    //         'success' => true,
    //     ];
    // }
}
