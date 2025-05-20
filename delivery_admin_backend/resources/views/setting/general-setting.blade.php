{!! html()->modelForm($settings, 'POST', route('settingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->attribute('id', 'general-setting-form')->open() !!}
{!! html()->hidden('id', null)->class('form-control') !!}
{!! html()->hidden('page', $page)->class('form-control') !!}

<div class="row">
    <div class="col-lg-6">
        <!-- Site Logo -->
        <div class="form-group">
            <label class="col-sm-6 form-control-label">{{ __('message.site_logo') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        @php
                            $logoPath = \App\Models\Setting::where('key', 'site_logo')->value('value');
                            $useLiveLogo = true;
                        @endphp

                        @if ($logoPath && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}?v={{ time() }}" width="100" id="site_logo_preview"
                                alt="site_logo" class="image site_logo site_logo_preview">
                        @else
                            <img src="{{ getSingleMedia($settings, 'site_logo') }}" width="100" id="site_logo_preview"
                                alt="site_logo" class="image site_logo site_logo_preview">
                            @php $useLiveLogo = false; @endphp
                        @endif
                        @if($useLiveLogo && getMediaFileExit($settings, 'site_logo'))
                            <a class="text-danger remove-file"
                                href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_logo']) }}"
                                data--submit="confirm_form" data--confirmation='true' data--ajax="true"
                                title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_logo')->class('custom-file-input custom-file-input-sm detail')->id('site_logo')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.logo'))->for('site_logo')->class('custom-file-label') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Dark Logo -->
        <div class="form-group">
            <label class="col-sm-6 form-control-label">{{ __('message.dark_logo') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        @php
                            $darkLogoPath = \App\Models\Setting::where('key', 'site_dark_logo')->value('value');
                            $useDarkLogo = true;
                        @endphp

                        @if ($darkLogoPath && file_exists(public_path($darkLogoPath)))
                            <img src="{{ asset($darkLogoPath) }}?v={{ time() }}" width="100" id="site_dark_logo_preview"
                                alt="site_dark_logo" class="image site_dark_logo site_dark_logo_preview border">
                        @else
                            <img src="{{ getSingleMedia($settings, 'site_dark_logo') }}" width="100"
                                id="site_dark_logo_preview" alt="site_dark_logo"
                                class="image site_dark_logo site_dark_logo_preview border">
                            @php $useDarkLogo = false; @endphp
                        @endif
                        @if($useDarkLogo && getMediaFileExit($settings, 'site_dark_logo'))
                            <a class="text-danger remove-file"
                                href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_dark_logo']) }}"
                                data--submit="confirm_form" data--confirmation='true' data--ajax="true"
                                title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_dark_logo')->class('custom-file-input custom-file-input-sm detail')->id('site_dark_logo')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.dark_logo'))->for('site_dark_logo')->class('custom-file-label') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Favicon -->
        <div class="form-group">
            <label class="col-sm-6 form-control-label">{{ __('message.favicon') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        @php
                            $faviconPath = \App\Models\Setting::where('key', 'site_favicon')->value('value');
                            $useFavicon = true;
                        @endphp

                        @if ($faviconPath && file_exists(public_path($faviconPath)))
                            <img src="{{ asset($faviconPath) }}?v={{ time() }}" height="32" id="site_favicon_preview"
                                alt="site_favicon" class="image site_favicon site_favicon_preview">
                        @else
                            <img src="{{ getSingleMedia($settings, 'site_favicon') }}" height="32" id="site_favicon_preview"
                                alt="site_favicon" class="image site_favicon site_favicon_preview">
                            @php $useFavicon = false; @endphp
                        @endif
                        @if($useFavicon && getMediaFileExit($settings, 'site_favicon'))
                            <a class="text-danger remove-file"
                                href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_favicon']) }}"
                                data--submit="confirm_form" data--confirmation='true' data--ajax="true"
                                title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-title='{{ __("message.remove_file_title", ["name" => __("message.image")]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_favicon')->class('custom-file-input custom-file-input-sm detail')->id('site_favicon')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.site_favicon'))->for('site_favicon')->class('custom-file-label') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_name'))->for('site_name')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_name', null)->class('form-control')->placeholder(__('message.site_name')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_description'))->for('site_description')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->textarea('site_description', null)->class('form-control textarea')->placeholder(__('message.site_description'))->attribute('rows', '3') !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.contact_email'))->for('support_email')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('support_email', null)->class('form-control')->placeholder(__('message.contact_email')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.contact_number'))->for('support_number')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('support_number', null)->class('form-control')->placeholder(__('message.contact_number')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_email'))->for('site_email')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_email', null)->class('form-control')->placeholder(__('message.site_email')) !!}
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="form-group">
            {!! html()->label(__('message.default_language'))->for('default_language')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                <select class="form-control select2js default_language" name="env[DEFAULT_LANGUAGE]"
                    id="default_language">
                    <option value="en" {{ config('app.locale') == 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ config('app.locale') == 'ar' ? 'selected' : '' }}>Arabic</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            {!! html()->label(__('message.language_option'))->for('language_option')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                <select class="form-control select2js language_option" name="language_option[]" id="language_option"
                    multiple>
                    @if(config('app.locale') == 'en')
                        <option value="en" disabled>English</option>
                        <option value="ar" selected>Arabic</option>
                    @else
                        <option value="en" selected>English</option>
                        <option value="ar" disabled>Arabic</option>
                    @endif
                </select>
                <small class="form-text text-muted">English is the default language, and Arabic is the second language
                    option.</small>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.currency'))->for('currency')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                @php
                    $currency_code = $settings->currency_code ?? 'USD';
                    $currency = currencyArray($currency_code);
                @endphp
                <select class="form-control select2js" name="currency_code" id="currency_code">
                    <option value="USD" {{ 'USD' == $currency_code ? 'selected' : '' }}>
                        ( $ ) United States (US) dollar
                    </option>
                    <option value="LBP" {{ 'LBP' == $currency_code ? 'selected' : '' }}>
                        ( ل.ل ) Lebanese pound
                    </option>
                </select>
                <small class="form-text text-muted">USD is the primary currency, and LBP (Lebanese Pound) is the
                    secondary currency.</small>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.currency_position'))->for('currency_position')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->select('currency_position', ['left' => __('message.left'), 'right' => __('message.right')], $settings->currency_position ?? 'left')->class('form-control select2js')!!}
            </div>
        </div>

        <div class="form-group col-md-12">
            {!! html()->label(__('message.theme_color'))->for('bg_color_code')->class('col-sm-12  form-control-label') !!}
            <div class="input-group">
                {!! html()->input('color', 'bg_color', $settings->color ?? '#5e3c9e')->class('form-control form-control-color')->id('bg_color_picker')->attribute('title', __('message.theme_color'))->required()!!}
                {!! html()->text('color', $settings->color ?? '#5e3c9e')->placeholder(__('message.bg_color'))->class('form-control')->id('bg_color_code')->required()!!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.timezone'), 'timezone')->class('col-sm-12 form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->select('timezone', [auth()->user()->timezone => timeZoneList()[auth()->user()->timezone]])
    ->value(old('timezone'))
    ->class('form-control select2js')
    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'timezone']))
    ->attribute('data-placeholder', __('message.select_field', ['name' => __('message.timezone')]))
    ->required()
                !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.facebook_url'))->for('facebook_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('facebook_url', null)->class('form-control')->placeholder(__('message.enter_name', ['name' => __('message.facebook_url')])) !!}
            </div>
        </div>
        <div class="form-group">
            {!! html()->label(__('message.twitter_url'))->for('twitter_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('twitter_url', null)->class('form-control')->placeholder(__('message.enter_name', ['name' => __('message.twitter_url')])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.linkedin_url'))->for('linkedin_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('linkedin_url', null)->class('form-control')->placeholder(__('message.enter_name', ['name' => __('message.linkedin_url')])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.instagram_url'))->for('instagram_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('instagram_url', null)->class('form-control')->placeholder(__('message.enter_name', ['name' => __('message.instagram_url')])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.copyright_text'))->for('copyright_text')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_copyright', null)->class('form-control')->placeholder(__('message.enter_name', ['name' => __('message.site_copyright')])) !!}
            </div>
        </div>
        {{-- <div class="form-group">
            {!! html()->label(__('message.help_support_url'))->for('help_support_url')->class('col-sm-12
            form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('help_support_url', null)->class('form-control')->placeholder(__('message.enter_name',
                [ 'name' => __('message.help_support_url') ])) !!}
            </div>
        </div> --}}
    </div>
    <hr>
    <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-md-right')->id('submit-btn') !!}
                <div id="submit-loader" style="display:none;" class="float-md-right mr-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!! html()->form()->close() !!}
<script>
    // Check if browser supports necessary features for AJAX uploads
    var supportsAjaxUpload = (window.FormData !== undefined);

    function getExtension(filename) {
        var parts = filename.split('.');
        return parts[parts.length - 1];
    }
    function isImage(filename) {
        var ext = getExtension(filename);
        switch (ext.toLowerCase()) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'svg':
            case 'ico':
                return true;
        }
        return false;
    }
    function readURL(input, className) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var res = isImage(input.files[0].name);
            if (res == false) {
                var msg = 'Image should be png/PNG, jpg/JPG, jpeg/JPEG, gif/GIF, svg/SVG, ico/ICO';
                Snackbar.show({ text: msg, pos: 'bottom-right', backgroundColor: '#d32f2f', actionTextColor: '#fff' });
                $(input).val("");
                return false;
            }

            // Validate file size (max 2MB)
            if (input.files[0].size > 2 * 1024 * 1024) {
                var msg = 'File size exceeds 2MB. Please upload a smaller file.';
                Snackbar.show({ text: msg, pos: 'bottom-right', backgroundColor: '#d32f2f', actionTextColor: '#fff' });
                $(input).val("");
                return false;
            }

            reader.onload = function (e) {
                $(document).find('img.' + className).attr('src', e.target.result);
                $(document).find("label." + className).text((input.files[0].name));
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function () {
        $('.select2js').select2();
        $(document).on('change', '#site_logo', function () {
            readURL(this, 'site_logo');
        });
        $(document).on('change', '#site_favicon', function () {
            readURL(this, 'site_favicon');
        });

        // The favicon will be handled by the main form submission
        $(document).on('change', '#site_dark_logo', function () {
            readURL(this, 'site_dark_logo');
        });

        // Function to refresh logo previews from server
        function refreshLogoPreviews() {
            var timestamp = new Date().getTime();

            // Direct image replacement approach
            // This forces browser to reload images despite cache
            $.ajax({
                url: '/get-logo-paths',
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function (response) {
                    if (response.site_logo) {
                        $('.site_logo_preview').attr('src', '').attr('src', response.site_logo + "?v=" + timestamp);
                    }
                    if (response.site_dark_logo) {
                        $('.site_dark_logo_preview').attr('src', '').attr('src', response.site_dark_logo + "?v=" + timestamp);
                    }
                    if (response.site_favicon) {
                        $('.site_favicon_preview').attr('src', '').attr('src', response.site_favicon + "?v=" + timestamp);
                    }
                },
                error: function () {
                    // Create new image elements to replace existing ones
                    var timestamp = new Date().getTime();

                    var logoPath = "{{ \App\Models\Setting::where('key', 'site_logo')->value('value') }}";
                    var darkLogoPath = "{{ \App\Models\Setting::where('key', 'site_dark_logo')->value('value') }}";
                    var faviconPath = "{{ \App\Models\Setting::where('key', 'site_favicon')->value('value') }}";

                    if (logoPath) {
                        var newLogoImg = $('<img>').attr({
                            'src': "{{ asset('') }}" + logoPath + "?v=" + timestamp,
                            'width': 100,
                            'id': 'site_logo_preview',
                            'alt': 'site_logo',
                            'class': 'image site_logo site_logo_preview'
                        });
                        $('.site_logo_preview').replaceWith(newLogoImg);
                    }

                    if (darkLogoPath) {
                        var newDarkLogoImg = $('<img>').attr({
                            'src': "{{ asset('') }}" + darkLogoPath + "?v=" + timestamp,
                            'width': 100,
                            'id': 'site_dark_logo_preview',
                            'alt': 'site_dark_logo',
                            'class': 'image site_dark_logo site_dark_logo_preview border'
                        });
                        $('.site_dark_logo_preview').replaceWith(newDarkLogoImg);
                    }

                    if (faviconPath) {
                        var newFaviconImg = $('<img>').attr({
                            'src': "{{ asset('') }}" + faviconPath + "?v=" + timestamp,
                            'height': 32,
                            'id': 'site_favicon_preview',
                            'alt': 'site_favicon',
                            'class': 'image site_favicon site_favicon_preview'
                        });
                        $('.site_favicon_preview').replaceWith(newFaviconImg);
                    }
                }
            });
        }

        // Check if we should refresh (after page load)
        if (sessionStorage.getItem('refreshLogos') === 'true') {
            refreshLogoPreviews();
            sessionStorage.removeItem('refreshLogos');
        }

        // Simplified language selection logic for English and Arabic only
        $('.default_language').on('change', function (e) {
            var id = $(this).val();
            $('.language_option').empty();

            if (id === 'en') {
                $('.language_option').append('<option value="en" disabled>English</option>');
                $('.language_option').append('<option value="ar" selected>Arabic</option>');
            } else {
                $('.language_option').append('<option value="en" selected>English</option>');
                $('.language_option').append('<option value="ar" disabled>Arabic</option>');
            }

            $('.language_option').select2("destroy").select2();
        });
        function colorCodeInput() {
            var colorCode = $('#bg_color_code').val();
            if (colorCode[0] !== '#') {
                colorCode = '#' + colorCode;
            }
            $('#bg_color_code').val(colorCode);
        }

        $('#bg_color_code').on('input', function () {
            colorCodeInput();
            var colorCode = $(this).val();
            $('#bg_color_picker').val(colorCode);
        });

        $('#bg_color_picker').on('input', function () {
            var selectedColor = $(this).val();
            $('#bg_color_code').val(selectedColor);
        });

        colorCodeInput();

        // Only use AJAX submission if browser supports it
        if (supportsAjaxUpload) {
            // Submit form via AJAX
            $('#general-setting-form').on('submit', function (e) {
                e.preventDefault();

                // Set a flag to refresh logo previews
                sessionStorage.setItem('refreshLogos', 'true');

                // Add a loading indicator
                var submitButton = $('#submit-btn');
                submitButton.prop('disabled', true);
                $('#submit-loader').show();

                // Create FormData object to handle file uploads
                var formData = new FormData(this);

                // Submit the form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Show success message
                        Snackbar.show({
                            text: "Settings updated successfully",
                            pos: 'bottom-right',
                            backgroundColor: '#28a745',
                            actionTextColor: '#fff'
                        });

                        // Refresh logo previews immediately with a slight delay to ensure files are processed
                        setTimeout(function () {
                            refreshLogoPreviews();
                        }, 500);

                        // Reset button
                        submitButton.prop('disabled', false);
                        $('#submit-loader').hide();
                    },
                    error: function (xhr) {
                        // Show error message
                        var errorMsg = "Error updating settings";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg += ": " + xhr.responseJSON.message;
                        }

                        Snackbar.show({
                            text: errorMsg,
                            pos: 'bottom-right',
                            backgroundColor: '#d32f2f',
                            actionTextColor: '#fff'
                        });

                        // Reset button
                        submitButton.prop('disabled', false);
                        $('#submit-loader').hide();
                    }
                });
            });
        } else {
            // For browsers that don't support FormData or other required features
            // we'll do a traditional form submit but still set the refresh flag
            $('#general-setting-form').on('submit', function () {
                sessionStorage.setItem('refreshLogos', 'true');
                $('#submit-btn').prop('disabled', true);
                $('#submit-loader').show();
                return true; // Allow traditional form submission
            });
        }
    });
</script>