<template>
  <div class="date-picker">
    <n-date-picker
      v-model:value="selectedDate"
      :type="type"
      :format="format"
      :placeholder="placeholder"
      :disabled="disabled"
      :clearable="clearable"
      :size="size"
      :is-date-disabled="isDateDisabled"
      @update:value="handleChange"
      @focus="$emit('focus', $event)"
      @blur="$emit('blur', $event)"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { NDatePicker } from 'naive-ui';
import dayjs from 'dayjs';

const props = defineProps({
  modelValue: {
    type: [String, Number, Date, Array],
    default: null
  },
  type: {
    type: String,
    default: 'date',
    validator: (value) => [
      'date', 'datetime', 'daterange', 'datetimerange', 
      'month', 'year', 'quarter', 'monthrange', 'quarterrange', 'yearrange'
    ].includes(value)
  },
  format: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select date'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: true
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  disabledDates: {
    type: [Array, Function],
    default: () => []
  }
});

const emit = defineEmits([
  'update:modelValue',
  'change',
  'focus',
  'blur'
]);

// Convert input value to timestamp for Naive UI
const convertToTimestamp = (value) => {
  if (!value) return null;
  
  if (Array.isArray(value)) {
    return value.map(v => {
      if (v instanceof Date) return v.getTime();
      if (typeof v === 'string') return dayjs(v).valueOf();
      return v;
    });
  }
  
  if (value instanceof Date) return value.getTime();
  if (typeof value === 'string') return dayjs(value).valueOf();
  return value;
};

// Initialize selected date
const selectedDate = ref(convertToTimestamp(props.modelValue));

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  selectedDate.value = convertToTimestamp(newValue);
});

// Format for display based on type
const defaultFormat = computed(() => {
  switch (props.type) {
    case 'date': return 'YYYY-MM-DD';
    case 'datetime': return 'YYYY-MM-DD HH:mm:ss';
    case 'daterange': return 'YYYY-MM-DD';
    case 'datetimerange': return 'YYYY-MM-DD HH:mm:ss';
    case 'month': return 'YYYY-MM';
    case 'year': return 'YYYY';
    case 'quarter': return 'YYYY-[Q]Q';
    default: return 'YYYY-MM-DD';
  }
});

// Handle date disabled function
const isDateDisabled = (timestamp) => {
  if (!props.disabledDates) return false;
  
  if (typeof props.disabledDates === 'function') {
    return props.disabledDates(timestamp);
  }
  
  if (Array.isArray(props.disabledDates)) {
    return props.disabledDates.some(date => {
      const disabledTimestamp = convertToTimestamp(date);
      return dayjs(timestamp).isSame(dayjs(disabledTimestamp), 'day');
    });
  }
  
  return false;
};

// Handle change event
const handleChange = (value) => {
  let formattedValue = value;
  
  // Convert timestamp back to string format if needed
  if (value !== null && typeof value !== 'undefined') {
    const format = props.format || defaultFormat.value;
    
    if (Array.isArray(value)) {
      formattedValue = value.map(v => v !== null ? dayjs(v).format(format) : null);
    } else {
      formattedValue = dayjs(value).format(format);
    }
  }
  
  emit('update:modelValue', formattedValue);
  emit('change', formattedValue);
};

// Legacy compatibility method for moment.js
const formatDate = (date, format) => {
  if (!date) return '';
  return dayjs(date).format(format || defaultFormat.value);
};

// Expose methods to parent components
defineExpose({
  formatDate,
  dayjs
});
</script>

<style scoped>
.date-picker {
  width: 100%;
  margin-bottom: 1rem;
}
</style>
