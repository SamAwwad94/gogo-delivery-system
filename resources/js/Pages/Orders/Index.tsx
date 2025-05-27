import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Select from 'react-select';
import Swal from 'sweetalert2';

interface Order {
    id: number;
    client?: {
        name: string;
        contact_number: string;
    };
    pickup_point?: string;
    delivery_point?: string;
    status: string;
    total_amount: number;
    date: string;
    created_at: string;
}

interface OrdersIndexProps {
    pageTitle: string;
    auth_user: any;
    assets: string[];
    orders: {
        data: Order[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters?: {
        order_type?: string;
        status?: string;
        date_start?: string;
        date_end?: string;
    };
}

export default function Index({ pageTitle, auth_user, assets, orders, filters }: OrdersIndexProps) {
    const [loading, setLoading] = useState(false);
    const [selectedOrders, setSelectedOrders] = useState<number[]>([]);
    const [currentFilters, setCurrentFilters] = useState(filters || {});

    // Format date helper
    const formatDate = (dateString: string) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    // Format location helper
    const formatLocation = (locationString?: string) => {
        if (!locationString) return 'N/A';
        try {
            const location = JSON.parse(locationString);
            return location.address || 'N/A';
        } catch {
            return locationString.length > 50
                ? locationString.substring(0, 50) + '...'
                : locationString;
        }
    };

    // Get status badge color
    const getStatusBadgeColor = (status: string) => {
        const statusColors: { [key: string]: string } = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'confirmed': 'bg-blue-100 text-blue-800',
            'in_progress': 'bg-purple-100 text-purple-800',
            'delivered': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800',
            'returned': 'bg-gray-100 text-gray-800',
        };
        return statusColors[status] || 'bg-gray-100 text-gray-800';
    };

    // Handle filter change
    const handleFilterChange = (key: string, value: string) => {
        const newFilters = { ...currentFilters, [key]: value };
        setCurrentFilters(newFilters);

        // Apply filters
        router.get(route('order.index'), newFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle bulk selection
    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            setSelectedOrders(orders.data.map(order => order.id));
        } else {
            setSelectedOrders([]);
        }
    };

    const handleSelectOrder = (orderId: number, checked: boolean) => {
        if (checked) {
            setSelectedOrders([...selectedOrders, orderId]);
        } else {
            setSelectedOrders(selectedOrders.filter(id => id !== orderId));
        }
    };

    // Handle bulk actions
    const handleBulkDelete = () => {
        if (selectedOrders.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedOrders.length} orders. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('order.bulk-delete'), {
                    data: { ids: selectedOrders },
                    onSuccess: () => {
                        setSelectedOrders([]);
                        Swal.fire(
                            'Deleted!',
                            'The selected orders have been deleted.',
                            'success'
                        );
                    }
                });
            }
        });
    };

    // Handle export
    const handleExport = (format: 'csv' | 'pdf') => {
        const exportUrl = format === 'csv'
            ? route('order.export.csv')
            : route('order.export.pdf');

        window.open(`${exportUrl}?${new URLSearchParams(currentFilters).toString()}`);
    };

    return (
        <>
            <Head title={pageTitle} />

            <div className="container-fluid">
                <div className="row">
                    <div className="col-lg-12">
                        <div className="card">
                            <div className="card-header d-flex justify-content-between align-items-center">
                                <h4 className="card-title">{pageTitle}</h4>
                                <div className="d-flex gap-2">
                                    {/* Export Buttons */}
                                    <div className="btn-group">
                                        <button
                                            type="button"
                                            className="btn btn-outline-primary btn-sm dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                        >
                                            Export
                                        </button>
                                        <ul className="dropdown-menu">
                                            <li>
                                                <button
                                                    className="dropdown-item"
                                                    onClick={() => handleExport('csv')}
                                                >
                                                    Export CSV
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    className="dropdown-item"
                                                    onClick={() => handleExport('pdf')}
                                                >
                                                    Export PDF
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    {/* Add New Order Button */}
                                    <Link
                                        href={route('order.create')}
                                        className="btn btn-primary btn-sm"
                                    >
                                        <i className="fas fa-plus me-1"></i>
                                        Add New Order
                                    </Link>
                                </div>
                            </div>

                            <div className="card-body">
                                {/* Filters */}
                                <div className="row mb-4">
                                    <div className="col-md-3">
                                        <label className="form-label">Order Type</label>
                                        <Select
                                            className="basic-single"
                                            classNamePrefix="select"
                                            value={
                                                currentFilters.order_type
                                                ? {
                                                    value: currentFilters.order_type,
                                                    label: currentFilters.order_type.charAt(0).toUpperCase() +
                                                           currentFilters.order_type.slice(1).replace('_', ' ')
                                                  }
                                                : null
                                            }
                                            onChange={(option: any) => handleFilterChange('order_type', option ? option.value : '')}
                                            options={[
                                                { value: '', label: 'All Orders' },
                                                { value: 'pending', label: 'Pending' },
                                                { value: 'confirmed', label: 'Confirmed' },
                                                { value: 'in_progress', label: 'In Progress' },
                                                { value: 'delivered', label: 'Delivered' },
                                                { value: 'cancelled', label: 'Cancelled' }
                                            ]}
                                            isClearable
                                            placeholder="Select Order Type"
                                        />
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Status</label>
                                        <Select
                                            className="basic-single"
                                            classNamePrefix="select"
                                            value={
                                                currentFilters.status
                                                ? {
                                                    value: currentFilters.status,
                                                    label: currentFilters.status.charAt(0).toUpperCase() +
                                                           currentFilters.status.slice(1).replace('_', ' ')
                                                  }
                                                : null
                                            }
                                            onChange={(option: any) => handleFilterChange('status', option ? option.value : '')}
                                            options={[
                                                { value: '', label: 'All Status' },
                                                { value: 'pending', label: 'Pending' },
                                                { value: 'confirmed', label: 'Confirmed' },
                                                { value: 'delivered', label: 'Delivered' },
                                                { value: 'cancelled', label: 'Cancelled' }
                                            ]}
                                            isClearable
                                            placeholder="Select Status"
                                        />
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Date From</label>
                                        <input
                                            type="date"
                                            className="form-control form-control-sm"
                                            value={currentFilters.date_start || ''}
                                            onChange={(e) => handleFilterChange('date_start', e.target.value)}
                                        />
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Date To</label>
                                        <input
                                            type="date"
                                            className="form-control form-control-sm"
                                            value={currentFilters.date_end || ''}
                                            onChange={(e) => handleFilterChange('date_end', e.target.value)}
                                        />
                                    </div>
                                </div>

                                {/* Bulk Actions */}
                                {selectedOrders.length > 0 && (
                                    <div className="alert alert-info d-flex justify-content-between align-items-center">
                                        <span>{selectedOrders.length} orders selected</span>
                                        <div>
                                            <button
                                                className="btn btn-danger btn-sm"
                                                onClick={handleBulkDelete}
                                            >
                                                Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                )}

                                {/* Orders Table */}
                                {loading ? (
                                    <div className="text-center p-5">
                                        <div className="spinner-border text-primary" role="status">
                                            <span className="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="table-responsive">
                                        <table className="table table-hover">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>
                                                        <input
                                                            type="checkbox"
                                                            className="form-check-input"
                                                            checked={selectedOrders.length === orders.data.length}
                                                            onChange={(e) => handleSelectAll(e.target.checked)}
                                                        />
                                                    </th>
                                                    <th>Order ID</th>
                                                    <th>Client</th>
                                                    <th>Pickup Point</th>
                                                    <th>Delivery Point</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {orders && orders.data && orders.data.length > 0 ? (
                                                    orders.data.map((order) => (
                                                        <tr key={order.id}>
                                                            <td>
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={selectedOrders.includes(order.id)}
                                                                    onChange={(e) => handleSelectOrder(order.id, e.target.checked)}
                                                                />
                                                            </td>
                                                            <td>
                                                                <Link
                                                                    href={route('order.show', order.id)}
                                                                    className="text-primary fw-bold"
                                                                >
                                                                    #{order.id}
                                                                </Link>
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <div className="fw-medium">
                                                                        {order.client?.name || 'N/A'}
                                                                    </div>
                                                                    <small className="text-muted">
                                                                        {order.client?.contact_number || ''}
                                                                    </small>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <small>{formatLocation(order.pickup_point)}</small>
                                                            </td>
                                                            <td>
                                                                <small>{formatLocation(order.delivery_point)}</small>
                                                            </td>
                                                            <td>
                                                                <span className={`badge ${getStatusBadgeColor(order.status)}`}>
                                                                    {order.status.replace('_', ' ').toUpperCase()}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span className="fw-medium">
                                                                    ${order.total_amount?.toFixed(2) || '0.00'}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small>{formatDate(order.date || order.created_at)}</small>
                                                            </td>
                                                            <td>
                                                                <div className="btn-group btn-group-sm">
                                                                    <Link
                                                                        href={route('order.show', order.id)}
                                                                        className="btn btn-outline-primary btn-sm"
                                                                        title="View"
                                                                    >
                                                                        <i className="fas fa-eye"></i>
                                                                    </Link>
                                                                    <Link
                                                                        href={route('order.edit', order.id)}
                                                                        className="btn btn-outline-secondary btn-sm"
                                                                        title="Edit"
                                                                    >
                                                                        <i className="fas fa-edit"></i>
                                                                    </Link>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    ))
                                                ) : (
                                                    <tr>
                                                        <td colSpan={9} className="text-center py-4">
                                                            <div className="text-muted">
                                                                <i className="fas fa-inbox fa-2x mb-2"></i>
                                                                <p>No orders found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {/* Pagination */}
                                {orders && orders.links && orders.links.length > 3 && (
                                    <div className="d-flex justify-content-between align-items-center mt-4">
                                        <div className="text-muted">
                                            Showing {((orders.current_page - 1) * orders.per_page) + 1} to{' '}
                                            {Math.min(orders.current_page * orders.per_page, orders.total)} of{' '}
                                            {orders.total} results
                                        </div>
                                        <nav>
                                            <ul className="pagination pagination-sm mb-0">
                                                {orders.links.map((link, i) => (
                                                    <li
                                                        key={i}
                                                        className={`page-item ${
                                                            link.active ? 'active' : ''
                                                        } ${link.url ? '' : 'disabled'}`}
                                                    >
                                                        <Link
                                                            href={link.url || '#'}
                                                            className="page-link"
                                                            dangerouslySetInnerHTML={{
                                                                __html: link.label,
                                                            }}
                                                        />
                                                    </li>
                                                ))}
                                            </ul>
                                        </nav>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

// Set the layout for this page
Index.layout = (page: React.ReactElement) => (
    <AppLayout title={page.props.pageTitle || 'Orders'}>{page}</AppLayout>
);
