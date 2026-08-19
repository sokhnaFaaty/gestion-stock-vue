<script setup>
defineProps({
  variant: { type: String, default: 'primary' },
  type: { type: String, default: 'button' },
});
</script>
 
<template>
  <button
    :type="type"
    class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-50"
    :class="{
      'bg-indigo-600 text-white hover:bg-indigo-700': variant === 'primary',
      'bg-gray-100 text-gray-700 hover:bg-gray-200': variant === 'secondary',
      'bg-red-600 text-white hover:bg-red-700': variant === 'danger',
    }"
  >
    <slot name="icone" />
    <slot />
  </button>
</template>
