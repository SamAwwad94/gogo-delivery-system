<form action="{{ route('settings.updateLogos') }}" method="POST" enctype="multipart/form-data" id="logoForm">
    @csrf

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>{{ __('message.logo_management') }}</h4>
        </div>
        <div class="card-body">
            <!-- Site Logo -->
            <div class="form-group">
                <label>{{ __('message.site_logo') }}</label>
                <div class="row">
                    <div class="col-md-4">
                        @php
                            $logoPath = \App\Models\Setting::where('key', 'site_logo')->value('value');
                        @endphp

                        @if ($logoPath && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}?v={{ time() }}" width="100" class="img-preview">
                        @else
                            <img src="{{ asset('images/logos/default-logo.png') }}" width="100" class="img-preview">
                        @endif
                    </div>
                    <div class="col-md-8">
                        <input type="file" name="site_logo" class="form-control" accept="image/*">
                        <small class="text-muted">{{ __('message.recommended_size') }}: 200-250px width</small>
                    </div>
                </div>
            </div>

            <!-- Dark Logo -->
            <div class="form-group mt-4">
                <label>{{ __('message.dark_logo') }}</label>
                <div class="row">
                    <div class="col-md-4">
                        @php
                            $darkLogoPath = \App\Models\Setting::where('key', 'site_dark_logo')->value('value');
                        @endphp

                        @if ($darkLogoPath && file_exists(public_path($darkLogoPath)))
                            <img src="{{ asset($darkLogoPath) }}?v={{ time() }}" width="100" class="img-preview">
                        @else
                            <img src="{{ asset('images/logos/dark_logo.png') }}" width="100" class="img-preview">
                        @endif
                    </div>
                    <div class="col-md-8">
                        <input type="file" name="site_dark_logo" class="form-control" accept="image/*">
                        <small class="text-muted">{{ __('message.recommended_size') }}: 200-250px width</small>
                    </div>
                </div>
            </div>

            <!-- Favicon -->
            <div class="form-group mt-4">
                <label>{{ __('message.favicon') }}</label>
                <div class="row">
                    <div class="col-md-4">
                        @php
                            $faviconPath = \App\Models\Setting::where('key', 'site_favicon')->value('value');
                        @endphp

                        @if ($faviconPath && file_exists(public_path($faviconPath)))
                            <img src="{{ asset($faviconPath) }}?v={{ time() }}" height="32" class="img-preview">
                        @else
                            <img src="{{ asset('images/logos/site_favicon.png') }}" height="32" class="img-preview">
                        @endif
                    </div>
                    <div class="col-md-8">
                        <input type="file" name="site_favicon" class="form-control" accept="image/*">
                        <small class="text-muted">{{ __('message.recommended_size') }}: 16x16px, 32x32px, or
                            64x64px</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ __('message.update_logos') }}</button>
        </div>
    </div>
</form>

<script>
    // Preview image before upload
    document.addEventListener('DOMContentLoaded', function () {
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type and size
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml', 'image/x-icon'];
                    if (!validTypes.includes(file.type)) {
                        alert('Invalid file type. Please upload an image file (jpg, jpeg, png, gif, svg, ico).');
                        this.value = '';
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        alert('File size exceeds 2MB. Please upload a smaller file.');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    const imgPreview = this.closest('.form-group').querySelector('.img-preview');

                    reader.onload = function (e) {
                        imgPreview.src = e.target.result;
                    }

                    reader.readAsDataURL(file);
                }
            });
        });

        // Handle form submission
        const logoForm = document.getElementById('logoForm');
        logoForm.addEventListener('submit', function (e) {
            // Set a flag to refresh logo previews when returning to general settings
            sessionStorage.setItem('refreshLogos', 'true');
        });

        // Check for flash message
        @if(session('success'))
            // Set flag to refresh other pages that might be using the logos
            sessionStorage.setItem('refreshLogos', 'true');
        @endif
    });
</script>