<?php

namespace App\Http\Requests;

use App\OrderApplication;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateOrderApplicationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(
            Gate::denies('order_application_edit') || !in_array($this->route()->order_application->status_id, [6, 7]),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return true;
    }

    public function rules()
    {
        return [
            'order_amount' => [
                'required',
            ],
        ];
    }
}
