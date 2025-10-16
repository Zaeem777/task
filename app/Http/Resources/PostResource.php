<?php

namespace App\Http\Resources;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public static $wrap = null;
      protected $message;

      public function __construct($resource, $message = "Success")  
    {
        parent::__construct($resource);
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
        'id' =>$this->id,
        'name' => $this->name,
        'description' =>$this->description,
        'image_path' => $this->image_path,
        'status' => $this->status,
        ]
        ]
        ];
    }

}

