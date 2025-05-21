/**
 * ShadCN Bundle
 * 
 * This file imports and bundles all ShadCN components and utilities.
 * It serves as the main entry point for ShadCN UI in the application.
 */

// Import CSS
import '../css/shadcn.css';

// Import components
import './shadcn-components';
import './theme-toggle';

// Import third-party libraries
import axios from 'axios';
import JSZip from 'jszip';

/**
 * ShadCN API Client
 * 
 * A simple API client for making requests to the backend.
 */
class ShadcnApiClient {
    constructor() {
        this.axios = axios.create({
            baseURL: '/api',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        // Add CSRF token to all requests
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
        
        // Add response interceptor for error handling
        this.axios.interceptors.response.use(
            response => response,
            error => {
                this.handleError(error);
                return Promise.reject(error);
            }
        );
    }
    
    /**
     * Make a GET request
     * 
     * @param {string} url - The URL to request
     * @param {Object} params - Query parameters
     * @returns {Promise} - Axios promise
     */
    get(url, params = {}) {
        return this.axios.get(url, { params });
    }
    
    /**
     * Make a POST request
     * 
     * @param {string} url - The URL to request
     * @param {Object} data - Request body
     * @returns {Promise} - Axios promise
     */
    post(url, data = {}) {
        return this.axios.post(url, data);
    }
    
    /**
     * Make a PUT request
     * 
     * @param {string} url - The URL to request
     * @param {Object} data - Request body
     * @returns {Promise} - Axios promise
     */
    put(url, data = {}) {
        return this.axios.put(url, data);
    }
    
    /**
     * Make a DELETE request
     * 
     * @param {string} url - The URL to request
     * @returns {Promise} - Axios promise
     */
    delete(url) {
        return this.axios.delete(url);
    }
    
    /**
     * Handle API errors
     * 
     * @param {Error} error - Axios error object
     */
    handleError(error) {
        if (error.response) {
            // Server responded with an error status
            const status = error.response.status;
            
            if (status === 401) {
                // Unauthorized - redirect to login
                window.location.href = '/login';
            } else if (status === 403) {
                // Forbidden - show permission error
                this.showErrorNotification('Permission Denied', 'You do not have permission to perform this action.');
            } else if (status === 422) {
                // Validation error - show validation errors
                const errors = error.response.data.errors;
                let errorMessage = 'Please correct the following errors:';
                
                for (const field in errors) {
                    errorMessage += `<br>- ${errors[field][0]}`;
                }
                
                this.showErrorNotification('Validation Error', errorMessage);
            } else {
                // Other server errors
                this.showErrorNotification('Server Error', 'An error occurred while processing your request.');
            }
        } else if (error.request) {
            // Request was made but no response received
            this.showErrorNotification('Network Error', 'Could not connect to the server. Please check your internet connection.');
        } else {
            // Error in setting up the request
            this.showErrorNotification('Request Error', 'An error occurred while setting up the request.');
        }
    }
    
    /**
     * Show error notification
     * 
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     */
    showErrorNotification(title, message) {
        if (window.Swal) {
            window.Swal.fire({
                title: title,
                html: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else if (window.toastr) {
            window.toastr.error(message, title);
        } else {
            alert(`${title}: ${message}`);
        }
    }
}

// Create global API client instance
window.shadcnApi = new ShadcnApiClient();

// Export components for use in other files
export { ShadcnApiClient };
