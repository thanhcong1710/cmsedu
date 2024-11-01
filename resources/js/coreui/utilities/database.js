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
import Vue from 'vue'
import Dexie from 'dexie'
import cookies from 'vue-cookies'

import { isUndefined, isNullOrUndefined, isString } from 'util'

Vue.prototype.cookies = cookies

const i = new Dexie('Ada')

function localStoreGetAllItem (x) {
	const o = x ? x : false
		let resp = null
	const keys = Object.keys(localStorage)
	let i = keys.length
	let arr = []
	let obj = {}
	while (i--) {
			const v = localStorage.getItem(keys[i])
			arr.push(v)
		obj[keys[i]] = v
	}
	resp = o ? obj : arr
	return resp
}

function localStoreSetItem (key, val = null) {
	let resp = false
	if (key) {
		try {
			localStorage.setItem(key, val)
			resp = true
		} catch(e) {}
	}
	return resp
}

function localStoreRemoveItem (key) {
	let resp = false
	if (localStorage.key(key)) {
		try {
			localStograge.removeItem(key)
			resp = true
		} catch(e) {}
	}
	return resp
}

function localStoreGetItem (key, def = null) {
		return localStorage.key(key) ? localStorage.getItem(key) : def
}

function localStoreIsItemExists (key) {
	return localStorage.key(key)
}

function localStoreCountItems() {
	return localStoreGetAllItem().length
}

function localStoreClearAllItems() {
	let resp = false
	try {
		localStorage.clear()
		resp = true
	} catch(e) {}
	return resp
}

function setCookies (key, val = null) {
	let resp = false
	if (key) {
		if (cookies.isKey(key)) {
			cookies.remove(key)
		}
		try {
			cookies.set(key, val)
			resp = true
		} catch(e) {}
	}
	return resp
}

function getCookies (key, def = null) {
	return cookies.isKey(key) ? cookies.get(key) : def
}

function deleteCookies (key) {
	let resp = false
  if (cookies.isKey(key)) {
		try {
			cookies.remove(key)
			resp = true
		} catch(e) {}
	}
	return resp
}

const c = {
	s: (k, v = null) => setCookies(k, v),
	g: (k, d = null) => getCookies(k, d),
	r: k => deleteCookies(k),
	e: k => isKey(k)
}

const l = {
	a: x => localStoreGetAllItem(x),
	s: (k, v = null) => localStoreSetItem(k, v),
	g: (k, d = null) => localStoreGetItem(k, d),
	e: k => localStoreIsItemExists(k),
	r: k => localStoreRemoveItem(k),
	c: () => localStoreCountItems(),
	d: () => localStoreClearAllItems()
}

const s = {
	s: (k, v) => sessionStorage.setItem(k, v),
	r: k => sessionStorage.removeItem(k),
	g: k => sessionStorage.getItem(k),
	d: sessionStorage.clear()
}

export default {
	i,
	c,
	l,
	s
}
