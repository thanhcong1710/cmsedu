/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

/* eslint-disable */
import config from '../../../../config/env.js'
import Vue from 'vue'
import md5 from 'js-md5'
import Axios from 'axios'
import db from './database'
import moment from 'moment'
import CryptoJS from 'crypto-js'
import VueRouter from 'vue-router'
import auth from './authentication'
import { isUndefined, isNullOrUndefined, isString } from 'util'

Vue.prototype.moment = moment
Vue.prototype.crypt = CryptoJS

const RSA = config.ROLE_SUPER_ADMINISTRATOR
const RCF = config.ROLE_MANAGERS
const RAM = config.ROLE_ADMINISTRATOR
const RZC = config.ROLE_ZONE_CEO
const RRC = config.ROLE_REGION_CEO
const RBC = config.ROLE_BRANCH_CEO
const RCA = config.ROLE_CASHIER
const REL = config.ROLE_EC_LEADER
const REC = config.ROLE_EC
const ROM = config.ROLE_OM
const RCM = config.ROLE_CM
const RTE = config.ROLE_TEACHER
const RCC = config.ROLE_CM_CASHIER
const ROC = config.ROLE_OM_CASHIER
const RHC = config.ROLE_HEAD_CASHIER

const SESSION_DATA = '__as'
const SESSION_USER = '__uf'
const SESSION_INFO = '__if'
const USERS_COOKIE = '__ck'
const TOKEN_KEY_ID = '__ast'

let temp = {}
temp = {
  calling: {}
}

const bus = new Vue()

const apax = new Vue()

const r = {
  super_administrator: RSA,
  founder: RCF,
  admin: RAM,
  zone_ceo: RZC,
  region_ceo: RRC,
  branch_ceo: RBC,
  cashier: RCA,
  ec_leader: REL,
  ec: REC,
  om: ROM,
  cm: RCM,
  teacher: RTE,
  cm_cashier: RCC,
  om_cashier: ROC,
  head_cashier: RHC
}

const stf = config.LMS_STF_ID

const url = {
  host: config.APP_URL !== '' ? config.APP_URL : $(location).attr('host'),
  base: $(location).attr('hostname'),
  port: $(location).attr('port'),
  prot: $(location).attr('protocol'),
  path: $(location).attr('pathname'),
  href: $(location).attr('href'),
  hash: $(location).attr('hash'),
  attr: $(location).attr('search')
}

const v = {
  img: {
    size: config.IMG_SIZE,
    exts: config.IMG_TYPE
  },
  file_img: {
    size: config.IMG_SIZE,
    exts: config.IMG_TYPE
  },
  doc: {
    size: config.DOC_SIZE,
    exts: config.DOC_TYPE
  },
  transfer_file: {
      size: config.FILE_SIZE,
      exts: config.FILE_TYPE
  },

  check(t, ft) {
    let resp = 'not_valid'
    const type = t.toString().replace('.', '').toLowerCase()
    if ((this.img.exts.indexOf(type) > -1) && (ft === 'img')) {
      resp = 'img'
    }
    if ((this.doc.exts.indexOf(type) > -1) && (ft === 'doc')) {
      resp = 'doc'
    }
    if ((this.transfer_file.exts.indexOf(type) > -1) && (ft === 'transfer_file')){
        resp = 'transfer_file'
    }
    return resp
  }
}

const ada = {
  c: () => Vue.prototype.global = {},
  s: (k, v = null) => {
    if (isUndefined(Vue.prototype.global)) {
      Vue.prototype.global = {}
    }
    Vue.prototype.global[k] = v
  },
  i: (m, f = null) => Vue.prototype.global[m] = f,
  g: (k, v = null) => isUndefined(Vue.prototype.global[k]) ? v : Vue.prototype.global[k],
  r: (k) => !isUndefined(Vue.prototype.global[k]) ? delete Vue.prototype.global[k] : false,
  d: (m, p = null) => !isUndefined(Vue.prototype.global[m]) ? Vue.prototype.global[m](p) : false
}

Vue.prototype.o = ada

const token = () => {
  let resp = db.c.g(TOKEN_KEY_ID)
  const valid = db.l.g(USERS_COOKIE)
  return resp && resp !== '' && resp === valid ? resp : valid ? valid : resp
}

const session = () => {
  return {
    roles: JSON.parse(db.l.g(SESSION_DATA)),
    user: JSON.parse(db.l.g(SESSION_USER)),
    info: JSON.parse(db.l.g(SESSION_INFO)),
  }
}

const chk = {
  boss: () => {
    const u = JSON.parse(db.l.g(SESSION_USER))
    return (u.role_id === RCM || u.role_id === REC) && u.superior_id === ''
  }
}

const la = {
  edit_contract_ec: [RSA, RCF, RAM, RZC, RRC, RBC, REL, ROM],
  edit_contract_cm: [RSA, RCF, RAM, RZC, RRC, RBC, ROM],
}

const ca = (act = '') => {
  let resp = false
  if (act) {
    const u = JSON.parse(db.l.g(SESSION_USER))
    if (u.role_id) {
      const role = parseInt(u.role_id, 10)
      resp = is.has(la, act) ? is.in(la[act], role) : resp
    }
  }
  return resp
}

const authorized = roleId => {
  const role = isNullOrUndefined(roleId) ? session().user.role_id : roleId
  return role > RBC;
}

const role = (id = 0) => {
  const uid = session().user && is.has(session().user, 'role_id') ? session().user.role_id : 0
  let rid = parseInt(id, 10) > 0 ? id : uid
  let resp = ''
  Object.keys(r).map(k => {
    if (parseInt(rid, 10) === parseInt(r[k], 10)) {
      resp = k
    }
  })
  return resp
}

const a = unauthorize => {
  const check = isNullOrUndefined(unauthorize) || unauthorize === '' || parseInt(unauthorize) === 0
  if (check) {
    const tokenKey = token()
    if (tokenKey) Axios.defaults.headers.common['Authorization'] = tokenKey
  } else {
    if (is.has(Axios.defaults.headers.common, 'Authorization')) delete Axios.defaults.headers.common['Authorization']
    if (unauthorize === 2) Axios.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded'
  }
  return Axios
}

const g = (link, attributes = null, unauthorize = false, blob = false) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    let hash = ''
    if (!unauthorize) {
      hash = md5(link)
    }
    if (!live(temp.calling) || (live(temp.calling) && !is.has(temp.calling, hash)) || 1==1) {
      if (hash) {
        temp.calling[hash] = 'processing...'
      }
      const payload = {
        params: attributes
      }
      if (blob) {
        payload.responseType = 'blob'
      }
      a().get(link, payload).then(response => {
        let result = response.data
        if(!unauthorize && live(temp.calling) && is.has(temp.calling, hash)){
          delete temp.calling[hash]
        }
        if (!blob && result.code && result.code !== 200) {
          alert(`Không thể truy xuất được dữ liệu từ máy chủ vì:\n\n*${result.message}*`)
          // error(result)
          reject(result)
        } else {
          if (blob) {
            resolve(result)
          } else {
            resolve(result.data)
          }
        }
      }).catch(e => {
        reject(e)
      })
    } else {
      log('Double request', temp.calling[hash])
      reject('Double request...')
    }
  } else {
    reject('Request url is not valid')
  }
})

const p = (link, params = null, unauthorize = false, full_response = false) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    let hash = ''
    if (!unauthorize) {
      hash = md5(`${link}${JSON.stringify(params)}`)
    }
    if (!live(temp.calling) || (live(temp.calling) && !is.has(temp.calling, hash)) || 1==1) {
      if (hash) {
        temp.calling[hash] = 'processing...'
      }
      a().post(link, params).then(response => {
        const result = response.data
        if(!full_response){
          const result = response.data
          if (result.code && result.code !== 200) {
            alert(`Không thể truy xuất được dữ liệu từ máy chủ vì lỗi:\n\n*${result.message}*`)
            // error(result)
            reject(result)
          } else {
            resolve(result.data)
          }
        }else{
          resolve(result)
        }
      }).catch(e => {
        reject(e)
      })
    } else {
      log('Double request', temp.calling[hash])
      reject('Double request...')
    }
  } else {
    reject('Request url is not valid')
  }
})

const put = (link, params = null, unauthorize = false, full_response = false) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    let hash = ''
    if (!unauthorize) {
      hash = md5(`${link}${JSON.stringify(params)}`)
    }
    if (!live(temp.calling) || (live(temp.calling) && !is.has(temp.calling, hash)) || 1==1) {
      if (hash) {
        temp.calling[hash] = 'processing...'
      }
      a().put(link, params).then(response => {
        const result = response.data
        if(!full_response){
          if (result.code && result.code !== 200) {
            alert(`Không thể truy xuất được dữ liệu từ máy chủ vì lỗi:\n\n*${result.message}*`)
            // error(result)
            reject(result)
          } else {
            resolve(result.data)
          }
        }else{
          resolve(result)
        }
      }).catch(e => {
        reject(e)
      })
    } else {
      log('Double request', temp.calling[hash])
      reject('Double request...')
    }
  } else {
    reject('Request url is not valid')
  }
})

const t = (link, params, unauthorize = false) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    let hash = ''
    if (!unauthorize) {
      hash = md5(`${link}${JSON.stringify(params)}`)
    }
    if (!live(temp.calling) || (live(temp.calling) && !is.has(temp.calling, hash)) ||1==1) {
      if (hash) {
        temp.calling[hash] = 'processing...'
      }
      a().put(link, params).then(response => {
        const result = response.data
        if (result.code && result.code !== 200) {
          alert(`Không thể truy xuất được dữ liệu từ máy chủ vì lỗi:\n\n*${result.message}*`)
          // error(result)
          reject(result)
        } else {
          resolve(result.data)
        }
      }).catch(e => {
        reject(e)
      })
    } else {
      log('Double request', temp.calling[hash])
      reject('Double request...')
    }
  } else {
    reject('Request url is not valid')
  }
})

const d = (link, unauthorize = false) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    let hash = ''
    if (!unauthorize) {
      hash = md5(link)
    }
    if (!live(temp.calling) || (live(temp.calling) && !is.has(temp.calling, hash)) ||1==1) {
      if (hash) {
        temp.calling[hash] = 'processing...'
      }
      a().delete(link).then(response => {
        const result = response.data
        if(live(temp.calling) && is.has(temp.calling, hash)){
          delete temp.calling[hash]
        }
        if (result.code && result.code !== 200) {
          alert(`Không thể truy xuất được dữ liệu từ máy chủ vì lỗi:\n\n*${result.message}*`)
          // error(result)
          reject(result)
        } else {
          resolve(result.data)
        }
      }).catch(e => {
        reject(e)
      })
    } else {
      log('Double request', temp.calling[hash])
      reject('Double request...')
    }
  } else {
    reject('Request url is not valid')
  }
})

const e = (link) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    const tokenKey = token()
    Axios.get(link, {
      headers: {
        Authorization: tokenKey
      },
      responseType: "blob"
    }).then(response => {
        resolve(response.data)
      }).catch(e => {
        reject(e)
      })
  } else {
    reject('Request url is not valid')
  }
})

const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

const is = {
  in: (obj, key) => obj && Array.isArray(obj) && key ? parseInt(obj.indexOf(key), 10) > -1 : false,
  obj: obj => typeof obj === 'object' && !Array.isArray(obj),
  arr: obj => Array.isArray(obj),
  has: (obj, key) => typeof obj === 'object' && !Array.isArray(obj) ? Object.hasOwnProperty.call(obj, key) : false,
  for: obj => Object.keys(obj)
}

function error(data) {
  if (is.has(data, 'code')) {
    if (data.code === 403) {
      const router = new VueRouter()
      auth.logout(router)
      router.go('/login')
    }
  }
}

function prl (c, n) {
  const l = n > 0 ? n : 150
  let p = c || '-'
  let r = ''
  for (let i = 0; i < l; i++) {
    r += p
  }
  return r
}

const sub = (txt, max) => {
  const lng = max > 10 ? max : 20
  return Vue._.truncate(txt, { length: lng, separator: /,?\.* +/ })
}

const go = (obj, route) => {
  if (obj && route) {
    if (route === '/login') {
      auth.logout(obj)
    } else obj.push(route)
  } else return false
}

const verify = (response, callback, reject) => {
  let resp = null
  if (response) {
    resp = response.data
    if (is.has(resp, 'code') && resp.code === 777) {
      resp.session_expired = true
      reject(resp.data)
    } else {
      callback(resp.data)
    }
  }
}

const pct = (number, precision = 1) => {
  const factor = Math.pow(10, precision)
  return Math.round(number * factor) / factor
}

function log (...obj) {
  if (obj.length === 1) {
    console.log(obj[0])
  } else if (obj.length === 2) {
    const msg = obj[0]
    let cnt = obj.slice(1)
    cnt = isString(cnt) ? cnt : JSON.stringify(cnt)
    console.log(`\n${prl()}\n${msg}: ${cnt}\n${prl()}\n\n`)
  } else {
    const msg = obj[0]
    const arr = obj.slice(1)
    if (arr) {
      let i = 0
      console.log(`\n${prl('=')}\n${msg}:\n${prl('˜')}`)
      arr.map(o => {
          if (_.isObject(o)) {
              console.log(`${JSON.stringify(o)}`)
          } else if (isString(o)) {
              console.log(`${o}`)
          } else {
              console.log(o)
          }
          if (i < arr.length - 1) {
              console.log(`${prl('-')}`)
          }
          i += 1
          return o
      })
      console.log(`${prl('_')}`)
      console.log(`Total: ${arr.length}`)
      console.log(`${prl('=')}\n\n`)
    }
  }
}

const pregquote = (str) => (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, '\\$1')

const highlight = (needle, haystack) =>
  haystack.replace(
    new RegExp('(' + pregquote(needle) + ')', 'ig'),
    '<span class="highlight">$1</span>'
  )

const currency = (num, cur) =>
  num
    .toFixed(1)
    .replace(/(\d)(?=(\d{3})+\.)/g, '$1,')
    .slice(0, -2) + cur

const userstatus = (stt) => {
  let st = ''
  switch (stt) {
    case 1: { st = 'Đang đi làm' } break
    case 2: { st = 'Đang nghỉ' } break
    case 3: { st = 'Đã thôi việc' } break
  }
  return st
}

const print = (id, tlt) => {
  log('Printing', id, tlt)
  const title = tlt || ''
  const contents = $(`#apax-printing-${id}`).html()
  const iframeID = (new Date()).getTime()
  const $frame = $(`<iframe id="${iframeID}" name='printing-frame' />`)
  $frame.appendTo("body")
  const $iframe = $(`#${iframeID}`)
  $iframe.css({
    position: "absolute",
    width: "0px",
    height: "0px",
    left: "-600px",
    top: "-600px"
  })
  setTimeout(function() {
    function setDocType($iframe, doctype){
      let win, doc
      win = $iframe.get(0)
      win = win.contentWindow || win.contentDocument || win
      doc = win.document || win.contentDocument || win
      doc.open()
      doc.write(doctype)
      doc.close()
    }
    setDocType($iframe, '<!DOCTYPE html>')
    const $doc = $iframe.contents()
    const $head = $doc.find("head")
    const $body = $doc.find("body")
    const $base = $('base')
    const baseURL = `${document.location.protocol}//${document.location.host}`
    $head.append(`<base href="${baseURL}">`)
    const media = $(this).attr("media") || "all"
    $body.addClass('print')
    $head.append(`<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="${media}">`)
    $head.append(`<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css" media="${media}">`)
    $head.append(`<link type="text/css" rel="stylesheet" href="/static/css/bootstrap/base.css" media="${media}">`)
    $head.append(`<link type="text/css" rel="stylesheet" href="/static/css/prints/${id}.css" media="${media}">`)
    $head.append(`<title>${tlt}</title>`)
    $body.append(contents.jquery ? contents.clone() : contents)
    setTimeout(function() {
      if (document.queryCommandSupported("print")) {
        $iframe[0].contentWindow.document.execCommand("print", false, null)
      } else {
        $iframe[0].contentWindow.focus()
        $iframe[0].contentWindow.print()
      }
    }, 333)
  }, 333)
}

function load (obj) {
  let resp = {}
  if (is.obj(obj) && is.has(obj, 'pages')) {
    if (is.obj(obj.pages)) {
      is.for(obj.pages).map(key => {
        let path = is.has(obj, 'path') ? obj.path + '/' : obj.name + '/'
        const item = obj.pages[key]
        path = is.has(item, 'f') ? path + item.f : path + key
        // log('Load path', `../views/${path}`)
        const page = {
          path: item.p,
          name: item.n,
          component: require('../views/' + path)
        }
        resp[key] = page
        return key
      })
    }
  }
  // log(`Loading ${obj.name}`, resp)
  return resp
}

const ufc = s =>
  s
    .split(' ')
    .map(
      x => (x[0] ? String(x[0]).toUpperCase() + x.slice(1) : '' + x.slice(1))
    )
    .join(' ')

const jss = o => JSON.stringify(o)

const jsp = s => JSON.parse(s)

function uniless (str) {
  str = str.toLowerCase()
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i')
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
  str = str.replace(/đ/g, 'd')
  return str.toUpperCase()
}

const onlynum = str => str ? str.toString().replace(/\D/g, '') : ''

const checkDateIn = (date, start, end) => {
  let resp = false
  const checkDate = moment(date)
  const startDate = moment(start)
  const endDate = moment(end)
  if (checkDate.isValid() && startDate.isValid() && endDate.isValid()) {
    const chk1 = checkDate.isSameOrAfter(startDate)
    const chk2 = checkDate.isSameOrBefore(endDate)
    if (chk1 === true && chk2 === true) {
      resp = true
    }
  }
  return resp
}

const checkInDaysRange = (date, range = []) => {
  let resp = false
  const checkDate = moment(date)
  if (checkDate.isValid() && range.length) {
    range.forEach(days => {
      const startDate = is.obj(days) && is.has(days, 'start_date') ? moment(days.start_date) : ''
      const endDate = is.obj(days) && is.has(days, 'end_date') ? moment(days.end_date) : ''
      // console.log(`Start: ${days.start_date} - End: ${days.end_date} - Check: ${date}`);
      if (startDate.isValid() && endDate.isValid() && checkDateIn(date, days.start_date, days.end_date)) {
        // console.log(`CHECK RESULT: ${days.start_date} - End: ${days.end_date} - Check: ${date}`);
        resp = true
      }
    })
  }
  return resp
}

function uniq(arr = []) {
  return arr.reduce((a, b) => {
    if (a.indexOf(b) < 0) a.push(b)
    return a
  }, [])
}

function validDate(date) {
  const regex = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/gm;
  let m = regex.exec(`${date}`.trim());
  return m !== null
}

function validHolidays(holidays = []) {
  let resp = holidays
  if (resp.length) {
    resp.sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
  }
  return resp
}
function validClassdays(classdays = []) {
  let resp = Array.isArray(classdays) && classdays.length ? classdays : [2, 5]
  if (Array.isArray(resp) && resp.length && resp.length > 1) {
    const buff = []
    resp.map(no => {
      buff.push(parseInt(no.toString().trim()))
      return no
    })
    resp = uniq(buff)
    resp.sort()
    if (resp[0].toString() === "0") {
      resp.shift()
      resp.push(0)
    }
    resp.map(no => parseInt(no.toString().trim()))
  }
  return resp
}

const calEndDate = (sessions = 0, classdates = [], holydays = [], date = "") => {
  const resp = {
    dates: [],
    total: 0,
    end_date: ''
  }
  const holidays = validHolidays(holydays)
  const classdays = validClassdays(classdates)
  if (sessions && classdays.length && moment(date).isValid()) {
    let sdt = date
    let ckd = moment(sdt)
    let swd = ckd.day()
    let dtl = []
    let nxd = 0
    let stp = 0
    let cwd = 0
    while (sessions > 0) {
      cwd = classdays.indexOf(swd)
      if (cwd > -1) {
        if (!checkInDaysRange(sdt, holidays)) {
          sessions -= 1
          dtl.push(sdt)
        }
        nxd = cwd === (classdays.length - 1) ? classdays[0] : classdays[(cwd + 1)]
        stp = nxd > classdays[cwd] ? nxd - classdays[cwd] : nxd === classdays[cwd] ? 7 : 7 - (classdays[cwd] - nxd)
        sdt = ckd.add(stp, 'days').format('YYYY-MM-DD')
      } else {
        sdt = ckd.add(1, 'days').format('YYYY-MM-DD')
      }
      ckd = moment(sdt)
      swd = ckd.day()
    }
    dtl = uniq(dtl)
    resp.dates = dtl
    resp.total = parseInt(dtl.length, 10)
    resp.end_date = dtl[dtl.length - 1]
  }
  return resp
}

function calSessions(start = '', end = '', holydays = [], classdates = []) {
  const resp = {
    dates: [],
    total: 0,
    end_date: ""
  }
  const classdays = validClassdays(classdates)
  const holidays = validHolidays(holydays)
  if (classdays.length && moment(start).isValid() && moment(end).isValid()) {
    let ckd = start
    let sdt = moment(ckd)
    let edt = moment(end)
    let swd = sdt.day()
    let dtl = []
    let nxd = 0
    let stp = 0
    let cwd = 0
    while (sdt.isSameOrBefore(edt)) {
      cwd = classdays.indexOf(swd)
      if (cwd > -1) {
        nxd = cwd === (classdays.length - 1) ? classdays[0] : classdays[(cwd + 1)]
        stp = nxd > classdays[cwd] ? nxd - classdays[cwd] : nxd === classdays[cwd] ? 7 : 7 - (classdays[cwd] - nxd)
        if (!checkInDaysRange(ckd, holidays)) {
          dtl.push(ckd)
        }
        ckd = sdt.add(stp, 'days').format('YYYY-MM-DD')
      } else {
        ckd = sdt.add(1, 'days').format('YYYY-MM-DD')
      }
      sdt = moment(ckd)
      swd = sdt.day()
    }
    dtl = uniq(dtl)
    resp.dates = dtl
    resp.total = parseInt(dtl.length, 10)
    resp.end_date = dtl[dtl.length - 1]
  }
  return resp
}

const getRealSessions = (
  sessions,
  classdates = [],
  holydays = [],
  date = ""
) => {
  const resp = {
    dates: [],
    total: 0,
    end_date: ""
  }
  let startDate = moment(date)
  const classdays = validClassdays(classdates)
  const holidays = validHolidays(holydays)
  if (sessions && classdays && startDate.isValid()) {
    let startDay = startDate.day()
    let checkedDate = date
    let dateList = []
    let stepDays = 0
    let buffDay = 0
    let nextDay = 0
    const firstDay = startDay
    // console.log(`Start date: ${date} - Classdays: ${JSON.stringify(classdays)} - Start: ${startDay}`);
    if (classdays.indexOf(startDay) > -1 && checkInDaysRange(date, holidays) === false) {
      // console.log(`*SD: ${startDay}|${startDate.format("YYYY-MM-DD")} ST: 0 - CKD: ${date}`)
      dateList.push(date)
      sessions -= 1
    }
    while (sessions > 0) {
      if (classdays.length > 0) {
        classdays.forEach(classday => {
          nextDay = classday
          buffDay = startDay
          startDay = nextDay
          if (buffDay === nextDay) {
            stepDays = 0
          } else if (nextDay > buffDay) {
            stepDays = parseInt(nextDay - buffDay, 10)
          } else if (nextDay === 0 && buffDay > 0) {
            stepDays = 7 - parseInt(buffDay, 10)
          } else if (firstDay === buffDay && (classdays[1] > firstDay || classdays[1] === 0)) {
            stepDays = classdays[1] === 0 ? 7 - firstDay : parseInt(classdays[1] - firstDay, 10)
            startDay = classdays[1]
          } else {
            stepDays = 7 - parseInt(buffDay - nextDay, 10)
          }
          const checkingDate = startDate
            .add(stepDays, "days")
            .format("YYYY-MM-DD")
          // console.log(`SD: ${buffDay}|${startDate.format("YYYY-MM-DD")} - CD/ND: ${classday} - ST: ${stepDays} - CKD: ${checkingDate}`);
          startDate = moment(checkingDate)
          if (dateList.indexOf(checkingDate) === -1 && checkInDaysRange(checkingDate, holidays) === false && sessions > 0) {
            // console.log(`Pick this date: ${checkingDate}`);
            sessions -= 1;
            dateList.push(checkingDate);
          } else {
            // console.log(`This date: ${checkingDate} is in holidays`);
          }
        })
      }
    }
    dateList = uniq(dateList)
    resp.dates = dateList
    resp.total = parseInt(dateList.length, 10)
    resp.end_date = dateList[dateList.length - 1]
  }
  return resp
}

function calcDoneSessions(
  start,
  end,
  holydays = [],
  classdates = [],
  date = ""
) {
  const resp = {
    dates: [],
    total: 0,
    end_date: ""
  };
  const startDate = moment(start)
  const endDate = moment(end)
  let checkDate = start
  let startDay = startDate.day()
  let dateList = []
  let stepDays = 0
  let buffDay = 0
  let nextDay = 0
  const firstDay = startDay
  const classdays = validClassdays(classdates)
  const holidays = validHolidays(holydays)

  if ((classdays.indexOf(startDay) > -1) && checkInDaysRange(start, holidays) == false) {
      dateList.push(start)
  }
  let holding = 0
  while (moment(checkDate).isSameOrBefore(endDate) && holding < 365) {
    classdays.forEach(classday => {
      nextDay = classday
      buffDay = startDay
      startDay = nextDay
      if (buffDay === nextDay) {
        stepDays = 0
      } else if (nextDay > buffDay) {
        stepDays = parseInt(nextDay - buffDay, 10)
      } else if (nextDay === 0 && buffDay > 0) {
        stepDays = 7 - parseInt(buffDay, 10)
      } else if (firstDay === buffDay && (classdays[1] > firstDay || classdays[1] === 0)) {
        stepDays = classdays[1] === 0 ? 7 - firstDay : parseInt(classdays[1] - firstDay, 10)
        startDay = classdays[1]
      } else {
        stepDays = 7 - parseInt(buffDay - nextDay, 10);
      }
      const checkingDate = moment(checkDate)
        .add(stepDays, "days")
        .format("YYYY-MM-DD")
      checkDate = checkingDate
      if (dateList.indexOf(checkingDate) === -1 && checkInDaysRange(checkingDate, holidays) == false && moment(checkingDate).isSameOrBefore(endDate)) {
        dateList.push(checkingDate)
      } else {
        holding += 1
      }
    })
  }
  dateList = uniq(dateList)
  resp.dates = dateList
  resp.total = parseInt(dateList.length, 10)
  resp.end_date = dateList[dateList.length - 1]
  return resp
}

function calcNewStartDate(end, classdays = [2, 5], holidays = []) {
  let resp = null
  const endDate = moment(end)
  const step = classdays.indexOf(endDate.day()) > -1 ? 2 : 1
  const date_info = getRealSessions(step, classdays, holidays, end)
  resp = date_info.end_date ? date_info.end_date : null
  return end_date
}

const ecr = (content, key = "Apax English") => CryptoJS.AES.encrypt(content, key).toString()
const dcr = (content, key = "Apax English") => CryptoJS.AES.decrypt(content, key)

function live(variable) {
    return (typeof variable !== 'undefined');
}

function validFile(file) {
    let resp = {
        is_valid: false,
        message: 'Định dạng file không hợp lệ'
    };
    const validTypes = ["image/png","image/jpeg","application/pdf","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/msword"];
    let pos = validTypes.indexOf(file.type);
    if(pos > -1){
      resp.is_valid = true;
      resp.message = '';
      switch(pos){
        case 0:
        case 1:
          resp.icon = 'fa-file-image-o';
          break;
        case 2:
          resp.icon = 'fa-file-pdf-o';
          break;
        default:
          resp.icon = 'fa-file-word-o';
      }
    }

    return resp;
}

const pre = date => moment(date).subtract(1,'days').format('YYYY-MM-DD');
const nxt = date => moment(date).add(1,'days').format('YYYY-MM-DD');

const rs = (obj, def = null) => {
  if (typeof obj === 'object' && !Array.isArray(obj)) {
    if (Object.keys(obj).length) {
      Object.keys(obj).forEach(key => {
        obj[key] = rs(obj[key])
      })
    } else obj = {}
  } else if (typeof obj === 'object' && Array.isArray(obj)) {
    obj = []
  } else if (typeof obj === 'number') {
    obj = 0
  } else if (typeof obj === 'boolean') {
    obj = false
  } else if (typeof obj === 'string') {
    obj = obj === 'display' ? 'hidden' : ''
  }
  return def !== null ? def : obj
}

const pr = (str, pat, val) => str && pat && val ? str.toString().replace(`[${pat}]`, val) : str

const set = (obj, val = {}, lim = true) => {
  let resp = obj
  if (is.obj(obj) && val) {
    Object.keys(val).forEach(key => {
      if (lim) {
        if (is.has(obj, key)) {
          resp[key] = val[key]
        }
      } else resp[key] = val[key]
    })
  } else if (is.arr(obj)) {
    resp.map(o => {
      if (is.obj(o)) {
        const key = Object.keys(o)[0]
        if (is.has(val, key)) {
          o[key] = val[key]
        }
      }
      return o
    })
  } else {
    resp = val
  }
  return resp
}

const m = alias => {
  return {
    list: {
      html: {
        page: {
          title: 'Nhấp vào để xem chi tiết',
          url: {
            link: `/${alias}/`,
            apis: `/api/${alias}/`,
            list: `/api/${alias}/list/`,
            load: `/api/${alias}/filter/`
          }
        },
        dom: {},
        data: {},
        search: {
          keyword: ''
        },
        loading: {
          action: true,
          class: false,
          data: 'Đang tải dữ liệu...',
          content: 'Đang xử lý...'
        },
        filter: {
        },
        order: {
          by: '',
          to: 'DESC'
        },
        pagination: {
          url: '',
          id: '',
          style: 'line',
          class: '',
          spage: 1,
          ppage: 1,
          npage: 2,
          lpage: 2,
          cpage: 1,
          total: 2,
          limit: 20,
          pages: []
        },
        completed: false,
        target:'',
      },
      keys: {},
      vals: {},
      item: {},
      data: {},
      list: [],
      cache: {
        item: {},
        data: {},
        list: []
      },
      temp: {},
      expired: false,
      session: session(),
    },
    page: {
      html: {
        page: {
          title: 'Nhấp vào để xem chi tiết',
          url: {
            link: `/${alias}/`,
            apis: `/api/${alias}/`
          }
        },
        dom: {},
        loading: {
          action: true,
          class: false,
          content: 'Đang tải dữ liệu...',
          source: '/static/img/images/loading/mnl_19.gif'
        },
        config: {}
      },
      obj: {},
      keys: {},
      vals: {},
      item: {},
      data: {},
      list: [],
      param: {},
      filter: {},
      cache: {
        dom: {},
        data: {
          obj: {},
          item: {},
          list: []
        }
      },
      temp: {},
      default: {},
      expired: false,
      completed: false,
      session: session(),
      back_date:'',
    }
  }
};

const makeParamsUrl = (params) => {
  let res = [];
  if (!params) return ""

  Object.keys(params).forEach((key) => {
    let value = params[key];
    if (Array.isArray(value)) {
      value.forEach((v) => {
        res.push(`${encodeURIComponent(key)}[]=${encodeURIComponent(v)}`)
      })
    } else {
      res.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
    }
  })
  return res.join("&");
}

const getFile = (url, params) => {
  return Axios.request({
    url: `${url}?${makeParamsUrl(params)}`,
    method: 'GET',
    responseType: 'blob',
  }).then((response) => {
    const contentDisposition = response.headers["content-disposition"] || "";
    const filename = contentDisposition.substring(
      contentDisposition.lastIndexOf("=") + 1,
      contentDisposition.length
    );
    const fileURL = window.URL.createObjectURL(new Blob([response.data]));
    const fileLink = document.createElement('a');
    fileLink.href = fileURL;
    fileLink.setAttribute('download', filename);
    document.body.appendChild(fileLink);
    fileLink.click();
  });
}

const getFilePost = (url, params) => {
  return Axios.request({
    url: `${url}`,
    method: 'POST',
    data: params,
    headers: {'Content-Type': 'multipart/form-data' },
    responseType: 'blob',
  }).then((response) => {
    const contentDisposition = response.headers["content-disposition"] || "";
    const filename = contentDisposition.substring(
      contentDisposition.lastIndexOf("=") + 1,
      contentDisposition.length
    );
    const fileURL = window.URL.createObjectURL(new Blob([response.data]));
    const fileLink = document.createElement('a');
    fileLink.href = fileURL;
    fileLink.setAttribute('download', filename);
    document.body.appendChild(fileLink);
    fileLink.click();
  });
}

const vld = {
  email: (str) => {
    // const pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(str)
  },
  null: (str) => {
    const pattern = /\S+/
    return pattern.test(str)
  },
  num: (str) => {
    // const pattern = /^\d+$/
    const pattern = /^-?\d+\.?\d*$/
    return pattern.test(str)
  },
  cc: (str) => {
    const pattern = /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|(222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11}|62[0-9]{14})$/
    return pattern.test(str)
  },
  visa: (str) => {
    const pattern = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/
    return pattern.test(str)
  },
  master: (str) => {
    const pattern = /^(?:5[1-5][0-9]{14})$/
    return pattern.test(str)
  },
  amex: (str) => {
    const pattern = /^(?:3[47][0-9]{13})$/
    return pattern.test(str)
  },
  discover: (str) => {
    const pattern = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/
    return pattern.test(str)
  },
  same: (str1, str2) => {
      return str1 === str2
  },
  phone: (str) => {
    const pattern = /^(\(?(0[0-9]{2,3})\)?|\)?(\+84)\)?[-. ]?([0-9]{3}))[-. ]?([0-9]{3})[-. ]?([0-9]{3})$/gm
    return pattern.test(str)
  }
}

function fmc (input) {
  let code = ''
  let drap = null
  let resp = {
    s: '',
    n: 0
  }
  if (!input || input.toString() === '' || input.toString() === '0') {
    resp.n = 0
    resp.s = '0'
  } else {
    drap = input.toString().replace(/[\D\s\._\-]+/g, "")
    drap = drap ? parseInt(drap, 10) : 0
    resp.n = drap
    resp.s = drap === 0 ? "0" : `${drap.toLocaleString( "en-US" )}`
  }
  return resp
}

const convertDateToString = date => date.getFullYear() + "-" + ((date.getMonth() > 8)?(date.getMonth() + 1):('0'+ '' + (date.getMonth() + 1))) + "-" + ((date.getDate() > 9)?date.getDate():('0'+ '' + (date.getDate())));

const rd = num => {
  let resp = parseInt(num, 10)
  if (num > 999) {
    const tod = num % 1000
    const nod = num / 1000
    if (tod > 0 && tod < 900) {
      resp = nod * 1000
    } else if (tod > 0 && tod > 900) {
      resp = (nod + 1) * 1000
    }
  }
  return resp
}

const formatMoney = input => input.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

const formatNumber = input => parseInt(input.replace(/,/g,""));

const genErrorHtml = message => `<span class='text-danger'><i class='fa fa-exclamation-circle'></i> ${message}</span></br>`;

const genSuccessHtml = message => `<span class='text-success'>${message}</span></br>`;

function cjrn_classdate(needle, haystack) {
  var length = haystack.length;
  for(var i = 0; i < length; i++) {
    if(haystack[i].cjrn_classdate == needle) return haystack[i].cjrn_id;
  }
  return 0;
}

function getDayNameVi(day)
{
  let resp = '';
  switch (day) {
    case 0:
      resp = 'Chủ nhật';
      break;
    case 1:
      resp = 'Thứ 2';
      break;
    case 2:
      resp = 'Thứ 3';
      break;
    case 3:
      resp = 'Thứ 4';
      break;
    case 4:
      resp = 'Thứ 5';
      break;
    case 5:
      resp = 'Thứ 6';
      break;
    case 6:
      resp = 'Thứ 7';
      break;
    default:
      resp = '';
      break;
  }
  return resp;
}

function icon(ext) {
    let resp = 'fa-upload';
    switch (ext){
        case 'jpg':
        case 'png':
            resp = 'fa-file-image-o';
            break;
        case 'pdf':
            resp = 'fa-file-pdf-o';
            break;
        case 'doc':
        case 'docx':
            resp = 'fa-file-word-o';
            break;
        case 'xls':
        case 'xlsx':
            resp = 'fa-file-excel-o';
        default:
            resp = 'fa-upload';
    }
    return resp;
}

function showError(modal, message) {
  modal.message = genErrorHtml(message)
  modal.show = true
}
function showSuccess(modal, message) {
  modal.message = genSuccessHtml(message)
  modal.show = true
}
function showModal(modal, message) {
  modal.message = message
  modal.show = true
}

function setClassDay (transfer_date) {
  return [new Date(transfer_date).getDay()]
}

function isEmailValid (email) {
  let regObj = {
    reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
  }
  return (email == "")? "" : (regObj.reg.test(email)) ? true : false
}

function calSessionTransfer (total_sessions, done_sessions, index, is_studied, total_fee, used_amount) {
  let resp = {
    session: 0,
    amount: 0
  }

  let session_transfer = total_sessions - done_sessions
  let amount_left = total_fee - used_amount
  if(is_studied){
    let hold_sessions = Math.floor(session_transfer * 0.25)

    resp.session = session_transfer - hold_sessions
    resp.amount = amount_left
  }else{
    if(index == 0){
      const hold_amount = 500000
      resp.amount = amount_left - hold_amount
      resp.session = Math.ceil((amount_left - hold_amount) / (total_fee / total_sessions))
    }else{
      resp.amount = amount_left
      resp.session = session_transfer
    }
  }

  return resp
}

function calTransferInfo (session_from_start_to_transfer, session_left, total_sessions, total_fee, is_studied, index) {
  let resp = {
    amount_left: 0,
    amount_from_start_to_transfer_date: 0,
    amount_transfer: 0,
    session_transfer: 0
  }

  let single_price = total_fee / total_sessions
  let amount_left = Math.floor(session_left * single_price)
  let amount_from_start_to_transfer = Math.floor(session_from_start_to_transfer * single_price)
  let amount_transfer = total_fee - amount_from_start_to_transfer

  if(is_studied){
    let hold_amount = amount_transfer * 0.25
    resp.amount_left = amount_left + hold_amount
    resp.amount_from_start_to_transfer_date = amount_from_start_to_transfer + hold_amount
    resp.amount_transfer = amount_transfer - hold_amount
    resp.session_transfer = Math.ceil(resp.amount_transfer / single_price)
  }else{
    if(index == 0){
      let hold_amount = 500000
      resp.amount_left = amount_left + hold_amount
      resp.amount_from_start_to_transfer_date = amount_from_start_to_transfer + hold_amount
      resp.amount_transfer = amount_transfer - hold_amount
      resp.session_transfer = Math.ceil(resp.amount_transfer / single_price)
    }else{
      resp.amount_left = amount_left
      resp.amount_from_start_to_transfer_date = amount_from_start_to_transfer
      resp.amount_transfer = amount_transfer
      resp.session_transfer = Math.ceil(resp.amount_transfer / single_price)
    }
  }

  return resp
}

function upperWord(text) {
  if (text === null || text === undefined) return text;

  return `${text}`.toLowerCase().replace(/(^|\s)\S/g, (l) => l.toUpperCase())
}

function onlyView (roleId) {
  if (roleId == 1200)
    return false
  else
    return true
}
let that = null
const i = obj => that = obj
const f = (v, c = 'đ') => (!isNaN(v) && v > 0 && c !== '' ? `${parseInt(v).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)}${c}` : `0${c}`)
const changeNull = text => text == null ? '' : text
const lg = (val, lbl='Data', cfg = 0) => {
  if (cfg === 4) {
    console.clear()
    console.trace()
  } else if (cfg === 3) {
    console.log("\n%c"+prl('=')+"\n"+lbl+"\n"+prl('.'), "color:Red")
    console.error(JSON.parse(JSON.stringify(val)))
    console.log("%c"+prl('=')+"\n", "color:Red")
  } else if (cfg === 2) {
    console.log("\n%c"+prl('=')+"\n"+lbl+"\n"+prl('-'), "color:DoubleBlue")
    console.info(JSON.parse(JSON.stringify(val)))
    console.log("%c"+prl('=')+"\n", "color:DoubleBlue")
  } else if (cfg === 1) {
    console.log("\n%c"+prl('=')+"\n"+lbl+"\n"+prl('-'), "color:Orange")
    console.warn(JSON.parse(JSON.stringify(val)))
    console.log("%c"+prl('=')+"\n", "color:Orange")
  } else {
    console.log("\n%c"+prl('=')+"\n"+lbl+"\n"+prl('-'), "color:Green")
    console.log(JSON.parse(JSON.stringify(val)))
    console.log("%c"+prl('=')+"\n", "color:Green")
  }
}
function utcToLocal(dateObject) {
  let utc = moment.utc(dateObject).toDate();
  return moment(utc).local().format('YYYY-MM-DD')
}
function isGreaterThan (_from, _to) {
  let _from_time = new Date(_from) // Y-m-d
  let _to_time = new Date(_to) // Y-m-d
  return (_from_time.getTime() > _to_time.getTime())?true:false
}
const isNumber = (variable) => {return typeof variable === 'number'}
function isValidDate(date) {
  let aDate = moment(date, "YYYY-MM-DD", true)
  return aDate.isValid()
}
function processClassDays(class_days_string) {
  let days = class_days_string.split(",")
  let class_days = []
  for(let i in days){
    class_days.push(parseInt(days[i]))
  }

  return class_days
}
const older = (day1, day2) => {
  const date1 = new Date(day1)
  const date2 = new Date(day2)
  return date1.getTime() > date2.getTime()
}

function dateToString (date) {
  const mm = date.getMonth() + 1
  const dd = date.getDate()
  const yyyy = date.getFullYear()
  return [yyyy, (mm > 9 ? '' : '0') + mm, (dd > 9 ? '' : '0') + dd].join('-')
}
export default {
  a,
  e,
  g,
  d,
  p,
  t,
  r,
  m,
  v,
  ca,
  is,
  la,
  re,
  rs,
  go,
  pr,
  rd,
  url,
  chk,
  md5,
  bus,
  fmc,
  set,
  ecr,
  dcr,
  pct,
  put,
  sub,
  ufc,
  jss,
  jsp,
  log,
  pre,
  nxt,
  vld,
  stf,
  apax,
  role,
  icon,
  live,
  load,
  print,
  token,
  verify,
  uniless,
  onlynum,
  session,
  currency,
  highlight,
  authorized,
  userstatus,
  calEndDate,
  checkDateIn,
  calSessions,
  getRealSessions,
  calcDoneSessions,
  checkInDaysRange,
  calcNewStartDate,
  validFile,
  formatMoney,
  formatNumber,
  genErrorHtml,
  genSuccessHtml,
  convertDateToString,
  showError,
  showSuccess,
  showModal,
  setClassDay,
  calSessionTransfer,
  calTransferInfo,
  validDate,
  upperWord,
  makeParamsUrl,
  getFile,
  isEmailValid,
  getDayNameVi,
  getFilePost,
  cjrn_classdate,
  onlyView,
  i,
  f,
  changeNull,
  lg,
  older,
  utcToLocal,
  isGreaterThan,
  isNumber,
  isValidDate,
  processClassDays,
  dateToString
};
