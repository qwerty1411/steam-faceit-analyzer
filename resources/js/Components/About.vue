<template>
    <section class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-500 py-20 px-6 md:px-24">
        <div class="max-w-6xl mx-auto">
            <!-- Заголовок -->
            <div class="mb-14 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-indigo-600 dark:text-indigo-400">
                    О Faceit TJ
                </h1>
                <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 max-w-3xl mx-auto">
                    Наш сервис помогает игрокам отслеживать свою статистику, находить интересные матчи и улучшать свой скилл в Faceit.
                </p>
            </div>

            <!-- Информационные блоки -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-20">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center transition">
                    <div class="mb-4 mx-auto w-14 h-14 flex items-center justify-center bg-indigo-100 dark:bg-indigo-600 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Точная статистика</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Автоматический сбор данных и вывод детальной аналитики по вашим матчам.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center transition">
                    <div class="mb-4 mx-auto w-14 h-14 flex items-center justify-center bg-indigo-100 dark:bg-indigo-600 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h11M9 21V3m0 0l7 7-7 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Быстрый поиск</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Молниеносный поиск игроков и матчей — без задержек и тормозов.
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center transition">
                    <div class="mb-4 mx-auto w-14 h-14 flex items-center justify-center bg-indigo-100 dark:bg-indigo-600 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Актуальность</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Все данные обновляются регулярно. Только свежая статистика.
                    </p>
                </div>
            </div>

            <!-- Иллюстрация -->
            <div class="rounded-xl overflow-hidden shadow-md">
                <img
                    src="https://images.unsplash.com/photo-1600691552220-2f4f97cba84c?auto=format&fit=crop&w=1500&q=80"
                    alt="Gaming illustration"
                    class="w-full object-cover h-64 md:h-96"
                    loading="lazy"
                />
            </div>
        </div>
    </section>
</template>

<script setup>
// нет логики
</script>

<style scoped>
/* Небольшая анимация при наведении на блок */
div:hover > .bg-gray-50,
div:hover > .dark\:bg-gray-800 {
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}
</style>
