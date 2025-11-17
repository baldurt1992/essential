<template>
    <div class="contact-page">
        <ContactHero :primary-whats-app-link="primaryWhatsAppLink" @open-dialog="openContactDialog" />

        <ContactDetails :channel-cards="channelCards" />

        <ContactExperience :contact-note="contactNote" :location-line-one="locationLineOne"
            :location-line-two="locationLineTwo" :support-hours="supportHours" :has-location="hasLocation" />

        <ContactSocial :decorated-social-links="decoratedSocialLinks" />

        <ContactFormDialog :visible="contactForm.isOpen" @update:visible="(val) => contactForm.isOpen = val"
            :form-data="contactForm.data" :errors="contactForm.errors" :interest-options="interestOptions"
            :submitting="contactForm.submitting" :breakpoints="dialogBreakpoints" @hide="handleDialogHide"
            @submit="submitContact" />

        <div v-if="isLoading" class="contact-overlay contact-overlay--loading">
            <span class="contact-overlay__spinner"></span>
            <p>Cargando canales...</p>
        </div>

        <div v-if="hasError" class="contact-overlay contact-overlay--error">
            <p>No pudimos cargar la información de contacto.</p>
            <button type="button" class="essential-contact-cta essential-contact-cta--primary" @click="retryFetch">
                Reintentar
            </button>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted } from 'vue';
    import { useSiteContactInformation } from '@/composables/useSiteContactInformation';
    import { useContactPageForm } from '@/composables/useContactPageForm';
    import { useContactChannels } from '@/composables/useContactChannels';
    import { useContactSocial } from '@/composables/useContactSocial';
    import ContactHero from '../contact/ui/ContactHero.vue';
    import ContactDetails from '../contact/ui/ContactDetails.vue';
    import ContactExperience from '../contact/ui/ContactExperience.vue';
    import ContactSocial from '../contact/ui/ContactSocial.vue';
    import ContactFormDialog from '../contact/modals/ContactFormDialog.vue';

    const contactStore = useSiteContactInformation();

    const { contactForm, openContactDialog, handleDialogHide, submitContact } = useContactPageForm();

    const { createChannelCards, primaryWhatsAppLink, supportHours } = useContactChannels();

    const channelCards = computed(() => createChannelCards(openContactDialog));

    const { decoratedSocialLinks } = useContactSocial();

    const contact = computed(() => contactStore.contact.value ?? {});
    const isLoading = computed(() => contactStore.isLoading.value);
    const hasError = computed(() => !!contactStore.state.error);

    const locationLineOne = computed(() => contact.value.location_line_one ?? '');
    const locationLineTwo = computed(() => contact.value.location_line_two ?? '');
    const contactNote = computed(() => contact.value.contact_note ?? '');
    const hasLocation = computed(() => !!(locationLineOne.value || locationLineTwo.value));

    const interestOptions = computed(() => {
        const topics = contact.value.metadata?.contact_topics;
        if (Array.isArray(topics) && topics.length) {
            return topics.map((topic) => ({
                label: topic,
                value: topic,
            }));
        }

        return [
            { label: 'Branding & Identidad', value: 'Branding & Identidad' },
            { label: 'Gestión de Redes', value: 'Gestión de Redes' },
            { label: 'Campañas y Publicidad', value: 'Campañas y Publicidad' },
            { label: 'Diseño Editorial', value: 'Diseño Editorial' },
            { label: 'Otro proyecto increíble', value: 'Otro proyecto increíble' },
        ];
    });

    const dialogBreakpoints = {
        '960px': '92vw',
        '640px': '98vw',
    };

    async function retryFetch() {
        await contactStore.fetchContactInformation();
    }

    onMounted(async () => {
        await contactStore.fetchContactInformation();
    });
</script>

<style scoped>
    .contact-page {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: clamp(48px, 10vw, 96px);
        padding: clamp(24px, 8vw, 80px) clamp(16px, 6vw, 120px) clamp(80px, 12vw, 160px);
        background: var(--essential-background-color);
        color: var(--essential-text-color);
        max-width: 100vw;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .contact-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        backdrop-filter: blur(4px);
        background: rgba(255, 255, 255, 0.8);
        z-index: 10;
    }

    body.dark-mode .contact-overlay {
        background: rgba(12, 12, 12, 0.88);
        color: rgba(243, 243, 243, 0.9);
    }

    .contact-overlay__spinner {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 3px solid rgba(23, 23, 23, 0.14);
        border-top-color: #dd3333;
        animation: contact-spin 0.9s linear infinite;
    }

    body.dark-mode .contact-overlay__spinner {
        border-color: rgba(255, 255, 255, 0.14);
        border-top-color: #ff6666;
    }

    .contact-overlay--error .essential-contact-cta {
        margin-top: 8px;
    }

    @keyframes contact-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 480px) {
        .contact-page {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>
