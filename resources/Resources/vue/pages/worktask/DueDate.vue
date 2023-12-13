<script setup lang="ts">
  import {
    computed,
    inject,
    reactive,
    defineEmits,
    defineProps,
    ref,
    onMounted,
  } from 'vue'
  import moment from 'moment'
  import { Axios, Flatpickr } from '~/services'
  import { useTempsStore } from '~/store/temps'

  const props = defineProps({
    modelValue: {
      type: String,
      default: '',
    },
  })

  const init = ref()
  const modalData = inject('modalData') as any
  const items = inject('items') as any
  const emit = defineEmits(['update:modelValue'])
  const temp = reactive({
    value_: '',
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
  })

  if (temp.value) {
    temp.value_ = moment(temp.value, 'YYYY-MM-DD HH:mm').format(
      'D MMM YYYY HH:mm',
    )
  } else {
    temp.value_ = moment(new Date()).format('D MMM YYYY HH:mm')
  }

  const tempsStore = useTempsStore()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  onMounted(() => {
    setTimeout(() => {
      init.value = Flatpickr(
        `.flatpickr-${modalData.currentData.id}-datetime`,
        {
          enableTime: true,
          time_24hr: true,
          // https://flatpickr.js.org/formatting/
          dateFormat: 'd M Y H:i',
          defaultDate: temp.value_,
          onChange(_, dateStr) {
            const due_date = moment(dateStr, 'D MMM YYYY HH:mm').format(
              'YYYY-MM-DD HH:mm:ss',
            )
            Axios({
              method: 'PUT',
              url: `/api/v1/${table_prefix}t_backlog_d_stories/${modalData.currentData.id}`,
              data: {
                due_date,
                awk_t_backlog_id: modalData.currentData.awk_t_backlog_id,
                user_story_title: modalData.currentData.user_story_title,
              },
            }).then(() => {
              const { categoryIndex, itemIndex } = modalData.currentDataDetail
              items.value[categoryIndex].items[itemIndex].due_date = due_date
            })
          },
          onOpen() {
            tempsStore.setIgnoreClickOutsideModal(true)
          },
          onClose() {
            setTimeout(() => {
              tempsStore.setIgnoreClickOutsideModal(false)
            }, 100)
          },
        },
      )
    }, 500)
  })
</script>
<template>
  <div class="flex flex-col">
    <label
      :for="`flatpickr-${modalData.currentData.id}-datetime-id`"
      class="text-xs uppercase text-gray-400 font-bold"
    >
      Due Date
    </label>
    <input
      :id="`flatpickr-${modalData.currentData.id}-datetime-id`"
      :class="{
        [`flatpickr-${modalData.currentData.id}-datetime`]: true,
      }"
      class="focus:ring-0 focus:outline-none text-[11px] cursor-pointer dark:bg-base-100 dark:text-white"
    />
  </div>
</template>
