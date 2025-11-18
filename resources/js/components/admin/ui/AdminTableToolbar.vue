<template>
    <div class="admin-table__toolbar">
        <div class="admin-table__meta">
            <span class="admin-table__count">{{ count }} {{ entityName }}</span>
            <span v-if="isRefreshing" class="admin-table__refresh">Actualizando…</span>
        </div>
        <slot />
    </div>
</template>

<script setup>
defineProps({
    count: {
        type: Number,
        required: true,
    },
    entityName: {
        type: String,
        required: true,
    },
    isRefreshing: {
        type: Boolean,
        default: false,
    },
});
</script>

