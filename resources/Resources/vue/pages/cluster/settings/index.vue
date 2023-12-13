<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
    </div>

    <Card>
      <template #card-header>
        <div class="flex">
          <div class="fle-grow-0">
            <div class="flex relative items-center">
              <div
                class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1"
              >
                {{ $t(pageConfigs.title) }}
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #card-body>
        <div class="pl-4">
          <div class="tabs">
            <a
              :class="{
                'tab tab-bordered': true,
                'tab-active': activeTab === 'general',
              }"
              @click="() => (activeTab = 'general')"
            >
              <i class="fa-light fa-gears mr-2"></i> General
            </a>
            <a
              :class="{
                'tab tab-bordered': true,
                'tab-active': activeTab === 'imports',
              }"
              @click="() => (activeTab = 'imports')"
            >
              <i class="fa-light fa-file-import mr-2"></i> Imports
            </a>
          </div>
        </div>
        <div v-if="activeTab === 'general'" class="pt-5">
          <General />
        </div>
        <div v-else class="pt-5 pl-4">
          <Imports />
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, provide, reactive, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import { useI18n } from 'vue-i18n'
  import General from './general.vue'
  import Imports from './imports.vue'
  import { Axios } from '~/services'

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const pageConfigs = reactive<PageConfigs>({
    breadcrumb: [
      {
        type: 'text-link',
        route: '/dashboard',
        icon: '<i class="fal fa-home-alt"></i>',
        text: 'Dashboard',
      },
      {
        type: 'text',
        text: 'Cluster',
        lang: 'menu.cluster',
      },
      {
        type: 'text',
        text: 'Settings',
        lang: 'menu.settings',
      },
    ],
    title: 'menu.settings',
    model: table_prefix + 'm_clusters',
    mode: 'edit',
    tabs: [
      {
        key: 'tab1',
        label: 'Patrols',
        icon: '<i class="fal fa-map-location-dot"></i>',
        active: true,
      },
      {
        key: 'tab2',
        label: 'Office',
        icon: '<i class="fal fa-building-user"></i>',
      },
      {
        key: 'tab3',
        label: 'Schedules',
        icon: '<i class="fa-light fa-user-clock"></i>',
      },
      {
        key: 'tab4',
        label: 'Salaries',
        icon: '<i class="fa-light fa-money-check-dollar-pen"></i>',
      },
      {
        key: 'tab5',
        label: 'Incentives',
        icon: '<i class="fa-light fa-hands-holding-dollar"></i>',
      },
      {
        key: 'tab6',
        label: 'Weighting',
        icon: '<i class="fa-light fa-scale-unbalanced-flip"></i>',
      },
      {
        key: 'tab7',
        label: 'Whatsapp Group',
        icon: '<i class="fa-brands fa-whatsapp"></i>',
      },
      {
        key: 'tab8',
        label: 'Administrator',
        icon: '<i class="fa-light fa-users"></i>',
      },
      {
        key: 'tab9',
        label: 'Scheduler',
        icon: '<i class="fa-light fa-calendar-clock"></i>',
      },
    ],
  })
  provide('pageConfigs', pageConfigs)

  defineComponent({
    name: 'ClusterSettingsForm',
  })

  const activeTab = ref('general')
  const bot_phone_number = ref('')
  provide('bot_phone_number', bot_phone_number)

  Axios({
    method: 'GET',
    url: '/api/v1/ihm_m_general',
    params: {
      where: "`group` = 'BOT PHONE NUMBER'",
    },
  }).then((res: any) => {
    if (res.data.data.length) {
      bot_phone_number.value = res.data.data[0].value_1
    }
  })
</script>
