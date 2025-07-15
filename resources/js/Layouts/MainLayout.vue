<template>
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <Header @reset="onReset" @toggle-theme="toggleTheme">
            <template #menu>
                <a href="#" @click.prevent="$emit('nav', 'home')" class="hover:text-blue-600 dark:hover:text-blue-400">Главная</a>
                <a href="#" @click.prevent="$emit('nav', 'about')" class="hover:text-blue-600 dark:hover:text-blue-400">О сервисе</a>
                <a href="#contacts" class="hover:text-blue-600 dark:hover:text-blue-400">Контакты</a>
            </template>
            <template #theme-toggle>
                <slot name="theme-toggle" />
            </template>
            <template #auth>
                <slot name="auth" />
            </template>
        </Header>

        <main class="flex-grow container mx-auto p-6">
            <slot />
        </main>

        <Footer />
    </div>
</template>

<script setup>
import Header from '@/Layouts/Header.vue'
import Footer from '@/Layouts/Footer.vue'

defineEmits(['reset', 'nav'])

const props = defineProps({
    toggleTheme: Function,
    onReset: Function
})
</script>
