<template>
    <div class="flex flex-col items-center">
        <div
            class="w-16 h-16 rounded-full flex items-center justify-center mb-2"
            :class="isWinner ? 'bg-green-100 dark:bg-green-900/30 border-2 border-green-500' : 'bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700'"
        >
            <span class="text-xl font-bold" :class="isWinner ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'">
                {{ team.nickname?.charAt(0) || 'T' }}
            </span>
        </div>
        <h3
            class="font-semibold text-center"
            :class="isWinner ? 'text-green-600 dark:text-green-400' : 'text-gray-800 dark:text-gray-200'"
        >
            {{ team.nickname || 'Команда' }}
        </h3>
    </div>
</template>

<script setup>
defineProps({
    team: {
        type: Object,
        required: true
    },
    isWinner: {
        type: Boolean,
        default: false
    }
})
</script>
