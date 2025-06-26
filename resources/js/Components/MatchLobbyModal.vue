<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-900 w-full max-w-6xl rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white">FACEIT Match Details</h2>
                    <div v-if="match" class="flex flex-wrap items-center gap-x-4 text-white/90 text-sm mt-1">
                        <span>{{ formatDate(match.started_at) }}</span>
                        <span>•</span>
                        <span>{{ matchDuration }}</span>
                        <span>•</span>
                        <span>{{ match.competition_name }}</span>
                        <span v-if="hasLocation">•</span>
                        <span v-if="hasLocation" class="flex items-center">
              <img :src="getFlagImage(selectedLocation)" class="w-4 h-3 mr-1 rounded-sm" />
              {{ selectedLocation }}
            </span>
                    </div>
                </div>
                <button @click="emit('close')" class="text-white hover:text-gray-200 text-2xl transition-transform hover:rotate-90">
                    ×
                </button>
            </div>

            <!-- Loader -->
            <div v-if="isLoading" class="flex-1 flex items-center justify-center p-6">
                <div class="animate-pulse flex flex-col items-center">
                    <div class="h-8 w-8 bg-blue-500 rounded-full mb-2"></div>
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Loading match data...</span>
                </div>
            </div>

            <!-- Main Content -->
            <div v-else-if="match" class="p-6 overflow-y-auto flex-1">
                <!-- Map and Score -->
                <div class="flex flex-col items-center mb-8">
                    <div v-if="hasMap" class="relative w-full max-w-lg mb-6 rounded-xl overflow-hidden shadow-lg">
                        <img :src="selectedMapImage" class="w-full h-40 object-cover brightness-75" />
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-t from-black/60 to-transparent">
              <span class="text-white font-bold text-2xl drop-shadow-lg">
                {{ selectedMapName }}
              </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-8 w-full max-w-3xl">
                        <TeamBadge
                            :team="match.teams.faction1"
                            :isWinner="match.results?.winner === 'faction1'"
                            size="lg"
                            class="flex-1"
                        />

                        <div class="flex flex-col items-center min-w-[180px]">
                            <div class="text-5xl font-bold dark:text-white flex items-center space-x-4">
                                <span :class="scoreClass('faction1')">{{ match.results?.score?.faction1 ?? '0' }}</span>
                                <span class="text-gray-400">:</span>
                                <span :class="scoreClass('faction2')">{{ match.results?.score?.faction2 ?? '0' }}</span>
                            </div>
                            <div v-if="hasTeamStats" class="flex items-center mt-3 gap-2">
                <span class="text-xs px-2 py-1 rounded-full"
                      :class="match.results?.winner === 'faction1'
                        ? 'bg-green-500/20 text-green-500'
                        : 'bg-red-500/20 text-red-500'">
                  {{ (match.teams.faction1.stats.winProbability * 100).toFixed(0) }}% win
                </span>
                                <span class="text-xs px-2 py-1 rounded-full"
                                      :class="match.results?.winner === 'faction2'
                        ? 'bg-green-500/20 text-green-500'
                        : 'bg-red-500/20 text-red-500'">
                  {{ (match.teams.faction2.stats.winProbability * 100).toFixed(0) }}% win
                </span>
                            </div>
                        </div>

                        <TeamBadge
                            :team="match.teams.faction2"
                            :isWinner="match.results?.winner === 'faction2'"
                            size="lg"
                            class="flex-1"
                        />
                    </div>
                </div>

                <!-- Team Stats -->
                <div v-if="hasTeamStats" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-lg dark:text-white flex items-center">
                                <span class="truncate max-w-[180px]">{{ match.teams.faction1.name || 'Team 1' }}</span>
                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-blue-500/10 text-blue-500">
                  {{ match.teams.faction1.stats.rating }} rating
                </span>
                            </h3>
                            <div class="flex items-center space-x-2">
                <span class="text-xs px-2 py-0.5 rounded bg-purple-500/10 text-purple-500">
                  Avg: {{ match.teams.faction1.stats.skillLevel.average }}
                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Level range</div>
                                <div class="font-semibold dark:text-white">
                                    {{ match.teams.faction1.stats.skillLevel.range.min }}
                                    -
                                    {{ match.teams.faction1.stats.skillLevel.range.max }}
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Premium</div>
                                <div class="font-semibold dark:text-white">
                                    {{ premiumCount(match.teams.faction1) }}
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Players</div>
                                <div class="font-semibold dark:text-white">
                                    {{ match.teams.faction1.roster.length }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-lg dark:text-white flex items-center">
                                <span class="truncate max-w-[180px]">{{ match.teams.faction2.name || 'Team 2' }}</span>
                                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-blue-500/10 text-blue-500">
                  {{ match.teams.faction2.stats.rating }} rating
                </span>
                            </h3>
                            <div class="flex items-center space-x-2">
                <span class="text-xs px-2 py-0.5 rounded bg-purple-500/10 text-purple-500">
                  Avg: {{ match.teams.faction2.stats.skillLevel.average }}
                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Level range</div>
                                <div class="font-semibold dark:text-white">
                                    {{ match.teams.faction2.stats.skillLevel.range.min }}
                                    -
                                    {{ match.teams.faction2.stats.skillLevel.range.max }}
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Premium</div>
                                <div class="font-semibold dark:text-white">
                                    {{ premiumCount(match.teams.faction2) }}
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Players</div>
                                <div class="font-semibold dark:text-white">
                                    {{ match.teams.faction2.roster.length }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Rosters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="font-semibold text-lg dark:text-white flex items-center">
                                <span class="truncate">{{ match.teams.faction1.name || 'Team 1' }}</span>
                                <span v-if="match.results?.winner === 'faction1'" class="ml-2 text-xs px-2 py-0.5 rounded bg-green-500/10 text-green-500">
                  WINNER
                </span>
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div
                                v-for="player in match.teams.faction1.roster"
                                :key="player.player_id"
                                class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer flex items-center"
                                @click="emit('select', player)"
                            >
                                <div class="flex-shrink-0 mr-3">
                                    <img
                                        :src="player.avatar || defaultAvatar"
                                        class="w-10 h-10 rounded-full object-cover border-2"
                                        :class="player.membership === 'premium'
                      ? 'border-yellow-400'
                      : 'border-gray-300 dark:border-gray-600'"
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ player.nickname }}
                                            <span v-if="player.nickname !== player.game_player_name" class="text-xs text-gray-500 ml-1">
                        ({{ player.game_player_name }})
                      </span>
                                        </p>
                                        <span v-if="player.membership === 'premium'" class="ml-2 text-yellow-500">★</span>
                                    </div>
                                    <div class="flex items-center mt-1 space-x-3 text-xs">
                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500">
                      Lvl {{ player.game_skill_level }}
                    </span>
                                        <span class="text-gray-500 dark:text-gray-400 truncate">
                      {{ player.player_id }}
                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="font-semibold text-lg dark:text-white flex items-center">
                                <span class="truncate">{{ match.teams.faction2.name || 'Team 2' }}</span>
                                <span v-if="match.results?.winner === 'faction2'" class="ml-2 text-xs px-2 py-0.5 rounded bg-green-500/10 text-green-500">
                  WINNER
                </span>
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div
                                v-for="player in match.teams.faction2.roster"
                                :key="player.player_id"
                                class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer flex items-center"
                                @click="emit('select', player)"
                            >
                                <div class="flex-shrink-0 mr-3">
                                    <img
                                        :src="player.avatar || defaultAvatar"
                                        class="w-10 h-10 rounded-full object-cover border-2"
                                        :class="player.membership === 'premium'
                      ? 'border-yellow-400'
                      : 'border-gray-300 dark:border-gray-600'"
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ player.nickname }}
                                            <span v-if="player.nickname !== player.game_player_name" class="text-xs text-gray-500 ml-1">
                        ({{ player.game_player_name }})
                      </span>
                                        </p>
                                        <span v-if="player.membership === 'premium'" class="ml-2 text-yellow-500">★</span>
                                    </div>
                                    <div class="flex items-center mt-1 space-x-3 text-xs">
                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500">
                      Lvl {{ player.game_skill_level }}
                    </span>
                                        <span class="text-gray-500 dark:text-gray-400 truncate">
                      {{ player.player_id }}
                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Match Information -->
                <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <h3 class="font-semibold text-lg mb-3 dark:text-white">Match Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                            <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Match ID</div>
                            <div class="font-semibold dark:text-white truncate">
                                {{ match.match_id }}
                            </div>
                        </div>
                        <div v-if="hasLocation" class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                            <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Server Location</div>
                            <div class="font-semibold dark:text-white">
                                {{ selectedLocation }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                            <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Format</div>
                            <div class="font-semibold dark:text-white">
                                BO{{ match.best_of }}
                            </div>
                        </div>
                        <div v-if="match.demo_url && match.demo_url.length"
                             @click="openDemo"
                             class="bg-white dark:bg-gray-700 p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="text-gray-500 dark:text-gray-300 text-sm mb-1">Demo</div>
                            <div class="font-semibold text-blue-500">
                                Available (Download)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    <img src="https://corporate.faceit.com/wp-content/themes/faceit/dist/images/logo.svg" class="h-4 mr-2" />
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-500/10 text-blue-500 ml-2">
            CS2
          </span>
                    <span v-if="match?.region" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
            {{ match.region }}
          </span>
                </div>
                <div class="flex space-x-3">
                    <button
                        v-if="match"
                        @click="openFaceit"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition-colors flex items-center text-sm"
                    >
                        View on FACEIT
                    </button>
                    <button
                        @click="emit('close')"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition-colors text-sm"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    matchId: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['close', 'select'])

const match = ref(null)
const isLoading = ref(true)
const defaultAvatar = 'https://cdn.faceit.com/web/960/src/app/assets/images/avatar_default.jpg'

onMounted(async () => {
    try {
        const response = await axios.get(`/api/matches/${props.matchId}`)
        match.value = response.data
    } catch (e) {
        console.error('Error loading match:', e)
    } finally {
        isLoading.value = false
    }
})

// Методы для открытия ссылок
function openDemo() {
    const url = match.value?.demo_url?.[0]
    if (url) window.open(url, '_blank')
}

function openFaceit() {
    if (match.value?.match_id) {
        window.open(`https://www.faceit.com/en/cs2/room/${match.value.match_id}`, '_blank')
    }
}

// Computed properties
const hasVoting = computed(() => match.value?.voting)
const hasLocation = computed(() => hasVoting.value?.location?.pick?.length > 0)
const selectedLocation = computed(() => hasLocation.value ? match.value.voting.location.pick[0] : 'Unknown')

const hasMap = computed(() => hasVoting.value?.map?.pick?.length > 0)
const selectedMap = computed(() => hasMap.value ? match.value.voting.map.pick[0] : null)
const selectedMapImage = computed(() => {
    if (!hasMap.value) return 'https://via.placeholder.com/800x400?text=Map+Image'
    const mapEntity = match.value.voting.map.entities.find(e => e.guid === selectedMap.value)
    return mapEntity?.image_lg || 'https://via.placeholder.com/800x400?text=Map+Image'
})
const selectedMapName = computed(() => {
    if (!hasMap.value) return 'Unknown Map'
    return getMapName(selectedMap.value)
})

const hasTeamStats = computed(() => match.value?.teams?.faction1?.stats && match.value?.teams?.faction2?.stats)

const matchStatus = computed(() => {
    if (!match.value?.status) return 'Status unknown'
    switch (match.value.status) {
        case 'FINISHED':
            return 'Match finished'
        case 'STARTED':
            return 'Match in progress'
        case 'CANCELLED':
            return 'Match cancelled'
        default:
            return match.value.status
    }
})

const matchDuration = computed(() => {
    if (!match.value?.started_at || !match.value?.finished_at) return '—'
    const seconds = match.value.finished_at - match.value.started_at
    const minutes = Math.floor(seconds / 60)
    const remaining = seconds % 60
    return `${minutes}m ${remaining}s`
})

// Хелперы
function formatDate(timestamp) {
    if (!timestamp) return '—'
    return new Date(timestamp * 1000).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

function scoreClass(faction) {
    const winner = match.value?.results?.winner
    if (!winner) return 'text-gray-800 dark:text-gray-200'
    return faction === winner
        ? 'text-green-600 dark:text-green-400 font-extrabold'
        : 'text-red-600 dark:text-red-400'
}

function getMapName(mapId) {
    const mapNames = {
        de_dust2: 'Dust II',
        de_mirage: 'Mirage',
        de_nuke: 'Nuke',
        de_ancient: 'Ancient',
        de_train: 'Train',
        de_anubis: 'Anubis',
        de_inferno: 'Inferno',
        de_vertigo: 'Vertigo'
    }
    return mapNames[mapId] || mapId
}

function getFlagImage(location) {
    return `https://distribution.faceit-cdn.net/images/flags/v1/${location.toLowerCase()}.jpg?width=110&height=55`
}

function premiumCount(team) {
    return team.roster.filter(p => p.membership === 'premium').length
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
