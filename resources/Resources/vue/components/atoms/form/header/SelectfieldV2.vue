<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          v-if="label"
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
            'px-0 select2-header': true,
            'relative flex-grow': inline || clientWidth < 640,
            'flex-grow': !inline || clientWidth < 640,
          }"
        >
          <select
            v-if="field"
            :id="name"
            :multiple="isMultiple"
            :class="{
              [`${name}-select2`]: true,
              hidden: true,
              'mb-0 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'is-invalid':
                fromGenerator && properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly"
          >
            <option v-if="placeholder && !isMultiple" value="">
              {{ t(placeholder) }}
            </option>
            <option
              v-for="(item, i) in temp.options_"
              :key="i"
              :value="item.id"
            >
              {{ t(item.text) }}
            </option>
          </select>
          <select
            v-else
            :id="name"
            :multiple="isMultiple"
            :class="{
              [`${name}-select2`]: true,
              hidden: true,
              'mb-0 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'is-invalid':
                fromGenerator && properties.errors && properties.errors(name),
            }"
            :disabled="disabled"
            :readonly="readonly"
          >
            <option v-if="placeholder && !isMultiple" value="">
              {{ t(placeholder) }}
            </option>
            <option v-for="(item, i) in temp.options_" :key="i" :value="item">
              {{ t(item) }}
            </option>
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
  import { Highlight } from '~/components/atoms'
  import {
    ApiInterface,
    Element,
    OldParams,
  } from '~/types/components/atoms/forms/header'
  import getFixParams from '~/helpers/form/select2/getFixParams'
  import { Ref, UnwrapRef } from '@vue/reactivity'
  import { Field } from '~/types/components/atoms/forms/header/select'
  import {
    IntToRupiah,
    Notyf,
    ObjectReader,
    UpdateWhereParams,
    t,
  } from '~/services'

  interface Prepare {
    url: string
    token: string
  }
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
    'update:ready',
    'update:items',
    'update:outVal',
  ])
  const parentReady = inject('parentReady') as Ref<UnwrapRef<boolean>>
  const temp = reactive({
    options_: [],
    options_reset: false,
    lockOptions: false,
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
    outVal: computed({
      get: () => props.outVal,
      set: (val) => emit('update:outVal', val),
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
    getApiUrl: () => {
      let url = ''
      if (props.api.url !== undefined) {
        url = props.api.url
      } else if (props.api.model !== undefined) {
        url = temp.apiOperationUrl + `/${props.api.model}`
      }

      return url
    },
    onInit: (pre: Prepare) => {
      return new Promise((resolve) => {
        selector.value = $(`.${props.name}-select2`)
        let select2Config = {
          placeholder: props.placeholder,
          allowClear: props.clearable,
          width: '100%',
        }

        if (props.api) {
          select2Config['ajax'] = {
            url: methods.getApiUrl(),
            cache: props.api.cache,
            data: function (query) {
              let params: any = {}
              if (props.api.parameters) {
                Object.entries(props.api.parameters).forEach(([key, val]) => {
                  params[key] = val
                })
              }

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

              const currentValue = selector.value.val()
              let whereColumn = props.field.value
              if (props.api.model) {
                whereColumn = `${props.api.model}.${props.field.value}`
              }

              if (!props.isMultiple) {
                if (currentValue) {
                  params.wherenotin = `${whereColumn}=["${currentValue}"]`
                } else {
                  params = getFixParams({
                    params,
                    value: currentValue,
                    term: query.term || '',
                    isMultiple: false,
                    field: props.field,
                    model: props.api.model || '',
                  })
                }
              } else if (props.isMultiple) {
                if (Array.isArray(temp.value) && temp.value.length) {
                  params.wherenotin = `${whereColumn}=${JSON.stringify(
                    currentValue,
                  )}`
                } else {
                  params = getFixParams({
                    params,
                    value: currentValue,
                    term: query.term || '',
                    isMultiple: true,
                    field: props.field,
                    model: props.api.model || '',
                  })
                }
              }

              return params
            },
            headers: {
              'Content-Type': 'application/json',
              Authorization: pre.token,
            },
            processResults: function (data) {
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

              temp.items = arrayData

              const results = []
              arrayData.forEach((vl) => {
                results.push({
                  id: ObjectReader(vl, props.field.value),
                  text: ObjectReader(
                    vl,
                    typeof props.field.display === 'string'
                      ? props.field.display
                      : '',
                  ),
                })
              })
              return {
                results,
              }
            },
          }
        } else {
          if (props.items.length) {
            props.items.forEach((vl) => {
              if (typeof vl === 'object') {
                temp.options_.push({
                  id: ObjectReader(vl, props.field.value),
                  text: ObjectReader(
                    vl,
                    typeof props.field.display === 'string'
                      ? props.field.display
                      : '',
                  ),
                })
              } else if (typeof vl === 'string') {
                temp.options_.push(vl)
              }
            })
          }
        }
        selector.value.select2(select2Config)
        selector.value
          .on('select2:closing', () => {
            methods.onChange({ type: 'bluring' })
          })
          .on('select2:close', () => {
            methods.onChange({ type: 'blur' })
          })
          .on('select2:opening', () => {
            methods.onChange({ type: 'focusing' })
          })
          .on('select2:open', () => {
            methods.onChange({ type: 'focus' })
          })
          .on('select2:selecting', () => {
            methods.onChange({ type: 'selecting' })
          })
          .on('select2:select', () => {
            methods.onChange({ type: 'select' })
          })
          .on('select2:unselecting', () => {
            methods.onChange({ type: 'unselecting' })
          })
          .on('select2:unselect', () => {
            methods.onChange({ type: 'unselect' })
          })
          .on('select2:clearing', () => {
            // for multiple has auto reset value by select2 to empty array
            if (!props.isMultiple) {
              temp.value = ''
            }
            methods.onChange({ type: 'clearing' })
          })
          .on('select2:clear', () => {
            if (props.isMultiple) {
              temp.value_ = []
              selector.value.val(null).trigger('change')
            }
            methods.onChange({ type: 'clear' })
          })
          .on('change', function () {
            // has commented, because there is a bug. temp.value can't be filled in here. when value for firstly is null oe empty
            const temps = selector.value.select2('data')
            if (props.isMultiple === true) {
              const tempArr: any = []
              temps.forEach((e) => {
                tempArr.push(e.id)
              })
              temp.value = tempArr
            } else {
              temp.value = temps.length ? temps[0].id : ''
            }

            temp.value_ = selector.value.val()

            methods.onChange({ type: 'change' })
          })

        resolve(true)
      })
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
      if (evt.type === 'change') {
        // [TODO] perlu di implementasikan semua ke semua komponent header, bertujuan untuk fix update value dari luar khususnya membersihkan data
        // temp.outVal = temp.value_
      }

      // bugfix size of multiple select
      if (props.isMultiple) {
        const selectorx = $(
          '.select2-container--default .select2-selection--multiple',
        )
        const tempArr = temp.value_ as Array<any>
        if (tempArr.length) {
          selectorx.css('height', 'unset')
          selectorx.css('min-height', '42px')
          selectorx.css('padding-bottom', '9px')
        } else {
          selectorx.css('height', '42px')
          selectorx.css('min-height', 'unset')
          selectorx.css('padding-bottom', 'unset')
        }
      }

      if (props.listener && parentReady.value) {
        props.listener(props.properties, evt.type, temp.value_)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    prepare: (isById = false): Promise<Prepare> => {
      return new Promise((resolve) => {
        if (props.api) {
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
                params.where = UpdateWhereParams(
                  params.where,
                  props.field,
                  temp.value_,
                )
              }
            } else {
              // updating by id from default value
              // with the aim of displaying the default values on the form
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

          resolve({ url, token: cookieToken })
        } else {
          resolve({ url: '', token: '' })
        }
      })
    },
    getById: (value?: any) => {
      return new Promise((resolve) => {
        const results = []
        if (!props.api) {
          selector.value.val(temp.value).trigger('change')
          resolve(results)
          return false
        }

        methods.prepare(true).then((res) => {
          $.ajax({
            method: 'GET',
            url: res.url,
            headers: {
              'Content-Type': 'application/json',
              Authorization: res.token,
            },
            success: (data) => {
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

              arrayData.forEach((vl) => {
                const id = ObjectReader(vl, props.field.value)
                const text = ObjectReader(
                  vl,
                  typeof props.field.display === 'string'
                    ? props.field.display
                    : '',
                )

                results.push({
                  id,
                  text,
                })

                const option = new Option(
                  text,
                  id,
                  // props.isMultiple ? value.includes(id) : id === value,
                  // props.isMultiple ? value.includes(id) : id === value
                  true,
                  true,
                )
                selector.value.append(option).trigger('change')
              })

              resolve(results)
            },
          })
        })
      })
    },
    start: (id, debug = '') => {
      if (!isInit.value) {
        methods.prepare(id).then((res) => {
          methods.onInit(res).then(() => {
            isInit.value = true
            if (id) {
              if (props.api) {
                methods.getById(temp.value_).then(() => {
                  temp.ready++
                })
              } else {
                selector.value.val(temp.value_).trigger('change')
                temp.ready++
              }
            } else {
              temp.ready++
            }
          })
        })
      } else {
        if (id) {
          if (props.api) {
            methods.getById(temp.value_)
          } else {
            selector.value.val(temp.value_).trigger('change')
          }
        }
      }
    },
  }

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

  const isInit = ref(false)
  onMounted(() => {
    setTimeout(() => {
      methods.start(isById, 'mounted')
    }, 1000)
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value_ !== props.outVal) {
        temp.outsideUpdating = true
        temp.value_ = props.outVal
        if (props.isMultiple) {
          if (temp.value_.length) {
            setTimeout(() => methods.start(true, 'watch'), 500)
          } else {
            selector.value.val(temp.value_).trigger('change')
          }
        } else {
          if (temp.value_) {
            setTimeout(() => methods.start(true, 'watch'), 500)
          } else {
            selector.value.val(temp.value_).trigger('change')
          }
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
      console.log(type)
    },
    rules: ['required']
  }
`)
</script>
