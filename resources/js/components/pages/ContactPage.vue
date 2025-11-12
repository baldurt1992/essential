<template>
    <div class="contact-page">
        <section class="contact-hero" ref="heroContainer" @mousemove="handleHeroMouseMove"
            @mouseleave="resetHeroOffsets">
            <div class="contact-hero__layout">
                <div class="contact-hero__text">
                    <div class="contact-hero__badge">
                        <span class="contact-hero__dot"></span>
                        <span>Contacto directo</span>
                    </div>
                    <h1 class="contact-hero__title">
                        Hablemos de tu próximo<br>
                        proyecto extraordinario
                    </h1>
                    <p class="contact-hero__description">
                        Estamos listos para acompañarte desde el primer boceto hasta el lanzamiento.
                        Compártenos tu idea y te respondemos en menos de 24 horas hábiles.
                    </p>
                    <div class="contact-hero__actions">
                        <button type="button" class="contact-cta-button contact-cta-button--primary"
                            @click="openContactDialog">
                            Enviar mensaje ahora
                        </button>
                        <a v-if="primaryWhatsAppLink" :href="primaryWhatsAppLink" target="_blank" rel="noopener"
                            class="contact-cta-button contact-cta-button--ghost">
                            Escríbenos por WhatsApp
                        </a>
                    </div>
                </div>
                <div class="contact-hero__visual">
                    <div class="contact-hero__image" :style="heroImageStyle">
                        <img src="/images/liquid-removebg-preview.png" alt="Visual Essential Contact" loading="lazy">
                    </div>
                    <div class="contact-hero__glow"></div>
                </div>
            </div>
        </section>

        <section class="contact-details">
            <div class="contact-details__header">
                <h2 class="contact-details__title">Canales disponibles</h2>
                <p class="contact-details__subtitle">
                    Elegí el canal que prefieras. Responderemos con la misma energía con la que creamos.
                </p>
            </div>

            <div class="contact-cards">
                <article v-for="channel in channelCards" :key="channel.id" class="contact-card">
                    <span class="contact-card__icon" aria-hidden="true" v-html="channel.icon"></span>
                    <div class="contact-card__content">
                        <p class="contact-card__label">{{ channel.label }}</p>
                        <p class="contact-card__value">{{ channel.value }}</p>
                        <p v-if="channel.helper" class="contact-card__helper">{{ channel.helper }}</p>
                    </div>
                    <a v-if="channel.href" :href="channel.href" class="contact-card__action"
                        :target="channel.newTab ? '_blank' : null" rel="noopener" @click="channel.onClick?.()">
                        {{ channel.actionLabel }}
                    </a>
                    <button v-else type="button" class="contact-card__action contact-card__action--button"
                        @click="channel.onClick?.()">
                        {{ channel.actionLabel }}
                    </button>
                </article>
            </div>
        </section>

        <section v-if="contactNote || hasLocation" class="contact-experience">
            <div class="contact-experience__card">
                <h3 class="contact-experience__title">Nuestra casa creativa</h3>
                <p v-if="hasLocation" class="contact-experience__location">
                    {{ locationLineOne }}<br v-if="locationLineTwo">
                    <span v-if="locationLineTwo">{{ locationLineTwo }}</span>
                </p>
                <p v-if="contactNote" class="contact-experience__note">
                    {{ contactNote }}
                </p>
            </div>
            <div v-if="supportHours" class="contact-experience__card contact-experience__card--hours">
                <h3 class="contact-experience__title">Horarios de soporte</h3>
                <p class="contact-experience__hours">{{ supportHours }}</p>
                <p class="contact-experience__helper">
                    Ajustamos nuestra agenda para lanzamientos y activaciones especiales.
                </p>
            </div>
        </section>

        <section v-if="decoratedSocialLinks.length" class="contact-social">
            <div class="contact-social__header">
                <h2 class="contact-social__title">Síguenos y colaboremos</h2>
                <p class="contact-social__subtitle">
                    Compartimos procesos, lanzamientos y detrás de cámaras en nuestros canales.
                </p>
            </div>
            <div class="contact-social__grid">
                <a v-for="link in decoratedSocialLinks" :key="link.network" :href="link.url" target="_blank"
                    rel="noopener" class="contact-social__item">
                    <span class="contact-social__network">
                        <i :class="['pi', link.icon]" aria-hidden="true"></i>
                        <span>{{ link.label }}</span>
                    </span>
                    <span class="contact-social__arrow" aria-hidden="true">
                        <svg viewBox="0 0 16 16" width="16" height="16">
                            <path d="M2.5 13.5 13.5 2.5M5.5 2.5h8v8" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                    </span>
                </a>
            </div>
        </section>

        <Dialog v-model:visible="contactForm.isOpen" modal dismissable-mask class="contact-dialog"
            :breakpoints="dialogBreakpoints" :style="{ width: 'min(560px, 92vw)' }" @hide="handleDialogHide">
            <template #header>
                <div class="contact-dialog__header">
                    <h2>Envíanos tu idea</h2>
                    <p>Resolvemos tu solicitud en menos de 24 horas hábiles.</p>
                </div>
            </template>

            <form class="contact-form" @submit.prevent="submitContact">
                <div class="contact-form__row">
                    <label class="contact-form__field">
                        <span>Nombre y apellido *</span>
                        <InputText v-model="contactForm.data.name" type="text" autocomplete="name"
                            :class="{ 'has-error': !!contactForm.errors.name }" placeholder="¿Cómo te llamas?" />
                        <small v-if="contactForm.errors.name" class="contact-form__error">{{ contactForm.errors.name
                        }}</small>
                    </label>
                </div>

                <div class="contact-form__row contact-form__row--split">
                    <label class="contact-form__field">
                        <span>Correo electrónico *</span>
                        <InputText v-model="contactForm.data.email" type="email" autocomplete="email"
                            :class="{ 'has-error': !!contactForm.errors.email }" placeholder="nombre@empresa.com" />
                        <small v-if="contactForm.errors.email" class="contact-form__error">{{ contactForm.errors.email
                        }}</small>
                    </label>

                    <label class="contact-form__field">
                        <span>WhatsApp o teléfono</span>
                        <InputText v-model="contactForm.data.phone" type="tel" autocomplete="tel"
                            :class="{ 'has-error': !!contactForm.errors.phone }" placeholder="+57 300 000 0000" />
                        <small v-if="contactForm.errors.phone" class="contact-form__error">{{ contactForm.errors.phone
                        }}</small>
                    </label>
                </div>

                <div class="contact-form__row contact-form__row--split">
                    <label class="contact-form__field">
                        <span>Marca o empresa</span>
                        <InputText v-model="contactForm.data.company" type="text" autocomplete="organization"
                            :class="{ 'has-error': !!contactForm.errors.company }" placeholder="Nombre de tu marca" />
                        <small v-if="contactForm.errors.company" class="contact-form__error">{{
                            contactForm.errors.company
                        }}</small>
                    </label>

                    <label class="contact-form__field">
                        <span>Interés principal</span>
                        <select v-model="contactForm.data.subject" class="contact-form__select"
                            :class="{ 'has-error': !!contactForm.errors.subject }">
                            <option value="" disabled>Selecciona una opción</option>
                            <option v-for="option in interestOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <small v-if="contactForm.errors.subject" class="contact-form__error">{{
                            contactForm.errors.subject
                        }}</small>
                    </label>
                </div>

                <div class="contact-form__row">
                    <label class="contact-form__field">
                        <span>Cuéntanos más *</span>
                        <Textarea v-model="contactForm.data.message" rows="5" auto-resize
                            :class="{ 'has-error': !!contactForm.errors.message }"
                            placeholder="Contexto, objetivos y cualquier dato clave que debamos conocer." />
                        <small v-if="contactForm.errors.message" class="contact-form__error">{{
                            contactForm.errors.message
                        }}</small>
                    </label>
                </div>

                <div class="contact-form__actions">
                    <button type="button" class="contact-cta-button contact-cta-button--ghost"
                        @click="contactForm.isOpen = false" :disabled="contactForm.submitting">
                        Cancelar
                    </button>
                    <button type="submit" class="contact-cta-button contact-cta-button--primary"
                        :disabled="contactForm.submitting">
                        <span v-if="!contactForm.submitting">Enviar mensaje</span>
                        <span v-else class="contact-form__spinner"></span>
                    </button>
                </div>

                <p class="contact-form__privacy">
                    Al continuar aceptas que usemos tus datos para responder a tu solicitud. Revisamos cada mensaje de
                    forma
                    manual.
                </p>
            </form>
        </Dialog>

        <div v-if="isLoading" class="contact-overlay contact-overlay--loading">
            <span class="contact-overlay__spinner"></span>
            <p>Cargando canales...</p>
        </div>

        <div v-if="hasError" class="contact-overlay contact-overlay--error">
            <p>No pudimos cargar la información de contacto.</p>
            <button type="button" class="contact-cta-button contact-cta-button--primary" @click="retryFetch">
                Reintentar
            </button>
        </div>
    </div>
</template>

<script setup>
    import { computed, reactive, ref, onMounted, onBeforeUnmount } from 'vue';
    import axios from 'axios';
    import { useToast } from 'primevue/usetoast';
    import { useSiteContactInformation } from '../../composables/useSiteContactInformation';

    const toast = useToast();
    const contactStore = useSiteContactInformation();
    const heroContainer = ref(null);

    const dialogBreakpoints = {
        '960px': '92vw',
        '640px': '98vw',
    };

    const heroState = reactive({
        currentX: 0,
        currentY: 0,
        targetX: 0,
        targetY: 0,
    });
    const heroMaxOffset = 44;
    const heroEasing = 0.12;
    const isHeroInteractive = ref(false);
    let heroAnimationFrame = null;

    const initialFormData = {
        name: '',
        email: '',
        phone: '',
        company: '',
        subject: '',
        message: '',
    };

    const contactForm = reactive({
        isOpen: false,
        submitting: false,
        data: { ...initialFormData },
        errors: {},
    });

    const contact = computed(() => contactStore.contact.value ?? {});
    const socialLinks = computed(() => contactStore.socialLinks.value ?? []);
    const isLoading = computed(() => contactStore.isLoading.value);
    const hasError = computed(() => !!contactStore.state.error);

    const primaryEmail = computed(() => contact.value.email ?? null);
    const primaryPhone = computed(() => contact.value.phone ?? null);
    const whatsappNumber = computed(() => contact.value.whatsapp ?? null);
    const supportHours = computed(() => contact.value.support_hours ?? null);
    const locationLineOne = computed(() => contact.value.location_line_one ?? '');
    const locationLineTwo = computed(() => contact.value.location_line_two ?? '');
    const contactNote = computed(() => contact.value.contact_note ?? '');
    const hasLocation = computed(() => !!(locationLineOne.value || locationLineTwo.value));

    const heroImageStyle = computed(() => {
        if (!isHeroInteractive.value) {
            return {};
        }

        return {
            transform: `translate3d(${heroState.currentX}px, ${heroState.currentY}px, 0)`,
        };
    });

    const formatPhoneHref = (value) => {
        if (!value) return null;
        const digits = value.replace(/[^+\d]/g, '');
        return digits ? `tel:${digits}` : null;
    };

    const formatWhatsAppLink = (value) => {
        if (!value) return null;
        const digits = value.replace(/[^+\d]/g, '');
        if (!digits) return null;
        const message = encodeURIComponent('Hola Essential, quiero crear algo brutal con ustedes ✨');
        return `https://wa.me/${digits.replace('+', '')}?text=${message}`;
    };

    const primaryWhatsAppLink = computed(() => formatWhatsAppLink(whatsappNumber.value));

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

    const channelIcons = {
        mail: `
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                <path d="M3.5 5.5h17a1.5 1.5 0 0 1 1.5 1.5v10a1.5 1.5 0 0 1-1.5 1.5h-17A1.5 1.5 0 0 1 2 17V7a1.5 1.5 0 0 1 1.5-1.5Z"
                    stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path>
                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        `,
        phone: `
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                <path d="M8.5 4.5 6 3 3 6l2.5 2.5a14 14 0 0 0 7 7L15 18l3-3-1.5-2.5"
                    stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M14 3h7v7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>
                <path d="M21 3 13 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        `,
        whatsapp: `
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                <path d="m5 20 1.2-3.6a7.5 7.5 0 1 1 2.9 2.2L5 20Z" stroke="currentColor" stroke-width="1.4"
                    stroke-linejoin="round"></path>
                <path d="M10.5 9a1 1 0 0 1 1 1v.5a.5.5 0 0 0 .3.4l1.3.5a.5.5 0 0 1 .2.8l-.6.6a.5.5 0 0 1-.6.1 4.5 4.5 0 0 1-2.5-2.5.5.5 0 0 1 .1-.6l.6-.6a.5.5 0 0 1 .3-.1Z"
                    fill="currentColor"></path>
            </svg>
        `,
        calendar: `
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                <path d="M5 4h14a1 1 0 0 1 1 1v15H4V5a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.4"
                    stroke-linejoin="round"></path>
                <path d="M8 3v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>
                <path d="M16 3v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>
                <path d="M4 9h16" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"></path>
                <rect x="7" y="12" width="3" height="3" rx=".6" fill="currentColor"></rect>
                <rect x="11" y="12" width="3" height="3" rx=".6" fill="currentColor"></rect>
                <rect x="15" y="12" width="3" height="3" rx=".6" fill="currentColor"></rect>
            </svg>
        `,
    };

    const SOCIAL_NETWORK_META = {
        website: { label: 'Sitio web', icon: 'pi-globe' },
        instagram: { label: 'Instagram', icon: 'pi-instagram' },
        facebook: { label: 'Facebook', icon: 'pi-facebook' },
        linkedin: { label: 'LinkedIn', icon: 'pi-linkedin' },
        twitter: { label: 'Twitter / X', icon: 'pi-twitter' },
        youtube: { label: 'YouTube', icon: 'pi-youtube' },
        tiktok: { label: 'TikTok', icon: 'pi-tiktok' },
        pinterest: { label: 'Pinterest', icon: 'pi-pinterest' },
        whatsapp: { label: 'WhatsApp', icon: 'pi-whatsapp' },
        telegram: { label: 'Telegram', icon: 'pi-telegram' },
        github: { label: 'GitHub', icon: 'pi-github' },
        slack: { label: 'Slack', icon: 'pi-slack' },
        reddit: { label: 'Reddit', icon: 'pi-reddit' },
        vimeo: { label: 'Vimeo', icon: 'pi-vimeo' },
        twitch: { label: 'Twitch', icon: 'pi-twitch' },
        behance: { label: 'Behance', icon: 'pi-images' },
        dribbble: { label: 'Dribbble', icon: 'pi-star-fill' },
    };

    const formatNetworkLabel = (network) => {
        if (!network) {
            return 'Canal';
        }

        return network
            .replace(/[_-]/g, ' ')
            .split(' ')
            .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    };

    const decoratedSocialLinks = computed(() =>
        socialLinks.value.map((link) => {
            const meta = SOCIAL_NETWORK_META[link.network] ?? {};
            return {
                ...link,
                label: meta.label ?? formatNetworkLabel(link.network),
                icon: meta.icon ?? 'pi-share-alt',
            };
        }),
    );

    const channelCards = computed(() => {
        const emailAction = primaryEmail.value
            ? {
                id: 'email',
                label: 'Correo directo',
                value: primaryEmail.value,
                helper: 'Ideal para propuestas y documentación detallada.',
                actionLabel: 'Abrir formulario',
                onClick: openContactDialog,
                icon: channelIcons.mail,
            }
            : null;

        const phoneAction = primaryPhone.value
            ? {
                id: 'phone',
                label: 'Llámanos',
                value: primaryPhone.value,
                helper: 'Atención de lunes a viernes, 9:00 a. m. a 6:00 p. m.',
                actionLabel: 'Llamar ahora',
                href: formatPhoneHref(primaryPhone.value),
                icon: channelIcons.phone,
            }
            : null;

        const whatsappAction = primaryWhatsAppLink.value
            ? {
                id: 'whatsapp',
                label: 'WhatsApp',
                value: whatsappNumber.value,
                helper: 'Respuestas ágiles y seguimiento en tiempo real.',
                actionLabel: 'Abrir chat',
                href: primaryWhatsAppLink.value,
                newTab: true,
                icon: channelIcons.whatsapp,
            }
            : null;

        const scheduleAction = supportHours.value
            ? {
                id: 'schedule',
                label: 'Agenda una reunión',
                value: 'Coordinamos una llamada estratégica',
                helper: supportHours.value,
                actionLabel: 'Coordinar por email',
                onClick: openContactDialog,
                icon: channelIcons.calendar,
            }
            : null;

        return [emailAction, phoneAction, whatsappAction, scheduleAction].filter(Boolean);
    });

    function openContactDialog() {
        contactForm.isOpen = true;
    }

    function handleDialogHide() {
        contactForm.errors = {};
    }

    function resetForm() {
        Object.assign(contactForm.data, initialFormData);
        contactForm.errors = {};
    }

    async function submitContact() {
        contactForm.submitting = true;
        contactForm.errors = {};

        try {
            await axios.post('/api/contact-messages', {
                ...contactForm.data,
                origin_url: window.location.href,
            });

            toast.add({
                severity: 'success',
                summary: 'Mensaje enviado',
                detail: 'Gracias por escribirnos. Te contactaremos muy pronto.',
                life: 6000,
            });

            contactForm.isOpen = false;
            resetForm();
        } catch (error) {
            if (error.response?.status === 422) {
                contactForm.errors = Object.fromEntries(
                    Object.entries(error.response.data.errors || {}).map(([field, messages]) => [field, messages[0]]),
                );
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'No pudimos enviar tu mensaje',
                    detail: 'Intenta nuevamente en unos segundos o escríbenos por WhatsApp.',
                    life: 6000,
                });
            }
        } finally {
            contactForm.submitting = false;
        }
    }

    async function retryFetch() {
        await contactStore.fetchContactInformation();
    }

    function updateHeroInteractivity() {
        const shouldAnimate = window.matchMedia('(min-width: 1024px)').matches
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!shouldAnimate) {
            heroState.currentX = 0;
            heroState.currentY = 0;
            heroState.targetX = 0;
            heroState.targetY = 0;
        }

        isHeroInteractive.value = shouldAnimate;
    }

    function ensureHeroAnimation() {
        if (heroAnimationFrame) {
            return;
        }
        heroAnimationFrame = requestAnimationFrame(animateHeroFrame);
    }

    function animateHeroFrame() {
        const deltaX = heroState.targetX - heroState.currentX;
        const deltaY = heroState.targetY - heroState.currentY;

        heroState.currentX += deltaX * heroEasing;
        heroState.currentY += deltaY * heroEasing;

        if (Math.abs(deltaX) < 0.3 && Math.abs(deltaY) < 0.3) {
            heroState.currentX = heroState.targetX;
            heroState.currentY = heroState.targetY;
            heroAnimationFrame = null;
            return;
        }

        heroAnimationFrame = requestAnimationFrame(animateHeroFrame);
    }

    function handleHeroMouseMove(event) {
        if (!isHeroInteractive.value || !heroContainer.value) {
            return;
        }

        const rect = heroContainer.value.getBoundingClientRect();
        const centerX = rect.left + rect.width * 0.75;
        const centerY = rect.top + rect.height * 0.5;

        const relativeX = (event.clientX - centerX) / rect.width;
        const relativeY = (event.clientY - centerY) / rect.height;

        heroState.targetX = Math.max(Math.min(-relativeX * heroMaxOffset, heroMaxOffset), -heroMaxOffset);
        heroState.targetY = Math.max(Math.min(-relativeY * heroMaxOffset, heroMaxOffset), -heroMaxOffset);

        ensureHeroAnimation();
    }

    function resetHeroOffsets() {
        heroState.targetX = 0;
        heroState.targetY = 0;
        ensureHeroAnimation();
    }

    onMounted(async () => {
        updateHeroInteractivity();
        window.addEventListener('resize', updateHeroInteractivity, { passive: true });
        await contactStore.fetchContactInformation();
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', updateHeroInteractivity);
        if (heroAnimationFrame) {
            cancelAnimationFrame(heroAnimationFrame);
            heroAnimationFrame = null;
        }
    });
</script>

<style scoped>
    .contact-page {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: clamp(48px, 10vw, 96px);
        padding: clamp(24px, 8vw, 80px) clamp(16px, 6vw, 120px) clamp(80px, 12vw, 160px);
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        max-width: 100vw;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .contact-hero {
        position: relative;
        overflow: hidden;
    }

    .contact-hero__layout {
        display: flex;
        flex-direction: column;
        gap: clamp(28px, 8vw, 64px);
        align-items: flex-start;
    }

    .contact-hero__text {
        display: flex;
        flex-direction: column;
        gap: clamp(16px, 5vw, 32px);
        max-width: 720px;
        width: 100%;
        z-index: 2;
        box-sizing: border-box;
    }

    .contact-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .contact-hero__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .contact-hero__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(28px, 8vw, 72px);
        letter-spacing: -0.015em;
        line-height: 1.05;
        text-transform: uppercase;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .contact-hero__description {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: clamp(16px, 3.6vw, 18px);
        line-height: 1.75;
        max-width: 52ch;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .contact-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .contact-hero__actions {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .contact-hero__visual {
        position: relative;
        align-self: center;
        width: min(420px, 70vw);
        aspect-ratio: 1;
    }

    .contact-hero__image {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        will-change: transform;
        transition: transform 0.12s linear;
    }

    .contact-hero__image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
        filter: saturate(110%);
        animation: contact-hero-float 8s ease-in-out infinite;
    }

    .contact-hero__glow {
        position: absolute;
        inset: 12%;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 50%, rgba(221, 51, 51, 0.32) 0%, rgba(221, 51, 51, 0) 72%);
        z-index: 1;
    }

    .contact-cta-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 14px 24px;
        border-radius: 999px;
        border: 1px solid currentColor;
        cursor: pointer;
        transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease;
    }

    .contact-cta-button--primary {
        background: #dd3333;
        border-color: #dd3333;
        color: #fff;
    }

    .contact-cta-button--ghost {
        background: transparent;
        color: inherit;
    }

    .contact-cta-button:not(:disabled):hover {
        transform: translateY(-2px);
    }

    .contact-cta-button:disabled {
        opacity: 0.5;
        cursor: wait;
    }

    .contact-details {
        display: flex;
        flex-direction: column;
        gap: clamp(24px, 6vw, 32px);
    }

    .contact-details__header {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 620px;
    }

    .contact-details__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(28px, 6vw, 40px);
        text-transform: uppercase;
    }

    .contact-details__subtitle {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(23, 23, 23, 0.74);
    }

    body.dark-mode .contact-details__subtitle {
        color: rgba(243, 243, 243, 0.72);
    }

    .contact-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: clamp(16px, 4vw, 28px);
    }

    .contact-card {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding: clamp(20px, 5vw, 28px);
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.9);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .contact-card:hover {
        transform: translateY(-4px);
        border-color: rgba(221, 51, 51, 0.25);
        box-shadow: 0 24px 40px rgba(0, 0, 0, 0.08);
    }

    body.dark-mode .contact-card {
        background: rgba(20, 20, 20, 0.92);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .contact-card__icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: rgba(221, 51, 51, 0.12);
        color: #dd3333;
    }

    body.dark-mode .contact-card__icon {
        background: rgba(255, 102, 102, 0.12);
        color: #ff6666;
    }

    .contact-card__content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .contact-card__label {
        margin: 0;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(23, 23, 23, 0.6);
    }

    .contact-card__value {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: clamp(18px, 4vw, 22px);
        text-transform: uppercase;
        color: var(--qode-text-color);
    }

    .contact-card__helper {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.64);
    }

    body.dark-mode .contact-card__helper {
        color: rgba(243, 243, 243, 0.64);
    }

    .contact-card__action {
        align-self: flex-start;
        margin-top: auto;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid var(--qode-text-color);
        background: transparent;
        color: var(--qode-text-color);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .contact-card__action:hover {
        background: var(--qode-heading-color);
        color: var(--qode-background-color);
        transform: translateY(-2px);
    }

    .contact-card__action--button {
        background: none;
    }

    body.dark-mode .contact-card__action {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    body.dark-mode .contact-card__action:hover {
        background: #ffffff;
        color: #171717;
    }

    .contact-experience {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: clamp(18px, 4vw, 28px);
    }

    .contact-experience__card {
        padding: clamp(24px, 5vw, 32px);
        border-radius: 24px;
        border: 1px solid rgba(23, 23, 23, 0.1);
        background: rgba(247, 247, 247, 0.9);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    body.dark-mode .contact-experience__card {
        background: rgba(26, 26, 26, 0.9);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .contact-experience__card--hours {
        background: rgba(221, 51, 51, 0.08);
    }

    body.dark-mode .contact-experience__card--hours {
        background: rgba(255, 102, 102, 0.08);
    }

    .contact-experience__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(22px, 4vw, 28px);
        text-transform: uppercase;
    }

    .contact-experience__location,
    .contact-experience__note,
    .contact-experience__hours,
    .contact-experience__helper {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .contact-experience__location,
    body.dark-mode .contact-experience__note,
    body.dark-mode .contact-experience__hours,
    body.dark-mode .contact-experience__helper {
        color: rgba(243, 243, 243, 0.78);
    }

    .contact-social {
        display: flex;
        flex-direction: column;
        gap: clamp(24px, 5vw, 36px);
    }

    .contact-social__header {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 420px;
    }

    .contact-social__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(26px, 6vw, 38px);
        text-transform: uppercase;
        line-height: 1.1;
    }

    .contact-social__subtitle {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.7;
        color: rgba(23, 23, 23, 0.7);
    }

    body.dark-mode .contact-social__subtitle {
        color: rgba(243, 243, 243, 0.7);
    }

    .contact-social__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: clamp(16px, 4vw, 24px);
    }

    .contact-social__item {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: clamp(16px, 4vw, 22px);
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 18px;
        text-decoration: none;
        color: inherit;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
    }

    .contact-social__item:hover {
        transform: translateY(-3px);
        border-color: rgba(221, 51, 51, 0.28);
        background: rgba(221, 51, 51, 0.06);
    }

    body.dark-mode .contact-social__item {
        border-color: rgba(243, 243, 243, 0.16);
        background: rgba(20, 20, 20, 0.6);
    }

    body.dark-mode .contact-social__item:hover {
        border-color: rgba(255, 102, 102, 0.36);
        background: rgba(255, 102, 102, 0.12);
    }

    .contact-social__network {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .contact-social__network i {
        font-size: 18px;
    }

    .contact-social__arrow {
        display: inline-flex;
        opacity: 0.4;
        transition: opacity 0.25s ease;
    }

    .contact-social__item:hover .contact-social__arrow {
        opacity: 1;
    }

    .contact-dialog :deep(.p-dialog-header) {
        padding-bottom: 0;
        border-bottom: none;
    }

    .contact-dialog__header {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .contact-dialog__header h2 {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(24px, 4vw, 30px);
        text-transform: uppercase;
    }

    .contact-dialog__header p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: rgba(23, 23, 23, 0.7);
    }

    body.dark-mode .contact-dialog__header p {
        color: rgba(243, 243, 243, 0.72);
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding-top: 12px;
    }

    .contact-form__row {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .contact-form__row--split {
        gap: clamp(16px, 3vw, 24px);
    }

    @media (min-width: 720px) {
        .contact-form__row--split {
            flex-direction: row;
        }

        .contact-form__row--split .contact-form__field {
            flex: 1;
        }
    }

    .contact-form__field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-family: 'Inter', sans-serif;
    }

    .contact-form__field span {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .contact-form__field :deep(.p-inputtext),
    .contact-form__field :deep(.p-inputtextarea),
    .contact-form__select {
        border-radius: 14px;
        border: 1px solid rgba(23, 23, 23, 0.18);
        padding: 12px 16px;
        font-size: 14px;
        background: rgba(255, 255, 255, 0.92);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .contact-form__field :deep(.p-inputtextarea) {
        resize: none;
    }

    .contact-form__select {
        font-family: 'Inter', sans-serif;
        color: var(--qode-text-color);
    }

    .contact-form__field :deep(.p-inputtext.has-error),
    .contact-form__field :deep(.p-inputtextarea.has-error),
    .contact-form__select.has-error,
    .contact-form__field .has-error {
        border-color: rgba(221, 51, 51, 0.6) !important;
    }

    body.dark-mode .contact-form__field :deep(.p-inputtext),
    body.dark-mode .contact-form__field :deep(.p-inputtextarea),
    body.dark-mode .contact-form__select {
        background: rgba(16, 16, 16, 0.92);
        border-color: rgba(243, 243, 243, 0.12);
        color: rgba(243, 243, 243, 0.92);
    }

    .contact-form__error {
        font-size: 12px;
        color: #dd3333;
    }

    .contact-form__actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 8px;
    }

    @media (min-width: 640px) {
        .contact-form__actions {
            flex-direction: row;
            justify-content: flex-end;
        }
    }

    .contact-form__privacy {
        margin: 0;
        font-size: 12px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.64);
        text-align: center;
    }

    body.dark-mode .contact-form__privacy {
        color: rgba(243, 243, 243, 0.64);
    }

    .contact-form__spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: contact-spin 0.8s linear infinite;
    }

    @keyframes contact-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes contact-hero-float {

        0%,
        100% {
            transform: translate3d(0, 0, 0) scale(1);
        }

        50% {
            transform: translate3d(0, -16px, 0) scale(1.015);
        }
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

    .contact-overlay--error .contact-cta-button {
        margin-top: 8px;
    }

    @media (max-width: 480px) {
        .contact-page {
            padding-left: 12px;
            padding-right: 12px;
        }

        .contact-hero__title {
            font-size: clamp(24px, 7vw, 32px);
            line-height: 1.1;
        }

        .contact-hero__description {
            font-size: 15px;
        }
    }

    @media (min-width: 960px) {
        .contact-hero__layout {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .contact-hero__visual {
            width: clamp(320px, 36vw, 540px);
            margin-right: clamp(0px, 4vw, 48px);
        }

        .contact-hero__actions {
            flex-direction: row;
            align-items: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .contact-cta-button,
        .contact-card,
        .contact-card__action,
        .contact-social__item {
            transition: none;
        }
    }
</style>
