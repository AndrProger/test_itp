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
    <div class="flex justify-center items-center mt-8 space-x-2">
      <!-- Previous Button -->
      <button
        v-if="pagination.current_page > 1"
        @click="$emit('page-change', pagination.current_page - 1)"
        class="px-3 py-3 sm:px-4 sm:py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md shadow-sm hover:bg-gray-50 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out transform hover:scale-105 active:scale-95 cursor-pointer disabled:cursor-not-allowed"
        :disabled="pagination.current_page <= 1"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 hover:-rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>

      <!-- Page Numbers -->
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="page !== '...'"
          @click="$emit('page-change', page)"
          :class="[
            'px-3 py-3 sm:px-4 sm:py-3 text-sm font-medium border transition-all duration-300 ease-in-out transform cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500',
            page === pagination.current_page
              ? 'bg-blue-600 text-white border-blue-600 shadow-lg scale-105 hover:bg-blue-700 hover:shadow-xl hover:scale-110'
              : 'bg-white text-gray-700 border-gray-300 shadow-sm hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600 hover:shadow-md hover:scale-105 active:scale-95'
          ]"
        >
          {{ page }}
        </button>
        <span
          v-else
          class="px-3 py-3 sm:px-4 sm:py-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default select-none"
        >
          ...
        </span>
      </template>

      <!-- Next Button -->
      <button
        v-if="pagination.current_page < pagination.last_page"
        @click="$emit('page-change', pagination.current_page + 1)"
        class="px-3 py-3 sm:px-4 sm:py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md shadow-sm hover:bg-gray-50 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out transform hover:scale-105 active:scale-95 cursor-pointer disabled:cursor-not-allowed"
        :disabled="pagination.current_page >= pagination.last_page"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>

    <!-- Pagination Info with hover animation -->
    <div class="text-center mt-4 text-sm text-gray-600 transition-all duration-300 hover:text-gray-800">
      <span class="inline-block transition-transform duration-300 hover:scale-105 cursor-default select-none">
        Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }} результатов
      </span>
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

const emit = defineEmits(['page-change'])

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
    emit('page-change', page)
  }
}
</script> 