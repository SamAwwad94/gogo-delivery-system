import React, { ReactNode } from "react";
import { Link } from "@inertiajs/react";

interface AppLayoutProps {
    children: ReactNode;
    title?: string;
}

const AppLayout: React.FC<AppLayoutProps> = ({ children, title }) => {
    return (
        <div className="min-h-screen bg-gray-100">
            {/* Navigation */}
            <nav className="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <h1 className="text-xl font-bold text-gray-900">
                                    Gogo Delivery Admin
                                </h1>
                            </div>
                        </div>
                        <div className="flex items-center space-x-4">
                            <span className="text-sm text-gray-500">
                                React + TypeScript + Inertia.js
                            </span>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Sidebar */}
            <aside className="fixed left-0 top-16 z-30 hidden h-[calc(100vh-4rem)] w-64 border-r border-gray-200 bg-white lg:block">
                <div className="h-full overflow-y-auto py-4 px-3">
                    <nav className="space-y-1">
                        {/* Dashboard */}
                        <Link
                            href="/home"
                            className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            <i className="fas fa-tachometer-alt w-5 h-5"></i>
                            <span>Dashboard</span>
                        </Link>

                        {/* Orders */}
                        <div className="space-y-1">
                            <div className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50">
                                <i className="fas fa-file-alt w-5 h-5"></i>
                                <span>Orders</span>
                            </div>
                            <div className="pl-6 space-y-1">
                                <Link
                                    href="/order"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-list w-4 h-4"></i>
                                    <span>All Orders</span>
                                </Link>
                                <Link
                                    href="/order?classic=1"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-table w-4 h-4"></i>
                                    <span>Classic Orders</span>
                                </Link>
                            </div>
                        </div>

                        {/* Users */}
                        <div className="space-y-1">
                            <div className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50">
                                <i className="fas fa-users w-5 h-5"></i>
                                <span>Users</span>
                            </div>
                            <div className="pl-6 space-y-1">
                                <Link
                                    href="/users"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-user-tie w-4 h-4"></i>
                                    <span>All Users</span>
                                </Link>
                                <Link
                                    href="/users?classic=1"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-table w-4 h-4"></i>
                                    <span>Classic Users</span>
                                </Link>
                            </div>
                        </div>

                        {/* Cities */}
                        <div className="space-y-1">
                            <div className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50">
                                <i className="fas fa-city w-5 h-5"></i>
                                <span>Cities</span>
                            </div>
                            <div className="pl-6 space-y-1">
                                <Link
                                    href="/city"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-list w-4 h-4"></i>
                                    <span>All Cities</span>
                                </Link>
                                <Link
                                    href="/city?classic=1"
                                    className="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <i className="fas fa-table w-4 h-4"></i>
                                    <span>Classic Cities</span>
                                </Link>
                            </div>
                        </div>
                    </nav>
                </div>
            </aside>

            {/* Page Content */}
            <main className="pt-16 lg:pl-64">
                <div className="p-6">{children}</div>
            </main>
        </div>
    );
};

export default AppLayout;
