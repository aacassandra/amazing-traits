<template>
  <div class="grid grid-cols-12">
    <div class="col-span-12 flex justify-center mb-3">
      <button
        v-if="form.addRow.active && form.addRow.from === 'empty'"
        :disabled="
          form.addRow.disabled ||
          !isReady ||
          isLoading ||
          (form.addRow.max && form.addRow.max === temp.form.rows.length)
        "
        class="btn btn-sm btn-primary border-0"
        :class="form.addRow.className"
        style="text-transform: unset"
        @click="methods.onAddNewRow"
      >
        {{ $t('global.add_new_row') }}
      </button>
      <AddNewList
        v-if="form.addRow.active && form.addRow.from === 'popup'"
        :disabled="
          form.addRow.disabled ||
          !isReady ||
          isLoading ||
          (!form.addRow.options.isMultiple && temp.form.rows.length !== 0)
        "
        :on-click="form.addRow.onClick"
        :on-verified="form.addRow.onVerified"
        :rows="temp.form.rows"
        :options="form.addRow.options"
      />
      <button
        v-if="form.clearAllRow.active"
        :disabled="
          form.clearAllRow.disabled ||
          !isReady ||
          !temp.form.rows.length ||
          isLoading
        "
        class="btn btn-sm btn-accent border-0 ml-2 text-white"
        style="text-transform: unset"
        @click="methods.onClearAllRow"
      >
        {{ $t('global.clear_all_row') }}
      </button>
    </div>
    <div
      v-if="!form.multiTable || (form.multiTable && !form.multiTable.active)"
      class="col-span-12 pl-1 pr-2.5"
    >
      <Detail :is-loading="isLoading" />
    </div>
    <DetailMulti v-else :is-loading="isLoading" />
  </div>
</template>
<script setup lang="ts">
  import {
    computed,
    defineEmits,
    defineExpose,
    defineProps,
    inject,
    onMounted,
    provide,
    reactive,
    ref,
  } from 'vue'
  import Detail from './Detail.vue'
  import AddNewList from './Popup/index.vue'
  import DetailMulti from './DetailMulti.vue'
  import { Schema } from '~/types'
  import { FormDetail } from '~/types/form/detail'
  import {
    ClearArray,
    Notyf,
    ObjectReader,
    ObjectUpdater,
    OnAddNewRow,
    Swal,
  } from '~/services'
  import Validator from '~/services/Validator/detail'
  import { FormDetailPropertiesFixer } from '~/helpers'
  import { useI18n } from 'vue-i18n'
  import onAddNewRow from '~/services/OnAddNewRow'

  const props = defineProps({
    form: {
      type: Object,
      required: true,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
  })

  const { t } = useI18n()
  const schema = inject('schema') as Schema
  const emit = defineEmits(['update:form'])
  const temp = reactive({
    form: computed({
      get: () => props.form,
      set: (val) => emit('update:form', val),
    }),
  }) as {
    form: FormDetail
  }
  provide('temp', temp)

  const isReady = ref(false)
  const formTotal = ref(0)
  provide('isReady', isReady)

  const methods = {
    onGetFormTotal: () => {
      formTotal.value = 0
      temp.form.rows.forEach(() => {
        if (Array.isArray(temp.form.properties)) {
          const fixProperties = temp.form.properties.filter(
            (pro) => pro.type === 'editor',
          )
          formTotal.value += fixProperties.length
        } else {
          // for multi table
          Object.entries(temp.form.properties).forEach(([_, val]) => {
            const tempProp = []
            val.forEach((vl) => {
              if (vl.type === 'editor') {
                tempProp.push(vl)
              }
            })
            formTotal.value += tempProp.length
          })
        }
      })
    },
    onRunInterver: () => {
      isReady.value = false

      if (temp.form.ready === undefined) temp.form.ready = 0
      const interver = ref(null)
      interver.value = setInterval(() => {
        methods.onGetFormTotal()
        if (formTotal.value === temp.form.ready) {
          clearInterval(interver.value)
          interver.value = null
          isReady.value = true
        }
      }, 1000)
    },
    onAddNewRow: () => {
      const fixProperties = FormDetailPropertiesFixer(temp.form.properties)
      const tempRow = {
        deleted: false,
      }
      fixProperties.filterProperties.forEach((fl) => {
        const value = OnAddNewRow(fl)

        ObjectUpdater(tempRow, fl.field, value)
      })
      temp.form.rows.push(tempRow)

      if (temp.form.listener) {
        temp.form.listener('add-new-row', tempRow, temp.form.rows.length - 1)
      }
    },
    onAddNewList: (list: Array<any>, reset = false) => {
      if (props.form.addRow.from === 'popup') {
        if (reset) {
          ClearArray(temp.form.rows)
        }

        setTimeout(
          () => {
            const fixProperties = FormDetailPropertiesFixer(
              temp.form.properties,
            )

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

              if (Array.isArray(temp.form.properties)) {
                props.form.properties.forEach((pro) => {
                  if (ObjectReader(li, pro.field)) {
                    ObjectUpdater(tempRow, pro.field, li[pro.field])
                  }
                })
              } else {
                fixProperties.unFilterProperties.forEach((pro) => {
                  if (ObjectReader(li, pro.field)) {
                    ObjectUpdater(tempRow, pro.field, li[pro.field])
                  }
                })
              }

              if (
                props.form.addRow.from === 'popup' &&
                props.form.addRow.options.includes &&
                props.form.addRow.options.includes.length
              ) {
                props.form.addRow.options.includes.forEach((inc) => {
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

              temp.form.rows.push(tempRow)
            })
            methods.onRunInterver()
          },
          reset ? 100 : 0,
        )
      }
    },
    onClearAllRow: () => {
      if (!temp.form.rows.length) {
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

      const { exceptRowIndex } = props.form.clearAllRow
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
          if (schema.mode === 'create') {
            if (exceptRowIndex !== undefined) {
              temp.form.rows = temp.form.rows.filter(
                (obj, objIndex) => objIndex === exceptRowIndex,
              )
            } else {
              // old version
              // ClearArray(temp.form.rows)

              temp.form.rows.length = 0
            }
          } else {
            // when mode update, need set flag deleted
            if (exceptRowIndex !== undefined) {
              temp.form.rows.forEach((_, rowI) => {
                if (rowI !== exceptRowIndex) {
                  temp.form.rows[rowI].deleted = true
                }
              })
            } else {
              // old version
              // temp.form.rows.forEach((_, rowI) => {
              //   temp.form.rows[rowI].deleted = true
              // })

              temp.form.rows.length = 0
            }
          }

          if (temp.form.listener) {
            temp.form.listener('clear-all-row', null, null)
          }
          temp.form.ready = 0
        }
      })
    },
  }
  provide('dtlParentMethods', methods)

  const getValidRows = () => {
    return Validator(temp.form)
  }

  const runLoader = () => {
    methods.onRunInterver()
  }

  defineExpose({
    getValidRows,
    runLoader,
  })

  onMounted(() => {
    methods.onRunInterver()
  })
</script>
