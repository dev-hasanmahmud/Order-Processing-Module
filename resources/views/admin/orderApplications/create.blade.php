@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.orderApplication.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.order-applications.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="order_amount">{{ trans('cruds.orderApplication.fields.order_amount') }}</label>
                <input class="form-control {{ $errors->has('order_amount') ? 'is-invalid' : '' }}" type="number" name="order_amount" id="order_amount" value="{{ old('order_amount', '') }}" step="0.01" required>
                @if($errors->has('order_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderApplication.fields.order_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.orderApplication.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderApplication.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
