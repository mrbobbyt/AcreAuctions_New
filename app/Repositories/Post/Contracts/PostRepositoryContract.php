<?php
declare(strict_types = 1);

namespace App\Repositories\Post\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\Exceptions\PostNotFoundException;

interface PostRepositoryContract
{
    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     * @throws PostNotFoundException
     */
    public function findBySlug(string $slug);

    /**
     * Find recommend posts
     * @return Model
     * @throws PostNotFoundException
     */
    public function getRecommendPosts();

    /**
     * @return LengthAwarePaginator
     */
    public function getPosts();


    /**
     * Find post by id
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id);


    /**
     * Check existing Post by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title);

}
