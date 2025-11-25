<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'dd/mm/aaaa' },
})
const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const inputText = ref('')
const today = new Date()
const viewYear = ref(today.getFullYear())
const viewMonth = ref(today.getMonth())

function pad(n) { return String(n).padStart(2, '0') }
function toDisplayFromISO(iso) {
  if (!iso) return ''
  const parts = iso.split('-')
  if (parts.length !== 3) return ''
  return `${pad(Number(parts[2]))}/${pad(Number(parts[1]))}/${parts[0]}`
}
function toISOFromDisplay(txt) {
  const m = txt.match(/^\s*(\d{2})\/(\d{2})\/(\d{4})\s*$/)
  if (!m) return ''
  const d = Number(m[1]), mth = Number(m[2]), y = Number(m[3])
  if (!y || mth < 1 || mth > 12 || d < 1 || d > 31) return ''
  return `${y}-${pad(mth)}-${pad(d)}`
}
function setFromISO(iso) {
  inputText.value = toDisplayFromISO(iso)
}
watch(() => props.modelValue, (v) => setFromISO(v), { immediate: true })

function toggle() { open.value = !open.value }
function selectDay(day) {
  const iso = `${viewYear.value}-${pad(viewMonth.value + 1)}-${pad(day)}`
  emit('update:modelValue', iso)
  setFromISO(iso)
  open.value = false
}
function prevMonth() {
  const d = new Date(viewYear.value, viewMonth.value, 1)
  d.setMonth(d.getMonth() - 1)
  viewYear.value = d.getFullYear()
  viewMonth.value = d.getMonth()
}
function nextMonth() {
  const d = new Date(viewYear.value, viewMonth.value, 1)
  d.setMonth(d.getMonth() + 1)
  viewYear.value = d.getFullYear()
  viewMonth.value = d.getMonth()
}
function commitTyped() {
  const iso = toISOFromDisplay(inputText.value)
  if (iso) emit('update:modelValue', iso)
}

function daysMatrix(year, month) {
  const first = new Date(year, month, 1)
  const startIdx = (first.getDay() + 6) % 7
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const rows = []
  let day = 1
  for (let r = 0; r < 6; r++) {
    const row = []
    for (let c = 0; c < 7; c++) {
      if (r === 0 && c < startIdx) row.push(null)
      else if (day > daysInMonth) row.push(null)
      else { row.push(day); day++ }
    }
    rows.push(row)
  }
  return rows
}
const week = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom']
</script>

<template>
  <div style="position:relative;">
    <div style="display:flex; gap:.5rem; align-items:center;">
      <input class="input" :placeholder="props.placeholder" v-model="inputText" @blur="commitTyped" @focus="open=true" />
      <button type="button" class="btn btn-outline" @click="toggle">Calendário</button>
    </div>
    <div v-if="open" style="position:absolute; z-index:30; background:#ffffff; border:1px solid #e5e7eb; border-radius:.5rem; box-shadow:0 4px 12px rgba(0,0,0,0.08); margin-top:.25rem; padding:.5rem; width: 320px; box-sizing:border-box; overflow:hidden;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem;">
        <button type="button" class="btn btn-outline" @click="prevMonth">‹</button>
        <div style="font-weight:600;">{{ (viewMonth+1).toString().padStart(2,'0') }}/{{ viewYear }}</div>
        <button type="button" class="btn btn-outline" @click="nextMonth">›</button>
      </div>
      <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:.25rem;">
        <div v-for="w in week" :key="w" style="text-align:center; font-weight:600; color:#6b7280; font-size:12px;">{{ w }}</div>
      </div>
      <div>
        <div v-for="(row, ri) in daysMatrix(viewYear, viewMonth)" :key="ri" style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:4px;">
          <template v-for="(d, di) in row" :key="di">
            <button v-if="d" type="button" class="btn btn-secondary" @click="selectDay(d)" style="width:100%; padding:.35rem 0;">{{ d }}</button>
            <div v-else></div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
