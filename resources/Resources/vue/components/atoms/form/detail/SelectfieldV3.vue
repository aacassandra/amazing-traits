<template>
  <div>
    <div class="flex items-center selectize-detail">
      <select
        v-if="selectField"
        :id="elementId"
        :multiple="isMultiple"
        :class="{
          [`${elementId}-selectize`]: true,
          'is-invalid':
            temp.form.rows[rowIndex].errors &&
            temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
        }"
        :disabled="disabled"
        :readonly="readonly"
      >
        <!-- -->
      </select>
      <select
        v-else
        :id="elementId"
        :multiple="isMultiple"
        :class="{
          [`${elementId}-selectize`]: true,
          'is-invalid':
            temp.form.rows[rowIndex].errors &&
            temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
        }"
        :disabled="disabled"
        :readonly="readonly"
      >
        <!-- -->
      </select>
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
    defineProps,
    inject,
    onMounted,
    reactive,
    ref,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { ApiInterface, Element } from '~/types/components/atoms/forms/detail'
  import {
    IntToRupiah,
    ObjectReader,
    ObjectUpdater,
    Swal,
    t,
    GetRandomString,
  } from '~/services'
  import { OldParams } from '~/types/components/atoms/forms/header'
  import { Lang } from '~/types/form/form-v1'

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
      type: Object as () => { value: string; display: string },
      default: null,
    },
    isMultiple: {
      type: Boolean,
      default: false,
    },
    clearable: {
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
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element

  const isReady = ref(false)

  let cacheControl: RequestCache = 'default'
  const tempVal = ref(
    computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  )

  const tempVal_ = reactive({
    value_: null,
    lockValue: false,
    valueBeforeBlur: null,
    options_: [],
    optionBeforeBlur: null, // is object
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
    outsideUpdating: false,
  })

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

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

      selector.value = $(`.${elementId.value}-selectize`)
      selector.value.selectize({
        // options: [{ value: '', text: 'Select more' }],
        // items: [''],
        placeholder: props.placeholder,
        sortField: [
          {
            field: 'text',
            direction: 'asc',
          },
          {
            field: '$score',
          },
        ],
        create: false,
        plugins,
        onChange: (value) => {
          if (!tempVal_.lockValue) {
            tempVal.value = value || null
            tempVal_.value_ = value || null
          } else if (value) {
            if (props.isMultiple && value.length) {
              tempVal.value = value
              tempVal_.value_ = value
            } else if (!props.isMultiple) {
              tempVal.value = value
              tempVal_.value_ = value
            }
          }

          if (props.isMultiple) {
            if (!tempVal_.value_.length) {
              setTimeout(() => {
                $(
                  `.${elementId.value}-selectize + .selectize-control > a.clear`,
                ).css('display', 'none')
              }, 1)
            }
          }

          methods.onChange({ type: 'change' })
        },
        onFocus: () => {
          tempVal_.lockValue = true
          methods.onChange({ type: 'focus' })
        },
        onBlur: () => {
          methods.load()
          methods.onChange({ type: 'blur' })
        },
        onDelete: (value) => {
          if (props.isMultiple) {
            if (value.length === 1) {
              tempVal.value = []
              tempVal_.value_ = []
            }
          } else {
            tempVal.value = null
            tempVal_.value_ = null
          }
        },
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
    onChange: (evt) => {
      nativeEdit.value = true

      if (props.listener) {
        props.listener(element, evt.type, tempVal.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    setOptions: (val: Array<any>, isById = false, debug = false) => {
      return new Promise((resolve) => {
        const selectize = selector.value[0].selectize

        selectize.clear()
        selectize.clearOptions()
        let selectedOptions = []

        if (tempVal_.value_) {
          if (props.isMultiple && tempVal_.value_.length) {
            tempVal_.options_.forEach((opt) => {
              if (tempVal_.value_.includes(opt.value)) {
                selectedOptions.push(opt)
              }
            })
          } else {
            tempVal_.options_.forEach((opt) => {
              if (tempVal_.value_ === opt.value) {
                selectedOptions.push(opt)
              }
            })
          }
        }
        selectedOptions = JSON.parse(JSON.stringify(selectedOptions))

        const newOptions = []

        val.forEach((vl) => {
          newOptions.push({
            text: ObjectReader(vl, props.selectField.display),
            value: ObjectReader(vl, props.selectField.value),
          })
        })

        tempVal_.options_ = selectedOptions.concat(newOptions)

        selectize.load(function (callback) {
          callback(tempVal_.options_)
        })

        if (isById) {
          // to update current value when id is encrypted
          if (!props.isMultiple) {
            tempVal.value = tempVal_.options_[0].value
            tempVal_.value_ = tempVal_.options_[0].value
            selector.value[0].selectize.setValue(tempVal_.value_)
          } else {
            const values = []
            tempVal_.options_.forEach((opt) => {
              values.push(opt.value)
            })

            tempVal.value = values
            tempVal_.value_ = values
            selector.value[0].selectize.setValue(tempVal_.value_)
          }

          tempVal_.lockValue = true
          methods.load(false, true).then(() => {
            tempVal_.lockValue = false
            resolve(true)
          })
        } else {
          if (tempVal_.value_) {
            if (props.isMultiple) {
              if (tempVal_.value_.length) {
                selectize.setValue(tempVal_.value_)
              }
            } else {
              selectize.setValue(tempVal_.value_)
            }
            tempVal_.lockValue = false
            resolve(true)
          } else {
            resolve(true)
          }
        }
      })
    },
    getValueFull: () => {
      if (typeof props.reduce === 'function') {
        return props.reduce(tempVal_.options_ ?? [])
      } else {
        return tempVal_.options_ ?? []
      }
    },
    load: (isById = false, debug = false) => {
      return new Promise((resolve, reject) => {
        if (!props.api) {
          resolve(tempVal_.options_)
        }

        let signal

        try {
          tempVal_.fetchController = new AbortController()
          signal = tempVal_.fetchController.signal
        } catch (e) {
          // eslint-disable-next-line no-console
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

          if (props.isMultiple && tempVal_.value_.length) {
            params.wherenotin = `${props.selectField.value}=${JSON.stringify(
              tempVal_.value_,
            )}`
          } else if (!props.isMultiple && tempVal_.value_) {
            params.wherenotin = `${props.selectField.value}=["${tempVal_.value_}"]`
          }
        } else if (isById === true) {
          if (tempVal_.outsideUpdating) {
            // updating by id from outside value
            if (props.isMultiple) {
              params.wherein = `${props.selectField.value}=${JSON.stringify(
                tempVal_.value_,
              )}`
            } else {
              if (typeof tempVal_.value_ === 'string') {
                params.where = `this.${props.selectField.value}=':${tempVal_.value_}:'`
              } else {
                params.where = `this.${props.selectField.value}=${tempVal_.value_}`
              }
            }
          } else {
            // updating by id from default value
            if (props.isMultiple) {
              params.wherein = `${props.selectField.value}=${JSON.stringify(
                props.default,
              )}`
            } else {
              if (typeof props.default === 'string') {
                params.where = `this.${props.selectField.value}=':${props.default}:'`
              } else {
                params.where = `this.${props.selectField.value}=${props.default}`
              }
            }
          }
        }

        // url = url + '?' + new URLSearchParams(params)
        // const cookieToken = useCookie('_token')
        // fetch(url, {
        //   method: 'GET',
        //   mode: 'cors',
        //   cache: cacheControl,
        //   credentials: 'same-origin',
        //   redirect: 'follow',
        //   referrerPolicy: 'no-referrer',
        //   headers: {
        //     'Content-Type': 'application/json',
        //     Authorization: cookieToken.value,
        //   },
        //   signal,
        // })
        //   .then((response) => {
        //     return response.json()
        //   })
        //   .then(async (data) => {
        //     if (data.success) {
        //       let fixedData = props.api.root ? data[props.api.root] : data
        //
        //       if (!Array.isArray(fixedData)) {
        //         fixedData = [fixedData]
        //       }
        //
        //       const arrayData = fixedData.map(function (row) {
        //         const func = props.api.transform
        //         if (typeof func === 'function') {
        //           return func(row)
        //         }
        //         return row
        //       })
        //
        //       await methods.setOptions(arrayData, isById, debug)
        //
        //       if (tempVal_.outsideUpdating) {
        //         tempVal_.outsideUpdating = false
        //       }
        //
        //       resolve(true)
        //     } else {
        //       await Swal.basic({
        //         icon: 'error',
        //         title: `Error ${data.statusCode}!`,
        //         html: data.statusMessage,
        //         button: {
        //           confirm: 'default',
        //           size: 'md',
        //         },
        //       })
        //       resolve(tempVal_.options_)
        //     }
        //   })
        //   .catch((err) => {
        //     tempVal_.options_ = []
        //     reject(err)
        //   })
      })
    },
  }

  let isById = false
  if (props.default) {
    // set default
    if (tempVal.value) {
      tempVal_.value_ = tempVal.value
    } else {
      tempVal_.value_ = props.default
    }
    isById = true
  } else {
    if (tempVal.value) {
      tempVal_.value_ = tempVal.value
    } else {
      tempVal_.value_ = props.isMultiple ? [] : ''
    }
  }

  onMounted(() => {
    setTimeout(() => {
      methods.onInit()
      setTimeout(() => {
        methods.load(isById).then(() => {
          setTimeout(() => {
            temp.form.ready++
            isReady.value = true
          }, 300)
        })
      }, 100)
    }, 1000)
  })

  watch(tempVal, () => {
    // if (isReady.value && temp.form.rows[props.rowIndex] && !nativeEdit.value) {
    //   tempVal_.outsideUpdating = true
    //   tempVal_.value_ = tempVal.value
    //   methods.load(true)
    // }
  })
</script>
