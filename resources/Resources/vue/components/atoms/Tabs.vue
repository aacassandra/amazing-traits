<template>
  <div>
    <div class="container px-4 mx-auto">
      <div class="grid grid-cols-12">
        <div class="col-span-12">
          <div class="tabs">
            <a
              v-for="(tab, tabI) in inItems"
              :key="`icon-${tab.key}-${tabI}`"
              :class="{ [`tab tab-${theme}`]: true, 'tab-active': tab.active }"
              @click="onClick(tab.key)"
            >
              <span v-if="tab.icon" class="mr-1" v-html="tab.icon"></span>
              <b>{{ tab.label }}</b>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="pt-3">
      <div
        v-for="(tab, tabI) in inItems"
        :key="`slot-${tab.key}-${tabI}`"
        :class="{ hidden: !tab.active }"
      >
        <slot :name="tab.key" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { TabsType } from '~/types/components/molecules/tabs'
import { ref } from '#imports'

const props = defineProps({
  items: {
    type: Array as () => TabsType,
    required: true
  },
  theme: {
    type: String as () => 'lifted' | 'bordered',
    default: 'lifted'
  },
  changeTab: {
    type: Function,
    default: null
  }
})

const inItems = ref(props.items)

const onClick = (key) => {
  inItems.value.forEach((item, itemI) => {
    inItems.value[itemI].active = false
  })

  inItems.value.forEach((item, itemI) => {
    if (item.key === key) {
      inItems.value[itemI].active = true
      if (props.changeTab) {
        props.changeTab(key)
      }
    }
  })
}
</script>
