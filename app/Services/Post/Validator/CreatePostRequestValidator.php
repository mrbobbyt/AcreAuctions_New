<?php
declare(strict_types = 1);

namespace App\Services\Post\Validator;

use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class CreatePostRequestValidator implements AbstractValidator
{
    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function attempt(Request $request): array
    {
        return [
            'body' => $this->validateBody($request),
            'image' => $this->validateImage($request),
        ];
    }

    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateBody(Request $request): array
    {

        $validator = Validator::make($request->all(), [
            'author_id' => 'required|numeric',
            'title' => 'required|string|max:255|min:3',
            'description' => 'required|string|min:3',
            'media' => 'array',
            'media.*' => 'nullable|string',
            'allow_comments' => 'required|boolean',
            'allow_somethings' => 'required|boolean'
        ]);

        return $validator->validate();
    }

    /**
     * Validate given data
     * @param Request $request
     * @return array
     */
    public function validateImage(Request $request): array
    {
        $validator = Validator::make($request->only('image'), [
            'image' => 'array',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        return $validator->validate();
    }
}
