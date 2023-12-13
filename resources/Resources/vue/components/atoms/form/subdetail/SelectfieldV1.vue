<template>
  <div>
    <div class="flex items-center">
      <select
        v-if="selectField"
        :id="elementId"
        v-model="tempVal_.value_"
        :multiple="isMultiple"
        :class="{
          'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
          'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 focus:outline-none block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
            tempValParent[rowIndex].errors &&
            tempValParent[rowIndex].errors(
              tempValParent,
              form,
              rowIndex,
              field,
            ),
        }"
        :disabled="disabled"
        :readonly="readonly"
        @change="methods.onChange"
        @focus="methods.searchFocus"
        @blur="methods.searchBlur"
      >
        <option v-if="placeholder" value="">
          {{ t(placeholder) }}
        </option>
        <option
          v-for="(item, i) in tempVal_.options_"
          :key="i"
          :value="item[selectField.value]"
        >
          {{ t(methods.getText(item, selectField.display)) }}
        </option>
      </select>
      <select
        v-else
        :id="elementId"
        v-model="tempVal_.value_"
        :multiple="isMultiple"
        :class="{
          'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
          'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 focus:outline-none block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
            tempValParent[rowIndex].errors &&
            tempValParent[rowIndex].errors(
              tempValParent,
              form,
              rowIndex,
              field,
            ),
        }"
        :disabled="disabled"
        :readonly="readonly"
        @change="methods.onChange"
        @focus="methods.searchFocus"
        @blur="methods.searchBlur"
      >
        <option v-if="placeholder" value="">
          {{ t(placeholder) }}
        </option>
        <option v-for="(item, i) in tempVal_.options_" :key="i" :value="item">
          {{ item }}
        </option>
      </select>
      <div v-if="!isReady" class="text-sm ml-2">
        <i class="fas fa-spin fa-spinner-third"></i>
      </div>
    </div>
    <form-validation
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
    />
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineProps,
    inject,
    onMounted,
    reactive,
    ref,
    Ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { ApiInterface, Element } from '~/types/components/atoms/forms/detail'
  import {
    IntToRupiah,
    ObjectReader,
    ObjectUpdater,
    Swal,
    t,
    UpdateWhereParams,
  } from '~/services'
  import { OldParams } from '~/types/components/atoms/forms/header'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { Lang } from '~/types/form/form-v1'
  import { Field } from '~/types/components/atoms/forms/header/select'

  defineComponent({
    name: 'SelectfieldV1Detail',
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
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    listener: {
      type: Function,
      default: null,
    },
    overrideParams: {
      type: Function as () => void,
      default: null,
    },
    reduce: {
      type: Function,
      default: null,
    },
    selectField: {
      type: Object as () => Field,
      default: null,
    },
    isMultiple: {
      type: Boolean,
      default: false,
    },
    api: {
      type: Object as () => ApiInterface,
      default: (): ApiInterface => {
        return null
      },
    },
    default: {
      type: [String, Number, Boolean, Array],
      default: '',
    },
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
    items: {
      type: Array as () => Array<
        | {
            [key: string]: any
          }
        | string
        | number
      >,
      default: () => {
        return []
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element

  const isReady = ref(false)

  let cacheControl: RequestCache = 'default'
  const tempVal = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  const tempVal_ = reactive({
    value_: null,
    valueBeforeBlur: null,
    options_: [],
    optionBeforeBlur: null, // is object
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    outsideUpdating: false,
  })

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)

  const methods = {
    getText(item: any, text: any) {
      let fixText = ''
      if (Array.isArray(text)) {
        text.forEach((tx) => {
          if (tx.type === 'text' && item[tx.value]) {
            fixText += `${item[tx.value]} `
          } else if (tx.type === 'note' && item[tx.value]) {
            fixText += `(${item[tx.value]}) `
          } else if (tx.type === 'price' && item[tx.value]) {
            fixText += `[${IntToRupiah(item[tx.value], 'Rp. ')}] `
          }
        })
      } else {
        fixText = item[text]
      }
      return fixText
    },
    onChange: () => {
      nativeEdit.value = true

      tempVal.value = tempVal_.value_

      if (props.listener) {
        props.listener(element, 'change', tempVal.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    setOptions: (val: Array<any>) => {
      tempVal_.options_ = val
    },
    getValueFull: () => {
      if (props.isMultiple) {
        let options = []
        try {
          options = tempVal_.options_.filter((dt) => {
            // eslint-disable-next-line
          return tempVal_.value_.includes(dt[props.selectField.value].toString())
          })
        } catch (e) {
          options = []
        }

        if (typeof props.reduce === 'function') {
          return props.reduce(options ?? [])
        } else {
          return options ?? []
        }
      } else {
        let option = {}
        try {
          option = tempVal_.options_.find((dt) => {
            // eslint-disable-next-line
          return dt[props.selectField.value] == tempVal_.value_
          })
        } catch (e) {
          option = {}
        }

        if (typeof props.reduce === 'function') {
          return props.reduce(option ?? {})
        } else {
          return option ?? {}
        }
      }
    },
    searchFocus: () => {
      if (!props.api) {
        return
      }
      tempVal_.valueBeforeBlur = tempVal_.value_
      tempVal_.optionBeforeBlur = methods.getValueFull()
      methods.load()
    },
    searchBlur() {
      if (!props.api) {
        return
      }
      if (tempVal_.value_ === null && tempVal_.valueBeforeBlur) {
        if (
          tempVal_.options_.findIndex(
            (dt) => dt[props.selectField.value] === tempVal_.valueBeforeBlur,
          ) < 0
        ) {
          tempVal_.options_.push(tempVal_.optionBeforeBlur)
        }
        tempVal_.value_ = tempVal_.valueBeforeBlur
      }
      tempVal_.valueBeforeBlur = null
    },
    load: (isById = false) => {
      isReady.value = false
      return new Promise((resolve, reject) => {
        if (!props.api) {
          if (props.items.length) {
            props.items.forEach((vl) => {
              if (typeof vl === 'object') {
                if (typeof props.selectField.display === 'string') {
                  tempVal_.options_.push({
                    [props.selectField.value]: ObjectReader(
                      vl,
                      props.selectField.value,
                    ),
                    [props.selectField.display]: ObjectReader(
                      vl,
                      props.selectField.display,
                    ),
                  })
                }
              } else if (typeof vl === 'string' || typeof vl === 'number') {
                tempVal_.options_.push(vl)
              }
            })
          }

          isReady.value = true
          resolve(tempVal_.options_)
        } else if (tempVal_.optionBeforeBlur) {
          // methods.setOptions([tempVal_.optionBeforeBlur])
        } else {
          methods.setOptions([])
        }

        let signal

        try {
          tempVal_.fetchController = new AbortController()
          signal = tempVal_.fetchController.signal
        } catch (e) {
          console.log(e)
        }

        let url = ''
        if (props.api.url !== undefined) {
          url = props.api.url
        } else if (props.api.model !== undefined) {
          url = tempVal_.apiOperationUrl + `/${props.api.model}`
        }

        const params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            params[key] = val
          })
        }

        cacheControl = props.api.cache === true ? 'force-cache' : 'no-cache'
        if (!isById) {
          cacheControl = 'force-cache'
          params.search = tempVal_.search

          let overrideParams = function (
            _: Element,
            oldParams: OldParams,
          ): OldParams {
            return oldParams
          }
          if (typeof props.overrideParams === 'function') {
            overrideParams = props.overrideParams
          }
          if (props.api.overrideParams !== undefined) {
            overrideParams = props.api.overrideParams
          }
          const newParams = overrideParams(element, params)
          if (typeof newParams === 'boolean' && !newParams) {
            return false
          }
          Object.assign(params, newParams)
          for (const key in params) {
            if (params[key] == null) {
              delete params[key]
            }
          }

          // if (!props.isMultiple && tempVal_.value_) {
          //   params.wherenotin = `${props.selectField.value}=["${tempVal_.value_}"]`
          // } else if (
          //   props.isMultiple &&
          //   Array.isArray(tempVal_.value_) &&
          //   tempVal_.value_.length
          // ) {
          //   params.wherenotin = `${props.selectField.value}=${JSON.stringify(
          //     tempVal_.value_,
          //   )}`
          // }
        } else if (isById === true) {
          if (tempVal_.outsideUpdating) {
            // updating by id from outside value
            if (props.isMultiple) {
              params.wherein = `${props.selectField.value}=${JSON.stringify(
                tempVal_.value_,
              )}`
            } else {
              params.where = UpdateWhereParams(
                params.where,
                props.selectField,
                tempVal_.value_,
              )
            }
          } else {
            // updating by id from default value
            // with the aim of displaying the default values on the form
            if (props.isMultiple) {
              params.wherein = `${props.selectField.value}=${JSON.stringify(
                tempVal_.value_,
              )}`
            } else {
              params.where = UpdateWhereParams(
                params.where,
                props.selectField,
                tempVal_.value_,
              )
            }
          }
        }

        url = url + '?' + new URLSearchParams(params)
        const cookieToken = localStorage.getItem('_token')
        fetch(url, {
          method: 'GET',
          mode: 'cors',
          cache: cacheControl,
          credentials: 'same-origin',
          redirect: 'follow',
          referrerPolicy: 'no-referrer',
          headers: {
            'Content-Type': 'application/json',
            Authorization: cookieToken,
          },
          signal,
        })
          .then((response) => {
            return response.json()
          })
          .then((data) => {
            if (data.success) {
              let fixedData = props.api.root ? data[props.api.root] : data
              if (!Array.isArray(fixedData)) {
                fixedData = [fixedData]
              }
              const arrayData = fixedData.map(function (row) {
                const func = props.api.transform
                if (typeof func === 'function') {
                  return func(row)
                }
                return row
              })

              methods.setOptions(arrayData)

              isReady.value = true

              // to update current value when id is encrypted
              if (tempVal_.outsideUpdating) {
                if (!props.isMultiple) {
                  tempVal_.value_ = ObjectReader(
                    tempVal_.options_[0],
                    props.selectField.value,
                  )
                } else {
                  const values = []
                  tempVal_.options_.forEach((opt) => {
                    values.push(ObjectReader(opt, props.selectField.value))
                  })

                  tempVal_.value_ = values
                }
                tempVal_.outsideUpdating = false
              }

              resolve(tempVal_.options_)
            } else {
              Swal.basic({
                icon: 'error',
                title: `Error ${data.statusCode}!`,
                html: data.statusMessage,
                button: {
                  confirm: 'default',
                  size: 'md',
                },
              })
              isReady.value = true
              resolve(tempVal_.options_)
            }
          })
          .catch((err) => {
            isReady.value = true
            tempVal_.options_ = []
            reject(err)
          })
      })
    },
  }

  // set default
  let isById = false
  if (props.default) {
    if (props.isMultiple) {
      if (tempVal.value && Array.isArray(tempVal.value)) {
        tempVal_.value_ = tempVal.value
        isById = !!tempVal.value.length
      } else if (Array.isArray(props.default)) {
        tempVal_.value_ = props.default
        isById = !!props.default.length
      }
    } else {
      if (tempVal.value) {
        tempVal_.value_ = tempVal.value
      } else {
        tempVal_.value_ = props.default
      }
      isById = true
    }
  } else {
    if (props.isMultiple) {
      if (tempVal.value && Array.isArray(tempVal.value)) {
        tempVal_.value_ = tempVal.value
        isById = !!tempVal.value.length
      } else {
        tempVal_.value_ = []
      }
    } else {
      if (tempVal.value) {
        tempVal_.value_ = tempVal.value
        isById = true
      } else {
        tempVal_.value_ = ''
      }
    }
  }

  onMounted(() => {
    setTimeout(() => {
      methods.load(isById).then(() => {
        isReady.value = true
        form.ready++
      })
    }, 100)
  })

  watch(
    tempVal,
    () => {
      if (isReady.value && tempValParent[props.rowIndex] && !nativeEdit.value) {
        tempVal_.outsideUpdating = true
        tempVal_.value_ = ObjectReader(
          tempValParent.value[props.rowIndex],
          props.field,
        )
        if (tempVal_.value_) {
          methods.load(true)
        }
      }
    },
    {
      deep: true,
    },
  )
</script>
