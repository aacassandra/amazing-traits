<script setup lang="ts">
  import { computed, defineProps, reactive } from 'vue'
  import { t } from '~/services'

  const props = defineProps({
    title: {
      type: String,
      default: 'Uknown',
    },
    series: {
      /**
       * example:
       * [
       *    {
       *      name: 'Tamu',
       *      data: [10, 7, 12, 15, 11, 28, 19],
       *    },
       *    {...}
       * ]
       */
      type: Array as () => Array<{
        name: string
        colorByPoint: boolean
        data: Array<{
          name: string
          y: number
          sliced?: boolean
          selected?: boolean
        }>
      }>,
      default: () => [],
    },
    suffix: {
      type: String,
      default: '',
    },
  })

  const configs = reactive({
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie',
    },
    title: {
      text: computed(() => props.title),
      align: 'left',
    },
    tooltip: {
      pointFormat: `{series.name}: <b>{point.y:.f} ${
        props.suffix ? t(props.suffix) : ''
      } ({point.percentage:.1f} %)</b>`,
    },
    accessibility: {
      point: {
        valueSuffix: computed(() => props.suffix),
      },
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: `<b>{point.name}</b>: {point.y:.f} ${
            props.suffix ? t(props.suffix) : ''
          } ({point.percentage:.1f} %)`,
        },
      },
    },
    series: computed(() => props.series),
  })
</script>

<template>
  <highcharts :options="configs" />
</template>
