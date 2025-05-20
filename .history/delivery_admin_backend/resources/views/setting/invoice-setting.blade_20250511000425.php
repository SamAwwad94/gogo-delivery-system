{!! html()->form('POST', route('frontend.website.information.update', 'order_invoice'))->attribute('data-toggle', 'validator')->attribute('enctype', 'multipart/form-data')->attribute('id', 'invoice-setting-form')->open() !!}
{!! html()->hidden('invoice_setting', 'invoice_setting') !!}

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{$pageTitle}}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="new-user-info">
                    <div class="row">
                        @foreach($invoice as $key => $value)
                            @if(in_array($key, ['company_name', 'company_contact_number', 'company_address']))
                                <div class="col-md-6 form-group">
                                    {!! html()->label(__('message.' . $key))->for($key)->class('form-control-label') !!}
                                    {!! html()->text($key, $value ?? null)->placeholder(__('message.' . $key))->class('form-control')->required() !!}
                                </div>
                            @else
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.' . $key))->for($key)->class('form-control-label') !!}
                                    <div class="custom-file mb-1">
                                        {!! html()->file($key)->class('custom-file-input invoice-image-input')->accept('image/*')->attribute('data--target', $key . '_image_preview') !!}
                                        {!! html()->label(__('message.choose_file', ['file' => __('message.image')]))->class('custom-file-label') !!}
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    @php
                                        $logoPath = null;
                                        if ($value && is_object($value) && $value->value) {
                                            $logoPath = $value->value;
                                        }
                                    @endphp

                                    @if ($logoPath && file_exists(public_path($logoPath)))
                                        <img id="{{$key}}_image_preview" src="{{ asset($logoPath) }}?v={{ time() }}" alt="{{$key}}"
                                            class="attachment-image mt-1 {{$key}}_image_preview">
                                    @else
                                        <img id="{{$key}}_image_preview" src="{{ getSingleMedia($value, $key) }}" alt="{{$key}}"
                                            class="attachment-image mt-1 {{$key}}_image_preview">
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <hr>
                    {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right')->id('invoice-submit-btn') !!}
                    <div id="invoice-submit-loader" style="display:none;" class="float-right mr-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="dropdown float-right mr-2">
                        <button class="btn btn-md btn-success dropdown-toggle" type="button" id="previewDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-regular fa-eye"></i> {{ __('message.preview') }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="previewDropdown">
                            <a class="dropdown-item" href="{{ route('previousinvoice', ['view' => 1]) }}"
                                target="_blank">
                                {{ __('message.view_in_browser') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('previousinvoice') }}" target="_blank">
                                {{ __('message.download_pdf') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!! html()->form()->close() !!}

<script>
    $(document).ready(function () {
        // Function to handle image preview
        function readURL(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Validate file type
                var fileName = input.files[0].name;
                var fileExtension = fileName.split('.').pop().toLowerCase();
                var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'ico'];

                if (!allowedExtensions.includes(fileExtension)) {
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
                    $('#' + previewId).attr('src', e.target.result);
                    // Update custom file label with filename
                    $(input).next('.custom-file-label').text(input.files[0].name);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Bind change event to all invoice image inputs
        $('.invoice-image-input').on('change', function () {
            var targetId = $(this).attr('data--target');
            readURL(this, targetId);
        });

        // Handle form submission with AJAX
        var supportsAjaxUpload = (window.FormData !== undefined);

        if (supportsAjaxUpload) {
            $('#invoice-setting-form').on('submit', function (e) {
                e.preventDefault();

                // Show loading indicator
                $('#invoice-submit-btn').prop('disabled', true);
                $('#invoice-submit-loader').show();

                // Create FormData for AJAX submission
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Show success message
                        Snackbar.show({
                            text: "Invoice settings updated successfully",
                            pos: 'bottom-right',
                            backgroundColor: '#28a745',
                            actionTextColor: '#fff'
                        });

                        // Reset buttons
                        $('#invoice-submit-btn').prop('disabled', false);
                        $('#invoice-submit-loader').hide();

                        // Refresh image with cache-busting timestamp
                        var timestamp = new Date().getTime();
                        $('.company_logo_image_preview').attr('src', $('.company_logo_image_preview').attr('src').split('?')[0] + '?v=' + timestamp);
                    },
                    error: function (xhr) {
                        // Show error message
                        var errorMsg = "Error updating invoice settings";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg += ": " + xhr.responseJSON.message;
                        }

                        Snackbar.show({
                            text: errorMsg,
                            pos: 'bottom-right',
                            backgroundColor: '#d32f2f',
                            actionTextColor: '#fff'
                        });

                        // Reset buttons
                        $('#invoice-submit-btn').prop('disabled', false);
                        $('#invoice-submit-loader').hide();
                    }
                });
            });
        }
    });
</script>