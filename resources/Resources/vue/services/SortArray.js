/**
 * example: schema.forms[2].rows.sort(SortArray('xxx.xxx', 'ascending'))
 * @param key
 * @param direction
 * @returns {function(*, *): *}
 */
export default (key, direction = 'ascending') => (a, b) => {
    const
        factor = +(direction === 'ascending') || -1,
        getValue = (object, keys) => keys.split('.').reduce((o, k) => o?.[k], object),
        valueA = getValue(a, key),
        valueB = getValue(b, key);

    return factor * (valueA > valueB || -(valueA < valueB));
}