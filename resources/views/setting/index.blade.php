<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.list') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <ul class="nav flex-column nav-pills me-3 tabslink" id="tabs-text" role="tablist">
                                    @if(in_array($page, ['profile_form', 'password-form']))
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=profile_form"
                                                data-target=".paste_here"
                                                class="nav-link {{ $page == 'profile_form' ? 'active' : '' }}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.profile')}} </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=password_form"
                                                data-target=".paste_here"
                                                class="nav-link {{ $page == 'password_form' ? 'active' : '' }}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.change_password') }}
                                            </a>
                                        </li>
                                    @else
                                        @hasanyrole('admin|demo_admin')
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=general-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'general-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.general_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=mobile-config"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'mobile-config' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.mobile_config') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=mail-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'mail-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.mail_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=language-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'language-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.language_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=notification-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'notification-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.notification_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=payment-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'payment-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.payment_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=invoice-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'invoice-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.invoice_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=order-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'order-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.order_settings') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=register-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'register-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.register_setting') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=reference-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'reference-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.reference_setting') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=insurance-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'insurance-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.insurance_setting') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=ordermail-setting"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'ordermail-setting' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.mail_template_setting') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=database-backup"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'database-backup' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.database_backup') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=print-label-mobail-number"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'print-label-mobail-number' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.print_label') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=sms-settings"
                                                data-target=".paste_here"
                                                class="nav-link {{$page == 'sms-settings' ? 'active' : ''}}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.sms_setting') }}</a>
                                        </li>
                                        @endhasanyrole
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <div class="tab-content" id="pills-tabContent-1">
                                    <div class="tab-pane active p-1">
                                        <div class="paste_here"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
        <script>
            $(document).ready(function () {
                // Only load the content once on page load
                var $activeTab = $('.nav-item').find('a.active');
                var loadurl = "{{route('layout_page')}}?page={{$page}}";
                var targ = $activeTab.attr('data-target') || '.paste_here';

                // Load the initial content once
                $.post(loadurl, { '_token': $('meta[name=csrf-token]').attr('content') }, function (data) {
                    $(targ).html(data);
                });

                // Set up click handlers for the tabs
                $(document).on('click', '[data-toggle="tabajax"]', function (e) {
                    e.preventDefault();
                    var $this = $(this),
                        loadurl = $this.attr('data-href'),
                        targ = $this.attr('data-target');

                    // Show loading indicator
                    $(targ).html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');

                    // Load the tab content via AJAX
                    $.post(loadurl, { '_token': $('meta[name=csrf-token]').attr('content') }, function (data) {
                        $(targ).html(data);
                    });

                    // Update active tab
                    $('.nav-item').find('a.active').removeClass('active');
                    $this.addClass('active');

                    return false;
                });
            });
        </script>
    @endsection
</x-master-layout>