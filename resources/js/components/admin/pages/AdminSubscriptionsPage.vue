<template>
    <section class="admin-page">
        <header class="admin-page__header">
            <h2 class="admin-page__title">Suscripciones</h2>
            <p class="admin-page__subtitle">
                Consulta las suscripciones activas, su estado de renovación y gestiona cancelaciones.
            </p>
        </header>

        <div class="admin-page__content">
            <div class="admin-placeholder">
                <span class="admin-placeholder__emoji">📊</span>
                <p class="admin-placeholder__text">
                    Aquí podrás filtrar suscripciones por estado, ver periodos actuales y contactar a tus clientes.
                </p>
            </div>
        </div>
    </section>
</template>

<style scoped>
    .admin-page {
        display: flex;
        flex-direction: column;
        gap: 26px;
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
        max-width: 540px;
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
        font-size: 42px;
    }

    .admin-placeholder__text {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.75);
    }
</style>
