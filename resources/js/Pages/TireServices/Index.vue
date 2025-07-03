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
              <label class="flex items-center group cursor-pointer">
                <input
                  v-model="filters.hasImage"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-300 hover:border-blue-400 cursor-pointer"
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
                Диапазон: {{ filterStats.area_range?.min ?? 0 }} - {{ filterStats.area_range?.max ?? 0 }} м²
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

        <!-- Sorting -->
        <div class="mt-6">
          <label class="block text-sm font-medium text-gray-700 mb-3">
            Сортировка
          </label>
          
          <!-- Sort Field Selection -->
          <div class="mb-3">
            <select
              v-model="filters.sortBy"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:border-gray-400 hover:shadow-md cursor-pointer"
              @change="performSearch"
            >
              <option value="id">По умолчанию (ID)</option>
              <option value="name">По названию</option>
              <option value="area">По площади</option>
              <option value="rooms_count">По количеству комнат</option>
              <option value="created_at">По дате добавления</option>
            </select>
          </div>
          
          <!-- Sort Direction -->
          <div class="flex space-x-2">
            <button
              @click="setSortDirection('asc')"
              :class="[
                'flex-1 px-2 py-1 text-xs rounded border transition-all duration-300 ease-in-out transform hover:scale-105 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500',
                filters.sortDirection === 'asc'
                  ? 'bg-blue-100 border-blue-500 text-blue-700 shadow-md hover:bg-blue-200 hover:shadow-lg'
                  : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200 hover:border-gray-400 hover:shadow-md'
              ]"
            >
              ↑ По возрастанию
            </button>
            <button
              @click="setSortDirection('desc')"
              :class="[
                'flex-1 px-2 py-1 text-xs rounded border transition-all duration-300 ease-in-out transform hover:scale-105 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500',
                filters.sortDirection === 'desc'
                  ? 'bg-blue-100 border-blue-500 text-blue-700 shadow-md hover:bg-blue-200 hover:shadow-lg'
                  : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200 hover:border-gray-400 hover:shadow-md'
              ]"
            >
              ↓ По убыванию
            </button>
          </div>
        </div>

        <!-- Clear Filters and Stats -->
        <div class="mt-4 flex justify-between items-center">
          <button
            @click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 underline self-start transition-all duration-300 ease-in-out transform hover:scale-105 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1 rounded px-1"
          >
            Очистить все фильтры
          </button>
          <div class="text-right">
            <div class="text-sm text-gray-600">
              {{ loading ? 'Поиск...' : `Найдено: ${data.pagination.total} результатов` }}
            </div>
            <div v-if="filters.sortBy !== 'id' || filters.name" class="text-xs text-blue-600 mt-1">
              {{ getSortingLabel() }}
            </div>
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
          @page-change="handlePageChange"
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
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import AppLayout from '../Layout/AppLayout.vue'
import ServiceCard from './Components/ServiceCard.vue'
import Pagination from './Components/Pagination.vue'

const store = useStore()

const filters = computed({
  get: () => store.state.filters,
  set: (val) => store.commit('setFilters', val)
})
const page = computed({
  get: () => store.state.page,
  set: (val) => store.commit('setPage', val)
})

const data = ref({ data: [], pagination: {}, success: true })
const filterStats = ref({ area_range: { min: 0, max: 1000 }, rooms_counts: [] })
const totalCount = ref(0)
const loading = ref(false)

// --- Поиск ---
const performSearch = async (customPage = null) => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.name) params.append('name', filters.value.name)
    if (filters.value.hasImage) params.append('has_image', '1')
    if (filters.value.areaMin) params.append('area_min', filters.value.areaMin)
    if (filters.value.areaMax) params.append('area_max', filters.value.areaMax)
    if (filters.value.sortBy) params.append('sort_by', filters.value.sortBy)
    if (filters.value.sortDirection) params.append('sort_direction', filters.value.sortDirection)
    params.append('page', customPage ?? page.value)
    params.append('per_page', 20)
    if (filters.value.roomsCount && filters.value.roomsCount.length > 0) {
      filters.value.roomsCount.forEach(count => {
        params.append('rooms_count[]', count)
      })
    }
    const response = await fetch('/api/tire-services?' + params.toString(), {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    const result = await response.json()
    if (result.success) {
      data.value = result
      if (result.total_count !== undefined) {
        totalCount.value = result.total_count
      }
      store.commit('setPage', customPage ?? page.value)
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

// --- Дебаунс ---
let debounceTimeout = null
const debouncedSearch = () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => performSearch(1), 500)
}

// --- Очистка фильтров ---
const clearFilters = () => {
  store.commit('clearFilters')
  performSearch(1)
}

// --- Сортировка ---
const setSortDirection = (direction) => {
  filters.value.sortDirection = direction
  store.commit('setFilters', filters.value)
  performSearch(1)
}

// --- Пагинация ---
const handlePageChange = (newPage) => {
  store.commit('setPage', newPage)
  performSearch(newPage)
}

// --- Синхронизация с URL ---
const updateUrl = () => {
  const params = new URLSearchParams()
  if (filters.value.name) params.set('name', filters.value.name)
  if (filters.value.hasImage) params.set('has_image', '1')
  if (filters.value.roomsCount.length) {
    filters.value.roomsCount.forEach(count => params.append('rooms_count[]', count))
  }
  if (filters.value.areaMin) params.set('area_min', filters.value.areaMin)
  if (filters.value.areaMax) params.set('area_max', filters.value.areaMax)
  if (filters.value.sortBy && filters.value.sortBy !== 'id') params.set('sort_by', filters.value.sortBy)
  if (filters.value.sortDirection && filters.value.sortDirection !== 'desc') params.set('sort_direction', filters.value.sortDirection)
  params.set('page', page.value)
  const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '')
  window.history.replaceState({}, '', newUrl)
}

// --- Получение статистики фильтров ---
const fetchFilterStats = async () => {
  const response = await fetch('/api/tire-services/stats')
  if (response.ok) {
    filterStats.value = await response.json()
  }
}

// --- Инициализация ---
onMounted(async () => {
  await fetchFilterStats()
  await performSearch(page.value)
})

// Get sorting label for display
const getSortingLabel = () => {
  if (filters.value.name) {
    return 'Сортировка по релевантности поиска'
  }
  
  const sortLabels = {
    'id': 'По умолчанию',
    'name': 'По названию',
    'area': 'По площади',
    'rooms_count': 'По количеству комнат',
    'created_at': 'По дате добавления'
  }
  
  const directionLabel = filters.value.sortDirection === 'asc' ? 'по возрастанию' : 'по убыванию'
  return `${sortLabels[filters.value.sortBy]} ${directionLabel}`
}
</script> 