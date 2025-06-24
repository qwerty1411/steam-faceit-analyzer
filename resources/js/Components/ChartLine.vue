<template>
    <canvas ref="chart"></canvas>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({ data: Array })
const chart = ref(null)
let chartInstance

onMounted(() => {
    chartInstance = new Chart(chart.value, {
        type: 'line',
        data: {
            labels: props.data.map(d => d.date),
            datasets: [{ label: 'ELO', data: props.data.map(d => d.elo), fill: false, borderColor: '#3B82F6' }],
        },
    })
})
watch(() => props.data, (nv) => {
    if (chartInstance) {
        chartInstance.data.labels = nv.map(d => d.date)
        chartInstance.data.datasets[0].data = nv.map(d => d.elo)
        chartInstance.update()
    }
})
</script>
