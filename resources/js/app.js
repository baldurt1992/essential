import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Tooltip from 'primevue/tooltip';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import Chip from 'primevue/chip';
import Paginator from 'primevue/paginator';

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
app.component('Textarea', Textarea);
app.component('Password', Password);
app.component('Button', Button);
app.component('Message', Message);
app.component('Chip', Chip);
app.component('Paginator', Paginator);

app.directive('tooltip', Tooltip);

app.use(router);

// Esperar a que el router esté listo antes de montar la app
// Esto asegura que las rutas se resuelvan correctamente en cargas iniciales
router.isReady().then(() => {
    app.mount('#app');
}).catch((error) => {
    console.error('[app] Error al inicializar router:', error);
    // Montar de todas formas para evitar que la app no se cargue
    app.mount('#app');
});
