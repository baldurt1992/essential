<template>
    <section class="admin-page admin-services-hero">
        <AdminPageHeader title="Video de cabecera · Servicios"
            subtitle="Reemplaza el video de fondo de la página /servicios. Se muestra en bucle, sin sonido, llenando toda la cabecera sin deformarse." />

        <div class="admin-page__content">
            <AdminLoader v-if="isLoading" message="Cargando configuración del video…" />

            <template v-else>
                <Message v-if="generalError" severity="error" class="admin-services-hero__message" closable
                    @close="clearError">
                    {{ generalError }}
                </Message>

                <div class="admin-services-hero__grid">
                    <section class="admin-services-hero__panel">
                        <h3 class="admin-services-hero__panel-title">Reproducción actual</h3>
                        <p class="admin-services-hero__hint">
                            Vista previa con las mismas reglas que el sitio: recorte uniforme (cover), sin bandas.
                        </p>
                        <div class="admin-services-hero__preview-frame">
                            <video v-if="previewUrl" ref="previewVideoRef" class="admin-services-hero__preview-video"
                                muted playsinline loop autoplay preload="auto" :src="previewUrl" />
                        </div>
                        <p v-if="hero?.updated_at" class="admin-services-hero__meta">
                            Última actualización: {{ formatDate(hero.updated_at) }}
                        </p>
                        <p v-if="hero?.uses_uploaded_video" class="admin-services-hero__badge">
                            Video personalizado activo
                        </p>
                        <p v-else class="admin-services-hero__badge admin-services-hero__badge--muted">
                            Video por defecto del proyecto
                        </p>
                    </section>

                    <section class="admin-services-hero__panel">
                        <h3 class="admin-services-hero__panel-title">Recomendaciones técnicas</h3>
                        <ul class="admin-services-hero__specs">
                            <li><strong>Formato:</strong> MP4 (H.264) o WebM (VP9). MP4 suele dar mejor compatibilidad
                                móvil.</li>
                            <li><strong>Resolución sugerida:</strong> 1920×1080 (16:9); mínimo 1280×720 para pantallas
                                grandes.</li>
                            <li><strong>Velocidad de bits:</strong> ~4–8 Mbps para 1080p; evita archivos enormes.</li>
                            <li><strong>Duración:</strong> bucles cortos (10–30 s) mejoran carga y repetición fluida.
                            </li>
                            <li><strong>Peso máximo:</strong> 50 MB (límite del servidor).</li>
                            <li><strong>Audio:</strong> no es necesario; la página reproduce silenciado.</li>
                        </ul>

                        <h3 class="admin-services-hero__panel-title">Subir nuevo video</h3>
                        <p class="admin-services-hero__hint">
                            Al guardar, el archivo anterior en servidor se elimina y se sirve el nuevo desde
                            <code>/storage</code>.
                        </p>

                        <input ref="fileInput" type="file" accept="video/mp4,video/webm,.mp4,.webm"
                            class="admin-services-hero__file" @change="onFileSelected" />

                        <div class="admin-services-hero__actions">
                            <button type="button" class="essential-button essential-button--ghost"
                                @click="triggerFilePick" :disabled="isSaving">
                                Elegir archivo
                            </button>
                            <button type="button" class="essential-button essential-button--primary"
                                @click="handleSubmit" :disabled="isSaving || !pendingFile">
                                {{ isSaving ? 'Subiendo…' : 'Reemplazar video' }}
                            </button>
                        </div>
                        <small v-if="pendingFileName" class="admin-services-hero__file-name">{{ pendingFileName }}</small>
                        <small v-if="fieldError" class="admin-services-hero__field-error">{{ fieldError }}</small>
                    </section>
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
    import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
    import Message from 'primevue/message';
    import AdminPageHeader from '../ui/AdminPageHeader.vue';
    import AdminLoader from '../ui/AdminLoader.vue';
    import { useAdminServicesHero } from '@/composables/admin/useAdminServicesHero';
    import { useToast } from 'primevue/usetoast';

    const toast = useToast();
    const {
        hero,
        isLoading,
        isSaving,
        error,
        fetchHero,
        uploadVideo,
        clearError,
    } = useAdminServicesHero();

    const fileInput = ref(null);
    const previewVideoRef = ref(null);
    const pendingFile = ref(null);
    const pendingFileName = ref('');
    const blobPreviewUrl = ref(null);

    watch(pendingFile, (file) => {
        if (file) {
            if (blobPreviewUrl.value?.startsWith('blob:')) {
                URL.revokeObjectURL(blobPreviewUrl.value);
            }
            blobPreviewUrl.value = URL.createObjectURL(file);
            return;
        }

        const toRevoke = blobPreviewUrl.value;
        blobPreviewUrl.value = null;

        if (toRevoke?.startsWith('blob:')) {
            nextTick(() => {
                requestAnimationFrame(() => {
                    URL.revokeObjectURL(toRevoke);
                });
            });
        }
    });

    const previewUrl = computed(() => blobPreviewUrl.value || hero.value?.video_url || '');

    watch(
        previewUrl,
        async (url) => {
            if (!url) {
                return;
            }
            await nextTick();
            const el = previewVideoRef.value;
            if (!el) {
                return;
            }
            el.load();
            el.play().catch(() => {});
        },
        { flush: 'post' },
    );

    onBeforeUnmount(() => {
        if (blobPreviewUrl.value?.startsWith('blob:')) {
            URL.revokeObjectURL(blobPreviewUrl.value);
        }
    });

    const generalError = computed(() => {
        const err = error.value;
        if (!err) {
            return null;
        }
        if (typeof err === 'string') {
            return err;
        }
        if (err.message) {
            return err.message;
        }
        return 'Ocurrió un error. Revisa el archivo e inténtalo de nuevo.';
    });

    const fieldError = computed(() => {
        const err = error.value;
        if (!err || !err.errors?.video) {
            return null;
        }
        const v = err.errors.video;
        return Array.isArray(v) ? v[0] : v;
    });

    const formatDate = (iso) => {
        if (!iso) {
            return '';
        }
        try {
            return new Intl.DateTimeFormat('es-CO', {
                dateStyle: 'short',
                timeStyle: 'short',
            }).format(new Date(iso));
        } catch {
            return iso;
        }
    };

    const triggerFilePick = () => {
        fileInput.value?.click();
    };

    const onFileSelected = (event) => {
        clearError();
        const file = event.target.files?.[0];
        if (!file) {
            pendingFile.value = null;
            pendingFileName.value = '';
            return;
        }
        pendingFile.value = file;
        pendingFileName.value = file.name;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    };

    const handleSubmit = async () => {
        if (!pendingFile.value) {
            return;
        }
        clearError();
        try {
            await uploadVideo(pendingFile.value);
            pendingFile.value = null;
            pendingFileName.value = '';
            toast.add({
                severity: 'success',
                summary: 'Video actualizado',
                detail: 'La cabecera de /servicios usará el nuevo archivo de inmediato.',
                life: 4000,
            });
        } catch {
            /* error state handled in composable */
        }
    };

    onMounted(() => {
        fetchHero().catch(() => {});
    });
</script>

<style scoped>
    .admin-services-hero__message {
        margin-bottom: 1.25rem;
    }

    .admin-services-hero__grid {
        display: grid;
        gap: 1.75rem;
        grid-template-columns: 1fr 1fr;
        align-items: start;
    }

    @media (max-width: 960px) {
        .admin-services-hero__grid {
            grid-template-columns: 1fr;
        }
    }

    .admin-services-hero__panel {
        background: var(--surface-card, #fff);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        padding: 1.5rem;
    }

    body.dark-mode .admin-services-hero__panel {
        background: #151520;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .admin-services-hero__panel-title {
        margin: 0 0 0.5rem;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .admin-services-hero__hint {
        margin: 0 0 1rem;
        font-size: 0.9rem;
        opacity: 0.85;
        line-height: 1.5;
    }

    .admin-services-hero__preview-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        max-height: 320px;
        border-radius: 12px;
        overflow: hidden;
        background: #0a0a0d;
    }

    .admin-services-hero__preview-video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .admin-services-hero__meta {
        margin: 0.75rem 0 0;
        font-size: 0.85rem;
        opacity: 0.75;
    }

    .admin-services-hero__badge {
        margin: 0.5rem 0 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #15803d;
    }

    .admin-services-hero__badge--muted {
        color: #6b7280;
        font-weight: 500;
    }

    .admin-services-hero__specs {
        margin: 0 0 1.5rem;
        padding-left: 1.2rem;
        line-height: 1.55;
        font-size: 0.9rem;
    }

    .admin-services-hero__specs li {
        margin-bottom: 0.45rem;
    }

    .admin-services-hero__file {
        display: none;
    }

    .admin-services-hero__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .admin-services-hero__file-name {
        display: block;
        margin-top: 0.5rem;
        word-break: break-all;
    }

    .admin-services-hero__field-error {
        display: block;
        margin-top: 0.5rem;
        color: #b91c1c;
        font-size: 0.85rem;
    }
</style>
