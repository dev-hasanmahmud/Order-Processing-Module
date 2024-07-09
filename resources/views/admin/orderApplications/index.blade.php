@extends('layouts.admin')
@section('content')
@can('order_application_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.order-applications.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.orderApplication.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.orderApplication.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-orderApplication">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.orderApplication.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.orderApplication.fields.order_amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.orderApplication.fields.description') }}
                        </th>
                        <th>
                            {{ trans('cruds.orderApplication.fields.status') }}
                        </th>
                        @if($user->is_admin)
                            <th>
                                {{ trans('cruds.orderApplication.fields.analyst') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderApplication.fields.cfo') }}
                            </th>
                        @endif
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderApplications as $key => $orderApplication)
                        <tr data-entry-id="{{ $orderApplication->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $orderApplication->id ?? '' }}
                            </td>
                            <td>
                                {{ $orderApplication->order_amount ?? '' }}
                            </td>
                            <td>
                                {{ $orderApplication->description ?? '' }}
                            </td>
                            <td>
                                {{ $user->is_user && $orderApplication->status_id < 8 ? $defaultStatus->name : $orderApplication->status->name }}
                            </td>
                            @if($user->is_admin)
                                <td>
                                    {{ $orderApplication->analyst->name ?? '' }}
                                </td>
                                <td>
                                    {{ $orderApplication->cfo->name ?? '' }}
                                </td>
                            @endif
                            <td>
                                @if($user->is_admin && in_array($orderApplication->status_id, [1, 3, 4]))
                                    <a class="btn btn-xs btn-success" href="{{ route('admin.order-applications.showSend', $orderApplication->id) }}">
                                        Send to
                                        @if($orderApplication->status_id == 1)
                                            analyst
                                        @else
                                            CFO
                                        @endif
                                    </a>
                                @elseif(($user->is_analyst && $orderApplication->status_id == 2) || ($user->is_cfo && $orderApplication->status_id == 5))
                                    <a class="btn btn-xs btn-success" href="{{ route('admin.order-applications.showAnalyze', $orderApplication->id) }}">
                                        Submit analysis
                                    </a>
                                @endif

                                @can('order_application_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.order-applications.show', $orderApplication->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @if(Gate::allows('order_application_edit') && in_array($orderApplication->status_id, [6,7]))
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.order-applications.edit', $orderApplication->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endif

                                @can('order_application_delete')
                                    <form action="{{ route('admin.order-applications.destroy', $orderApplication->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('order_application_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.order-applications.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-orderApplication:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

})

</script>
@endsection
