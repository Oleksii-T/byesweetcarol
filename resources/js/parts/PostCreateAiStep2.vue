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
      <div class="col-md-12">
        <div class="d-flex align-items-center mb-2">
          <div class="checkbox mr-1 my-1" style="min-width: 100px">
            <label class="m-0">
              <input v-model="formData.translate" type="checkbox" />
              Translate
            </label>
          </div>
        </div>
      </div>
      <div class="col-md-12" v-if="formData.translate">
        <div class="form-group">
          <select v-model="formData.language" class="form-control">
            <template v-for="language in props.languages">
              <option :value="language">{{ language }}</option>
            </template>
          </select>
          <FormError :error="validationErrors.language" />
        </div>
      </div>
      <div class="col-md-12" v-if="formData.translate">
        <div class="form-group">
          <select v-model="formData.translator" class="form-control">
            <template v-for="translator in props.translators">
              <option :value="translator.id">{{ translator.name }}</option>
            </template>
          </select>
          <FormError :error="validationErrors.language" />
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <button class="btn btn-success min-w-100" @click="finish()">
      <template v-if="isLoading"> Loading... </template>
      <template v-else-if="formData.translate"> Translate </template>
      <template v-else> Create Post </template>
    </button>
  </div>
</template>

<script setup>
import SummernoteEditor from '@/parts/SummernoteEditor.vue';
import { ref, defineProps, defineEmits, watch, onMounted } from 'vue';
import helpers from '@/helpers';

const props = defineProps(['postGeneratedContent', 'translateUrl', 'translators', 'languages']);
const emits = defineEmits(['finish']);
const validationErrors = ref({});
const isLoading = ref(false);
const formData = ref({
  translate: true,
  content: props.postGeneratedContent,
  language: props.languages[0],
  translator: props.translators[0].id,
});

const finish = async () => {
  isLoading.value = true;

  if (!formData.value.translate) {
    emits('finish', {
      content: formData.value.content,
      translatedContent: null,
      translated: false,
    });
    return;
  }

  try {
    const response = await axios.post(props.translateUrl, {
      text: formData.value.content,
      language: formData.value.language,
      translator: formData.value.translator,
    });
    emits('finish', {
      content: formData.value.content,
      translatedContent: response.data.data,
      translated: true,
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
  () => props.modelValue,
  newValue => {
    formData.value.content = newValue;
  }
);

onMounted(() => {
  //
});
</script>
