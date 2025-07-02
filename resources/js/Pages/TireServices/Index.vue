<template>
  <AppLayout :total-count="totalCount">
    <div class="space-y-6">
      <!-- Filters Section -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          🔍 Фильтры поиска
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Name Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Название
            </label>
            <input
              v-model="filters.name"
              type="text"
              placeholder="Введите название сервиса..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="debouncedSearch"
            />
          </div>

          <!-- Has Image Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Наличие фотографии
            </label>
            <div class="flex items-center space-x-4">
              <label class="flex items-center">
                <input
                  v-model="filters.hasImage"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  @change="performSearch"
                />
                <span class="ml-2 text-sm text-gray-600">Только с фото</span>
              </label>
            </div>
          </div>

          <!-- Area Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Площадь (м²)
            </label>
            <div class="space-y-2">
              <div class="flex space-x-2">
                <input
                  v-model.number="filters.areaMin"
                  type="number"
                  placeholder="От"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  @input="debouncedSearch"
                />
                <input
                  v-model.number="filters.areaMax"
                  type="number"
                  placeholder="До"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  @input="debouncedSearch"
                />
              </div>
              <div class="text-xs text-gray-500">
                Диапазон: {{ filterStats.area_range.min }} - {{ filterStats.area_range.max }} м²
              </div>
            </div>
          </div>
        </div>

        <!-- Rooms Count Filter -->
        <div class="mt-6">
          <label class="block text-sm font-medium text-gray-700 mb-3">
            Количество комнат
          </label>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="roomCount in filterStats.rooms_counts"
              :key="roomCount"
              class="flex items-center"
            >
              <input
                v-model="filters.roomsCount"
                :value="roomCount"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                @change="performSearch"
              />
              <span class="ml-2 text-sm text-gray-600">
                {{ roomCount }} {{ roomCount === 1 ? 'комната' : roomCount < 5 ? 'комнаты' : 'комнат' }}
              </span>
            </label>
          </div>
        </div>

        <!-- Clear Filters -->
        <div class="mt-4 flex justify-between items-center">
          <button
            @click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 underline"
          >
            Очистить все фильтры
          </button>
          <div class="text-sm text-gray-600">
            {{ loading ? 'Поиск...' : `Найдено: ${data.pagination.total} результатов` }}
          </div>
        </div>
      </div>

      <!-- Results Section -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Загрузка результатов...</p>
        </div>

        <!-- Results Grid -->
        <div v-else-if="data.data.length > 0" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ServiceCard
              v-for="service in data.data"
              :key="service.id"
              :service="service"
            />
          </div>

          <!-- Pagination -->
          <Pagination
            v-if="data.pagination.last_page > 1"
            :pagination="data.pagination"
            @page-changed="handlePageChange"
            class="mt-8"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="p-12 text-center">
          <div class="text-gray-400 text-6xl mb-4">🔍</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            Результатов не найдено
          </h3>
          <p class="text-gray-600 mb-4">
            Попробуйте изменить параметры поиска или очистить фильтры
          </p>
          <button
            @click="clearFilters"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Очистить фильтры
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import AppLayout from '../Layout/AppLayout.vue'
import ServiceCard from './Components/ServiceCard.vue'
import Pagination from './Components/Pagination.vue'

// Props от Laravel
const props = defineProps({
  initialData: Object,
  filterStats: Object,
  initialFilters: Object
})

// Reactive state
const data = ref(props.initialData)
const loading = ref(false)
const totalCount = ref(props.initialData?.total_count || 0)

const filters = reactive({
  name: props.initialFilters.name || '',
  hasImage: props.initialFilters.has_image || false,
  roomsCount: props.initialFilters.rooms_count || [],
  areaMin: props.initialFilters.area_min || null,
  areaMax: props.initialFilters.area_max || null
})

// Debounced search function
const debouncedSearch = debounce(() => {
  performSearch()
}, 500)

// Perform search
const performSearch = async (page = 1) => {
  loading.value = true
  
  try {
    const params = new URLSearchParams()
    
    // Add basic params
    if (filters.name) params.append('name', filters.name)
    if (filters.hasImage) params.append('has_image', '1')
    if (filters.areaMin) params.append('area_min', filters.areaMin)
    if (filters.areaMax) params.append('area_max', filters.areaMax)
    params.append('page', page)
    params.append('per_page', 15)
    
    // Add rooms_count array properly
    if (filters.roomsCount && filters.roomsCount.length > 0) {
      filters.roomsCount.forEach(count => {
        params.append('rooms_count[]', count)
      })
    }
    
    console.log('API Request URL:', '/api/tire-services?' + params.toString())
    
    const response = await fetch('/api/tire-services?' + params.toString(), {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const result = await response.json()
    console.log('API Response:', result)
    
    if (result.success) {
      data.value = result
      
      // Обновляем общее количество, если оно есть в ответе
      if (result.total_count !== undefined) {
        totalCount.value = result.total_count
      }
      
      // Update URL with current filters
      updateUrl()
    } else {
      console.error('API returned error:', result)
    }
  } catch (error) {
    console.error('Search error:', error)
  } finally {
    loading.value = false
  }
}

// Update URL with current filters
const updateUrl = () => {
  const params = new URLSearchParams()
  
  if (filters.name) params.set('name', filters.name)
  if (filters.hasImage) params.set('has_image', '1')
  if (filters.roomsCount.length) {
    filters.roomsCount.forEach(count => params.append('rooms_count[]', count))
  }
  if (filters.areaMin) params.set('area_min', filters.areaMin)
  if (filters.areaMax) params.set('area_max', filters.areaMax)
  
  const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '')
  window.history.replaceState({}, '', newUrl)
}

// Clear all filters
const clearFilters = () => {
  filters.name = ''
  filters.hasImage = false
  filters.roomsCount = []
  filters.areaMin = null
  filters.areaMax = null
  
  performSearch()
}

// Handle page change
const handlePageChange = (page) => {
  performSearch(page)
}

// Initialize component
onMounted(() => {
  // Parse URL parameters on load
  const urlParams = new URLSearchParams(window.location.search)
  
  // Update filters from URL
  if (urlParams.get('name')) filters.name = urlParams.get('name')
  if (urlParams.get('has_image')) filters.hasImage = urlParams.get('has_image') === '1'
  if (urlParams.get('area_min')) filters.areaMin = parseInt(urlParams.get('area_min'))
  if (urlParams.get('area_max')) filters.areaMax = parseInt(urlParams.get('area_max'))
  
  // Parse rooms_count array
  const roomsCountParams = urlParams.getAll('rooms_count[]')
  if (roomsCountParams.length > 0) {
    filters.roomsCount = roomsCountParams.map(count => parseInt(count))
  }
  
  console.log('Component mounted with filters:', filters)
  console.log('Initial data:', data.value)
  
  // Load initial data if needed
  if (!data.value || !data.value.data || data.value.data.length === 0) {
    performSearch()
  }
})
</script> 