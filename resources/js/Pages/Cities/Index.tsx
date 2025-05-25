import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

interface City {
    id: number;
    name: string;
    country?: {
        name: string;
    };
    status: string;
    created_at: string;
    fixed_charges?: number;
    per_distance_charges?: number;
    per_weight_charges?: number;
}

interface CitiesIndexProps {
    pageTitle: string;
    auth_user: any;
    assets: string[];
    cities: {
        data: City[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters?: {
        status?: string;
        country_id?: string;
        search?: string;
    };
}

export default function Index({ pageTitle, auth_user, assets, cities, filters }: CitiesIndexProps) {
    const [loading, setLoading] = useState(false);
    const [selectedCities, setSelectedCities] = useState<number[]>([]);
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
        };
        return statusColors[status] || 'bg-gray-100 text-gray-800';
    };

    // Handle filter change
    const handleFilterChange = (key: string, value: string) => {
        const newFilters = { ...currentFilters, [key]: value };
        setCurrentFilters(newFilters);
        
        // Apply filters
        router.get(route('city.index'), newFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle bulk selection
    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            setSelectedCities(cities.data.map(city => city.id));
        } else {
            setSelectedCities([]);
        }
    };

    const handleSelectCity = (cityId: number, checked: boolean) => {
        if (checked) {
            setSelectedCities([...selectedCities, cityId]);
        } else {
            setSelectedCities(selectedCities.filter(id => id !== cityId));
        }
    };

    // Handle bulk actions
    const handleBulkDelete = () => {
        if (selectedCities.length === 0) return;
        
        if (confirm(`Are you sure you want to delete ${selectedCities.length} cities?`)) {
            router.delete(route('city.bulk-delete'), {
                data: { ids: selectedCities },
                onSuccess: () => {
                    setSelectedCities([]);
                }
            });
        }
    };

    // Handle export
    const handleExport = (format: 'csv' | 'pdf') => {
        const exportUrl = format === 'csv' 
            ? route('city.export.csv') 
            : route('city.export.pdf');
        
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

                                    {/* Add New City Button */}
                                    <Link
                                        href={route('city.create')}
                                        className="btn btn-primary btn-sm"
                                    >
                                        <i className="fas fa-plus me-1"></i>
                                        Add New City
                                    </Link>
                                </div>
                            </div>

                            <div className="card-body">
                                {/* Filters */}
                                <div className="row mb-4">
                                    <div className="col-md-4">
                                        <label className="form-label">Status</label>
                                        <select 
                                            className="form-select form-select-sm"
                                            value={currentFilters.status || ''}
                                            onChange={(e) => handleFilterChange('status', e.target.value)}
                                        >
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div className="col-md-4">
                                        <label className="form-label">Country</label>
                                        <select 
                                            className="form-select form-select-sm"
                                            value={currentFilters.country_id || ''}
                                            onChange={(e) => handleFilterChange('country_id', e.target.value)}
                                        >
                                            <option value="">All Countries</option>
                                            {/* Add country options here */}
                                        </select>
                                    </div>
                                    <div className="col-md-4">
                                        <label className="form-label">Search</label>
                                        <input 
                                            type="text" 
                                            className="form-control form-control-sm"
                                            placeholder="Search by city name..."
                                            value={currentFilters.search || ''}
                                            onChange={(e) => handleFilterChange('search', e.target.value)}
                                        />
                                    </div>
                                </div>

                                {/* Bulk Actions */}
                                {selectedCities.length > 0 && (
                                    <div className="alert alert-info d-flex justify-content-between align-items-center">
                                        <span>{selectedCities.length} cities selected</span>
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

                                {/* Cities Table */}
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
                                                            checked={selectedCities.length === cities.data.length}
                                                            onChange={(e) => handleSelectAll(e.target.checked)}
                                                        />
                                                    </th>
                                                    <th>City Name</th>
                                                    <th>Country</th>
                                                    <th>Status</th>
                                                    <th>Fixed Charges</th>
                                                    <th>Per Distance</th>
                                                    <th>Per Weight</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {cities && cities.data && cities.data.length > 0 ? (
                                                    cities.data.map((city) => (
                                                        <tr key={city.id}>
                                                            <td>
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={selectedCities.includes(city.id)}
                                                                    onChange={(e) => handleSelectCity(city.id, e.target.checked)}
                                                                />
                                                            </td>
                                                            <td>
                                                                <div className="fw-medium">{city.name}</div>
                                                            </td>
                                                            <td>
                                                                <small>{city.country?.name || 'N/A'}</small>
                                                            </td>
                                                            <td>
                                                                <span className={`badge ${getStatusBadgeColor(city.status)}`}>
                                                                    {city.status.toUpperCase()}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span className="fw-medium">
                                                                    ${city.fixed_charges?.toFixed(2) || '0.00'}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span className="fw-medium">
                                                                    ${city.per_distance_charges?.toFixed(2) || '0.00'}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span className="fw-medium">
                                                                    ${city.per_weight_charges?.toFixed(2) || '0.00'}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small>{formatDate(city.created_at)}</small>
                                                            </td>
                                                            <td>
                                                                <div className="btn-group btn-group-sm">
                                                                    <Link
                                                                        href={route('city.show', city.id)}
                                                                        className="btn btn-outline-primary btn-sm"
                                                                        title="View"
                                                                    >
                                                                        <i className="fas fa-eye"></i>
                                                                    </Link>
                                                                    <Link
                                                                        href={route('city.edit', city.id)}
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
                                                                <i className="fas fa-city fa-2x mb-2"></i>
                                                                <p>No cities found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {/* Pagination */}
                                {cities && cities.links && cities.links.length > 3 && (
                                    <div className="d-flex justify-content-between align-items-center mt-4">
                                        <div className="text-muted">
                                            Showing {((cities.current_page - 1) * cities.per_page) + 1} to{' '}
                                            {Math.min(cities.current_page * cities.per_page, cities.total)} of{' '}
                                            {cities.total} results
                                        </div>
                                        <nav>
                                            <ul className="pagination pagination-sm mb-0">
                                                {cities.links.map((link, i) => (
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
    <AppLayout title={page.props.pageTitle || 'Cities'}>{page}</AppLayout>
);
