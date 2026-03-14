<template>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Content</label>
          <SummernoteEditor v-model="formData.content" />
          <span v-if="validationErrors.content" class="input-error">{{ validationErrors.content[0] }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <button class="btn btn-success min-w-100" @click="finish()">
      <template v-if="isLoading"> Loading... </template>
      <template v-else> Create Post </template>
    </button>
  </div>
</template>

<script setup>
import SummernoteEditor from '@/parts/SummernoteEditor.vue';
import { ref, defineProps, defineEmits, watch } from 'vue';
import helpers from '@/helpers';

const props = defineProps(['modelValue']);
const emits = defineEmits(['update:modelValue', 'finish']);
const validationErrors = ref({});
const isLoading = ref(false);
const formData = ref({
  content: props.modelValue,
});

const finish = () => {
  emits('finish', formData.value.content);
};
</script>
