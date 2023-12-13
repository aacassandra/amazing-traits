import { t } from '~/services'

export default (props, value) => {
  if (Array.isArray(props.column.templates) && props.column.templates.length) {
    let finalTemplate = '<div>#ERROR</div>'
    props.column.templates.forEach((temp) => {
      const split = temp.split('|')
      if (split.length === 2) {
        if (split[0] == value) {
          finalTemplate = split[1].replace(':value', t(value))
        }
      } else if (split.length === 3) {
        if (split[1] === 'replace') {
          // format: ['replace|:search|:replace_with']
          // ex: template: ['replace|/storage/uploads/files/|empty-string']
          // from value '/storage/uploads/files/RAK Belanja - Kab. Gresik - DINAS KESEHATAN 2023.xlsx'
          // output: 'RAK Belanja - Kab. Gresik - DINAS KESEHATAN 2023.xlsx'
          finalTemplate = value.replace(
            split[1],
            split[2] === 'empty-string' ? '' : split[2],
          )
        } else {
          /**
           * example:
           * {
           *   label: 'Active Flag',
           *   type: 'number',
           *   width: '200px',
           *   templates: [
           *     '1|Active|<div class="p-1"><span class="bg-blue-500 px-2 py-1 rounded-md text-white">:value</span></div>',
           *     '0|Inactive|<div class="p-1"><span class="bg-red-500 px-2 py-1 rounded-md text-white">:value</span></div>',
           *   ],
           *   show: true,
           *   model: 'ihm_m_general',
           *   field: 'active_flag',
           *   filter: true,
           *   filterOptions: {
           *     enabled: true,
           *     filterDropdownItems: [
           *       { value: 1, text: 'Active' },
           *       { value: 0, text: 'Inactive' },
           *     ],
           *   },
           * },
           */
          if (JSON.parse(split[0]) === value) {
            finalTemplate = split[2].replace(':value', t(split[1]))
          }
        }
      } else {
        finalTemplate = '<div>#INVALID FORMAT</div>'
      }
    })
    return finalTemplate
  } else {
    return '<div>#ERROR</div>'
  }
}
