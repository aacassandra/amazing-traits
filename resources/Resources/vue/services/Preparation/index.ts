import { SchemaV1 } from '~/types/form/form-v1'
import { ObjectReader, ObjectUpdater } from '~/services'
import { FormDetailPropertiesFixer } from '~/helpers'
import axios from 'axios'
import { FormDetailEditor } from '~/types/form/detail'

const removeErrors = (data) => {
  return new Promise(async (resolve) => {
    const temp = []
    for (let i = 0; i < data.length; i++) {
      const row = data[i]
      const json = {}
      for (const key in row) {
        const val = row[key]
        if (key !== 'errors') {
          if (Array.isArray(val)) {
            json[key] = await removeErrors(val)
          } else {
            json[key] = val
          }
        }
      }

      temp.push(json)
    }

    resolve(temp)
  })
}

const uploadFile = (file: File) => {
  return new Promise((resolve) => {
    const cookieToken = localStorage.getItem('_token')
    // const filename = file.name.replace(/[^\w.-]+/g, '')
    const host = import.meta.env.VITE_APP_API_HOST

    const formData = new FormData()
    formData.append('file', file)
    axios({
      method: 'POST',
      url: `${host}/api/upload`,
      headers: {
        Authorization: cookieToken,
        'Content-Type': 'multipart/form-data',
      },
      data: formData,
    }).then((res) => {
      resolve(res.data.replace(host, ''))
    })
  })
}

export default (schema: SchemaV1) => {
  return new Promise(async (resolve) => {
    const temp = {}

    for (let i = 0; i < schema.forms.length; i++) {
      const form = schema.forms[i]
      if (form.type === 'header') {
        temp[form.key] = {}
        temp[form.key][form.model] = {}
        for (const key in form.properties) {
          const pro = form.properties[key]
          if (key !== 'errors') {
            if (pro.type === 'filefield') {
              if (!pro.options.autoUpload) {
                if (pro.value) {
                  temp[form.key][form.model][key] =
                    typeof pro.value === 'string'
                      ? pro.value
                      : await uploadFile(pro.value)
                } else if (pro.linkResponse) {
                  temp[form.key][form.model][key] = pro.linkResponse
                } else {
                  // ignore when value null or linkResponse from edit is undefined
                }
              } else {
                temp[form.key][form.model][key] = pro.linkResponse
              }
            } else {
              temp[form.key][form.model][key] = pro.value
            }
          }
        }
      }

      if (form.type === 'detail') {
        const { relation } = form
        const details = []
        for (let u = 0; u < form.rows.length; u++) {
          const row = form.rows[u]
          if (!row.deleted) {
            // if deleted from edit mode
            const tempDet = {}
            const fixProperties = FormDetailPropertiesFixer(form.properties)

            for (let u1 = 0; u1 < fixProperties.filterProperties.length; u1++) {
              const pro: FormDetailEditor = fixProperties.filterProperties[u1]
              if (pro.type === 'editor' && pro.editor) {
                const val = ObjectReader(row, pro.field)
                if (pro.editor.type === 'filefield') {
                  if (!pro.editor.options.autoUpload && val) {
                    const resUrl =
                      typeof val === 'string' ? val : await uploadFile(val)
                    ObjectUpdater(tempDet, pro.field, resUrl)
                  } else {
                    ObjectUpdater(tempDet, pro.field, val)
                  }
                } else if (
                  pro.editor.type === 'daterangefield' ||
                  pro.editor.type === 'datetimerangefield'
                ) {
                  const { start, end } = pro.editor.options.getFrom
                  if (val !== undefined) {
                    if (start && end) {
                      ObjectUpdater(tempDet, start, val.start)
                      ObjectUpdater(tempDet, end, val.end)
                    } else {
                      ObjectUpdater(tempDet, pro.field, val)
                    }
                  }
                } else {
                  if (val !== undefined) {
                    ObjectUpdater(tempDet, pro.field, val)
                  }
                }
              }
            }

            // add inluded files
            if (form.includes && form.includes.length) {
              for (let x = 0; x < form.includes.length; x++) {
                const field = form.includes[x]
                ObjectUpdater(tempDet, field, row[field])
              }
            }

            // removing errors field
            for (const key in tempDet) {
              const val = tempDet[key]
              if (Array.isArray(val)) {
                tempDet[key] = await removeErrors(val)
              }
            }

            details.push(tempDet)
          }
        }

        temp[relation.key][relation.model][relation.column] = details
      }
    }

    resolve(temp)
  })
}
