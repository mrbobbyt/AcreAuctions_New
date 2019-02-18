<?php
declare(strict_types = 1);

namespace App\Services\Admin\Validators;

use App\Rules\CheckExportType;
use App\Services\Auth\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class ExportRequestValidator implements AbstractValidator
{

    /**
     * Return validated array of data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function attempt(Request $request)
    {
        $formats = [
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            'pdf' => \Maatwebsite\Excel\Excel::MPDF,
            'xls' => \Maatwebsite\Excel\Excel::XLS,
            'html' => \Maatwebsite\Excel\Excel::HTML,
        ];

        return [
            'body' => $this->validateBody($request),
            'type' => $this->validateType($request),
            'format' => $formats[$request->input('type')],
        ];
    }

    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateBody(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'array',
            'id.*' => 'required|numeric',
        ]);

        return $validator->validate();
    }


    /**
     * Validate given data
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function validateType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', new CheckExportType]
        ]);

        return $validator->validate();
    }
}