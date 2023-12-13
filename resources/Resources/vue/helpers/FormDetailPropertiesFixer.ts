import {
  FormDetailDefault,
  FormDetailEditor,
  FormDetailProperties,
  FormMultiDetailProperties,
} from '~/types/form/detail'

interface PropertiesResponse {
  filterProperties: Array<any>
  unFilterProperties: Array<any>
}
export default (
  properties: FormDetailProperties | FormMultiDetailProperties,
  includes?: Array<string>,
): PropertiesResponse => {
  let filterProperties: FormDetailProperties | FormMultiDetailProperties = []
  const unFilterProperties: Array<FormDetailEditor> = []
  if (Array.isArray(properties)) {
    // only for not multi table
    filterProperties = properties.filter((pro) => pro.type === 'editor') as
      | FormDetailProperties
      | FormMultiDetailProperties

    if (includes && includes.length > 0) {
      properties.forEach((property) => {
        if (includes.includes(property.field)) {
          // eslint-disable-next-line @typescript-eslint/ban-ts-comment
          // @ts-ignore
          filterProperties.push(property)
        }
      })
    }
  } else {
    // for multi table
    Object.entries(properties).forEach(([_, val]) => {
      const tempProp = []
      const tempPropUnfilter = []
      val.forEach((vl) => {
        if (vl.type === 'editor') {
          tempProp.push(vl)
        }
        tempPropUnfilter.push(vl)
      })

      // need update
      if (filterProperties.length) {
        tempProp.forEach((tp) => {
          let match = 0
          filterProperties.forEach((fp) => {
            if (tp.field === fp.field && tp.type === fp.type) {
              match++
            }
          })

          if (!match) {
            filterProperties.push(tp)
          }
        })
      } else {
        tempProp.forEach((tp) => {
          filterProperties.push(tp)
        })
      }

      if (unFilterProperties.length) {
        tempPropUnfilter.forEach((tp) => {
          let match = 0
          unFilterProperties.forEach((fp) => {
            if (tp.field === fp.field && tp.type === fp.type) {
              match++
            }
          })

          if (!match) {
            unFilterProperties.push(tp)
          }
        })
      } else {
        tempPropUnfilter.forEach((tp) => {
          unFilterProperties.push(tp)
        })
      }
    })
  }

  return {
    filterProperties,
    unFilterProperties,
  }
}
