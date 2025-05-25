import React from "react";
import { router } from "@inertiajs/react";

type OrderActionsProps = {
    id: number;
    deletedAt: string | null;
    status: string;
    canEdit: boolean;
    canDelete: boolean;
    canShow: boolean;
    userRoles: string[];
};

const OrderActions: React.FC<OrderActionsProps> = ({
    id,
    deletedAt,
    status,
    canEdit,
    canDelete,
    canShow,
    userRoles,
}) => {
    const handleDelete = (force = false) => {
        if (
            confirm(
                force
                    ? "Are you sure you want to permanently delete this order?"
                    : "Are you sure you want to delete this order?"
            )
        ) {
            router.delete(
                route(force ? "order.force.delete" : "order.destroy", {
                    id,
                    ...(force ? { type: "forcedelete" } : {}),
                })
            );
        }
    };

    const handleRestore = () => {
        if (confirm("Are you sure you want to restore this order?")) {
            router.get(route("order.restore", { id, type: "restore" }));
        }
    };

    const handleShow = () => {
        router.get(route("order.show", id));
    };

    const isAdminOrClient =
        userRoles.includes("admin") || userRoles.includes("client");

    return (
        <div className="d-flex justify-content-end align-items-center">
            {deletedAt ? (
                <>
                    {canEdit && (
                        <button
                            onClick={handleRestore}
                            className="btn btn-link mr-2"
                            title="Restore"
                        >
                            <i
                                className="ri-refresh-line"
                                style={{ fontSize: 18 }}
                            ></i>
                        </button>
                    )}
                    {canDelete && (
                        <button
                            onClick={() => handleDelete(true)}
                            className="btn btn-link text-danger mr-2"
                            title="Force Delete"
                        >
                            <i
                                className="ri-delete-bin-2-fill"
                                style={{ fontSize: 18 }}
                            ></i>
                        </button>
                    )}
                </>
            ) : (
                <>
                    {canDelete && (
                        <button
                            onClick={() => handleDelete(false)}
                            className="btn btn-link text-danger mr-2"
                            title="Delete"
                        >
                            <i className="fas fa-trash-alt"></i>
                        </button>
                    )}

                    {isAdminOrClient && status !== "draft" && canShow && (
                        <button
                            onClick={handleShow}
                            className="btn btn-link mr-2"
                            title="View"
                        >
                            <i className="fas fa-eye text-secondary"></i>
                        </button>
                    )}
                </>
            )}
        </div>
    );
};

export default OrderActions;
