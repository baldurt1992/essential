<template>
    <section class="admin-page">
        <header class="admin-page__header">
            <div>
                <h2 class="admin-page__title">Planes</h2>
                <p class="admin-page__subtitle">
                    Crea y controla tus planes de suscripción: intervalos, precios y beneficios asociados.
                </p>
            </div>
            <Button label="Nuevo plan" icon="pi pi-plus" class="admin-page__action" />
        </header>

        <div class="admin-page__content">
            <div class="admin-placeholder">
                <span class="admin-placeholder__emoji">🗓️</span>
                <p class="admin-placeholder__text">
                    Próximamente: listado de planes con sincronización a Stripe, detalles de intervalos y planes destacados.
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
    import Button from 'primevue/button';
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

    .admin-page__action :deep(.p-button) {
        background: #3b44cd;
        border: none;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-page__content {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 40px;
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .admin-placeholder {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 420px;
    }

    .admin-placeholder__emoji {
        font-size: 40px;
    }

    .admin-placeholder__text {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.75);
    }
</style>
