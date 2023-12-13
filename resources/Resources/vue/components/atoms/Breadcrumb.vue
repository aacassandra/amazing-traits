<template>
  <div class="text-sm breadcrumbs">
    <ul>
      <li v-for="(item, i) in data" :key="i">
        <router-link
          v-if="item.type === 'text-link'"
          :to="item.route"
          class="flex"
        >
          <div v-if="item.icon" class="mr-2">
            <span v-html="item.icon"></span>
          </div>
          <div>
            {{ item.lang ? $t(item.lang) : item.text }}
          </div>
        </router-link>
        <div v-else-if="item.type === 'text'" class="flex">
          <div v-if="item.icon" class="mr-2">
            <span v-html="item.icon"></span>
          </div>
          <div>
            {{ item.lang ? $t(item.lang) : item.text }}
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import { Breadcrumb } from '~/types/components/atoms/breadcrumb'

  defineComponent({
    name: 'Breadcrumb',
  })

  defineProps({
    data: {
      type: Array as () => Array<Breadcrumb>,
      required: true,
    },
  })
</script>
