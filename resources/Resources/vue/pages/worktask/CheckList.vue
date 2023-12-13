<script setup lang="ts">
  import { computed, defineProps, inject, reactive, ref } from 'vue'
  import draggable from 'vuedraggable'
  import { useTempsStore } from '~/store/temps'
  import { onClickOutside, useElementBounding } from '@vueuse/core'
  import { Axios, Cryptor } from '~/services'

  const props = defineProps({
    itemIndex: {
      type: [Number, String],
      required: true,
    },
  })

  const drag = ref(false)
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const modalData = inject('modalData') as any
  const dropdownMenuIsOpen = ref(false)
  const newItem = ref('')
  const item = reactive({
    value: computed({
      get: () =>
        modalData.currentData[`${table_prefix}t_backlog_d_stories_s_acs`][
          props.itemIndex
        ],
      set: (val) =>
        modalData.currentData[`${table_prefix}t_backlog_d_stories_s_acs`][
          props.itemIndex
        ],
    }),
  })
  const hasChecked = ref(0)
  const headFocused = ref(false)
  const headIsDeleted = ref(false)
  const tempsStore = useTempsStore()
  const div = document.createElement('div')
  document.body.appendChild(div)

  onClickOutside(div, () => {
    if (dropdownMenuIsOpen.value) {
      methods.onCloseMenu()
    }
  })

  item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`].forEach(
    (dt) => {
      if (dt.checked) {
        hasChecked.value = hasChecked.value + 1
      }
    },
  )
  const methods = {
    onHeadChange: () => {
      Axios({
        method: 'PUT',
        url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs/${item.value.id}`,
        data: {
          title: item.value.title,
        },
      })
    },
    onChange: (element, index) => {
      const dt =
        item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`][index]
      if (dt.checked) {
        hasChecked.value = hasChecked.value + 1
      } else {
        hasChecked.value = hasChecked.value - 1
      }

      Axios({
        method: 'PUT',
        url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs_d_values/${element.id}`,
        data: dt,
      })
    },
    onItemMouseOver: (index) => {
      item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`][
        index
      ].focus = true
    },
    onItemMouseLeave: (index) => {
      item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`][
        index
      ].focus = false
    },
    onAddNewItem: () => {
      document.getElementById(`${item.value.id}-input-newitem`).focus()
    },
    onAddNewItemReal: () => {
      if (newItem.value) {
        Axios({
          method: 'POST',
          url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs_d_values`,
          data: {
            [`${table_prefix}t_backlog_d_stories_s_acs_id`]: item.value.id,
            checked: false,
            value: newItem.value,
            order:
              item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`]
                .length + 1,
            fixed: false,
          },
        }).then(() => {
          item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`].push({
            checked: false,
            focus: false,
            value: newItem.value,
            order:
              item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`]
                .length + 1,
            fixed: false,
          })

          newItem.value = ''
        })
      }
    },
    onRemove: (order) => {
      item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`].forEach(
        (dt, index) => {
          if (dt.order === order) {
            Axios({
              method: 'DELETE',
              url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs_d_values/${dt.id}`,
            }).then(() => {
              item.value[
                `${table_prefix}t_backlog_d_stories_s_acs_d_values`
              ].splice(index, 1)
            })
          }
        },
      )
      methods.onCloseMenu()
    },
    onDuplicate: (order) => {
      item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`].forEach(
        (dt) => {
          if (dt.order === order) {
            Axios({
              method: 'POST',
              url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs_d_values`,
              data: {
                [`${table_prefix}t_backlog_d_stories_s_acs_id`]: item.value.id,
                checked: dt.checked,
                value: dt.value,
                order:
                  item.value[
                    `${table_prefix}t_backlog_d_stories_s_acs_d_values`
                  ].length + 1,
                fixed: false,
              },
            }).then(() => {
              item.value[
                `${table_prefix}t_backlog_d_stories_s_acs_d_values`
              ].push({
                checked: dt.checked,
                value: dt.value,
                focus: false,
                order:
                  item.value[
                    `${table_prefix}t_backlog_d_stories_s_acs_d_values`
                  ].length + 1,
                fixed: false,
              })
            })
          }
        },
      )
      methods.onCloseMenu()
    },
    onFocus: (elId, order) => {
      document.getElementById(elId + '-input-' + order).focus()
      methods.onCloseMenu()
    },
    onCloseMenu: () => {
      div.innerHTML = '<div></div>'
      dropdownMenuIsOpen.value = false
      setTimeout(() => {
        tempsStore.setIgnoreClickOutsideModal(false)
      }, 100)
    },
    onToggleHeadMenu: () => {
      dropdownMenuIsOpen.value = true

      const id = document.getElementById(`${item.value.id}-menu`)
      const { top, left } = useElementBounding(id)
      const style = `top: ${top.value + 35}px; left: ${
        left.value - 160
      }px; height: auto; width: 192px; z-index: 9999
      `
      div.innerHTML = `<div class="fixed border dark:border-gray-500 rounded-xl shadow-lg" style="${style}">
            <div id="lm-btn-rename-head" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2 rounded-t-xl">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-pencil"></i>
                </div>
                <div class="flex-grow">
                    Rename
                </div>
            </div>
            <div id="lm-btn-delete-head" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-trash"></i>
                </div>
                <div class="flex-grow">
                    Delete
                </div>
            </div>
            <div id="lm-btn-close-head" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2 rounded-b-xl">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-circle-xmark"></i>
                </div>
                <div class="flex-grow">
                    Close
                </div>
            </div>
        </div>`

      document
        .getElementById('lm-btn-rename-head')
        .addEventListener('click', () => {
          document.getElementById(`${item.value.id}-input`).focus()
          methods.onCloseMenu()
        })
      document
        .getElementById('lm-btn-delete-head')
        .addEventListener('click', () => {
          Axios({
            method: 'DELETE',
            url: `/api/v1/${table_prefix}t_backlog_d_stories_s_acs/${item.value.id}`,
          }).then(() => {
            modalData.currentData[
              `${table_prefix}t_backlog_d_stories_s_acs`
            ].forEach((dt, dtI) => {
              if (Cryptor.decrypt(dt.id) === Cryptor.decrypt(item.value.id)) {
                modalData.currentData[
                  `${table_prefix}t_backlog_d_stories_s_acs`
                ].splice(dtI, 1)
              }
            })
            headIsDeleted.value = true
            methods.onCloseMenu()
          })
        })
      document
        .getElementById('lm-btn-close-head')
        .addEventListener('click', () => {
          methods.onCloseMenu()
        })

      tempsStore.setIgnoreClickOutsideModal(true)
    },
    onToggleMenu: (elId, order) => {
      dropdownMenuIsOpen.value = true

      const id = document.getElementById(elId + '-' + order)
      const { top, left } = useElementBounding(id)
      const style = `top: ${top.value + 35}px; left: ${
        left.value - 160
      }px; height: auto; width: 192px; z-index: 9999
      `
      div.innerHTML = `<div class="fixed border dark:border-gray-500 rounded-xl shadow-lg" style="${style}">
            <div id="lm-btn-add-new-item" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2 rounded-t-xl">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-plus"></i>
                </div>
                <div class="flex-grow">
                    Add new item
                </div>
            </div>
            <div id="lm-btn-duplicate" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-copy"></i>
                </div>
                <div class="flex-grow">
                    Duplicate
                </div>
            </div>
            <div id="lm-btn-rename" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-pencil"></i>
                </div>
                <div class="flex-grow">
                    Rename
                </div>
            </div>
            <div id="lm-btn-delete" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-trash"></i>
                </div>
                <div class="flex-grow">
                    Delete
                </div>
            </div>
            <div id="lm-btn-close" class="cursor-pointer flex transition-all duration-200 bg-base-100 hover:bg-base-200 items-center text-xs py-2 rounded-b-xl">
                <div class="flex-grow-0 px-2">
                    <i class="fa-light fa-circle-xmark"></i>
                </div>
                <div class="flex-grow">
                    Close
                </div>
            </div>
        </div>`

      document
        .getElementById('lm-btn-add-new-item')
        .addEventListener('click', () => {
          methods.onAddNewItem()
          methods.onCloseMenu()
        })
      document
        .getElementById('lm-btn-duplicate')
        .addEventListener('click', () => {
          methods.onDuplicate(order)
        })
      document.getElementById('lm-btn-rename').addEventListener('click', () => {
        methods.onFocus(elId, order)
      })
      document.getElementById('lm-btn-delete').addEventListener('click', () => {
        methods.onRemove(order)
      })
      document.getElementById('lm-btn-close').addEventListener('click', () => {
        methods.onCloseMenu()
      })

      tempsStore.setIgnoreClickOutsideModal(true)
    },
  }
</script>

<template>
  <div v-if="!headIsDeleted">
    <div
      class="text-gray-5 text-sm flex items-center"
      @mouseover="() => (headFocused = true)"
      @mouseleave="() => (headFocused = false)"
    >
      <span class="text-xs mr-2 flex-grow-0">
        ({{ hasChecked }}/{{
          item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`]
            .length
        }})
      </span>
      <form class="flex-grow" @submit.prevent="methods.onHeadChange">
        <input
          :id="`${item.value.id}-input`"
          v-model="item.value.title"
          type="text"
          class="font-bold focus:ring-0 focus:outline-none w-full text-gray-900 dark:text-white dark:bg-base-100 dark:caret-white"
          placeholder="Type your checklist title"
          required
          @change="methods.onHeadChange"
        />
      </form>
      <i
        v-if="headFocused"
        :id="`${item.value.id}-menu`"
        class="fa-solid fa-ellipsis ml-3 mr-3 text-xl cursor-pointer flex-grow-0"
        @click="methods.onToggleHeadMenu"
      ></i>
    </div>
    <div
      :class="{
        'pl-0 mt-2': true,
        'pb-4':
          !item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`]
            .length,
      }"
    >
      <draggable
        v-model="
          item.value[`${table_prefix}t_backlog_d_stories_s_acs_d_values`]
        "
        :group="item.value.id"
        item-key="id"
        @start="drag = true"
        @end="drag = false"
      >
        <template #item="{ element, index }">
          <div
            :class="{
              'transition-all duration-300 py-0 flex items-center': true,
            }"
            @mouseover="methods.onItemMouseOver(index)"
            @mouseleave="methods.onItemMouseLeave(index)"
          >
            <div
              :class="{
                'flex-grow-0 text-lg text-gray-400 px-3 cursor-move opacity-0 transition-all duration-300': true,
                'opacity-100': element.focus,
              }"
            >
              <i class="fa-solid fa-grip-dots-vertical"></i>
            </div>
            <div class="form-control flex-grow">
              <label class="label cursor-pointer flex justify-start">
                <input
                  v-model="element.checked"
                  type="checkbox"
                  class="checkbox mr-3"
                  @change="methods.onChange(element, index)"
                />
                <input
                  :id="element.id + '-input-' + element.order"
                  v-model="element.value"
                  type="text"
                  class="flex-grow text-gray-900 dark:text-white dark:bg-base-100 dark:caret-white dark:border-base-100 w-full focus:ring-0 focus:outline-none border border-white hover:border-gray-300 dark:hover:border-gray-800 p-1"
                  placeholder="Type here"
                  @change="methods.onChange(element, index)"
                />
              </label>
            </div>
            <div
              :id="element.id + '-' + element.order"
              :class="{
                'flex-grow-0 text-lg text-gray-400 px-3 cursor-pointer opacity-0 transition-all duration-300': true,
                'opacity-100': element.focus,
              }"
              @click="methods.onToggleMenu(element.id, element.order)"
            >
              <i class="fa-solid fa-ellipsis"></i>
            </div>
          </div>
        </template>
      </draggable>
      <div class="flex items-center my-2 pl-7">
        <div class="flex-grow-0 text-lg text-gray-400 px-3">
          <i class="fa-light fa-plus"></i>
        </div>
        <form
          class="flex-grow text-xs"
          @submit.prevent="methods.onAddNewItemReal"
        >
          <input
            :id="`${item.value.id}-input-newitem`"
            v-model="newItem"
            type="text"
            class="flex-grow text-gray-900 dark:text-white dark:bg-base-100 dark:caret-white dark:border-base-100 w-full focus:ring-0 focus:outline-none border border-white hover:border-gray-300 dark:hover:border-gray-800 p-1"
            placeholder="Add new checklist"
          />
        </form>
      </div>
    </div>
  </div>
</template>
