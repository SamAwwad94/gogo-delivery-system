<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\ShadcnDataTableTrait;
use Carbon\Carbon;

class ShadcnOrderDataTable extends DataTable
{
    use ShadcnDataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="rounded border-input bg-transparent text-primary focus:ring-primary/30" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('id', function ($row) {
                return '<a href="' . route('order.show', $row->id) . '" class="text-primary hover:underline">#' . $row->id . '</a>';
            })
            ->editColumn('client_id', function ($row) {
                return optional($row->client)->display_name ?? '-';
            })
            ->editColumn('deliveryman_id', function ($row) {
                return optional($row->deliveryman)->display_name ?? '-';
            })
            ->editColumn('pickup_point', function ($row) {
                return $row->pickup_point;
            })
            ->editColumn('delivery_point', function ($row) {
                return $row->delivery_point;
            })
            ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->format('d M Y');
            })
            ->editColumn('status', function ($row) {
                $status_class = match($row->status) {
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    'accepted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'on_going' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                    'ready' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                };
                
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">' . 
                    ucfirst(str_replace('_', ' ', $row->status)) . 
                '</span>';
            })
            ->editColumn('payment_status', function ($row) {
                $status_class = match($row->payment_status) {
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                };
                
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">' . 
                    ucfirst($row->payment_status) . 
                '</span>';
            })
            ->editColumn('payment_type', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->payment_type));
            })
            ->editColumn('total_amount', function ($row) {
                return getPriceFormat($row->total_amount);
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="flex items-center space-x-2">';
                
                $action .= '<a href="' . route('order.show', $row->id) . '" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring h-8 w-8 p-0 hover:bg-accent hover:text-accent-foreground" title="View">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>';
                
                $action .= '<a href="' . route('order.edit', $row->id) . '" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring h-8 w-8 p-0 hover:bg-accent hover:text-accent-foreground" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </a>';
                
                $action .= '<a href="' . route('order-invoice', $row->id) . '" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring h-8 w-8 p-0 hover:bg-accent hover:text-accent-foreground" title="Invoice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                </a>';
                
                $action .= '</div>';
                
                return $action;
            })
            ->rawColumns(['checkbox', 'id', 'status', 'payment_status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('checkbox')
                ->title('<input type="checkbox" class="rounded border-input bg-transparent text-primary focus:ring-primary/30" id="datatable-checkbox-all">')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(20)
                ->addClass('text-center'),
            Column::make('id')->title('Order ID'),
            Column::make('client_id')->title('Client'),
            Column::make('deliveryman_id')->title('Delivery Person'),
            Column::make('pickup_point')->title('Pickup'),
            Column::make('delivery_point')->title('Delivery'),
            Column::make('date')->title('Date'),
            Column::make('status')->title('Status'),
            Column::make('payment_status')->title('Payment Status'),
            Column::make('payment_type')->title('Payment Type'),
            Column::make('total_amount')->title('Total'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Orders_' . date('YmdHis');
    }
}
