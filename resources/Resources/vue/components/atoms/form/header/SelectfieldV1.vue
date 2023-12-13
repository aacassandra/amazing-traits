<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          :style="{
            minWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
            maxWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
          }"
          :class="{
            'flex mb-2': true,
            'flex-grow-0 mt-3 pl-0': inline && clientWidth > 639,
            'flex-grow pl-0': !inline || clientWidth < 640,
          }"
        >
          <label
            :for="name"
            :class="{
              'block text-xs font-medium text-gray-900 dark:text-gray-300': true,
              'text-red-700 dark:text-red-500':
                properties.errors && properties.errors(name),
            }"
          >
            {{ t(label) }}
            <span
              v-if="properties[name].rules.includes('required')"
              class="text-red-600"
            >
              *
            </span>
          </label>
          <div
            v-if="information"
            class="ft-sz-12 px-2 text-gray-300 dark:text-gray-800"
          >
            <i
              class="fas fa-question-circle cursor-pointer"
              @click="methods.onShowDoc"
            ></i>
          </div>
        </div>
        <div
          :class="{
            'px-0': true,
            'relative flex-grow': inline || clientWidth < 640,
            'flex-grow': !inline || clientWidth < 640,
          }"
        >
          <select
            v-if="field"
            :id="name"
            v-model="temp.value_"
            :multiple="isMultiple"
            :class="{
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 focus:outline-none block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly || false"
            @change="methods.onChange"
            @focus="methods.searchFocus"
            @blur="methods.searchBlur"
          >
            <option v-if="placeholder" value="">
              {{ t(placeholder) }}
            </option>
            <option
              v-for="(item, i) in temp.options_"
              :key="i"
              :value="item[field.value]"
            >
              {{ t(methods.getText(item, field.display)) }}
            </option>
          </select>
          <select
            v-else
            :id="name"
            v-model="temp.value_"
            :multiple="isMultiple"
            :class="{
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 focus:outline-none block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly || false"
            @change="methods.onChange"
            @focus="methods.searchFocus"
            @blur="methods.searchBlur"
          >
            <option v-if="placeholder" value="">
              {{ t(placeholder) }}
            </option>
            <option v-for="(item, i) in temp.options_" :key="i" :value="item">
              {{ t(item) }}
            </option>
          </select>
          <form-validation
            :name="name"
            :no-margin-top="true"
            :properties="properties"
          />
        </div>
      </div>
    </div>
    <div v-if="docs" :class="{ 'col-span-6 pl-3': true, 'pt-7': !inline }">
      <Highlight language="js" :code="code" />
    </div>
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineEmits,
    defineProps,
    inject,
    onMounted,
    reactive,
    ref,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import {
    IntToRupiah,
    Notyf,
    ObjectReader,
    Swal,
    t,
    UpdateWhereParams,
  } from '~/services'
  import { Highlight } from '~/components/atoms'
  import {
    ApiInterface,
    Element,
    OldParams,
  } from '~/types/components/atoms/forms/header'
  import { Lang } from '~/types/form/form-v1'
  import { Field } from '~/types/components/atoms/forms/header/select'

  type RequestCache =
    | 'default'
    | 'force-cache'
    | 'no-cache'
    | 'no-store'
    | 'only-if-cached'
    | 'reload'

  defineComponent({
    name: 'FormSelect',
  })

  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    modelValue: {
      type: [String, Number, Boolean, Array],
      default: null,
    },
    outVal: {
      type: [String, Number, Boolean, Array],
      default: null,
    },
    label: {
      type: String,
      default: 'Label',
    },
    placeholder: {
      type: [String, Object as () => Lang],
      default: '',
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
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    hidden: {
      type: Boolean,
      default: false,
    },
    inline: {
      type: Boolean,
      default: false,
    },
    docs: {
      type: Boolean,
      default: false,
    },
    information: {
      type: String,
      default: null,
    },
    properties: {
      type: Object,
      default: null,
    },
    fromGenerator: {
      type: Boolean,
      default: false,
    },
    ready: {
      type: Number,
      default: 0,
    },
    field: {
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
    items: {
      type: Array as () => Array<{ [key: string]: any } | string>,
      default: () => {
        return []
      },
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits([
    'update:modelValue',
    'update:outVal',
    'update:ready',
    'update:items',
  ])
  let cacheControl: RequestCache = 'default'
  const temp = reactive({
    outsideUpdating: false,
    value_: null,
    valueBeforeBlur: null,
    options_: [],
    optionBeforeBlur: null, // is object
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    items: computed({
      get: () => props.items,
      set: (val) => emit('update:items', val),
    }),
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    outVal: computed({
      get: () => props.outVal,
      set: (val) => emit('update:outVal', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
  })

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
    onShowDoc: () => {
      Notyf({
        type: 'info',
        message: t(props.information),
        duration: 3000,
        ripple: true,
        dismissible: true,
        position: {
          x: 'right',
          y: 'top',
        },
      })
    },
    onChange: () => {
      nativeEdit.value = true

      if (!props.isMultiple) {
        document.getElementById(props.name).blur()
      }

      temp.value = temp.value_

      if (props.listener) {
        // temp.outVal = temp.value
        props.listener(props.properties, 'change', temp.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    setOptions: (val: Array<any>) => {
      const fixOptions = []
      const currentValue = $(`#${props.name}`).val()
      temp.options_.forEach((opt) => {
        if (props.isMultiple) {
          if (
            Array.isArray(currentValue) &&
            currentValue.includes(opt[props.field.value])
          ) {
            fixOptions.push(opt)
          }
        } else if (opt[props.field.value] === currentValue) {
          fixOptions.push(opt)
        }
      })

      val.forEach((opt) => {
        if (props.isMultiple) {
          if (
            Array.isArray(currentValue) &&
            !currentValue.includes(opt[props.field.value])
          ) {
            fixOptions.push(opt)
          }
        } else if (opt[props.field.value] !== currentValue) {
          fixOptions.push(opt)
        }
      })

      temp.options_ = fixOptions
    },
    getValueFull: () => {
      if (props.isMultiple) {
        let options = []
        try {
          options = temp.options_.filter((dt) => {
            // eslint-disable-next-line
          return temp.value_.includes(dt[props.field.value].toString())
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
          option = temp.options_.find((dt) => {
            // eslint-disable-next-line
          return dt[props.field.value] == temp.value_
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

      temp.valueBeforeBlur = temp.value_
      temp.optionBeforeBlur = methods.getValueFull()

      methods.load()
    },
    searchBlur() {
      if (!props.api) {
        return
      }
      if (temp.value_ === null && temp.valueBeforeBlur) {
        if (
          temp.options_.findIndex(
            (dt) => dt[props.field.value] === temp.valueBeforeBlur,
          ) < 0
        ) {
          temp.options_.push(temp.optionBeforeBlur)
        }
        temp.value_ = temp.valueBeforeBlur
      }
      temp.valueBeforeBlur = null
    },
    load: (isById = false) => {
      return new Promise((resolve, reject) => {
        if (!props.api) {
          if (props.items.length) {
            props.items.forEach((vl) => {
              if (typeof vl === 'object') {
                if (typeof props.field.display === 'string') {
                  temp.options_.push({
                    [props.field.value]: ObjectReader(vl, props.field.value),
                    [props.field.display]: ObjectReader(
                      vl,
                      props.field.display,
                    ),
                  })
                }
              } else if (typeof vl === 'string' || typeof vl === 'number') {
                temp.options_.push(vl)
              }
            })
          }

          resolve(temp.options_)
        } else if (temp.optionBeforeBlur) {
          methods.setOptions([temp.optionBeforeBlur])
        } else {
          methods.setOptions([])
        }

        let signal

        try {
          temp.fetchController = new AbortController()
          signal = temp.fetchController.signal
        } catch (e) {
          console.log(e)
        }

        let url = ''
        if (props.api.url !== undefined) {
          url = props.api.url
        } else if (props.api.model !== undefined) {
          url = temp.apiOperationUrl + `/${props.api.model}`
        }

        let params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            params[key] = val
          })
        }
        cacheControl = props.api.cache === true ? 'force-cache' : 'no-cache'

        if (!isById) {
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
          const newParams = overrideParams(props.properties, params)
          if (typeof newParams === 'boolean' && !newParams) {
            return false
          }
          Object.assign(params, newParams)
          for (const key in params) {
            if (params[key] == null) {
              delete params[key]
            }
          }

          // if (!props.isMultiple) {
          //   params.wherenotin = `${props.field.value}=["${temp.value_}"]`
          // } else if (props.isMultiple) {
          //   if (Array.isArray(temp.value_) && temp.value_.length) {
          //     params.wherenotin = `${props.field.value}=${JSON.stringify(
          //       temp.value_,
          //     )}`
          //   } else {
          //     params = getFixParams({
          //       params,
          //       value: temp.value_,
          //       isMultiple: true,
          //       field: props.field,
          //     })
          //   }
          // }
        } else if (isById === true) {
          if (temp.outsideUpdating) {
            // updating by id from outside value
            if (props.isMultiple) {
              params.wherein = `${props.field.value}=${JSON.stringify(
                temp.value_,
              )}`
            } else {
              params.where = UpdateWhereParams(
                params.where,
                props.field,
                temp.value_,
              )
            }
          } else {
            // updating by id from default value
            if (props.isMultiple) {
              params.wherein = `${props.field.value}=${JSON.stringify(
                temp.value_,
              )}`
            } else {
              params.where = UpdateWhereParams(
                params.where,
                props.field,
                temp.value_,
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

              if (isById) {
                // to update current value when id is encrypted
                if (!props.isMultiple) {
                  // temp.value = ObjectReader(temp.options_[0], props.field.value)
                  // temp.value_ = ObjectReader(temp.options_[0], props.field.value)
                } else {
                  // const values = []
                  // temp.options_.forEach((opt) => {
                  //   values.push(ObjectReader(opt, props.field.value))
                  // })
                  //
                  // temp.value = values
                  // temp.value_ = values
                }

                if (temp.outsideUpdating) {
                  temp.outsideUpdating = false
                }
              }

              resolve(temp.options_)
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
              resolve(temp.options_)
            }
          })
          .catch((err) => {
            temp.options_ = []
            reject(err)
          })
      })
    },
  }

  // set default
  let isById = false
  if (props.default) {
    // set default
    if (props.isMultiple) {
      if (temp.value && Array.isArray(temp.value)) {
        temp.value_ = temp.value
        isById = !!temp.value.length
      } else if (Array.isArray(props.default)) {
        temp.value_ = props.default
        isById = !!props.default.length
      }
    } else {
      if (temp.value) {
        temp.value_ = temp.value
      } else {
        temp.value_ = props.default
      }
      isById = true
    }
  } else {
    if (props.isMultiple) {
      if (temp.value && Array.isArray(temp.value)) {
        temp.value_ = temp.value
        isById = !!temp.value.length
      } else {
        temp.value_ = []
      }
    } else {
      if (temp.value) {
        temp.value_ = temp.value
        isById = true
      } else {
        temp.value_ = ''
      }
    }
  }

  onMounted(() => {
    setTimeout(() => {
      if (props.api) {
        methods.load(isById).then(() => {
          temp.ready++
        })
      } else {
        if (props.field) {
          temp.items.forEach((vl) => {
            temp.options_.push({
              text: ObjectReader(vl, props.field.display),
              value: ObjectReader(vl, props.field.value),
            })
          })
        } else {
          temp.options_ = temp.items
        }
        temp.ready++
      }
    }, 100)
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        temp.outsideUpdating = true
        temp.value = props.outVal
        temp.value_ = props.outVal
        if (temp.value_) {
          methods.load(true)
        }
      }
    },
  )

  const code = ref(`
  {
    version: 1,
    type: 'selectfield',
    value: '',
    items: [],
    col: 'col-12 col-lg-6',
    options: {
      label: 'Select v1',
      information: 'Help text example: Select v1',
      inline: true,
      placeholder: 'Select one',
      readonly: false,
      disabled: false,
      hidden: false,
      multiple: false,
      field: {
        value: 'objectId',
        display: 'name'
      },
      reduce: (row) => { return row },
      api: {
        model: 'course_ctg',
        root: 'data',
        parameters: {
          paginate: 25,
          encrypt: false
        },
        cache: false,
        transform: (row) => { return row },
        overrideParams: (element, oldParams) => {
          oldParams['name'] = 'Jason'

          return oldParams
        }
      }
    },
    listener: (element, type, value) => {
      // console.log(type)
    },
    rules: ['required']
  }
`)
</script>
<style scoped>
  .is-disabled {
    background-color: #e3e6e9 !important;
  }
</style>
