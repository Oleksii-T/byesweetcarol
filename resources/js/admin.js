import './bootstrap';

import { createApp } from 'vue/dist/vue.esm-bundler';
import PostContent from './components/Admin/PostContent.vue';
import PostCreateAi from './components/Admin/PostCreateAi/PostCreateAi.vue';
import SummernoteEditor from './parts/SummernoteEditor.vue';
import RichImageInput from './parts/RichImageInput.vue';
import Swal from 'sweetalert2';
// import SummernoteEditor from 'vue3-summernote-editor';
// import $ from "jquery";
// import CodeEditor from 'simple-code-editor';
// import { VueEditor } from "vue2-editor";
import helpers from './helpers';

// window.$ = window.jQuery = $;

const app = createApp({});
app.component('post-content', PostContent);
app.component('post-create-ai', PostCreateAi);
// app.component('VueEditor', VueEditor);
// app.component('CodeEditor', CodeEditor);
app.component('SummernoteEditor', SummernoteEditor);
app.component('RichImageInput', RichImageInput);
app.provide('helpers', helpers);
app.provide('alert', Swal);
app.mount('#app');
