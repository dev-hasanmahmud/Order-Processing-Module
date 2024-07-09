<?php

namespace App\Http\Requests;

use App\OrderApplication;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyOrderApplicationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_application_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:order_applications,id',
        ];
    }
}
