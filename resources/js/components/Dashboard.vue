<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            Tracker Forms Dashboard
          </h1>
          <p class="text-gray-600 mt-2">Manage and track all your client transactions</p>
        </div>
        
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
          <button
            @click="exportToCSV"
            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition-all duration-200"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
          </button>
          
          <button
            @click="handleLogout"
            class="flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all duration-200"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
          </button>
        </div>
      </div>

      <!-- IMPROVED Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Forms Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
              Forms
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ trackerForms.length }}</p>
            <p class="text-sm text-gray-500 truncate">Total Forms</p>
          </div>
        </div>

        <!-- Total Amount In Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-green-50 rounded-lg">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
              +{{ stats.totalAmountIn > 0 ? formatCurrency(stats.totalAmountIn / trackerForms.length) : '0' }}
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(stats.totalAmountIn) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Amount In</p>
          </div>
        </div>

        <!-- Total Fees Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-orange-50 rounded-lg">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
              {{ stats.totalAmountIn > 0 ? ((stats.totalFees / stats.totalAmountIn) * 100).toFixed(1) + '%' : '0%' }}
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(stats.totalFees) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Fees</p>
          </div>
        </div>

        <!-- Total Amount Out Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
              Net
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(stats.totalAmountOut) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Amount Out</p>
          </div>
        </div>

        <!-- Completed Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-emerald-50 rounded-lg">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
              {{ trackerForms.length > 0 ? Math.round((stats.completed / trackerForms.length) * 100) : 0 }}%
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ stats.completed }}</p>
            <p class="text-sm text-gray-500 truncate">Completed Forms</p>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
              <div 
                class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" 
                :style="{ width: trackerForms.length > 0 ? (stats.completed / trackerForms.length) * 100 + '%' : '0%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Pending Card -->
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-amber-50 rounded-lg">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
              {{ trackerForms.length > 0 ? Math.round((stats.pending / trackerForms.length) * 100) : 0 }}%
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ stats.pending }}</p>
            <p class="text-sm text-gray-500 truncate">Pending Forms</p>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
              <div 
                class="bg-amber-500 h-1.5 rounded-full transition-all duration-500" 
                :style="{ width: trackerForms.length > 0 ? (stats.pending / trackerForms.length) * 100 + '%' : '0%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Messages -->
    <div v-if="successMessage" class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl flex items-center animate-fade-in">
      <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-green-700 font-medium">{{ successMessage }}</p>
      <button @click="successMessage = ''" class="ml-auto text-green-600 hover:text-green-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div v-if="errorMessage" class="mb-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl flex items-center animate-fade-in">
      <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-red-700 font-medium">{{ errorMessage }}</p>
      <button @click="errorMessage = ''" class="ml-auto text-red-600 hover:text-red-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Action Bar -->
    <div class="mb-6 bg-white rounded-xl shadow-lg p-4 border border-gray-200">
      <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
          <div class="relative flex-1 md:flex-none">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search forms..."
              class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-64 transition-all duration-200"
              @input="onSearch"
            />
            <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          
          <button
            @click="showFilters = !showFilters"
            class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filters
          </button>
          
          <div v-if="selectedForms.length > 0" class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">{{ selectedForms.length }} selected</span>
            <button
              @click="bulkDelete"
              class="flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all duration-200"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete
            </button>
          </div>
        </div>
        
        <button
          @click="openAddModal"
          class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-5 rounded-lg flex items-center shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add New Form
        </button>
      </div>

      <!-- Filters Panel -->
      <div v-if="showFilters" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 animate-slide-down">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="filterStatus" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              @change="applyFilters"
            >
              <option value="all">All Status</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
            <select 
              v-model="filterPaymentMethod" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              @change="applyFilters"
            >
              <option value="all">All Methods</option>
              <option v-for="method in paymentMethods" :key="method" :value="method">{{ method }}</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
            <input 
              v-model="filterDateFrom" 
              type="date" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              @change="applyFilters"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
            <input 
              v-model="filterDateTo" 
              type="date" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              @change="applyFilters"
            />
          </div>
        </div>
        
        <div class="flex justify-end mt-4">
          <button
            @click="resetFilters"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg mr-2 transition-colors duration-200"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="relative">
        <div class="w-16 h-16 border-4 border-blue-200 rounded-full"></div>
        <div class="absolute top-0 left-0 w-16 h-16 border-4 border-blue-600 rounded-full animate-spin border-t-transparent"></div>
        <div class="mt-4 text-gray-600">Loading tracker forms...</div>
      </div>
    </div>

    <!-- IMPROVED Table Container -->
    <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="selectedForms.length === paginatedForms.length && paginatedForms.length > 0"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-colors duration-200"
                />
              </th>
              <th 
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                @click="sortTable('client_name')"
              >
                <div class="flex items-center">
                  Client Name
                  <svg v-if="sortField === 'client_name'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </th>
              <th 
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                @click="sortTable('date')"
              >
                <div class="flex items-center">
                  Date
                  <svg v-if="sortField === 'date'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Payment Method
              </th>
              <th 
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                @click="sortTable('amount_in')"
              >
                <div class="flex items-center">
                  Amount In
                  <svg v-if="sortField === 'amount_in'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fees
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount Out
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="formItem in paginatedForms" 
              :key="formItem.id" 
              class="hover:bg-blue-50 transition-all duration-150"
              :class="{ 
                'bg-blue-50': selectedForms.includes(formItem.id),
                'hover:scale-[1.002]': !selectedForms.includes(formItem.id)
              }"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  type="checkbox"
                  :checked="selectedForms.includes(formItem.id)"
                  @change="toggleSelectForm(formItem.id)"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-colors duration-200"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <span class="font-semibold text-blue-600">{{ formItem.client_name.charAt(0) }}</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900 hover:text-blue-600 transition-colors duration-200 cursor-pointer" @click="openEditModal(formItem)">
                      {{ formItem.client_name }}
                    </div>
                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ formItem.description || 'No description' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDateForDisplay(formItem.date) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full transition-all duration-200',
                  formItem.payment_method === 'Cash' ? 'bg-green-100 text-green-800 hover:bg-green-200' :
                  formItem.payment_method === 'Bank Transfer' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' :
                  formItem.payment_method === 'Mobile Money' ? 'bg-purple-100 text-purple-800 hover:bg-purple-200' :
                  formItem.payment_method === 'Credit Card' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' :
                  'bg-gray-100 text-gray-800 hover:bg-gray-200'
                ]">
                  {{ formItem.payment_method }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                {{ formatCurrency(formItem.amount_in) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">
                {{ formatCurrency(formItem.fees) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                {{ formatCurrency(formItem.amount_out) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span :class="[
                    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full transition-all duration-200',
                    formItem.feedback ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200 hover:from-green-200 hover:to-emerald-200' : 
                    'bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200 hover:from-amber-200 hover:to-orange-200'
                  ]">
                    {{ formItem.feedback ? 'Completed' : 'Pending' }}
                  </span>
                  <div v-if="formItem.feedback_date" class="ml-2 text-xs text-gray-500">
                    {{ formatDateForDisplay(formItem.feedback_date) }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <button
                    @click="openEditModal(formItem)"
                    class="text-blue-600 hover:text-blue-900 p-1.5 hover:bg-blue-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteTrackerForm(formItem.id)"
                    class="text-red-600 hover:text-red-900 p-1.5 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  <button
                    v-if="!formItem.feedback"
                    @click="markAsCompleted(formItem)"
                    class="text-green-600 hover:text-green-900 p-1.5 hover:bg-green-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Mark as Completed"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredForms.length === 0">
              <td colspan="9" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                  <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4 animate-pulse">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">No tracker forms found</h3>
                  <p class="text-gray-500 mb-4">Try adjusting your search or filters</p>
                  <button
                    @click="resetFilters"
                    class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200"
                  >
                    Reset all filters
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="bg-white px-6 py-4 border-t border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to 
            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredForms.length) }}</span> of 
            <span class="font-medium">{{ filteredForms.length }}</span> results
          </div>
          <div class="flex items-center space-x-2">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              Previous
            </button>
            
            <div class="flex items-center space-x-1">
              <button
                v-for="page in getPaginationRange()"
                :key="page"
                @click="changePage(page)"
                :class="[
                  'px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200',
                  currentPage === page
                    ? 'bg-blue-600 text-white shadow-md'
                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>
            
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
              {{ isEditing ? 'Edit Tracker Form' : 'Add New Tracker Form' }}
            </h3>
            <button
              @click="showModal = false"
              class="text-gray-400 hover:text-gray-600 p-1 hover:bg-gray-100 rounded-lg transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                <input
                  v-model="form.client_name"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                  placeholder="Enter client name"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                <input
                  v-model="form.date"
                  type="date"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                <select
                  v-model="form.payment_method"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                  <option value="">Select Method</option>
                  <option v-for="method in paymentMethods" :key="method" :value="method">
                    {{ method }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount In *</label>
                <div class="relative">
                  <span class="absolute left-3 top-3 text-gray-500">KES</span>
                  <input
                    v-model="form.amount_in"
                    type="number"
                    step="0.01"
                    required
                    @input="calculateNetAmount"
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    placeholder="0.00"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fees</label>
                <div class="relative">
                  <span class="absolute left-3 top-3 text-gray-500">KES</span>
                  <input
                    v-model="form.fees"
                    type="number"
                    step="0.01"
                    @input="calculateNetAmount"
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    placeholder="0.00"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Out</label>
                <div class="relative">
                  <span class="absolute left-3 top-3 text-gray-500">KES</span>
                  <input
                    v-model="form.amount_out"
                    type="number"
                    step="0.01"
                    readonly
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 transition-all duration-200"
                  />
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                placeholder="Enter transaction description"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                <textarea
                  v-model="form.feedback"
                  rows="2"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                  placeholder="Enter feedback"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feedback Date</label>
                <input
                  v-model="form.feedback_date"
                  type="date"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                />
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <button
                type="button"
                @click="showModal = false"
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-200 transform hover:-translate-y-0.5"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg"
              >
                {{ isEditing ? 'Update Form' : 'Create Form' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Database Connection Status -->
    <div v-if="showDbStatus" class="fixed bottom-4 right-4 z-40 animate-slide-up">
      <div class="bg-white rounded-lg shadow-xl border border-gray-200 p-4 max-w-sm">
        <div class="flex items-center justify-between mb-2">
          <h4 class="font-semibold text-gray-800">Database Status</h4>
          <button @click="showDbStatus = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Connection:</span>
            <span class="flex items-center">
              <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
              <span class="text-sm font-medium text-green-600">Connected</span>
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Total Records:</span>
            <span class="text-sm font-medium text-blue-600">{{ trackerForms.length }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Last Sync:</span>
            <span class="text-sm text-gray-500">{{ lastSync }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center text-sm text-gray-500">
      <p>© {{ new Date().getFullYear() }} Web Tracker Dashboard. All rights reserved.</p>
    </div>
  </div>
</template>

<!-- The script section remains the same as your original -->
<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import api from '../services/api';
import { useRouter } from 'vue-router';

const router = useRouter();
const trackerForms = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('date');
const sortDirection = ref('desc');
const selectedForms = ref([]);
const showFilters = ref(false);
const filterStatus = ref('all');
const filterPaymentMethod = ref('all');
const filterDateFrom = ref('');
const filterDateTo = ref('');
const showDbStatus = ref(true);
const lastSync = ref('Just now');

// Database connection state
const dbConnection = ref({
  status: 'connected',
  server: 'localhost:3306',
  database: 'tracker_db',
  version: '8.0'
});

const form = ref({
  id: null,
  client_name: '',
  date: '',
  payment_method: '',
  description: '',
  amount_in: '',
  fees: '',
  amount_out: '',
  feedback: '',
  feedback_date: ''
});

const paymentMethods = ref(['Cash', 'Bank Transfer', 'Mobile Money', 'Credit Card', 'Other']);

// Debounce search
let searchTimeout = null;

// Computed properties
const filteredForms = computed(() => {
  let filtered = [...trackerForms.value];
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(form => 
      form.client_name.toLowerCase().includes(query) ||
      form.description.toLowerCase().includes(query) ||
      form.payment_method.toLowerCase().includes(query)
    );
  }
  
  // Status filter
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(form => 
      filterStatus.value === 'completed' ? form.feedback : !form.feedback
    );
  }
  
  // Payment method filter
  if (filterPaymentMethod.value !== 'all') {
    filtered = filtered.filter(form => 
      form.payment_method === filterPaymentMethod.value
    );
  }
  
  // Date range filter
  if (filterDateFrom.value) {
    const fromDate = new Date(filterDateFrom.value);
    filtered = filtered.filter(form => new Date(form.date) >= fromDate);
  }
  
  if (filterDateTo.value) {
    const toDate = new Date(filterDateTo.value);
    filtered = filtered.filter(form => new Date(form.date) <= toDate);
  }
  
  // Sorting
  filtered.sort((a, b) => {
    let aValue = a[sortField.value];
    let bValue = b[sortField.value];
    
    if (sortField.value.includes('date')) {
      aValue = new Date(aValue);
      bValue = new Date(bValue);
    }
    
    if (sortDirection.value === 'asc') {
      return aValue > bValue ? 1 : -1;
    } else {
      return aValue < bValue ? 1 : -1;
    }
  });
  
  return filtered;
});

const paginatedForms = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredForms.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredForms.value.length / itemsPerPage.value);
});

const stats = computed(() => {
  const totalAmountIn = trackerForms.value.reduce((sum, form) => sum + (parseFloat(form.amount_in) || 0), 0);
  const totalFees = trackerForms.value.reduce((sum, form) => sum + (parseFloat(form.fees) || 0), 0);
  const totalAmountOut = trackerForms.value.reduce((sum, form) => sum + (parseFloat(form.amount_out) || 0), 0);
  const completed = trackerForms.value.filter(form => form.feedback).length;
  const pending = trackerForms.value.length - completed;
  
  return {
    totalAmountIn,
    totalFees,
    totalAmountOut,
    completed,
    pending,
    averageTransaction: trackerForms.value.length > 0 ? totalAmountIn / trackerForms.value.length : 0
  };
});

// Fetch tracker forms
const fetchTrackerForms = async () => {
  loading.value = true;
  errorMessage.value = '';
  
  try {
    const params = {
      page: currentPage.value,
      per_page: itemsPerPage.value,
      sort_field: sortField.value,
      sort_direction: sortDirection.value
    };
    
    if (searchQuery.value) params.search = searchQuery.value;
    if (filterStatus.value !== 'all') params.status = filterStatus.value;
    if (filterPaymentMethod.value !== 'all') params.payment_method = filterPaymentMethod.value;
    if (filterDateFrom.value) params.date_from = filterDateFrom.value;
    if (filterDateTo.value) params.date_to = filterDateTo.value;
    
    const response = await api.getTrackerForms(params);
    trackerForms.value = response.data.data;

    
    // Update last sync time
    lastSync.value = new Date().toLocaleTimeString();
    
    // Check database connection
    if (response.data.success) {
      dbConnection.value.status = 'connected';
    }
    
  } catch (error) {
    errorMessage.value = 'Failed to fetch tracker forms. Please try again.' + error;
    dbConnection.value.status = 'disconnected';
    console.error('Error fetching tracker forms:', error);
  } finally {
    loading.value = false;
  }
};

// Open modal for adding new form
const openAddModal = () => {
  isEditing.value = false;
  resetForm();
  showModal.value = true;
};

// Open modal for editing
const openEditModal = (trackerForm) => {
  isEditing.value = true;
  form.value = { ...trackerForm };
  form.value.date = formatDateForInput(trackerForm.date);
  form.value.feedback_date = trackerForm.feedback_date ? formatDateForInput(trackerForm.feedback_date) : '';
  showModal.value = true;
};

// Format date for input field
const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toISOString().split('T')[0];
};

// Format date for display
const formatDateForDisplay = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Format currency
const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '-';
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'KES',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount);
};

// Calculate net amount
const calculateNetAmount = () => {
  const amountIn = parseFloat(form.value.amount_in) || 0;
  const fees = parseFloat(form.value.fees) || 0;
  form.value.amount_out = (amountIn - fees).toFixed(2);
};

// Reset form
const resetForm = () => {
  form.value = {
    id: null,
    client_name: '',
    date: new Date().toISOString().split('T')[0],
    payment_method: '',
    description: '',
    amount_in: '',
    fees: '',
    amount_out: '',
    feedback: '',
    feedback_date: ''
  };
};

// Handle form submission
const handleSubmit = async () => {
  errorMessage.value = '';
  successMessage.value = '';
  
  try {
    if (isEditing.value) {
      await api.updateTrackerForm(form.value.id, form.value);
      successMessage.value = 'Tracker form updated successfully!';
    } else {
      await api.createTrackerForm(form.value);
      successMessage.value = 'Tracker form created successfully!';
    }
    
    await fetchTrackerForms();
    showModal.value = false;
    resetForm();
    
    // Clear success message after 3 seconds
    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  } catch (error) {
    console.log(error);
    if (error.response && error.response.data.errors) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join(', ');
    } else {
      errorMessage.value = 'An error occurred. Please try again.';
    }
  }
};

// Delete tracker form
const deleteTrackerForm = async (id) => {
  if (!confirm('Are you sure you want to delete this tracker form?')) {
    return;
  }
  
  try {
    await api.deleteTrackerForm(id);
    await fetchTrackerForms();
    successMessage.value = 'Tracker form deleted successfully!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    errorMessage.value = 'Failed to delete tracker form. Please try again.';
  }
};

// Bulk delete
const bulkDelete = async () => {
  if (selectedForms.value.length === 0) {
    errorMessage.value = 'Please select at least one form to delete.';
    return;
  }
  
  if (!confirm(`Are you sure you want to delete ${selectedForms.value.length} selected forms?`)) {
    return;
  }
  
  try {
    await api.bulkDeleteForms(selectedForms.value);
    await fetchTrackerForms();
    selectedForms.value = [];
    successMessage.value = `${selectedForms.value.length} forms deleted successfully!`;
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    errorMessage.value = 'Failed to delete selected forms. Please try again.';
  }
};

// Toggle select all
const toggleSelectAll = () => {
  if (selectedForms.value.length === paginatedForms.value.length) {
    selectedForms.value = [];
  } else {
    selectedForms.value = paginatedForms.value.map(form => form.id);
  }
};

// Toggle selection for a single form
const toggleSelectForm = (id) => {
  const index = selectedForms.value.indexOf(id);
  if (index > -1) {
    selectedForms.value.splice(index, 1);
  } else {
    selectedForms.value.push(id);
  }
};

// Export to CSV
const exportToCSV = async () => {
  try {
    const params = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (filterStatus.value !== 'all') params.status = filterStatus.value;
    if (filterPaymentMethod.value !== 'all') params.payment_method = filterPaymentMethod.value;
    if (filterDateFrom.value) params.date_from = filterDateFrom.value;
    if (filterDateTo.value) params.date_to = filterDateTo.value;
    
    const response = await api.exportToCSV(params);
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `tracker-forms-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    successMessage.value = 'Data exported to CSV successfully!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    errorMessage.value = 'Failed to export data. Please try again.';
  }
};

// Sort table
const sortTable = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  fetchTrackerForms();
};

// Change page
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchTrackerForms();
  }
};

// Get pagination range
const getPaginationRange = () => {
  const range = [];
  const total = totalPages.value;
  const current = currentPage.value;
  const delta = 2;
  
  for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
    range.push(i);
  }
  
  if (current - delta > 2) {
    range.unshift('...');
  }
  if (current + delta < total - 1) {
    range.push('...');
  }
  
  range.unshift(1);
  if (total > 1) {
    range.push(total);
  }
  
  return range;
};

// Reset filters
const resetFilters = () => {
  filterStatus.value = 'all';
  filterPaymentMethod.value = 'all';
  filterDateFrom.value = '';
  filterDateTo.value = '';
  searchQuery.value = '';
  currentPage.value = 1;
  fetchTrackerForms();
};

// Apply filters
const applyFilters = () => {
  currentPage.value = 1;
  fetchTrackerForms();
};

// Debounced search
const onSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchTrackerForms();
  }, 500);
};

// Mark as completed
const markAsCompleted = async (formItem) => {
  const feedback = prompt('Enter feedback for completion:', formItem.feedback || '');
  if (feedback === null) return; // User cancelled
  
  if (!feedback.trim()) {
    errorMessage.value = 'Feedback is required to mark as completed.';
    return;
  }
  
  try {
    const data = {
      feedback: feedback.trim(),
      feedback_date: new Date().toISOString().split('T')[0]
    };
    
    await api.markAsCompleted(formItem.id, data);
    await fetchTrackerForms();
    
    successMessage.value = 'Form marked as completed successfully!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    errorMessage.value = 'Failed to mark form as completed. Please try again.';
  }
};

// Logout function
const handleLogout = async () => {
  try {
    await api.logout();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/');
  } catch (error) {
    console.error('Logout error:', error);
  }
};

// Auto-refresh data every 30 seconds
const startAutoRefresh = () => {
  setInterval(() => {
    if (!showModal.value) {
      fetchTrackerForms();
    }
  }, 30000);
};

// Initialize on mount
onMounted(() => {
  fetchTrackerForms();
  
  // Set today's date as default for new forms
  const today = new Date().toISOString().split('T')[0];
  form.value.date = today;
  
  // Start auto-refresh
  startAutoRefresh();
  
  // Hide db status after 10 seconds
  setTimeout(() => {
    showDbStatus.value = false;
  }, 10000);
});
</script>

<style scoped>
/* Custom scrollbar for modal */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Smooth transitions */
input, select, textarea, button {
  transition: all 0.2s ease-in-out;
}

/* Custom focus styles */
input:focus, select:focus, textarea:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table row hover effect */
tr {
  transition: all 0.15s ease-in-out;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-slide-down {
  animation: slideDown 0.3s ease-out;
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

/* Gradient text animation */
.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* Pulse animation for status indicators */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover scale effect */
.hover-scale {
  transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
  transform: scale(1.05);
}

/* Improved stats cards */
.stats-card {
  position: relative;
  overflow: hidden;
}

.stats-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
}

/* Progress bar animation */
.progress-bar {
  transition: width 1s ease-in-out;
}
</style>