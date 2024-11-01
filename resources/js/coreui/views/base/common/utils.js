export function getDate (date) {
  if (date instanceof Date && !isNaN(date.valueOf())) {
    const year          = date.getFullYear()
    const month         = (date.getMonth() + 1).toString()
    const formatedMonth = month.length === 1 ? `0${month}` : month
    const day           = date.getDate().toString()
    const formatedDay   = day.length === 1 ? `0${day}` : day
    return `${year}-${formatedMonth}-${formatedDay}`
  }
  return ''
}

export function getDateCustom (date) {
  if (date instanceof Date && !isNaN(date.valueOf())) {
    const year          = date.getFullYear()
    const month         = (date.getMonth() + 1).toString()
    const formatedMonth = month.length === 1 ? `0${month}` : month
    const day           = date.getDate().toString()
    const formatedDay   = day.length === 1 ? `0${day}` : day
    return `${year}-${formatedMonth}-${formatedDay}`
  }
  return ''
}
