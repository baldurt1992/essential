<template>
    <section class="services-intro">
        <div class="services-intro__container">
            <div class="services-intro__hero">
                <div class="essential-eyebrow">
                    <span class="essential-eyebrow__dot"></span>
                    <span class="essential-eyebrow__label">{{ eyebrow }}</span>
                </div>
                <h1 class="services-hero__title" v-html="title"></h1>
            </div>
            <div class="services-intro__content">
                <p class="services-hero__description">{{ description }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
    defineProps({
        eyebrow: {
            type: String,
            default: 'portafolio',
        },
        title: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            required: true,
        },
    });
</script>

<style scoped>
    .services-intro {
        position: relative;
        width: 100%;
        margin: 0;
        padding: 0;
        background: var(--essential-background-color);
        z-index: 1;
    }

    .services-intro__container {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: clamp(24px, 6vw, 80px);
        padding-top: 0;
        padding-bottom: 0;
        padding-left: 10.5%;
        padding-right: 10%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .services-intro__hero {
        flex: 0 0 auto;
        margin: 0;
        padding: 0;
        width: 50%;
    }

    .services-hero__title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 8vw, 56px);
        font-weight: 400;
        line-height: 1.05;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        color: var(--essential-heading-color);
        margin: 0;
        padding: 0;
    }

    .services-intro__content {
        flex: 1;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
        width: 50%;
    }

    .services-hero__description {
        font-family: var(--essential-font-body);
        font-size: 17px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.78);
        margin: 0;
        padding: 0;
        max-width: 100%;
        padding-left: 9%;
    }

    body.dark-mode .services-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    @media (max-width: 1024px) {
        .services-intro__container {
            flex-direction: row;
            gap: clamp(20px, 4vw, 40px);
            padding-left: 5%;
            padding-right: 5%;
        }

        .services-hero__title {
            font-size: clamp(28px, 6vw, 44px);
        }

        .services-hero__description {
            font-size: 14px;
        }
    }

    @media (max-width: 758px) {
        .services-intro__container {
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .services-intro__hero {
            width: 100%;
        }

        .services-hero__title {
            font-size: clamp(28px, 8vw, 40px);
        }

        .services-hero__description {
            font-size: 14px;
        }

        .services-intro__content {
            width: 100%;
        }
    }
</style>

