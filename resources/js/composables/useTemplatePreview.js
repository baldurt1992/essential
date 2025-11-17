import { ref, watch, onMounted, onBeforeUnmount } from 'vue';

/**
 * Composable para manejar la lógica de preview de plantillas
 */
export function useTemplatePreview() {
    const previewVisible = ref(false);
    const selectedTemplate = ref(null);
    const previewDialogWidth = ref('92vw');

    const previewBreakpoints = {
        '1280px': '720px',
        '960px': '92vw',
    };

    const updatePreviewWidth = () => {
        if (window.innerWidth >= 1280) {
            previewDialogWidth.value = '900px';
            return;
        }
        if (window.innerWidth >= 960) {
            previewDialogWidth.value = '720px';
            return;
        }
        previewDialogWidth.value = '92vw';
    };

    const openPreview = (template) => {
        selectedTemplate.value = template;
        previewVisible.value = true;
    };

    const handleResize = () => {
        updatePreviewWidth();
    };

    watch(previewVisible, (visible) => {
        if (visible) {
            updatePreviewWidth();
        }
    });

    onMounted(() => {
        updatePreviewWidth();
        window.addEventListener('resize', handleResize, { passive: true });
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
    });

    return {
        previewVisible,
        selectedTemplate,
        previewDialogWidth,
        previewBreakpoints,
        openPreview,
    };
}

