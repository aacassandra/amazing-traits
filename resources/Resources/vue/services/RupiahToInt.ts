export default function(str: string) {
  let res = str.replace("Rp. ", "");
  let splitter = res.split(".");
  let newRes = "";
  splitter.forEach((e, i) => {
    if (i == 0) {
      newRes = e;
    } else {
      newRes = newRes + e;
    }
  });

  return parseInt(newRes);
}