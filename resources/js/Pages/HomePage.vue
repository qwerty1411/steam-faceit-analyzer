<template>
    <div class="p-6">
        <div v-if="!user" class="text-center">
            <button
                @click="startSteamLogin"
                class="bg-gradient-to-r from-gray-700 to-green-600 hover:from-gray-800 hover:to-green-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition">
                Войти через Steam
            </button>
        </div>

        <div v-else>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <img :src="user.steam_avatar" alt="avatar" class="w-10 h-10 rounded-full"/>
                    <span class="text-white font-medium truncate max-w-[150px]">{{ user.name }}</span>
                    <button
                        @click="logout"
                        class="text-sm text-red-400 hover:text-red-500 ml-2 transition-colors">
                        Выйти
                    </button>
                </div>

                <router-link
                    to="/players"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                    Faceit Tracker →
                </router-link>
            </div>

            <div class="mb-6">
                <h1 class="text-xl font-bold mb-2 text-white">Поиск игры</h1>
                <p class="text-gray-400 mb-4">Соберите лобби из 5 игроков и начните поиск матча.</p>
                <button
                    @click="findGame"
                    :disabled="lobbyPlayers.length === 0"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
                    Найти игру
                </button>
            </div>

            <div class="bg-gray-800 p-4 rounded-lg shadow">
                <h2 class="text-white font-semibold mb-3">Текущее лобби:</h2>
                <ul class="space-y-2">
                    <li
                        v-for="(player, index) in lobbyPlayers"
                        :key="index"
                        class="text-white">
                        {{ index + 1 }}. {{ player.nickname }}
                    </li>
                </ul>
                <p v-if="lobbyPlayers.length === 0" class="text-gray-500">Лобби пусто.</p>

                <button
                    @click="invitePlayer"
                    :disabled="lobbyPlayers.length >= 5"
                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded">
                    Пригласить игрока
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import {useRouter} from 'vue-router'

const router = useRouter()
const user = ref(null)
const lobbyPlayers = ref([])

function startSteamLogin() {
    const win = window.open('/auth/steam', 'steamLogin', 'width=600,height=700')
    const timer = setInterval(() => {
        if (win.closed) {
            clearInterval(timer)
            fetchUser()
        }
    }, 1000)
}

function logout() {
    fetch('/logout', {method: 'POST'})
        .then(() => {
            localStorage.removeItem('steam_user')
            user.value = null
            location.reload()
        })
}

function fetchUser() {
    fetch('/api/user')
        .then(res => res.json())
        .then(data => {
            if (data?.id) {
                localStorage.setItem('steam_user', JSON.stringify(data))
                user.value = data
            }
        })
}

function findGame() {
    alert('Поиск игры начат!')
}

function invitePlayer() {
    const nickname = prompt('Введите никнейм игрока:')
    if (nickname && lobbyPlayers.value.length < 5) {
        lobbyPlayers.value.push({nickname})
    }
}

onMounted(() => {
    const stored = localStorage.getItem('steam_user')
    if (stored) {
        user.value = JSON.parse(stored)
    } else {
        fetchUser()
    }
})
</script>
