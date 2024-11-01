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

import u from '../../utilities/utility'

const routers = u.load({
  name: 'accounting',
  pages: {
    waitcharge_list: {
      p: '/waitcharges',
      f: 'waitcharges/list',
      n: 'Danh Sách Chờ Đóng Phí'
    },
    waitcharge_add: {
      p: '/waitcharges/:id',
      f: 'waitcharges/add',
      n: 'Thêm Mới Phiếu Thu'
    },
    charge_list: {
      p: '/charges',
      f: 'charges/list',
      n: 'Danh Sách Thu Phí'
    },
    charge_add: {
      p: '/charges/add-charge',
      f: 'charges/add',
      n: 'Thêm Mới Phiếu Thu'
    },
    charge_edit: {
      p: '/charges/:id/edit',
      f: 'charges/edit',
      n: 'Cập Nhật Thông Tin Thu Phí'
    },
    charge_detail: {
      p: '/charges/:id',
      f: 'charges/detail',
      n: 'Nội Dung Thu Phí Chi Tiết'
    },
  }
})

export default {
  routers,
  router: {
    path: '/accounting',
    redirect: '/charges',
    name: 'Kế Toán',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.waitcharge_list,
      routers.waitcharge_add,
      routers.charge_list,
      routers.charge_add,
      routers.charge_edit,
      routers.charge_detail,
    ]
  }
}
