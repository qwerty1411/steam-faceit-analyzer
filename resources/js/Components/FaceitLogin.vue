<template>
    <div>
        <!-- Кнопка входа через Faceit -->
        <button
            v-if="!user"
            @click="startLogin"
            class="
        flex items-center justify-center
        bg-gradient-to-r to-blue-400 from-orange-600
        hover:from-orange-500 hover:to-blue-700
        text-white font-semibold
        py-1 px-4
        rounded-lg
        shadow-md hover:shadow-lg
        transform hover:-translate-y-0.5
        transition
      "
        >
            Войти
            <span
                class="

          flex items-center justify-center
        "
            >
        <img
            src="/assets/faceit-logo.png"
            alt="Faceit"
            class="w-8 h-8"
        />
      </span>
        </button>

        <!-- Профиль пользователя -->
        <div v-else class="flex items-center space-x-3">
            <img
                :src="user.avatar"
                alt="avatar"
                class="w-4 h-4 shadow-sm"
            />
            <span class="text-white font-medium truncate max-w-[120px]">
        {{ user.nickname }}
      </span>
            <button
                @click="logout"
                class="
          text-sm text-red-400 hover:text-red-500
          ml-2
          transition-colors
        "
            >
                Выйти
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(null)

function startLogin() {
    window.open('/auth/faceit', 'faceitLogin', 'width=600,height=700')
}

function logout() {
    localStorage.removeItem('faceit_user')
    user.value = null
    location.reload()
}

onMounted(() => {
    window.addEventListener('message', (event) => {
        if (event.origin !== window.location.origin) return
        if (event.data?.type === 'faceit-auth-success') {
            localStorage.setItem('faceit_user', JSON.stringify(event.data.user))
            user.value = event.data.user
        }
    })

    const stored = localStorage.getItem('faceit_user')
    if (stored) {
        user.value = JSON.parse(stored)
    }
})
</script>
