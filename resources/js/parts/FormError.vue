<template>
  <span v-if="props.errors" class="input-error">{{ props.errors[0] }}</span>
</template>

<script setup>
const props = defineProps(['errors']);
</script>
