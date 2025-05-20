import React from "react";
import OrderTable from "@/Pages/Shadcn/OrderTable"; // Using @ alias
// import { Button } from '@/Components/ui/button'; // Example import for a ShadCN button

// Props would be passed by Inertia from the controller
export default function Orders({
    pageTitle,
    authUser,
    orders,
    buttonConfig,
    filters,
}) {
    console.log("Orders page props:", {
        pageTitle,
        authUser,
        orders,
        buttonConfig,
        filters,
    });

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-2xl font-bold mb-4">
                {pageTitle || "Orders (Shadcn)"}
            </h1>

            {/* Example: Add New Order Button */}
            {/* {buttonConfig && (
        <Button onClick={() => window.location.href = buttonConfig.url} className="mb-4">
          {buttonConfig.iconSvg && <span dangerouslySetInnerHTML={{ __html: buttonConfig.iconSvg }} className="mr-2" />}
          {buttonConfig.text}
        </Button>
      )} */}

            <p className="mb-4">Filters and other UI elements can go here.</p>

            <OrderTable orders={orders} authUser={authUser} filters={filters} />
        </div>
    );
}
