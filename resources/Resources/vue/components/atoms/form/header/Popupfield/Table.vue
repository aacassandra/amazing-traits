<template>
  <div class="grid grid-cols-12 mt-6 z-20 relative">
    <div class="col-span-6"></div>
    <div class="col-span-6 flex items-center justify-end">
      <input
        v-model="searchTerm"
        type="text"
        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 ml-1 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
        placeholder="Search..."
        style="max-width: 200px"
        :disabled="data.isLoading"
        @keyup="methods.onSearch"
      />
      <button
        type="button"
        class="ml-2 text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-1 focus:ring-blue-500 font-medium rounded-lg text-sm px-3 py-2.5 text-center dark:bg-gray-600 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-700 dark:focus:ring-blue-500"
        style="min-width: 40px"
        @click="methods.onReloadData"
      >
        <i class="fas fa-sync"></i>
      </button>
    </div>
    <div class="col-span-12">
      <div
        :class="{
          'mt-5 z-10 relative': true,
          'custom-table-good-single': !isMultiple,
          'custom-table-good-multiple': isMultiple,
        }"
      >
        <vue-good-table-custom
          mode="remote"
          :total-rows="data.totalRecords"
          :sort-options="{
            enabled: true,
          }"
          :select-options="{
            enabled: isMultiple,
            selectOnCheckboxOnly: false,
          }"
          :line-numbers="tableConfigs?.lineNumbers"
          :max-height="tableConfigs?.maxHeight"
          :fixed-header="
            tableConfigs?.fixedHeader ? clientWidth >= 1366 : false
          "
          :pagination-options="{
            enabled: true,
            pagination: true,
            mode: 'pages',
            setCurrentPage: data.serverParams.page,
            perPage: data.serverParams.perPage,
            perPageDropdown: [5, 10, 25, 50, 100],
            nextLabel: 'Next',
            prevLabel: 'Prev',
            rowsPerPageLabel: 'Rows per page',
            ofLabel: 'of',
            pageLabel: 'page', // for 'pages' mode
            allLabel: 'All',
          }"
          :search-options="{
            enabled: true,
            externalQuery: searchTerm,
          }"
          :is-loading="data.isLoading"
          :rows="data.rows"
          :columns="data.columns"
          style-class="vgt-table"
          :selected-count="fix.value.length"
          @page-change="methods.onPageChange"
          @sort-change="methods.onSortChange"
          @column-filter="methods.onColumnFilter"
          @per-page-change="methods.onPerPageChange"
          @selected-rows-change="methods.selectionChanged"
          @un-selected-row="methods.unSelectionChanged"
          @row-click="methods.onRowClick"
          @clear-all="methods.onClearAllRow"
        >
          <template #table-row="cprops">
            <div v-if="cprops.column.type === 'image'">
              <img
                v-if="cprops.formattedRow[cprops.column.field]"
                :src="cprops.formattedRow[cprops.column.field]"
                alt=""
                :style="cprops.column.style"
              />
            </div>
            <div v-else-if="cprops.column.type === 'div'">
              <span
                v-html="
                  cprops.column.html.replace(
                    ':value',
                    cprops.formattedRow[cprops.column.field],
                  )
                "
              ></span>
            </div>
            <div v-else>
              {{ cprops.formattedRow[cprops.column.field] || '-' }}
            </div>
          </template>
        </vue-good-table-custom>
      </div>
      <div v-if="isMultiple" class="col-span-12 flex justify-end mt-5">
        <button
          type="button"
          class="btn btn-sm btn-primary text-white"
          :disabled="data.isLoading"
          style="text-transform: none !important"
          @click="methods.onVerifySelectedRow"
        >
          <div>Verify</div>
          <div class="ml-1">
            <i class="fas fa-arrow-alt-right"></i>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable no-console */
  import { inject, onMounted, reactive, ref, watch, defineProps } from 'vue'
  import {
    ApiInterface,
    Element,
    OldParams,
  } from '~/types/components/atoms/forms/header'
  import { TableColumns } from '~/types/form/form-v1'
  import { FixColumnFilter } from '~/services/Helpers/Popup'
  import { TableConfigs } from '~/types/form/detail'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  import { Swal, t } from '~/services'

  const props = defineProps({
    api: {
      type: Object as () => ApiInterface,
      default: (): ApiInterface => {
        return null
      },
    },
    isMultiple: {
      type: Boolean,
      default: false,
    },
    field: {
      type: Object as () => {
        value: string
        display: string
        columns: TableColumns
      },
      default: null,
    },
    properties: {
      type: Object as () => Element,
      default: null,
    },
    tableConfigs: {
      type: Object as () => TableConfigs,
      default: () => {
        return {
          fixedHeader: true,
          maxHeight: '350px',
          lineNumbers: false,
        }
      },
    },
  })

  const searchTerm = ref('')
  const clientWidth = inject('clientWidth')
  const temp = inject('temp') as {
    value_: any
    valueBeforeBlur: any
    options_: Array<any>
    optionBeforeBlur: any
    fetchController: any
    apiOperationUrl: string
    search: string
    displayValue: string
    showModal: boolean
    value: any
    ready: number
  }

  const data = reactive({
    isLoading: false,
    columns: props.field.columns,
    rows: [],
    totalRecords: 0,
    serverParams: {
      columnFilters: {},
      sort: [],
      page: 1,
      perPage: 5,
    },
  })

  // Helper for multiple select only
  const fix = reactive({
    displayValue: '',
    value: [],
  })
  let cacheControl: RequestCache = 'default'
  const nativeEdit = inject<{ value: boolean }>('nativeEdit')
  const hasChange = ref(false)
  const compParentMethods = inject('compParentMethods') as any
  const methods = {
    onSearch: () => {
      //
    },
    updateParams(newProps) {
      newProps = FixColumnFilter(newProps, props.field.columns)
      data.serverParams = Object.assign({}, data.serverParams, newProps)
    },
    onPageChange(params) {
      hasChange.value = true
      methods.updateParams({ page: params.currentPage })
      methods.loadItems()
    },
    onPerPageChange(params) {
      hasChange.value = true
      methods.updateParams({ perPage: params.currentPerPage })
      methods.loadItems()
    },
    onSortChange(params) {
      methods.updateParams({
        sort: params,
      })
      methods.loadItems()
    },
    onColumnFilter(params) {
      methods.updateParams(params)
      methods.loadItems()
    },
    onRowClick: (params) => {
      // only execute on single select
      if (!props.isMultiple) {
        temp.displayValue = data.rows[params.pageIndex][props.field.display]
        temp.value = data.rows[params.pageIndex][props.field.value]
        temp.value_ = data.rows[params.pageIndex][props.field.value]
        temp.showModal = false

        compParentMethods.onChange({
          type: 'change',
          value: data.rows[params.pageIndex][props.field.value],
        })
      }
    },
    onReloadData: () => {
      methods.loadItems()
    },
    setOptions: (val: Array<any>) => {
      const fixOptions = val
      const currentValue = fix.value

      fixOptions.forEach((opt, optI) => {
        if (props.isMultiple) {
          if (
            Array.isArray(currentValue) &&
            currentValue.includes(opt[props.field.value])
          ) {
            fixOptions[optI].vgtSelected = true
          }
        }
      })

      data.rows = fixOptions
      temp.options_ = fixOptions
    },
    loadItems() {
      data.isLoading = true
      return new Promise((resolve, reject) => {
        if (!props.api) {
          resolve(temp.options_)
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
          url = props.api.url.replace('/api/v1/', '/api/v1/table/')
        } else if (props.api.model !== undefined) {
          url = temp.apiOperationUrl + `/table/${props.api.model}`
        }

        let params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            if (key === 'paginate' && !hasChange.value) {
              data.serverParams.perPage = val
            } else if (key === 'page' && !hasChange.value) {
              data.serverParams.page = val
            } else {
              params[key] = typeof val === 'object' ? JSON.stringify(val) : val
            }
          })
        }
        cacheControl = props.api.cache === true ? 'force-cache' : 'no-cache'

        let overrideParams = function (
          _: Element,
          oldParams: OldParams,
        ): OldParams {
          return oldParams
        }
        if (typeof props.api.overrideParams === 'function') {
          overrideParams = props.api.overrideParams
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

        if (temp.value_) {
          if (props.isMultiple && temp.value_.length) {
            params.wherenotin = `${props.field.value}=${JSON.stringify(
              temp.value_,
            )}`
          } else if (temp.value_) {
            //
          }
        }

        url = url + '?' + new URLSearchParams(params)
        const cookieToken = localStorage.getItem('_token')
        fetch(url, {
          method: 'POST',
          mode: 'cors',
          cache: cacheControl,
          credentials: 'same-origin',
          redirect: 'follow',
          body: JSON.stringify(data.serverParams),
          referrerPolicy: 'no-referrer',
          headers: {
            'Content-Type': 'application/json',
            Authorization: cookieToken,
          },
          signal,
        })
          .then((response) => response.json())
          .then((res) => {
            if (res.success) {
              let fixedData = props.api.root ? res[props.api.root] : res
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

              data.isLoading = false
              data.totalRecords = res.total
              methods.setOptions(arrayData)
              resolve(temp.options_)
            } else {
              Swal.basic({
                icon: 'error',
                title: `Error ${res.statusCode}!`,
                html: res.statusMessage,
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
    selectionChanged: (params) => {
      params.selectedRows.forEach((row) => {
        let match = 0
        if (fix.value.includes(row[props.field.value])) {
          match++
        }

        if (!match) {
          fix.value.push(row[props.field.value])
        }
      })
    },
    unSelectionChanged: (param) => {
      fix.value.forEach((value, valueI) => {
        if (param[props.field.value] === value) {
          fix.value.splice(valueI, 1)
        }
      })
    },
    onClearAllRow: () => {
      fix.value = []
      data.rows.forEach((row, rowI) => {
        data.rows[rowI].vgtSelected = false
      })
    },
    onVerifySelectedRow: () => {
      nativeEdit.value = true
      const fixVal = []
      fix.value.forEach((vl) => {
        fixVal.push(vl)
      })

      temp.value = fixVal
      temp.value_ = fixVal

      temp.displayValue = fixVal.length ? `${fixVal.length} items selected` : ''
      temp.showModal = false
      setTimeout(() => {
        nativeEdit.value = false
      }, 300)
    },
  }

  onMounted(() => {
    if (props.isMultiple) {
      temp.value.forEach((vl) => {
        fix.value.push(vl)
      })
    }
    methods.loadItems()
  })

  let calcMover = 0
  let calcInterver
  watch(searchTerm, () => {
    calcMover += 1
    if (!calcInterver) {
      let match = 0
      calcInterver = setInterval(() => {
        const currentMover = calcMover
        setTimeout(() => {
          if (currentMover === calcMover) {
            match += 1
            if (match >= 3) {
              clearInterval(calcInterver)
              calcInterver = null
              let temp = {}
              props.field.columns.forEach((co) => {
                if (co.type !== 'image' && co.type !== 'div') {
                  if (co.filter === true) {
                    temp = {
                      ...temp,
                      [co.field]: searchTerm.value,
                    }
                  }
                }
              })

              methods.updateParams({ columnFilters: temp })
              methods.loadItems()
            }
          } else {
            match = 0
          }
        }, 200)
      }, 500)
    }
  })
</script>
<style scoped>
  .custom-table-good :deep(table tr) {
    max-height: 7px;
  }
</style>
