<template>
  <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden card-animation">
    <!-- Image Section -->
    <div class="relative h-48 bg-gray-100">
      <img
        v-if="service.image"
        :src="service.image_url || placeholderImage"
        :alt="service.name"
        class="w-full h-full object-cover"
        @error="handleImageError"
      />
      <div v-else class="flex items-center justify-center h-full text-gray-400">
        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
          <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17l2.5-3.25L14.5 17H9z"/>
        </svg>
      </div>
      
      <!-- Has Image Badge -->
      <div v-if="service.image" class="absolute top-2 right-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
        <span class="">
          📷
        </span>
        <span class>
          Есть фото
        </span>
      </div>
    </div>

    <!-- Content Section -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 title-fixed-height">
        {{ service.name }}
      </h3>

      <!-- Details Grid -->
      <div class="space-y-2 mb-4">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">Комнат:</span>
          <span class="font-medium text-gray-900">
            {{ service.rooms_count }} {{ getRoomsLabel(service.rooms_count) }}
          </span>
        </div>
        
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">Этаж:</span>
          <span class="font-medium text-gray-900">{{ service.floor }}</span>
        </div>
        
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">Площадь:</span>
          <span class="font-medium text-gray-900">{{ service.area }} м²</span>
        </div>
        
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600">Добавлено:</span>
          <span class="font-medium text-gray-900">{{ formatDate(service.created_at) }}</span>
        </div>
      </div>

      <!-- Description -->
      <p class="text-sm text-gray-600 mb-4 line-clamp-3 description-fixed-height">
        {{ service.description || 'Описание не указано' }}
      </p>

      <!-- Action Button -->
      <div class="flex justify-between items-center card-button-animation">
        <button
          @click="openDetails"
          class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer"
        >
          Подробнее
        </button>
        
        <!-- Metadata -->
        <div class="text-xs text-gray-400">
          ID: {{ service.id }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  service: {
    type: Object,
    required: true
  }
})

// Placeholder image URL
const placeholderImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGFsaWdubWVudC1iYXNlbGluZT0ibWlkZGxlIiBmaWxsPSIjOTk5Ij5ObyBJbWFnZTwvdGV4dD48L3N2Zz4='

// Get proper room label in Russian
const getRoomsLabel = (count) => {
  if (count === 1) return 'комната'
  if (count < 5) return 'комнаты'
  return 'комнат'
}

// Handle image load error
const handleImageError = (event) => {
  event.target.src = placeholderImage
}

// Open service details
const openDetails = () => {
  router.visit(`/tire-service/${props.service.id}`)
}

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'Не указано'
  
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) {
    return 'Сегодня'
  } else if (diffDays === 1) {
    return 'Вчера'
  } else if (diffDays < 7) {
    return `${diffDays} дн. назад`
  } else if (diffDays < 30) {
    const weeks = Math.floor(diffDays / 7)
    return `${weeks} нед. назад`
  } else if (diffDays < 365) {
    const months = Math.floor(diffDays / 30)
    return `${months} мес. назад`
  } else {
    return date.toLocaleDateString('ru-RU', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.title-fixed-height {
  height: 3.5rem; /* Фиксированная высота для 2 строк (56px) */
  line-height: 1.75rem; /* 28px line-height для text-lg */
}


  .description-fixed-height {
    height: 3.75rem; /* Фиксированная высота для 3 строк text-sm (60px) */
    line-height: 1.25rem; /* 20px line-height для text-sm */
  }
  .card-animation {
  transition: transform 0.3s ease-in-out;
}

.card-animation:hover {
  transform: translateY(-5px);
}
</style> 
