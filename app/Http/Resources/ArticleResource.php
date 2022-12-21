<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'article_title'=>$this->article_title,
            'article_slug'=>$this->article_slug,
            'image_url_name'=>asset($this->image_url_name),
            'article_body'=>$this->article_body,
            'article_status'=>$this->article_status,
            'article_category'=>$this->category,
            'article_author'=>$this->author
        ];
    }
}
