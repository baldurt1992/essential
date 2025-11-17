<template>
    <section class="plans-extra">
        <div class="plans-extra__card">
            <h2>¿Necesitas un plan híbrido?</h2>
            <p>
                Mezclamos producción de plantillas, branding y campañas en un solo paquete. Cuéntanos tu frecuencia,
                equipo y objetivos y armamos un plan personalizado con métricas claras.
            </p>
            <RouterLink :to="{ name: 'contact', query: { from: 'plans-custom' } }"
                class="essential-contact-cta essential-contact-cta--primary">
                Diseñar plan a medida
            </RouterLink>
        </div>
    </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>

<style scoped>
.plans-extra {
    display: flex;
    justify-content: center;
}

.plans-extra__card {
    width: min(960px, 100%);
    padding: clamp(28px, 6vw, 40px);
    border-radius: 28px;
    border: 1px solid rgba(221, 51, 51, 0.16);
    background: rgba(221, 51, 51, 0.06);
    display: flex;
    flex-direction: column;
    gap: 18px;
    text-align: left;
}

body.dark-mode .plans-extra__card {
    border-color: rgba(255, 102, 102, 0.28);
    background: rgba(255, 102, 102, 0.12);
}

.plans-extra__card h2 {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(24px, 5vw, 32px);
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.plans-extra__card p {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    line-height: 1.7;
    color: rgba(23, 23, 23, 0.78);
}

body.dark-mode .plans-extra__card p {
    color: rgba(243, 243, 243, 0.78);
}
</style>

