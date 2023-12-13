<template>
  <div class="grid grid-cols-12 mt-6 z-20 relative">
    <div v-if="options.globalSearch" class="col-span-6"></div>
    <div
      v-if="options.globalSearch"
      class="col-span-6 flex items-center justify-end"
    >
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
          'custom-table-good-single': !options.isMultiple,
          'custom-table-good-multiple': options.isMultiple,
        }"
      >
        <vue-good-table-custom
          mode="remote"
          :total-rows="data.totalRecords"
          :sort-options="{
            enabled: true,
          }"
          :select-options="{
            enabled: options.isMultiple && !options.tableConfigs?.selectAll,
            selectOnCheckboxOnly: false,
          }"
          :max-height="options.tableConfigs?.maxHeight"
          :fixed-header="
            options.tableConfigs.fixedHeader ? clientWidth >= 1366 : false
          "
          :pagination-options="{
            enabled: !options.tableConfigs?.selectAll,
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
          @page-change="methods.onPageChange"
          @sort-change="methods.onSortChange"
          @column-filter="methods.onColumnFilter"
          @per-page-change="methods.onPerPageChange"
          @selected-rows-change="methods.selectionChanged"
          @row-click="methods.onRowClick"
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
            <div v-else-if="cprops.column.type === 'rupiah'">
              {{
                IntToRupiah(cprops.formattedRow[cprops.column.field], 'Rp. ')
              }}
            </div>
            <div v-else>
              {{
                cprops.formattedRow[cprops.column.field] !== null
                  ? cprops.formattedRow[cprops.column.field]
                  : '-'
              }}
            </div>
          </template>
        </vue-good-table-custom>
      </div>
      <div v-if="options.isMultiple" class="col-span-12 flex justify-end mt-5">
        <button
          type="button"
          class="btn btn-sm btn-primary"
          :class="options.className.verivy"
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
  import { Ref, UnwrapRef } from 'vue'
  import { IntToRupiah } from '../../../../services'
  import { OldParams } from '~/types/components/atoms/forms/header'
  import { FixColumnFilter } from '~/services/Helpers/Popup'
  import { AddRowFromPopupOptions } from '~/types/form/detail'
  import { Swal } from '~/services'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  const props = defineProps({
    rows: {
      type: Array as () => Array<any>,
      required: true,
    },
    options: {
      type: Object as () => AddRowFromPopupOptions,
      required: true,
    },
    onVerified: {
      type: Function,
      default: null,
    },
  })

  const searchTerm = ref('')
  const hasInit = ref(false)
  const popupTemp = inject('popupTemp') as {
    fetchController: any
    apiOperationUrl: string
    search: string
    showModal: boolean
    modalLock: boolean
  }
  const tempVal = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  const clientWidth = inject('clientWidth')
  const data = reactive({
    isLoading: false,
    columns: props.options.columns,
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
  const theme = inject('theme')
  const parentMethods = inject('subDtlParentMethods') as any
  const methods = {
    onSearch: () => {
      //
    },
    updateParams(newProps) {
      newProps = FixColumnFilter(newProps, props.options.columns)
      data.serverParams = Object.assign({}, data.serverParams, newProps)
    },
    onPageChange(params) {
      methods.updateParams({ page: params.currentPage })
      methods.loadItems()
    },
    onPerPageChange(params) {
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
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    onRowClick: (params) => {
      // only execute on single select
      if (!props.options.isMultiple) {
        fix.value = [params.row]
        parentMethods.onAddNewList(fix.value)
        popupTemp.showModal = false
      }
    },
    onReloadData: () => {
      methods.loadItems()
    },
    loadItems() {
      data.isLoading = true
      return new Promise((resolve, reject) => {
        let signal

        try {
          popupTemp.fetchController = new AbortController()
          signal = popupTemp.fetchController.signal
        } catch (e) {
          console.log(e)
        }

        let url = ''
        if (props.options.api.url !== undefined) {
          url = props.options.api.url.replace('/api/v1/', '/api/v1/table/')
        } else if (props.options.api.model !== undefined) {
          url = popupTemp.apiOperationUrl + `/table/${props.options.api.model}`
        }

        const params: any = {}
        if (props.options.api.parameters) {
          Object.entries(props.options.api.parameters).forEach(([key, val]) => {
            if (!hasInit.value && key === 'paginate') {
              data.serverParams.perPage = val
            } else if (!hasInit.value && key === 'page') {
              data.serverParams.page = val
            } else if (!['paginate', 'page'].includes(key)) {
              // data.serverParams.columnFilters[key] = val
              params[key] = val
            }
          })
        }
        cacheControl =
          props.options.api.cache === true ? 'force-cache' : 'no-cache'

        let overrideParams = function (oldParams: OldParams): OldParams {
          return oldParams
        }
        if (typeof props.options.api.overrideParams === 'function') {
          overrideParams = props.options.api.overrideParams
        }
        if (props.options.api.overrideParams !== undefined) {
          overrideParams = props.options.api.overrideParams
        }
        const newParams = overrideParams(params)
        if (typeof newParams === 'boolean' && !newParams) {
          return false
        }

        Object.assign(params, newParams)
        for (const key in params) {
          if (params[key] == null) {
            delete params[key]
          }
        }

        if (props.rows.length) {
          const ids = []
          props.rows.forEach((row) => {
            if (!row.deleted) {
              ids.push(row[props.options.uniqueColumn])
            }
          })
          if (ids.length) {
            params.wherenotin = `${props.options.uniqueColumn}=${JSON.stringify(
              ids,
            )}`
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
              let fixedData = props.options.api.root
                ? res[props.options.api.root]
                : res
              if (!Array.isArray(fixedData)) {
                fixedData = [fixedData]
              }
              const arrayData = fixedData.map(function (row) {
                const func = props.options.api.transform
                if (typeof func === 'function') {
                  return func(row)
                }
                return row
              })

              hasInit.value = true
              data.isLoading = false
              data.totalRecords = res.total
              data.rows = arrayData
              resolve(true)
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
              resolve(false)
            }
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    selectionChanged: (params) => {
      fix.value = []
      params.selectedRows.forEach((row) => {
        fix.value.push(row)
      })
    },
    onVerifySelectedRow: () => {
      if (props.options.tableConfigs?.selectAll) {
        const run = () => {
          parentMethods.onAddNewList(data.rows, true)
          popupTemp.showModal = false
          if (props.onVerified) {
            props.onVerified()
          }
        }

        let hasRows = 0
        tempVal.value.forEach((row) => {
          if (!row.deleted) {
            hasRows++
          }
        })
        if (hasRows) {
          popupTemp.modalLock = true
          Swal.confirm({
            title: 'Replacement?',
            html: 'Do you want to replace the detail data? click Yes to continue',
            icon: 'warning',
            button: {
              confirm: 'primary',
              size: 'md',
            },
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
          }).then((result: any) => {
            if (result.isConfirmed) {
              run()
            }
            setTimeout(() => (popupTemp.modalLock = false), 100)
          })
        } else {
          run()
        }
      } else {
        parentMethods.onAddNewList(fix.value)
        popupTemp.showModal = false
      }
    },
  }

  onMounted(() => {
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
              let temporary = {}
              props.options.columns.forEach((co) => {
                if (
                  co.type !== 'image' &&
                  co.type !== 'div' &&
                  co.filter === true
                ) {
                  temporary = {
                    ...temporary,
                    [co.field]: searchTerm.value,
                  }
                }
              })

              methods.updateParams({ columnFilters: temporary })
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
