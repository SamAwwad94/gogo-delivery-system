<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class ShippedOrderDataTable extends DataTable
{
    use DataTableTrait;

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn(
                'checkbox',
                fn($row) =>
                '<input type="checkbox" class="select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">'
            )
            ->editColumn('created_at', fn($query) => dateAgoFormate($query->created_at, true))
            ->addColumn(
                'invoice',
                fn($query) =>
                $query ? '<a href="' . route('order-invoice', $query->id) . '"><i class="fa fa-download"></i></a>' : 'N/A'
            )
            ->editColumn('milisecond', fn($row) => $row->milisecond ?? '-')
            ->editColumn(
                'id',
                fn($row) =>
                $row->id ? '<a href="' . route('order.show', $row->id) . '">' . $row->id . '</a>' : '-'
            )
            ->addColumn(
                'name',
                fn($row) =>
                optional($row->courierCompany)->name ?? '-'
            )
            ->addColumn(
                'link',
                fn($row) =>
                $row->courierCompany && $row->courierCompany->link
                ? '<a href="' . $row->courierCompany->link . '" target="_blank">' . $row->courierCompany->link . '</a>'
                : '-'
            )
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
            ->rawColumns([
                'action',
                'checkbox',
                'invoice',
                'id',
                'name',
                'link'
            ]);
    }

    public function query(Order $model)
    {
        $auth = auth()->user();
        $query = $model->newQuery()->where('status', 'shipped');

        if ($status = request()->input('status')) {
            $query->where('status', $status);
        }

        if ($city = request()->input('city_id')) {
            $query->where('city_id', $city);
        }

        if ($country = request()->input('country_id')) {
            $query->where('country_id', $country);
        }

        if ($from = request()->input('from_date') && $to = request()->input('to_date')) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($auth->user_type === 'client') {
            $query->where('client_id', $auth->id);
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
            ['data' => 'milisecond', 'name' => 'milisecond', 'title' => $prefix . ' #'],
            ['data' => 'id', 'name' => 'id', 'title' => __('message.order_id')],
            ['data' => 'name', 'name' => 'name', 'title' => __('message.company_name')],
            ['data' => 'link', 'name' => 'link', 'title' => __('message.traking_link')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.date')],
            ['data' => 'invoice', 'name' => 'invoice', 'title' => __('message.invoice'), 'orderable' => false],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
