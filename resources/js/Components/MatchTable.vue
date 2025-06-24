<template>
    <div>
        <!-- Таблица матчей -->
        <table class="w-full border-collapse shadow-sm rounded overflow-hidden text-sm">
            <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 uppercase font-medium">
                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-700 text-left">Дата</th>
                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-700 text-left">Противники</th>
                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-700 text-left">Счёт</th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(match, index) in matches"
                :key="match.match_id"
                @click="openLobby(match)"
                class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                :class="[
                    index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-950'
                ]"
            >
                <td class="px-4 py-2 text-gray-700 dark:text-gray-300 font-mono whitespace-nowrap">
                    {{ new Date(match.started_at * 1000).toLocaleDateString() }}
                </td>
                <td class="px-4 py-2 text-gray-800 dark:text-gray-100 font-normal whitespace-nowrap">
                    {{ match.teams?.faction1?.nickname || '–' }}
                    <span class="mx-2 text-gray-400 dark:text-gray-500">vs</span>
                    {{ match.teams?.faction2?.nickname || '–' }}
                </td>
                <td class="px-4 py-2 text-gray-900 dark:text-gray-100 font-semibold flex items-center space-x-2">
                    <span>
                      {{ match.results?.score.faction1 ?? '-' }} - {{ match.results?.score.faction2 ?? '-' }}
                    </span>
                    <svg
                        v-if="isWin(match)"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-green-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg
                        v-else-if="isLoss(match)"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-red-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Модалка с лобби -->
        <MatchLobbyModal
            v-if="selectedMatch"
            :visible="!!selectedMatch"
            :match="selectedMatch"
            @close="closeLobby"
            @select="handleSelect"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import MatchLobbyModal from '@/Components/MatchLobbyModal.vue'

const props = defineProps({ matches: Array })
const emit = defineEmits(['select-player'])

const selectedMatch = ref(null)

function openLobby(match) {
    selectedMatch.value = match
}

function closeLobby() {
    selectedMatch.value = null
}

function handleSelect(nickname) {
    emit('select-player', nickname)
}

function isWin(match) {
    return match.results?.score?.faction1 < match.results?.score?.faction2
}
function isLoss(match) {
    return match.results?.score?.faction1 > match.results?.score?.faction2
}
</script>
