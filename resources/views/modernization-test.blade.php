<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Modernization Test Page</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>This page tests the modernized components to ensure they work correctly.</p>
                        
                        <div id="modernization-test-app"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Load Vue App -->
    @vite(['resources/js/modernization-test.js'])
</x-master-layout>
