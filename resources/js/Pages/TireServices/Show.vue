<template>
  <AppLayout :total-count="1">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <button
          @click="goBack"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          Назад к результатам поиска
        </button>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="md:flex">
          <!-- Image Section -->
          <div class="md:w-1/2">
            <div class="relative h-64 md:h-full bg-gray-100">
              <img
                v-if="tireService.image"
                :src="tireService.image_url || placeholderImage"
                :alt="tireService.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div v-else class="flex items-center justify-center h-full text-gray-400">
                <div class="text-center">
                  <svg class="w-20 h-20 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17l2.5-3.25L14.5 17H9z"/>
                  </svg>
                  <p class="text-sm">Изображение отсутствует</p>
                </div>
              </div>
              
              <!-- Image Badge -->
              <div v-if="tireService.image" class="absolute top-4 right-4">
                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                  📷 Есть фото
                </span>
              </div>
            </div>
          </div>

          <!-- Content Section -->
          <div class="md:w-1/2 p-8">
            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-6">
              {{ tireService.name }}
            </h1>

            <!-- Details Grid -->
            <div class="space-y-6">
              <!-- Basic Info -->
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm text-gray-600 mb-1">Количество комнат</div>
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ tireService.rooms_count }}
                    <span class="text-base font-normal text-gray-600">
                      {{ getRoomsLabel(tireService.rooms_count) }}
                    </span>
                  </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm text-gray-600 mb-1">Этаж</div>
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ tireService.floor }}
                  </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                  <div class="text-sm text-gray-600 mb-1">Площадь</div>
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ tireService.area }} м²
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Описание</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="text-gray-700 leading-relaxed">
                    {{ tireService.description || 'Описание не указано' }}
                  </p>
                </div>
              </div>

              <!-- Metadata -->
              <div class="border-t pt-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">ID сервиса:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ tireService.id }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Дата создания:</span>
                    <span class="font-medium text-gray-900 ml-2">
                      {{ formatDate(tireService.created_at) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-4 pt-4">
                <button
                  @click="goBack"
                  class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors"
                >
                  Назад к поиску
                </button>
                <button
                  @click="shareService"
                  class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md font-medium hover:bg-gray-50 transition-colors"
                >
                  Поделиться
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Similar Services -->
      <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
          Похожие сервисы
        </h2>
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
          <div class="text-gray-400 text-4xl mb-4">🔍</div>
          <p class="text-gray-600">
            Функция поиска похожих сервисов будет добавлена в следующей версии
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '../Layout/AppLayout.vue'

const props = defineProps({
  tireService: {
    type: Object,
    required: true
  }
})

// Placeholder image URL
const placeholderImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiM5Y2ExYTYiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='

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

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Go back to search
const goBack = () => {
  router.visit('/')
}

// Share service
const shareService = () => {
  if (navigator.share) {
    navigator.share({
      title: props.tireService.name,
      text: `Проверьте этот шиномонтажный сервис: ${props.tireService.name}`,
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href).then(() => {
      alert('Ссылка скопирована в буфер обмена!')
    })
  }
}
</script> 