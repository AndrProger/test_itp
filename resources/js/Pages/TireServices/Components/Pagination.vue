<template>
  <div class="flex items-center justify-between">
    <!-- Results info -->
    <div class="text-sm text-gray-700">
      Показано 
      <span class="font-medium">{{ pagination.from }}</span>
      -
      <span class="font-medium">{{ pagination.to }}</span>
      из
      <span class="font-medium">{{ pagination.total }}</span>
      результатов
    </div>

    <!-- Pagination controls -->
    <div class="flex items-center space-x-2">
      <!-- Previous button -->
      <button
        @click="goToPage(pagination.current_page - 1)"
        :disabled="pagination.current_page <= 1"
        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="sr-only">Предыдущая</span>
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Page numbers -->
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="page !== '...'"
          @click="goToPage(page)"
          :class="[
            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
            page === pagination.current_page
              ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
        <span
          v-else
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
        >
          ...
        </span>
      </template>

      <!-- Next button -->
      <button
        @click="goToPage(pagination.current_page + 1)"
        :disabled="pagination.current_page >= pagination.last_page"
        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="sr-only">Следующая</span>
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['page-changed'])

// Calculate visible page numbers
const visiblePages = computed(() => {
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const delta = 2 // Number of pages to show on each side of current page
  const pages = []

  // Always show first page
  if (current > delta + 2) {
    pages.push(1)
    if (current > delta + 3) {
      pages.push('...')
    }
  }

  // Show pages around current page
  const start = Math.max(1, current - delta)
  const end = Math.min(last, current + delta)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  // Always show last page
  if (current < last - delta - 1) {
    if (current < last - delta - 2) {
      pages.push('...')
    }
    pages.push(last)
  }

  return pages
})

// Navigate to specific page
const goToPage = (page) => {
  if (page >= 1 && page <= props.pagination.last_page && page !== props.pagination.current_page) {
    emit('page-changed', page)
  }
}
</script> 