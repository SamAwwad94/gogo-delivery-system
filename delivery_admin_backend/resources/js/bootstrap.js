window._ = require("lodash");

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// Import modernized dependencies
import dayjsWithMomentCompat from "./utils/date";
import { useToast } from "./plugins/toast";

// Legacy dependencies that are still needed
window.Popper = require("popper.js").default;
window.$ = window.jQuery = require("jquery"); // Still needed for legacy code
require("bootstrap");
window.ApexCharts = require("apexcharts");
require("web-animations-js");
window.Vivus = require("vivus");
window.dragula = require("dragula");
window.Scrollbar = require("smooth-scrollbar/dist/smooth-scrollbar");
require("jquery.appear");
require("datatables");
require("quill");
require("bootstrap-validator");

// Setup modern replacements
window.moment = dayjsWithMomentCompat; // Replace moment with dayjs
const toast = useToast();
toast.setupLegacyBridge(); // Setup toastr compatibility layer
window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
