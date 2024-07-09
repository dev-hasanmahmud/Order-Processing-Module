@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.orderApplication.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-applications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.orderApplication.fields.id') }}
                        </th>
                        <td>
                            {{ $orderApplication->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderApplication.fields.order_amount') }}
                        </th>
                        <td>
                            {{ $orderApplication->order_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderApplication.fields.description') }}
                        </th>
                        <td>
                            {{ $orderApplication->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderApplication.fields.status') }}
                        </th>
                        <td>
                            {{ $user->is_user && $orderApplication->status_id < 8 ? $defaultStatus->name : $orderApplication->status->name }}
                        </td>
                    </tr>
                    @if($user->is_admin)
                        <tr>
                            <th>
                                {{ trans('cruds.orderApplication.fields.analyst') }}
                            </th>
                            <td>
                                {{ $orderApplication->analyst->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.orderApplication.fields.cfo') }}
                            </th>
                            <td>
                                {{ $orderApplication->cfo->name ?? '' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if($user->is_admin && count($logs))
                <h3>Logs</h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Changes</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    {{ $log['user'] }}
                                </td>
                                <td>
                                    <ul>
                                        @foreach($log['changes'] as $change)
                                            <li>
                                                {!! $change !!}
                                            </li>
                                        @endforeach
                                        @if($log['comment'])
                                            <li>
                                                <b>Comment</b>: {{ $log['comment'] }}
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                <td>
                                    {{ $log['time'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="form-group">
                @if($user->is_admin && in_array($orderApplication->status_id, [1, 3, 4]))
                    <a class="btn btn-success" href="{{ route('admin.order-applications.showSend', $orderApplication->id) }}">
                        Send to
                        @if($orderApplication->status_id == 1)
                            analyst
                        @else
                            CFO
                        @endif
                    </a>
                @elseif(($user->is_analyst && $orderApplication->status_id == 2) || ($user->is_cfo && $orderApplication->status_id == 5))
                    <a class="btn btn-success" href="{{ route('admin.order-applications.showAnalyze', $orderApplication->id) }}">
                        Submit analysis
                    </a>
                @endif

                @if(Gate::allows('order_application_edit') && in_array($orderApplication->status_id, [6,7]))
                    <a class="btn btn-info" href="{{ route('admin.order-applications.edit', $orderApplication->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                @endif

                @can('order_application_delete')
                    <form action="{{ route('admin.order-applications.destroy', $orderApplication->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-danger" value="{{ trans('global.delete') }}">
                    </form>
                @endcan
            </div>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-applications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
