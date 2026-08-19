<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  error: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  autocomplete: { type: String, default: undefined },
  required: { type: Boolean, default: false },
});
 
defineEmits(['update:modelValue']);
</script>
 
<template>
  <div>
    <label v-if="label" class="mb-1 block text-sm font-medium text-gray-700">
      {{ label }}
    </label>
 
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      :required="required"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
      :class="error ? 'border-red-400' : 'border-gray-300'"
    />
 
    <p v-if="error" class="mt-1 text-xs text-red-600">
      {{ error }}
    </p>
  </div>
</template>
