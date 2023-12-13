<template>
  <div>
    <div class="grid grid-cols-12 mt-2 z-20 relative">
      <div class="col-span-6 flex">
        <input
          v-if="globalSearch"
          v-model="searchTerm"
          type="text"
          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 ml-1 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
          placeholder="Search..."
          style="max-width: 200px"
          :disabled="data.isLoading"
        />
        <div class="tooltip" :data-tip="$t('global.preview')">
          <button
            v-if="data.actions.preview"
            class="btn btn-sm text-blue-500 hover:bg-blue-400 border-blue-400 hover:border-blue-400 btn-outline ml-1.5"
            @click="data.actions.preview"
          >
            <i class="fal fa-search"></i>
          </button>
        </div>
        <slot name="actions-custom" />
        <div class="tooltip" :data-tip="$t('global.edit')">
          <button
            v-if="data.actions.edit"
            class="btn btn-sm text-yellow-600 hover:bg-yellow-600 border-yellow-600 hover:border-yellow-600 btn-outline ml-1.5"
            @click="data.actions.edit"
          >
            <i class="fal fa-pencil-alt"></i>
          </button>
        </div>
        <div class="tooltip" :data-tip="$t('global.delete')">
          <button
            v-if="data.actions.delete"
            class="btn btn-sm btn-error btn-outline ml-1.5"
            @click="data.actions.delete"
          >
            <i class="fal fa-trash-alt"></i>
          </button>
        </div>
        <div class="tooltip" :data-tip="$t('global.refresh')">
          <button
            class="btn btn-sm btn-success btn-outline ml-1.5"
            @click="methods.loadItems"
          >
            <i class="fal fa-sync-alt"></i>
          </button>
        </div>
        <div class="tooltip" :data-tip="$t('global.settings')">
          <button
            class="btn btn-sm btn-neutral btn-outline ml-1.5"
            @click="methods.onSettingColumns"
          >
            <i class="fal fa-cog"></i>
          </button>
        </div>
      </div>
      <div class="col-span-6 flex items-center justify-end"></div>
      <div class="col-span-12 custom-table-good-landing">
        <div
          :class="{
            'mt-5 z-10 relative': true,
          }"
        >
          <vue-good-table-custom
            mode="remote"
            :total-rows="data.totalRecords"
            :sort-options="{
              enabled: true,
            }"
            :select-options="{
              enabled: false,
              selectOnCheckboxOnly: true,
            }"
            :pagination-options="{
              enabled: true,
              pagination: true,
              mode: 'pages',
              setCurrentPage: data.serverParams.page,
              perPage: data.serverParams.perPage,
              perPageDropdownEnabled: true,
              perPageDropdown: [5, 10, 25, 50, 100],
              nextLabel: 'Next',
              prevLabel: 'Prev',
              rowsPerPageLabel: 'Rows per page',
              ofLabel: 'of',
              pageLabel: 'page', // for 'pages' mode
              allLabel: 'All',
            }"
            :group-options="{
              enabled: isGrouping,
              collapsable: isGrouping,
            }"
            :is-loading="data.isLoading"
            :rows="data.rows"
            :columns="data.fixColumns"
            style-class="vgt-table"
            :line-numbers="data.tableConfigs?.lineNumbers"
            :max-height="data.tableConfigs?.maxHeight"
            :fixed-header="
              data.tableConfigs?.fixedHeader ? clientWidth >= 1366 : false
            "
            :row-style-class="methods.rowStyleClassFn"
            @page-change="methods.onPageChange"
            @sort-change="methods.onSortChange"
            @column-filter="methods.onColumnFilter"
            @per-page-change="methods.onPerPageChange"
            @selected-rows-change="methods.selectionChanged"
            @row-click="methods.onRowClick"
          >
            <template #table-row="props">
              <div v-if="props.column.type === 'image'">
                <div v-if="props.column.conditional">
                  {{
                    props.column.conditional(
                      props.formattedRow,
                      data.rows,
                      props.index,
                    )
                  }}
                </div>
                <div v-else>
                  <img
                    v-if="props.formattedRow[props.column.field]"
                    :id="`${props.column.field}-${props.row.id}`"
                    :data-original="props.formattedRow[props.column.field]"
                    :src="props.formattedRow[props.column.field]"
                    alt=""
                    :style="props.column.style"
                  />
                </div>
              </div>
              <div v-else-if="props.column.type === 'rupiah'">
                <div v-if="props.column.conditional">
                  {{
                    props.column.conditional(
                      props.formattedRow,
                      data.rows,
                      props.index,
                    )
                  }}
                </div>
                <div v-else>
                  {{
                    IntToRupiah(props.formattedRow[props.column.field], 'Rp. ')
                  }}
                </div>
              </div>
              <div v-else-if="props.column.templates">
                <div v-if="props.column.conditional">
                  {{
                    props.column.conditional(
                      props.formattedRow,
                      data.rows,
                      props.index,
                    )
                  }}
                </div>
                <div v-else>
                  <span
                    v-html="
                      GetColumnTemplate(
                        props,
                        props.formattedRow[props.column.field] !== null
                          ? props.formattedRow[props.column.field]
                          : null,
                      )
                    "
                  ></span>
                </div>
              </div>
              <div v-else>
                <div v-if="props.column.conditional">
                  {{
                    props.column.conditional(
                      props.formattedRow,
                      data.rows,
                      props.index,
                    )
                  }}
                </div>
                <div v-else>
                  {{
                    props.formattedRow[props.column.field]
                      ? props.formattedRow[props.column.field]
                      : '-'
                  }}
                </div>
              </div>
            </template>
          </vue-good-table-custom>
        </div>
      </div>
    </div>
    <Modal v-model="showModal" width="w-3/12">
      <template #modal-title> List of Columns </template>
      <template #modal-body>
        <div class="pt-3">
          <div
            v-for="(item, index) in data.columns"
            :key="index"
            class="grid grid-cols-12 mb-3"
          >
            <div class="col-span-6">
              <label
                :for="`col-set-${index}`"
                :class="{
                  'block text-xs font-medium text-gray-900 dark:text-gray-300': true,
                }"
                @click="methods.selectColumnToggle()"
              >
                {{ item.label }}
              </label>
            </div>
            <div class="col-span-6 flex justify-end">
              <div class="flex relative items-center">
                <label :for="`col-set-${index}`" class="cursor-pointer">
                  <input
                    :id="`col-set-${index}`"
                    v-model="item.show"
                    type="checkbox"
                    class="sr-only"
                    @click="methods.selectColumnToggle()"
                  />
                  <div
                    class="w-11 h-6 bg-gray-200 rounded-full border border-gray-200 toggle-bg dark:bg-gray-700 dark:border-gray-600"
                  ></div>
                </label>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup lang="ts">
  import { inject, onMounted, ref, watch, defineProps, defineExpose } from 'vue'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  import Modal from '~/components/atoms/Modal.vue'
  import { DataLanding } from '~/types/pages'
  import {
    Axios,
    ClearArray,
    Cryptor,
    GetRandomString,
    IntToRupiah,
    Notyf,
    ObjectReader,
  } from '~/services'
  import { FixColumnFilter } from '~/services/Helpers/Popup'
  import GetColumnTemplate from '~/helpers/table/GetColumnTemplate'
  import { OldParams } from '~/types/components/atoms/forms/header'
  import Viewer from 'viewerjs'
  import 'viewerjs/dist/viewer.css'

  defineProps({
    globalSearch: {
      type: Boolean,
      default: true,
    },
  })

  const theme = inject('theme')
  const clientWidth = inject('clientWidth')
  const clientHeight = inject('clientHeight')
  const showModal = ref(false)
  const searchTerm = ref('')
  const isGrouping = ref(false)
  const data = inject('data') as DataLanding
  data.fixColumns = data.columns

  const methods = {
    selectColumnToggle: () => {
      const fixCols = []
      setTimeout(() => {
        data.columns.forEach((col) => {
          if (col.show) {
            fixCols.push(col)
          }
        })

        data.fixColumns = fixCols
      }, 100)
    },
    onSettingColumns: () => {
      showModal.value = !showModal.value
    },
    updateParams(newProps) {
      newProps = FixColumnFilter(newProps, data.fixColumns)
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
    onRowClick: (params) => {
      data.selectedRow = params

      if (isGrouping.value) {
        // const { unix } = params.row
        // ada bug ketika di click auto collapse
        // data.rows.forEach((row, rowI) => {
        //   row.children.forEach((child, childI) => {
        //     data.rows[rowI].children[childI].row_clicked = false
        //   })
        // })
        // data.rows.forEach((row, rowI) => {
        //   row.children.forEach((child, childI) => {
        //     if (child.unix === unix) {
        //       data.rows[rowI].children[childI].row_clicked = true
        //     }
        //   })
        // })
      } else {
        data.rows.forEach((row, rowI) => {
          data.rows[rowI].row_clicked = false
        })

        data.rows[params.pageIndex].row_clicked = true
      }
    },
    rowStyleClassFn: (row) => {
      return row.row_clicked ? 'bg-gray-100 dark:bg-gray-600' : ''
    },
    onReloadData: () => {
      data.isRefresh = true
      methods.updateParams({ page: 1 })
      methods.loadItems()
    },
    onGroupingResponse: (res) => {
      const groupKeys = []
      for (const key in data.groups) {
        if (key !== 'children') {
          groupKeys.push(key)
        }
      }

      const groupBy = (keys) => (array) =>
        array.reduce((objectsByKeyValue, obj) => {
          const value = keys.map((key) => obj[key]).join('-')
          objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(
            obj,
          )
          return objectsByKeyValue
        }, {})

      let startGrouping = groupBy(groupKeys)
      const groups = startGrouping(res)

      const finalResponse = []
      Object.entries(groups).forEach(([key, children], index) => {
        const keys = key.split('-')
        let newChildren = children as Array<any>
        // newChildren.forEach((nc, ncI) => {
        //   groupKeys.forEach((gk) => {
        //     delete newChildren[ncI][gk]
        //   })
        // })
        let before = {
          children: newChildren,
        }
        keys.forEach((ky, ix) => {
          if (!ix) {
            before[groupKeys[ix]] = `${ky} (${before.children.length})`
          } else {
            before[groupKeys[ix]] = ky
          }
        })
        finalResponse.push(before)
      })
      return finalResponse
    },
    loadItems() {
      data.isLoading = true
      const params = {}
      for (const key in data.api.parameters) {
        if (typeof data.api.parameters[key] === 'object') {
          params[key] = JSON.stringify(data.api.parameters[key])
        } else {
          params[key] = data.api.parameters[key]
        }
      }

      let overrideParams = function (oldParams: OldParams): OldParams {
        return oldParams
      }
      if (data.api.overrideParams !== undefined) {
        overrideParams = data.api.overrideParams
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

      const url =
        data.api.url.replace('/api/v1/', '/api/v1/table/') +
        '?' +
        new URLSearchParams(params)
      Axios({
        method: 'POST',
        url,
        data: data.serverParams,
      })
        .then((res: any) => {
          data.isLoading = false
          data.isRefresh = false
          ClearArray(data.rows)
          let rows = res.data.data
          rows.forEach((row, rowI) => {
            rows[rowI]['unix'] = GetRandomString(5)
          })
          if (data.groups) {
            isGrouping.value = true
            data.rows = methods.onGroupingResponse(rows)
          } else {
            data.rows = rows
          }

          setTimeout(() => {
            data.columns.forEach((col) => {
              if (col.type === 'image' && col.viewer === true) {
                const field = col.field
                data.rows.forEach((row) => {
                  const galley = document.getElementById(`${field}-${row.id}`)
                  new Viewer(galley, {
                    url: 'data-original',
                    toolbar: {
                      zoomIn: 1,
                      zoomOut: 1,
                      oneToOne: 1,
                      reset: 1,
                      prev: 1,
                      play: {
                        show: 0,
                      },
                      next: 1,
                      rotateLeft: 1,
                      rotateRight: 1,
                      flipHorizontal: 1,
                      flipVertical: 1,
                    },
                  })
                })
              }
            })
          }, 300)
          data.totalRecords = res.data.total
        })
        .catch((err) => {
          data.isLoading = false
          data.isRefresh = false
          if ([404].includes(err.response.status)) {
            if (err.response.data.data) {
              err.response.data.data.forEach((dt) => {
                Notyf({
                  type: 'error',
                  message: dt,
                  duration: 3000,
                  ripple: true,
                  dismissible: true,
                  position: {
                    x: 'right',
                    y: 'top',
                  },
                })
              })
            } else {
              Notyf({
                type: 'error',
                message: err.response.statusText,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            }
          } else if ([400, 500].includes(err.response.status)) {
            Notyf({
              type: 'error',
              message: err.response.statusText,
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          } else if ([401].includes(err.response.status)) {
            Notyf({
              type: 'error',
              message: err.response.data.message,
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          }
        })
    },
  }

  defineExpose({
    methods,
  })

  onMounted(() => {
    if (!data.disabledAutoLoad) {
      methods.loadItems()
    }
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
              data.columns.forEach((co) => {
                if (
                  co.type !== 'boolean' &&
                  co.type !== 'image' &&
                  co.type !== 'div' &&
                  co.filter === true
                ) {
                  temp = {
                    ...temp,
                    [co.field]: searchTerm.value,
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
