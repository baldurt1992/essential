<template>
    <li :class="['menu-item', 'menu-item-type-custom', 'menu-item-object-custom', { 'menu-item-has-children': hasChildren, 'qodef-menu-item--narrow': hasChildren }]">
        <a v-if="!hasChildren" :href="link">
            <span class="qodef-menu-item-text">{{ text }}</span>
        </a>
        <a v-else>
            <span class="qodef-menu-item-text">{{ text }}</span>
        </a>
        
        <div v-if="hasChildren" class="qodef-drop-down-second">
            <div class="qodef-drop-down-second-inner">
                <ul class="sub-menu">
                    <li v-for="subItem in subItems" :key="subItem.id" 
                        class="menu-item menu-item-type-custom menu-item-object-custom">
                        <a :href="subItem.link">
                            <span class="qodef-menu-item-text">{{ subItem.text }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
</template>

<script setup>
defineProps({
    text: {
        type: String,
        required: true
    },
    link: {
        type: String,
        default: '#'
    },
    hasChildren: {
        type: Boolean,
        default: false
    },
    subItems: {
        type: Array,
        default: () => []
    }
});
</script>

<style scoped>
/* Los estilos están en el Header.vue ya que son compartidos */
</style>

