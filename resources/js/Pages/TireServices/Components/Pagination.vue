<template>
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <!-- Results info - visible only on medium screens and up -->
    <div class="hidden md:block text-sm text-gray-700">
      Показано
      <span class="font-medium">{{ pagination.from }}</span>
      -
      <span class="font-medium">{{ pagination.to }}</span>
      из
      <span class="font-medium">{{ pagination.total }}</span>
      результатов
    </div>

    <!-- Pagination controls -->
    <div class="flex justify-center items-center space-x-1 sm:space-x-2 mx-auto md:mx-0">
      <!-- Previous Button -->
      <button
        v-if="pagination.current_page > 1"
        @click="$emit('page-change', pagination.current_page - 1)"
        class="px-2 py-2 sm:px-3 sm:py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out cursor-pointer disabled:cursor-not-allowed"
        :disabled="pagination.current_page <= 1"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>

      <!-- Page Numbers -->
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="page !== '...'"
          @click="$emit('page-change', page)"
          :class="[
            'px-2 py-2 sm:px-3 sm:py-3 text-sm font-medium border transition-all duration-300 ease-in-out cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500',
            page === pagination.current_page
              ? 'bg-blue-600 text-white border-blue-600 shadow-lg'
              : 'bg-white text-gray-700 border-gray-300 shadow-sm hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600'
          ]"
        >
          {{ page }}
        </button>
        <span
          v-else
          class="px-2 py-2 sm:px-3 sm:py-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default select-none"
        >
          ...
        </span>
      </template>

      <!-- Next Button -->
      <button
        v-if="pagination.current_page < pagination.last_page"
        @click="$emit('page-change', pagination.current_page + 1)"
        class="px-2 py-2 sm:px-3 sm:py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out cursor-pointer disabled:cursor-not-allowed"
        :disabled="pagination.current_page >= pagination.last_page"
      >
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>

    <!-- Pagination Info - visible on all screens, but positioned differently based on screen size -->
    <div class="text-center text-sm text-gray-600 mt-2 md:mt-0">
      <span class="inline-block cursor-default select-none">
        Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }} результатов
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['page-change'])

// Responsive window width tracking
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)

const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth
}

onMounted(() => {
  window.addEventListener('resize', updateWindowWidth)
  updateWindowWidth()
})

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth)
})

// Compute delta based on screen size
const delta = computed(() => {
  // On mobile screens (< 640px), show only 1 page on each side
  if (windowWidth.value < 640) {
    return 1
  }
  // On medium screens (< 768px), show 1 page on each side
  else if (windowWidth.value < 768) {
    return 1
  }
  // On larger screens, show 2 pages on each side
  return 2
})

// Calculate visible page numbers
const visiblePages = computed(() => {
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const currentDelta = delta.value
  const pages = []

  // For very small pagination, just show all pages
  if (last <= 5) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
    return pages
  }

  // Always show first page
  if (current > currentDelta + 2) {
    pages.push(1)
    if (current > currentDelta + 3) {
      pages.push('...')
    }
  }

  // Show pages around current page
  const start = Math.max(1, current - currentDelta)
  const end = Math.min(last, current + currentDelta)

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  // Always show last page
  if (current < last - currentDelta - 1) {
    if (current < last - currentDelta - 2) {
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
