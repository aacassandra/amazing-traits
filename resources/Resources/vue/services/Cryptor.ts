import CryptoJS from 'crypto-js'

const secret_key = import.meta.env.VITE_CRYPTO_KEY
const secret_iv = import.meta.env.VITE_CRYPTO_IV

const text = 'alaks@123'

const keyHex = CryptoJS.SHA256(secret_key).toString().substring(0, 32) // Fix 1: Truncate the key to 32 bytes
const ivHex = CryptoJS.SHA256(secret_iv).toString().substring(0, 16)
const key = CryptoJS.enc.Utf8.parse(keyHex) // Fix 2: Convert key and IV to a WordArray using the UTF8 encoder
const iv = CryptoJS.enc.Utf8.parse(ivHex)

function getRandomInt(min, max) {
  min = Math.ceil(min)
  max = Math.floor(max)
  return Math.floor(Math.random() * (max - min + 1)) + min
}

function aesEncrypt(data) {
  const before = data + '::' + getRandomInt(100, 999)
  const cipher = CryptoJS.AES.encrypt(before, key, {
    iv: iv,
    mode: CryptoJS.mode.CBC,
    padding: CryptoJS.pad.Pkcs7,
  })
  return btoa(cipher.toString())
}

function aesDecrypt(data) {
  if (typeof data === 'number') {
    return data
  }

  try {
    const cipher = CryptoJS.AES.decrypt(atob(data), key, {
      iv: iv,
      mode: CryptoJS.mode.CBC,
      padding: CryptoJS.pad.Pkcs7,
    })
    const output = cipher.toString(CryptoJS.enc.Utf8)

    if (output.indexOf('::') > -1) {
      const split = output.split('::')
      return split[0]
    } else {
      return output
    }
  } catch (error) {
    // Tangani kesalahan yang mungkin terjadi saat dekripsi
    // console.error('Terjadi kesalahan saat dekripsi:', error.message)
    return null // Atau Anda bisa mengembalikan nilai lain yang menandakan kesalahan
  }
}

export default {
  encrypt: aesEncrypt,
  decrypt: aesDecrypt,
}
