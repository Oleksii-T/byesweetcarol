<template>
  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center">
        <template v-for="(step, stepId) in steps">
          <h5
            :class="['m-0 mr-2', step.is_active ? '' : 'text-secondary']"
            @click="goToStep(stepId)"
            style="cursor: pointer"
          >
            {{ step.name }}
          </h5>
          <span v-if="stepId != 'step_3'" class="mr-2">></span>
        </template>
      </div>
    </div>
    <template v-if="props.dataprops">
      <PostCreateAiStep1
        v-if="steps.step_1.is_active"
        :prompts="props.dataprops.prompts"
        :models="dataprops.models"
        :generateUrl="dataprops.generateUrl"
        @generated="finishStep1"
      />
      <PostCreateAiStep2
        v-if="steps.step_2.is_active"
        :postGeneratedContent="postGeneratedContent"
        :translateUrl="props.dataprops.translateUrl"
        :translators="props.dataprops.translators"
        :languages="props.dataprops.languages"
        @finish="finishStep2"
      />
      <PostCreateAiStep3 v-if="steps.step_3.is_active" v-model="postTranslatedContent" @finish="finishStep3" />
    </template>
  </div>
</template>

<script setup>
import PostCreateAiStep1 from '@/parts/PostCreateAiStep1.vue';
import PostCreateAiStep2 from '@/parts/PostCreateAiStep2.vue';
import PostCreateAiStep3 from '@/parts/PostCreateAiStep3.vue';
import { ref, defineProps, watch } from 'vue';
import helpers from '@/helpers';

const props = defineProps(['dataprops']);

const title = ref('');
const postGeneratedContent = ref('');
const postTranslatedContent = ref('');
const postEditedContent = ref('');
const steps = ref({
  step_1: {
    name: 'General info',
    available: true,
    is_active: true,
  },
  step_2: {
    name: 'Editing',
    available: false,
    is_active: false,
  },
  step_3: {
    name: 'Translation',
    available: false,
    is_active: false,
  },
});

const finishStep1 = data => {
  postGeneratedContent.value = data.content;
  title.value = data.title;
  steps.value.step_2.available = true;
  goToStep('step_2');
};

const finishStep2 = data => {
  postGeneratedContent.value = data.content;

  if (data.translated) {
    steps.value.step_3.available = true;
    postTranslatedContent.value = data.translatedContent;
    goToStep('step_3');
    return;
  }

  postEditedContent.value = data.content;
  create();
};

const finishStep3 = data => {
  postTranslatedContent.value = data;
  postEditedContent.value = data;
  create();
};

const goToStep = stepId => {
  if (!steps.value[stepId].available) {
    helpers.showToast('Step is unavailable', false);
    return;
  }

  for (const stepId in steps.value) {
    steps.value[stepId].is_active = false;
  }

  steps.value[stepId].is_active = true;
};

const create = async () => {
  try {
    const response = await axios.post(props.dataprops.createUrl, {
      title: title.value,
      content: postEditedContent.value,
    });
    window.location.href = response.data.data.redirect;
  } catch (error) {
    if (error.response.status == 422) {
      helpers.showToast(error.response.data.message, false);
    } else {
      helpers.showError('', error.response.data.message);
    }
  }
};
</script>
