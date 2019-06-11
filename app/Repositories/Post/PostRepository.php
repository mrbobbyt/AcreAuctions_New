<?php
declare(strict_types=1);

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Repositories\Post\Exceptions\PostNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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
        $posts = Post::query()->where('slug', $slug)->first();

        if ($posts === null) {
            throw new PostNotFoundException();
        }

        return $posts;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getPosts(): LengthAwarePaginator
    {
        $posts = (new Post)->newQuery();
        return $posts->get()->paginate(8);
    }

    /**
     * @param int $key
     * @param int $id
     * @return bool|Model
     * @throws PostNotFoundException
     */
    public function findImage(int $key, int $id)
    {
        $image = $this->findByPk($id)->images->where('id', $key)->first();

        return ($image === null) ? false : $image;
    }

    /**
     * @return Collection
     */
    public function getRecommendPosts(): Collection
    {
        $posts = (new Post)->newQuery();
        return $posts->inRandomOrder()->limit(4)->get();
    }


    /**
     * Find post by id
     * @param int $id
     * @return Model
     * @throws PostNotFoundException
     */
    public function findByPk(int $id): Model
    {
        $posts = Post::find($id);

        if ($posts === null) {
            throw new PostNotFoundException();
        }

        return $posts;
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
