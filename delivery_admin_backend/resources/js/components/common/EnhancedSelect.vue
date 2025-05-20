<template>
  <div class="enhanced-select">
    <n-select
      v-model:value="selectedValue"
      :options="formattedOptions"
      :placeholder="placeholder"
      :multiple="multiple"
      :clearable="clearable"
      :filterable="filterable"
      :loading="loading"
      :disabled="disabled"
      :size="size"
      :remote="remote"
      :remote-method="remoteMethod"
      @update:value="handleChange"
      @focus="$emit('focus', $event)"
      @blur="$emit('blur', $event)"
      @search="$emit('search', $event)"
      @clear="$emit('clear')"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { NSelect } from 'naive-ui';

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: null
  },
  options: {
    type: Array,
    default: () => []
  },
  valueField: {
    type: String,
    default: 'value'
  },
  labelField: {
    type: String,
    default: 'label'
  },
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  multiple: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: true
  },
  filterable: {
    type: Boolean,
    default: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  remote: {
    type: Boolean,
    default: false
  },
  remoteMethod: {
    type: Function,
    default: null
  }
});

const emit = defineEmits([
  'update:modelValue',
  'change',
  'focus',
  'blur',
  'search',
  'clear'
]);

const selectedValue = ref(props.modelValue);

// Format options to match Naive UI's expected format
const formattedOptions = computed(() => {
  return props.options.map(option => {
    if (typeof option === 'object') {
      return {
        label: option[props.labelField] || option.text || option.name || option.title,
        value: option[props.valueField] || option.id,
        disabled: option.disabled || false
      };
    } else {
      return {
        label: option.toString(),
        value: option
      };
    }
  });
});

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  selectedValue.value = newValue;
});

// Handle internal changes
const handleChange = (value) => {
  emit('update:modelValue', value);
  emit('change', value);
};

// Legacy compatibility method for Select2
const setupLegacyBridge = (element) => {
  if (typeof window !== 'undefined' && element) {
    // Create a fake select2 API on the element
    element.select2 = {
      val: (value) => {
        if (value === undefined) {
          return selectedValue.value;
        }
        selectedValue.value = value;
        return element;
      },
      trigger: (event) => {
        // Do nothing, just for compatibility
        return element;
      }
    };
  }
};

// Expose methods to parent components
defineExpose({
  focus: () => {
    // Focus implementation would depend on getting a ref to the NSelect component
  },
  blur: () => {
    // Blur implementation would depend on getting a ref to the NSelect component
  },
  clear: () => {
    selectedValue.value = props.multiple ? [] : null;
  },
  setupLegacyBridge
});
</script>

<style scoped>
.enhanced-select {
  width: 100%;
  margin-bottom: 1rem;
}
</style>
