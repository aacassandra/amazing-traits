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
            v-if="label"
            :for="name"
            :class="{
              'block text-xs font-medium text-gray-900 dark:text-gray-300': true,
              'text-red-700 dark:text-red-500':
                fromGenerator && properties.errors && properties.errors(name),
            }"
          >
            {{ t(label) }}
            <span
              v-if="
                fromGenerator && properties[name].rules.includes('required')
              "
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
            'px-0 selectize-header': true,
            'relative flex-grow': inline || clientWidth < 640,
            'flex-grow': !inline || clientWidth < 640,
            'opacity-0': !componentisReady,
          }"
        >
          <select
            v-if="field"
            :id="name"
            :multiple="isMultiple"
            :class="{
              [`${name}-selectize`]: true,
              'is-invalid':
                fromGenerator && properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly"
          >
            <!-- -->
          </select>
          <select
            v-else
            :id="name"
            :multiple="isMultiple"
            :class="{
              [`${name}-selectize`]: true,
              'mb-0 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'is-invalid':
                fromGenerator && properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly"
          >
            <!-- -->
          </select>
          <form-validation
            v-if="fromGenerator"
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
  import { IntToRupiah, Notyf, ObjectReader, Swal, t } from '~/services'
  import { Highlight } from '~/components/atoms'
  import {
    ApiInterface,
    Element,
    OldParams,
  } from '~/types/components/atoms/forms/header'
  // import css availble in cms.vue

  type RequestCache =
    | 'default'
    | 'force-cache'
    | 'no-cache'
    | 'no-store'
    | 'only-if-cached'
    | 'reload'
  import { Lang } from '~/types/form/form-v1'

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
      default: '',
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
    clearable: {
      type: Boolean,
      default: false,
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
      type: Object as () => { value: string; display: string },
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
      type: Array as () => Array<any>,
      default: () => {
        return []
      },
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits([
    'update:modelValue',
    'update:ready',
    'update:items',
  ])

  const isReady = ref(false)
  const componentisReady = ref(false)
  let cacheControl: RequestCache = 'default'
  const temp = reactive({
    options_: [],
    lockValue: false,
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    value_: null,
    items: computed({
      get: () => props.items,
      set: (val) => emit('update:items', val),
    }),
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
    outsideUpdating: false,
  })

  const nativeEdit = ref(false)

  const selector = ref(null)
  const methods = {
    onInit: () => {
      const plugins = []
      if (props.isMultiple) {
        plugins.push('remove_button')
        plugins.push('drag_drop')
      }
      if (props.clearable) {
        plugins.push('clear_button')
      }

      selector.value = $(`.${props.name}-selectize`)
      selector.value.selectize({
        // options: [{ value: '', text: 'Select more' }],
        // items: [''],
        placeholder: props.placeholder,
        sortField: [
          // {
          //   field: 'text',
          //   direction: 'asc'
          // },
          // {
          //   field: '$score'
          // }
        ],
        create: false,
        plugins,
        onChange: (value) => {
          if (!temp.lockValue) {
            temp.value = value || null
            temp.value_ = value || null
          } else if (value) {
            if (props.isMultiple && value.length) {
              temp.value = value
              temp.value_ = value
            } else if (!props.isMultiple) {
              temp.value = value
              temp.value_ = value
            }
          }

          if (props.isMultiple) {
            if (!temp.value_.length) {
              setTimeout(() => {
                $(
                  `.${props.name}-selectize + .selectize-control > a.clear`,
                ).css('display', 'none')
              }, 1)
            }
          }

          methods.onChange({ type: 'change' })
        },
        onFocus: () => {
          methods.load()
          // temp.lockValue = true
          methods.onChange({ type: 'focus' })
        },
        onBlur: () => {
          methods.onChange({ type: 'blur' })
        },
        onDelete: (value) => {
          if (props.isMultiple) {
            if (value.length === 1) {
              temp.value = []
              temp.value_ = []
            }
          } else {
            temp.value = null
            temp.value_ = null
          }
        },
      })
      setTimeout(() => {
        componentisReady.value = true
      }, 300)
    },
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
    onChange: (evt) => {
      nativeEdit.value = true

      if (props.listener) {
        props.listener(props.properties, evt.type, temp.value_)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    setOptions: (arrayData: Array<any>, isById = false, debug = false) => {
      return new Promise((resolve) => {
        const selectize = selector.value[0].selectize

        selectize.clear()
        selectize.clearOptions()
        let selectedOptions = []

        if (temp.value_) {
          if (props.isMultiple && temp.value_.length) {
            const tempArr = temp.value_ as Array<any>
            if (tempArr.length) {
              temp.options_.forEach((opt) => {
                if (tempArr.includes(opt.value)) {
                  selectedOptions.push(opt)
                }
              })
            }
          } else {
            temp.options_.forEach((opt) => {
              if (temp.value_ === opt.value) {
                selectedOptions.push(opt)
              }
            })
          }
        }
        selectedOptions = JSON.parse(JSON.stringify(selectedOptions))

        const newOptions = []
        arrayData.forEach((vl) => {
          newOptions.push({
            text: ObjectReader(vl, props.field.display),
            value: ObjectReader(vl, props.field.value),
          })
        })

        temp.options_ = selectedOptions.concat(newOptions)

        selectize.load(function (callback) {
          callback(temp.options_)
        })

        if (isById) {
          // this will be useful if the id value has been encrypted
          if (!props.isMultiple) {
            temp.value = temp.options_[0].value
            temp.value_ = temp.options_[0].value
            selector.value[0].selectize.setValue(temp.value_)
          } else {
            const values = []
            temp.options_.forEach((opt) => {
              values.push(opt.value)
            })

            temp.value = values
            temp.value_ = values
            selector.value[0].selectize.setValue(temp.value_)
          }

          temp.lockValue = true
          methods.load(false, true).then(() => {
            temp.lockValue = false
            resolve(true)
          })
        } else {
          if (temp.value_) {
            if (props.isMultiple) {
              if (temp.value_.length) {
                selectize.setValue(temp.value_)
              }
            } else {
              selectize.setValue(temp.value_)
            }
            temp.lockValue = false
            resolve(true)
          } else {
            resolve(true)
          }
        }
      })
    },
    getValueFull: () => {
      if (typeof props.reduce === 'function') {
        return props.reduce(temp.options_ ?? [])
      } else {
        return temp.options_ ?? []
      }
    },
    load: (isById = false, debug = false) => {
      isReady.value = false
      if (debug) {
        // eslint-disable-next-line no-console
        console.log('is load')
      }
      return new Promise((resolve, reject) => {
        if (!props.api) {
          resolve(temp.options_)
        }

        let signal

        try {
          temp.fetchController = new AbortController()
          signal = temp.fetchController.signal
        } catch (e) {
          // eslint-disable-next-line no-console
          console.log(e)
        }

        let url = ''
        if (props.api.url !== undefined) {
          url = props.api.url
        } else if (props.api.model !== undefined) {
          url = temp.apiOperationUrl + `/${props.api.model}`
        }

        const params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            params[key] = val
          })
        }
        cacheControl = props.api.cache === true ? 'force-cache' : 'no-cache'
        if (!isById) {
          // cacheControl = 'force-cache'

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

          if (!props.isMultiple && temp.value_) {
            params.wherenotin = `${props.field.value}=["${temp.value_}"]`
          } else if (
            props.isMultiple &&
            Array.isArray(temp.value_) &&
            temp.value_.length
          ) {
            params.wherenotin = `${props.field.value}=${JSON.stringify(
              temp.value_,
            )}`
          }
        } else {
          if (temp.outsideUpdating) {
            // updating by id from outside value
            if (props.isMultiple) {
              params.wherein = `${props.field.value}=${JSON.stringify(
                temp.value_,
              )}`
            } else {
              if (typeof temp.value_ === 'string' && temp.value_) {
                params.where = `this.${props.field.value}=':${temp.value_}:'`
              } else if (temp.value_) {
                params.where = `this.${props.field.value}=${temp.value_}`
              }
            }
          } else {
            // updating by id from default value
            // with the aim of displaying the default values on the form
            if (props.isMultiple) {
              params.wherein = `${props.field.value}=${JSON.stringify(
                props.default,
              )}`
            } else {
              if (typeof temp.value_ === 'string') {
                params.where = `this.${props.field.value}=':${temp.value_}:'`
              } else {
                params.where = `this.${props.field.value}=${temp.value_}`
              }
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
          .then(async (data) => {
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

              await methods.setOptions(arrayData, isById, debug)

              if (temp.outsideUpdating) {
                temp.outsideUpdating = false
              }

              resolve(true)
            } else {
              await Swal.basic({
                icon: 'error',
                title: `Error ${data.statusCode}!`,
                html: data.statusMessage,
                button: {
                  confirm: 'default',
                  size: 'md',
                },
              })

              isReady.value = true
              resolve(temp.options_)
            }
          })
          .catch((err) => {
            isReady.value = true
            temp.options_ = []
            reject(err)
          })
      })
    },
  }

  let isById = false
  let isEmpty = false // [TODO] isEmpty perlu di implementasikan di detail dan subdetail
  if (props.default) {
    // set default
    if (temp.value) {
      temp.value_ = temp.value
    } else {
      temp.value_ = props.default
    }
    isById = true
  } else {
    if (temp.value) {
      temp.value_ = temp.value
      isById = true
    } else {
      temp.value_ = props.isMultiple ? [] : ''
    }
  }

  onMounted(() => {
    setTimeout(() => {
      if (props.api) {
        methods.onInit()
        setTimeout(() => {
          methods.load(isById).then(() => {
            setTimeout(() => {
              temp.ready++
            }, 300)
          })
        }, 100)
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
        methods.onInit()
        setTimeout(() => {
          temp.ready++
        }, 300)
      }
    }, 1000)
  })

  watch(
    () => props.disabled,
    () => {
      if (props.disabled) {
        selector.value[0].selectize.disable()
      } else {
        selector.value[0].selectize.enable()
      }
    },
  )

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value_ !== props.outVal) {
        let run = false
        if (props.isMultiple) {
          if (Array.isArray(props.outVal) && props.outVal.length) {
            run = true
          } else {
            selector.value[0].selectize.setValue([])
            temp.value = []
            temp.value_ = []
            $(`.${props.name}-selectize + .selectize-control > a.clear`).css(
              'display',
              'none',
            )
          }
        } else {
          if (props.outVal) {
            run = true
          } else {
            selector.value[0].selectize.setValue('')
            temp.value = ''
            temp.value_ = ''
            $(`.${props.name}-selectize + .selectize-control > a.clear`).css(
              'display',
              'none',
            )
          }
        }

        if (run) {
          temp.outsideUpdating = true
          temp.value = props.outVal
          temp.value_ = props.outVal
          methods.load(true)
        }
      }
    },
  )

  const code = ref(`
  {
    version: 2,
    type: 'selectfield',
    value: '',
    items: [],
    col: 'col-span-12 lg:col-span-6',
    options: {
      label: 'Select v2',
      information: 'Help text example: Select v2',
      inline: true,
      placeholder: 'Select one',
      readonly: false,
      disabled: false,
      hidden: false,
      multiple: false,
      clearable: true,
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
