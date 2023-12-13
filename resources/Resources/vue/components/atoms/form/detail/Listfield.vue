<template>
  <div
    :class="{
      'is-invalid':
        temp.form.rows[rowIndex].errors &&
        temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
    }"
  >
    <draggable
      v-model="tempVal_"
      group="people"
      item-key="id"
      @start="drag = true"
      @end="drag = false"
      @change="methods.onChange"
    >
      <template #item="{ element, index }">
        <div
          :class="{
            'transition-all duration-300 py-2 flex items-center': true,
          }"
          @mouseover="methods.onMouseOver(index)"
          @mouseleave="methods.onMouseLeave(index)"
        >
          <div
            :class="{
              'flex-grow-0 text-lg text-gray-400 px-3 cursor-move opacity-0 transition-all duration-300': true,
              'opacity-100': element.focus,
            }"
          >
            <i class="fa-solid fa-grip-dots-vertical"></i>
          </div>
          <input
            :id="elementId + '-input-' + element.order"
            v-model="element.value"
            type="text"
            class="flex-grow text-gray-900 dark:text-white dark:bg-base-100 dark:caret-white dark:border-base-100 w-full focus:ring-0 focus:outline-none border border-white hover:border-gray-300 dark:hover:border-gray-800 p-2"
            placeholder="Type here"
          />
          <div
            :id="elementId + '-' + element.order"
            :class="{
              'flex-grow-0 text-lg text-gray-400 px-3 cursor-pointer opacity-0 transition-all duration-300': true,
              'opacity-100': element.focus,
            }"
            @click="methods.onToggleMenu(element.order)"
          >
            <i class="fa-solid fa-ellipsis"></i>
          </div>
        </div>
      </template>
    </draggable>
    <div
      class="flex cursor-pointer items-center my-2"
      @click="methods.onAddNewItem"
    >
      <div class="flex-grow-0 text-lg text-gray-400 px-3">
        <i class="fa-light fa-plus"></i>
      </div>
      <div class="flex-grow text-xs">Add new item</div>
    </div>
    <form-validation
      :rows="temp.form.rows"
      :row-index="rowIndex"
      :field="field"
      :form="temp.form"
    />
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    inject,
    onMounted,
    ref,
    watch,
    defineProps,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import draggable from 'vuedraggable'
  import { useElementBounding, onClickOutside } from '@vueuse/core'
  import { useTempsStore } from '~/store/temps'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TagsfieldDetail',
  })

  const props = defineProps({
    rowIndex: {
      type: Number,
      required: true,
    },
    field: {
      type: String,
      required: true,
    },
    placeholder: {
      type: [String, Object as () => Lang],
      default: '',
    },
    listener: {
      type: Function,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    default: {
      type: Array,
      default: null,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const tempVal_ = ref([])
  const drag = ref(false)
  const tempVal = ref(
    computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  )

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const isReady = ref(false)
  const dropdownMenuIsOpen = ref(false)
  const nativeEdit = ref(false)

  const div = document.createElement('div')
  const tempsStore = useTempsStore()
  const methods = {
    onChange: (evt) => {
      if (evt.moved) {
        evt.type = 'moved'
      }

      tempVal.value = tempVal_.value
      if (props.listener) {
        props.listener(element, evt.type, tempVal_.value)
      }
    },
    onAddNewItem: () => {
      tempVal_.value.push({
        value: '',
        focus: false,
        order: tempVal_.value.length + 1,
        fixed: false,
      })

      methods.onChange({ type: 'change' })
    },
    onMouseOver: (index) => {
      tempVal_.value[index].focus = true
    },
    onMouseLeave: (index) => {
      tempVal_.value[index].focus = false
    },
    onRemove: (order) => {
      tempVal_.value.forEach((value, index) => {
        if (value.order === order) {
          tempVal_.value.splice(index, 1)
        }
      })
      methods.onCloseMenu()
      methods.onChange({ type: 'change' })
    },
    onDuplicate: (order) => {
      tempVal_.value.forEach((item, index) => {
        if (item.order === order) {
          tempVal_.value.push({
            value: item.value,
            focus: false,
            order: tempVal_.value.length + 1,
            fixed: false,
          })
        }
      })
      methods.onCloseMenu()
      methods.onChange({ type: 'change' })
    },
    onCloseMenu: () => {
      div.innerHTML = '<div></div>'
      dropdownMenuIsOpen.value = false
      setTimeout(() => {
        tempsStore.setIgnoreClickOutsideModal(false)
      }, 100)
    },
    onFocus: (order) => {
      document.getElementById(elementId.value + '-input-' + order).focus()
      methods.onCloseMenu()
    },
    onToggleMenu: (order) => {
      dropdownMenuIsOpen.value = true

      const id = document.getElementById(elementId.value + '-' + order)
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
        methods.onFocus(order)
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

  onMounted(() => {
    isReady.value = true
    div.setAttribute('id', 'listfield-dropdown-menu')
    document.body.appendChild(div)

    onClickOutside(div, () => {
      if (dropdownMenuIsOpen.value) {
        methods.onCloseMenu()
      }
    })

    // set default value
    if (Array.isArray(tempVal.value) && tempVal.value.length > 0) {
      tempVal_.value = tempVal.value
    }

    setTimeout(() => {
      temp.form.ready++
    }, 100)
  })

  watch(tempVal, () => {
    if (isReady.value && !nativeEdit.value) {
      //
    }
  })
</script>
