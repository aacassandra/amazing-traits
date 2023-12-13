<!--suppress JSUnresolvedVariable -->
<template>
  <div class="mx-auto px-0 pl-4">
    <div :class="{ 'grid grid-cols-12': true, hidden: !isReady }">
      <div
        v-for="(item, name, i) in temp.form.properties"
        :key="i"
        class="mb-4 pr-5"
        :class="
          (name === 'errors' ||
          (item.options && item.options.hidden && item.options.hidden === true)
            ? 'hidden '
            : '') +
          (form.docs
            ? 'col-span-12'
            : item && item.col
            ? `${item.col}`
            : 'col-span-12')
        "
      >
        <Textfield
          v-if="name !== 'errors' && item.type && item.type === 'textfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Rupiahfield
          v-if="name !== 'errors' && item.type && item.type === 'rupiahfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Numberfield
          v-if="name !== 'errors' && item.type && item.type === 'numberfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Passwordfield
          v-if="name !== 'errors' && item.type && item.type === 'passwordfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :eye="item.options.eye"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <TextAreafield
          v-if="name !== 'errors' && item.type && item.type === 'textareafield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <TextEditorfieldV1
          v-if="
            name !== 'errors' &&
            item.type &&
            item.type === 'texteditorfield' &&
            item.version === 1
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :toolbars="item.options.toolbars"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <TextEditorfieldV2
          v-if="
            name !== 'errors' &&
            item.type &&
            item.type === 'texteditorfield' &&
            item.version === 2
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :toolbars="item.options.toolbars"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Tagsfield
          v-if="name !== 'errors' && item.type && item.type === 'tagsfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Datefield
          v-if="name !== 'errors' && item.type && item.type === 'datefield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <MonthYearfield
          v-if="
            name !== 'errors' && item.type && item.type === 'monthyearfield'
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <DateTimefield
          v-if="name !== 'errors' && item.type && item.type === 'datetimefield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :in24h="item.options.in24h"
        />
        <Timefield
          v-if="name !== 'errors' && item.type && item.type === 'timefield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :in24h="item.options.in24h"
        />
        <DateRangefield
          v-if="
            name !== 'errors' && item.type && item.type === 'daterangefield'
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <DateTimeRangefield
          v-if="
            name !== 'errors' && item.type && item.type === 'datetimerangefield'
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :input="item.options.input"
          :output="item.options.output"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :in24h="item.options.in24h"
        />
        <Slugfield
          v-if="name !== 'errors' && item.type && item.type === 'slugfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Phonefield
          v-if="name !== 'errors' && item.type && item.type === 'phonefield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :display="item.options.display"
          :output="item.options.output"
          :country-code="item.options.countryCode"
        />
        <SelectfieldV1
          v-if="
            name !== 'errors' &&
            item.type &&
            item.type === 'selectfield' &&
            item.version === 1
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:items="item.items"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :field="item.options.field"
          :listener="item.listener"
          :is-multiple="item.options.multiple"
          :reduce="item.options.reduce"
          :api="item.options.api"
        />
        <SelectfieldV2
          v-if="
            name !== 'errors' &&
            item.type &&
            item.type === 'selectfield' &&
            item.version === 2
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:items="item.items"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :clearable="item.options.clearable"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :field="item.options.field"
          :is-multiple="item.options.multiple"
          :reduce="item.options.reduce"
          :api="item.options.api"
        />
        <SelectfieldV3
          v-if="
            name !== 'errors' &&
            item.type &&
            item.type === 'selectfield' &&
            item.version === 3
          "
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :clearable="item.options.clearable"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :field="item.options.field"
          :is-multiple="item.options.multiple"
          :reduce="item.options.reduce"
          :api="item.options.api"
        />
        <Popupfield
          v-if="name !== 'errors' && item.type && item.type === 'popupfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :clearable="item.options.clearable"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
          :field="item.options.field"
          :is-multiple="item.options.multiple"
          :reduce="item.options.reduce"
          :table-configs="item.options.tableConfigs"
          :api="item.options.api"
        />
        <Switchfield
          v-if="name !== 'errors' && item.type && item.type === 'switchfield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :readonly="item.options.readonly"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :information="item.options.information"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :from-generator="true"
        />
        <Filefield
          v-if="name !== 'errors' && item.type && item.type === 'filefield'"
          v-model="item.value"
          v-model:ready="temp.form.ready"
          v-model:has-changed="item.hasChanged"
          v-model:link-preview="item.linkPreview"
          v-model:link-response="item.linkResponse"
          v-model:out-val="item.outVal"
          :default="item.default"
          :name="name"
          :label="item.options.label"
          :placeholder="item.options.placeholder"
          :disabled="item.options.disabled"
          :hidden="item.options.hidden"
          :inline="item.options.inline"
          :viewer="item.options.viewer"
          :information="item.options.information"
          :clearable="item.options.clearable"
          :listener="item.listener"
          :docs="form.docs"
          :properties="temp.form.properties"
          :auto-upload="item.options.autoUpload"
          :from-generator="true"
        />
      </div>
    </div>
    <div :class="{ 'grid grid-cols-12': true, hidden: isReady }">
      <div
        class="col-span-12 text-7xl flex justify-center items-center"
        style="height: 300px"
      >
        <progress class="progress w-56"></progress>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, defineEmits, defineExpose, defineProps } from 'vue'
  import { FormHeader } from '~/types/form/header'
  import { computed, inject, provide, reactive, watch } from 'vue'
  import Validator from '~/services/Validator/header'

  // Components
  import Textfield from '~/components/atoms/form/header/Textfield.vue'
  import Rupiahfield from '~/components/atoms/form/header/Rupiahfield.vue'
  import Numberfield from '~/components/atoms/form/header/Numberfield.vue'
  import Passwordfield from '~/components/atoms/form/header/Passwordfield.vue'
  import TextAreafield from '~/components/atoms/form/header/TextAreafield.vue'
  import Tagsfield from '~/components/atoms/form/header/Tagsfield.vue'
  import Datefield from '~/components/atoms/form/header/Datefield.vue'
  import MonthYearfield from '~/components/atoms/form/header/MonthYearfield.vue'
  import DateTimefield from '~/components/atoms/form/header/DateTimefield.vue'
  import Timefield from '~/components/atoms/form/header/Timefield.vue'
  import DateRangefield from '~/components/atoms/form/header/DateRangefield.vue'
  import DateTimeRangefield from '~/components/atoms/form/header/DateTimeRangefield.vue'
  import Slugfield from '~/components/atoms/form/header/Slugfield.vue'
  import SelectfieldV1 from '~/components/atoms/form/header/SelectfieldV1.vue'
  import SelectfieldV2 from '~/components/atoms/form/header/SelectfieldV2.vue'
  import SelectfieldV3 from '~/components/atoms/form/header/SelectfieldV3.vue'
  import Popupfield from '~/components/atoms/form/header/Popupfield/Index.vue'
  import Switchfield from '~/components/atoms/form/header/Switchfield.vue'
  import TextEditorfieldV1 from '~/components/atoms/form/header/TextEditorfieldV1.vue'
  import TextEditorfieldV2 from '~/components/atoms/form/header/TextEditorfieldV2.vue'
  import Filefield from '~/components/atoms/form/header/Filefield.vue'
  import Phonefield from '~/components/atoms/form/header/Phonefield.vue'
  import { Selectfield } from '~/types/components/atoms/forms/header/select'
  import { ObjectReader } from '~/services'
  import { Schema } from '~/types'

  const props = defineProps({
    form: {
      type: Object,
      required: true,
    },
  })

  const schema = inject('schema') as Schema
  const isReady = ref(false)
  provide('parentReady', isReady)
  const emit = defineEmits(['update:form'])
  const temp = reactive({
    form: computed({
      get: () => props.form,
      set: (val) => emit('update:form', val),
    }),
  }) as {
    form: FormHeader
  }
  const formTotal = ref(Object.entries(props.form.properties).length - 1)

  const getValid = () => {
    return Validator(temp.form, schema.mode)
  }

  defineExpose({
    getValid,
  })

  watch(temp, () => {
    if (temp.form.ready === formTotal.value) {
      setTimeout(() => {
        isReady.value = true
      }, 500)
    }
  })

  Object.entries(temp.form.properties).forEach(([key]) => {
    if (key !== 'errors') {
      temp.form.properties[key].setValue = (val) => {
        temp.form.properties[key].outVal = val
      }

      if (temp.form.properties[key].type === 'selectfield') {
        const newForm = temp.form.properties[key] as Selectfield
        newForm.getValueFull = () => {
          let response = null

          if (!newForm.options.multiple) {
            // single pick
            newForm.items.forEach((item) => {
              const value = ObjectReader(item, newForm.options.field.value)
              if (value === newForm.value) {
                response = item
              }
            })
          } else {
            // multi pick
            const items = []
            newForm.items.forEach((item) => {
              const value = ObjectReader(item, newForm.options.field.value)
              if (
                Array.isArray(newForm.value) &&
                newForm.value.includes(value)
              ) {
                items.push(item)
              }
            })
            response = items
          }
          return response
        }
      }
    }
  })
</script>
