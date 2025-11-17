import { computed } from 'vue';
import { useSiteContactInformation } from './useSiteContactInformation';

/**
 * Composable para manejar los canales de contacto (email, phone, whatsapp, schedule)
 */
export function useContactChannels() {
    const contactStore = useSiteContactInformation();

    const contact = computed(() => contactStore.contact.value ?? {});

    const primaryEmail = computed(() => contact.value.email ?? null);
    const primaryPhone = computed(() => contact.value.phone ?? null);
    const whatsappNumber = computed(() => contact.value.whatsapp ?? null);
    const supportHours = computed(() => contact.value.support_hours ?? null);

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


    const createChannelCards = (openContactDialog) => {
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
    };

    return {
        createChannelCards,
        primaryWhatsAppLink,
        supportHours,
    };
}

