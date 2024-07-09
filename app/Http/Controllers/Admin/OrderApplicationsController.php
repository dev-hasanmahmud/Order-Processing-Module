<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderApplicationRequest;
use App\Http\Requests\StoreOrderApplicationRequest;
use App\Http\Requests\UpdateOrderApplicationRequest;
use App\OrderApplication;
use App\Role;
use App\Services\AuditLogService;
use App\Status;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderApplicationsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('order_application_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderApplications = OrderApplication::with('status', 'analyst', 'cfo')->get();
        $defaultStatus    = Status::find(1);
        $user             = auth()->user();

        return view('admin.orderApplications.index', compact('orderApplications', 'defaultStatus', 'user'));
    }

    public function create()
    {
        abort_if(Gate::denies('order_application_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orderApplications.create');
    }

    public function store(StoreOrderApplicationRequest $request)
    {
        $orderApplication = OrderApplication::create($request->only('order_amount', 'description'));

        return redirect()->route('admin.order-applications.index');
    }

    public function edit(OrderApplication $orderApplication)
    {
        abort_if(
            Gate::denies('order_application_edit') || !in_array($orderApplication->status_id, [6,7]),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $statuses = Status::whereIn('id', [1, 8, 9])->pluck('name', 'id');

        $orderApplication->load('status');

        return view('admin.orderApplications.edit', compact('statuses', 'orderApplication'));
    }

    public function update(UpdateOrderApplicationRequest $request, OrderApplication $orderApplication)
    {
        $orderApplication->update($request->only('order_amount', 'description', 'status_id'));

        return redirect()->route('admin.order-applications.index');
    }

    public function show(OrderApplication $orderApplication)
    {
        abort_if(Gate::denies('order_application_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderApplication->load('status', 'analyst', 'cfo', 'created_by', 'logs.user', 'comments');
        $defaultStatus = Status::find(1);
        $user          = auth()->user();
        $logs          = AuditLogService::generateLogs($orderApplication);

        return view('admin.orderApplications.show', compact('orderApplication', 'defaultStatus', 'user', 'logs'));
    }

    public function destroy(OrderApplication $orderApplication)
    {
        abort_if(Gate::denies('order_application_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderApplication->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderApplicationRequest $request)
    {
        OrderApplication::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function showSend(OrderApplication $orderApplication)
    {
        abort_if(!auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($orderApplication->status_id == 1) {
            $role = 'Analyst';
            $users = Role::find(3)->users->pluck('name', 'id');
        } else if (in_array($orderApplication->status_id, [3,4])) {
            $role = 'CFO';
            $users = Role::find(4)->users->pluck('name', 'id');
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        return view('admin.orderApplications.send', compact('orderApplication', 'role', 'users'));
    }

    public function send(Request $request, OrderApplication $orderApplication)
    {
        abort_if(!auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($orderApplication->status_id == 1) {
            $column = 'analyst_id';
            $users  = Role::find(3)->users->pluck('id');
            $status = 2;
        } else if (in_array($orderApplication->status_id, [3,4])) {
            $column = 'cfo_id';
            $users  = Role::find(4)->users->pluck('id');
            $status = 5;
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'user_id' => 'required|in:' . $users->implode(',')
        ]);

        $orderApplication->update([
            $column => $request->user_id,
            'status_id' => $status
        ]);

        return redirect()->route('admin.order-applications.index')->with('message', 'Order application has been sent for analysis');
    }

    public function showAnalyze(OrderApplication $orderApplication)
    {
        $user = auth()->user();

        abort_if(
            (!$user->is_analyst || $orderApplication->status_id != 2) && (!$user->is_cfo || $orderApplication->status_id != 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return view('admin.orderApplications.analyze', compact('orderApplication'));
    }

    public function analyze(Request $request, OrderApplication $orderApplication)
    {
        $user = auth()->user();

        if ($user->is_analyst && $orderApplication->status_id == 2) {
            $status = $request->has('approve') ? 3 : 4;
        } else if ($user->is_cfo && $orderApplication->status_id == 5) {
            $status = $request->has('approve') ? 6 : 7;
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'comment_text' => 'required'
        ]);

        $orderApplication->comments()->create([
            'comment_text' => $request->comment_text,
            'user_id'      => $user->id
        ]);

        $orderApplication->update([
            'status_id' => $status
        ]);

        return redirect()->route('admin.order-applications.index')->with('message', 'Analysis has been submitted');
    }
}
