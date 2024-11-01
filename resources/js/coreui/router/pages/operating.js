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
  name : 'operating',
  pages: {
    student_contract_list: {
      p: '/students/:id/contracts',
      f: 'contracts/detail',
      n: 'Danh Sách Nhập Học Của Học Sinh',
    },
    contract_list: {
      p: '/contracts',
      f: 'contracts/list',
      n: 'Danh Sách Nhập Học',
    },
    contract_add: {
      p: '/contracts/add',
      f: 'contracts/add',
      n: 'Thêm Bản Ghi Nhập Học Mới',
    },
    contract_create: {
      p: '/contracts/:id/create',
      f: 'contracts/create',
      n: 'Khai Báo Thông Tin Nhập Học',
    },
    contract_edit: {
      p: '/contracts/:id/edit',
      f: 'contracts/edit',
      n: 'Sửa Thông Tin Nhập Học',
    },
    contract_detail: {
      p: '/contracts/:id',
      f: 'contracts/detail',
      n: 'Nội Dung Nhập Học Chi Tiết',
    },
    enrolment_list: {
      p: '/enrolments',
      f: 'enrolments/list',
      n: 'Danh Sách Đăng Ký Lớp Học',
    },
    enrolment_add: {
      p: '/enrolments/add-enrolment',
      f: 'enrolments/add',
      n: 'Thêm Bản Ghi Đăng Ký Mới',
    },
    enrolment_edit: {
      p: '/enrolments/:id/edit',
      f: 'enrolments/edit',
      n: 'Sửa Thông Tin Đăng Ký Lớp Học',
    },
    enrolment_detail: {
      p: '/enrolments/:id',
      f: 'enrolments/detail',
      n: 'Nội Dung Đăng Ký Lớp Học Chi Tiết',
    },
    reserve_list: {
      p: '/reserves',
      f: 'reserves/list',
      n: 'Danh Sách Bảo Lưu',
    },
    reserve_edit: {
      p: '/reserves/edit/:id',
      f: 'reserves/edit',
      n: 'Cập nhật bảo lưu'
    },
    reserve_add: {
      p: '/reserves/add-reserve',
      f: 'reserves/add',
      n: 'Thêm Bản Ghi Bảo Lưu Mới',
    },
    reserve_add_multiple: {
      p: '/reserves/add-multiple',
      f: 'reserves/add-multiple',
      n: 'Thêm Bảo Lưu Cho Lớp'
    },
    reserve_approve: {
      p: '/reserves/approve',
      f: 'reserves/approve',
      n: 'Phê duyệt bảo lưu',
    },
    reserve_detail: {
      p: '/reserves/:id',
      f: 'reserves/detail',
      n: 'Nội Dung Bảo Lưu Chi Tiết',
    },
    pending_list: {
      p: '/pendings',
      f: 'pendings/list',
      n: 'Danh Sách Pending',
    },
    pending_add: {
      p: '/pendings/add-pending',
      f: 'pendings/add',
      n: 'Thêm mới Pending',
    },
    pending_approve: {
      p: '/pendings/approve',
      f: 'pendings/approve',
      n: 'Phê duyệt pending',
    },
    pending_detail: {
      p: '/pendings/:id',
      f: 'pendings/detail',
      n: 'Nội Dung Pending Chi Tiết',
    },
    withdrawal_list: {
      p: '/withdrawals',
      f: 'withdrawals/list',
      n: 'Danh Sách Rút Phí',
    },
    withdrawal_add: {
      p: '/withdrawals/add',
      f: 'withdrawals/add',
      n: 'Thêm Bản Ghi Rút Phí',
    },
    withdrawal_approve: {
      p: '/withdrawals/approve',
      f: 'withdrawals/approve',
      n: 'Duyệt Rút Phí',
    },
    withdrawal_refun: {
      p: '/withdrawals/refun',
      f: 'withdrawals/refun',
      n: 'Hoàn Phí',
    },
    withdrawal_detail: {
      p: '/withdrawals/:id',
      f: 'withdrawals/detail',
      n: 'Nội Dung Rút Phí',
    },
    tuition_transfer_list: {
      p: '/tuition-transfers',
      f: 'tuition_transfers/list',
      n: 'Danh Sách Chuyển Phí',
    },
    tuition_transfer_add: {
      p: '/tuition-transfers/add-tuition-transfer',
      f: 'tuition_transfers/add',
      n: 'Thêm Bản Ghi Chuyển Phí Mới',
    },
    class_transfer_list: {
      p: '/class-transfers',
      f: 'class_transfers/list',
      n: 'Danh Sách Chuyển Lớp',
    },
    class_transfer_add: {
      p: '/class-transfers/add-class-transfer',
      f: 'class_transfers/add',
      n: 'Thêm Bản Ghi Chuyển Lớp Mới',
    },
    class_transfer_trial: {
      p: '/class-transfers/class-transfer-trial',
      f: 'class_transfers/trial',
      n: 'Thêm Bản Ghi Chuyển Lớp Mới',
    },
    class_transfer_approve: {
      p: '/class-transfers/approve',
      f: 'class_transfers/approve',
      n: 'Duyệt Chuyển Lớp',
    },
    class_transfer_detail: {
      p: '/class-transfers/:id',
      f: 'class_transfers/detail',
      n: 'Nội Dung Chuyển Lớp',
    },
    class_transfer_multiple: {
      p: '/class_transfers/add-multiple',
      f: 'class_transfers/add-multiple',
      n: 'Thêm Chuyển Lớp Cho Cả Lớp'
    },
    branch_transfer_list: {
      p: '/branch-transfers',
      f: 'branch_transfers/list',
      n: 'Danh Sách Chuyển Trung Tâm',
    },
    branch_transfer_add: {
      p: '/branch-transfers/add-branch-transfer',
      f: 'branch_transfers/add',
      n: 'Thêm Bản Ghi Chuyển Trung Tâm',
    },
    semester_transfer: {
      p: '/semester-transfer',
      f: 'semester_transfers/detail',
      n: 'Chuyển Kỳ',
    },
    cm_transfer: {
      p: '/cm-transfer',
      f: 'cm_transfers/detail',
      n: 'Chuyển Đổi Người Quản Lý',
    },
    cm_transfer_history: {
      p: '/cm-transfer-history',
      f: 'cm_transfers/history',
      n: 'Lịch Sử Chuyển Đổi Người Quản Lý',
    },
    recharge_list: {
      p: '/recharges',
      f: 'recharges/list',
      n: 'Danh Sách Tái Phí',
    },
    recharge_add: {
      p: '/recharges/add',
      f: 'recharges/add',
      n: 'Thêm Bản Ghi Tái Phí Mới',
    },
    recharge_edit: {
      p: '/recharges/:id/edit',
      f: 'recharges/edit',
      n: 'Sửa Thông Tin Tái Phí',
    },
    recharge_detail: {
      p: '/recharges/:id',
      f: 'recharges/detail',
      n: 'Nội Dung Tái Phí Chi Tiết',
    },
    issues_list: {
      p: '/issues',
      f: 'issues/list',
      n: 'Issues List',
    },
    feedback_list: {
      p: '/feedback/:type',
      f: 'issues/list',
      n: 'Issues List',
    },
    issues_add: {
      p: '/issues/:id/add',
      f: 'issues/add',
      n: 'Add Issues',
    },
    issues_edit: {
      p: '/issues/:id/edit',
      f: 'issues/edit',
      n: 'Edit Issues',
    },
    issues_detail: {
      p: '/issues/:id',
      f: 'issues/detail',
      n: 'Issues Detail',
    },
    tracking_list: {
      p: '/tracking',
      f: 'tracking/list',
      n: 'Theo Dõi Chỗ Trống Trong Lớp',
    },
    ctp_list: {
      p: '/ctp',
      f: 'ctp/list',
      n: 'CTP',
    },
    attendances: {
      p: '/attendances-old',
      f: 'attendances/list',
      n: 'Attendance',
    },
    collaborator: {
      p: '/collaborator',
      f: 'collaborator/index',
      n: 'Cộng tác viên',
    },
    collaborator_edit: {
      p: '/collaborator/:id/edit',
      f: 'collaborator/edit',
      n: 'Cập nhật cộng tác viên',
    },
    collaborator_add: {
      p: '/collaborator/add',
      f: 'collaborator/add',
      n: 'Thêm mới cộng tác viên',
    },
    attendances_new: {
      p: '/attendances',
      f: 'attendances/new',
      n: 'Điểm danh',
    },
    exchange:{
      p: '/exchange',
      f: 'exchange/list',
      n: 'Quy đổi'
    },
    exchange_add: {
      p: '/exchange/add-exchange',
      f: 'exchange/add',
      n: 'Thêm mới Quy đổi'
    },
    report_student: {
      p: '/report_student',
      f: 'report_student/add',
      n: 'Nhận xét học sinh'
    },
    report_student_new: {
      p: '/report_student_new',
      f: 'report_student/add_new',
      n: 'Nhận xét học sinh'
    },
  },
})

export default {
  routers,
  router: {
    path     : '/operating',
    redirect : '/contracts',
    name     : 'Vận Hành',
    component: {
      render (c) {
        return c('router-view')
      },
    },
    children: [
      routers.contract_list,
      routers.contract_add,
      routers.contract_create,
      routers.student_contract_list,
      routers.contract_edit,
      routers.contract_detail,
      routers.enrolment_list,
      routers.enrolment_add,
      routers.enrolment_edit,
      routers.enrolment_detail,
      routers.reserve_list,
      routers.reserve_add,
      routers.reserve_add_multiple,
      routers.reserve_approve,
      routers.reserve_detail,
      routers.reserve_edit,
      routers.withdrawal_refun,
      routers.withdrawal_list,
      routers.withdrawal_add,
      routers.withdrawal_approve,
      routers.withdrawal_detail,
      routers.pending_list,
      routers.pending_add,
      routers.pending_approve,
      routers.pending_detail,
      routers.class_transfer_list,
      routers.class_transfer_add,
      routers.class_transfer_trial,
      routers.class_transfer_approve,
      routers.class_transfer_detail,
      routers.class_transfer_multiple,
      routers.tuition_transfer_list,
      routers.tuition_transfer_add,
      routers.branch_transfer_list,
      routers.branch_transfer_add,
      routers.semester_transfer,
      routers.cm_transfer,
      routers.cm_transfer_history,
      routers.recharge_list,
      routers.recharge_add,
      routers.recharge_edit,
      routers.recharge_detail,
      routers.issues_list,
      routers.issues_add,
      routers.issues_edit,
      routers.issues_detail,
      routers.tracking_list,
      routers.ctp_list,
      routers.attendances,
      routers.feedback_list,
      routers.collaborator,
      routers.collaborator_add,
      routers.collaborator_edit,
      routers.attendances_new,
      routers.exchange,
      routers.exchange_add,
      routers.report_student,
      routers.report_student_new,
    ],
  },
}
