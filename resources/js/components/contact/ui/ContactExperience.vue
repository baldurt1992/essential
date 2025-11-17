<template>
    <section v-if="contactNote || hasLocation || supportHours" class="contact-experience">
        <div v-if="contactNote || hasLocation" class="contact-experience__card">
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
</template>

<script setup>
defineProps({
    contactNote: {
        type: String,
        default: '',
    },
    locationLineOne: {
        type: String,
        default: '',
    },
    locationLineTwo: {
        type: String,
        default: '',
    },
    supportHours: {
        type: String,
        default: null,
    },
    hasLocation: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
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
    color: var(--essential-heading-color);
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
</style>

