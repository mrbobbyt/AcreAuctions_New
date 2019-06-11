<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Repositories\Post\Contracts\PostRepositoryContract;
use App\Repositories\Post\Exceptions\PostNotFoundException;
use App\Services\Post\Validator\UpdatePostRequestValidator;
use App\Services\Post\Validator\CreatePostRequestValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Validation\ValidationException;
use App\Services\Post\Contracts\PostServiceContract;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;

class PostController extends Controller
{
    protected $postService;
    protected $postRepository;

    public function __construct(
        PostServiceContract $postService,
        PostRepositoryContract $postRepository
    )
    {
        $this->postService = $postService;
        $this->postRepository = $postRepository;


    }

    /**
     * METHOD: get
     * URL: /blog/{slug}
     * @param string $slug
     * @return Response
     */
    public function view(string $slug): Response
    {
        try {
            $post = $this->postRepository->findBySlug($slug);
        } catch (PostNotFoundException $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_I_AM_A_TEAPOT);
        }

        return \response(['post' => PostResource::make($post)]);
    }

    /**
     * Create Post
     * METHOD: post
     * URL: /post/create
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = (new CreatePostRequestValidator)->attempt($request);
            $post = $this->postService->create($data);

        } catch (JWTException | Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response(['post' => PostResource::make($post)]);
    }

    /**
     * Update Post
     * METHOD: post
     * URL: /post/{id}
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        try {
            $data = (new UpdatePostRequestValidator)->attempt($request);
            $post = $this->postService->update($data, $id);

        } catch (ValidationException $e) {
            return \response(['message' => $e->validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Post not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return \response(['post' => PostResource::make($post)]);
    }

    /**
     * Delete Post
     * METHOD: delete
     * URL: /post/{id}
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        try {
            $this->postService->delete($id);

        } catch (ModelNotFoundException $e) {
            return \response(['message' => 'Post not exist.'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return \response(['message' => 'Post delete error.'], Response::HTTP_BAD_REQUEST);
        }

        return \response(['message' => 'Post successfully deleted.']);
    }

    /**
     * Return posts
     * METHOD: get
     * URL: /blog
     * @return Response
     */
    public function getAllPosts(): Response
    {
        try {
            $result = $this->postRepository->getPosts();
        }  catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return \response(['posts' => new PostCollection($result)]);
    }

    /**
     * Return random posts
     * METHOD: get
     * URL: /post/recommend
     * @return Response
     */
    public function getRecommendPosts(): Response
    {
        try {
            $posts = $this->postRepository->getRecommendPosts();
        }  catch (Throwable $e) {
            return \response(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return \response(['recommendPosts' => new PostCollection($posts)]);
    }

}
