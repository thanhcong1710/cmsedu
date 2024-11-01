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

// import auth0 from 'auth0-js'
// import Router from 'vue-router'
// import decode from 'jwt-decode'
import cookies from 'vue-cookies'
import u from './utility'
import db from './database'

const SESSION_STATUS = 'ss'
const SESSION_DATA = '__as'
const SESSION_USER = '__uf'
const SESSION_INFO = '__if'
const USERS_COOKIE = '__ck'
const TOKEN_KEY_ID = '__ast'
const REDIRECT = '/login'
const ACCESS_TOKEN = 'access_token'
// const CLIENT_ID = 'Ada-System'
// const CLIENT_DOMAIN = 'apaxenglish.com'
// const SCOPE = ''
// const AUDIENCE = ''

// var auth = new auth0.WebAuth({
//   clientID: CLIENT_ID,
//   domain: CLIENT_DOMAIN
// })

// var router = new Router({
//   mode: 'history'
// })

const getIdToken = () => cookies.get(TOKEN_KEY_ID)

const getAccessToken = () => localStorage.getItem(ACCESS_TOKEN)

// function clearIdToken () {
//   cookies.remove(TOKEN_KEY_ID)
// }

// function clearAccessToken () {
//   localStorage.removeItem(ACCESS_TOKEN)
// }
function getParameterByName (name) {
  let match = RegExp('[#&]' + name + '=([^&]*)').exec(window.location.hash)
  return match && decodeURIComponent(match[1].replace(/\+/g, ' '))
}

function setAccessToken () {
  let accessToken = getParameterByName(ACCESS_TOKEN)
  localStorage.setItem(ACCESS_TOKEN, accessToken)
}

function setIdToken () {
  let idToken = getParameterByName('id_token')
  localStorage.setItem(ACCESS_TOKEN, idToken)
}
// function getTokenExpirationDate (encodedToken) {
//   const token = decode(encodedToken)
//   if (!token.exp) {
//     return null
//   }
//   const date = new Date(0)
//   date.setUTCSeconds(token.exp)
//   return date
// }

// const isTokenExpired = (token) => getTokenExpirationDate(token) < new Date()

const isLoggedIn = () => {
  const key = db.c.g(TOKEN_KEY_ID)
  const dat = db.l.g(SESSION_DATA)
  const usr = db.l.g(SESSION_USER)
  const inf = db.l.g(SESSION_INFO)
  return dat && dat.length > 10 && key && key.length > 10 && usr && usr.length > 10 && inf && inf.length > 0
}

/* eslint-disable */
function forbidden (to) {
  const session = u.session()
  const urlpath = to.path.replace('/', '')
  return to.path === '/' || to.path === '/dashboard' ? false : !u.is.in(session.roles, urlpath)
}

function login (obj, data, link) {
  db.c.s(TOKEN_KEY_ID, data['access-token'])
  db.l.s(USERS_COOKIE, data['access-token'])
  db.l.s(SESSION_STATUS, 'started')
  db.l.s(SESSION_DATA, JSON.stringify(data.roles))
  db.l.s(SESSION_INFO, JSON.stringify(data.extra))
  db.l.s(SESSION_USER, JSON.stringify(data.information))
  obj.push(link)
}

function logout (obj) {
  u.a().get('/api/logout').then(r => {})
  db.l.s(SESSION_STATUS, '')
  db.l.s(SESSION_DATA, '?')
  db.l.s(SESSION_USER, '?')
  db.l.s(SESSION_INFO, '?')
  db.l.d()
  db.c.r(TOKEN_KEY_ID)
  obj.push('/login')
}

function checkAuthorize (to, from, next) {
  if (u.is.has(to.query, 'messenger')) {
    const content = to.query.messenger.split(':')
    const action = content[0]
    const detail = content[1]
    let msg = ''
    if (action === 'alert') {
      msg = detail
    }
    if (msg) {
      alert(msg)
    }
  }
  if (!isLoggedIn()) {
    cookies.remove(TOKEN_KEY_ID)
    next({ path: REDIRECT, query: { redirect: to.fullPath } })
  // } else if (forbidden(to)) {
  //   const backPath = from.fullPath
  //   next({ path: backPath, query: { messenger: 'alert:Truy cập bị từ chối!\nRất tiếc, vì lý do bảo mật nên bạn không thể truy cập trực tiếp vào địa chỉ đường dẫn này!' } })
  } else {
    next()
  }
};

export default {
  login,
  logout,
  checkAuthorize,
  isLoggedIn,
  setIdToken,
  setAccessToken,
  getIdToken,
  getAccessToken
}
