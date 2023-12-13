import axios, { AxiosRequestConfig } from 'axios'

export default (config?: AxiosRequestConfig) => {
  return new Promise((resolve, reject) => {
    const cookieToken = localStorage.getItem('_token')

    let newConfig = config

    if (!newConfig.headers) {
      newConfig = {
        ...newConfig,
        headers: {
          Authorization: cookieToken,
        },
      }
    } else {
      newConfig.headers = {
        ...newConfig.headers,
        Authorization: cookieToken,
      }
    }

    axios(newConfig)
      .then((res) => resolve(res))
      .catch((err) => reject(err))
  })
}
