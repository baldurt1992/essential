<template>
    <section class="contact-details">
        <div class="contact-details__header">
            <h2 class="contact-details__title">Canales disponibles</h2>
            <p class="contact-details__subtitle">
                Elegí el canal que prefieras. Responderemos con la misma energía con la que creamos.
            </p>
        </div>

        <div class="contact-cards">
            <ContactCard v-for="channel in channelCards" :key="channel.id" :channel="channel" />
        </div>
    </section>
</template>

<script setup>
import ContactCard from './ContactCard.vue';

defineProps({
    channelCards: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
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
    color: var(--essential-heading-color);
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
</style>

