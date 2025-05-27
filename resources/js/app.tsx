import "../css/app.css";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";

// Create a global route function to replace Ziggy
declare global {
    interface Window {
        route: (
            name: string,
            params?: Record<string, any>,
            absolute?: boolean
        ) => string;
    }
}

window.route = (
    name: string,
    params: Record<string, any> = {},
    absolute: boolean = true
): string => {
    // Handle named routes
    const routes: Record<string, string> = {
        home: "/home",
        "order.index": "/order",
        "order.create": "/order/create",
        "order.show": "/order/{id}",
        "order.edit": "/order/{id}/edit",
        "users.index": "/users",
        "users.create": "/users/create",
        "users.show": "/users/{id}",
        "users.edit": "/users/{id}/edit",
        "city.index": "/city",
        "city.create": "/city/create",
        "city.show": "/city/{id}",
        "city.edit": "/city/{id}/edit",
        "order.export.csv": "/order/export/csv",
        "order.export.pdf": "/order/export/pdf",
        "users.export.csv": "/users/export/csv",
        "users.export.pdf": "/users/export/pdf",
        "city.export.csv": "/city/export/csv",
        "city.export.pdf": "/city/export/pdf",
        "order.bulk-delete": "/order/bulk-delete",
        "users.bulk-delete": "/users/bulk-delete",
        "city.bulk-delete": "/city/bulk-delete",
    };

    let path = routes[name] || name;

    // If path doesn't start with /, treat it as a path
    if (!path.startsWith("/")) {
        path = "/" + path;
    }

    // Replace route parameters like {id} with actual values
    Object.keys(params).forEach((key) => {
        const placeholder = `{${key}}`;
        if (path.includes(placeholder)) {
            path = path.replace(placeholder, params[key]);
            delete params[key]; // Remove from params so it's not added as query string
        }
    });

    const url = new URL(window.location.origin);
    url.pathname = path;

    // Add remaining params as query string
    Object.keys(params).forEach((key) => {
        url.searchParams.append(key, params[key]);
    });

    return absolute ? url.toString() : url.pathname + url.search;
};

createInertiaApp({
    title: (title) => `${title} - Gogo Delivery`,
    resolve: (name) => {
        const tsxPages = import.meta.glob("./Pages/**/*.tsx", {
            eager: true,
        }) as Record<string, any>;
        const jsxPages = import.meta.glob("./Pages/**/*.jsx", {
            eager: true,
        }) as Record<string, any>;

        // Combine both TSX and JSX pages
        const pages = { ...tsxPages, ...jsxPages };

        // Try to find the page with .tsx extension first, then .jsx
        return pages[`./Pages/${name}.tsx`] || pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: "#4B5563",
    },
});
