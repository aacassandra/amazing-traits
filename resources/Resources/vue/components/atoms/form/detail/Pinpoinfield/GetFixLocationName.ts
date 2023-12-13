export default (data) => {
  let fixName = ''

  if (data.address.road) {
    fixName += data.address.road
  } else {
    if (data.address.village) {
      fixName += data.address.village
    }
  }

  // Kelurahan (neighbourhood || residential)
  if (data.address.neighbourhood) {
    if (data.address.road || data.address.village) {
      fixName += ', ' + data.address.neighbourhood
    } else {
      fixName += data.address.neighbourhood
    }
  } else if (data.address.residential) {
    if (data.address.road || data.address.village) {
      fixName += ', ' + data.address.residential
    } else {
      fixName += data.address.residential
    }
  }

  // Kecamatan (municipality || suburb|| city_district)
  if (data.address.municipality) {
    if (
      data.address.neighbourhood ||
      data.address.residential ||
      data.address.road ||
      data.address.village
    ) {
      fixName += ', ' + data.address.municipality
    } else {
      fixName += data.address.municipality
    }
  } else if (data.address.suburb) {
    if (
      data.address.neighbourhood ||
      data.address.residential ||
      data.address.road ||
      data.address.village
    ) {
      fixName += ', ' + data.address.suburb
    } else {
      fixName += data.address.suburb
    }
  } else if (data.address.city_district) {
    if (
      data.address.neighbourhood ||
      data.address.residential ||
      data.address.road ||
      data.address.village
    ) {
      fixName += ', ' + data.address.city_district
    } else {
      fixName += data.address.city_district
    }
  }

  // City
  if (data.address.city) {
    fixName += ', ' + data.address.city
  }

  // Province
  if (data.address.state) {
    fixName += ', ' + data.address.state
  }

  // Kode POS
  if (data.address.postcode) {
    fixName += ', ' + data.address.postcode
  }

  return fixName
}
