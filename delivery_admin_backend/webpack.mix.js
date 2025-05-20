const mix = require("laravel-mix");
const tailwindcss = require("@tailwindcss/postcss");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// Legacy assets
mix.js("resources/js/backend-bundle.js", "public/js/backend-bundle.min.js")
    .sass(
        "resources/sass/backend-bundle.scss",
        "public/css/backend-bundle.min.css"
    )
    .sass("resources/sass/backend.scss", "public/css")
    .options({
        processCssUrls: false,
    });

// Modern ShadCN assets
mix.js("resources/js/shadcn-bundle.js", "public/js/shadcn-bundle.min.js")
    .postCss("resources/css/shadcn.css", "public/css/shadcn.min.css", [
        tailwindcss("./tailwind.config.js"),
        require("autoprefixer"),
    ])
    .js("resources/js/theme-toggle.js", "public/js/theme-toggle.min.js")
    .js(
        "resources/js/shadcn-components.js",
        "public/js/shadcn-components.min.js"
    )
    .js(
        "resources/js/shadcn-filter-configs.js",
        "public/js/shadcn-filter-configs.min.js"
    )
    // Toastr is already included in the project
    // .copy(
    //     "node_modules/toastr/build/toastr.min.css",
    //     "public/css/toastr.min.css"
    // )
    // SweetAlert2 is already included in the project
    // .copy(
    //     "node_modules/sweetalert2/dist/sweetalert2.min.css",
    //     "public/css/sweetalert2.min.css"
    // )
    // Dropzone is already included in the project
    // .copy(
    //     "node_modules/dropzone/dist/min/dropzone.min.css",
    //     "public/css/dropzone.min.css"
    // )
    // Lozad is already included in the project
    // .copy("node_modules/lozad/dist/lozad.min.js", "public/js/lozad.min.js")
    // Chart.js is already included in the project
    // .copy("node_modules/chart.js/dist/chart.umd.js", "public/js/chart.min.js")
    // Leaflet is already included in the project
    // .copy("node_modules/leaflet/dist/leaflet.css", "public/css/leaflet.css")
    // .copy("node_modules/leaflet/dist/leaflet.js", "public/js/leaflet.js")
    // .copy("node_modules/leaflet/dist/images", "public/images/leaflet")
    // JSZip is already included in the project
    // .copy("node_modules/jszip/dist/jszip.min.js", "public/js/jszip.min.js")
    .webpackConfig({
        stats: {
            children: true,
        },
    })
    .version();
