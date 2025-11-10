import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Tooltip from 'primevue/tooltip';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import Chip from 'primevue/chip';

// Importar componentes principales
import App from './components/App.vue';
import router from './router';

const app = createApp(App);

// Configurar PrimeVue con tema Aura
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: 'body.dark-mode'
        }
    },
});

app.use(ToastService);
app.use(ConfirmationService);

app.component('Dialog', Dialog);
app.component('InputText', InputText);
app.component('Password', Password);
app.component('Button', Button);
app.component('Message', Message);
app.component('Chip', Chip);

app.directive('tooltip', Tooltip);

app.use(router);

app.mount('#app');
