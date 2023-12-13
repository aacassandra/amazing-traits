<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 flex flex-col justify-center my-auto">
        <div class="ft-inter-700 text-2xl">Dashboard</div>
        <div class="ft-inter-500 text-base text-gray-500">
          {{ $t('global.welcome') }}
          <span class="text-blue-400"> {{ authStore.user?.name }} </span>,
          {{ $t('dashboard.everything_looks') }}
        </div>
      </div>
    </div>
    <AdminDashboard v-if="authStore.user?.role === 'ADMIN'" />
    <InternalAdminDashboard
      v-if="
        authStore.user?.role === 'SUPERADMIN' ||
        authStore.user?.role === 'INTERNAL - ADMIN'
      "
    />
  </div>
</template>
<script setup lang="ts">
  import { defineComponent, inject, onMounted, reactive } from 'vue'
  import { useAuthStore } from '~/store/auth'
  import { BarChart, PieChart } from '~/components/atoms/charts'
  import { Axios } from '~/services'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  import AdminDashboard from '~/components/molecules/dashboard/Admin.vue'
  import InternalAdminDashboard from '~/components/molecules/dashboard/InternalAdmin.vue'

  defineComponent({
    name: 'IndexPage',
  })

  const theme = inject('theme')
  const authStore = useAuthStore()
</script>
