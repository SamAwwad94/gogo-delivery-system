import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

interface User {
    id: number;
    name: string;
    email: string;
    contact_number?: string;
    user_type: string;
    status: string;
    is_verified: boolean;
    created_at: string;
    profile_image?: string;
}

interface UsersIndexProps {
    pageTitle: string;
    auth_user: any;
    assets: string[];
    users: {
        data: User[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters?: {
        user_type?: string;
        status?: string;
        is_verified?: string;
        search?: string;
    };
}

export default function Index({ pageTitle, auth_user, assets, users, filters }: UsersIndexProps) {
    const [loading, setLoading] = useState(false);
    const [selectedUsers, setSelectedUsers] = useState<number[]>([]);
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

    // Get status badge color
    const getStatusBadgeColor = (status: string) => {
        const statusColors: { [key: string]: string } = {
            'active': 'bg-green-100 text-green-800',
            'inactive': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'banned': 'bg-gray-100 text-gray-800',
        };
        return statusColors[status] || 'bg-gray-100 text-gray-800';
    };

    // Get user type badge color
    const getUserTypeBadgeColor = (userType: string) => {
        const typeColors: { [key: string]: string } = {
            'admin': 'bg-purple-100 text-purple-800',
            'client': 'bg-blue-100 text-blue-800',
            'delivery_man': 'bg-orange-100 text-orange-800',
        };
        return typeColors[userType] || 'bg-gray-100 text-gray-800';
    };

    // Handle filter change
    const handleFilterChange = (key: string, value: string) => {
        const newFilters = { ...currentFilters, [key]: value };
        setCurrentFilters(newFilters);
        
        // Apply filters
        router.get(route('users.index'), newFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle bulk selection
    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            setSelectedUsers(users.data.map(user => user.id));
        } else {
            setSelectedUsers([]);
        }
    };

    const handleSelectUser = (userId: number, checked: boolean) => {
        if (checked) {
            setSelectedUsers([...selectedUsers, userId]);
        } else {
            setSelectedUsers(selectedUsers.filter(id => id !== userId));
        }
    };

    // Handle bulk actions
    const handleBulkDelete = () => {
        if (selectedUsers.length === 0) return;
        
        if (confirm(`Are you sure you want to delete ${selectedUsers.length} users?`)) {
            router.delete(route('users.bulk-delete'), {
                data: { ids: selectedUsers },
                onSuccess: () => {
                    setSelectedUsers([]);
                }
            });
        }
    };

    // Handle export
    const handleExport = (format: 'csv' | 'pdf') => {
        const exportUrl = format === 'csv' 
            ? route('users.export.csv') 
            : route('users.export.pdf');
        
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

                                    {/* Add New User Button */}
                                    <Link
                                        href={route('users.create')}
                                        className="btn btn-primary btn-sm"
                                    >
                                        <i className="fas fa-plus me-1"></i>
                                        Add New User
                                    </Link>
                                </div>
                            </div>

                            <div className="card-body">
                                {/* Filters */}
                                <div className="row mb-4">
                                    <div className="col-md-3">
                                        <label className="form-label">User Type</label>
                                        <select 
                                            className="form-select form-select-sm"
                                            value={currentFilters.user_type || ''}
                                            onChange={(e) => handleFilterChange('user_type', e.target.value)}
                                        >
                                            <option value="">All Types</option>
                                            <option value="admin">Admin</option>
                                            <option value="client">Client</option>
                                            <option value="delivery_man">Delivery Man</option>
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Status</label>
                                        <select 
                                            className="form-select form-select-sm"
                                            value={currentFilters.status || ''}
                                            onChange={(e) => handleFilterChange('status', e.target.value)}
                                        >
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="pending">Pending</option>
                                            <option value="banned">Banned</option>
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Verification</label>
                                        <select 
                                            className="form-select form-select-sm"
                                            value={currentFilters.is_verified || ''}
                                            onChange={(e) => handleFilterChange('is_verified', e.target.value)}
                                        >
                                            <option value="">All</option>
                                            <option value="1">Verified</option>
                                            <option value="0">Not Verified</option>
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">Search</label>
                                        <input 
                                            type="text" 
                                            className="form-control form-control-sm"
                                            placeholder="Search by name or email..."
                                            value={currentFilters.search || ''}
                                            onChange={(e) => handleFilterChange('search', e.target.value)}
                                        />
                                    </div>
                                </div>

                                {/* Bulk Actions */}
                                {selectedUsers.length > 0 && (
                                    <div className="alert alert-info d-flex justify-content-between align-items-center">
                                        <span>{selectedUsers.length} users selected</span>
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

                                {/* Users Table */}
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
                                                            checked={selectedUsers.length === users.data.length}
                                                            onChange={(e) => handleSelectAll(e.target.checked)}
                                                        />
                                                    </th>
                                                    <th>User</th>
                                                    <th>Contact</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Verified</th>
                                                    <th>Joined</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {users && users.data && users.data.length > 0 ? (
                                                    users.data.map((user) => (
                                                        <tr key={user.id}>
                                                            <td>
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={selectedUsers.includes(user.id)}
                                                                    onChange={(e) => handleSelectUser(user.id, e.target.checked)}
                                                                />
                                                            </td>
                                                            <td>
                                                                <div className="d-flex align-items-center">
                                                                    <div className="avatar avatar-sm me-2">
                                                                        {user.profile_image ? (
                                                                            <img 
                                                                                src={user.profile_image} 
                                                                                alt={user.name}
                                                                                className="rounded-circle"
                                                                                style={{ width: '32px', height: '32px' }}
                                                                            />
                                                                        ) : (
                                                                            <div className="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                                                 style={{ width: '32px', height: '32px', fontSize: '12px' }}>
                                                                                {user.name.charAt(0).toUpperCase()}
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                    <div>
                                                                        <div className="fw-medium">{user.name}</div>
                                                                        <small className="text-muted">{user.email}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <small>{user.contact_number || 'N/A'}</small>
                                                            </td>
                                                            <td>
                                                                <span className={`badge ${getUserTypeBadgeColor(user.user_type)}`}>
                                                                    {user.user_type.replace('_', ' ').toUpperCase()}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span className={`badge ${getStatusBadgeColor(user.status)}`}>
                                                                    {user.status.toUpperCase()}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {user.is_verified ? (
                                                                    <span className="badge bg-success">
                                                                        <i className="fas fa-check me-1"></i>
                                                                        Verified
                                                                    </span>
                                                                ) : (
                                                                    <span className="badge bg-warning">
                                                                        <i className="fas fa-clock me-1"></i>
                                                                        Pending
                                                                    </span>
                                                                )}
                                                            </td>
                                                            <td>
                                                                <small>{formatDate(user.created_at)}</small>
                                                            </td>
                                                            <td>
                                                                <div className="btn-group btn-group-sm">
                                                                    <Link
                                                                        href={route('users.show', user.id)}
                                                                        className="btn btn-outline-primary btn-sm"
                                                                        title="View"
                                                                    >
                                                                        <i className="fas fa-eye"></i>
                                                                    </Link>
                                                                    <Link
                                                                        href={route('users.edit', user.id)}
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
                                                        <td colSpan={8} className="text-center py-4">
                                                            <div className="text-muted">
                                                                <i className="fas fa-users fa-2x mb-2"></i>
                                                                <p>No users found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {/* Pagination */}
                                {users && users.links && users.links.length > 3 && (
                                    <div className="d-flex justify-content-between align-items-center mt-4">
                                        <div className="text-muted">
                                            Showing {((users.current_page - 1) * users.per_page) + 1} to{' '}
                                            {Math.min(users.current_page * users.per_page, users.total)} of{' '}
                                            {users.total} results
                                        </div>
                                        <nav>
                                            <ul className="pagination pagination-sm mb-0">
                                                {users.links.map((link, i) => (
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
    <AppLayout title={page.props.pageTitle || 'Users'}>{page}</AppLayout>
);
