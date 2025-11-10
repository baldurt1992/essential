<template>
    <section class="admin-page">
        <header class="admin-page__header">
            <h2 class="admin-page__title">Información de contacto</h2>
            <p class="admin-page__subtitle">
                Actualiza correo, teléfono y mensajes que aparecen en el footer y en los correos a clientes.
            </p>
        </header>

        <div class="admin-page__content">
            <div class="admin-placeholder">
                <span class="admin-placeholder__emoji">✉️</span>
                <p class="admin-placeholder__text">
                    Próximamente: formulario para editar los datos de contacto y enlaces sociales que muestra la web.
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
        max-width: 520px;
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
