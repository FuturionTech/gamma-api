<?php

namespace App\GraphQL\Mutations;

use App\Models\BlogPost;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateBlogPost
{
    use HasImageUploads;

    /**
     * Update a blog post with optional featured image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $post = BlogPost::findOrFail($id);
        $oldImagePath = $post->featured_image_url;

        // Update blog post fields
        if (isset($input['title'])) {
            $post->title = $input['title'];
        }

        if (isset($input['slug'])) {
            $post->slug = $input['slug'];
        }

        if (isset($input['excerpt'])) {
            $post->excerpt = $input['excerpt'];
        }

        if (isset($input['content'])) {
            $post->content = $input['content'];
        }

        if (isset($input['featured_image_url'])) {
            $post->featured_image_url = $input['featured_image_url'];
        }

        if (isset($input['author_id'])) {
            $post->author_id = $input['author_id'];
        }

        if (isset($input['category'])) {
            $post->category = $input['category'];
        }

        if (isset($input['tags'])) {
            $post->tags = $input['tags'];
        }

        if (isset($input['status'])) {
            $post->status = $input['status'];
        }

        if (isset($input['published_at'])) {
            $post->published_at = $input['published_at'];
        }

        $post->save();

        // Process featured image upload if provided
        if (isset($input['featured_image']) && $input['featured_image']) {
            $uploadPath = UploadPaths::BLOG_POST($post->id);
            $imagePath = $this->processImageUpload(
                $input['featured_image'],
                $uploadPath,
                $oldImagePath,
                'blog_featured_image'
            );

            if ($imagePath) {
                $post->featured_image_url = $imagePath;
                $post->save();
            }
        }

        return $post->fresh();
    }
}
