import React from "react";
// import { Button } from '@/Components/ui/button'; // Example
// import { Input } from '@/Components/ui/input'; // Example

// This component would receive the list of orders and render them,
// possibly using a table structure built with ShadCN components or a library like TanStack Table.
export default function OrderTable({ orders, authUser, filters }) {
    console.log("OrderTable component props:", { orders, authUser, filters });

    if (!orders || !orders.data || orders.data.length === 0) {
        return <p>No orders found.</p>;
    }

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr className="bg-gray-100">
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Order ID
                        </th>
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Customer
                        </th>
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Date
                        </th>
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Status
                        </th>
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Total
                        </th>
                        <th className="px-4 py-2 border-b text-left text-sm font-semibold text-gray-600">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {orders.data.map((order) => (
                        <tr key={order.id} className="hover:bg-gray-50">
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                {order.id}
                            </td>
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                {order.client?.name || "N/A"}
                            </td>
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                {new Date(
                                    order.created_at
                                ).toLocaleDateString()}
                            </td>
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                <span
                                    className={`px-2 py-1 text-xs font-semibold rounded-full ${
                                        order.status_color
                                            ? "bg-" +
                                              order.status_color +
                                              "-100 text-" +
                                              order.status_color +
                                              "-800"
                                            : "bg-gray-100 text-gray-800"
                                    }`}
                                >
                                    {order.status}
                                </span>
                            </td>
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                {order.total_amount} {order.currency_code}
                            </td>
                            <td className="px-4 py-2 border-b text-sm text-gray-700">
                                {/* <Button variant="outline" size="sm" onClick={() => console.log('View order', order.id)}>View</Button> */}
                                <a
                                    href={`/order-view/${order.id}`}
                                    className="text-blue-500 hover:underline"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            {/* Pagination can be added here */}
        </div>
    );
}
