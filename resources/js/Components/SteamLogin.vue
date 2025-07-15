<template>
    <div>
        <!-- Кнопка входа через Steam -->
        <button
            v-if="!user"
            @click="startSteamLogin"
            class="flex items-center justify-center
                   bg-gradient-to-r from-gray-700 to-green-600
                   hover:from-gray-800 hover:to-green-700
                   text-white font-semibold py-1 px-4
                   rounded-lg shadow-md hover:shadow-lg
                   transform hover:-translate-y-0.5
                   transition">
            Войти через Steam
            <img
                src="/assets/steam-icon.png"
                alt="Steam"
                class="w-6 h-6 ml-2"
            />
        </button>

        <!-- Профиль пользователя -->
        <div v-else class="flex items-center space-x-3">
            <img
                :src="user.steam_avatar"
                alt="avatar"
                class="w-6 h-6 rounded-full shadow-sm"
            />
            <span class="text-white font-medium truncate max-w-[120px]">
                {{ user.name }}
            </span>
            <button
                @click="logout"
                class="text-sm text-red-400 hover:text-red-500 ml-2 transition-colors">
                Выйти
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(null)

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
    fetch('/logout', { method: 'POST' })
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

onMounted(() => {
    const stored = localStorage.getItem('steam_user')
    if (stored) {
        user.value = JSON.parse(stored)
    } else {
        fetchUser()
    }
})
</script>
