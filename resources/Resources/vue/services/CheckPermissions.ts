interface Item {
  permissions?: Array<string>
}

export default (sbPermissions: Array<string>, permissions: Array<any>) => {
  if (sbPermissions) {
    if (!sbPermissions.length) {
      return true
    }

    let match = 0
    sbPermissions.forEach((pm) => {
      permissions.forEach((pma) => {
        if (pm === pma) {
          match++
        }
      })
    })

    return !!match
  } else {
    return true
  }
}
