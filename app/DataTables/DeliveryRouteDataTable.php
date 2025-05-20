<?php

namespace App\DataTables;

use App\Models\DeliveryRoute;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Traits\ShadcnDataTableTrait;

class DeliveryRouteDataTable extends DataTable
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
            ->editColumn('status', function($route) {
                return $route->status_badge;
            })
            ->editColumn('delivery_man_id', function($route) {
                return optional($route->deliveryMan)->name ?? 'N/A';
            })
            ->editColumn('created_at', function($route) {
                return $route->created_at->format('M d, Y H:i');
            })
            ->addColumn('orders_count', function($route) {
                return $route->orders->count();
            })
            ->addColumn('action', function($route) {
                $action = '<div class="flex items-center space-x-2">';
                
                // View button
                $action .= '<a href="' . route('delivery-routes.show', $route->id) . '" class="action-button" data-tooltip="View">
                    <i class="fas fa-eye text-primary"></i>
                </a>';
                
                // Map button
                $action .= '<a href="' . route('delivery-routes.map', $route->id) . '" class="action-button" data-tooltip="View Map">
                    <i class="fas fa-map-marked-alt text-info"></i>
                </a>';
                
                // Edit button
                $action .= '<a href="' . route('delivery-routes.edit', $route->id) . '" class="action-button" data-tooltip="Edit">
                    <i class="fas fa-edit text-secondary"></i>
                </a>';
                
                // Delete button
                $action .= '<a href="javascript:void(0)" class="action-button delete-route" data-id="' . $route->id . '" data-tooltip="Delete">
                    <i class="fas fa-trash text-destructive"></i>
                </a>';
                
                $action .= '</div>';
                
                return $action;
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DeliveryRoute $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DeliveryRoute $model)
    {
        return $model->newQuery()->with(['deliveryMan', 'orders']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('delivery-routes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'desc')
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            )
            ->parameters($this->getShadcnDataTableParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('delivery_man_id')->title('Delivery Man'),
            Column::make('start_location'),
            Column::make('status'),
            Column::make('orders_count')->title('Orders'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
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
        return 'DeliveryRoutes_' . date('YmdHis');
    }
}
