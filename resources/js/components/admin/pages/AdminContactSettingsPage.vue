<template>
    <section class="admin-page">
        <header class="admin-page__header">
            <div>
                <h2 class="admin-page__title">Información de contacto</h2>
                <p class="admin-page__subtitle">
                    Mantén actualizados los datos que mostramos en la landing y en los correos automáticos.
                </p>
            </div>

            <button type="button" class="essential-button essential-button--ghost" @click="resetForm"
                :disabled="isSaving || !hasChanges">
                Restablecer
            </button>
        </header>

        <div class="admin-page__content">
            <div v-if="isLoading" class="admin-loader">
                <span class="admin-loader__spinner"></span>
                <p class="admin-loader__text">Cargando datos de contacto…</p>
            </div>

            <form v-else class="contact-form" @submit.prevent="handleSubmit">
                <section class="contact-form__section">
                    <header class="contact-form__section-header">
                        <h3>Datos principales</h3>
                        <p>Se muestran en el footer, en la página “Contacto” y en la firma de correos.</p>
                    </header>

                    <div class="contact-form__grid">
                        <div class="contact-form__field">
                            <label for="contact-email">Correo principal</label>
                            <InputText id="contact-email" v-model="form.email"
                                :class="{ 'p-invalid': fieldError('email') }" placeholder="hola@essentialinnovation.com"
                                @input="clearFieldError('email')" />
                            <small v-if="fieldError('email')" class="contact-form__error">{{ fieldError('email')
                                }}</small>
                        </div>

                        <div class="contact-form__field">
                            <label for="contact-phone">Teléfono</label>
                            <InputText id="contact-phone" v-model="form.phone" placeholder="+57 1 234 5678"
                                @input="clearFieldError('phone')" />
                        </div>

                        <div class="contact-form__field">
                            <label for="contact-whatsapp">WhatsApp</label>
                            <InputText id="contact-whatsapp" v-model="form.whatsapp" placeholder="+57 300 123 4567"
                                @input="clearFieldError('whatsapp')" />
                        </div>

                        <div class="contact-form__field">
                            <label for="contact-support-hours">Horario de atención</label>
                            <InputText id="contact-support-hours" v-model="form.support_hours"
                                placeholder="Lunes a viernes · 09:00 a 18:00 (GMT-5)"
                                @input="clearFieldError('support_hours')" />
                        </div>
                    </div>
                </section>

                <section class="contact-form__section">
                    <header class="contact-form__section-header">
                        <h3>Ubicación</h3>
                        <p>Información contextual para clientes sobre zona horaria y origen del estudio.</p>
                    </header>

                    <div class="contact-form__grid contact-form__grid--two">
                        <div class="contact-form__field">
                            <label for="contact-location-line-one">Dirección / ciudad</label>
                            <InputText id="contact-location-line-one" v-model="form.location_line_one"
                                placeholder="Bogotá, Colombia" @input="clearFieldError('location_line_one')" />
                        </div>

                        <div class="contact-form__field">
                            <label for="contact-location-line-two">Referencia adicional</label>
                            <InputText id="contact-location-line-two" v-model="form.location_line_two"
                                placeholder="Atendemos proyectos globales · Zona GMT-5"
                                @input="clearFieldError('location_line_two')" />
                        </div>
                    </div>
                </section>

                <section class="contact-form__section">
                    <header class="contact-form__section-header">
                        <h3>Presencia digital</h3>
                        <p>Agrega únicamente redes que cuenten con icono disponible en la librería.</p>
                    </header>

                    <div class="contact-socials">
                        <div v-if="!form.social_links.length" class="contact-socials__empty">
                            <i class="pi pi-share-alt"></i>
                            <p>Aún no hay redes configuradas. Añade tus canales oficiales.</p>
                        </div>

                        <div v-for="(link, index) in form.social_links" :key="`social-${link.network}-${index}`"
                            class="contact-socials__item">
                            <div class="contact-socials__item-header">
                                <span class="contact-socials__icon">
                                    <i :class="['pi', networkMeta(link.network).icon]" />
                                </span>

                                <Dropdown v-model="form.social_links[index].network"
                                    :options="getNetworkOptions(link.network)" option-label="label" option-value="value"
                                    class="contact-socials__dropdown"
                                    @update:model-value="setSocialNetwork(index, $event)" />

                                <button type="button" class="contact-socials__remove" @click="removeSocialLink(index)"
                                    :disabled="isSaving" aria-label="Eliminar red social">
                                    <i class="pi pi-times"></i>
                                </button>
                            </div>
                            <small v-if="socialError(index, 'network')" class="contact-form__error">
                                {{ socialError(index, 'network') }}
                            </small>

                            <InputText v-model="form.social_links[index].url"
                                :placeholder="networkMeta(link.network).placeholder"
                                :class="{ 'p-invalid': socialError(index, 'url') }"
                                @input="clearSocialError(index, 'url')" />
                            <small v-if="socialError(index, 'url')" class="contact-form__error">
                                {{ socialError(index, 'url') }}
                            </small>
                        </div>
                    </div>

                    <div class="contact-socials__actions">
                        <button type="button" class="essential-button essential-button--ghost" @click="addSocialLink"
                            :disabled="!availableNetworkOptions.length || isSaving">
                            <i class="pi pi-plus"></i>
                            <span>Agregar red</span>
                        </button>
                        <span v-if="!availableNetworkOptions.length" class="contact-socials__hint">
                            Ya añadiste todas las redes disponibles.
                        </span>
                    </div>
                </section>

                <section class="contact-form__section">
                    <header class="contact-form__section-header">
                        <h3>Mensaje personalizado</h3>
                        <p>Texto destacado en la página de contacto y en recordatorios automáticos.</p>
                    </header>

                    <Textarea id="contact-note" v-model="form.contact_note" auto-resize rows="4"
                        placeholder="Escríbenos para cotizar tu próximo lanzamiento, respondemos en menos de 24 horas."
                        @input="clearFieldError('contact_note')" />
                </section>

                <footer class="contact-form__actions">
                    <Message v-if="generalError" severity="error" :closable="false">
                        {{ generalError }}
                    </Message>

                    <button type="submit" class="essential-button essential-button--primary" :disabled="isSaving">
                        <span v-if="isSaving" class="contact-form__spinner"></span>
                        <span>Guardar cambios</span>
                    </button>
                </footer>
            </form>
        </div>
    </section>
</template>

<script setup>
    import { computed, onMounted, reactive, ref, watch } from 'vue';
    import InputText from 'primevue/inputtext';
    import Textarea from 'primevue/textarea';
    import Dropdown from 'primevue/dropdown';
    import Message from 'primevue/message';
    import { useToast } from 'primevue/usetoast';
    import { useAdminContactInformation } from '../../../composables/admin/useContactInformation';

    const SOCIAL_NETWORK_OPTIONS = [
        { value: 'website', label: 'Sitio web / Portafolio', icon: 'pi-globe', placeholder: 'https://essentialinnovation.com' },
        { value: 'instagram', label: 'Instagram', icon: 'pi-instagram', placeholder: 'https://instagram.com/usuario' },
        { value: 'facebook', label: 'Facebook', icon: 'pi-facebook', placeholder: 'https://facebook.com/usuario' },
        { value: 'linkedin', label: 'LinkedIn', icon: 'pi-linkedin', placeholder: 'https://linkedin.com/in/usuario' },
        { value: 'twitter', label: 'Twitter / X', icon: 'pi-twitter', placeholder: 'https://twitter.com/usuario' },
        { value: 'youtube', label: 'YouTube', icon: 'pi-youtube', placeholder: 'https://youtube.com/@canal' },
        { value: 'tiktok', label: 'TikTok', icon: 'pi-tiktok', placeholder: 'https://tiktok.com/@usuario' },
        { value: 'pinterest', label: 'Pinterest', icon: 'pi-pinterest', placeholder: 'https://pinterest.com/usuario' },
        { value: 'whatsapp', label: 'WhatsApp', icon: 'pi-whatsapp', placeholder: 'https://wa.me/numero' },
        { value: 'telegram', label: 'Telegram', icon: 'pi-telegram', placeholder: 'https://t.me/usuario' },
        { value: 'github', label: 'GitHub', icon: 'pi-github', placeholder: 'https://github.com/usuario' },
        { value: 'slack', label: 'Slack', icon: 'pi-slack', placeholder: 'https://slack.com/usuario' },
        { value: 'reddit', label: 'Reddit', icon: 'pi-reddit', placeholder: 'https://reddit.com/u/usuario' },
        { value: 'vimeo', label: 'Vimeo', icon: 'pi-vimeo', placeholder: 'https://vimeo.com/usuario' },
        { value: 'twitch', label: 'Twitch', icon: 'pi-twitch', placeholder: 'https://twitch.tv/usuario' },
    ];

    const toast = useToast();
    const {
        contact,
        isLoading,
        isSaving,
        fetchContactInformation,
        updateContactInformation,
        state,
        clearError,
    } = useAdminContactInformation();

    const form = reactive({
        email: '',
        phone: '',
        whatsapp: '',
        support_hours: '',
        location_line_one: '',
        location_line_two: '',
        contact_note: '',
        social_links: [],
    });

    const backendErrors = ref({});
    const generalError = ref('');

    const baseFields = ['email', 'phone', 'whatsapp', 'support_hours', 'location_line_one', 'location_line_two', 'contact_note'];

    const fieldError = (field) => backendErrors.value?.[field]?.[0] ?? null;

    const clearFieldError = (field) => {
        if (backendErrors.value?.[field]) {
            const next = { ...backendErrors.value };
            delete next[field];
            backendErrors.value = next;
        }
    };

    const socialError = (index, field = 'url') => backendErrors.value?.[`social_links.${index}.${field}`]?.[0] ?? null;

    const clearSocialError = (index, field) => {
        const key = `social_links.${index}.${field}`;
        if (backendErrors.value?.[key]) {
            const next = { ...backendErrors.value };
            delete next[key];
            backendErrors.value = next;
        }
    };

    const networkMeta = (network) =>
        SOCIAL_NETWORK_OPTIONS.find((option) => option.value === network) ?? {
            value: network,
            label: network,
            icon: 'pi-link',
            placeholder: 'https://',
        };

    const usedNetworks = computed(() => form.social_links.map((link) => link.network));

    const availableNetworkOptions = computed(() =>
        SOCIAL_NETWORK_OPTIONS.filter((option) => !usedNetworks.value.includes(option.value))
    );

    const getNetworkOptions = (currentNetwork) =>
        SOCIAL_NETWORK_OPTIONS.filter(
            (option) => option.value === currentNetwork || !usedNetworks.value.includes(option.value)
        );

    const addSocialLink = () => {
        const next = availableNetworkOptions.value[0];
        if (!next) {
            toast.add({
                severity: 'info',
                summary: 'Sin redes disponibles',
                detail: 'Ya añadiste todas las redes soportadas.',
                life: 3000,
            });
            return;
        }

        form.social_links.push({ network: next.value, url: '' });
    };

    const removeSocialLink = (index) => {
        form.social_links.splice(index, 1);
        backendErrors.value = {};
        generalError.value = '';
        clearError();
    };

    const setSocialNetwork = (index, value) => {
        if (!value) {
            return;
        }

        const duplicated = form.social_links.some((link, idx) => link.network === value && idx !== index);
        if (duplicated) {
            toast.add({
                severity: 'warn',
                summary: 'Red duplicada',
                detail: 'Ya tienes esa red configurada.',
                life: 3000,
            });
            return;
        }

        form.social_links[index].network = value;
        clearSocialError(index, 'network');
        clearSocialError(index, 'url');
    };

    const normalizeSocialLinks = (links = []) =>
        links
            .filter((link) => link?.network || link?.url)
            .map((link) => ({
                network: link?.network ?? '',
                url: link?.url ?? '',
            }));

    const syncForm = (payload) => {
        baseFields.forEach((field) => {
            form[field] = payload?.[field] ?? '';
        });

        form.social_links = Array.isArray(payload?.social_links)
            ? payload.social_links.map((link) => ({
                network: link.network,
                url: link.url,
            }))
            : [];
    };

    const hasChanges = computed(() => {
        const value = contact.value || {};
        const baseChanged = baseFields.some((field) => (form[field] ?? '') !== (value[field] ?? ''));
        const socialChanged =
            JSON.stringify(normalizeSocialLinks(form.social_links)) !==
            JSON.stringify(normalizeSocialLinks(value.social_links ?? []));

        return baseChanged || socialChanged;
    });

    watch(
        contact,
        (newValue) => {
            syncForm(newValue);
        },
        { immediate: true, deep: true },
    );

    watch(
        () => state.error,
        (error) => {
            if (!error) {
                backendErrors.value = {};
                generalError.value = '';
                return;
            }

            backendErrors.value = error.errors ?? {};
            generalError.value = error.message ?? 'No pudimos guardar la información de contacto.';
        },
    );

    const resetForm = () => {
        syncForm(contact.value);
        backendErrors.value = {};
        generalError.value = '';
        clearError();
    };

    const handleSubmit = async () => {
        backendErrors.value = {};
        generalError.value = '';

        try {
            await updateContactInformation({ ...form });
            backendErrors.value = {};
            generalError.value = '';
            clearError();
            toast.add({ severity: 'success', summary: 'Contacto actualizado', life: 3000 });
        } catch (error) {
            if (error.response?.status === 422) {
                backendErrors.value = error.response.data.errors ?? {};
                generalError.value = error.response.data.message ?? 'Revisa los campos resaltados.';
            } else {
                generalError.value = error.response?.data?.message ?? 'Error al guardar la información de contacto.';
            }
        }
    };

    onMounted(async () => {
        try {
            await fetchContactInformation();
        } catch (error) {
            generalError.value = error.response?.data?.message ?? 'No pudimos cargar la información de contacto.';
        }
    });
</script>

<style scoped>
    .admin-page {
        display: flex;
        flex-direction: column;
        gap: 26px;
    }

    .admin-page__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .admin-page__title {
        font-family: 'Space Mono', monospace;
        font-size: 22px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .admin-page__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.8;
        margin: 0;
        max-width: 520px;
    }

    .admin-page__content {
        background: rgba(255, 255, 255, 0.82);
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 40px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .admin-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin: 60px 0;
    }

    .admin-loader__spinner {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 3px solid rgba(221, 51, 51, 0.2);
        border-top-color: #dd3333;
        animation: contact-spin 1s linear infinite;
    }

    .admin-loader__text {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: rgba(23, 23, 23, 0.7);
    }

    @keyframes contact-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .contact-form__section {
        display: flex;
        flex-direction: column;
        gap: 18px;
        background: rgba(23, 23, 23, 0.02);
        border-radius: 18px;
        padding: 26px 30px;
        border: 1px solid rgba(23, 23, 23, 0.06);
    }

    .contact-form__section-header h3 {
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .contact-form__section-header p {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: rgba(23, 23, 23, 0.6);
        margin: 0;
        max-width: 560px;
    }

    .contact-form__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .contact-form__grid--two {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }

    .contact-form__field {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .contact-form__field label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(23, 23, 23, 0.85);
    }

    .contact-form__error {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #dd3333;
    }

    .contact-socials {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .contact-socials__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 32px 16px;
        border: 1px dashed rgba(23, 23, 23, 0.16);
        border-radius: 16px;
        color: rgba(23, 23, 23, 0.55);
        font-family: 'Inter', sans-serif;
        text-align: center;
    }

    .contact-socials__empty .pi {
        font-size: 24px;
        color: #dd3333;
    }

    .contact-socials__item {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(23, 23, 23, 0.08);
    }

    .contact-socials__item-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .contact-socials__icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(221, 51, 51, 0.1);
        color: #dd3333;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .contact-socials__dropdown {
        flex: 1;
    }

    .contact-socials__remove {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid rgba(23, 23, 23, 0.12);
        background: transparent;
        color: rgba(23, 23, 23, 0.7);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .contact-socials__remove:hover {
        background: rgba(221, 51, 51, 0.12);
        color: #dd3333;
    }

    .contact-socials__actions {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .contact-socials__hint {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: rgba(23, 23, 23, 0.55);
    }

    .contact-form__actions {
        display: flex;
        flex-direction: column;
        gap: 18px;
        align-items: flex-end;
    }

    .contact-form__spinner {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #ffffff;
        animation: contact-spin 1s linear infinite;
    }

    @media (max-width: 1024px) {
        .admin-page__content {
            padding: 28px;
        }

        .contact-form__section {
            padding: 22px 24px;
        }
    }

    @media (max-width: 640px) {
        .admin-page__header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .contact-form__actions {
            align-items: stretch;
        }
    }
</style>
