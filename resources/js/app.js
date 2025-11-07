import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import 'primeflex/primeflex.css';
import 'primeicons/primeicons.css';

// Importar componentes principales
import App from './components/App.vue';

const app = createApp(App);

// Configurar PrimeVue
app.use(PrimeVue, {
    unstyled: false,
});

app.mount('#app');
