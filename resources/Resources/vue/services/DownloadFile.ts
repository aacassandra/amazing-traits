import axios from 'axios'

export default (opt = { url: '', fileName: '' }) => {
  return new Promise((res, rej) => {
    axios({
      url: opt.url,
      method: 'GET',
      responseType: 'blob',
    })
      .then((response) => {
        // create file link in browser's memory
        const href = URL.createObjectURL(response.data)

        // create "a" HTML element with href to file & click
        const link = document.createElement('a')
        link.href = href
        // link.setAttribute('download', 'Laporan-Realisasi.xlsx') // or any other extension
        link.setAttribute('download', opt.fileName) // or any other extension
        document.body.appendChild(link)
        link.click()

        // clean up "a" element & remove ObjectURL
        document.body.removeChild(link)
        URL.revokeObjectURL(href)
        res(true)
      })
      .catch((err) => {
        console.log(err.response.data)
        rej(err)
      })
  })
}
