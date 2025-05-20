@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'value' => '',
    'placeholder' => 'Write something...',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'height' => '200px',
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
            content: @js($value),
            editor: null,
            placeholder: '{{ $placeholder }}',
            disabled: {{ $disabled ? 'true' : 'false' }},
            name: '{{ $name }}',
            id: '{{ $id }}',
            height: '{{ $height }}',
            init() {
                this.$nextTick(() => {
                    this.initQuill();
                    
                    // Create a bridge to the old tinymce API
                    window.tinymceEditor = window.tinymceEditor || function(selector, options, callback) {
                        // This is a compatibility function for old code
                        if (callback && typeof callback === 'function') {
                            // Create a fake editor object
                            const fakeEditor = {
                                getContent: () => this.content,
                                setContent: (content) => {
                                    this.content = content;
                                    if (this.editor) {
                                        this.editor.root.innerHTML = content;
                                    }
                                },
                                on: (event, callback) => {
                                    // Do nothing, just for compatibility
                                }
                            };
                            
                            // Call the callback with our fake editor
                            callback(fakeEditor);
                        }
                    };
                });
            },
            initQuill() {
                if (window.Quill) {
                    const options = {
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                ['blockquote', 'code-block'],
                                [{ 'header': 1 }, { 'header': 2 }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                [{ 'script': 'sub' }, { 'script': 'super' }],
                                [{ 'indent': '-1' }, { 'indent': '+1' }],
                                [{ 'direction': 'rtl' }],
                                [{ 'size': ['small', false, 'large', 'huge'] }],
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'font': [] }],
                                [{ 'align': [] }],
                                ['clean'],
                                ['link', 'image']
                            ]
                        },
                        placeholder: this.placeholder,
                        readOnly: this.disabled,
                        theme: 'snow'
                    };
                    
                    const container = this.$refs.editor;
                    this.editor = new Quill(container, options);
                    
                    // Set initial content
                    if (this.content) {
                        this.editor.root.innerHTML = this.content;
                    }
                    
                    // Set editor height
                    container.style.height = this.height;
                    
                    // Handle text change
                    this.editor.on('text-change', () => {
                        const html = this.editor.root.innerHTML;
                        this.content = html === '<p><br></p>' ? '' : html;
                        this.dispatchChangeEvent();
                    });
                } else {
                    console.error('Quill is not loaded. Please include Quill library.');
                }
            },
            dispatchChangeEvent() {
                this.$dispatch('rich-text-change', {
                    name: this.name,
                    value: this.content
                });
                
                // Dispatch a native change event for jQuery handlers
                const event = new Event('change');
                document.getElementById(this.id)?.dispatchEvent(event);
                
                @if($onChange)
                {{ $onChange }}(this.content);
                @endif
            }
        }"
        class="rich-text-editor-wrapper"
    >
        <div x-ref="editor" class="quill-editor"></div>
        <textarea 
            id="{{ $id }}"
            name="{{ $name }}"
            x-bind:value="content"
            style="display: none;"
            @if($required) required @endif
        ></textarea>
    </div>
</div>

@once
    @push('styles')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .rich-text-editor-wrapper {
            margin-bottom: 1rem;
        }
        
        .rich-text-editor-wrapper .ql-toolbar {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            background-color: #f8f9fa;
        }
        
        .rich-text-editor-wrapper .ql-container {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            font-size: 1rem;
        }
        
        .rich-text-editor-wrapper .ql-editor {
            min-height: 100px;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    @endpush
@endonce
