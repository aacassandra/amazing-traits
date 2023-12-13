<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
      <div class="col-span-6 flex justify-end">
        <div class="tabs">
          <a
            :class="{ 'tab tab-bordered': true, 'tab-active': view === 'list' }"
            @click="() => (view = 'list')"
          >
            <i class="fa-regular fa-list-ul mr-2"></i> List
          </a>
          <a
            :class="{
              'tab tab-bordered': true,
              'tab-active': view === 'board',
            }"
            @click="() => (view = 'board')"
          >
            <i class="fa-light fa-objects-align-top mr-2"></i> Board
          </a>
        </div>
      </div>
    </div>

    <div
      v-if="view === 'board'"
      class="absolute px-10"
      :style="{
        width: `100%`,
        overflowX: 'auto',
        height: `${clientHeight - 127}px`,
        left: 0,
      }"
    >
      <div class="flex mt-10">
        <div
          v-for="(item, itemIndex) in items"
          :key="itemIndex"
          style="min-width: 250px"
          class="mr-6"
        >
          <div
            :class="`border-t-2 border-t-${item.bgColor.replace(
              'bg-',
              '',
            )} shadow-md shadow-gray-300 dark:shadow-gray-800 rounded-lg px-4 py-3 flex items-center w-full`"
          >
            {{ item.text }}
            <div
              class="border px-1 flex items-center rounded-lg text-xs ml-2"
              style="height: 20px"
            >
              {{ item.items.length }}
            </div>
          </div>
          <div class="flex flex-col">
            <draggable
              v-model="item.items"
              group="task"
              item-key="id"
              @start="drag = true"
              @end="drag = false"
              @change="methods.onChangeStatusFromDraggableItem"
            >
              <template #item="{ element, index }">
                <div
                  class="cursor-pointer hover:bg-base-200 shadow-md shadow-gray-300 dark:shadow-gray-800 rounded-lg px-4 py-3 flex flex-col w-full mt-5"
                  @click="methods.onShowingModal(itemIndex, index)"
                >
                  <div class="text-xs">
                    <i>{{ element.stack_category }}</i>
                  </div>
                  <div class="font-medium text-xs mt-2">
                    <b>[{{ element.code }}]</b> {{ element.user_story_title }}
                  </div>
                  <div class="text-xs mt-2 flex items-center">
                    <div class="flex-grow">
                      <i
                        class="fa-duotone fa-calendar-days text-blue-800 mr-1"
                      ></i>
                      {{
                        moment(element.due_date, 'YYYY-MM-DD HH:mm').format(
                          'D MMM YYYY HH:mm',
                        )
                      }}
                    </div>
                    <div
                      v-if="element.isProgress"
                      class="flex-grow-0 flex justify-end"
                    >
                      <i class="fad fa-spinner-third fa-spin"></i>
                    </div>
                  </div>
                </div>
              </template>
            </draggable>
          </div>
        </div>
        <div style="width: 2.5rem" class="opacity-0">s</div>
      </div>
    </div>
  </div>

  <Modal
    v-model="modalActive"
    width="w-[89%]"
    position="center"
    :no-padding="true"
  >
    <template #modal-title>
      <Breadcrumb :data="modalData.breadcrumb" />
    </template>
    <template #modal-header>
      <div class="flex items-center px-6">
        <div class="dropdown dropdown-bottom mr-4 flex-grow-0">
          <label
            tabindex="0"
            :class="`rounded-md btn btn-sm m-1 normal-case ${
              modalData.currentData['status.value_2']
            } hover:${methods.getBgHover()} ${
              modalData.currentData['status.value_3']
            }`"
          >
            {{ modalData.currentData['status.value_1'] }}
          </label>
          <ul
            tabindex="0"
            class="dropdown-content z-[1] menu p-2 shadow-lg border border-base-200 shadow-base-200 bg-base-100 rounded-box w-52"
          >
            <li v-for="(item, index) in items" :key="index">
              <a
                class="flex items-center"
                @click="methods.onChangeStatus(item)"
              >
                <div
                  :style="{ width: '10px', height: '10px' }"
                  :class="`flex-grow-0 ${item.bgColor} mr-3 rounded-sm`"
                ></div>
                <div class="flex-grow">
                  {{ item.text }}
                </div>
                <div
                  v-if="modalData.currentData['status.value_1'] === item.text"
                  class="flex-grow-0 text-success"
                >
                  <i class="fa-solid fa-check"></i>
                </div>
              </a>
            </li>
          </ul>
        </div>
        <div class="font-medium text-lg flex-grow">
          <b>[{{ modalData.currentData.code }}]</b>
          {{ modalData.currentData.user_story_title }}
        </div>
        <div class="flex-grow-0">
          <DueDate v-model="modalData.currentData.due_date" />
        </div>
      </div>
    </template>
    <template #modal-body>
      <div class="grid grid-cols-12 mt-5">
        <!-- Left Section -->
        <div
          class="col-span-6 border-t border-base-200 rounded-bl-md px-5 py-4 pb-10"
          :style="{ maxHeight: `${clientHeight - 250}px`, overflowY: 'auto' }"
        >
          <TextEditor
            v-model="modalData.currentData.story"
            :element-id="modalData.currentData.id"
            :disabled="false"
            :readonly="false"
            mode="BalloonEditor"
            root-class="text-editor-modal-woktask-left"
          ></TextEditor>

          <div class="mt-10">
            <div
              v-for="(item, itemIndex) in modalData.currentData[
                `${table_prefix}t_backlog_d_stories_s_acs`
              ]"
              :key="itemIndex"
              class="mb-4"
            >
              <CheckList :item-index="itemIndex" />
            </div>
            <div class="mb-4">
              <NewCheckList />
            </div>
          </div>
        </div>
        <!-- Right Section -->
        <div
          class="col-span-6 rounded-br-md border border-l-0 border-base-200"
          :style="{ maxHeight: `${clientHeight - 250}px` }"
        >
          <div
            class="p-2 bg-gray-100 dark:bg-gray-700"
            :style="{
              height: modalData.commentFocused
                ? `${clientHeight - 250 - 50 - 230}px`
                : `${clientHeight - 250 - 50}px`,
            }"
          >
            <div class="chat chat-start">
              <div class="chat-bubble">
                It's over Anakin, <br />I have the high ground.
              </div>
            </div>
            <div class="chat chat-end">
              <div class="chat-bubble">You underestimate my power!</div>
            </div>
          </div>
          <div
            v-click-outside="methods.modalCommentUnFocused"
            class="cu-comment-bar"
            :style="{ height: modalData.commentFocused ? '280px' : '50px' }"
          >
            <div v-if="modalData.commentFocused" style="height: 230px">
              <TextEditor
                v-model="modalData.comment"
                element-id="modal-comment"
                :disabled="false"
                :readonly="false"
                mode="ClassicEditor"
                root-class="text-editor-modal-woktask-right"
                height="230px"
              ></TextEditor>
            </div>
            <div
              style="height: 50px"
              class="border-t border-base-300 flex items-center pr-2"
            >
              <div class="flex-grow px-3">
                <input
                  v-if="!modalData.commentFocused"
                  type="text"
                  class="focus:outline-none focus:ring-0 ft-nunito-600 w-full bg-base-100"
                  placeholder="Comment here..."
                  @click="methods.onCommentFocused"
                />
              </div>
              <div class="flex-grow-0">
                <button
                  type="button"
                  class="btn btn-sm btn-primary normal-case text-white"
                >
                  Send
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
  import {
    defineComponent,
    inject,
    onMounted,
    provide,
    reactive,
    Ref,
    ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import { useRoute } from 'vue-router'
  import { PageConfigs } from '~/types/pages/form/v1'
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { useI18n } from 'vue-i18n'
  import { useSidebarsStore } from '~/store/sidebar'
  import { Axios, Cryptor, Notyf } from '~/services'
  import draggable from 'vuedraggable'
  import Modal from '~/components/atoms/Modal.vue'
  import TextEditor from './TextEditor.vue'
  import CheckList from './CheckList.vue'
  import NewCheckList from './NewCheckList.vue'
  import DueDate from './DueDate.vue'
  import moment from 'moment'

  defineComponent({
    name: 'WorktaskIndex',
  })

  const { t } = useI18n()
  const route = useRoute()
  const drag = ref(false)
  const modalActive = ref(false)
  const clientWidth = inject('clientWidth') as Ref<UnwrapRef<number>>
  const clientHeight = inject('clientHeight') as Ref<UnwrapRef<number>>
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const pageConfigs = reactive<PageConfigs>({
    breadcrumb: [
      {
        type: 'text-link',
        route: '/dashboard',
        icon: '<i class="fal fa-home-alt"></i>',
        text: 'Dashboard',
      },
      {
        type: 'text',
        text: 'Worktask',
      },
    ],
    title: 'menu.general',
  })
  const modalData = reactive({
    breadcrumb: [
      {
        type: 'text',
        text: 'Worktask',
      },
    ],
    currentDataDetail: {
      categoryIndex: null,
      itemIndex: null,
    },
    currentData: null,
    commentFocused: false,
    comment: '',
  })
  provide('modalData', modalData)
  const view = ref('list')
  const sidebarsStore = useSidebarsStore()
  const items = ref([])
  provide('items', items)
  const stack_category_id = ref(null)
  const onInitialized = ref(false)

  // 1. load status first and push to items.value
  // 2. load d stories and filter by category, then push to items.value[x].items
  const methods = {
    detectParams: () => {
      return new Promise((resolve) => {
        sidebarsStore.getSidebars.forEach((side, sideI) => {
          if (side.type === 'dropdown' && side.text === 'Worktask') {
            side.items.forEach((item, itemI) => {
              if (
                item.type === 'text-link' &&
                item.route.indexOf(`/worktask/${route.params.code}`) > -1
              ) {
                stack_category_id.value = item.id.replace('worktask-menu-', '')
                if (pageConfigs.breadcrumb.length === 3) {
                  pageConfigs.breadcrumb[2].text = item.text
                  modalData.breadcrumb[1].text = item.text
                } else {
                  pageConfigs.breadcrumb.push({
                    type: 'text',
                    text: item.text,
                  })
                  modalData.breadcrumb.push({
                    type: 'text',
                    text: item.text,
                  })
                }
                resolve(true)
              }
            })
          }
        })
      })
    },
    getBacklogStatuses: () => {
      console.log('get status')
      return new Promise((resolve, reject) => {
        const params = {
          where: "`group`='BACKLOG STATUS'",
        }
        items.value.length = 0
        Axios({
          method: 'get',
          url:
            `/api/v1/${table_prefix}m_general?` + new URLSearchParams(params),
        })
          .then((response: any) => {
            response.data.data.forEach((item) => {
              items.value.push({
                id: item.id,
                text: item.value_1,
                bgColor: item.value_2,
                textColor: item.value_3,
                items: [],
              })
            })

            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    getBacklogs: () => {
      return new Promise((resolve, reject) => {
        Axios({
          method: 'get',
          url: `/api/v1/${table_prefix}t_backlog_d_stories/worktask?stack_category=${stack_category_id.value}`,
        }).then((response: any) => {
          response.data.forEach((dt, dti) => {
            items.value.forEach((item, itemI) => {
              if (item.text === dt['status.value_1']) {
                response.data[dti]['isProgress'] = false
                items.value[itemI].items.push(response.data[dti])
              }
            })
          })

          resolve(response)
        })
      })
    },
    onShowingModal: (categoryIndex, itemIndex) => {
      items.value[categoryIndex].items[itemIndex].isProgress = true
      const { id } = items.value[categoryIndex].items[itemIndex]
      Axios({
        method: 'GET',
        url: `/api/v1/${table_prefix}t_backlog_d_stories/${id}`,
      }).then((res: any) => {
        items.value[categoryIndex].items[itemIndex].isProgress = false
        modalActive.value = true
        modalData.currentData = res.data.data
        modalData.currentDataDetail = {
          categoryIndex,
          itemIndex,
        }
      })
    },
    onInitilization: async (as = '') => {
      if (!onInitialized.value) {
        console.log(as)
        onInitialized.value = true
        await methods.detectParams()
        await methods.getBacklogStatuses()
        await methods.getBacklogs()
        onInitialized.value = false
      }
    },
    modalCommentUnFocused: () => {
      modalData.commentFocused = false
    },
    onCommentFocused: () => {
      modalData.commentFocused = true
      setTimeout(() => {
        document.getElementById(`modal-comment-child`).focus()
      }, 300)
    },
    getBgHover: () => {
      let bg = modalData.currentData['status.value_2']
      if (bg.indexOf('-100') > -1) {
        bg = bg.replace('-100', '-200')
      } else if (bg.indexOf('-200') > -1) {
        bg = bg.replace('-200', '-300')
      } else if (bg.indexOf('-300') > -1) {
        bg = bg.replace('-300', '-400')
      } else if (bg.indexOf('-400') > -1) {
        bg = bg.replace('-400', '-500')
      } else if (bg.indexOf('-500') > -1) {
        bg = bg.replace('-500', '-600')
      } else if (bg.indexOf('-600') > -1) {
        bg = bg.replace('-600', '-700')
      } else if (bg.indexOf('-700') > -1) {
        bg = bg.replace('-700', '-800')
      } else if (bg.indexOf('-800') > -1) {
        bg = bg.replace('-800', '-900')
      } else if (bg.indexOf('-900') > -1) {
        bg = bg.replace('-900', '-900')
      }

      return bg
    },
    onChangeStatus: async (fromItem, pushNewItem = true) => {
      items.value.forEach((item, itemI) => {
        if (item.text === modalData.currentData['status.value_1']) {
          items.value[itemI].items.forEach((subItem, subItemI) => {
            if (
              Cryptor.decrypt(subItem.id) ===
              Cryptor.decrypt(modalData.currentData.id)
            ) {
              items.value[itemI].items.splice(subItemI, 1)
            }
          })
        }
      })

      let beforeSend = modalData.currentData
      beforeSend.status_id = fromItem.id
      const res = await Axios({
        method: 'PUT',
        url: `/api/v1/${table_prefix}t_backlog_d_stories/${modalData.currentData.id}`,
        data: beforeSend,
      })

      modalData.currentData['status_id'] = fromItem.id
      modalData.currentData['status.value_1'] = fromItem.text
      modalData.currentData['status.value_2'] = fromItem.bgColor
      modalData.currentData['status.value_3'] = fromItem.textColor

      if (pushNewItem) {
        items.value.forEach((item, itemI) => {
          if (item.text === fromItem.text) {
            items.value[itemI].items.push(modalData.currentData)
          }
        })
      }

      const { code, user_story_title } = modalData.currentData
      modalActive.value = false
      Notyf({
        type: 'success',
        message: `[${code}] - ${user_story_title} has been successfully moved to ${fromItem.text}`,
        duration: 3000,
        ripple: true,
        dismissible: true,
        position: {
          x: 'right',
          y: 'top',
        },
      })
    },
    onChangeStatusFromDraggableItem: (e) => {
      if (e.added) {
        const fromItemId = e.added.element.id
        modalData.currentData = e.added.element
        items.value.forEach((item) => {
          item.items.forEach((subItem) => {
            if (Cryptor.decrypt(subItem.id) === Cryptor.decrypt(fromItemId)) {
              methods.onChangeStatus(item, false)
            }
          })
        })
      }
    },
  }

  onMounted(() => {
    methods.onInitilization('mounted')
  })

  watch(route, () => {
    methods.onInitilization('route changed')
  })
</script>
<style scoped>
  .cu-comment-bar {
    @apply relative;
  }
  .cu-comment-bar:before {
    border-radius: 0;
    content: '';
    display: block;
    position: absolute;
    z-index: 1;
    top: -30px;
    left: 0;
    width: 100%;
    height: 30px;
    background-image: linear-gradient(
      -180deg,
      transparent 0%,
      rgba(0, 0, 0, 0.08) 100%
    );
  }
</style>
