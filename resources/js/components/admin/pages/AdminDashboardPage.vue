<template>
    <section class="dashboard-page">
        <header class="dashboard-header">
            <div class="dashboard-header__intro">
                <h2 class="dashboard-header__title">Resumen general</h2>
                <p class="dashboard-header__subtitle">
                    Estado actual del negocio, actividad reciente y accesos rápidos para administración.
                </p>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- <section class="dashboard-section">
                <h3 class="dashboard-section__title">Indicadores clave</h3>

                <div class="dashboard-grid dashboard-grid--metrics">
                    <article class="metric-card metric-card--templates">
                        <div class="metric-card__header">
                            <span class="metric-card__icon">
                                <i class="pi pi-clone" aria-hidden="true"></i>
                            </span>
                            <div class="metric-card__tags">
                                <span class="metric-card__badge metric-card__badge--primary">Catálogo</span>
                                <span class="metric-card__badge">Plantillas</span>
                            </div>
                        </div>

                        <div class="metric-card__body">
                            <p class="metric-card__value">—</p>
                            <p class="metric-card__label">Plantillas publicadas</p>
                        </div>

                        <footer class="metric-card__footer">
                            <button type="button" class="metric-card__link" @click="goTo('admin.templates')">
                                Gestionar plantillas
                                <i class="pi pi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </footer>
                    </article>

                    <article class="metric-card metric-card--plans">
                        <div class="metric-card__header">
                            <span class="metric-card__icon">
                                <i class="pi pi-briefcase" aria-hidden="true"></i>
                            </span>
                            <div class="metric-card__tags">
                                <span class="metric-card__badge metric-card__badge--primary">Stripe</span>
                                <span class="metric-card__badge">Planes</span>
                            </div>
                        </div>

                        <div class="metric-card__body">
                            <p class="metric-card__value">—</p>
                            <p class="metric-card__label">Planes activos</p>
                        </div>

                        <footer class="metric-card__footer">
                            <button type="button" class="metric-card__link" @click="goTo('admin.plans')">
                                Ver planes
                                <i class="pi pi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </footer>
                    </article>

                    <article class="metric-card metric-card--subscriptions">
                        <div class="metric-card__header">
                            <span class="metric-card__icon">
                                <i class="pi pi-id-card" aria-hidden="true"></i>
                            </span>
                            <div class="metric-card__tags">
                                <span class="metric-card__badge metric-card__badge--primary">Clientes</span>
                                <span class="metric-card__badge">Suscripciones</span>
                            </div>
                        </div>

                        <div class="metric-card__body">
                            <p class="metric-card__value">—</p>
                            <p class="metric-card__label">Suscripciones activas</p>
                        </div>

                        <footer class="metric-card__footer">
                            <button type="button" class="metric-card__link" @click="goTo('admin.subscriptions')">
                                Revisar suscripciones
                                <i class="pi pi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </footer>
                    </article>

                    <article class="metric-card metric-card--services">
                        <div class="metric-card__header">
                            <span class="metric-card__icon">
                                <i class="pi pi-list" aria-hidden="true"></i>
                            </span>
                            <div class="metric-card__tags">
                                <span class="metric-card__badge metric-card__badge--primary">Servicios</span>
                                <span class="metric-card__badge">Portafolio</span>
                            </div>
                        </div>

                        <div class="metric-card__body">
                            <p class="metric-card__value">—</p>
                            <p class="metric-card__label">Servicios publicados</p>
                        </div>

                        <footer class="metric-card__footer">
                            <button type="button" class="metric-card__link" @click="goTo('admin.services')">
                                Gestionar servicios
                                <i class="pi pi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </footer>
                    </article>
                </div>
            </section> -->

            <section class="dashboard-section">
                <h3 class="dashboard-section__title">Acciones rápidas</h3>

                <div class="dashboard-grid dashboard-grid--actions">
                    <button type="button" class="action-card" @click="goTo('admin.templates')">
                        <div class="action-card__icon action-card__icon--primary">
                            <i class="pi pi-plus" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="action-card__title">Nueva plantilla</p>
                            <p class="action-card__subtitle">Sube el paquete, imagen de preview y define el precio.</p>
                        </div>
                    </button>

                    <button type="button" class="action-card" @click="goTo('admin.plans')">
                        <div class="action-card__icon action-card__icon--secondary">
                            <i class="pi pi-dollar" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="action-card__title">Configurar plan</p>
                            <p class="action-card__subtitle">Crea un plan en Stripe y sincronízalo con Essential.</p>
                        </div>
                    </button>

                    <button type="button" class="action-card" @click="goTo('admin.services')">
                        <div class="action-card__icon action-card__icon--tertiary">
                            <i class="pi pi-globe" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="action-card__title">Actualizar servicios</p>
                            <p class="action-card__subtitle">Mantén el portafolio alineado a la oferta comercial.</p>
                        </div>
                    </button>

                    <button type="button" class="action-card" @click="goTo('admin.contact')">
                        <div class="action-card__icon action-card__icon--accent">
                            <i class="pi pi-envelope" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="action-card__title">Datos de contacto</p>
                            <p class="action-card__subtitle">Actualiza redes sociales, horarios y datos públicos.</p>
                        </div>
                    </button>
                </div>
            </section>

            <section class="dashboard-section">
                <h3 class="dashboard-section__title">Actividad reciente</h3>

                <div class="activity-card">
                    <div class="activity-card__header">
                        <span class="activity-card__badge">Coming soon</span>
                        <p class="activity-card__summary">
                            Conexión con Stripe y registros de compras para mostrar eventos recientes.
                        </p>
                    </div>

                    <div class="activity-card__placeholder">
                        <span class="activity-card__icon">
                            <i class="pi pi-chart-line" aria-hidden="true"></i>
                        </span>
                        <p>Próximamente: timeline de suscripciones, descargas y ventas puntuales.</p>
                    </div>
                </div>
            </section>
        </div>
    </section>
</template>

<script setup>
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const goTo = (name) => {
        if (!name) {
            return;
        }
        router.push({ name });
    };
</script>

<style scoped>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .dashboard-header__intro {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .dashboard-header__title {
        font-family: 'Space Mono', monospace;
        font-size: 24px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin: 0;
    }

    .dashboard-header__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.75;
        margin: 0;
        max-width: 520px;
    }

    .dashboard-content {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .dashboard-section {
        background: rgba(255, 255, 255, 0.88);
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 30px 28px;
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    @media (min-width: 768px) {
        .dashboard-section {
            padding: 32px 36px;
        }
    }

    .dashboard-section__title {
        font-family: 'Space Mono', monospace;
        font-size: 15px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin: 0;
        color: rgba(23, 23, 23, 0.78);
    }

    .dashboard-grid {
        display: grid;
        gap: 18px;
    }

    @media (min-width: 768px) {
        .dashboard-grid--metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1180px) {
        .dashboard-grid--metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (min-width: 768px) {
        .dashboard-grid--actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .metric-card {
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #141414;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 20px 22px;
        color: #ffffff;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.02);
    }

    .metric-card__header {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .metric-card__icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .metric-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .metric-card__badge {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.88);
    }

    .metric-card__badge--primary {
        background: rgba(221, 51, 51, 0.18);
        color: #ff6666;
    }

    .metric-card__body {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .metric-card__value {
        font-family: 'Lexend', sans-serif;
        font-weight: 500;
        font-size: 30px;
        margin: 0;
    }

    .metric-card__label {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.65);
        margin: 0;
    }

    .metric-card__footer {
        margin-top: auto;
    }

    .metric-card__link {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.85);
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 0;
    }

    .metric-card__link:hover {
        color: #ffffff;
    }

    .action-card {
        display: flex;
        gap: 16px;
        padding: 18px 20px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        text-align: left;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.06);
    }

    .action-card__icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .action-card__icon--primary {
        background: rgba(221, 51, 51, 0.14);
        color: #dd3333;
    }

    .action-card__icon--secondary {
        background: rgba(33, 150, 243, 0.14);
        color: #2196f3;
    }

    .action-card__icon--tertiary {
        background: rgba(76, 175, 80, 0.14);
        color: #4caf50;
    }

    .action-card__icon--accent {
        background: rgba(156, 39, 176, 0.14);
        color: #9c27b0;
    }

    .action-card__title {
        font-family: 'Lexend', sans-serif;
        font-size: 15px;
        margin: 0;
        color: #171717;
    }

    .action-card__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        line-height: 1.45;
        margin: 4px 0 0;
        color: rgba(23, 23, 23, 0.65);
    }

    .activity-card {
        background: rgba(23, 23, 23, 0.94);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 24px 26px;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .activity-card__header {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .activity-card__badge {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        align-self: flex-start;
    }

    .activity-card__summary {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
    }

    .activity-card__placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 28px 20px;
        border: 1px dashed rgba(255, 255, 255, 0.16);
        border-radius: 16px;
        text-align: center;
    }

    .activity-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
    }

    .activity-card__placeholder p {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.65);
        margin: 0;
    }
</style>
