<?php

use Illuminate\Http\UploadedFile;



if (! function_exists('get_image_path')) {
    function get_image_path($model, $name)
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
    function upload_image($img, $folder, $type)
    {
        $name = time() .'_'. $type .'_'. $img->getClientOriginalName();
        if (!$img->move('images/'. $folder, $name)) {
            throw new Exception('Can not upload '. $type .'.', 500);
        }

        return $name;
    }
}