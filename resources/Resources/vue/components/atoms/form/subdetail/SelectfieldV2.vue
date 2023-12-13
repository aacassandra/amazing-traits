<template>
  <div>
    <div class="flex items-center select2-detail">
      <select
        v-if="selectField"
        :id="elementId"
        :multiple="isMultiple"
        :class="{
          [`${elementId}-select2`]: true,
          hidden: true,
          'is-invalid':
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
      >
        <option v-if="placeholder && !isMultiple" value="">
          {{ t(placeholder) }}
        </option>
        <option
          v-for="(item, i) in tempVal_.options_"
          :key="i"
          :value="item.value"
        >
          {{ t(item.text) }}
        </option>
      </select>
      <select
        v-else
        :id="elementId"
        :multiple="isMultiple"
        :class="{
          [`${elementId}-select2`]: true,
          hidden: true,
          'is-invalid':
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
      >
        <option v-if="placeholder && !isMultiple" value="">
          {{ t(placeholder) }}
        </option>
        <option
          v-for="(item, i) in tempVal_.options_"
          :key="i"
          :value="item"
          :disabled="disabledItems.includes(item)"
        >
          {{ t(item) }}
        </option>
      </select>
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
  import { ObjectReader, ObjectUpdater, t, UpdateWhereParams } from '~/services'
  import { OldParams } from '~/types/components/atoms/forms/header'
  import { FormSubDetail } from '~/types/form/subdetail'
  import getFixParams from '~/helpers/form/select2/getFixParams'
  import { Lang } from '~/types/form/form-v1'
  import { Field } from '~/types/components/atoms/forms/header/select'

  interface Prepare {
    url: string
    token: string
  }

  defineComponent({
    name: 'SelectfieldV2Detail',
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
    disabledItems: {
      type: Array as () => Array<any>,
      default: () => [],
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
    clearable: {
      type: Boolean,
      default: false,
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
      type: Array as () => Array<{ [key: string]: any } | string>,
      default: () => {
        return []
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element

  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const tempVal = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )

  const tempVal_ = reactive({
    value_: null,
    options_reset: false,
    options_: [],
    lockOptions: false,
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    outsideUpdating: false,
  })

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)

  const selector = ref(null)
  const methods = {
    getApiUrl: () => {
      let url = ''
      if (props.api.url !== undefined) {
        url = props.api.url
      } else if (props.api.model !== undefined) {
        url = tempVal_.apiOperationUrl + `/${props.api.model}`
      }

      return url
    },
    onInit: (pre: Prepare) => {
      return new Promise((resolve) => {
        selector.value = $(`.${elementId.value}-select2`)
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

              const currentValue = selector.value.val()
              let whereColumn = props.selectField.value
              if (props.api.model) {
                whereColumn = `${props.api.model}.${props.selectField.value}`
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
                    field: props.selectField,
                    model: props.api.model || '',
                  })
                }
              } else if (props.isMultiple) {
                if (Array.isArray(tempVal.value) && tempVal.value.length) {
                  params.wherenotin = `${whereColumn}=${JSON.stringify(
                    currentValue,
                  )}`
                } else {
                  params = getFixParams({
                    params,
                    value: currentValue,
                    term: query.term || '',
                    isMultiple: true,
                    field: props.selectField,
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

              const results = []

              arrayData.forEach((vl) => {
                if (typeof props.selectField.display === 'string') {
                  results.push({
                    id: ObjectReader(vl, props.selectField.value),
                    text: ObjectReader(vl, props.selectField.display),
                  })
                }
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
                if (typeof props.selectField.display === 'string') {
                  tempVal_.options_.push({
                    id: ObjectReader(vl, props.selectField.value),
                    text: ObjectReader(vl, props.selectField.display),
                  })
                }
              } else if (typeof vl === 'string' || typeof vl === 'number') {
                tempVal_.options_.push(vl)
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
              tempVal_.value_ = ''
            }
            tempVal_.lockOptions = true
            methods.onChange({ type: 'clearing' })
          })
          .on('select2:clear', () => {
            if (props.isMultiple) {
              tempVal_.lockOptions = true
              // tempVal_.value_ = []
              selector.value.val(null).trigger('change')
              setTimeout(() => {
                tempVal_.lockOptions = false
              }, 500)
            }
            methods.onChange({ type: 'clear' })
          })
          .on('change', function () {
            const temps = selector.value.select2('data')
            if (props.isMultiple === true) {
              const tempArr: any = []
              temps.forEach((e) => {
                tempArr.push(e.id)
              })
              tempVal.value = tempArr
            } else {
              tempVal.value = temps.length ? temps[0].id : ''
            }

            tempVal_.value_ = selector.value.val()

            methods.onChange({ type: 'change' })
          })

        methods.onChange({ type: 'change' })

        resolve(true)
      })
    },
    onChange: (evt) => {
      nativeEdit.value = true

      // bugfix size of multiple select
      if (props.isMultiple) {
        const selectorx = $(
          '.select2-container--default .select2-selection--multiple',
        )
        const tempArr = tempVal_.value_ as Array<any>
        if (tempArr.length) {
          selectorx.css('height', 'unset')
          selectorx.css('min-height', '33px')
          selectorx.css('padding-bottom', '0px')
        } else {
          selectorx.css('height', 'unset')
          selectorx.css('min-height', '33px')
          selectorx.css('padding-bottom', '0px')
        }
      }

      if (props.listener) {
        props.listener(element, evt.type, tempVal_.value_)
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
            url = tempVal_.apiOperationUrl + `/${props.api.model}`
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

            if (!props.isMultiple && tempVal_.value_) {
              params.wherenotin = `${props.selectField.value}=["${tempVal_.value_}"]`
            } else if (
              props.isMultiple &&
              Array.isArray(tempVal_.value_) &&
              tempVal_.value_.length
            ) {
              params.wherenotin = `${props.selectField.value}=${JSON.stringify(
                tempVal_.value_,
              )}`
            }
          } else {
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
          selector.value.val(tempVal.value).trigger('change')
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
                if (typeof props.selectField.display === 'string') {
                  const id = ObjectReader(vl, props.selectField.value)
                  const text = ObjectReader(vl, props.selectField.display)

                  results.push({
                    id,
                    text,
                  })

                  const option = new Option(
                    text,
                    id,
                    // props.isMultiple ? value.includes(id) : id === value,
                    // props.isMultiple ? value.includes(id) : id === value,
                    true,
                    true,
                  )
                  selector.value.append(option).trigger('change')
                }
              })

              resolve(true)
            },
          })
        })
      })
    },
  }

  let isById = false
  if (props.default) {
    // set default
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
      methods.prepare(isById).then((res) => {
        methods.onInit(res).then(() => {
          if (isById) {
            methods.getById(tempVal_.value_).then(() => {
              form.ready++
            })
          } else {
            form.ready++
          }
        })
      })
    }, 100)
  })

  watch(tempVal, () => {
    if (!nativeEdit.value) {
      tempVal_.outsideUpdating = true
      tempVal_.value_ = tempVal.value
      if (tempValParent.value[props.rowIndex] !== undefined) {
        if (props.isMultiple) {
          if (tempVal_.value_.length) {
            methods.getById(tempVal_.value_)
          } else {
            selector.value.val(tempVal_.value_).trigger('change')
          }
        } else {
          if (tempVal_.value_) {
            methods.getById(tempVal_.value_)
          } else {
            selector.value.val(tempVal_.value_).trigger('change')
          }
        }
      }
    }
  })
</script>
