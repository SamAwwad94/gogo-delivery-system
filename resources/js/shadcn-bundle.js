/**
 * ShadCN Bundle - Main JavaScript file for ShadCN components
 * 
 * This file imports all the necessary JavaScript libraries for ShadCN components
 * and initializes them.
 */

// Import core libraries
import './bootstrap';
import 'toastr';
import Swal from 'sweetalert2';
import Dropzone from 'dropzone';
import 'select2';
import ApexCharts from 'apexcharts';
import lozad from 'lozad';
import moment from 'moment';

// Make libraries available globally
window.Swal = Swal;
window.Dropzone = Dropzone;
window.ApexCharts = ApexCharts;
window.lozad = lozad;
window.moment = moment;

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', function() {
    // Initialize lozad for lazy loading images
    const observer = lozad('.lozad', {
        rootMargin: '10px 0px', // margin around root
        threshold: 0.1, // visibility percentage before loading
        loaded: function(el) {
            // Add a class when the image is loaded
            el.classList.add('loaded');
        }
    });
    observer.observe();

    // Initialize tooltips
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.tooltip !== 'undefined') {
        jQuery('[data-toggle="tooltip"]').tooltip();
    }

    // Initialize Select2 on all select elements with the select2 class
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        jQuery('.select2').select2({
            theme: 'shadcn',
            dropdownCssClass: 'select2-shadcn-dropdown'
        });
    }

    // Initialize theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            // Toggle dark mode
            document.documentElement.classList.toggle('dark');
            
            // Save preference to localStorage
            const isDarkMode = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        });
    }
});

// Configure Toastr
if (typeof toastr !== 'undefined') {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: false,
        onclick: null,
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
        extendedTimeOut: '1000',
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };
}

// Configure SweetAlert2 defaults
if (typeof Swal !== 'undefined') {
    Swal.mixin({
        customClass: {
            confirmButton: 'shadcn-button shadcn-button-primary ml-2',
            cancelButton: 'shadcn-button shadcn-button-outline mr-2',
            input: 'shadcn-input'
        },
        buttonsStyling: false
    });
}

// Configure Dropzone defaults
if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
    
    // Set default options for all Dropzone instances
    Dropzone.prototype.defaultOptions.previewTemplate = `
        <div class="dz-preview dz-file-preview">
            <div class="dz-image">
                <img data-dz-thumbnail />
            </div>
            <div class="dz-details">
                <div class="dz-size"><span data-dz-size></span></div>
                <div class="dz-filename"><span data-dz-name></span></div>
            </div>
            <div class="dz-progress">
                <span class="dz-upload" data-dz-uploadprogress></span>
            </div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="dz-success-mark">
                <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="27" cy="27" r="27" fill="currentColor" fill-opacity="0.1"/>
                    <path d="M36 19L24 31L18 25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="dz-error-mark">
                <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="27" cy="27" r="27" fill="currentColor" fill-opacity="0.1"/>
                    <path d="M34 20L20 34" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 20L34 34" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    `;
}
