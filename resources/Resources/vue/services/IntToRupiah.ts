export default function (angka: string | number, prefix: string) {
  angka = angka.toString()
  const splitDecimal = angka.split('.')
  angka = splitDecimal[0]
  let number_string = angka.replace(/[^,\d]/g, '').toString(),
    split = number_string.split(','),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi)

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if (ribuan) {
    const separator = sisa ? '.' : ''
    rupiah += separator + ribuan.join('.')
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
  return prefix == undefined ? rupiah : rupiah ? 'Rp. ' + rupiah : ''
}
