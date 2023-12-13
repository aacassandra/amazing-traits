import axios, { AxiosRequestConfig } from 'axios'

// file is event.target.files[0]
export default (file: File) => {
  return new Promise((resolve, reject) => {
    const filename = file.name.replace(/[^\w.-]+/g, "");
    const config: AxiosRequestConfig = {
      method: 'post',
      url: `${process.env.NUXT_PARSE_SERVER_URL}/files/${filename}`,
      headers: {
        'X-Parse-Application-Id': process.env.NUXT_PARSE_APP_ID || "",
        'X-Parse-Client-Key': process.env.NUXT_PARSE_CLIENT_KEY || "",
        'Content-Type': file.type
      },
      data: file
    };

    axios(config)
      .then((res) => {
        resolve(res);
      })
      .catch((err) => {
        reject(err);
      });
  });
}
