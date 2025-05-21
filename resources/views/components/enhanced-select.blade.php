@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'value' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false,
    'multiple' => false,
    'disabled' => false,
    'class' => '',
    'valueField' => 'id',
    'labelField' => 'name',
    'onChange' => '',
])

@php
    $id = $id ?? $name;
    
    // Format options for JSON
    $formattedOptions = collect($options)->map(function($option) use ($valueField, $labelField) {
        if (is_array($option)) {
            return [
                'value' => $option[$valueField] ?? $option['id'] ?? '',
                'label' => $option[$labelField] ?? $option['name'] ?? $option['text'] ?? '',
                'disabled' => $option['disabled'] ?? false
            ];
        } elseif (is_object($option)) {
            return [
                'value' => $option->{$valueField} ?? $option->id ?? '',
                'label' => $option->{$labelField} ?? $option->name ?? $option->text ?? '',
                'disabled' => $option->disabled ?? false
            ];
        } else {
            return [
                'value' => $option,
                'label' => $option
            ];
        }
    })->toJson();
    
    // Format value for JSON
    $jsonValue = json_encode($value);
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
            options: {{ $formattedOptions }},
            value: {{ $jsonValue }},
            multiple: {{ $multiple ? 'true' : 'false' }},
            placeholder: '{{ $placeholder }}',
            disabled: {{ $disabled ? 'true' : 'false' }},
            name: '{{ $name }}',
            id: '{{ $id }}',
            init() {
                // Initialize the select component
                this.$nextTick(() => {
                    // Create a bridge to the old select2 API
                    const element = document.getElementById(this.id);
                    if (element) {
                        element.select2 = {
                            val: (value) => {
                                if (value === undefined) {
                                    return this.value;
                                }
                                this.value = value;
                                this.dispatchChangeEvent();
                                return element;
                            },
                            trigger: (event) => {
                                // Do nothing, just for compatibility
                                return element;
                            }
                        };
                    }
                });
            },
            dispatchChangeEvent() {
                this.$dispatch('enhanced-select-change', {
                    name: this.name,
                    value: this.value
                });
                
                // Dispatch a native change event for jQuery handlers
                const event = new Event('change');
                document.getElementById(this.id)?.dispatchEvent(event);
                
                // Dispatch a select2:select event for compatibility
                const select2Event = new CustomEvent('select2:select', {
                    detail: { 
                        id: this.value,
                        text: this.getSelectedText()
                    }
                });
                document.getElementById(this.id)?.dispatchEvent(select2Event);
            },
            getSelectedText() {
                if (this.multiple) {
                    return this.options
                        .filter(option => this.value.includes(option.value))
                        .map(option => option.label)
                        .join(', ');
                } else {
                    const selected = this.options.find(option => option.value == this.value);
                    return selected ? selected.label : '';
                }
            }
        }"
        x-on:enhanced-select-change="@if($onChange) {{ $onChange }}($event.detail) @endif"
        class="enhanced-select-wrapper"
    >
        <select 
            id="{{ $id }}"
            name="{{ $name }}"
            x-bind:multiple="multiple"
            x-bind:disabled="disabled"
            x-on:change="value = $event.target.value; dispatchChangeEvent()"
            class="form-control"
            @if($required) required @endif
        >
            <option value="">{{ $placeholder }}</option>
            <template x-for="option in options" :key="option.value">
                <option 
                    x-bind:value="option.value"
                    x-bind:selected="multiple ? (value && value.includes(option.value)) : value == option.value"
                    x-bind:disabled="option.disabled"
                    x-text="option.label"
                ></option>
            </template>
        </select>
        
        <!-- Hidden input for compatibility with old code -->
        <input type="hidden" name="{{ $name }}_selected_text" x-bind:value="getSelectedText()" />
    </div>
</div>

@once
    @push('scripts')
    <script>
        // Initialize all enhanced-select components with select2 for backward compatibility
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.enhanced-select-wrapper select');
            selects.forEach(select => {
                if (window.jQuery && window.jQuery.fn.select2) {
                    // Initialize with select2 for backward compatibility
                    window.jQuery(select).select2({
                        placeholder: select.getAttribute('placeholder') || 'Select an option',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    // Sync the Alpine.js value with select2
                    window.jQuery(select).on('select2:select select2:unselect', function(e) {
                        const event = new Event('change', { bubbles: true });
                        select.dispatchEvent(event);
                    });
                }
            });
        });
    </script>
    @endpush
@endonce
