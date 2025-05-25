<?php
namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class OrderDataTable extends DataTable
{
    use DataTableTrait;

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class=" select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('status', function ($query) {
                $status = 'primary';
                $status_name = 'default';
                switch ($query->status) {
                    case 'draft':
                        $status = 'light';
                        $status_name = __('message.draft');
                        break;
                    case 'create':
                        $status = 'primary';
                        $status_name = __('message.create');
                        break;
                    case 'completed':
                        $status = 'success';
                        $status_name = __('message.delivered');
                        break;
                    case 'courier_assigned':
                        $status = 'warning';
                        $status_name = __('message.assigned');
                        break;
                    case 'active':
                        $status = 'info';
                        $status_name = __('message.active');
                        break;
                    case 'courier_departed':
                        $status = 'info';
                        $status_name = __('message.departed');
                        break;
                    case 'courier_picked_up':
                        $status = 'warning';
                        $status_name = __('message.picked_up');
                        break;
                    case 'courier_arrived':
                        $status = 'warning';
                        $status_name = __('message.arrived');
                        break;
                    case 'cancelled':
                        $status = 'danger';
                        $status_name = __('message.cancelled');
                        break;
                    case 'shipped':
                        $status = 'primary';
                        $status_name = __('message.shipped');
                        break;
                    case 'reschedule':
                        $status = 'secondary';
                        $status_name = __('message.reschedule');
                        break;
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })
            ->editColumn('created_at', fn($q) => dateAgoFormate($q->created_at, true))
            ->editColumn('pickup_point', function ($q) {
                $address = $q->pickup_point['address'] ?? '';
                return $address && $q->deleted_at ? '<span style="color:  #000000" data-toggle="tooltip" title="' . $address . '">' . stringLong($address, 'title', 20) . '</span>' : ($address ? '<span data-toggle="tooltip" title="' . $address . '">' . stringLong($address, 'title', 20) . '</span>' : '-');
            })
            ->editColumn('delivery_point', function ($q) {
                $address = $q->delivery_point['address'] ?? '';
                return $address && $q->deleted_at ? '<span style="color:  #000000" data-toggle="tooltip" title="' . $address . '">' . stringLong($address, 'title', 20) . '</span>' : ($address ? '<span data-toggle="tooltip" title="' . $address . '">' . stringLong($address, 'title', 20) . '</span>' : '-');
            })
            ->addColumn('invoice', fn($q) => $q->status == 'completed' ? '<a href="' . route('order-invoice', $q->id) . '"><i class="fa fa-download"></i></a>' : 'N/A')
            ->editColumn('delivery_man_id', fn($q) => $q->delivery_man ? '<a href="' . route('deliveryman.show', $q->delivery_man->id) . '">' . $q->delivery_man->name . '</a>' : '-')
            ->editColumn('parent_order_id', fn($q) => in_array($q->id, $q->pluck('parent_order_id')->toArray()) ? '<i class="fa-solid fa-right-left text-primary"></i>' : '-')
            ->filterColumn('delivery_man_id', fn($q, $k) => $q->orWhereHas('delivery_man', fn($q2) => $q2->where('name', 'like', "%{$k}%")))
            ->editColumn('milisecond', fn($r) => $r->milisecond ?? '-')
            ->editColumn('delivery_datetime', fn($r) => dateAgoFormate($r->delivery_datetime, true) ?? '-')
            ->editColumn('pickup_datetime', fn($r) => dateAgoFormate($r->pickup_datetime, true) ?? '-')
            ->editColumn('assign_datetime', fn($r) => dateAgoFormate($r->assign_datetime, true) ?? '-')
            ->order(function ($q) {
                if (request()->has('order')) {
                    $order = request()->order[0];
                    $index = $order['column'];
                    $q->orderBy(request()->columns[$index]['data'] ?? 'id', $order['dir'] ?? 'desc');
                }
            })
            ->editColumn('id', fn($r) => $r->id ? '<a href="' . route('order.show', $r->id) . '">' . $r->id . '</a>' : '-')
            ->editColumn('client_id', fn($r) => $r->client ? '<a href="' . route('users.show', $r->client->id) . '">' . $r->client->name . '</a>' : '-')
            ->filterColumn('client_id', fn($q, $k) => $q->orWhereHas('client', fn($q2) => $q2->where('name', 'like', "%{$k}%")))
            ->editColumn('total_amount', fn($r) => optional($r)->total_amount ? getPriceFormat($r->total_amount) : '-')
            ->addColumn('assign', function ($r) {
                if ($r->status != 'shipped_order') {
                    if ($r->deleted_at)
                        return "<span style='color: red'>" . __('message.order_deleted') . "</span>";
                    if ($r->status === 'cancelled')
                        return "<span class='text-primary'>" . __('message.order_cancelled') . "</span>";
                    if ($r->status === 'draft')
                        return "<span class='text-primary'>" . __('message.order_draft') . "</span>";
                    if ($r->status === 'completed')
                        return "<span class='text-primary'>" . __('message.order_completed') . "</span>";
                    if ($r->status === 'shipped')
                        return "<span class='text-primary'>" . __('message.order_shipped') . "</span>";
                    return '<a href="' . route("order-assign", ['id' => $r->id]) . '" class="btn btn-sm btn-outline-primary loadRemoteModel">' . ($r->delivery_man_id === null ? __('message.assign') : __('message.transfer')) . '</a>';
                }
                return "<span class='text-primary'>" . __('message.order_shipped') . "</span>";
            })
            ->addColumn('action', function ($order) {
                return '<div data-order-actions=\'' . json_encode([
                    'id' => $order->id,
                    'deletedAt' => $order->deleted_at,
                    'status' => $order->status,
                    'canEdit' => auth()->user()->can('order-edit'),
                    'canDelete' => auth()->user()->can('order-delete'),
                    'canShow' => auth()->user()->can('order-show'),
                    'userRoles' => auth()->user()->roles->pluck('name')->toArray(),
                ]) . '\'></div>';
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'checkbox', 'pickup_point', 'delivery_point', 'invoice', 'delivery_man_id', 'client_id', 'id', 'assign', 'parent_order_id']);
    }

    public function query(Order $model)
    {
        $auth = auth()->user();
        $query = $model->newQuery()->whereNotIn('status', ['pending']);
        $pendingOrder = $model->newQuery();

        // Filters ...
        $status = request()->input('status');
        if ($status)
            $query->where('status', $status);
        if ($city = request()->input('city_id'))
            $query->where('city_id', $city);
        if ($country = request()->input('country_id'))
            $query->where('country_id', $country);
        if ($from = request()->input('from_date') and $to = request()->input('to_date'))
            $query->whereBetween('created_at', [$from, $to]);
        if ($auth->user_type == 'client')
            $query->where('client_id', $auth->id);

        // Order type filter
        $type = $_GET['orders_type'] ?? null;
        if ($type) {
            $map = [
                'create' => fn($q) => $q->where('status', 'create')->where('is_shipped', 0),
                'draft' => fn($q) => $q->where('status', 'draft')->where('is_shipped', 0),
                'today' => fn($q) => $q->whereDate('created_at', Carbon::today()),
                'inprogress' => fn($q) => $q->whereIn('status', ['courier_departed', 'courier_picked_up', 'courier_assigned', 'courier_arrived', 'active'])->where('is_shipped', 0),
                'cancel' => fn($q) => $q->where('status', 'cancelled')->where('is_shipped', 0),
                'complete' => fn($q) => $q->where('status', 'completed')->where('is_shipped', 0),
                'reschedule' => fn($q) => $q->where('status', 'reschedule'),
                'pending' => fn($q) => $pendingOrder->where('status', 'pending'),
                'shipped' => fn($q) => $q->where('status', 'shipped'),
                'bidding' => fn($q) => $q->where('bid_type', 1),
                'schedule' => fn($q) => $q->where(function ($q) {
                    $q->whereDate('pickup_point->start_time', '>', now()->toDateString())
                        ->orWhereDate('delivery_point->start_time', '>', now()->toDateString());
                }),
            ];
            if (isset($map[$type]))
                $query = $map[$type]($query);
        }

        return $query->withTrashed();
    }

    protected function getColumns()
    {
        $prefix = strtoupper(appSettingcurrency('prefix'));
        return [
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '<input type="checkbox" class="select-all-table" name="select_all" id="select-all-table">', 'orderable' => false, 'searchable' => false, 'width' => 20],
            ['data' => 'milisecond', 'name' => 'milisecond', 'title' => $prefix . ' #'],
            ['data' => 'id', 'name' => 'id', 'title' => __('message.order_id')],
            ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.customer_name')],
            ['data' => 'pickup_point', 'name' => 'pickup_point', 'title' => __('message.pickup_address')],
            ['data' => 'delivery_point', 'name' => 'delivery_point', 'title' => __('message.delivery_address')],
            ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man')],
            ['data' => 'pickup_datetime', 'name' => 'pickup_datetime', 'title' => __('message.pickup_date')],
            ['data' => 'delivery_datetime', 'name' => 'delivery_datetime', 'title' => __('message.delivery_date')],
            ['data' => 'assign_datetime', 'name' => 'assign_datetime', 'title' => __('message.assign_date')],
            ['data' => 'invoice', 'name' => 'invoice', 'title' => __('message.invoice'), 'orderable' => false],
            ['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('message.total_amount')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            ['data' => 'parent_order_id', 'name' => 'parent_order_id', 'title' => __('message.is_return')],
            ['data' => 'assign', 'name' => 'assign', 'title' => __('message.assign'), 'orderable' => false],
            Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-center hide-search'),
        ];
    }
}
