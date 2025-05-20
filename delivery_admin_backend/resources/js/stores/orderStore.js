import { defineStore } from 'pinia';
import axios from 'axios';

export const useOrderStore = defineStore('orders', {
  state: () => ({
    orders: [],
    loading: false,
    error: null,
    pagination: {
      page: 1,
      pageSize: 10,
      total: 0
    },
    filters: {
      search: '',
      status: null,
      dateRange: null,
      customer: null,
      phone: null,
      location: null,
      paymentStatus: null
    }
  }),
  
  getters: {
    filteredOrders: (state) => {
      // This would be handled by the backend in a real implementation
      return state.orders;
    },
    
    paginatedOrders: (state) => {
      // This would be handled by the backend in a real implementation
      return state.orders;
    }
  },
  
  actions: {
    async fetchOrders() {
      this.loading = true;
      this.error = null;
      
      try {
        // In a real implementation, this would call your Laravel API
        // with filters and pagination parameters
        const response = await axios.get('/api/orders', {
          params: {
            page: this.pagination.page,
            pageSize: this.pagination.pageSize,
            ...this.filters
          }
        });
        
        this.orders = response.data.data;
        this.pagination.total = response.data.total;
      } catch (error) {
        this.error = error.message || 'Failed to fetch orders';
        console.error('Error fetching orders:', error);
        
        // For development, use mock data if API fails
        this.orders = [
          {
            id: 'ORD-001',
            date: '2023-05-15',
            customer: 'John Doe',
            phone: '+1 (555) 123-4567',
            pickupLocation: '123 Main St, New York, NY',
            deliveryLocation: '456 Broadway, New York, NY',
            status: 'pending',
            paymentStatus: 'unpaid',
            amount: 45.99
          },
          {
            id: 'ORD-002',
            date: '2023-05-14',
            customer: 'Jane Smith',
            phone: '+1 (555) 987-6543',
            pickupLocation: '789 Oak Ave, Los Angeles, CA',
            deliveryLocation: '101 Pine St, Los Angeles, CA',
            status: 'processing',
            paymentStatus: 'paid',
            amount: 78.50
          },
          // Add more mock data as needed
        ];
      } finally {
        this.loading = false;
      }
    },
    
    setFilters(filters) {
      this.filters = { ...this.filters, ...filters };
      this.pagination.page = 1; // Reset to first page when filters change
      this.fetchOrders();
    },
    
    resetFilters() {
      this.filters = {
        search: '',
        status: null,
        dateRange: null,
        customer: null,
        phone: null,
        location: null,
        paymentStatus: null
      };
      this.fetchOrders();
    },
    
    setPage(page) {
      this.pagination.page = page;
      this.fetchOrders();
    },
    
    setPageSize(pageSize) {
      this.pagination.pageSize = pageSize;
      this.pagination.page = 1; // Reset to first page when page size changes
      this.fetchOrders();
    }
  }
});
