<template>
  <div>
    <vue-good-table-custom
      :columns="multiKey ? properties[multiKey] : properties"
      :rows="tempVal"
      :is-loading="!isReady"
      :max-height="form.options.tableConfigs?.maxHeight"
      :fixed-header="fixHeader"
      :line-numbers="form.options.tableConfigs?.lineNumbers || false"
    >
      <template #table-row="props">
        <SubDetailSubCore
          :column="props.column"
          :row-id="props.row.id"
          :row-index="props.row.originalIndex"
          :formatted-row="props.formattedRow"
        />
      </template>
    </vue-good-table-custom>
  </div>
</template>

<script setup lang="ts">
  import { defineProps, inject, Ref, ref, UnwrapRef } from 'vue'
  import SubDetailSubCore from './SubDetailSubCore.vue'
  import { FormDetailProperties } from '~/types/form/detail'
  import { FormSubDetail } from '~/types/form/subdetail'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  defineProps({
    isReady: {
      type: Boolean,
      default: false,
    },
    multiKey: {
      type: String,
      default: null,
    },
  })

  const fixHeader = ref(false)
  const properties = inject('properties') as FormDetailProperties
  const form = inject('form') as FormSubDetail
  const tempVal = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  setTimeout(() => {
    if (form.options.tableConfigs?.fixedHeader) {
      fixHeader.value = true
    }
  }, 500)
</script>
