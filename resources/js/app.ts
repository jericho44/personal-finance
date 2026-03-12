import { createApp } from 'vue'
import MainApp from "@components/App.vue";
import router from '@routes/index'
import { createPinia } from 'pinia'

import 'vue3-toastify/dist/index.css';

import VueDatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';

const pinia = createPinia()

const app = createApp(MainApp);

app.use(pinia);
app.use(router);
app.component("AppDatepicker", VueDatePicker);


app.mount("#app");
