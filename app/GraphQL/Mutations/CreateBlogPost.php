<?php

namespace App\GraphQL\Mutations;

use App\Models\BlogPost;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;
use Illuminate\Support\Str;

class CreateBlogPost
{
    use HasImageUploads;

    /**
     * Create a new blog post with optional featured image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create blog post first to get ID
        $post = BlogPost::create([
            'title' => $input['title'],
            'slug' => $input['slug'] ?? Str::slug($input['title']),
            'excerpt' => $input['excerpt'] ?? null,
            'content' => $input['content'],
            'featured_image_url' => $input['featured_image_url'] ?? null,
            'author_id' => $input['author_id'] ?? null,
            'category' => $input['category'] ?? null,
            'tags' => $input['tags'] ?? null,
            'status' => $input['status'] ?? 'draft',
            'published_at' => $input['published_at'] ?? null,
            'view_count' => 0,
        ]);

        // Process featured image upload if provided
        if (isset($input['featured_image']) && $input['featured_image']) {
            $uploadPath = UploadPaths::BLOG_POST($post->id);
            $imagePath = $this->processImageUpload(
                $input['featured_image'],
                $uploadPath,
                null,
                'blog_featured_image'
            );

            if ($imagePath) {
                $post->featured_image_url = $imagePath;
                $post->save();
            }
        }

        return $post;
    }
}
