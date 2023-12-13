export default (data: any, path: string, value: any, push = false) => {
  let schema = data // a moving reference to internal objects within obj
  const pList = path.split('.')
  const len = pList.length
  for (let i = 0; i < len - 1; i++) {
    const elem = pList[i]
    if (!schema[elem]) schema[elem] = {}
    schema = schema[elem]
  }

  if (push) {
    schema[pList[len - 1]].push(value)
  } else {
    schema[pList[len - 1]] = value
  }
}
