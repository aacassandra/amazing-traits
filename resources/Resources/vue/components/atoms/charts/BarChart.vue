<script setup lang="ts">
  import { reactive, defineProps, computed } from 'vue'
  import { t } from '~/services'

  const props = defineProps({
    title: {
      type: String,
      default: 'Uknown',
    },
    labels: {
      type: Array as () => Array<string>,
      default: () => [],
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
      type: Array as () => Array<{ name: string; data: Array<number> }>,
      default: () => [],
    },
    unit: {
      type: String,
      default: '',
    },
  })

  const configs = reactive({
    chart: {
      type: 'column',
    },
    title: {
      text: computed(() => props.title),
    },
    // subtitle: {
    //   text: 'Source: WorldClimate.com',
    // },
    xAxis: {
      categories: computed(() => props.labels),
      crosshair: true,
    },
    yAxis: {
      min: 0,
      // title: {
      //   text: 'Rainfall (mm)',
      // },
    },
    tooltip: {
      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
      pointFormat:
        '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
        `<td style="padding:0"><b>{point.y:.f} ${
          props.unit ? t(props.unit) : ''
        }</b></td></tr>`,
      footerFormat: '</table>',
      shared: true,
      useHTML: true,
    },
    plotOptions: {
      column: {
        pointPadding: 0.2,
        borderWidth: 0,
      },
    },
    series: computed(() => props.series),
    exporting: {
      buttons: {
        contextButton: {
          menuItems: [
            'viewFullscreen',
            'separator',
            'downloadPNG',
            'downloadSVG',
            'downloadPDF',
            'separator',
            'downloadXLS',
          ],
        },
      },
      enabled: true,
    },
    navigation: {
      buttonOptions: {
        align: 'right',
        verticalAlign: 'top',
        y: 0,
      },
    },
  })
</script>

<template>
  <highcharts :options="configs" />
</template>
