<template>
  <div>
    <vue-good-table-custom
      :columns="
        multiKey ? temp.form.properties[multiKey] : temp.form.properties
      "
      :rows="temp.form.rows"
      :is-loading="!isReady || isLoading"
      :max-height="temp.form.tableConfigs?.maxHeight"
      :fixed-header="
        temp.form.tableConfigs?.fixedHeader ? clientWidth >= 1366 : false
      "
      :line-numbers="temp.form.tableConfigs?.lineNumbers || false"
    >
      <template #table-row="props">
        <DetailSubCore
          :row-id="props.row.id"
          :column="props.column"
          :row-index="props.row.originalIndex"
          :formatted-row="props.formattedRow"
        />
      </template>
    </vue-good-table-custom>
  </div>
</template>

<script setup lang="ts">
  import { defineProps, inject, Ref, UnwrapRef } from 'vue'
  import DetailSubCore from './DetailSubCore.vue'
  import { FormDetail } from '~/types/form/detail'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  defineProps({
    isLoading: {
      type: Boolean,
      default: false,
    },
    multiKey: {
      type: String,
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')
  const isReady = inject('isReady') as Ref<UnwrapRef<boolean>>
  const temp = inject('temp') as {
    form: FormDetail
  }
</script>
