import { computed } from 'vue';
import { useSiteContactInformation } from './useSiteContactInformation';

/**
 * Composable para manejar las redes sociales de contacto
 */
export function useContactSocial() {
    const contactStore = useSiteContactInformation();

    const socialLinks = computed(() => contactStore.socialLinks.value ?? []);

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

    return {
        decoratedSocialLinks,
    };
}

