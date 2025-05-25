import React from 'react';

interface DashboardProps {
    title: string;
}

const Dashboard: React.FC<DashboardProps> = ({ title }) => {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h1 className="text-2xl font-bold text-gray-900 mb-4">
                                {title || 'Dashboard'}
                            </h1>
                            <p className="text-gray-600">
                                Welcome to your React + TypeScript dashboard powered by Inertia.js!
                            </p>
                            <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div className="bg-blue-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold text-blue-900">Orders</h3>
                                    <p className="text-blue-700">Manage your delivery orders</p>
                                </div>
                                <div className="bg-green-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold text-green-900">Drivers</h3>
                                    <p className="text-green-700">Track delivery personnel</p>
                                </div>
                                <div className="bg-purple-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold text-purple-900">Analytics</h3>
                                    <p className="text-purple-700">View performance metrics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
