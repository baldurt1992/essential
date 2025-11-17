<template>
    <div class="services-contact-section__form">
        <h2 class="services-contact-section__title">¿En qué podemos ayudarte?</h2>
        <form @submit.prevent="handleContactSubmit" class="services-contact-form">
            <div class="services-contact-form__field">
                <input v-model="contactForm.email" type="email" name="email" placeholder="E-mail" required
                    class="services-contact-form__input" />
            </div>
            <div class="services-contact-form__row">
                <div class="services-contact-form__field">
                    <input v-model="contactForm.firstName" type="text" name="first-name"
                        placeholder="Nombre" required class="services-contact-form__input" />
                </div>
                <div class="services-contact-form__field">
                    <input v-model="contactForm.lastName" type="text" name="last-name"
                        placeholder="Apellido" required class="services-contact-form__input" />
                </div>
            </div>
            <div class="services-contact-form__field">
                <textarea v-model="contactForm.message" name="message" placeholder="Mensaje" rows="5"
                    required class="services-contact-form__textarea"></textarea>
            </div>
            <button type="submit" class="services-contact-form__submit" :disabled="contactForm.submitting">
                <span v-if="!contactForm.submitting">enviar_mensaje</span>
                <span v-else>Enviando...</span>
            </button>
        </form>
    </div>
</template>

<script setup>
    import { useContactForm } from '@/composables/useContactForm';

    const { contactForm, handleContactSubmit } = useContactForm();
</script>

<style scoped>
    .services-contact-section__form {
        flex: 0 0 auto;
        width: 50%;
        max-width: 600px;
    }

    .services-contact-section__title {
        font-family: 'Lexend', sans-serif;
        font-weight: 400;
        color: var(--essential-heading-color);
        font-size: 56px;
        line-height: 1.07143em;
        text-transform: uppercase;
        margin: 25px 0 40px 0;
        word-wrap: break-word;
    }

    .services-contact-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .services-contact-form__row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .services-contact-form__field {
        position: relative;
        width: 100%;
    }

    .services-contact-form__input,
    .services-contact-form__textarea {
        width: 100%;
        border: none;
        border-bottom: 1px solid rgba(23, 23, 23, 0.6);
        background: transparent;
        font-family: var(--essential-font-body);
        font-size: 16px;
        color: var(--essential-text-color);
        padding: 12px 0;
        outline: none;
        transition: border-color 0.3s ease;
    }

    body.dark-mode .services-contact-form__input,
    body.dark-mode .services-contact-form__textarea {
        border-bottom-color: rgba(255, 255, 255, 0.6);
    }

    .services-contact-form__input:focus,
    .services-contact-form__textarea:focus {
        border-bottom-color: var(--essential-text-color);
    }

    .services-contact-form__input::placeholder,
    .services-contact-form__textarea::placeholder {
        color: rgba(23, 23, 23, 0.8);
        font-family: var(--essential-font-body);
    }

    body.dark-mode .services-contact-form__input::placeholder,
    body.dark-mode .services-contact-form__textarea::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .services-contact-form__textarea {
        resize: vertical;
        min-height: 120px;
        font-family: var(--essential-font-body);
    }

    .services-contact-form__submit {
        align-self: flex-start;
        border: none;
        background: #171717;
        color: #dd3333;
        font-family: var(--essential-font-menu);
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: lowercase;
        padding: 10px 20px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
        margin-top: 8px;
    }

    body.dark-mode .services-contact-form__submit {
        background: #ffffff;
    }

    .services-contact-form__submit:hover:not(:disabled) {
        background: #2a2a2a;
        transform: translateY(-2px);
    }

    body.dark-mode .services-contact-form__submit:hover:not(:disabled) {
        background: #e5e5e5;
    }

    .services-contact-form__submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @media (max-width: 1024px) {
        .services-contact-section__form {
            width: 100%;
            max-width: 100%;
        }

        .services-contact-section__title {
            font-size: clamp(32px, 8vw, 48px);
        }
    }

    @media (max-width: 758px) {
        .services-contact-form__row {
            grid-template-columns: 1fr;
            gap: 32px;
        }
    }
</style>

