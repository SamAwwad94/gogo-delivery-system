import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import Select from 'react-select';
import Swal from 'sweetalert2';

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

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedCities.length} cities. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('city.bulk-delete'), {
                    data: { ids: selectedCities },
                    onSuccess: () => {
                        setSelectedCities([]);
                        Swal.fire(
                            'Deleted!',
                            'The selected cities have been deleted.',
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
            ? route('city.export.csv')
            : route('city.export.pdf');

        window.open(`${exportUrl}?${new URLSearchParams(currentFilters).toString()}`);
    };

    return (
        <div id="app">
            <Head title={pageTitle} />

            {/* Top Navbar */}
            <div className="mm-top-navbar">
                <div className="mm-navbar-custom">
                    <nav className="navbar navbar-expand-lg navbar-light p-0">
                        <div className="mm-navbar-logo d-flex align-items-center justify-content-between">
                            <i className="fas fa-bars wrapper-menu"></i>
                            <a href="/" className="header-logo">
                                <img src="/images/logo.png" className="img-fluid mode light-img rounded-normal site_logo_preview" alt="logo" />
                                <img src="/images/logo-dark.png" className="img-fluid mode dark-img rounded-normal darkmode-logo site_dark_logo_preview" alt="dark-logo" />
                            </a>
                        </div>
                        <div className="mm-search-bar device-search m-auto"></div>
                        <div className="d-flex align-items-center">
                            <div className="change-mode">
                                <div className="custom-control custom-switch custom-switch-icon custom-control-inline">
                                    <div className="custom-switch-inner">
                                        <p className="mb-0"> </p>
                                        <input type="checkbox" className="custom-control-input" id="dark-mode" data-active="true" />
                                        <label className="custom-control-label" htmlFor="dark-mode" data-mode="toggle">
                                            <span className="switch-icon-left">
                                                <svg className="svg-icon" id="h-moon" height="20" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                </svg>
                                            </span>
                                            <span className="switch-icon-right">
                                                <svg className="svg-icon" id="h-sun" height="20" width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div className="navbar-nav-container" id="navbarSupportedContent">
                                <ul className="navbar-nav ml-auto navbar-list align-items-center">
                                    <li className="nav-item nav-icon dropdown">
                                        <a href="#" className="nav-item nav-icon dropdown" id="btnFullscreen">
                                            <i className="max">
                                                <svg className="svg-icon text-primary" id="d-3-max" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                                                </svg>
                                            </i>
                                        </a>
                                    </li>
                                    <li className="nav-item nav-icon dropdown">
                                        <a href="#" className="nav-item nav-icon dropdown-toggle pr-0 search-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <img src="/images/user/user.png" className="img-fluid avatar-rounded" alt="user" />
                                        </a>
                                        <ul className="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <li className="dropdown-item d-flex">
                                                <a href="/setting?page=profile_form">My Profile</a>
                                            </li>
                                            <li className="dropdown-item d-flex">
                                                <a href="/setting">Settings</a>
                                            </li>
                                            <li className="dropdown-item d-flex border-top">
                                                <a href="/logout" className="pl-1">Logout</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

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
                                        <Select
                                            className="basic-single"
                                            classNamePrefix="select"
                                            value={
                                                currentFilters.status
                                                ? {
                                                    value: currentFilters.status,
                                                    label: currentFilters.status === 'active' ? 'Active' : 'Inactive'
                                                  }
                                                : null
                                            }
                                            onChange={(option: any) => handleFilterChange('status', option ? option.value : '')}
                                            options={[
                                                { value: '', label: 'All Status' },
                                                { value: 'active', label: 'Active' },
                                                { value: 'inactive', label: 'Inactive' }
                                            ]}
                                            isClearable
                                            placeholder="Select Status"
                                        />
                                    </div>
                                    <div className="col-md-4">
                                        <label className="form-label">Country</label>
                                        <Select
                                            className="basic-single"
                                            classNamePrefix="select"
                                            value={
                                                currentFilters.country_id
                                                ? { value: currentFilters.country_id, label: 'Selected Country' }
                                                : null
                                            }
                                            onChange={(option: any) => handleFilterChange('country_id', option ? option.value : '')}
                                            options={[
                                                { value: '', label: 'All Countries' },
                                                // We would normally fetch countries from the API and map them here
                                                // For now, we'll just have the "All Countries" option
                                            ]}
                                            isClearable
                                            placeholder="Select Country"
                                        />
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
        </div>
    );
}

// No layout wrapper needed as we're including the layout directly in the component
