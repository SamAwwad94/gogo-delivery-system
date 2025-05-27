import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import Select from 'react-select';
import Swal from 'sweetalert2';

export default function Driver({ pageTitle, auth_user, assets, drivers, filters }) {
    const [loading, setLoading] = useState(false);
    const [selectedDrivers, setSelectedDrivers] = useState([]);
    const [currentFilters, setCurrentFilters] = useState(filters || {});
    const [darkMode, setDarkMode] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

    // State for dropdowns
    const [notificationDropdownOpen, setNotificationDropdownOpen] = useState(false);
    const [languageDropdownOpen, setLanguageDropdownOpen] = useState(false);
    const [userDropdownOpen, setUserDropdownOpen] = useState(false);
    const [sidebarMenuOpen, setSidebarMenuOpen] = useState(true);
    const [exportDropdownOpen, setExportDropdownOpen] = useState(false);

    // Format date helper
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    // Get status badge color
    const getStatusBadgeColor = (status) => {
        const statusColors = {
            'active': 'bg-green-100 text-green-800',
            'inactive': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800',
        };
        return statusColors[status] || 'bg-gray-100 text-gray-800';
    };

    // Handle filter change
    const handleFilterChange = (key, value) => {
        const newFilters = { ...currentFilters, [key]: value };
        setCurrentFilters(newFilters);

        // Apply filters
        router.get(route('deliveryman.index'), newFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Handle bulk selection
    const handleSelectAll = (checked) => {
        if (checked) {
            setSelectedDrivers(drivers.data.map(driver => driver.id));
        } else {
            setSelectedDrivers([]);
        }
    };

    const handleSelectDriver = (driverId, checked) => {
        if (checked) {
            setSelectedDrivers([...selectedDrivers, driverId]);
        } else {
            setSelectedDrivers(selectedDrivers.filter(id => id !== driverId));
        }
    };

    // Handle bulk actions
    const handleBulkDelete = () => {
        if (selectedDrivers.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedDrivers.length} drivers. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('deliveryman.bulk-delete'), {
                    data: { ids: selectedDrivers },
                    onSuccess: () => {
                        setSelectedDrivers([]);
                        Swal.fire(
                            'Deleted!',
                            'The selected drivers have been deleted.',
                            'success'
                        );
                    }
                });
            }
        });
    };

    // Handle export
    const handleExport = (format) => {
        const exportUrl = format === 'csv'
            ? route('deliveryman.export.csv')
            : route('deliveryman.export.pdf');

        window.open(`${exportUrl}?${new URLSearchParams(currentFilters).toString()}`);
    };

    // Toggle dark mode
    const toggleDarkMode = () => {
        const newDarkMode = !darkMode;
        setDarkMode(newDarkMode);

        // Use React way to manage body class
        if (newDarkMode) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    };

    // Toggle sidebar
    const toggleSidebar = () => {
        const newSidebarState = !sidebarCollapsed;
        setSidebarCollapsed(newSidebarState);

        // Use React way to manage body class
        if (newSidebarState) {
            document.body.classList.add('sidebar-main');
        } else {
            document.body.classList.remove('sidebar-main');
        }
    };

    // Set initial state on component mount
    useEffect(() => {
        // Check if dark mode is already enabled
        if (document.body.classList.contains('dark-mode')) {
            setDarkMode(true);
        }

        // Check if sidebar is already collapsed
        if (document.body.classList.contains('sidebar-main')) {
            setSidebarCollapsed(true);
        }

        // Add click handler to close dropdowns when clicking outside
        const handleClickOutside = (event) => {
            // Close dropdowns if clicking outside of them
            if (!event.target.closest('.dropdown-toggle') &&
                !event.target.closest('.dropdown-menu')) {
                setNotificationDropdownOpen(false);
                setLanguageDropdownOpen(false);
                setUserDropdownOpen(false);
                setExportDropdownOpen(false);
            }
        };

        document.addEventListener('click', handleClickOutside);

        // Cleanup on unmount
        return () => {
            document.removeEventListener('click', handleClickOutside);
        };
    }, []);

    return (
        <>
            <Head title={pageTitle} />

            <div id="loading">
                <div id="loading-center"></div>
            </div>

            {/* Header */}
            <div className="mm-top-navbar">
                <div className="mm-navbar-custom">
                    <nav className="navbar navbar-expand-lg navbar-light p-0">
                        <div className="mm-navbar-logo d-flex align-items-center justify-content-between">
                            <i className="fas fa-bars wrapper-menu" onClick={toggleSidebar}></i>
                            <a href="/" className="header-logo">
                                <img src={assets?.site_logo || "/images/logo.png"}
                                    className="img-fluid mode light-img rounded-normal site_logo_preview" alt="logo" />
                                <img src={assets?.site_dark_logo || "/images/logo-dark.png"}
                                    className="img-fluid mode dark-img rounded-normal darkmode-logo site_dark_logo_preview" alt="dark-logo" />
                            </a>
                        </div>
                        <div className="mm-search-bar device-search m-auto">
                            {/* Search bar can be added here if needed */}
                        </div>
                        <div className="d-flex align-items-center">
                            <div className="change-mode">
                                <div className="custom-control custom-switch custom-switch-icon custom-control-inline">
                                    <div className="custom-switch-inner">
                                        <p className="mb-0"> </p>
                                        <input type="checkbox" className="custom-control-input" id="dark-mode"
                                            checked={darkMode} onChange={toggleDarkMode} data-active="true" />
                                        <label className="custom-control-label" htmlFor="dark-mode" data-mode="toggle">
                                            <span className="switch-icon-left">
                                                <svg className="svg-icon" id="h-moon" height="20" width="20"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                </svg>
                                            </span>
                                            <span className="switch-icon-right">
                                                <svg className="svg-icon" id="h-sun" height="20" width="20"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div className="navbar-nav-container" id="navbarSupportedContent">
                                <ul className="navbar-nav ml-auto navbar-list align-items-center">
                                    {/* Notification Bell */}
                                    <li className="nav-item nav-icon dropdown">
                                        <a href="#"
                                           className="search-toggle dropdown-toggle notification_list"
                                           onClick={(e) => {
                                               e.preventDefault();
                                               setNotificationDropdownOpen(!notificationDropdownOpen);
                                               // Close other dropdowns
                                               setLanguageDropdownOpen(false);
                                               setUserDropdownOpen(false);
                                           }}
                                        >
                                            <svg className="svg-icon text-primary" id="mm-bell-2" xmlns="http://www.w3.org/2000/svg"
                                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                            </svg>
                                            <span className="bg-primary"></span>
                                        </a>
                                        {notificationDropdownOpen && (
                                            <div className="mm-sub-dropdown dropdown-menu notification-menu show">
                                                <div className="card shadow-none m-0 border-0">
                                                    <div className="card-body p-0 notification_data">
                                                        {/* Notification content would go here */}
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </li>

                                    {/* Language Dropdown */}
                                    <li className="nav-item nav-icon dropdown">
                                        <a href="#"
                                           className="search-toggle dropdown-toggle"
                                           onClick={(e) => {
                                               e.preventDefault();
                                               setLanguageDropdownOpen(!languageDropdownOpen);
                                               // Close other dropdowns
                                               setNotificationDropdownOpen(false);
                                               setUserDropdownOpen(false);
                                           }}
                                        >
                                            <svg className="svg-icon text-primary" id="mm-globe-icon" xmlns="http://www.w3.org/2000/svg"
                                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                                <path
                                                    d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                                </path>
                                            </svg>
                                            <span className="bg-primary"></span>
                                        </a>
                                        {languageDropdownOpen && (
                                            <div className="mm-sub-dropdown dropdown-menu language-menu show">
                                                <div className="card shadow-none m-0 border-0">
                                                    <div className="p-0">
                                                        <ul className="dropdown-menu-1 list-group list-group-flush">
                                                            <li className="dropdown-item-1 list-group-item px-2">
                                                                <a className="p-0" href="/change-language/en">
                                                                    <img src="/images/flag/en.png" alt="img-flag-en"
                                                                        className="img-fluid mr-2 selected-lang-list" />
                                                                    English
                                                                </a>
                                                            </li>
                                                            <li className="dropdown-item-1 list-group-item px-2">
                                                                <a className="p-0" href="/change-language/ar">
                                                                    <img src="/images/flag/ar.png" alt="img-flag-ar"
                                                                        className="img-fluid mr-2 selected-lang-list" />
                                                                    Arabic
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </li>

                                    {/* Fullscreen Button */}
                                    <li className="nav-item nav-icon dropdown full-screen">
                                        <a href="#" className="nav-item nav-icon dropdown" id="btnFullscreen">
                                            <i className="max">
                                                <svg className="svg-icon text-primary" id="d-3-max" width="20" height="20"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" strokeWidth="2" strokeLinecap="round"
                                                    strokeLinejoin="round">
                                                    <path
                                                        d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                                                    </path>
                                                </svg>
                                            </i>
                                            <i className="min d-none">
                                                <svg className="svg-icon text-primary" id="d-3-min" width="20"
                                                    height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" strokeWidth="2" strokeLinecap="round"
                                                    strokeLinejoin="round">
                                                    <path
                                                        d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3">
                                                    </path>
                                                </svg>
                                            </i>
                                        </a>
                                    </li>

                                    {/* User Profile Dropdown */}
                                    <li className="nav-item nav-icon dropdown">
                                        <a href="#"
                                           className="nav-item nav-icon dropdown-toggle pr-0 search-toggle"
                                           onClick={(e) => {
                                               e.preventDefault();
                                               setUserDropdownOpen(!userDropdownOpen);
                                               // Close other dropdowns
                                               setNotificationDropdownOpen(false);
                                               setLanguageDropdownOpen(false);
                                           }}
                                        >
                                            <img src={auth_user?.profile_image || "/images/user/user.png"}
                                                className="img-fluid avatar-rounded" alt="user" />
                                        </a>
                                        {userDropdownOpen && (
                                            <ul className="dropdown-menu dropdown-menu-right show">
                                                <li className="dropdown-item d-flex">
                                                    <svg className="svg-icon mr-0 text-primary" id="h-01-p" width="20"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <a href="/setting?page=profile_form">My Profile</a>
                                                </li>
                                                <li className="dropdown-item d-flex">
                                                    <svg className="svg-icon mr-0 text-primary" id="h-03-p" width="20"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <a href="/setting">Settings</a>
                                                </li>
                                                <li className="dropdown-item d-flex border-top">
                                                    <svg className="svg-icon mr-0 text-primary" id="h-05-p" width="20"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                    <a href="/logout" className="pl-1">Logout</a>
                                                </li>
                                            </ul>
                                        )}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            {/* Sidebar */}
            <div className={`mm-sidebar sidebar-default ${sidebarCollapsed ? 'sidebar-collapsed' : ''}`}>
                <div className="mm-sidebar-logo d-flex align-items-center justify-content-between">
                    <a href="/" className="header-logo">
                        <img src={assets?.site_logo || "/images/logo.png"}
                            className="img-fluid mode light-img rounded-normal light-logo site_logo_preview" alt="logo" />
                        <img src={assets?.site_dark_logo || "/images/logo-dark.png"}
                            className="img-fluid mode dark-img rounded-normal darkmode-logo site_dark_logo_preview" alt="dark-logo" />
                    </a>
                    <div className="side-menu-bt-sidebar" onClick={toggleSidebar}>
                        <i className="fas fa-bars wrapper-menu"></i>
                    </div>
                </div>

                <div className="data-scrollbar" data-scroll="1">
                    <nav className="mm-sidebar-menu">
                        <ul id="mm-sidebar-toggle" className="side-menu">
                            {/* Sidebar menu items would be dynamically generated here */}
                            <li className="sidebar-layout">
                                <a href="/home" className="svg-icon">
                                    <i className="fas fa-home"></i>
                                    <span className="ml-2">Dashboard</span>
                                </a>
                            </li>
                            <li className="sidebar-layout active">
                                <a href="#"
                                   className="svg-icon"
                                   onClick={(e) => {
                                       e.preventDefault();
                                       setSidebarMenuOpen(!sidebarMenuOpen);
                                   }}
                                >
                                    <i className="fa fa-user-tie"></i>
                                    <span className="ml-2">Delivery Man</span>
                                    <i className={`fas ${sidebarMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right'} arrow-right`}></i>
                                </a>
                                <ul className={`submenu ${sidebarMenuOpen ? 'collapse show' : 'collapse'}`} id="delivery_man">
                                    <li className="sidebar-layout">
                                        <a href="/deliveryman/create" className="svg-icon">
                                            <i className="fas fa-plus-square"></i>
                                            <span className="ml-2">Add Delivery Man</span>
                                        </a>
                                    </li>
                                    <li className="sidebar-layout active">
                                        <a href="/deliveryman" className="svg-icon">
                                            <i className="fas fa-list"></i>
                                            <span className="ml-2">List Delivery Men</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            {/* More sidebar items would go here */}
                        </ul>
                    </nav>
                    <div className="pt-5 pb-5"></div>
                </div>
            </div>

            {/* Main Content */}
            <div className="content-page">
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
                                                onClick={() => setExportDropdownOpen(!exportDropdownOpen)}
                                            >
                                                Export
                                            </button>
                                            {exportDropdownOpen && (
                                                <ul className="dropdown-menu show">
                                                    <li>
                                                        <button
                                                            className="dropdown-item"
                                                            onClick={() => {
                                                                handleExport('csv');
                                                                setExportDropdownOpen(false);
                                                            }}
                                                        >
                                                            Export CSV
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            className="dropdown-item"
                                                            onClick={() => {
                                                                handleExport('pdf');
                                                                setExportDropdownOpen(false);
                                                            }}
                                                        >
                                                            Export PDF
                                                        </button>
                                                    </li>
                                                </ul>
                                            )}
                                        </div>

                                        {/* Add New Driver Button */}
                                        <Link
                                            href={route('deliveryman.create')}
                                            className="btn btn-primary btn-sm"
                                        >
                                            <i className="fas fa-plus me-1"></i>
                                            Add New Driver
                                        </Link>
                                    </div>
                                </div>

                                <div className="card-body">
                                    {/* Filters */}
                                    <div className="row mb-4">
                                        <div className="col-md-3">
                                            <label className="form-label">Status</label>
                                            <Select
                                                className="basic-single"
                                                classNamePrefix="select"
                                                value={
                                                    currentFilters.status
                                                    ? {
                                                        value: currentFilters.status,
                                                        label: currentFilters.status.charAt(0).toUpperCase() + currentFilters.status.slice(1)
                                                      }
                                                    : null
                                                }
                                                onChange={(option) => handleFilterChange('status', option ? option.value : '')}
                                                options={[
                                                    { value: '', label: 'All Status' },
                                                    { value: 'active', label: 'Active' },
                                                    { value: 'inactive', label: 'Inactive' },
                                                    { value: 'pending', label: 'Pending' }
                                                ]}
                                                isClearable
                                                placeholder="Select Status"
                                            />
                                        </div>
                                        <div className="col-md-3">
                                            <label className="form-label">Country</label>
                                            <Select
                                                className="basic-single"
                                                classNamePrefix="select"
                                                value={
                                                    currentFilters.country_id
                                                    ? { value: currentFilters.country_id, label: 'Selected Country' }
                                                    : null
                                                }
                                                onChange={(option) => handleFilterChange('country_id', option ? option.value : '')}
                                                options={[
                                                    { value: '', label: 'All Countries' }
                                                    // We would normally fetch countries from the API and map them here
                                                ]}
                                                isClearable
                                                placeholder="Select Country"
                                            />
                                        </div>
                                        <div className="col-md-3">
                                            <label className="form-label">City</label>
                                            <Select
                                                className="basic-single"
                                                classNamePrefix="select"
                                                value={
                                                    currentFilters.city_id
                                                    ? { value: currentFilters.city_id, label: 'Selected City' }
                                                    : null
                                                }
                                                onChange={(option) => handleFilterChange('city_id', option ? option.value : '')}
                                                options={[
                                                    { value: '', label: 'All Cities' }
                                                    // We would normally fetch cities from the API and map them here
                                                ]}
                                                isClearable
                                                placeholder="Select City"
                                            />
                                        </div>
                                        <div className="col-md-3">
                                            <label className="form-label">Search</label>
                                            <input
                                                type="text"
                                                className="form-control form-control-sm"
                                                placeholder="Search by name, email..."
                                                value={currentFilters.search || ''}
                                                onChange={(e) => handleFilterChange('search', e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    {/* Bulk Actions */}
                                    {selectedDrivers.length > 0 && (
                                        <div className="alert alert-info d-flex justify-content-between align-items-center">
                                            <span>{selectedDrivers.length} drivers selected</span>
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

                                    {/* Drivers Table */}
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
                                                                checked={selectedDrivers.length === drivers?.data?.length}
                                                                onChange={(e) => handleSelectAll(e.target.checked)}
                                                            />
                                                        </th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>City</th>
                                                        <th>Country</th>
                                                        <th>Contact Number</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Last Active</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {drivers && drivers.data && drivers.data.length > 0 ? (
                                                        drivers.data.map((driver) => (
                                                            <tr key={driver.id}>
                                                                <td>
                                                                    <input
                                                                        type="checkbox"
                                                                        className="form-check-input"
                                                                        checked={selectedDrivers.includes(driver.id)}
                                                                        onChange={(e) => handleSelectDriver(driver.id, e.target.checked)}
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <div className="fw-medium">
                                                                        <Link href={route('deliveryman.show', driver.id)} className="text-primary">
                                                                            {driver.name}
                                                                        </Link>
                                                                    </div>
                                                                </td>
                                                                <td>{driver.email}</td>
                                                                <td>{driver.city?.name || 'N/A'}</td>
                                                                <td>{driver.country?.name || 'N/A'}</td>
                                                                <td>{driver.contact_number}</td>
                                                                <td>
                                                                    <span className={`badge ${getStatusBadgeColor(driver.status)}`}>
                                                                        {driver.status?.toUpperCase()}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <small>{formatDate(driver.created_at)}</small>
                                                                </td>
                                                                <td>
                                                                    <small>{formatDate(driver.last_actived_at)}</small>
                                                                </td>
                                                                <td>
                                                                    <div className="btn-group btn-group-sm">
                                                                        <Link
                                                                            href={route('deliveryman.show', driver.id)}
                                                                            className="btn btn-outline-primary btn-sm"
                                                                            title="View"
                                                                        >
                                                                            <i className="fas fa-eye"></i>
                                                                        </Link>
                                                                        <Link
                                                                            href={route('deliveryman.edit', driver.id)}
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
                                                            <td colSpan={10} className="text-center py-4">
                                                                <div className="text-muted">
                                                                    <i className="fas fa-user-tie fa-2x mb-2"></i>
                                                                    <p>No drivers found</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    )}

                                    {/* Pagination */}
                                    {drivers && drivers.links && drivers.links.length > 3 && (
                                        <div className="d-flex justify-content-between align-items-center mt-4">
                                            <div className="text-muted">
                                                Showing {((drivers.current_page - 1) * drivers.per_page) + 1} to{' '}
                                                {Math.min(drivers.current_page * drivers.per_page, drivers.total)} of{' '}
                                                {drivers.total} results
                                            </div>
                                            <nav>
                                                <ul className="pagination pagination-sm mb-0">
                                                    {drivers.links.map((link, i) => (
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

            {/* Footer */}
            <footer className="mm-footer">
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-lg-6">
                            <ul className="list-inline mb-0">
                                <li className="list-inline-item"><a href="/privacy-policy">Privacy Policy</a></li>
                                <li className="list-inline-item"><a href="/term-condition">Terms of Use</a></li>
                            </ul>
                        </div>
                        <div className="col-lg-6 text-right">
                            <span className="mr-1">
                                {new Date().getFullYear()} © Gogo Delivery
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </>
    );
}
