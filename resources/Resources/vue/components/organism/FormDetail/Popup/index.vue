<template>
  <button
    :disabled="disabled"
    class="btn btn-sm btn-primary border-0"
    :class="options.className.addNewList"
    style="text-transform: unset"
    @click="methods.onShowModal"
  >
    {{ $t('global.add_new_list') }}
  </button>

  <Modal
    v-model="temp.showModal"
    width="w-11/12 max-w-5xl"
    :click-outside-disabled="temp.modalLock"
  >
    <template #modal-title>
      {{
        options.isMultiple
          ? options.tableConfigs?.selectAll
            ? $t('global.select_all_rows')
            : $t('global.select_multiple_rows')
          : $t('global.select_one_row')
      }}
    </template>
    <template #modal-body>
      <Table
        v-if="temp.showModal"
        :rows="rows"
        :options="options"
        :on-verified="onVerified"
      />
    </template>
  </Modal>
</template>

<script setup lang="ts">
  import { defineComponent, provide, reactive, defineProps } from 'vue'
  import Table from './Table.vue'
  import Modal from '~/components/atoms/Modal.vue'
  import { AddRowFromPopupOptions } from '~/types/form/detail'

  defineComponent({
    name: 'Popupfield',
  })

  const props = defineProps({
    disabled: {
      type: Boolean,
      default: false,
    },
    rows: {
      type: Array,
      required: true,
    },
    onClick: {
      type: Function,
      default: null,
    },
    onVerified: {
      type: Function,
      default: null,
    },
    options: {
      type: Object as () => AddRowFromPopupOptions,
      required: true,
      default: () => {
        return {
          isMultiple: false,
          globalSearch: false,
          className: {
            addNewList: '',
            verivy: '',
          },
          tableConfigs: {
            fixedHeader: true,
            selectAll: false,
          },
          uniqueColumn: '',
          includes: [],
          columns: [],
          api: {
            model: '',
            root: '',
            parameters: {},
            overrideParams: () => {
              //
            },
            cache: false,
          },
        }
      },
    },
  })

  const temp = reactive({
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    showModal: false,
    modalLock: false,
  })

  provide('popupTemp', temp)

  const methods = {
    onShowModal: () => {
      if (props.onClick) {
        const valid = props.onClick()
        if (!valid) {
          return false
        }
      }

      if (props.options.api) {
        if (!props.options.api) {
          return
        }
        temp.showModal = true
      }
    },
    onCloseModal: () => {
      if (temp.showModal) {
        if (!props.options.api) {
          return
        }

        temp.showModal = false
      }
    },
  }
</script>
