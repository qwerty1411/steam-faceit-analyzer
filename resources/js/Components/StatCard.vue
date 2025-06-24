<template>
    <div class="bg-white dark:bg-gray-800 shadow rounded-b-xl p-4 text-center transition-colors duration-300">
        <div class="text-gray-500 dark:text-gray-300">{{ title }}</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            <slot>{{ value }}</slot>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: String,
    value: [Number, String]
})
</script>
