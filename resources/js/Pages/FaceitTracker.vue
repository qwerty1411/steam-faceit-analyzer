<template>
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="logo" @click="resetAndGoHome">
                    <div class="logo-inner"></div>
                </div>
                <h1 class="text-2xl font-bold text-blue-600 cursor-pointer" @click="resetAndGoHome">Faceit TJ</h1>
            </div>
            <nav class="space-x-4 text-gray-600 dark:text-gray-300 flex items-center">
                <a href="#" @click.prevent="currentPage = 'home'" class="hover:text-blue-600 dark:hover:text-blue-400">Главная</a>
                <a href="#" @click.prevent="currentPage = 'about'" class="hover:text-blue-600 dark:hover:text-blue-400">О сервисе</a>
                <a href="#contacts" class="hover:text-blue-600 dark:hover:text-blue-400">Контакты</a>

                <!-- Переключатель темы -->
                <button
                    @click="toggleTheme"
                    class="ml-6 p-2 rounded-full border border-gray-300 dark:border-gray-600 focus:outline-none"
                    :aria-label="isDark ? 'Светлая тема' : 'Тёмная тема'"
                    title="Переключить тему"
                >
                    <template v-if="isDark">
                        <!-- Солнце -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m8.66-11h-1M4.34 12h-1m15.66 4.66l-.7-.7M6.34 6.34l-.7-.7m12.02 12.02l-.7-.7M6.34 17.66l-.7-.7M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                        </svg>
                    </template>
                    <template v-else>
                        <!-- Луна -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                        </svg>
                    </template>
                </button>
            </nav>
        </header>

        <!-- Main -->
        <main class="flex-grow container mx-auto p-6">
            <!-- Главная страница -->
            <template v-if="currentPage === 'home'">
                <!-- Ввод ника -->
                <div class="flex justify-center mb-10">
                    <div class="w-full max-w-3xl">
                        <label for="nickname"
                               class="block mb-2 font-semibold text-gray-700 dark:text-gray-200 text-center text-lg">
                            Введите никнейм Faceit
                        </label>
                        <div class="flex">
                            <input
                                v-model="nickname"
                                @keyup.enter="fetch"
                                id="nickname"
                                type="text"
                                placeholder="например, s1mple"
                                class="flex-grow px-4 py-2 rounded-l-md border border-gray-300 focus:ring focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <button
                                @click="fetch"
                                class="bg-blue-600 text-white px-6 rounded-r-md hover:bg-blue-700 transition"
                                :disabled="loading"
                            >
                                Поиск
                            </button>
                        </div>
                        <p v-if="error" class="text-red-500 mt-2 text-center">{{ error }}</p>

                        <!-- Блок с подсказками поиска -->
                        <div v-if="searchSuggestions.length > 0 && !profile && !loading" class="mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                            <div v-for="player in searchSuggestions" :key="player.player_id"
                                 @click="selectSuggestion(player.nickname)"
                                 class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer flex items-center">
                                <img :src="player.avatar" class="w-8 h-8 rounded-full mr-3" alt="avatar">
                                <div>
                                    <p class="font-medium dark:text-white">
                                        {{ player.nickname }} |
                                        <img
                                            :src="`https://flagcdn.com/24x18/${player.country.toLowerCase()}.png`"
                                            alt="flag"
                                            class="w-6 h-3 rounded-sm inline-block"
                                        />
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <span>Уровень: {{ player.games[1]?.skill_level || 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Лоудер -->
                <div v-if="loading" class="flex justify-center py-10">
                    <svg
                        class="animate-spin h-10 w-10 text-blue-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>
                    </svg>
                </div>

                <!-- Про игроки (показываются только до поиска) -->
                <div v-if="!profile && !loading" class="mb-10">
                    <h2 class="text-xl font-semibold text-center text-gray-800 dark:text-white mb-6">🔥 Популярные
                        игроки</h2>
                    <div class="flex justify-center gap-6 flex-wrap">
                        <div
                            v-for="player in proPlayers"
                            :key="player.nickname"
                            @click="selectPro(player.nickname)"
                            class="cursor-pointer w-32 flex flex-col items-center p-3 rounded-lg shadow-md hover:shadow-lg bg-white dark:bg-gray-800 transition"
                        >
                            <img :src="player.avatar" alt="avatar"
                                 class="w-16 h-16 rounded-full mb-2 border-2 border-blue-500"/>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ player.nickname }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-300">ELO: {{ player.elo }}</p>
                        </div>
                    </div>
                    <section
                        v-if="!profile && !loading"
                        class="mt-12 mx-auto w-full max-w-5xl bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 transition-colors"
                    >
                        <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 text-gray-900 dark:text-gray-100">
                            <!-- Текстовая часть -->
                            <div class="mb-6 md:mb-0 md:w-1/2">
                                <h2 class="text-2xl font-bold mb-3 text-indigo-700 dark:text-indigo-400">
                                    💼 Открой свой первый кейс бесплатно!
                                </h2>
                                <p class="mb-4 text-sm md:text-base text-gray-700 dark:text-gray-300">
                                    Испытай удачу и получи шанс выбить топовый скин. Лучшие кейсы с бонусами и реферальной системой!
                                </p>
                                <a
                                    href="https://yourcasesite.com"
                                    target="_blank"
                                    class="inline-block bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition"
                                >
                                    Перейти на сайт →
                                </a>
                            </div>

                            <!-- Изображение -->
                            <div class="md:w-1/2 flex justify-center">
                                <img
                                    src="https://pub-5f12f7508ff04ae5925853dee0438460.r2.dev/data/csgo/resource/flash/econ/weapon_cases/crate_community_33_png.png"
                                    alt="case image"
                                    class="w-32 md:w-36 drop-shadow-lg animate-bounce-slow"
                                />
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Профиль -->
                <section v-if="profile && !loading"
                         class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300">
                    <div
                        class="relative flex space-x-6 items-center mb-6 p-6 rounded-lg bg-center bg-cover bg-no-repeat"
                        :style="{ backgroundImage: `url(${profile.profile.cover_image || ''})` }"
                    >
                        <!-- Затемнение -->
                        <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg"></div>

                        <!-- Контент поверх фона -->
                        <img
                            :src="profile.profile.avatar"
                            alt="avatar"
                            class="w-28 h-28 rounded-full border-4 border-white z-10"
                        />

                        <div class="z-10 text-white">
                            <h2 class="text-3xl font-bold">{{ profile.profile.nickname }}</h2>
                            <div class="flex items-center space-x-2 mt-1">
                                <img
                                    :src="`https://flagcdn.com/24x18/${profile.profile.country.toLowerCase()}.png`"
                                    alt="flag"
                                    class="w-6 h-4 rounded-sm"
                                />
                                <span class="uppercase font-semibold">{{ profile.profile.country }}</span>
                            </div>
                            <a
                                :href="profile.profile.faceit_url"
                                target="_blank"
                                class="inline-block mt-2 text-blue-300 hover:underline"
                            >
                                Открыть профиль на Faceit
                            </a>
                            <p class="mt-3">ELO: <strong>{{ profile.profile.games['cs2']?.faceit_elo || 'N/A' }}</strong>
                            </p>
                            <p>Уровень: <strong>{{ profile.profile.games['cs2']?.skill_level || 'N/A' }}</strong></p>
                        </div>
                    </div>


                    <!-- Карточки со статистикой -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-10">
                        <StatCard title="Матчи" :value="profile.stats.lifetime['Matches']"/>
                        <StatCard title="Победы" :value="profile.stats.lifetime['Wins']"/>
                        <StatCard title="Win Rate" :value="profile.stats.lifetime['Win Rate %'] + '%'"/>
                        <StatCard title="K/D Ratio"
                                  :value="Number(profile.stats.lifetime['K/D Ratio'] / profile.stats.lifetime['Matches']).toFixed(2)"/>
                        <StatCard title="HS %" :value="profile.stats.lifetime['Average Headshots %'] + '%'"/>
                        <StatCard title="Longest Win Streak" :value="profile.stats.lifetime['Longest Win Streak']"/>
                        <StatCard title="Recent Results">
                            <div class="flex space-x-1 justify-center">
              <span
                  v-for="(r, i) in profile.stats.lifetime['Recent Results']"
                  :key="i"
                  :class="[
                  'w-6 h-6 flex items-center justify-center rounded font-bold text-white text-xs',
                  r === '1' ? 'bg-green-500' : 'bg-red-500'
                ]"
              >
                {{ r === '1' ? 'W' : 'L' }}
              </span>
                            </div>
                        </StatCard>
                        <StatCard title="Current Win Streak" :value="profile.stats.lifetime['Current Win Streak']"/>
                    </div>

                    <!-- Таблица матчей -->
                    <div class="mt-10">
                        <h2 class="text-xl font-semibold mb-4 dark:text-white">Последние матчи</h2>
                        <MatchTable :matches="profile.matches"/>
                    </div>
                </section>
            </template>

            <!-- About страница -->
            <section v-if="currentPage === 'about'" class="prose max-w-none dark:text-white">
                <h1>О сервисе Faceit TJ</h1>
                <p>
                    Добро пожаловать на Faceit TJ — сервис, который помогает искать и анализировать статистику игроков Faceit по CS2.
                    Здесь вы можете быстро узнать уровень игрока, его Эло, выигранные матчи и многое другое.
                </p>
                <p>
                    Мы создали этот сервис, чтобы сделать вашу игру интереснее и помочь найти сильных соперников или партнеров для игры.
                </p>
                <h2>Функции сервиса</h2>
                <ul>
                    <li>Поиск по нику игрока Faceit</li>
                    <li>Просмотр подробной статистики и последних матчей</li>
                    <li>Тёмная и светлая тема для комфортного использования</li>
                    <li>Топовые игроки и их профили для быстрого доступа</li>
                    <li>Подсказки при поиске игроков</li>
                </ul>
                <h2>Контакты</h2>
                <p>Если у вас есть вопросы или предложения, пишите на <a href="mailto:huseinnurkhonov16@gmail.com" class="text-blue-600 hover:underline">huseinnurkhonov16@gmail.com</a>.</p>
            </section>
        </main>

        <footer
            class="bg-gray-100 dark:bg-gray-800 text-center text-gray-600 dark:text-gray-400 py-6 mt-auto transition-colors duration-300">
            <p>© 2025 Faceit TJ. Все права защищены.</p>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

import StatCard from '@/Components/StatCard.vue'
import MatchTable from '@/Components/MatchTable.vue'

const nickname = ref('')
const profile = ref(null)
const error = ref(null)
const isDark = ref(false)
const loading = ref(false)
const currentPage = ref('home')
const searchSuggestions = ref([])
const searchTimeout = ref(null)

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark'
    applyTheme()
})

watch(isDark, (value) => {
    localStorage.setItem('theme', value ? 'dark' : 'light')
    applyTheme()
})

// Вотчер для подсказок поиска
watch(nickname, (newVal) => {
    if (newVal.trim().length >= 2 && !profile.value) {
        clearTimeout(searchTimeout.value)
        searchTimeout.value = setTimeout(() => {
            fetchSuggestions(newVal.trim())
        }, 300)
    } else {
        searchSuggestions.value = []
    }
})

async function fetchSuggestions(query) {
    if (!query || query.length < 2) return

    try {
        const response = await axios.get('/api/faceit/search', {
            params: { nickname: query, limit: 5 }
        })
        searchSuggestions.value = response.data || []
    } catch (e) {
        console.error('Error fetching suggestions:', e)
        searchSuggestions.value = []
    }
}

function selectSuggestion(nick) {
    nickname.value = nick
    searchSuggestions.value = []
    fetch()
}

function toggleTheme() {
    isDark.value = !isDark.value
}

function applyTheme() {
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

async function fetch() {
    error.value = null
    profile.value = null
    loading.value = true
    searchSuggestions.value = []

    if (!nickname.value.trim()) {
        error.value = 'Пожалуйста, введите никнейм.'
        loading.value = false
        return
    }
    try {
        const res = await axios.get('/api/faceit', { params: { nickname: nickname.value.trim() } })
        if (!res.data || res.data.error) {
            error.value = res.data?.error || 'Игрок не найден'
            loading.value = false
            return
        }
        profile.value = res.data
    } catch (e) {
        error.value = 'Ошибка при загрузке данных. Попробуйте позже.'
    } finally {
        loading.value = false
    }
}

function resetAndGoHome() {
    nickname.value = ''
    profile.value = null
    error.value = null
    loading.value = false
    currentPage.value = 'home'
    searchSuggestions.value = []
}

const proPlayers = [
    {
        nickname: 's1mple',
        elo: 3720,
        avatar: 'https://img-cdn.hltv.org/playerbodyshot/O2mYq7Td1la8B8xGC-dYr8.png?ixlib=java-2.1.0&w=400&s=a1921e5df7d31e47aadc050ab4c0ff55',
    },
    {
        nickname: 'ZywOo',
        elo: 4032,
        avatar: 'https://img-cdn.hltv.org/playerbodyshot/Xkqvuwl9o12Mi20Vd0lzHl.png?ixlib=java-2.1.0&w=400&s=f64d118affc3f2fbadcebf861d70400d',
    },
    {
        nickname: 'niko',
        elo: 4136,
        avatar: 'https://img-cdn.hltv.org/playerbodyshot/Ixkh35BQq0Gtp3UiQgmx-A.png?ixlib=java-2.1.0&w=400&s=43b014d57d1cfd4a7bda2d77fdcbd37c',
    },
    {
        nickname: 'm0NESY',
        elo: 4801,
        avatar: 'https://img-cdn.hltv.org/playerbodyshot/KZANjwU4854qZUafHkghTd.png?ixlib=java-2.1.0&w=400&s=5394f4983ff221b67b21710daba1d3d4',
    },
    {
        nickname: 'b1t',
        elo: 3764,
        avatar: 'https://img-cdn.hltv.org/playerbodyshot/XZQDtRCDiUl-lXsByQS_ij.png?ixlib=java-2.1.0&w=400&s=1a34e233e07e2432103fad93940b2a3d',
    },
]

function selectPro(nick) {
    nickname.value = nick
    fetch()
}
</script>

<style>
/* Плавные переходы темы */
html {
    transition: background-color 0.3s ease, color 0.3s ease;
}

@keyframes bounce-slow {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-15px);
    }
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite;
}

/* Анимация спиннера */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Логотип CSS-only */
.logo {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.logo:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.logo-inner {
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 4px;
    position: relative;
    transform: rotate(45deg);
}

.logo-inner::before,
.logo-inner::after {
    content: '';
    position: absolute;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.logo-inner::before {
    width: 8px;
    height: 8px;
    top: -4px;
    left: -4px;
    border-radius: 50%;
}

.logo-inner::after {
    width: 12px;
    height: 12px;
    bottom: -6px;
    right: -6px;
    border-radius: 50%;
}

.dark .logo {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
}

.dark .logo-inner {
    background: #1f2937;
}

.dark .logo-inner::before,
.dark .logo-inner::after {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
}

/* Анимация логотипа при наведении */
.logo:hover .logo-inner {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        transform: rotate(45deg) scale(1);
    }
    50% {
        transform: rotate(45deg) scale(1.1);
    }
    100% {
        transform: rotate(45deg) scale(1);
    }
}
</style>
