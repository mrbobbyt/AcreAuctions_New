<?php
declare(strict_types = 1);

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Repositories\Post\Exceptions\PostNotFoundException;
use Illuminate\Database\Eloquent\Model;

class PostRepository implements PostRepositoryContract
{
    /**
     * Find post by url
     * @param string $slug
     * @return Model
     * @throws PostNotFoundException
     */
    public function findBySlug(string $slug): Model
    {
        $listing = Post::query()->where('slug', $slug)->first();

        if ($listing === null) {
            throw new PostNotFoundException();
        }

        return $listing;
    }


    /**
     * Find post by id
     * @param int $id
     * @return Model
     * @throws PostNotFoundException
     */
    public function findByPk(int $id): Model
    {
        $listing = Post::find($id);

        if ($listing === null) {
            throw new PostNotFoundException();
        }

        return $listing;
    }


    /**
     * Check existing Post by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title): bool
    {
        return Post::query()->where('title', $title)->exists();
    }
}
