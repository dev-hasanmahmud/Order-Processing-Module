<?php

namespace App\Http\Requests;

use App\OrderApplication;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreOrderApplicationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_application_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'order_amount' => [
                'required',
                'gt:0',
            ],
        ];
    }
}
