<?php
declare(strict_types = 1);

use Illuminate\Http\UploadedFile;


if (! function_exists('get_image_path')) {
    /**
     * Create path to the image
     * @param string $model
     * @param string $name
     * @return string
     */
    function get_image_path($model, $name): string
    {
        return public_path().'/images/'. $model .'/' . $name;
    }
}

if (! function_exists('get_doc_path')) {
    /**
     * Create path to the doc
     * @param int $id
     * @param string $name
     * @return string
     */
    function get_doc_path($id, $name): string
    {
        return public_path().'/doc/'. $id .'/' . $name;
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

if (! function_exists('upload_doc')) {
    /**
     * Upload doc into server
     * @param UploadedFile $doc
     * @param string $type
     * @param int $id
     * @return string
     * @throws Exception
     */
    function upload_doc($doc, $id, $type): string
    {
        $name = time() .'_'. $type .'_'. $doc->getClientOriginalName();
        if (!$doc->move('doc/'. $id, $name)) {
            throw new Exception('Can not upload '. $type .'.', 500);
        }

        return $name;
    }
}