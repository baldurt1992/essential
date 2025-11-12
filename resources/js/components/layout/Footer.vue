<template>
    <footer id="site-footer" role="contentinfo">
        <div class="footer-top">
            <div class="footer-top__inner">
                <div class="footer-grid">
                    <div class="footer-column">
                        <h6 class="footer-column__title">Plantillas</h6>
                        <ul class="footer-column__list">
                            <li><a href="/plantillas">Flyers</a></li>
                            <li><a href="/plantillas">Novedades</a></li>
                            <li><a href="/plantillas">Populares</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h6 class="footer-column__title">Información</h6>
                        <ul class="footer-column__list">
                            <li><a href="/precios">Precios</a></li>
                            <li><a href="/servicios">Servicios</a></li>
                            <li><a href="/contacto">Contactar</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h6 class="footer-column__title">Legal</h6>
                        <ul class="footer-column__list">
                            <li><a href="/cookies">Cookies</a></li>
                            <li><a href="/politica_privacidad">Política de Privacidad</a></li>
                            <li><a href="/terminosdeuso">Términos de uso</a></li>
                            <li><a href="/copright">Copyright</a></li>
                        </ul>
                    </div>

                    <div class="footer-column footer-column--contact">
                        <h6 class="footer-column__title">Redes y contacto</h6>

                        <div class="footer-contact">
                            <div v-if="isLoading" class="footer-contact__loading">
                                <span class="footer-contact__spinner"></span>
                                <p>Cargando información…</p>
                            </div>

                            <template v-else>
                                <ul v-if="contactItems.length" class="footer-contact__list">
                                    <li v-for="item in contactItems" :key="item.key" class="footer-contact__item">
                                        <span class="footer-contact__item-icon">
                                            <i :class="['pi', item.icon]" aria-hidden="true"></i>
                                        </span>
                                        <div class="footer-contact__item-content">
                                            <span class="footer-contact__item-label">{{ item.label }}</span>
                                            <a v-if="item.href" :href="item.href" target="_blank" rel="noopener"
                                                class="footer-contact__item-link">
                                                {{ item.display }}
                                            </a>
                                            <span v-else class="footer-contact__item-text">{{ item.display }}</span>
                                        </div>
                                    </li>
                                </ul>

                                <p v-if="contactNote" class="footer-contact__note">
                                    {{ contactNote }}
                                </p>

                                <div v-if="displaySocials.length" class="footer-contact__socials">
                                    <a v-for="social in displaySocials" :key="social.network" :href="social.url"
                                        target="_blank" rel="noopener" class="footer-contact__social-link">
                                        <span class="sr-only">{{ social.label }}</span>
                                        <i :class="['pi', social.icon]" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom__inner">
                <div class="footer-bottom__content">
                    <p>
                        © 2025
                        <a href="/" target="_blank" rel="nofollow noopener">
                            ESSENTIAL INNOVATION
                        </a>
                        , ALL RIGHTS RESERVED
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
    import { computed, onMounted } from 'vue';
    import { useSiteContactInformation } from '../../composables/useSiteContactInformation';

    const SOCIAL_ICON_MAP = {
        website: { icon: 'pi-globe', label: 'Sitio web' },
        instagram: { icon: 'pi-instagram', label: 'Instagram' },
        facebook: { icon: 'pi-facebook', label: 'Facebook' },
        linkedin: { icon: 'pi-linkedin', label: 'LinkedIn' },
        twitter: { icon: 'pi-twitter', label: 'Twitter' },
        youtube: { icon: 'pi-youtube', label: 'YouTube' },
        tiktok: { icon: 'pi-tiktok', label: 'TikTok' },
        pinterest: { icon: 'pi-pinterest', label: 'Pinterest' },
        whatsapp: { icon: 'pi-whatsapp', label: 'WhatsApp' },
        telegram: { icon: 'pi-telegram', label: 'Telegram' },
        github: { icon: 'pi-github', label: 'GitHub' },
        slack: { icon: 'pi-slack', label: 'Slack' },
        reddit: { icon: 'pi-reddit', label: 'Reddit' },
        vimeo: { icon: 'pi-vimeo', label: 'Vimeo' },
        twitch: { icon: 'pi-twitch', label: 'Twitch' },
    };

    const { contact, socialLinks, isLoading, fetchContactInformation } = useSiteContactInformation();

    onMounted(() => {
        fetchContactInformation();
    });

    const sanitizeHref = (value) => value?.trim() ?? '';

    const formatTelHref = (value) => {
        const raw = sanitizeHref(value);
        if (!raw) {
            return null;
        }
        const digits = raw.replace(/[^\d+]/g, '');
        return digits ? `tel:${digits}` : null;
    };

    const formatWhatsappHref = (value) => {
        const raw = sanitizeHref(value);
        if (!raw) {
            return null;
        }
        if (raw.startsWith('http')) {
            return raw;
        }
        const digits = raw.replace(/[^\d]/g, '');
        return digits ? `https://wa.me/${digits}` : null;
    };

    const formatUrl = (value) => {
        const raw = sanitizeHref(value);
        if (!raw) {
            return null;
        }
        if (raw.startsWith('http://') || raw.startsWith('https://')) {
            return raw;
        }
        return `https://${raw}`;
    };

    const joinLocation = (lineOne, lineTwo) => {
        return [lineOne, lineTwo].filter(Boolean).join(' · ');
    };

    const contactItems = computed(() => {
        const data = contact.value ?? {};

        const items = [
            {
                key: 'email',
                label: 'Correo',
                icon: 'pi-envelope',
                value: data.email,
                href: data.email ? `mailto:${sanitizeHref(data.email)}` : null,
                display: data.email,
            },
            {
                key: 'phone',
                label: 'Teléfono',
                icon: 'pi-phone',
                value: data.phone,
                href: formatTelHref(data.phone),
                display: data.phone,
            },
            {
                key: 'whatsapp',
                label: 'WhatsApp',
                icon: 'pi-whatsapp',
                value: data.whatsapp,
                href: formatWhatsappHref(data.whatsapp),
                display: data.whatsapp,
            },
            {
                key: 'support_hours',
                label: 'Horario',
                icon: 'pi-clock',
                value: data.support_hours,
                display: data.support_hours,
            },
            {
                key: 'location',
                label: 'Ubicación',
                icon: 'pi-map-marker',
                value: joinLocation(data.location_line_one, data.location_line_two),
                display: joinLocation(data.location_line_one, data.location_line_two),
            },
            {
                key: 'website',
                label: 'Sitio web',
                icon: 'pi-globe',
                value: data.website_url,
                href: formatUrl(data.website_url),
                display: data.website_url?.replace(/^https?:\/\//, '') ?? '',
            },
        ];

        return items
            .filter((item) => sanitizeHref(item.value))
            .map((item) => ({
                ...item,
                display: item.display?.trim() ?? item.value,
            }));
    });

    const contactNote = computed(() => contact.value?.contact_note?.trim() ?? '');

    const displaySocials = computed(() =>
        socialLinks.value
            .map((link) => {
                const meta = SOCIAL_ICON_MAP[link.network];
                if (!meta) {
                    return null;
                }
                const href = formatUrl(link.url);
                if (!href) {
                    return null;
                }
                return {
                    ...meta,
                    network: link.network,
                    url: href,
                };
            })
            .filter(Boolean),
    );
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;500&display=swap');

    #site-footer {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
        color: var(--qode-text-color);
        background: #171717;
    }

    #site-footer>* {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
    }

    .footer-top {
        background-color: #171717 !important;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        border-top: 0 solid var(--qode-heading-color);
        padding: 136px 0 50px;
    }

    .footer-top__inner,
    .footer-bottom__inner {
        width: min(1380px, 100%);
        margin: 0 auto;
        padding: 0 40px;
    }

    @media (max-width: 1200px) {

        .footer-top__inner,
        .footer-bottom__inner {
            padding: 0 30px;
        }
    }

    @media (max-width: 680px) {

        .footer-top__inner,
        .footer-bottom__inner {
            padding: 0 20px;
        }
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 36px;
    }

    @media (min-width: 768px) {
        .footer-grid {
            gap: 42px;
        }
    }

    @media (min-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .footer-column__title {
        font-family: 'Lexend', sans-serif;
        font-size: 20px;
        font-weight: 500;
        line-height: 1.5625em;
        text-transform: uppercase;
        color: #ffffff;
        display: block;
        margin: 0 0 24px;
    }

    .footer-column__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }

    .footer-column__list a {
        font-family: 'Inter', sans-serif;
        font-size: 17px;
        font-weight: 400;
        line-height: 1.52941em;
        color: #ffffff;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-column__list a:hover {
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-column--contact {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .footer-contact {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .footer-contact__loading {
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: var(--footer-muted);
    }

    .footer-contact__spinner {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-top-color: var(--footer-text);
        animation: footer-spin 0.8s linear infinite;
    }

    @keyframes footer-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .footer-contact__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }

    .footer-contact__item {
        display: grid;
        grid-template-columns: 32px 1fr;
        gap: 12px;
    }

    .footer-contact__item-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
    }

    .footer-contact__item-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.55);
        display: block;
        margin-bottom: 4px;
    }

    .footer-contact__item-text,
    .footer-contact__item-link {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.45;
        color: #ffffff;
        word-break: break-word;
    }

    .footer-contact__item-link {
        text-decoration: none;
    }

    .footer-contact__item-link:hover {
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-contact__note {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.55);
        margin: 0;
    }

    .footer-contact__socials {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 4px;
    }

    .footer-contact__social-link {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .footer-contact__social-link:hover {
        background: #ffffff;
        color: #171717;
        transform: translateY(-2px);
    }

    .footer-bottom {
        background-color: #171717 !important;
        padding: 36px 0;
        border-top: 0 solid var(--qode-heading-color);
    }

    .footer-bottom__content {
        width: 100%;
        text-align: right;
    }

    .footer-bottom__content p {
        margin: 0;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        color: #ffffff;
    }

    .footer-bottom__content a {
        color: #ffffff;
        text-decoration: none;
    }

    .footer-bottom__content a:hover {
        text-decoration: underline;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    @media (min-width: 768px) {
        .footer-bottom__content {
            text-align: right;
        }
    }
</style>
