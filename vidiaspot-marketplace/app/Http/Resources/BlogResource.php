<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'author' => $this->author,
            'author_id' => $this->author_id,
            'category' => $this->category,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'tags' => $this->tags,
            'featured_image' => $this->featured_image,
            'view_count' => $this->view_count,
            'like_count' => $this->like_count,
            'comment_count' => $this->comment_count,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'reading_time' => $this->reading_stats['reading_time'] ?? null,
            'word_count' => $this->reading_stats['word_count'] ?? null,
            'seo_meta' => $this->seo_meta,
            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : null,
        ];
    }
}
