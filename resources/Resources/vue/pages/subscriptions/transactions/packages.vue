<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
      <div class="col-span-12 pb-20">
        <input
          ref="refproof1"
          type="file"
          accept="image/*"
          class="hidden"
          @change="methods.onUploadingProof"
        />
        <Card v-if="!data.is_preview">
          <template #card-header>
            <div class="flex">
              <div class="flex-grow">
                <div
                  class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1 pt-1"
                >
                  1. {{ $t('global.select_package') }}
                </div>
              </div>
            </div>
          </template>
          <template #card-body>
            <div class="mt-3">
              <div class="container mx-auto px-4 pb-4">
                <div class="grid grid-cols-12 gap-6">
                  <div
                    v-for="(paket, index) in paketFil"
                    :key="index"
                    class="col-span-12 xl:col-span-4"
                  >
                    <div
                      class="min-h-[450px] 2xl:min-h-[400px]"
                      :class="{
                        'card shadow-xl border shadow-base-200': true,
                        'bg-base-100':
                          !data.selected_package ||
                          (data.selected_package &&
                            data.selected_package.name !== paket.name),
                        'bg-emerald-50':
                          data.selected_package &&
                          data.selected_package.name === paket.name,
                      }"
                    >
                      <div class="card-body p-3.5">
                        <div class="text-md font-semibold">
                          {{ $t('global.package') }}
                        </div>
                        <div class="flex justify-between mt-3">
                          <span
                            class="text-3xl font-bold text-primary ft-poppins-600"
                          >
                            {{ paket.name }}
                          </span>
                          <span
                            v-if="paket.name === 'Trial'"
                            class="bg-[#00a848] text-sm font-semibold rounded-full text-white flex items-center px-5 ft-spd-700"
                          >
                            {{ $t('others.try_free_or_invesment') }}
                          </span>
                        </div>
                        <p
                          class="text-sm mt-3 ft-poppins-400"
                          style="min-height: 70px"
                        >
                          {{ paket.description }}
                        </p>
                        <table class="table">
                          <!-- head -->
                          <thead>
                            <tr>
                              <th>{{ $t('global.months') }}</th>
                              <th>Subtotal</th>
                              <th>Diskon</th>
                              <th>Total</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(
                                period, index2
                              ) in paket.ihm_m_packages_d_payment_periods"
                              :key="index2"
                            >
                              <td class="text-[12px] ft-poppins-400">
                                {{ period.months }}
                              </td>
                              <td class="text-[12px] ft-poppins-400">
                                {{
                                  methods.getRealPrice(
                                    paket.price,
                                    period.months,
                                  )
                                }}
                              </td>
                              <td class="text-[12px] ft-poppins-400">
                                {{ methods.getDiscount(period.discount) }}
                              </td>
                              <td class="text-[12px] ft-poppins-400">
                                {{
                                  methods.getPrice(
                                    paket.price,
                                    period.months,
                                    period.discount,
                                  )
                                }}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <button
                          v-if="
                            !data.selected_package ||
                            data.selected_package.name !== paket.name
                          "
                          class="w-full bg-primary rounded-md mt-4 py-1.5 text-white font-semibold ft-spd-700"
                          @click="methods.onSelectPackage(paket)"
                        >
                          {{ $t('global.select_package') }}
                        </button>
                        <div v-else class="join mt-4">
                          <input
                            v-model="data.selected_months"
                            type="number"
                            min="1"
                            :disabled="paket.name === 'Trial'"
                            :max="paket.max_months"
                            class="input input-sm input-bordered join-item w-full"
                          />
                          <label
                            class="label join-item bg-base-300 h-[32px] w-[80px] flex justify-center"
                          >
                            <span class="label-text">Bulan</span>
                          </label>
                        </div>
                        <div :class="show == true ? '' : 'hidden'">
                          <hr class="my-4" />
                          <div style="min-height: 550px">
                            <div
                              v-for="(
                                feature, i
                              ) in paket.ihm_m_packages_d_features"
                              :key="i"
                              class="flex flex-col"
                            >
                              <div class="text-sm mb-[16px]">
                                <i
                                  class="fa-solid fa-circle-check text-green-600 mr-[10px]"
                                ></i
                                >{{ feature.value }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-span-12">
                    <button
                      class="font-semibold w-full btn btn-sm text-primary hover:text-white border-primary hover:border-primary bg-base-100 hover:bg-primary normal-case"
                      @click="methods.showCard()"
                    >
                      <span>
                        {{ $t('others.view_package_detail') }}
                        <i v-if="show" class="fa-solid fa-chevron-up"></i>
                        <i v-else class="fa-solid fa-chevron-down"></i>
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </Card>
        <Card v-if="!data.is_approved || data.is_approval">
          <template #card-header>
            <div class="flex">
              <div class="flex-grow flex flex-row">
                <BackButton
                  v-if="data.is_approval"
                  route="approval"
                  class="mr-2"
                >
                  {{ $t('global.back') }}
                </BackButton>
                <div
                  class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1 pt-1"
                >
                  <span v-if="!data.is_preview">
                    2. {{ $t('others.select_payment_method') }}
                  </span>
                  <span v-else>
                    {{ $t('others.detail_payment_method') }}
                  </span>
                </div>
              </div>

              <div v-if="data.is_approval" class="flex-grow flex justify-end">
                <button
                  type="button"
                  class="btn btn-sm btn-primary normal-case text-white"
                  @click="methods.onLookDetailCluster"
                >
                  {{ $t('others.see_cluster_details') }}
                  <i class="fa-light fa-magnifying-glass"></i>
                </button>
              </div>
            </div>
          </template>
          <template #card-body>
            <div class="mt-3">
              <div v-if="data.is_preview" class="flex items-center mb-8">
                <div class="flex-grow flex items-center">
                  <div class="ft-poppins-400">Order ID:</div>
                  <div class="ft-poppins-600 ml-2 flex items-center">
                    {{ data.payment_res.invoice_code }}
                  </div>
                </div>
                <div class="flex-grow flex justify-end">
                  <div
                    :class="{
                      'ft-poppins-600 text-white rounded-full px-2 py-1 text-xs': true,
                      'bg-warning': data.payment_res.status !== 'APPROVED',
                      'bg-error': data.payment_res.status === 'REJECTED',
                      'bg-success': data.payment_res.status === 'APPROVED',
                    }"
                  >
                    {{ data.payment_res.status }}
                  </div>
                </div>
              </div>

              <!-- virtual account -->
              <div class="flex mb-3">
                <div
                  class="flex items-center w-1/5 pl-2.5 h-[48px] border-l border-t border-b bg-gray-100 border-gray-200 dark:border-gray-700"
                  style="
                    border-top-left-radius: 7px;
                    border-bottom-left-radius: 7px;
                  "
                >
                  <input
                    id="virtual_account"
                    v-model="data.method_pay"
                    type="radio"
                    value="virtual_account"
                    checked
                    disabled
                    name="cara_bayar"
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    @change="methods.changeHowPay('virtual_account')"
                  />
                  <div class="flex w-full items-center justify-between">
                    <label
                      for="virtual_account"
                      class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                    >
                      Virtual Account
                    </label>
                    <div
                      class="bg-warning py-1 px-2 text-white ft-poppins-500 rounded-full text-xs"
                    >
                      <i class="fa-regular fa-screwdriver-wrench"></i> Comming
                      Soon
                    </div>
                  </div>
                </div>
                <select
                  :disabled="
                    data.method_pay == 'manual_transfer' ||
                    !data.selected_months ||
                    data.is_preview
                  "
                  class="select select-bordered w-4/5 rounded-none"
                  style="
                    border-top-right-radius: 7px;
                    border-bottom-right-radius: 7px;
                  "
                >
                  <option value="" selected disabled>Pilih Bank</option>
                </select>
              </div>

              <!-- manual transfer -->
              <div class="flex">
                <div
                  class="flex items-center w-1/5 pl-2.5 h-[48px] border-l border-t border-b bg-gray-100 border-gray-200 dark:border-gray-700"
                  style="
                    border-top-left-radius: 7px;
                    border-bottom-left-radius: 7px;
                  "
                >
                  <input
                    id="manual_transfer"
                    v-model="data.method_pay"
                    type="radio"
                    :disabled="!data.selected_months || data.is_trial"
                    value="manual_transfer"
                    name="cara_bayar"
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    @change="methods.changeHowPay('manual_transfer')"
                  />
                  <label
                    for="manual_transfer"
                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                  >
                    {{ $t('field.manual_transfer') }}
                  </label>
                </div>
                <select
                  v-model="data.selected_bank"
                  :disabled="
                    data.method_pay == 'virtual_account' ||
                    !data.selected_months ||
                    data.is_preview ||
                    data.is_trial
                  "
                  class="select select-bordered w-4/5 rounded-none"
                  style="
                    border-top-right-radius: 7px;
                    border-bottom-right-radius: 7px;
                  "
                >
                  <option value="" selected disabled>Pilih Bank</option>
                  <option
                    v-for="(item, index) in data.manual_transfer.items"
                    :key="index"
                    :value="item.id"
                  >
                    {{ item.value_1 }}
                  </option>
                </select>
              </div>

              <div>
                <hr class="my-4" />
                <span class="text-sm ft-poppins-500 font-semibold">
                  {{ $t('others.voucher_n_ref_code') }}
                </span>
                <div class="w-full flex items-center mt-8">
                  <div class="w-1/5">
                    <span class="text-sm font-semibold mr-5">
                      {{ $t('field.ref_code') }} (Optional)
                    </span>
                  </div>
                  <div class="w-4/5 flex items-center">
                    <input
                      :disabled="
                        !data.selected_months ||
                        data.is_preview ||
                        data.is_trial
                      "
                      type="text"
                      name="kode_refferal"
                      placeholder="Masukan Kode Refferal"
                      class="input input-bordered flex-grow mr-4"
                    />
                    <button
                      v-if="!data.is_preview"
                      :disabled="!data.selected_months || data.is_trial"
                      class="btn btn-primary font-semibold rounded-md text-white text-sm flex-grow-0 normal-case"
                    >
                      Gunakan
                    </button>
                  </div>
                </div>
                <div class="w-full flex items-center mt-5">
                  <div class="w-1/5">
                    <span class="text-sm font-semibold mr-5">
                      Voucher (Optional)
                    </span>
                  </div>
                  <div class="w-4/5 flex items-center">
                    <input
                      :disabled="
                        !data.selected_months ||
                        data.is_preview ||
                        data.is_trial
                      "
                      type="text"
                      name="voucher"
                      placeholder="Masukan Kode Voucher"
                      class="input input-bordered flex-grow mr-4"
                    />
                    <button
                      v-if="!data.is_preview"
                      :disabled="!data.selected_months || data.is_trial"
                      class="btn btn-primary font-semibold flex-grow-0 rounded-md text-white text-sm normal-case"
                    >
                      Gunakan
                    </button>
                  </div>
                </div>
              </div>

              <div
                v-if="
                  data.selected_package &&
                  data.selected_months &&
                  (data.selected_bank || data.is_trial)
                "
              >
                <hr class="my-4" />
                <span class="text-sm ft-poppins-500 font-semibold">
                  {{ $t('others.payment_confirmation') }}
                </span>

                <!-- Payment Details -->
                <div class="flex flex-col mt-5">
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm font-semibold">
                      {{ $t('global.package') }} -
                      {{ data.selected_package.name }} /
                      {{ $t('global.months') }}
                    </span>
                    <span class="text-sm">
                      {{ IntToRupiah(data.selected_package.price, 'Rp. ') }}
                    </span>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">{{ $t('field.quantity') }}</span>
                    <span class="text-sm"
                      >{{ data.selected_months }}
                      {{ $t('global.months') }}</span
                    >
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">Subtotal</span>
                    <span class="text-sm">
                      {{
                        IntToRupiah(
                          data.selected_package.price * data.selected_months,
                          'Rp. ',
                        )
                      }}
                    </span>
                  </div>
                  <div
                    v-if="methods.onGetDiscount(data.selected_months, true)"
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid text-emerald-600 ft-poppins-400"
                  >
                    <span class="text-sm">
                      Diskon (Pembayaran Per Tahun /
                      {{ methods.onGetDiscount(data.selected_months) }})
                    </span>
                    <span class="text-sm">{{
                      methods.onGetDiscountPrice(data.selected_months)
                    }}</span>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid text-primary ft-poppins-400"
                  >
                    <span class="text-sm">{{ $t('field.admin_fee') }}</span>
                    <span class="text-sm">
                      {{
                        !data.is_trial
                          ? IntToRupiah(data.admin_fee, 'Rp. ')
                          : 'Rp. 0'
                      }}
                    </span>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm italic">{{
                      $t('field.unique_payment_code')
                    }}</span>
                    <span class="text-sm">{{
                      methods.onGetUnicodePayment()
                    }}</span>
                  </div>
                  <div
                    class="flex flex-row justify-between border bg-gray-200 p-3 border-solid"
                  >
                    <span class="text-sm">{{
                      $t('field.total_payment_code')
                    }}</span>
                    <span class="text-sm font-bold">{{
                      methods.onGetTotal()
                    }}</span>
                  </div>
                </div>

                <hr class="my-4" />

                <!-- Back Account Name & Account Number, will showing when the client preview this page -->
                <div
                  v-if="data.is_preview && !data.is_trial"
                  class="flex flex-col mt-5"
                >
                  <div
                    class="flex flex-row justify-end border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <img
                      :src="data.payment_res.payment_method.value_4"
                      :alt="data.payment_res.payment_method.value_1"
                      class="h-[35px]"
                    />
                  </div>
                  <div
                    class="flex flex-row justify-between border bg-gray-200 p-3 border-solid"
                  >
                    <span class="text-sm">{{
                      $t('others.account_number')
                    }}</span>
                    <span class="ft-poppins-600 text-[16px]">
                      {{ data.payment_res.payment_method.value_2 }}
                    </span>
                  </div>
                  <div
                    class="flex flex-row justify-between border p-3 border-solid"
                  >
                    <span class="text-sm">{{ $t('others.account_name') }}</span>
                    <span class="ft-poppins-600 text-[16px]">
                      {{ data.payment_res.payment_method.value_3 }}
                    </span>
                  </div>
                </div>

                <!-- Approval Log & Textarea for reason -->
                <div v-if="data.is_approval" class="grid grid-cols-12 mt-6">
                  <div class="col-span-6 pr-3">
                    <vue-good-table-custom
                      :columns="detailLog.column"
                      :rows="detailLog.rows"
                      style-class="vgt-table"
                      :theme="theme === 'dark' ? 'black-rhino' : ''"
                    >
                      <template #table-row="props">
                        {{ props.formattedRow[props.column.field] }}
                      </template>
                    </vue-good-table-custom>
                  </div>
                  <div class="col-span-6">
                    <TextAreafield
                      v-model="detailLog.note"
                      :disabled="
                        !btnApproval.reject ||
                        !btnApproval.revise ||
                        !btnApproval.approve
                      "
                      name="note"
                      label="field.reason"
                      information="information.approval_reason"
                      class="mb-5"
                    />
                  </div>
                </div>

                <div class="flex justify-between mt-4">
                  <div>
                    <RejectButton
                      v-if="btnApproval.reject"
                      :on-click="methods.onReject"
                    >
                      {{ $t('global.reject') }}
                    </RejectButton>
                  </div>
                  <div>
                    <button
                      v-if="!data.is_preview && !data.is_trial"
                      type="button"
                      :disabled="data.is_progress.order"
                      class="btn btn-sm btn-primary text-sm font-semibold text-white ml-auto normal-case"
                      @click="methods.onPayment"
                    >
                      {{
                        data.have_order
                          ? $t('others.view_payment_detail')
                          : $t('others.pay_now')
                      }}
                      <i
                        v-if="!data.is_progress.order && !data.have_order"
                        class="fa-solid fa-circle-chevron-right"
                      ></i>
                      <i
                        v-else-if="data.is_progress.order && !data.have_order"
                        class="fad fa-spin fa-spinner-third"
                      ></i>
                      <i
                        v-if="data.have_order"
                        class="fa-light fa-magnifying-glass"
                      ></i>
                    </button>

                    <button
                      v-else-if="data.is_trial && !data.have_order"
                      type="button"
                      :disabled="data.is_progress.order"
                      class="btn btn-sm btn-primary text-sm font-semibold text-white ml-auto normal-case"
                      @click="methods.onPayment"
                    >
                      Submit
                      <i
                        v-if="!data.is_progress.order"
                        class="fa-solid fa-circle-chevron-right"
                      ></i>
                      <i v-else class="fad fa-spin fa-spinner-third"></i>
                    </button>

                    <!-- Button for uploading proof of payment -->
                    <button
                      v-else-if="!data.have_paid && !data.is_trial"
                      type="button"
                      class="btn btn-sm btn-warning normal-case text-white"
                      :disabled="data.is_progress.upload_proof"
                      @click="
                        () => {
                          refproof1.click()
                        }
                      "
                    >
                      {{ $t('others.upload_proof_payment') }}
                      <i
                        v-if="!data.is_progress.upload_proof"
                        class="fa-light fa-arrow-up-from-bracket"
                      ></i>
                      <i v-else class="fad fa-spin fa-spinner-third"></i>
                    </button>

                    <!-- Button for showing proof of payment -->
                    <button
                      v-else-if="!data.is_trial"
                      type="button"
                      class="btn btn-sm btn-info text-white normal-case"
                      @click="() => (data.show_modal.proof_payment = true)"
                    >
                      {{ $t('others.display_proof_payment') }}
                      <i class="fa-light fa-magnifying-glass"></i>
                    </button>

                    <SubmitButton
                      v-if="btnApproval.approve"
                      :on-click="methods.onApprove"
                      class="ml-2"
                    >
                      {{ $t('global.approve') }}
                    </SubmitButton>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </Card>

        <!-- Card when data is approved for client -->
        <Card v-if="data.is_approved && !data.is_approval">
          <template #card-header>
            <div class="flex">
              <div class="flex-grow flex flex-row">
                <div
                  class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1 pt-1"
                >
                  <span>Langganan</span>
                </div>
              </div>
            </div>
          </template>
          <template #card-body>
            <div class="mt-3">
              <div class="flex items-center mb-8">
                <div class="flex-grow flex items-center">
                  <div class="ft-poppins-400">Order ID:</div>
                  <div class="ft-poppins-600 ml-2 flex items-center">
                    {{ data.payment_res.invoice_code }}
                  </div>
                </div>
              </div>

              <div>
                <div class="flex flex-col mt-5">
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">{{ $t('global.package') }}</span>
                    <span class="text-sm">
                      {{ data.selected_package.name }}
                    </span>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">{{ $t('field.quantity') }}</span>
                    <span class="text-sm">
                      {{ data.selected_months }} {{ $t('global.months') }}
                    </span>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">Status</span>
                    <div>
                      <div
                        class="ft-poppins-600 text-white rounded-full px-2 py-1 text-xs bg-success"
                      >
                        {{ data.payment_res.status }}
                      </div>
                    </div>
                  </div>
                  <div
                    class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
                  >
                    <span class="text-sm">Expired Time</span>
                    <span class="text-sm">
                      {{
                        moment(
                          data.payment_res.expired_at,
                          'YYYY-MM-DD HH:mm:ss',
                        ).format('DD MMMM YYYY HH:mm')
                      }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </div>

  <Modal
    v-model="data.show_modal.order"
    :keep-active="data.show_modal.proof_payment"
    width="w-11/12 max-w-5xl"
  >
    <template #modal-title>
      <div
        v-if="i18nLocale.locale.value === 'en'"
        class="text-[24px] ft-poppins-600"
      >
        <span class="text-primary">{{ $t('others.package') }}</span>
        {{ $t('others.payment') }}
      </div>
      <div
        v-if="i18nLocale.locale.value === 'id'"
        class="text-[24px] ft-poppins-600"
      >
        <span class="text-primary">{{ $t('others.payment') }}</span>
        {{ $t('others.package') }}
      </div>
    </template>
    <template #modal-body>
      <div
        :class="`px-5 overflow-y-auto overflow-x-hidden`"
        :style="{ height: `${clientHeight - 250}px` }"
      >
        <div class="flex items-center">
          <div class="ft-poppins-400">Order ID:</div>
          <div class="ft-poppins-600 ml-2">
            {{ data.payment_res.invoice_code }}
          </div>
        </div>

        <div class="flex flex-col mt-5">
          <div
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
          >
            <span class="text-sm font-semibold">
              {{ $t('global.package') }} - {{ data.selected_package.name }} /
              {{ $t('global.months') }}
            </span>
            <span class="text-sm">
              {{ IntToRupiah(data.selected_package.price, 'Rp. ') }}
            </span>
          </div>
          <div
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
          >
            <span class="text-sm">{{ $t('field.quantity') }}</span>
            <span class="text-sm"
              >{{ data.selected_months }} {{ $t('global.months') }}</span
            >
          </div>
          <div
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
          >
            <span class="text-sm">Subtotal</span>
            <span class="text-sm">
              {{
                IntToRupiah(
                  data.selected_package.price * data.selected_months,
                  'Rp. ',
                )
              }}
            </span>
          </div>
          <div
            v-if="methods.onGetDiscount(data.selected_months, true)"
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid text-emerald-600 ft-poppins-400"
          >
            <span class="text-sm">
              Diskon (Pembayaran Per Tahun /
              {{ methods.onGetDiscount(data.selected_months) }})
            </span>
            <span class="text-sm">{{
              methods.onGetDiscountPrice(data.selected_months)
            }}</span>
          </div>
          <div
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid text-primary ft-poppins-400"
          >
            <span class="text-sm">{{ $t('field.admin_fee') }}</span>
            <span class="text-sm">{{
              IntToRupiah(data.admin_fee, 'Rp. ')
            }}</span>
          </div>
          <div
            class="flex flex-row justify-between border border-gray-200 p-3 border-solid ft-poppins-400"
          >
            <span class="text-sm italic">{{
              $t('field.unique_payment_code')
            }}</span>
            <span class="text-sm">{{
              methods.onGetUnicodePayment(false)
            }}</span>
          </div>
          <div
            class="flex flex-row justify-between border bg-gray-200 p-3 border-solid"
          >
            <span class="text-sm">{{ $t('field.total_payment_code') }}</span>
            <span class="text-sm font-bold">{{ methods.onGetTotal() }}</span>
          </div>
        </div>

        <p class="ft-poppins-400 text-[18px] mt-[32px]">
          <span v-html="methods.getX1()"></span>
        </p>

        <div class="flex flex-col mt-5">
          <div
            class="flex flex-row justify-end border border-gray-200 p-3 border-solid ft-poppins-400"
          >
            <img
              :src="data.payment_res.payment_method.value_4"
              :alt="data.payment_res.payment_method.value_1"
              class="h-[35px]"
            />
          </div>
          <div
            class="flex flex-row justify-between border bg-gray-200 p-3 border-solid"
          >
            <span class="text-sm">{{ $t('others.account_number') }}</span>
            <span class="ft-poppins-600 text-[16px]">
              {{ data.payment_res.payment_method.value_2 }}
            </span>
          </div>
          <div class="flex flex-row justify-between border p-3 border-solid">
            <span class="text-sm">{{ $t('others.account_name') }}</span>
            <span class="ft-poppins-600 text-[16px]">
              {{ data.payment_res.payment_method.value_3 }}
            </span>
          </div>
        </div>

        <p class="ft-poppins-400 text-[18px] mt-[32px]">
          <span v-html="methods.getX2()"></span>
        </p>
      </div>
    </template>
    <template #modal-footer>
      <div class="flex justify-end px-5 mt-4">
        <input
          ref="refproof2"
          type="file"
          accept="image/*"
          class="hidden"
          @change="methods.onUploadingProof"
        />
        <button
          v-if="!data.have_paid"
          type="button"
          class="btn btn-sm btn-warning normal-case text-white"
          :disabled="data.is_progress.upload_proof"
          @click="
            () => {
              refproof2.click()
            }
          "
        >
          {{ $t('others.upload_proof_payment') }}
          <i
            v-if="!data.is_progress.upload_proof"
            class="fa-light fa-arrow-up-from-bracket"
          ></i>
          <i v-else class="fad fa-spin fa-spinner-third"></i>
        </button>
        <button
          v-else
          type="button"
          class="btn btn-sm btn-info text-white normal-case"
          @click="() => (data.show_modal.proof_payment = true)"
        >
          {{ $t('others.display_proof_payment') }}
          <i class="fa-light fa-magnifying-glass"></i>
        </button>
      </div>
    </template>
  </Modal>

  <Modal v-model="data.show_modal.proof_payment" width="max-w-5xl">
    <template #modal-title> Image Viewer </template>
    <template #modal-body>
      <div class="w-full flex justify-center">
        <img
          :src="data.payment_res.proof_payment_img_url"
          alt=""
          style="max-width: 650px; object-fit: scale-down"
        />
      </div>
    </template>
  </Modal>
</template>
<script setup lang="ts">
  import { inject, reactive, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import {
    Axios,
    Cryptor,
    GetRandomString,
    IntToRupiah,
    Notyf,
    Swal,
    t,
  } from '~/services'
  import Modal from '~/components/atoms/Modal.vue'
  import Table from '~/components/atoms/form/header/Popupfield/Table.vue'
  import { useI18n } from 'vue-i18n'
  import moment from 'moment'
  import { useRoute } from 'vue-router'
  import SubmitButton from '~/components/atoms/buttons/SubmitButton.vue'
  import RejectButton from '~/components/atoms/buttons/RejectButton.vue'
  import ReviseButton from '~/components/atoms/buttons/ReviseButton.vue'
  import TextAreafield from '~/components/atoms/form/header/TextAreafield.vue'
  import BackButton from '~/components/atoms/buttons/BackButton.vue'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  const theme = inject('theme')
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const i18nLocale = useI18n()
  const route = useRoute()
  const clientHeight = inject('clientHeight')
  const isLoading = ref(false)
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
        text: 'Subscriptions',
        lang: 'menu.subscriptions',
      },
      {
        type: 'text',
        text: 'Transactions',
        lang: 'menu.transactions',
      },
      {
        type: 'text',
        text: 'Packages',
        lang: 'menu.packages',
      },
    ],
    title: 'Langganan Paket',
  })

  const detailLog = reactive({
    rows: [],
    column: [
      {
        label: 'field.action_user',
        field: 'action_user.name',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'field.action_type',
        field: 'action',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
          filterDropdownItems: [
            { value: 'PROGRESS', text: 'PROGRESS' },
            { value: 'REVISED', text: 'REVISED' },
            { value: 'REJECTED', text: 'REJECTED' },
            { value: 'APPROVED', text: 'APPROVED' },
          ],
        },
      },
      {
        label: 'field.action_at',
        show: true,
        type: 'date',
        width: '150px',
        field: 'action_at',
        dateInputFormat: 'yyyy-MM-dd HH:mm:ss', // expects 2018-03-16
        dateOutputFormat: 'dd-MM-yyyy HH:mm', // outputs Mar 16th 2018
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'field.reason',
        field: 'action_note',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
    ],
    note: '',
  })
  const btnApproval = reactive({
    revise: false,
    reject: false,
    approve: false,
  })
  const data = reactive({
    method_pay: 'manual_transfer',
    manual_transfer: {
      items: [],
    },
    selected_package: null,
    selected_months: 0,
    selected_bank: '',
    unicode_payment: 0,
    total: 0,
    have_order: false,
    have_paid: false,
    show_modal: {
      order: false,
      proof_payment: false,
    },
    payment_res: {
      package_id: '',
      price: 0,
      months: 0,
      discount: 0,
      admin_fee: 0,
      unicode_payment: 0,
      total: 0,
      payment_method: {
        id: '',
        code: null,
        group: '',
        key: null,
        value_1: '',
        value_2: '',
        value_3: '',
        value_4: '',
      },
      package: {
        id: '',
        name: '',
        description: '',
        price: 550000,
        active_flag: true,
        ihm_m_packages_d_features: [
          {
            id: '',
            ihm_m_packages_id: '',
            value: '',
            created_id: '',
            updated_id: null,
            deleted_id: null,
            deleted_at: null,
            created_at: '',
            updated_at: null,
          },
        ],
        ihm_m_packages_d_payment_periods: [
          {
            id: '',
            ihm_m_packages_id: '',
            months: '1',
            discount: 0,
            created_id: '',
            updated_id: null,
            deleted_id: null,
            deleted_at: null,
            created_at: '',
            updated_at: null,
          },
        ],
      },
      cluster_id: '',
      invoice_code: '',
      status: '',
      created_id: '',
      updated_at: '',
      created_at: '',
      proof_payment_img_url: '',
      id: '',
    },
    is_progress: {
      order: false,
      upload_proof: false,
    },
    proof_response: '',
    admin_fee: 0,
    is_preview: false,
    is_approval: false,
    is_pending: false,
    is_approved: false,
    is_trial: false,
    reason: '',
  })

  if (route.query.id) {
    let url = `/api/v1/ihm_t_subscriptions/${route.query.id}`
    if (route.query.approval) {
      data.is_approval = true
      url += '?is_approval=true'
    }

    Axios({
      method: 'GET',
      url,
    }).then((res: any) => {
      const res_f = res.data.data
      detailLog.rows = res_f.approval.logs
      if (data.is_approval) {
        btnApproval.revise = res_f.approval.revise
        btnApproval.reject = res_f.approval.reject
        btnApproval.approve = res_f.approval.approve
      }
      data.payment_res = res_f
      data.selected_package = res_f.package
      data.selected_months = res_f.months
      data.selected_bank = res_f.payment_method ? res_f.payment_method.id : null
      data.have_order = true
      data.have_paid = true
      data.admin_fee = res_f.package.admin_fee * 1
      data.total = res_f.total || 0
      data.unicode_payment = res_f.unicode_payment
      data.is_preview = true
      data.is_trial = res_f.package.name === 'Trial'
    })
  } else {
    Axios({
      method: 'GET',
      url: '/api/v1/ihm_t_subscriptions/current_subscription',
    }).then((res: any) => {
      const res_f = res.data.data
      if (res_f) {
        data.payment_res = res_f
        data.selected_package = res_f.package
        data.selected_months = res_f.months
        data.selected_bank = res_f.payment_method
          ? res_f.payment_method.id
          : null
        data.have_order = true
        data.have_paid = res_f.status !== 'PENDING'
        data.admin_fee = res_f.package.admin_fee * 1
        data.total = res_f.total
        data.unicode_payment = res_f.unicode_payment
        data.is_preview = true
        data.is_pending = res_f.status === 'PENDING'
        data.is_approved = res_f.status === 'APPROVED'
        data.is_trial = res_f.package.name === 'Trial'
      }
    })
  }

  Axios({
    method: 'GET',
    url: '/api/v1/ihm_m_packages?with_detail=true',
  }).then((res: any) => {
    if (res.data.data.length) {
      paketFil.value = res.data.data
      if (!route.query.id) {
        data.admin_fee = res.data.data[0].admin_fee * 1
      }
    }
  })

  Axios({
    method: 'GET',
    url: "/api/v1/ihm_m_general?where=`group` = 'BANK TRANSFER IHOME'",
  }).then((res: any) => {
    data.manual_transfer.items = res.data.data
  })

  const refproof1 = ref()
  const refproof2 = ref()
  let paketFil = ref<Array<any>>([])
  let show = ref(false)
  const methods = {
    getX1: () => {
      return t({
        lang: 'others.please_transfer',
        option: { total: methods.onGetTotal() },
      })
    },
    getX2: () => {
      return t({
        lang: 'others.please_transfer_before',
        option: {
          before: moment(data.payment_res.created_at, 'DD/MM/YYYY HH:mm')
            .add(1, 'days')
            .format('DD/MM/YYYY HH:mm'),
        },
      })
    },
    changeHowPay: (how: string) => {
      if (how == 'virtual_account') {
        data.method_pay = 'virtual_account'
      } else {
        data.method_pay = 'manual_transfer'
      }
    },
    showCard: () => {
      show.value = !show.value
    },
    getRealPrice: (price, months) => {
      const totalprice = price * months
      return IntToRupiah(totalprice, 'Rp. ')
    },
    getPrice: (price, months, discount) => {
      const totalprice = price * months
      if (discount) {
        return IntToRupiah(totalprice - totalprice * discount, 'Rp. ')
      } else {
        return IntToRupiah(totalprice, 'Rp. ')
      }
    },
    getDiscount: (discount) => {
      return discount * 100 + '%'
    },
    onSelectPackage: (paket) => {
      if (paket.name === 'Trial') {
        data.selected_months = paket.max_months
        data.is_trial = true
      } else {
        data.selected_months = 0
        data.is_trial = false
      }
      data.selected_package = paket
    },
    onGetDiscount: (months, shower = false) => {
      let discount = 0
      data.selected_package.ihm_m_packages_d_payment_periods.forEach(
        (period) => {
          if (months >= period.months * 1) {
            discount = period.discount
          }
        },
      )

      if (shower) {
        return discount
      } else {
        return discount * 100 + '%'
      }
    },
    onGetDiscountPrice: (months) => {
      let discount = 0
      data.selected_package.ihm_m_packages_d_payment_periods.forEach(
        (period) => {
          if (months >= period.months * 1) {
            discount = period.discount
          }
        },
      )

      if (discount) {
        return `-${IntToRupiah(
          data.selected_package.price * months * discount,
          'Rp. ',
        )}`
      } else {
        return IntToRupiah(0, 'Rp. ')
      }
    },
    onGetUnicodePayment: (is_new = true) => {
      if (data.is_trial) {
        data.unicode_payment = 0
        data.total = 0
        return IntToRupiah(data.unicode_payment, 'Rp. ')
      }

      let subtotal = data.selected_package.price * data.selected_months
      let discount = 0
      let months = data.selected_months
      data.selected_package.ihm_m_packages_d_payment_periods.forEach(
        (period) => {
          if (months >= period.months * 1) {
            discount = period.discount
          }
        },
      )

      if (discount) {
        subtotal = subtotal - subtotal * discount
      }

      subtotal += data.admin_fee

      if (is_new) {
        let unix = GetRandomString(3, '0123456789')
        if (typeof unix === 'string') {
          data.total = subtotal + parseInt(unix)
          data.unicode_payment = parseInt(JSON.stringify(data.total).slice(-3))
          return IntToRupiah(data.unicode_payment, 'Rp. ')
        }
      } else {
        return IntToRupiah(data.unicode_payment, 'Rp. ')
      }
    },
    onGetTotal: () => {
      return IntToRupiah(data.total, 'Rp. ')
    },
    onPayment: () => {
      if (data.have_order) {
        data.show_modal.order = true
        return false
      }

      if (data.is_progress.order) {
        return false
      } else {
        data.is_progress.order = true
      }

      let discount = 0
      let months = data.selected_months
      data.selected_package.ihm_m_packages_d_payment_periods.forEach(
        (period) => {
          if (months >= period.months * 1) {
            discount = period.discount
          }
        },
      )

      const payment_method = data.manual_transfer.items.filter(
        (item) =>
          Cryptor.decrypt(item.id) === Cryptor.decrypt(data.selected_bank),
      )[0]

      Axios({
        method: 'POST',
        url: '/api/v1/ihm_t_subscriptions',
        data: {
          package_id: data.selected_package.id,
          price: data.selected_package.price,
          months,
          discount,
          admin_fee: data.is_trial ? 0 : data.admin_fee,
          unicode_payment: data.is_trial ? 0 : data.unicode_payment,
          total: data.is_trial ? 0 : data.total,
          payment_method: JSON.stringify(payment_method),
          package: JSON.stringify(data.selected_package),
        },
      })
        .then((res: any) => {
          data.is_progress.order = false
          data.have_order = true
          data.payment_res = res.data.data
          data.payment_res.package = res.data.data.package
          data.payment_res.payment_method = !data.is_trial
            ? res.data.data.payment_method
            : null
          data.show_modal.order = !data.is_trial
          Notyf({
            type: 'success',
            message: res.data.message,
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
        })
        .catch((err: any) => {
          if (
            err.response.data.errors &&
            Array.isArray(err.response.data.errors)
          ) {
            err.response.data.errors.forEach((message) => {
              Notyf({
                type: 'error',
                message,
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
          data.is_progress.order = false
        })
    },
    onUploadingProof: (event: any) => {
      if (!data.is_progress.upload_proof) {
        data.is_progress.upload_proof = true
      } else {
        return false
      }

      const host = import.meta.env.VITE_APP_API_HOST
      const file = event.target.files[0]
      const formData = new FormData()
      formData.append('file', file)
      formData.append('subscription_id', data.payment_res.id)
      Axios({
        url: `/api/v1/ihm_t_subscriptions/upload_proof_payment`,
        method: 'POST',
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        data: formData,
      })
        .then((res: any) => {
          data.payment_res.status = 'PROGRESS'
          data.payment_res.proof_payment_img_url =
            res.data.data.proof_payment_img_url
          data.have_paid = true
          data.is_progress.upload_proof = false
          Notyf({
            type: 'success',
            message: res.data.message,
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
        })
        .catch((err: any) => {
          data.is_progress.upload_proof = false
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
        })
    },
    onLookDetailCluster: () => {
      const url = `/setup/master/clusters/form?id=${data.payment_res.cluster_id}&preview=true`

      window.open(url, '_blank')
    },
    onAction: (action: string) => {
      const send = () => {
        const url = `/api/v1/${table_prefix}e_approval/change_status`
        Axios({
          method: 'POST',
          url,
          data: {
            trx_id: route.query.id,
            trx_name: 'Subscriptions',
            trx_table: 'ihm_t_subscriptions',
            action,
            action_note: detailLog.note,
          },
        })
          .then((res: any) => {
            detailLog.note = ''
            btnApproval.reject = false
            btnApproval.revise = false
            btnApproval.approve = false
            data.payment_res.status = action
            data.is_approved = action === 'APPROVED'
            Notyf({
              type: 'success',
              message: t(res.data.message),
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          })
          .catch((err) => {
            //
          })
      }
      Swal.confirm({
        title: t('global.confirmation'),
        html: t(`alert.${action.toLowerCase()}_data.html`),
        icon: 'warning',
        button: {
          confirm: 'primary',
          size: 'md',
        },
        showCancelButton: true,
        confirmButtonText: t('global.yes'),
        cancelButtonText: t('global.cancel'),
      }).then((result: any) => {
        if (result.isConfirmed) {
          send()
        }
      })
    },
    onReject: () => {
      methods.onAction('REJECTED')
    },
    onRevise: () => {
      methods.onAction('REVISED')
    },
    onApprove: () => {
      methods.onAction('APPROVED')
    },
  }
</script>
