@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'value' => '',
    'placeholder' => 'Select date',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'format' => 'YYYY-MM-DD',
    'type' => 'date', // date, datetime, daterange, month, year
    'minDate' => null,
    'maxDate' => null,
    'onChange' => '',
])

@php
    $id = $id ?? $name;
@endphp

<div class="form-group {{ $class }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div 
        x-data="{
            selectedDate: @js($value),
            placeholder: '{{ $placeholder }}',
            disabled: {{ $disabled ? 'true' : 'false' }},
            name: '{{ $name }}',
            id: '{{ $id }}',
            format: '{{ $format }}',
            type: '{{ $type }}',
            minDate: @js($minDate),
            maxDate: @js($maxDate),
            flatpickrInstance: null,
            init() {
                this.$nextTick(() => {
                    this.initFlatpickr();
                });
            },
            initFlatpickr() {
                if (window.flatpickr) {
                    const options = {
                        dateFormat: this.convertFormatToFlatpickr(this.format),
                        allowInput: true,
                        altInput: true,
                        altFormat: this.convertFormatToFlatpickr(this.format),
                        enableTime: this.type === 'datetime',
                        mode: this.type.includes('range') ? 'range' : 'single',
                        disabled: this.disabled,
                        placeholder: this.placeholder,
                        onChange: (selectedDates, dateStr) => {
                            this.selectedDate = dateStr;
                            this.dispatchChangeEvent();
                        }
                    };
                    
                    if (this.minDate) {
                        options.minDate = this.minDate;
                    }
                    
                    if (this.maxDate) {
                        options.maxDate = this.maxDate;
                    }
                    
                    if (this.type === 'month') {
                        options.plugins = [new window.monthSelectPlugin({
                            shorthand: true,
                            dateFormat: 'Y-m',
                            altFormat: 'F Y'
                        })];
                    }
                    
                    if (this.type === 'year') {
                        options.plugins = [new window.yearSelectPlugin({
                            shorthand: true,
                            dateFormat: 'Y',
                            altFormat: 'Y'
                        })];
                    }
                    
                    this.flatpickrInstance = flatpickr(this.$refs.input, options);
                    
                    // Create a bridge to the old moment.js API
                    window.moment = window.moment || function(date, format) {
                        return {
                            format: (outputFormat) => {
                                if (!date) return '';
                                
                                // Simple format conversion for common formats
                                if (outputFormat === 'YYYY-MM-DD') {
                                    return date;
                                }
                                
                                if (outputFormat === 'MM/DD/YYYY') {
                                    const parts = date.split('-');
                                    return `${parts[1]}/${parts[2]}/${parts[0]}`;
                                }
                                
                                if (outputFormat === 'DD/MM/YYYY') {
                                    const parts = date.split('-');
                                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                                }
                                
                                // Default to original date
                                return date;
                            }
                        };
                    };
                } else {
                    console.error('Flatpickr is not loaded. Please include Flatpickr library.');
                }
            },
            convertFormatToFlatpickr(format) {
                // Convert moment.js/dayjs format to flatpickr format
                const conversions = {
                    'YYYY': 'Y',
                    'YY': 'y',
                    'MM': 'm',
                    'M': 'n',
                    'DD': 'd',
                    'D': 'j',
                    'HH': 'H',
                    'H': 'H',
                    'mm': 'i',
                    'm': 'i',
                    'ss': 'S',
                    's': 'S',
                    'A': 'K',
                    'a': 'K'
                };
                
                let flatpickrFormat = format;
                Object.keys(conversions).forEach(key => {
                    flatpickrFormat = flatpickrFormat.replace(key, conversions[key]);
                });
                
                return flatpickrFormat;
            },
            dispatchChangeEvent() {
                this.$dispatch('date-picker-change', {
                    name: this.name,
                    value: this.selectedDate
                });
                
                // Dispatch a native change event for jQuery handlers
                const event = new Event('change');
                document.getElementById(this.id)?.dispatchEvent(event);
                
                @if($onChange)
                {{ $onChange }}(this.selectedDate);
                @endif
            }
        }"
        class="date-picker-wrapper"
    >
        <input 
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            x-ref="input"
            x-bind:value="selectedDate"
            x-bind:placeholder="placeholder"
            x-bind:disabled="disabled"
            class="form-control"
            @if($required) required @endif
        />
    </div>
</div>

@once
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .date-picker-wrapper {
            position: relative;
        }
        
        .flatpickr-calendar {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 3px 13px rgba(0,0,0,0.08);
        }
        
        .flatpickr-day.selected {
            background: #3b82f6;
            border-color: #3b82f6;
        }
        
        .flatpickr-day.selected:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script>
        // Create a year select plugin for flatpickr
        window.yearSelectPlugin = function(config) {
            return function(fp) {
                return {
                    onReady: function() {
                        fp.config.dateFormat = config.dateFormat || 'Y';
                        fp.config.altFormat = config.altFormat || 'Y';
                        fp.config.mode = 'single';
                        
                        // Modify the calendar to show only years
                        fp.yearElements.forEach(yearElem => {
                            yearElem.addEventListener('click', function() {
                                const year = parseInt(yearElem.textContent);
                                fp.setDate(new Date(year, 0, 1));
                                fp.close();
                            });
                        });
                    }
                };
            };
        };
    </script>
    @endpush
@endonce
