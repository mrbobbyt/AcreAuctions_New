<?php
declare(strict_types=1);

namespace App\Services\Post;

use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Services\Post\Exceptions\PostAlreadyExistsException;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

use App\Services\Post\Contracts\PostServiceContract;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PostService implements PostServiceContract
{
    protected $model;
    protected $postRepository;

    public function __construct(
        Post $post,
        PostRepositoryContract $postRepository
    )
    {
        $this->model = $post;
        $this->postRepository = $postRepository;
    }


    /**
     * @param array $data
     * @return Model
     * @throws \Throwable
     */
    public function create(array $data): Model
    {
        if ($this->postRepository->findByTitle($data['body']['title'])) {
            throw new PostAlreadyExistsException();
        }

        $data['body']['slug'] = make_url($data['body']['title']);
        $post = $this->model->query()->make()->fill($data['body']);
        $post->saveOrFail();
        return $post;
    }

    /**
     * Delete post and related models
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $post = $this->postRepository->findByPk($id);
        $post->delete();

        return true;
    }
}
