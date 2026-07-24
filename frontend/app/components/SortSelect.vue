<script setup lang="ts">
defineProps<{ options: { label: string, sort: string, order: string }[] }>()

const sort = defineModel<string>('sort', { default: '' })
const order = defineModel<string>('order', { default: '' })

function onChange(event: Event) {
  const value = (event.target as HTMLSelectElement).value
  if (!value) {
    sort.value = ''
    order.value = ''
    return
  }
  const [newSort, newOrder] = value.split(':')
  sort.value = newSort ?? ''
  order.value = newOrder ?? ''
}
</script>

<template>
  <select
    data-testid="sort-select"
    :value="sort ? `${sort}:${order}` : ''"
    class="rounded-md border border-hearthlight/40 bg-white px-3 py-2 text-inkwell shadow-sm focus:border-hearthlight focus:outline-none focus:ring-1 focus:ring-hearthlight dark:bg-inkwell/60 dark:text-aged-edge"
    @change="onChange"
  >
    <option value="">Sort…</option>
    <option v-for="opt in options" :key="`${opt.sort}:${opt.order}`" :value="`${opt.sort}:${opt.order}`">
      {{ opt.label }}
    </option>
  </select>
</template>
