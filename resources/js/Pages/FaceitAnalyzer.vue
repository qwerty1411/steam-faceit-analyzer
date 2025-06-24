<template>
    <div class="max-w-5xl mx-auto p-6 space-y-8">
        <!-- Input для ввода никнейма -->
        <div class="mb-6">
            <label for="nickname" class="block text-lg font-medium text-gray-700 mb-1">Введите никнейм Faceit</label>
            <div class="flex">
                <input v-model="nickname" @keyup.enter="fetch" type="text" id="nickname" class="flex-1 px-4 py-2 border rounded-l-md shadow-sm focus:ring focus:outline-none" placeholder="Например, s1mple" />
                <button @click="fetch" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">Загрузить</button>
            </div>
        </div>

        <div v-if="profile">
            <!-- Header: аватар, ник, страна, ссылки, ELO -->
            <div class="flex items-center space-x-6">
                <img :src="profile.profile.avatar" class="w-24 h-24 rounded-full shadow" />
                <div>
                    <h1 class="text-3xl font-bold">{{ profile.profile.nickname }}</h1>
                    <span class="inline-block bg-gray-200 text-sm px-2 py-1 rounded">{{ profile.profile.country }}</span>
                    <a :href="profile.profile.faceit_url" target="_blank" class="ml-2 text-blue-600">FaceIt</a>
                    <div class="mt-2 text-lg">
                        Уровень: {{ profile.profile.games['csgo'].faceit_elo }} &nbsp;
                        ELO: <strong>{{ profile.profile.games['cs2'].faceit_elo }}</strong>
                    </div>
                </div>
            </div>

            <!-- Статистика: матчи, победы, K/D, HS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <StatCard title="Матчи" :value="profile.stats.lifetime['Matches']" />
                <StatCard title="Победы" :value="profile.stats.lifetime['Wins']" />
                <StatCard title="K/D" :value="Number(profile.stats.lifetime['K/D Ratio']).toFixed(2)" />
                <StatCard title="HS %" :value="profile.stats.lifetime['Headshots %'] + '%'" />
            </div>

            <!-- График ELO -->
            <div>
                <h2 class="text-xl font-semibold mb-2">ELO изменения</h2>
                <ChartLine :data="eloHistory" />
            </div>

            <!-- Таблица последних матчей -->
            <div>
                <h2 class="text-xl font-semibold mb-2">Последние матчи</h2>
                <MatchTable :matches="profile.matches" />
            </div>
        </div>

        <div v-else class="text-center text-gray-500 text-lg">Введите никнейм и нажмите "Загрузить"</div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import StatCard from '@/Components/StatCard.vue'
import ChartLine from '@/Components/ChartLine.vue'
import MatchTable from '@/Components/MatchTable.vue'

const nickname = ref('')
const profile = ref(null)
const eloHistory = ref([])

async function fetch() {
    if (!nickname.value) return

    try {
        const res = await axios.get('/api/faceit', { params: { nickname: nickname.value } })
        profile.value = res.data

        // Построим историю elo на основе последних матчей
        const eloBase = Number(profile.value.stats.lifetime['Elo']) || 0
        let runningElo = eloBase

        eloHistory.value = profile.value.matches.slice().reverse().map((match) => {
            const eloChange = match.elo_change || 0
            runningElo -= eloChange
            return {
                date: new Date(match.started_at * 1000).toLocaleDateString(),
                elo: runningElo
            }
        })
    } catch (e) {
        console.error('Ошибка при загрузке профиля:', e)
        profile.value = null
        eloHistory.value = []
    }
}
</script>
