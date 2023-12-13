type items = Array<any>

export default (array: items) => {
    while (array.length > 0) {
        array.pop()
    }
}