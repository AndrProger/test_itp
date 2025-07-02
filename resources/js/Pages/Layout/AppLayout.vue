<template>
  <Head title="shinka.kz" />
  
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-3">
            <img src="/icons8-покрышка-ios-17-filled-32.png" alt="shinka.kz" class="w-8 h-8">
            <h1 class="text-xl font-semibold text-gray-900">
              shinka.kz
            </h1>
            <span class="text-sm text-gray-500">
              — поиск шиномонтажных сервисов
            </span>
          </div>
          <div class="text-sm text-gray-500">
            Всего найдено: {{ totalCount }} сервисов
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center text-gray-500 text-sm">
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