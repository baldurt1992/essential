<template>
    <div id="essential-side-area" ref="sideAreaRef" class="essential-side-area"
        :class="{ 'essential--opened': isOpen }">
        <!-- Close Button -->
        <a href="javascript:void(0)" id="essential-side-area-close"
            class="essential-opener-icon essential-m essential-source--predefined essential--opened"
            @click="closeSideArea">
            <span class="essential-m-icon">
                <svg class="essential-svg--menu" xmlns="http://www.w3.org/2000/svg" width="41.477" height="41.477"
                    viewBox="0 0 41.477 41.477">
                    <g>
                        <g transform="translate(8.257 8.257)">
                            <ellipse cx="12.715" cy="12.715" rx="12.715" ry="12.715" fill="none" stroke="currentColor"
                                stroke-width="1.5"></ellipse>
                            <ellipse cx="12.715" cy="12.715" rx="11.715" ry="11.715" fill="none" stroke="currentColor"
                                stroke-width="1.5"></ellipse>
                        </g>
                        <g>
                            <path d="M20.737 0v41.478" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                            <path d="M41.477 20.739H0" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                        </g>
                    </g>
                </svg>
            </span>
        </a>

        <!-- Side Area Inner Content -->
        <div id="essential-side-area-inner" class="essential-side-area-inner">
            <!-- Logo -->
            <div class="widget widget_moreau_core_single_image">
                <div
                    class="essential-shortcode essential-m essential-single-image essential-layout--default essential--retina">
                    <div class="essential-m-image">
                        <img loading="lazy" itemprop="image" src="/images/logo-web-negro.png" width="1000" height="150"
                            alt="Essential Innovation">
                    </div>
                </div>
            </div>

            <!-- Separator Line -->
            <div class="widget widget_moreau_core_separator">
                <div class="essential-shortcode essential-m essential-separator clear essential-show--yes">
                    <div class="essential-m-line"></div>
                </div>
            </div>

            <!-- Title -->
            <div class="widget widget_moreau_core_title_widget">
                <h2 class="essential-widget-title">
                    Ponte en Contacto
                </h2>
            </div>

            <!-- Company Info -->
            <div class="widget widget_block">
                <a href=" " target="_blank" rel="noopener">
                    ESSENTIAL INNOVATION<br>
                    CREATIVE DESINGS
                </a>
            </div>

            <!-- Email -->
            <div class="widget widget_block">
                <a href="mailto:infoessential.innovation@gmail.com">infoessential.innovation@gmail.com</a>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, onMounted, onBeforeUnmount } from 'vue';

    const props = defineProps({
        isOpen: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['update:isOpen', 'close']);

    const sideAreaRef = ref(null);

    const closeSideArea = () => {
        emit('update:isOpen', false);
        emit('close');
    };

    // Cerrar con tecla Escape
    const handleEscape = (e) => {
        if (e.key === 'Escape' && props.isOpen) {
            closeSideArea();
        }
    };

    // Cerrar al hacer click fuera del side area
    const handleClickOutside = (e) => {
        if (props.isOpen && sideAreaRef.value && !sideAreaRef.value.contains(e.target)) {
            closeSideArea();
        }
    };

    // Cerrar al hacer scroll en la página
    const handleScroll = () => {
        if (props.isOpen) {
            closeSideArea();
        }
    };

    // Agregar listeners cuando está abierto
    watch(() => props.isOpen, (newVal) => {
        if (newVal) {
            window.addEventListener('keydown', handleEscape);
            window.addEventListener('scroll', handleScroll, true);
            // Agregar listener con delay para evitar que el click que abre el menú también lo cierre
            setTimeout(() => {
                document.addEventListener('click', handleClickOutside);
            }, 100);
        } else {
            window.removeEventListener('keydown', handleEscape);
            window.removeEventListener('scroll', handleScroll, true);
            document.removeEventListener('click', handleClickOutside);
        }
    });

    onBeforeUnmount(() => {
        window.removeEventListener('keydown', handleEscape);
        window.removeEventListener('scroll', handleScroll, true);
        document.removeEventListener('click', handleClickOutside);
    });
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;500&display=swap');

    /* Side Area Base */
    #essential-side-area {
        position: fixed;
        top: 0;
        right: 0;
        width: 543px;
        max-width: 100%;
        height: 100vh;
        background-color: #DD3333;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
        overflow-x: hidden;
    }

    #essential-side-area.essential--opened {
        transform: translateX(0);
    }

    /* Close Button */
    #essential-side-area-close {
        position: absolute;
        top: 40px;
        right: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 41.477px;
        height: 41.477px;
        color: #ffffff;
        cursor: pointer;
        z-index: 1001;
        transition: transform 0.3s ease;
    }

    #essential-side-area-close:hover {
        transform: rotate(90deg);
    }

    #essential-side-area-close svg {
        display: block;
        width: 100%;
        height: 100%;
    }

    /* Side Area Inner Content */
    .essential-side-area-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh;
        padding: 120px 40px 40px;
        text-align: center;
    }

    /* Widget Styles */
    .widget {
        margin-bottom: 0;
        width: 100%;
        max-width: 600px;
    }

    /* Logo */
    .essential-single-image {
        margin-bottom: 55px;
        width: 100%;
    }

    .essential-m-image {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .essential-m-image img {
        max-width: 400px;
        width: 100%;
        height: auto;
        display: block;
    }

    /* Separator Line */
    .essential-separator {
        margin-bottom: 55px;
        width: 100%;
    }

    .essential-m-line {
        width: 100%;
        height: 1px;
        background-color: #3b44cd;
        border: none;
        margin: 0;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Title */
    .essential-widget-title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 5vw, 48px);
        font-weight: 700;
        line-height: 1.2;
        text-transform: uppercase;
        color: #ffffff;
        margin: 0 0 23px 0;
        text-align: center;
    }

    /* Company Info */
    .widget_block a {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        color: #ffffff;
        text-decoration: none;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 20px;
        letter-spacing: 0.08em;
        text-align: center;
    }

    .widget_block a:hover {
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }

    /* Email */
    .widget_block:last-child {
        margin-top: 10px;
    }

    .widget_block:last-child a {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        color: #ffffff;
        text-decoration: none;
        text-transform: none;
        letter-spacing: 0.02em;
    }

    /* Responsive */
    @media (max-width: 680px) {
        #essential-side-area-close {
            top: 20px;
            right: 20px;
        }

        .essential-side-area-inner {
            padding: 80px 20px 20px;
        }

        .essential-widget-title {
            font-size: 32px;
        }

        .widget_block a {
            font-size: 16px;
        }
    }
</style>
