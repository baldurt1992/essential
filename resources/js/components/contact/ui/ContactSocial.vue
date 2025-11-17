<template>
    <section v-if="decoratedSocialLinks.length" class="contact-social">
        <div class="contact-social__header">
            <h2 class="contact-social__title">Síguenos y colaboremos</h2>
            <p class="contact-social__subtitle">
                Compartimos procesos, lanzamientos y detrás de cámaras en nuestros canales.
            </p>
        </div>
        <div class="contact-social__grid">
            <a v-for="link in decoratedSocialLinks" :key="link.network" :href="link.url" target="_blank"
                rel="noopener" class="essential-contact-social-item">
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
</template>

<script setup>
defineProps({
    decoratedSocialLinks: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
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
    color: var(--essential-heading-color);
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

.essential-contact-social-item:hover .contact-social__arrow {
    opacity: 1;
}
</style>

