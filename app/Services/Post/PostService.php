<?php
declare(strict_types=1);

namespace App\Services\Post;

use App\Models\Image;
use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Services\Image\ImageService;
use App\Services\Post\Exceptions\PostAlreadyExistsException;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use File;

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

        $post = $this->model->query()->make()->fill(array_merge(
            $data['body'],
            ['description' => '']
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

        $desc = $data['body']['description'];

        preg_match_all('/data[^"]*/i', $data['body']['description'], $results);

        foreach ($results[0] as $result) {
            $descImg = true;
            $data64 = explode(',', $result);
            $image = $this->imageService->create($data64[1], $post->id, 'post', $descImg);

            if ($image) {
                $desc = preg_replace('/data[^"]*/i', \Request::root() . $image->getFullsizeAttribute(), $desc, 1);
            }
        }

        $this->updateDescPost($desc, $post->id);
        return $post;
    }

    /**
     * @param string $newDesc
     * @param int $id
     */
    private function updateDescPost(string $newDesc, int $id)
    {
        $post = $this->postRepository->findByPk($id);
        $post->description = $newDesc;
        $post->save();
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

        /** Give array links from existing description */
        preg_match_all("/src\s*=\s*\"(.+?)\"/i", $post->description, $linksFromDesc);

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->postRepository->findByTitle(
                        $data['body']['title']) && $post->title !== $data['body']['title']
                ) {
                    throw new PostAlreadyExistsException();
                }
                $data['body']['slug'] = make_url($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                if ($key !== 'media' && $key !== 'description') {
                    $post->$key = $property;
                }
            }
            $post->saveOrFail();
        }


        if ($data['body']['description']) {
            $desc = $data['body']['description'];

            preg_match_all('/data[^"]*/i', $data['body']['description'], $results);

            foreach ($results[0] as $result) {
                $descImg = true;
                $data64 = explode(',', $result);
                $image = $this->imageService->create($data64[1], $post->id, 'post', $descImg);

                if ($image) {
                    $desc = preg_replace('/data[^"]*/i', \Request::root() . $image->getFullsizeAttribute(), $desc, 1);
                }
            }

            $this->updateDescPost($desc, $post->id);

        }

        $updatedPost = $this->postRepository->findByPk($id);

        /** Give array links from updated description */
        preg_match_all("/src\s*=\s*\"(.+?)\"/i", $updatedPost->description, $linksFromUpdatedDesc);

        /** if removed images from desc, we need remove images also from server */
        $unnecessaryImages = array_diff($linksFromDesc[1], $linksFromUpdatedDesc[1]);

        foreach ($unnecessaryImages as $unnecessaryImage) {
            $imagePath = stristr($unnecessaryImage, 'images');
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        if ($data['image']) {

            $fullSizeImageNameFromDB = [];
            $fullSizeImageNameFromData = [];

            foreach ($post->gallery as $imageGallery) {
                $imageNameFullSize = Image::query()->where('id', $imageGallery->fullsize_id)->first();
                array_push($fullSizeImageNameFromDB, $imageNameFullSize->name);
            }

            foreach ($data['image']['image'] as $image) {
                $imageFullSizeName = $image->getClientOriginalName();

                array_push($fullSizeImageNameFromData, $imageFullSizeName);

                if (!File::exists(public_path('images/fullsize/' . $imageFullSizeName))) {
                    $this->imageService->create($image, $id, 'post');
                }
            }

            $removedImages = array_diff($fullSizeImageNameFromDB, $fullSizeImageNameFromData);
            $this->deleteRelatedFullSizeImages($removedImages);
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
     * @param array $removedImages
     * @throws Exception
     */
    protected function deleteRelatedFullSizeImages(array $removedImages): void
    {

        foreach ($removedImages as $removedImage) {
            $image = Image::query()->where('name', $removedImage)->first();
            $this->imageService->deleteImageWhenUpdatedPost($image);
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
