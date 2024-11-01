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
  name: 'students',
  pages: {
    list: {
      p: 'list/:page',
      n: 'Danh Sách Học Sinh'
    },
    care: {
      p: '/student-care',
      f: 'care-new/list',
      n: 'Chăm Sóc'
    },
    care_add: {
      p: '/students/add-care',
      f: 'care-add',
      n: 'Thêm chăm sóc khách hàng'
    },
    care_edit: {
      p: '/students/:id/edit-care',
      f: 'care-edit',
      n: 'Sửa chăm sóc khách hàng'
    },
    detail: {
      p: '/students/:id',
      n: 'Hồ Sơ Học Sinh'
    },
    add: {
      p: 'add-student',
      n: 'Thêm Học Sinh Mới'
    },
    edit: {
      p: '/students/:id/edit',
      n: 'Cập Nhật Thông Tin Học Sinh'
    },
    enrolment: {
      p: 'student-enrolments',
      n: 'Đăng Ký Nhập Học'
    } ,
    care_detail: {
      p: '/students/detail-care/:id',
      f: 'care-detail',
      n: 'Thông tin chăm sóc khách hàng'
    },
    student_temp_list: {
      p: '/student-temp',
      f: 'student-temp/list/Index',
      n: 'Danh Sách dữ liệu học sinh thô'
    },
    student_temp_import: {
      p: '/student-temp/import',
      f: 'student-temp/import/Index',
      n: 'Nhập danh sách học sinh thô'
    },
    student_temp_new: {
      p: '/student-temp/new',
      f: 'student-temp/new/Index',
      n: 'Thêm mới một học sinh'
    },
    student_temp_edit: {
      p: '/student-temp/:id/edit',
      f: 'student-temp/care/Index',
      n: 'Sửa học sinh'
    },
    student_temp_info: {
      p: '/student-temp/:id/info',
      f: 'student-temp/care/Index',
      n: 'Chi tiết học sinh'
    },
    student_care_detail: {
      p: '/student-care/:id',
      f: 'care-new/Index',
      n: 'Thông tin chăm sóc khách hàng'
    },
    student_checkin: {
      p: '/student-checkin',
      f: 'check-in/index',
      n: 'Danh sách checkin'
    },
    student_checkin_new: {
      p: '/student-checkin/add',
      f: 'check-in/add',
      n: 'Thêm mới checkin'
    },
    student_checkin_edit: {
      p: '/student-checkin/:id/edit',
      f: 'check-in/edit',
      n: 'Cập nhật checkin'
    },
    student_checkin_detail: {
      p: '/student-checkin/:id/detail',
      f: 'check-in/detail',
      n: 'Chi tiết checkin'
    },
    student_checkin_import: {
      p: '/student-checkin/import',
      f: 'check-in/import',
      n: 'Import checkin'
    },
  }
})

export default {
  routers,
  router: {
    path: 'students',
    redirect: '/students/list/1',
    name: 'Học Sinh',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.list,
      routers.add,
      routers.edit,
      routers.care,
      routers.care_add,
      routers.care_edit,
      routers.care_detail,
      routers.detail,
      routers.enrolment,
      routers.student_temp_list,
      routers.student_temp_import,
      routers.student_temp_new,
      routers.student_temp_edit,
      routers.student_temp_info,
      routers.student_care_detail,
      routers.student_checkin_new,
      routers.student_checkin_edit,
      routers.student_checkin,
      routers.student_checkin_detail,
      routers.student_checkin_import
    ]
  }
}
