<template>
  <div v-if="!isUserLoaded" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="text-center">
      <div class="relative">
        <div class="w-20 h-20 border-4 border-blue-200 rounded-full"></div>
        <div class="absolute top-0 left-0 w-20 h-20 border-4 border-blue-600 rounded-full animate-spin border-t-transparent"></div>
      </div>
      <p class="mt-6 text-lg text-gray-600 font-medium">Loading your dashboard...</p>
      <p class="mt-2 text-sm text-gray-500">Please wait while we load your data</p>
    </div>
  </div>
  
  <div v-else class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6">
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            Tracker Forms Dashboard
          </h1>
          <p class="text-gray-600 mt-2">View all entries - You can only edit/delete your own</p>
        </div>
        
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
          <div class="text-sm text-gray-600 bg-white px-3 py-2 rounded-lg border border-gray-200">
            Logged in as: <span class="font-semibold text-blue-600">{{ currentUser?.name || 'User' }}</span>
          </div>
          
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

      <!-- Stats Cards - Global Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
              Global
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ globalStats.total_forms || 0 }}</p>
            <p class="text-sm text-gray-500 truncate">Total Forms (All Users)</p>
          </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-green-50 rounded-lg">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(globalStats.total_amount_in) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Amount In (All Users)</p>
          </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-orange-50 rounded-lg">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(globalStats.total_fees) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Fees (All Users)</p>
          </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="flex items-start justify-between mb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
              Global
            </div>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(globalStats.total_amount_out) }}</p>
            <p class="text-sm text-gray-500 truncate">Total Amount Out (All Users)</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Search and Filters -->
    <div class="mb-6 bg-white rounded-xl shadow-lg p-4 border border-gray-200">
      <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
          <div class="relative flex-1 md:flex-none">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search all forms..."
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
              Delete My Selected
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

    <!-- Data Table -->
    <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="selectedForms.length === trackerForms.length && trackerForms.length > 0"
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
                @click="sortTable('sales_person')"
              >
                <div class="flex items-center">
                  Sales Person
                  <svg v-if="sortField === 'sales_person'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                Feedback
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Feedback Date
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created By
              </th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="formItem in trackerForms" 
              :key="formItem.id" 
              class="hover:bg-blue-50 transition-all duration-150"
              :class="{ 
                'bg-blue-50': selectedForms.includes(formItem.id),
                'bg-green-50': isOwnForm(formItem)
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
                    <span class="font-semibold text-blue-600">{{ formItem.client_name ? formItem.client_name.charAt(0) : '?' }}</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">
                      {{ formItem.client_name || 'Unnamed' }}
                      <span v-if="isOwnForm(formItem)" class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Yours</span>
                    </div>
                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ formItem.description || 'No description' }}</div>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center mr-3 text-xs font-bold text-gray-600">
                    {{ formItem.sales_person ? formItem.sales_person.charAt(0).toUpperCase() : '?' }}
                  </div>
                  <div class="text-sm font-medium text-gray-900">{{ formItem.sales_person || 'N/A' }}</div>
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
                  {{ formItem.payment_method || 'N/A' }}
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
              
              <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" :title="formItem.feedback">
                {{ formItem.feedback || '-' }}
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formItem.feedback_date ? formatDateForDisplay(formItem.feedback_date) : '-' }}
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                  <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-700 mr-2">
                    {{ getInitials(formItem) }}
                  </div>
                  {{ getUserName(formItem) }}
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <!-- Edit button - only visible for own forms -->
                  <button
                    v-if="isOwnForm(formItem)"
                    @click="openEditModal(formItem)"
                    class="text-blue-600 hover:text-blue-900 p-1.5 hover:bg-blue-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Edit your form"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  
                  <!-- Disabled edit button for non-owners -->
                  <button
                    v-else
                    disabled
                    class="text-gray-400 p-1.5 cursor-not-allowed"
                    title="You can only edit your own forms"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  
                  <!-- Delete button - only visible for own forms -->
                  <button
                    v-if="isOwnForm(formItem)"
                    @click="deleteTrackerForm(formItem.id)"
                    class="text-red-600 hover:text-red-900 p-1.5 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Delete your form"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  
                  <!-- Disabled delete button for non-owners -->
                  <button
                    v-else
                    disabled
                    class="text-gray-400 p-1.5 cursor-not-allowed"
                    title="You can only delete your own forms"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  
                  <!-- Mark as completed button - only for owners -->
                  <button
                    v-if="!formItem.feedback && isOwnForm(formItem)"
                    @click="markAsCompleted(formItem)"
                    class="text-green-600 hover:text-green-900 p-1.5 hover:bg-green-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Add Feedback"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  
                  <!-- Completed badge -->
                  <span
                    v-else-if="formItem.feedback"
                    class="text-green-600 text-xs font-medium px-2 py-1 bg-green-50 rounded-full"
                  >
                    ✓ Completed
                  </span>
                </div>
              </td>
            </tr>
            <tr v-if="trackerForms.length === 0">
              <td colspan="12" class="px-6 py-12 text-center">
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
            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, totalRecords) }}</span> of 
            <span class="font-medium">{{ totalRecords }}</span> results
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

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
              {{ isEditing ? 'Edit Your Form' : 'Add New Form' }}
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person *</label>
                <input
                  v-model="form.sales_person"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                  placeholder="Enter sales person name"
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
                    v-model.number="form.amount_in"
                    type="number"
                    step="0.01"
                    min="0"
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
                    v-model.number="form.fees"
                    type="number"
                    step="0.01"
                    min="0"
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

            <div v-if="isEditing">
              <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
              <textarea
                v-model="form.feedback"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                placeholder="Enter feedback"
              ></textarea>
            </div>

            <div v-if="isEditing">
              <label class="block text-sm font-medium text-gray-700 mb-2">Feedback Date</label>
              <input
                v-model="form.feedback_date"
                type="date"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
              />
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

    <!-- Database Status -->
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
            <span class="text-sm font-medium text-blue-600">{{ totalRecords }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Your Records:</span>
            <span class="text-sm font-medium text-green-600">{{ myRecordsCount }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Last Sync:</span>
            <span class="text-sm text-gray-500">{{ lastSync }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-8 text-center text-sm text-gray-500">
      <p>© {{ new Date().getFullYear() }} Web Tracker Dashboard. All rights reserved.</p>
      <p class="mt-1 text-xs">You can view all entries, but only edit/delete your own.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onBeforeUnmount } from 'vue';
import api from '../services/api'; 
import { useRouter } from 'vue-router';

const router = useRouter();

// --- STATE ---
const trackerForms = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const showModal = ref(false);
const isEditing = ref(false);

// Current user - improved initialization
const currentUser = ref(null);
const isUserLoaded = ref(false);

// Pagination
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(25);
const totalPages = ref(1);
const totalRecords = ref(0);

// Sorting & Filtering
const sortField = ref('date');
const sortDirection = ref('desc');
const selectedForms = ref([]);
const showFilters = ref(false);
const filterStatus = ref('all');
const filterPaymentMethod = ref('all');
const filterDateFrom = ref('');
const filterDateTo = ref('');

// System
const showDbStatus = ref(true);
const lastSync = ref('Just now');
let autoRefreshInterval = null;

const form = ref({
  id: null,
  client_name: '',
  sales_person: '',
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
let searchTimeout = null;

// --- COMPUTED ---
const myRecordsCount = computed(() => {
  if (!trackerForms.value) return 0;
  return trackerForms.value.filter(item => isOwnForm(item)).length;
});

// Check if user owns a form - improved with multiple fallbacks
const isOwnForm = (formItem) => {
  if (!currentUser.value || !currentUser.value.id) {
    console.log('No current user or user ID');
    return false;
  }
  
  if (!formItem) {
    console.log('No form item');
    return false;
  }
  
  // Convert both to numbers for reliable comparison
  const userId = Number(currentUser.value.id);
  
  // Check various possible user ID fields in the form data
  let formUserId = null;
  
  if (formItem.user_id !== undefined && formItem.user_id !== null) {
    formUserId = Number(formItem.user_id);
  } else if (formItem.user && formItem.user.id !== undefined && formItem.user.id !== null) {
    formUserId = Number(formItem.user.id);
  } else if (formItem.userId !== undefined && formItem.userId !== null) {
    formUserId = Number(formItem.userId);
  }
  
  console.log('Ownership check:', { userId, formUserId, isOwn: userId === formUserId });
  
  return userId === formUserId;
};

// Helper to get user initials
const getInitials = (formItem) => {
  if (formItem.user && formItem.user.name) {
    return formItem.user.name.charAt(0).toUpperCase();
  }
  if (formItem.user_id) {
    return 'U';
  }
  return '?';
};

// Helper to get user name
const getUserName = (formItem) => {
  if (formItem.user && formItem.user.name) {
    return formItem.user.name;
  }
  if (formItem.user_id) {
    return `User #${formItem.user_id}`;
  }
  return 'Unknown';
};

// --- GLOBAL STATS ---
const globalStats = ref({
  total_forms: 0,
  total_amount_in: 0,
  total_fees: 0,
  total_amount_out: 0
});

// --- USER LOADING FUNCTION ---
const loadUserData = () => {
  try {
    const userStr = localStorage.getItem('user');
    console.log('Raw user data from storage:', userStr);
    
    if (!userStr || userStr === '{}') {
      console.error('No user data found in localStorage');
      router.push('/');
      return false;
    }
    
    const user = JSON.parse(userStr);
    console.log('Parsed user data:', user);
    
    if (!user || !user.id) {
      console.error('Invalid user data structure');
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      router.push('/');
      return false;
    }
    
    currentUser.value = user;
    isUserLoaded.value = true;
    return true;
  } catch (e) {
    console.error('Failed to load user data:', e);
    router.push('/');
    return false;
  }
};

// --- API CALLS ---
const fetchGlobalStats = async () => {
  try {
    const response = await api.getStats(); 
    if (response.data.success) {
      globalStats.value = response.data.data;
    }
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

const fetchTrackerForms = async () => {
  if (!isUserLoaded.value) {
    console.log('User not loaded yet, skipping fetch');
    return;
  }
  
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
    
    console.log('Fetching forms with params:', params);
    
    const response = await api.getTrackerForms(params);
    
    console.log('Forms response:', response.data);
    
    trackerForms.value = response.data.data || [];

    if (response.data.meta) {
        totalPages.value = response.data.meta.last_page || 1;
        totalRecords.value = response.data.meta.total || 0;
    }
    
    lastSync.value = new Date().toLocaleTimeString();
    
    // Debug ownership
    if (trackerForms.value.length > 0) {
      console.log('First form ownership check:', {
        userId: currentUser.value?.id,
        formUserId: trackerForms.value[0].user_id,
        isOwn: isOwnForm(trackerForms.value[0])
      });
    }
    
  } catch (error) {
    console.error('Fetch forms error:', error);
    errorMessage.value = 'Failed to fetch forms.';
  } finally {
    loading.value = false;
  }
};

// --- MODAL & FORM LOGIC ---
const openAddModal = () => {
  isEditing.value = false;
  resetForm();
  showModal.value = true;
};

const openEditModal = (item) => {
  if (!isOwnForm(item)) {
    errorMessage.value = 'You can only edit your own forms.';
    return;
  }
  isEditing.value = true;
  form.value = { ...item };
  form.value.date = item.date ? item.date.split('T')[0] : '';
  form.value.feedback_date = item.feedback_date ? item.feedback_date.split('T')[0] : '';
  showModal.value = true;
};

const calculateNetAmount = () => {
  const amountIn = parseFloat(form.value.amount_in) || 0;
  const fees = parseFloat(form.value.fees) || 0;
  form.value.amount_out = (amountIn - fees).toFixed(2);
};

const resetForm = () => {
  const today = new Date().toISOString().split('T')[0];
  form.value = {
    id: null,
    client_name: '',
    sales_person: '',
    date: today,
    payment_method: '',
    description: '',
    amount_in: '',
    fees: '',
    amount_out: '',
    feedback: '',
    feedback_date: ''
  };
};

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    if (isEditing.value) {
      await api.updateTrackerForm(form.value.id, form.value);
      successMessage.value = 'Updated successfully!';
    } else {
      await api.createTrackerForm(form.value);
      successMessage.value = 'Created successfully!';
    }
    
    await fetchTrackerForms(); 
    await fetchGlobalStats(); 
    
    showModal.value = false;
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    console.error('Submit error:', error);
    if (error.response?.status === 403) {
      errorMessage.value = 'You can only edit your own forms.';
    } else {
      errorMessage.value = 'Operation failed.';
    }
  }
};

// --- ACTIONS ---
const deleteTrackerForm = async (id) => {
  if (!confirm('Are you sure you want to delete this form?')) return;
  try {
    await api.deleteTrackerForm(id);
    await fetchTrackerForms();
    await fetchGlobalStats();
    successMessage.value = 'Deleted successfully!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (e) {
    console.error('Delete error:', e);
    if (e.response?.status === 403) {
      errorMessage.value = 'You can only delete your own forms.';
    } else {
      errorMessage.value = 'Delete failed.';
    }
  }
};

const bulkDelete = async () => {
  if (!selectedForms.value.length) return;
  
  const mySelectedForms = trackerForms.value
    .filter(item => selectedForms.value.includes(item.id) && isOwnForm(item))
    .map(item => item.id);
  
  if (mySelectedForms.length === 0) {
    errorMessage.value = 'None of the selected forms belong to you.';
    return;
  }
  
  if (!confirm(`Delete ${mySelectedForms.length} of your form(s)?`)) return;
  
  try {
    const response = await api.bulkDeleteForms(selectedForms.value);
    selectedForms.value = [];
    await fetchTrackerForms();
    await fetchGlobalStats();
    
    if (response.data.skipped > 0) {
      successMessage.value = `${response.data.deleted} deleted, ${response.data.skipped} skipped (not yours)`;
    } else {
      successMessage.value = 'Bulk delete successful!';
    }
    
    setTimeout(() => successMessage.value = '', 3000);
  } catch (e) {
    console.error('Bulk delete error:', e);
    if (e.response?.status === 403) {
      errorMessage.value = 'You can only delete your own forms.';
    } else {
      errorMessage.value = 'Bulk delete failed.';
    }
  }
};

const markAsCompleted = async (item) => {
  if (!isOwnForm(item)) {
    errorMessage.value = 'You can only update your own forms.';
    return;
  }
  
  const feedback = prompt('Enter feedback:', item.feedback || '');
  if (feedback === null) return;
  if (!feedback.trim()) { 
    errorMessage.value = 'Feedback required'; 
    return; 
  }

  try {
    await api.markAsCompleted(item.id, { 
        feedback: feedback, 
        feedback_date: new Date().toISOString().split('T')[0] 
    });
    await fetchTrackerForms();
    successMessage.value = 'Marked as completed!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (e) {
    console.error('Mark as completed error:', e);
    errorMessage.value = 'Failed to update.';
  }
};

const exportToCSV = async () => {
    const params = {
        search: searchQuery.value,
        status: filterStatus.value !== 'all' ? filterStatus.value : undefined,
        payment_method: filterPaymentMethod.value !== 'all' ? filterPaymentMethod.value : undefined,
        date_from: filterDateFrom.value,
        date_to: filterDateTo.value
    };
    try {
        const response = await api.exportToCSV(params);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `tracker-${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        successMessage.value = 'Export successful!';
        setTimeout(() => successMessage.value = '', 3000);
    } catch (e) {
        console.error('Export error:', e);
        errorMessage.value = 'Export failed.';
    }
};

const handleLogout = async () => {
  try {
    await api.logout();
  } catch (e) {
    console.error('Logout error:', e);
  } finally {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/');
  }
};

// --- HELPER FUNCTIONS ---
const formatDateForDisplay = (dateStr) => {
  if (!dateStr) return '-';
  try {
    return new Date(dateStr).toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric', 
      year: 'numeric' 
    });
  } catch {
    return '-';
  }
};

const formatCurrency = (amount) => {
  if (amount === undefined || amount === null) return '-';
  return new Intl.NumberFormat('en-US', { 
    style: 'currency', 
    currency: 'KES',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount);
};

// --- PAGINATION & SORTING ---
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchTrackerForms();
  }
};

const sortTable = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  fetchTrackerForms();
};

const resetFilters = () => {
  searchQuery.value = '';
  filterStatus.value = 'all';
  filterPaymentMethod.value = 'all';
  filterDateFrom.value = '';
  filterDateTo.value = '';
  currentPage.value = 1;
  fetchTrackerForms();
};

const applyFilters = () => {
  currentPage.value = 1;
  fetchTrackerForms();
};

const onSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchTrackerForms();
  }, 500);
};

const toggleSelectAll = () => {
  if (!trackerForms.value.length) return;
  
  if (selectedForms.value.length === trackerForms.value.length) {
    selectedForms.value = [];
  } else {
    selectedForms.value = trackerForms.value.map(f => f.id);
  }
};

const toggleSelectForm = (id) => {
  const idx = selectedForms.value.indexOf(id);
  if (idx > -1) selectedForms.value.splice(idx, 1);
  else selectedForms.value.push(id);
};

const getPaginationRange = () => {
  if (!totalPages.value) return [1];
  
  const range = [];
  const delta = 2;
  
  for (let i = Math.max(2, currentPage.value - delta); i <= Math.min(totalPages.value - 1, currentPage.value + delta); i++) {
    range.push(i);
  }
  
  if (currentPage.value - delta > 2) range.unshift('...');
  if (currentPage.value + delta < totalPages.value - 1) range.push('...');
  
  range.unshift(1);
  if (totalPages.value > 1) range.push(totalPages.value);
  
  return range;
};

// --- DEBUG FUNCTION ---
const debugAuth = () => {
  console.log('=== AUTH DEBUG ===');
  console.log('Token:', localStorage.getItem('token'));
  console.log('User:', currentUser.value);
  console.log('First form:', trackerForms.value[0]);
  console.log('Is own form check:', isOwnForm(trackerForms.value[0]));
  console.log('=================');
};

// Call this after fetching forms
const debugFormData = () => {
  if (trackerForms.value.length > 0) {
    console.log('Debug - Form ownership check:');
    console.log('Current user ID:', currentUser.value?.id);
    console.log('Form user_id:', trackerForms.value[0]?.user_id);
    console.log('Form user object:', trackerForms.value[0]?.user);
    console.log('Is own form:', isOwnForm(trackerForms.value[0]));
  }
};

// --- INITIALIZATION ---
onMounted(() => {
  console.log('Dashboard mounted');
  if (loadUserData()) {
    console.log('User data loaded, fetching forms...');
    fetchTrackerForms();
    fetchGlobalStats();
    
    // Auto refresh every 30 seconds
    autoRefreshInterval = setInterval(() => { 
      if (!showModal.value && isUserLoaded.value) { 
        console.log('Auto-refreshing data...');
        fetchTrackerForms(); 
        fetchGlobalStats(); 
      } 
    }, 30000);
    
    // Hide DB status after 10 seconds
    setTimeout(() => showDbStatus.value = false, 10000);
  }
});

// Clean up on unmount
onBeforeUnmount(() => {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
  }
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
});
</script>

<style scoped>
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
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

input, select, textarea, button {
  transition: all 0.2s ease-in-out;
}

input:focus, select:focus, textarea:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

tr {
  transition: all 0.15s ease-in-out;
}

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

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

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

.hover-scale {
  transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
  transform: scale(1.05);
}

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

.progress-bar {
  transition: width 1s ease-in-out;
}
</style>