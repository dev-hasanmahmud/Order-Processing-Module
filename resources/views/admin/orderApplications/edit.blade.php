@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.orderApplication.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.order-applications.update", [$orderApplication->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="order_amount">{{ trans('cruds.orderApplication.fields.order_amount') }}</label>
                <input class="form-control {{ $errors->has('order_amount') ? 'is-invalid' : '' }}" type="number" name="order_amount" id="order_amount" value="{{ old('order_amount', $orderApplication->order_amount) }}" step="0.01" required>
                @if($errors->has('order_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderApplication.fields.order_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.orderApplication.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $orderApplication->description) }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderApplication.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="status_id">{{ trans('cruds.orderApplication.fields.status') }}</label>
                <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status_id" id="status_id">
                    @foreach($statuses as $id => $status)
                        <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderApplication.fields.status_helper') }}</span>
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
