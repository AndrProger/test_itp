<template>
  <Head title="shinka.kz" />
  
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl 2xl:max-w-screen-2xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
          <!-- Logo and title section -->
          <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
            <img 
              src="/icons8-покрышка-ios-17-filled-32.png" 
              alt="shinka.kz" 
              class="w-6 h-6 sm:w-8 sm:h-8 flex-shrink-0"
            >
            <div class="min-w-0 flex-1">
              <div class="flex items-center space-x-2">
                <h1 class="text-lg sm:text-xl font-semibold text-gray-900 flex-shrink-0">
                  shinka.kz
                </h1>
                <span class="text-xs sm:text-sm text-gray-500 truncate">
                  — поиск шиномонтажных сервисов
                </span>
              </div>
            </div>
          </div>
          
          <!-- Counter section -->
          <div class="text-xs sm:text-sm text-gray-500 ml-2 flex-shrink-0">
            <span class="hidden sm:inline">Всего найдено: </span>
            <span class="font-medium">{{ totalCount }}</span>
            <span class="hidden sm:inline"> сервисов</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl 2xl:max-w-screen-2xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8 sm:mt-12 lg:mt-16">
      <div class="max-w-7xl 2xl:max-w-screen-2xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-6 sm:py-8">
        <div class="text-center text-gray-500 text-xs sm:text-sm">
          © 2024 shinka.kz — надежный поиск шиномонтажных сервисов
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  totalCount: {
    type: Number,
    default: 0
  }
})
</script> 