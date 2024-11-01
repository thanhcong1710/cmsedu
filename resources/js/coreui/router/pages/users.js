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
  name: 'users',
  pages: {
    list: {
      p: 'list/:page',
      n: 'Danh Sách Nhân Viên'
    },
    detail: {
      p: '/users/:id',
      n: 'Hồ Sơ Nhân Viên'
    },
    add: {
      p: '/add-user',
      n: 'Thêm nhân viên mới'
    },
    edit: {
      p: '/users/:id/edit',
      n: 'Cập Nhật Thông Tin Nhân Viên'
    },
    edit_user: {
      p: '/edit-user/:id',
      n: 'Thay Đổi Thông Tin Nhân Viên'
    },
    rank: {
      p: '/user-ranks',
      n: 'Xếp Hạng Nhân Viên'
    },
    rank_edit: {
      p: '/user-ranks/:id/edit',
      n: 'Sửa Xếp Hạng Nhân Viên'
    },
    management: {
      p: '/user-management',
      n: 'Quản Lý Nhân Viên'
    },
    role: {
      p: 'user-roles',
      n: 'Phân Quyền Người Dùng'
    },
    user_role: {
      p: '/user-role/:id',
      f: 'role/index',
      n: 'Phân Quyền Người Dùng'
    }
  }
})

export default {
  routers,
  router: {
    path: 'users',
    redirect: '/users/list/1',
    name: 'Nhân Viên',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.list,
      routers.add,
      routers.edit,
      routers.detail,
      routers.rank,
      routers.rank_edit,
      routers.management,
      routers.edit_user,
      routers.user_role

    ]
  }
}
