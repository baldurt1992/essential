<template>
    <li
        :class="['menu-item', 'menu-item-type-custom', 'menu-item-object-custom', { 'menu-item-has-children': hasChildren, 'essential-menu-item--narrow': hasChildren }]">
        <RouterLink :to="link" :exact="isExact" :active-class="''" :exact-active-class="''"
            :class="['essential-menu-item-link', { 'router-link-active': isActive }]">
            <span class="essential-menu-item-text">{{ text }}</span>
        </RouterLink>

        <div v-if="hasChildren" class="essential-drop-down-second">
            <div class="essential-drop-down-second-inner">
                <ul class="sub-menu">
                    <li v-for="subItem in subItems" :key="subItem.id"
                        class="menu-item menu-item-type-custom menu-item-object-custom">
                        <RouterLink :to="subItem.link" class="essential-menu-item-link">
                            <span class="essential-menu-item-text">{{ subItem.text }}</span>
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </div>
    </li>
</template>

<script setup>
    import { RouterLink, useRoute } from 'vue-router';
    import { computed } from 'vue';

    const props = defineProps({
        text: {
            type: String,
            required: true,
        },
        link: {
            type: [String, Object],
            default: '#',
        },
        hasChildren: {
            type: Boolean,
            default: false,
        },
        subItems: {
            type: Array,
            default: () => [],
        },
    });

    const route = useRoute();

    // Determinar si el link está activo basándome en el nombre de la ruta
    const isActive = computed(() => {
        if (typeof props.link === 'object' && props.link.name) {
            // Si el link tiene un nombre, comparar con el nombre de la ruta actual
            // Para home, también verificar que el path sea exactamente "/"
            if (props.link.name === 'home') {
                return route.name === 'home' && route.path === '/';
            }
            return route.name === props.link.name;
        }
        if (typeof props.link === 'string') {
            // Si es un string, comparar con el path completo
            // Si es "/", solo activar cuando el path sea exactamente "/"
            if (props.link === '/') {
                return route.path === '/';
            }
            return route.path === props.link;
        }
        return false;
    });

    // Usar exact solo para el link de home para evitar que se marque como activo en otras páginas
    const isExact = computed(() => {
        if (typeof props.link === 'object' && props.link.name === 'home') {
            return true;
        }
        if (typeof props.link === 'string' && props.link === '/') {
            return true;
        }
        // Para otros links, no usar exact ya que usamos nuestra lógica personalizada de isActive
        return false;
    });
</script>

<style scoped>
    .essential-menu-item-link {
        display: flex;
        align-items: center;
        padding: 0;
        color: inherit;
        text-decoration: none;
    }

    .essential-menu-item-link .essential-menu-item-text {
        font-family: "IBM Plex Mono", sans-serif;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.5625em;
        text-transform: uppercase;
        color: inherit;
        padding: 0 5px 1px;
        position: relative;
        transition: color 0.15s ease-out;
    }

    .essential-menu-item-link .essential-menu-item-text::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--qode-heading-color);
        transition: transform 0.42s cubic-bezier(0.33, 0.81, 0.11, 0.96);
        transform: scaleX(0);
        transform-origin: left;
        z-index: -1;
    }

    .essential-menu-item-link:hover .essential-menu-item-text {
        color: var(--qode-background-color);
    }

    .essential-menu-item-link:hover .essential-menu-item-text::before {
        transform: scaleX(1);
    }
</style>
