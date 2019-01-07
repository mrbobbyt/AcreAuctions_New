<?php
declare(strict_types = 1);

use Illuminate\Http\UploadedFile;


if (! function_exists('get_image_path')) {
    /**
     * Create path to the image
     * @param $model
     * @param $name
     * @return string
     */
    function get_image_path($model, $name): string
    {
        return public_path().'/images/'. $model .'/' . $name;
    }
}

if (! function_exists('upload_image')) {
    /**
     * Upload image into server
     * @param UploadedFile $img
     * @param string $type
     * @param string $folder
     * @return string
     * @throws Exception
     */
    function upload_image($img, $folder, $type): string
    {
        $name = time() .'_'. $type .'_'. $img->getClientOriginalName();
        if (!$img->move('images/'. $folder, $name)) {
            throw new Exception('Can not upload '. $type .'.', 500);
        }

        return $name;
    }
}

if (! function_exists('make_url')) {
    /**
     * Return slug created from title
     * @param string $title
     * @return string
     */
    function make_url(string $title): string
    {
        return preg_replace('/[^a-z0-9]+/i', '_', $title);
    }
}
