<template>
  <div class="col-span-12 mt-5">
    <div class="tabs">
      <a
        v-for="(item, index) in tabs.items"
        :key="index"
        :class="{
          'tab tab-lifted': true,
          'tab-active': item.key === tabs.currentActive,
        }"
        style="font-weight: bold"
        @click="methods.onTab(item.key)"
      >
        <div v-if="item.icon">
          <div v-if="item.icon.position === 'icon-text'">
            <span v-if="item.icon" v-html="item.icon.value"></span>&nbsp;
            {{ item.name }}
          </div>
          <div v-if="item.icon.position === 'text-icon'">
            {{ item.name }}
            &nbsp;<span v-if="item.icon" v-html="item.icon.value"></span>
          </div>
        </div>
        <div v-else>
          {{ item.name }}
        </div>
      </a>
    </div>
    <div class="mt-5">
      <div v-for="(item, index) in tabs.items" :key="index">
        <div :class="{ hidden: item.key !== tabs.currentActive }">
          <Detail :is-loading="isLoading" :multi-key="item.key" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import Detail from './Detail.vue'
  import { inject, onMounted, reactive, defineProps } from 'vue'
  import { UppercaseFirst } from '~/services'
  import { FormDetail } from '~/types/form/detail'

  defineProps({
    isLoading: {
      type: Boolean,
      default: false,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }

  const tabs = reactive<{
    currentActive: string
    items: Array<{
      active: boolean
      name: string
      key: string
      icon: { value: string; position: 'text-icon' | 'icon-text' }
    }>
  }>({
    currentActive: '',
    items: [],
  })

  const methods = {
    onTab: (key: string) => {
      tabs.currentActive = key
    },
  }

  onMounted(() => {
    if (temp.form.multiTable && temp.form.multiTable.active) {
      temp.form.multiTable.tabs.forEach((tab, tabI) => {
        if (!tabI) {
          tabs.currentActive = tab.key
        }
        tabs.items.push({
          active: !tabI,
          name: tab.name ? tab.name : UppercaseFirst(tab.key),
          key: tab.key,
          icon: tab.icon || null,
        })
      })
    }
  })
</script>
