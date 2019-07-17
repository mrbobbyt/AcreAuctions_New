<?php
declare(strict_types=1);

namespace App\Services\Post;

use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Services\Image\ImageService;
use App\Services\Post\Exceptions\PostAlreadyExistsException;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\FullsizePreview;

use App\Services\Post\Contracts\PostServiceContract;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PostService implements PostServiceContract
{
    protected $model;
    protected $postRepository;
    protected $imageService;

    public function __construct(
        Post $post,
        PostRepositoryContract $postRepository,
        ImageService $imageService
    )
    {
        $this->model = $post;
        $this->postRepository = $postRepository;
        $this->imageService = $imageService;
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

        $desc = $data['body']['description'];

        preg_match('/data([^"]*)/i', $data['body']['description'], $results);

        foreach ($results as $result) {
            $name = str_random(20) . '_post_desc.jpg';
            $data64 = explode(',', $result);

            $unCodedImg = base64_decode($data64[1]);

            file_put_contents(public_path('/images/post_desc/' . $name), $unCodedImg);

            $desc = preg_replace('/data([^"]*)/', \Request::root() . '/images/post_desc/' . $name, $desc, 1);
        }

        $post = $this->model->query()->make()->fill(array_merge(
            $data['body'],
            ['description' => $desc]
        ));

        $post->saveOrFail();

        if ($data['body']['media']) {
            foreach ($data['body']['media'] as $imgUrl) {
                if (isset($imgUrl)) {
                    $this->imageService->createImageFromUrl($imgUrl, $post->id, 'post');
                }
            }
        }

        if ($data['image']) {
            foreach ($data['image']['image'] as $key => $item) {
                $this->imageService->create($item, $post->id, 'post');
            }
        }
        return $post;
    }

    /**
     * @param array $data
     * @param int $id
     * @return Model
     * @throws \Throwable
     */
    public function update(array $data, int $id): Model
    {
        $post = $this->postRepository->findByPk($id);

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->postRepository->findByTitle($data['body']['title'])) {
                    throw new PostAlreadyExistsException();
                }
                $data['body']['slug'] = make_url($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                if ($key !== 'media') {
                    $post->$key = $property;
                }
            }
            $post->saveOrFail();
        }

        if ($data['image']) {
            $this->deleteRelatedImages($id);
            foreach ($data['image']['image'] as $image) {
                $this->imageService->create($image, $id, 'post');
            }
        }

        if ($data['body']['media']) {
            foreach ($data['body']['media'] as $imgUrl) {
                if (isset($imgUrl)) {
                    $this->imageService->createImageFromUrl($imgUrl, $post->id, 'post');
                }
            }
        }

        return $post;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    protected function deleteRelatedImages(int $id): void
    {
        $listingImages = $this->postRepository->findByPk($id)->images;

        foreach ($listingImages as $image) {
            $relation = FullsizePreview::query()->where('fullsize_id', $image->id)->first();

            if ($relation) {
                $imagePreview = $this->postRepository->findImage($relation->preview_id, $id);
                $this->imageService->delete($imagePreview);
            }

            $this->imageService->delete($image);
        }
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

        $images = $post->images;
        if ($images !== null) {
            foreach ($images as $image) {
                $this->imageService->delete($image);
            }
        }

        $post->delete();

        return true;
    }
}
