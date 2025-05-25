<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class ClientOrderDataTable extends DataTable
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
                $status = 'danger';
                $status_name = 'cancelled';
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
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })
            ->editColumn('created_at', fn($q) => dateAgoFormate($q->created_at, true))
            ->editColumn('pickup_point', fn($q) => $this->renderAddress($q->pickup_point['address'] ?? '', $q->deleted_at))
            ->editColumn('delivery_point', fn($q) => $this->renderAddress($q->delivery_point['address'] ?? '', $q->deleted_at))
            ->addColumn('invoice', fn($q) => $q->status == 'completed' ? '<a href="' . route('order-invoice', $q->id) . '"><i class="fa fa-download"></i></a>' : 'N/A')
            ->editColumn('delivery_man_id', fn($q) => $q->delivery_man ? '<a href="' . route('deliveryman.show', $q->delivery_man->id) . '">' . $q->delivery_man->name . '</a>' : '-')
            ->editColumn('parent_order_id', fn($q) => in_array($q->id, $q->pluck('parent_order_id')->toArray()) ? '<i class="fa-solid fa-right-left text-primary"></i>' : '-')
            ->filterColumn('delivery_man_id', fn($query, $keyword) => $query->orWhereHas('delivery_man', fn($q) => $q->where('name', 'like', "%{$keyword}%")))
            ->editColumn('milisecond', fn($row) => strtoupper(appSettingcurrency('prefix')) . $row->milisecond)
            ->editColumn('delivery_datetime', fn($row) => dateAgoFormate($row->delivery_datetime, true) ?? '-')
            ->editColumn('pickup_datetime', fn($row) => dateAgoFormate($row->pickup_datetime, true) ?? '-')
            ->editColumn('assign_datetime', fn($row) => dateAgoFormate($row->assign_datetime, true) ?? '-')
            ->editColumn('id', fn($row) => $row->id ? '<a href="' . route('order.show', $row->id) . '">' . $row->id . '</a>' : '-')
            ->editColumn('client_id', fn($row) => $row->client ? '<a href="' . route('users.show', $row->client->id) . '">' . $row->client->name . '</a>' : '-')
            ->filterColumn('client_id', fn($query, $keyword) => $query->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$keyword}%")))
            ->addColumn('action', function ($order) {
                return '<div data-order-actions=\'' . json_encode([
                    'id' => $order->id,
                    'deletedAt' => $order->deleted_at,
                    'status' => $order->status,
                    'canEdit' => auth()->user()->can('order-edit'),
                    'canDelete' => auth()->user()->can('order-delete'),
                    'canShow' => auth()->user()->can('order-show'),
                    'userRoles' => auth()->user()->getRoleNames()->toArray(),
                ]) . '\'></div>';
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'checkbox', 'pickup_point', 'delivery_point', 'invoice', 'delivery_man_id', 'client_id', 'id', 'parent_order_id']);
    }

    public function query(Order $model)
    {
        $auth = auth()->user();
        $query = $model->newQuery();

        if ($status = request()->input('status'))
            $query->where('status', $status);
        if ($city = request()->input('city_id'))
            $query->where('city_id', $city);
        if ($country = request()->input('country_id'))
            $query->where('country_id', $country);
        if ($from = request()->input('from_date') && $to = request()->input('to_date'))
            $query->whereBetween('created_at', [$from, $to]);
        if ($auth->user_type == 'client')
            $query->where('client_id', $auth->id);

        switch ($_GET['orders_type'] ?? null) {
            case 'pending':
                $query->where('status', 'create')->where(fn($q) => $q->where('is_reschedule', 0)->where('is_shipped', 0));
                break;
            case 'schedule':
                $tomorrow = Carbon::tomorrow();
                $query->whereDate('pickup_datetime', $tomorrow)->orWhereDate('delivery_datetime', $tomorrow);
                break;
            case 'draft':
                $query->where('status', 'draft')->where(fn($q) => $q->where('is_reschedule', 0)->where('is_shipped', 0));
                break;
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'inprogress':
                $query->whereIn('status', ['courier_departed', 'courier_picked_up', 'courier_assigned', 'courier_arrived', 'active'])->where(fn($q) => $q->where('is_reschedule', 0)->where('is_shipped', 0));
                break;
            case 'cancel':
                $query->where('status', 'cancelled')->where(fn($q) => $q->where('is_reschedule', 0)->where('is_shipped', 0));
                break;
            case 'complete':
                $query->where('status', 'completed')->where(fn($q) => $q->where('is_reschedule', 0)->where('is_shipped', 0));
                break;
            case 'reschedule_order':
                $query->whereNotNull('is_reschedule')->where('is_reschedule', '!=', 0);
                break;
            case 'shipped_order':
                $query->whereNotNull('is_shipped')->where('is_shipped', '!=', 0);
                break;
        }

        return $query->withTrashed();
    }

    protected function getColumns()
    {
        $prefix = strtoupper(appSettingcurrency('prefix'));
        return [
            Column::make('checkbox')
                ->searchable(false)
                ->title('<input type="checkbox" class="select-all-table" name="select_all" id="select-all-table">')
                ->orderable(false)
                ->width(20),
            ['data' => 'milisecond', 'name' => 'milisecond', 'title' => $prefix . ' #' ?? __('message.document_at')],
            ['data' => 'id', 'name' => 'id', 'title' => __('message.order_id')],
            ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.customer_name')],
            ['data' => 'pickup_point', 'name' => 'pickup_point', 'title' => __('message.pickup_address')],
            ['data' => 'delivery_point', 'name' => 'delivery_point', 'title' => __('message.delivery_address')],
            ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man')],
            ['data' => 'pickup_datetime', 'name' => 'pickup_datetime', 'title' => __('message.pickup_date')],
            ['data' => 'delivery_datetime', 'name' => 'delivery_datetime', 'title' => __('message.delivery_date')],
            ['data' => 'assign_datetime', 'name' => 'assign_datetime', 'title' => __('message.assign_date')],
            ['data' => 'invoice', 'name' => 'invoice', 'title' => __('message.invoice'), 'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            ['data' => 'parent_order_id', 'name' => 'parent_order_id', 'title' => __('message.is_return')],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }

    private function renderAddress($address, $isDeleted)
    {
        $title = htmlspecialchars($address, ENT_QUOTES);
        $short = stringLong($address, 'title', 20);
        $style = $isDeleted ? 'style="color: #000000"' : '';
        return $address ? "<span $style data-toggle='tooltip' title='{$title}'>{$short}</span>" : '-';
    }
}
