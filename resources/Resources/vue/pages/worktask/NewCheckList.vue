<script setup lang="ts">
  import { inject } from 'vue'
  import { Axios } from '~/services'

  const modalData = inject('modalData') as any
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX

  const onAddNewCheckList = () => {
    Axios({
      method: 'POST',
      url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs`,
      data: {
        [`${table_prefix}t_backlog_d_stories_id`]: modalData.currentData.id,
        title: 'CHECKLIST',
      },
    }).then((res: any) => {
      modalData.currentData[`${table_prefix}t_backlog_d_stories_s_acs`].push({
        id: res.data.data.id,
        title: 'CHECKLIST',
        awk_t_backlog_d_stories_s_acs_d_values: [],
      })
    })
  }
</script>

<template>
  <div
    class="text-gray-5 text-sm flex items-center cursor-pointer hover:text-primary transition duration-200"
    @click="onAddNewCheckList"
  >
    <b class="uppercase"><i class="fa-regular fa-plus"></i> Add Checklist</b>
  </div>
</template>
