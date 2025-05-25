@foreach($items as $item)
    <li @lm_attrs($item) @lm_endattrs>
        @if($item->link)
            <a @lm_attrs($item->link) class="nav-link" @lm_endattrs href="{!! $item->url() !!}">
                {!! $item->title !!}
            </a>
        @else
            <span class="navbar-text">{!! $item->title !!}</span>
        @endif

        @if($item->hasChildren())
            <ul class="submenu">
                @include('vendor.laravel-menu.bootstrap-navbar-child-items', ['items' => $item->children()])
            </ul>
        @endif
    </li>
    @if($item->divider)
        <li{!! Lavary\Menu\Builder::attributes($item->divider) !!}>
            </li>
    @endif
@endforeach