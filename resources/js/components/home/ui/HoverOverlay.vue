<template>
    <div class="essential-e-hover-overlay">
        <div class="essential-e-hover-content">
            <div class="essential-e-hover-info-box">
                <div class="essential-e-hover-info">
                    <div class="essential-e-hover-border-line line-top"></div>
                    <div class="essential-e-hover-info-inner">
                        <slot name="category">
                            <span v-if="category">{{ category }}</span>
                        </slot>
                    </div>
                    <div class="essential-e-hover-border-line line-bottom"></div>
                </div>
                <div class="essential-e-hover-separator-line"></div>
                <div class="essential-e-hover-title-box">
                    <div class="essential-e-hover-border-line line-top"></div>
                    <h5 itemprop="name" class="essential-e-hover-title">
                        <slot name="title">
                            <a v-if="title && link" itemprop="url" class="essential-e-title-link" :href="link">
                                {{ title }}
                            </a>
                            <span v-else-if="title">{{ title }}</span>
                        </slot>
                    </h5>
                    <div class="essential-e-hover-border-line line-bottom"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        category: {
            type: String,
            default: null,
        },
        title: {
            type: String,
            default: null,
        },
        link: {
            type: String,
            default: null,
        },
    });
</script>

<style>
    .essential-e-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 40px;
    }

    .essential-e-hover-content {
        position: relative;
        width: 100%;
    }

    .essential-e-hover-info-box {
        position: relative;
    }

    .essential-e-hover-border-line {
        position: absolute;
        left: 0;
        width: 0;
        height: 1px;
        background-color: #fff !important;
    }

    .essential-e-hover-border-line.line-top {
        top: 0;
        transform-origin: left;
    }

    .essential-e-hover-border-line.line-bottom {
        bottom: 0;
        transform-origin: left;
    }

    .essential-e-hover-separator-line {
        width: 0;
        height: 1px;
        background-color: #fff !important;
        margin: 10px 0;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
    }

    .essential-e-hover-info,
    .essential-e-hover-title-box {
        position: relative;
        background-color: #171717 !important;
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        padding: 12px 20px;
        margin: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-20px);
        transition: opacity 0.4s ease 0.2s, transform 0.4s ease 0.2s, visibility 0.4s ease 0.2s, border-left 0s 0.6s, border-right 0s 0.6s;
    }

    .essential-e-hover-info {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        color: #fff !important;
        letter-spacing: 0.05em;
    }

    .essential-e-hover-info-inner {
        position: relative;
        z-index: 1;
        color: #fff !important;
    }

    .essential-e-hover-info a {
        color: #fff !important;
        text-decoration: none;
    }

    .essential-e-hover-title-box {
        margin-top: 15px;
    }

    .essential-e-hover-title {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        color: #fff !important;
        position: relative;
        z-index: 1;
    }

    .essential-e-hover-title a {
        color: #fff !important;
        text-decoration: none;
    }

    /* Note: Hover animations are controlled by parent component CSS */
    /* Parent should add styles like:
   .parent:hover .essential-e-hover-overlay { opacity: 1; visibility: visible; }
   .parent:hover .essential-e-hover-overlay .essential-e-hover-info .essential-e-hover-border-line.line-top {
       animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
   }
   etc.
*/

    @keyframes drawLineLeftToRight {
        from {
            width: 0;
        }

        to {
            width: 100%;
        }
    }

    @media (max-width: 1024px) {
        .essential-e-hover-overlay {
            display: none !important;
        }
    }
</style>
