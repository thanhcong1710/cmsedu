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
  name : 'pages',
  pages: {
    lab: {
      p: '/lab',
      n: 'Laboratory',
    },
    sso_login: {
      p: '/single-sign-on/:hrm_id/:token',
      f: 'single_sign_on',
      n: 'Đăng Nhập Hệ Thống',
    },
    switch_system: {
      p: '/switch_system',
      n: 'Chuyển Hệ Thống',
    },
    login: {
      p: '/login',
      n: 'Đăng Nhập Hệ Thống',
    },
    login_backup: {
      p: '/login_backup',
      n: 'Đăng Nhập Hệ Thống',
    },
    tools: {
      p: '/tools',
      n: 'Tools',
      f: 'tools'
    },
    forgot: {
      p: '/forgot',
      n: 'Quên Thông Tin Đăng Nhập',
    },
    confirm: {
      p: '/confirm',
      n: 'Xác Nhận Thông Tin Tài Khoản',
    },
    page_404: {
      p: '/404',
      n: 'Trang Bạn Yêu Cầu Không Tồn Tại!',
    },
    page_500: {
      p: '/500',
      n: 'Máy Chủ Không Phản Hồi!',
    },
    print_reserve: {
      p: '/print/reserve/:id',
      n: 'Đơn bảo lưu học phí',
      f: 'reserve',
    },
    print_class_transfer: {
      p: '/print/class-transfer/:id',
      n: 'Đơn xin chuyển lớp - chuyển trung tâm',
      f: 'class_transfer',
    },
    print_branch_transfer: {
      p: '/print/branch-transfer/:id',
      n: 'Đơn xin chuyển trung tâm',
      f: 'branch_transfer',
    },
    print_tuition_transfer: {
      p: '/print/tuition-transfer/:id',
      n: 'Đơn xin chuyển học phí',
      f: 'tuition_transfer',
    },
    print_contract: {
      p: '/print/contract/:id',
      n: 'Đơn nhập học',
      f: 'contract',
    },
    print_recycle: {
      p: '/print/recycle/:id',
      n: 'Hồ sơ tái tục',
      f: 'recycle',
    },
    print_trial_register: {
      p: '/print/trial-register/:id',
      n: 'Phiếu học trải nghiệm',
      f: 'trial_register',
    },
    print_register: {
      p: '/print/register/:id',
      n: 'Phiếu đăng ký lớp học',
      f: 'enrolment'
    },
    print_issue: {
      p: '/print/issue/:id/:mode',
      n: 'Issue',
      f: 'issue'
    },
    print_test: {
      p: '/print/test/:id',
      n: 'Test',
      f: 'test'
    },
    print_tuition_withdraw: {
      p: '/print/tuition-withdraw/:id',
      n: 'Đơn rút học phí',
      f: 'tuition_withdraw'
    },
    import_std : {
      p: '/import/students',
      n: 'Import Students',
      f: 'import'
    },
    import_user : {
      p: '/import/users',
      n: ' Import Users',
      f: 'import_user'
    },
    print_feedback: {
      p: '/print/feedback/:class_id/:student_id/:date',
      n: 'In phiếu nhận xét học sinh',
      f: 'feedback',
    },
    import_contract: {
      p: '/import/contract',
      n: 'Import Contract',
      f: 'import_contract'
    },
    import_payment: {
      p: '/import/payment',
      n: 'Import Payment',
      f: 'import_payment'
    },
    import_class: {
      p: '/import/class',
      n: 'Import Class',
      f: 'import_class'
    },
    import_discount_codes: {
      p: '/import/discount',
      n: 'Import Discount Code',
      f: 'import_discount_codes'
    },
    migrate: {
      p: '/migrate',
      n: 'migrate',
      f: 'migrate'
    },
    import_reserve: {
      p: '/import/reserve',
      n: 'Import Reserve',
      f: 'import_reserve'
    }
  }
})

export default routers
