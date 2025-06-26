<template>
    <div class="space-y-4">
        <!-- Фильтры -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">Показать:</label>
                <select
                    v-model="matchFilter"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-1 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="all">Все матчи</option>
                    <option value="wins">Только победы</option>
                    <option value="losses">Только поражения</option>
                </select>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Всего матчей: {{ filteredMatches.length }} ({{ winCount }} побед, {{ lossCount }} поражений)
            </div>
        </div>

        <!-- Таблица матчей -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Противники</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Счёт</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                    v-for="match in filteredMatches"
                    :key="match.match_id"
                    @click="openMatch(match)"
                    class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-700 dark:text-gray-300">
                        {{ formatDate(match.started_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ match.teams?.faction1?.nickname || 'Команда 1' }} vs {{ match.teams?.faction2?.nickname || 'Команда 2' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ match.results?.score?.faction1 ?? '0' }} : {{ match.results?.score?.faction2 ?? '0' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
              <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getStatusClasses(match)"
              >
                {{ matchResultText(match) }}
              </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Модалка -->
        <MatchLobbyModal
            v-if="selectedMatchId"
            :match-id="selectedMatchId"
            @close="selectedMatchId = null"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import MatchLobbyModal from './MatchLobbyModal.vue'

const props = defineProps({
    matches: Array,
    currentPlayerId: String
})

const matchFilter = ref('all')
const selectedMatchId = ref(null)

function getPlayerTeam(match) {
    if (match.teams?.faction1?.players?.some(p => p.player_id === props.currentPlayerId)) return 'faction1'
    if (match.teams?.faction2?.players?.some(p => p.player_id === props.currentPlayerId)) return 'faction2'
    return null
}

function isWin(match) {
    const team = getPlayerTeam(match)
    return team && match.results?.winner === team
}

function isLoss(match) {
    const team = getPlayerTeam(match)
    return team && match.results?.winner && match.results.winner !== team
}

function matchResultText(match) {
    if (!match.results?.winner) return 'В процессе'
    return isWin(match) ? 'Победа' : 'Поражение'
}

function getStatusClasses(match) {
    if (!match.results?.winner) {
        return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    }
    return isWin(match)
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
}

function formatDate(timestamp) {
    if (!timestamp) return '—'
    return new Date(timestamp * 1000).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const filteredMatches = computed(() => {
    let result = props.matches
    if (matchFilter.value === 'wins') result = result.filter(isWin)
    if (matchFilter.value === 'losses') result = result.filter(isLoss)
    return result
})

const winCount = computed(() => props.matches.filter(isWin).length)
const lossCount = computed(() => props.matches.filter(isLoss).length)

function openMatch(match) {
    selectedMatchId.value = match.match_id
}
</script>
