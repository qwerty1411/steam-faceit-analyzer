<template>
    <div class="border rounded-xl overflow-hidden" :class="isWinner ? 'border-green-500/30' : 'border-gray-200 dark:border-gray-700'">
        <div
            class="p-3 font-semibold"
            :class="isWinner ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
        >
            Состав команды
        </div>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <li
                v-for="player in team.players"
                :key="player.player_id"
                class="p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
            >
                <div class="flex items-center space-x-3">
                    <img
                        :src="player.avatar || fallbackAvatar"
                        alt="avatar"
                        class="w-10 h-10 rounded-full border border-gray-300 dark:border-gray-600"
                    />
                    <div class="flex-1 min-w-0">
                        <button
                            @click.stop="$emit('select', player.nickname)"
                            class="text-sm font-medium truncate hover:underline"
                            :class="isWinner ? 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300' : 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300'"
                        >
                            {{ player.nickname }}
                        </button>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <div>Уровень: {{ player.skill_level || '—' }}</div>
                            <div>ELO: {{ player.faceit_elo || '—' }}</div>
                        </div>
                    </div>
                    <div class="text-xs bg-gray-100 dark:bg-gray-700 rounded-full px-2 py-1">
                        {{ player.game_player_name || 'Steam' }}
                    </div>
                </div>
            </li>
        </ul>
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

const emit = defineEmits(['select'])

const fallbackAvatar = 'https://ui-avatars.com/api/?name=Player&background=random&color=fff&size=128'
</script>
