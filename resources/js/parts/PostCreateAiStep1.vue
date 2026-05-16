<template>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Title</label>
          <input v-model="formData.title" type="text" class="form-control" />
          <FormError :error="validationErrors.title" />
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label>Structure</label>
          <textarea rows="7" v-model="formData.structure" class="form-control"></textarea>
          <FormError :error="validationErrors.structure" />
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group mb-1">
          <div class="d-flex align-items-center mb-2">
            <label class="m-0 mr-2">Prompt</label>
            <select v-model="formData.selected_prompt">
              <template v-for="prompt in prompts">
                <option :value="prompt.id">{{ prompt.name }}</option>
              </template>
            </select>
          </div>
          <textarea rows="11" v-model="formData.prompt" class="form-control"></textarea>
          <FormError :error="validationErrors.prompt" />
        </div>
        <div class="d-flex align-items-center mb-2">
          <div class="checkbox mr-1 my-1" style="min-width: 150px">
            <label class="m-0">
              <input v-model="formData.save_prompt" type="checkbox" />
              Save prompt
            </label>
          </div>
          <div v-if="formData.save_prompt">
            <input v-model="formData.save_prompt_name" type="text" class="form-control" placeholder="Prompt Name" />
            <FormError :error="validationErrors.save_prompt_name" />
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label>Model</label>
          <select v-model="formData.model" name="" class="form-control">
            <template v-for="model in models">
              <option :value="model.id">{{ model.name }}</option>
            </template>
          </select>
          <FormError :error="validationErrors.model" />
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <button class="btn btn-success min-w-100" @click="generate()">
      <template v-if="isLoading"> Loading... </template>
      <template v-else> Generate </template>
    </button>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import helpers from '@/helpers';

const props = defineProps(['prompts', 'models', 'generateUrl']);
const emits = defineEmits(['generated']);
const validationErrors = ref({});
const isLoading = ref(false);
const formData = ref({
  title: '',
  structure: '',
  prompt: props.prompts[0].value,
  selected_prompt: props.prompts[0].id,
  model: props.models[0].id,
});

const generate = async () => {
  validationErrors.value = {};

  isLoading.value = true;
  const data = {
    ...formData.value,
    save_prompt: formData.value.save_prompt ? 1 : 0,
  };
  try {
    const response = await axios.post(props.generateUrl, data);
    emits('generated', {
      title: formData.value.title,
      content: response.data.data,
    });
  } catch (error) {
    if (error.response.status == 422) {
      helpers.showToast(error.response.data.message, false);
      validationErrors.value = error.response.data.errors;
    } else {
      helpers.showError('', error.response.data.message);
    }
  }

  isLoading.value = false;
};

watch(
  () => formData.value.selected_prompt,
  newPromptId => {
    formData.value.prompt = props.prompts.find(p => p.id == newPromptId).value;
  }
);
</script>
