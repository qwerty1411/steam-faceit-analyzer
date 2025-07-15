<template>
    <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <div class="logo" @click="$emit('reset')">
                <div class="logo-inner"></div>
            </div>
            <h1 class="text-2xl font-bold text-blue-600 cursor-pointer" @click="$emit('reset')">Faceit TJ</h1>
        </div>
        <nav class="space-x-4 text-gray-600 dark:text-gray-300 flex items-center">
            <slot name="menu" />
            <button @click="$emit('toggle-theme')" class="ml-6 p-2 rounded-full border border-gray-300 dark:border-gray-600">
                <slot name="theme-toggle" />
            </button>
            <slot name="auth" />
        </nav>
    </header>
</template>

<script setup>
defineEmits(['reset', 'toggle-theme'])
</script>
