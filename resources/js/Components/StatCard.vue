<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="p-2 rounded-full" :class="iconBgClass">
                <slot name="icon">
                    <span class="text-white">{{ icon }}</span>
                </slot>
            </div>
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ title }}</div>
                <!-- Показываем value только если не используется slot -->
                <div v-if="!$slots.default" class="font-semibold dark:text-white">
                    {{ value }}
                </div>
                <div v-else>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String
})

const iconBgClass = computed(() => {
    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-amber-500']
    const randomIndex = Math.floor(Math.random() * colors.length)
    return colors[randomIndex]
})
</script>
