<template>
  <div class="rich-text-editor">
    <div ref="editor" class="quill-editor"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Write something...'
  },
  readOnly: {
    type: Boolean,
    default: false
  },
  toolbar: {
    type: Array,
    default: () => [
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
  height: {
    type: String,
    default: '200px'
  }
});

const emit = defineEmits(['update:modelValue', 'editor-change', 'focus', 'blur']);
const editor = ref(null);
let quill = null;

onMounted(() => {
  const options = {
    modules: {
      toolbar: props.toolbar
    },
    placeholder: props.placeholder,
    readOnly: props.readOnly,
    theme: 'snow'
  };

  quill = new Quill(editor.value, options);
  
  // Set initial content
  if (props.modelValue) {
    quill.root.innerHTML = props.modelValue;
  }

  // Set editor height
  editor.value.style.height = props.height;

  // Handle text change
  quill.on('text-change', () => {
    const html = quill.root.innerHTML;
    if (html === '<p><br></p>') {
      emit('update:modelValue', '');
    } else {
      emit('update:modelValue', html);
    }
    emit('editor-change', quill);
  });

  // Handle focus and blur events
  quill.on('selection-change', (range) => {
    if (range) {
      emit('focus', quill);
    } else {
      emit('blur', quill);
    }
  });
});

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (quill && newValue !== quill.root.innerHTML) {
    quill.root.innerHTML = newValue || '';
  }
});

// Watch for readOnly changes
watch(() => props.readOnly, (newValue) => {
  if (quill) {
    quill.enable(!newValue);
  }
});

// Clean up on component unmount
onBeforeUnmount(() => {
  if (quill) {
    quill.off('text-change');
    quill.off('selection-change');
  }
});

// Expose the Quill instance to parent components
defineExpose({
  getQuill: () => quill
});
</script>

<style scoped>
.rich-text-editor {
  border-radius: 4px;
  margin-bottom: 1rem;
}

:deep(.ql-toolbar) {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  background-color: #f8f9fa;
}

:deep(.ql-container) {
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  font-size: 1rem;
}

:deep(.ql-editor) {
  min-height: 100px;
  max-height: 500px;
  overflow-y: auto;
}
</style>
