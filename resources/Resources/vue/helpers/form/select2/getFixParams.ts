import { Field } from '~/types/components/atoms/forms/header/select'

export default (options: {
  params: { [key: string]: any }
  value: any
  term?: string
  isById?: boolean
  isMultiple?: boolean
  field: Field
  showAll?: boolean // needed when using selectfield v1, when set value from getById
  model?: string
}) => {
  const responseParams = {}
  if (options.params.where) {
    responseParams['where'] = options.params.where
  }

  Object.entries(options.params).forEach(([key, value]) => {
    if (key !== 'where') {
      responseParams[key] = value
    }
  })

  if (options.isById) {
    if (!options.isMultiple && !options.showAll) {
      responseParams['where'][options.field.value] = options.value
    } else if (!options.showAll) {
      // where[options.field.value] = {
      //   $in: options.value,
      // }
    }
  } else {
    const type = options.field.type ? options.field.type.display : 'string'

    let whereColumn = options.field.display
    if (options.model) {
      whereColumn = `${options.model}.${options.field.display}`
    }

    if (responseParams['where']) {
      if (Object.keys(responseParams['where']).length && options.term) {
        if (type === 'string') {
          responseParams[
            'where'
          ] += ` and ${whereColumn} LIKE '%${options.term}%'`
        } else if (type === 'number') {
          responseParams['where'] += ` and ${whereColumn} = ${options.term}`
        } else {
          // defualt string
          responseParams[
            'where'
          ] += ` and ${whereColumn} LIKE '%${options.term}%'`
        }
      } else if (options.term) {
        if (type === 'string') {
          responseParams['where'] = `${whereColumn} LIKE '%${options.term}%'`
        } else if (type === 'number') {
          responseParams['where'] = `${whereColumn} = ${options.term}`
        } else {
          // defualt string
          responseParams['where'] = `${whereColumn} LIKE '%${options.term}%'`
        }
      }
    } else if (options.term) {
      if (type === 'string') {
        responseParams['where'] = `${whereColumn} LIKE '%${options.term}%'`
      } else if (type === 'number') {
        responseParams['where'] = `${whereColumn} = ${options.term}`
      } else {
        // defualt string
        responseParams['where'] = `${whereColumn} LIKE '%${options.term}%'`
      }
    }
  }

  return responseParams
}
