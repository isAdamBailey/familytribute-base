<script setup lang="ts">
import type { TaggingPerson } from '~/types/api'

const props = defineProps<{
  modelValue: number[]
  options: TaggingPerson[]
  testId?: string
}>()

const emit = defineEmits<{ 'update:modelValue': [number[]] }>()

const pending = ref<number | ''>('')

const available = computed(() =>
  props.options.filter(person => !props.modelValue.includes(person.id)),
)

const selected = computed(() =>
  props.modelValue
    .map(id => props.options.find(person => person.id === id))
    .filter((person): person is TaggingPerson => !!person),
)

function add() {
  if (pending.value === '') return
  emit('update:modelValue', [...props.modelValue, Number(pending.value)])
  pending.value = ''
}

function remove(id: number) {
  emit('update:modelValue', props.modelValue.filter(existing => existing !== id))
}
</script>

<template>
  <div :data-testid="testId">
    <div v-if="selected.length" class="mb-2 flex flex-wrap gap-2">
      <span
        v-for="person in selected"
        :key="person.id"
        class="inline-flex items-center gap-1 rounded-full bg-hearthlight-subtle px-3 py-1 text-sm text-hearthlight-deep dark:bg-old-binding/20 dark:text-hearthlight"
      >
        {{ person.full_name }}
        <button type="button" :aria-label="`Remove ${person.full_name}`" @click="remove(person.id)">
          <i class="ri-close-line" />
        </button>
      </span>
    </div>
    <select
      v-model="pending"
      class="block w-full rounded-md border border-hearthlight-subtle bg-white px-3 py-2 text-sm dark:border-old-binding/30 dark:bg-inkwell dark:text-aged-edge"
      @change="add"
    >
      <option value="">Select a person to tag…</option>
      <option v-for="person in available" :key="person.id" :value="person.id">
        {{ person.full_name }}
      </option>
    </select>
  </div>
</template>
