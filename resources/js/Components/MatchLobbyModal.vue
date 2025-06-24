<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-900 w-full max-w-5xl rounded-lg shadow-lg overflow-hidden transition-colors duration-300">
            <!-- Заголовок -->
            <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Составы команд</h2>
                <button @click="emit('close')" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            </div>

            <!-- Содержимое -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Команда 1 -->
                <div>
                    <h3 :class="teamClass('faction1')" class="font-semibold mb-4 text-lg">
                        {{ match.teams.faction1.nickname || 'Команда 1' }}
                    </h3>
                    <ul class="space-y-4">
                        <li
                            v-for="player in match.teams.faction1.players"
                            :key="player.player_id"
                            class="flex items-center space-x-4"
                        >
                            <img
                                :src="player.avatar || fallback"
                                alt="avatar"
                                class="w-12 h-12 rounded-full border border-gray-300 dark:border-gray-700 shadow"
                            />
                            <div class="flex-1">
                                <button
                                    @click.stop="select(player.nickname)"
                                    :class="buttonClass('faction1')"
                                    class="font-medium hover:underline dark:text-white"
                                >
                                    {{ player.nickname }}
                                </button>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Steam: {{ player.game_player_name || '—' }}<br />
                                    Уровень: {{ player.skill_level || '—' }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Команда 2 -->
                <div>
                    <h3 :class="teamClass('faction2')" class="font-semibold mb-4 text-lg">
                        {{ match.teams.faction2.nickname || 'Команда 2' }}
                    </h3>
                    <ul class="space-y-4">
                        <li
                            v-for="player in match.teams.faction2.players"
                            :key="player.player_id"
                            class="flex items-center space-x-4"
                        >
                            <img
                                :src="player.avatar || fallback"
                                alt="avatar"
                                class="w-12 h-12 rounded-full border border-gray-300 dark:border-gray-700 shadow"
                            />
                            <div class="flex-1">
                                <button
                                    @click.stop="select(player.nickname)"
                                    :class="buttonClass('faction2')"
                                    class="font-medium hover:underline dark:text-white"
                                >
                                    {{ player.nickname }}
                                </button>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Steam: {{ player.game_player_name || '—' }}<br />
                                    Уровень: {{ player.skill_level || '—' }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Подвал -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 text-right">
                <button
                    @click="emit('close')"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    match: Object,
    visible: Boolean
})

const emit = defineEmits(['close', 'select'])

const fallback = 'https://ui-avatars.com/api/?name=Player&background=aaa&color=fff'

function teamClass(faction) {
    const winner = props.match.results?.winner
    if (!winner) return 'text-gray-800 dark:text-gray-100'

    if (faction === winner) return 'text-green-600 dark:text-green-400'
    else return 'text-red-600 dark:text-red-400'
}

function buttonClass(faction) {
    const winner = props.match.results?.winner
    if (!winner) return 'text-blue-600 dark:text-blue-400'

    if (faction === winner) return 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300'
    else return 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300'
}

function select(nickname) {
    emit('select', nickname)
    emit('close')
}
</script>
