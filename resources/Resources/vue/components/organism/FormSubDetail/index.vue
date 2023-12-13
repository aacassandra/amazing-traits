<template>
  <div class="grid-cols-12 mt-5">
    <div
      v-if="form.options.element && form.options.element.header"
      class="col-span-12 mb-3"
    >
      <span v-html="form.options.element.header"></span>
    </div>
    <div class="col-span-12 flex justify-center mb-3">
      <button
        v-if="
          form.options.addRow.active && form.options.addRow.from === 'empty'
        "
        class="btn btn-sm btn-primary"
        :class="form.options.addRow.className"
        style="text-transform: unset"
        :disabled="
          !isReady ||
          form.options.addRow.disabled ||
          (form.options.addRow.max &&
            form.options.addRow.max === tempVal.length) ||
          disabled
        "
        @click="methods.onAddNewRow"
      >
        {{ t('global.add_new_row') }}
      </button>
      <AddNewList
        v-if="
          form.options.addRow.active && form.options.addRow.from === 'popup'
        "
        :disabled="
          form.options.addRow.disabled ||
          !isReady ||
          disabled ||
          (!form.options.addRow.options.isMultiple && tempVal.length !== 0)
        "
        :on-click="form.options.addRow.onClick"
        :on-verified="form.options.addRow.onVerified"
        :rows="tempVal"
        :options="form.options.addRow.options"
      />
      <button
        v-if="form.options.clearAllRow.active"
        :disabled="!isReady || !totalRow || disabled"
        class="btn btn-sm btn-error ml-2 text-white"
        style="text-transform: unset"
        @click="methods.onRemoveAllRow"
      >
        {{ t('global.clear_all_row') }}
      </button>
    </div>
    <div
      v-if="
        !form.options.multiTable ||
        (form.options.multiTable && !form.options.multiTable.active)
      "
      class="col-span-12 pl-1 pr-2.5"
    >
      <SubDetailCore :is-ready="isReady" />
    </div>
    <SubDetailMulti v-else />
    <div
      v-if="form.options.element && form.options.element.footer"
      class="col-span-12 mb-3"
    >
      <span v-html="form.options.element.footer"></span>
    </div>
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineExpose,
    defineProps,
    inject,
    onUnmounted,
    provide,
    ref,
    Ref,
    UnwrapRef,
  } from 'vue'
  import SubDetailCore from './SubDetailCore.vue'
  import AddNewList from './Popup/index.vue'
  import {
    ClearArray,
    Notyf,
    ObjectReader,
    ObjectUpdater,
    OnAddNewRow,
    Swal,
    t,
  } from '~/services'
  import {
    FormDetailProperties,
    FormMultiDetailProperties,
  } from '~/types/form/detail'
  import { FormSubDetail } from '~/types/form/subdetail'
  import Validator from '~/services/Validator/subdetail'
  import { Schema } from '~/types'
  import SubDetailMulti from './SubDetailMulti.vue'
  import { FormDetailPropertiesFixer } from '~/helpers'

  defineComponent({
    name: 'SubDetailGenerator',
  })

  const props = defineProps({
    onChange: {
      type: Function,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  })

  const allDisabled = ref(computed(() => props.disabled))
  provide('allDisabled', allDisabled)
  const schema = inject('schema') as Schema
  const tempVal = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const form = inject('form') as FormSubDetail
  form.ready = 0

  const totalRow = ref(0)
  provide('totalRow', totalRow)
  if (!tempVal.value) {
    tempVal.value = []
  } else {
    totalRow.value = tempVal.value.length
  }
  const isReady = ref(false)
  provide('isReady', isReady)
  const properties = inject('properties') as
    | FormDetailProperties
    | FormMultiDetailProperties
  const formTotal = ref(0)
  const keepModalShow = inject('keepModalShow') as Ref<UnwrapRef<boolean>>
  const fixProperties = FormDetailPropertiesFixer(
    properties,
    form.options.includes,
  )
  tempVal.value.forEach(() => {
    const finalProp = fixProperties.filterProperties.filter(
      (pro) => pro.type === 'editor',
    )
    formTotal.value += finalProp.length
  })

  const interver = ref(null)
  interver.value = setInterval(() => {
    if (formTotal.value === form.ready) {
      clearInterval(interver.value)
      interver.value = null
      isReady.value = true
    }
  }, 1000)

  const methods = {
    onAddNewRow: () => {
      const tempRow = {
        deleted: false,
      }
      fixProperties.filterProperties.forEach((fl) => {
        const value = OnAddNewRow(fl)

        ObjectUpdater(tempRow, fl.field, value)
      })

      props.onChange({ type: 'addrow' })
      totalRow.value++
      tempVal.value.push(tempRow)
    },
    onRemoveAllRow: () => {
      if (!tempVal.value.length) {
        Notyf({
          type: 'info',
          message: 'No data found',
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
        return false
      }

      keepModalShow.value = true
      const { exceptRowIndex } = form.options.clearAllRow
      let swalMessage = t('alert.remove_all_rows.html')
      if (exceptRowIndex !== undefined) {
        swalMessage = t('alert.remove_all_rows_except_row.html', {
          index: exceptRowIndex + 1,
        })
      }
      Swal.confirm({
        title: t('alert.remove_all_rows.title'),
        html: swalMessage,
        icon: 'warning',
        button: {
          confirm: 'primary',
          size: 'md',
        },
        showCancelButton: true,
        confirmButtonText: t('global.yes'),
        cancelButtonText: t('global.cancel'),
      }).then((result: any) => {
        if (result.isConfirmed) {
          props.onChange({ type: 'clearall' })
          totalRow.value = 0

          if (schema.mode === 'create') {
            if (exceptRowIndex !== undefined) {
              tempVal.value = tempVal.value.filter(
                (obj, objIndex) => objIndex === exceptRowIndex,
              )
            } else {
              // old version
              // ClearArray(tempVal.value)

              tempVal.value.length = 0
            }
          } else {
            // when mode update, need set flag deleted
            if (exceptRowIndex !== undefined) {
              tempVal.value.forEach((_, rowI) => {
                if (rowI !== exceptRowIndex) {
                  tempVal.value[rowI].deleted = true
                }
              })
            } else {
              // old version
              // tempVal.value.forEach((_, rowI) => {
              //   tempVal.value[rowI].deleted = true
              // })

              tempVal.value.length = 0
            }
          }

          form.ready = 0
        }

        setTimeout(() => {
          keepModalShow.value = false
        }, 300)
      })
    },
    onAddNewList: (list: Array<any>, reset = false) => {
      if (form.options.addRow.from === 'popup') {
        if (reset) {
          ClearArray(tempVal.value)
        }

        setTimeout(
          () => {
            list.forEach((li) => {
              const tempRow = {
                delete: false,
              }

              fixProperties.filterProperties.forEach((fl) => {
                const value = OnAddNewRow(fl)

                ObjectUpdater(tempRow, fl.field, value)
              })

              if (li.id) {
                tempRow['id'] = li.id
              }

              if (Array.isArray(properties)) {
                // for unmultiple table properties
                properties.forEach((pro) => {
                  if (li[pro.field] !== null && li[pro.field] !== undefined) {
                    ObjectUpdater(tempRow, pro.field, li[pro.field])
                  }
                })
              } else {
                // for multiple table properties
                fixProperties.unFilterProperties.forEach((pro) => {
                  if (li[pro.field] !== null && li[pro.field] !== undefined) {
                    ObjectUpdater(tempRow, pro.field, li[pro.field])
                  }
                })
              }

              if (
                form.options.addRow.from === 'popup' &&
                form.options.addRow.options.includes &&
                form.options.addRow.options.includes.length
              ) {
                form.options.addRow.options.includes.forEach((inc) => {
                  if (inc.includes(':')) {
                    const cx = inc.split(':')
                    if (cx.length) {
                      ObjectUpdater(
                        tempRow,
                        cx[1],
                        ObjectReader(li, cx[0]) || null,
                      )
                    }
                  } else {
                    ObjectUpdater(tempRow, inc, ObjectReader(li, inc) || null)
                  }
                })
              }

              totalRow.value++
              tempVal.value.push(tempRow)
            })
          },
          reset ? 100 : 0,
        )
      }
    },
  }
  provide('subDtlParentMethods', methods)

  onUnmounted(() => {
    form.ready = 0
  })

  const getValidRows = () => {
    return Validator(form, tempVal.value)
  }

  defineExpose({
    getValidRows,
  })
</script>
