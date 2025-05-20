<form action="{{ route('order.index') }}" method="GET" id="order-filter-form">
    <div class="shadcn-filter-container mb-4">
        <div
            class="shadcn-filter-header flex items-center justify-between p-3 bg-muted rounded-t-lg border-b border-border">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                </svg>
                <h3 class="text-base font-medium">Filters</h3>
            </div>
            <div class="flex items-center">
                <a href="{{ route('order.index') }}" class="shadcn-button shadcn-button-ghost text-sm mr-2">Clear</a>
                <button type="submit" class="shadcn-button shadcn-button-primary text-sm">Apply</button>
            </div>
        </div>
        <div class="shadcn-filter-body p-3 bg-background rounded-b-lg border border-t-0 border-border">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                <!-- Order ID Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="order_id">Order ID</label>
                    <input type="text" id="order_id" name="order_id"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                        placeholder="Search by order ID" value="{{ request('order_id') }}">
                </div>

                <!-- Status Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="status">Status</label>
                    <select id="status" name="status"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="create" {{ request('status') == 'create' ? 'selected' : '' }}>Created</option>
                        <option value="courier_assigned" {{ request('status') == 'courier_assigned' ? 'selected' : '' }}>
                            Assigned</option>
                        <option value="courier_accepted" {{ request('status') == 'courier_accepted' ? 'selected' : '' }}>
                            Accepted</option>
                        <option value="courier_arrived" {{ request('status') == 'courier_arrived' ? 'selected' : '' }}>
                            Arrived</option>
                        <option value="courier_picked_up" {{ request('status') == 'courier_picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="courier_departed" {{ request('status') == 'courier_departed' ? 'selected' : '' }}>
                            Departed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Delivered
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="from_date"
                            class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                            placeholder="From" value="{{ request('from_date') }}">
                        <input type="date" name="to_date"
                            class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                            placeholder="To" value="{{ request('to_date') }}">
                    </div>
                </div>

                <!-- Customer Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="client_id">Customer</label>
                    <select id="client_id" name="client_id"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                        <option value="">All Customers</option>
                        @foreach(\App\Models\User::where('user_type', 'client')->get() as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Phone Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="phone">Phone</label>
                    <input type="text" id="phone" name="phone"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                        placeholder="Search by phone" value="{{ request('phone') }}">
                </div>

                <!-- Pickup Location Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="pickup_location">Pickup Location</label>
                    <input type="text" id="pickup_location" name="pickup_location"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                        placeholder="Search by location" value="{{ request('pickup_location') }}">
                </div>

                <!-- Delivery Location Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="delivery_location">Delivery Location</label>
                    <input type="text" id="delivery_location" name="delivery_location"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                        placeholder="Search by location" value="{{ request('delivery_location') }}">
                </div>

                <!-- Payment Status Filter -->
                <div class="shadcn-filter-item">
                    <label class="text-sm font-medium mb-1 block" for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status"
                        class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                        <option value="">All Payment Statuses</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid
                        </option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>