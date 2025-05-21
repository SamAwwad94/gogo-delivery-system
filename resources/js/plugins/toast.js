import { createApp } from 'vue';
import Toast, { POSITION, TYPE } from "vue-toastification";
import "vue-toastification/dist/index.css";

const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true
};

// Create a global toast function that can be called from anywhere
export const useToast = () => {
    const app = createApp({});
    app.use(Toast, options);
    const toast = app.config.globalProperties.$toast;
    
    return {
        success(message, title = null) {
            toast.success(message, {
                type: TYPE.SUCCESS,
                timeout: 5000
            });
        },
        error(message, title = null) {
            toast.error(message, {
                type: TYPE.ERROR,
                timeout: 10000
            });
        },
        info(message, title = null) {
            toast.info(message, {
                type: TYPE.INFO,
                timeout: 5000
            });
        },
        warning(message, title = null) {
            toast.warning(message, {
                type: TYPE.WARNING,
                timeout: 7000
            });
        },
        // Bridge to window.toastr for legacy code
        setupLegacyBridge() {
            if (typeof window !== 'undefined') {
                window.toastr = {
                    success: this.success,
                    error: this.error,
                    info: this.info,
                    warning: this.warning,
                    options: {
                        timeOut: 5000,
                        set timeOut(value) {
                            // This is just a placeholder to avoid errors in legacy code
                        }
                    }
                };
            }
        }
    };
};

// Plugin installation
export default {
    install: (app) => {
        app.use(Toast, options);
        
        // Create a global $toast property
        app.config.globalProperties.$toast = {
            success(message, title = null) {
                app.config.globalProperties.$toast.success(message, {
                    type: TYPE.SUCCESS,
                    timeout: 5000
                });
            },
            error(message, title = null) {
                app.config.globalProperties.$toast.error(message, {
                    type: TYPE.ERROR,
                    timeout: 10000
                });
            },
            info(message, title = null) {
                app.config.globalProperties.$toast.info(message, {
                    type: TYPE.INFO,
                    timeout: 5000
                });
            },
            warning(message, title = null) {
                app.config.globalProperties.$toast.warning(message, {
                    type: TYPE.WARNING,
                    timeout: 7000
                });
            }
        };
        
        // Setup legacy bridge for non-Vue code
        const toast = useToast();
        toast.setupLegacyBridge();
    }
};
