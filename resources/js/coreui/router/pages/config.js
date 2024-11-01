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
  name: 'config',
  pages: {
    branch_list: {
      p: '/branches',
      f: 'branches/list',
      n: 'Danh Sách Các Đơn Vị Cơ Sở'
    },
    branch_add: {
      p: '/branches/add-branch',
      f: 'branches/add',
      n: 'Thêm Trung Tâm Mới'
    },
    branch_edit: {
      p: '/branches/:id/edit',
      f: 'branches/edit',
      n: 'Sửa Thông Tin Trung Tâm'
    },
    branch_detail: {
      p: '/branches/:id',
      f: 'branches/detail',
      n: 'Chi Tiết Thông Tin Đơn Vị Cơ Sở'
    },
    settings_list: {
      p: '/settings',
      f: 'settings/list',
      n: 'Thiết Lập Cấu Hình Vận Hành Trung Tâm'
    },
    class_list: {
      p: '/classes',
      f: 'classes/list',
      n: 'Danh Sách Các Lớp Học'
    },
    class_add: {
      p: '/classes/add-class',
      f: 'classes/add',
      n: 'Thêm Lớp Học Mới'
    },
    class_edit: {
      p: '/classes/:id/edit',
      f: 'classes/edit',
      n: 'Sửa Thông Tin Lớp Học'
    },
    class_detail: {
      p: '/classes/:id',
      f: 'classes/detail',
      n: 'Chi Tiết Thông Tin Lớp Học'
    },
    contact_list: {
      p: '/contacts',
      f: 'contacts/list',
      n: 'Danh Sách Các Hình Thức Liên Lạc'
    },
    contact_add: {
      p: '/contacts/add-contact',
      f: 'contacts/add',
      n: 'Thêm Hình Thức Liên Lạc Mới'
    },
    contact_edit: {
      p: '/contacts/:id/edit',
      f: 'contacts/edit',
      n: 'Sửa Thông Tin Hình Thức Liên Lạc'
    },
    contact_detail: {
      p: '/contacts/:id',
      f: 'contacts/detail',
      n: 'Chi Tiết Thông Tin Hình Thức Liên Lạc'
    },
    cycle_list: {
      p: '/cycles',
      f: 'cycles/list',
      n: 'Danh Sách Các Kỳ Xếp Hạng Nhân Viên'
    },
    cycle_add: {
      p: '/cycles/add-cycles',
      f: 'cycles/add',
      n: 'Thêm Kỳ Xếp Hạng Nhân Viên Mới'
    },
    cycle_edit: {
      p: '/cycles/:id/edit',
      f: 'cycles/edit',
      n: 'Sửa Thông Tin Kỳ Xếp Hạng Nhân Viên'
    },
    cycle_detail: {
      p: '/cycles/:id',
      f: 'cycles/detail',
      n: 'Chi Tiết Thông Tin Kỳ Xếp Hạng Nhân Viên'
    },
    fee_type_list: {
      p: '/fee-types',
      f: 'fee_types/list',
      n: 'Danh Sách Các Loại Thu Phí'
    },
    fee_type_add: {
      p: '/fee-types/add-fee-type',
      f: 'fee_types/add',
      n: 'Thêm Loại Thu Phí Mới'
    },
    fee_type_edit: {
      p: '/fee-types/:id/edit',
      f: 'fee_types/edit',
      n: 'Sửa Thông Tin Loại Thu Phí'
    },
    fee_type_detail: {
      p: '/fee-types/:id',
      f: 'fee_types/detail',
      n: 'Chi Tiết Thông Tin Loại Thu Phí'
    },
    grade_list: {
      p: '/grades',
      f: 'grades/list',
      n: 'Danh Sách School Grade'
    },
    grade_add: {
      p: '/grades/add-grade',
      f: 'grades/add',
      n: 'Thêm School Grade Mới'
    },
    grade_edit: {
      p: '/grades/:id/edit',
      f: 'grades/edit',
      n: 'Sửa Thông Tin School Grade'
    },
    grade_detail: {
      p: '/grades/:id',
      f: 'grades/detail',
      n: 'Chi Tiết Thông Tin School Grade'
    },
    holiday_list: {
      p: '/holidays',
      f: 'holidays/list',
      n: 'Danh Sách Các Ngày Nghỉ Lễ'
    },
    holiday_add: {
      p: '/holidays/add-holiday',
      f: 'holidays/add',
      n: 'Thêm Ngày Nghỉ Lễ Mới'
    },
    holiday_edit: {
      p: '/holidays/:id/edit',
      f: 'holidays/edit',
      n: 'Sửa Thông Tin Ngày Nghỉ Lễ'
    },
    holiday_detail: {
      p: '/holidays/:id',
      f: 'holidays/detail',
      n: 'Chi Tiết Thông Tin Ngày Nghỉ Lễ'
    },
    payment_list: {
      p: '/payments',
      f: 'payments/list',
      n: 'Danh Sách Các Hình Thức Thu Phí'
    },
    payment_add: {
      p: '/payments/add-payment',
      f: 'payments/add',
      n: 'Thêm Hình Thức Thu Phí Mới'
    },
    payment_edit: {
      p: '/payments/:id/edit',
      f: 'payments/edit',
      n: 'Sửa Thông Tin Hình Thức Thu Phí'
    },
    payment_detail: {
      p: '/payments/:id',
      f: 'payments/detail',
      n: 'Chi Tiết Thông Tin Hình Thức Thu Phí'
    },
    pending_rule_list: {
      p: '/pending-rules',
      f: 'pending_rules/list',
      n: 'Danh Sách Các Quy Định Bảo lưu, Pending'
    },
    pending_rule_add: {
      p: '/pending-rules/add-pending-rule',
      f: 'pending_rules/add',
      n: 'Thêm Quy Định Bảo lưu, Pending Mới'
    },
    pending_rule_edit: {
      p: '/pending-rules/:id/edit',
      f: 'pending_rules/edit',
      n: 'Sửa Thông Tin Quy Định Bảo lưu, Pending'
    },
    pending_rule_detail: {
      p: '/pending-rules/:id',
      f: 'pending_rules/detail',
      n: 'Chi Tiết Thông Tin Quy Định Bảo lưu, Pending'
    },
    product_list: {
      p: '/products',
      f: 'products/list',
      n: 'Danh Sách Các Sản Phẩm'
    },
    product_add: {
      p: '/products/add-product',
      f: 'products/add',
      n: 'Thêm Sản Phẩm Mới'
    },
    product_edit: {
      p: '/products/:id/edit',
      f: 'products/edit',
      n: 'Sửa Thông Tin Sản Phẩm'
    },
    product_detail: {
      p: '/products/:id',
      f: 'products/detail',
      n: 'Chi Tiết Thông Tin Sản Phẩm'
    },
    program_list: {
      p: '/programs',
      f: 'programs/list',
      n: 'Danh Sách Các Chương Trình'
    },
    program_add: {
      p: '/programs/add-program',
      f: 'programs/add',
      n: 'Thêm Chương Trình Mới'
    },
    program_edit: {
      p: '/programs/:id/edit',
      f: 'programs/edit',
      n: 'Sửa Thông Tin Chương Trình'
    },
    program_detail: {
      p: '/programs/:id',
      f: 'programs/detail',
      n: 'Chi Tiết Thông Tin Chương Trình'
    },
    reason_list: {
      p: '/reasons',
      f: 'reasons/list',
      n: 'Danh Sách Các Lý Do Bảo Lưu, Pending'
    },
    reason_add: {
      p: '/reasons/add-reason',
      f: 'reasons/add',
      n: 'Thêm Lý Do Bảo Lưu, Pending Mới'
    },
    reason_edit: {
      p: '/reasons/:id/edit',
      f: 'reasons/edit',
      n: 'Sửa Lý Do Bảo Lưu, Pending'
    },
    reason_detail: {
      p: '/reasons/:id',
      f: 'reasons/detail',
      n: 'Nội Dung Lý Do Bảo Lưu, Pending'
    },
    region_list: {
      p: '/regions',
      f: 'regions/list',
      n: 'Danh Sách Các Phân Vùng'
    },
    region_add: {
      p: '/regions/add-region',
      f: 'regions/add',
      n: 'Thêm Phân Vùng Mới'
    },
    region_edit: {
      p: '/regions/:id/edit',
      f: 'regions/edit',
      n: 'Sửa Thông Tin Phân Vùng'
    },
    region_detail: {
      p: '/regions/:id',
      f: 'regions/detail',
      n: 'Chi Tiết Thông Tin Phân Vùng'
    },
    room_list: {
      p: '/rooms',
      f: 'rooms/list',
      n: 'Danh Sách Các Phòng Học'
    },
    room_add: {
      p: '/rooms/add-room',
      f: 'rooms/add',
      n: 'Thêm Phòng Học Mới'
    },
    room_edit: {
      p: '/rooms/:id/edit',
      f: 'rooms/edit',
      n: 'Sửa Thông Tin Phòng Học'
    },
    room_detail: {
      p: '/rooms/:id',
      f: 'rooms/detail',
      n: 'Chi Tiết Thông Tin Phòng Học'
    },
    book_list: {
      p: '/books',
      f: 'books/list',
      n: 'Danh Sách Các Đầu Sách'
    },
    book_add: {
      p: '/books/add-book',
      f: 'books/add',
      n: 'Thêm Đầu Sách Học Mới'
    },
    book_edit: {
      p: '/books/:id/edit',
      f: 'books/edit',
      n: 'Sửa Thông Tin Đầu Sách'
    },
    book_detail: {
      p: '/books/:id',
      f: 'books/detail',
      n: 'Chi Tiết Thông Tin Đầu Sách'
    },
    score_list: {
      p: '/scores',
      f: 'scores/list',
      n: 'Danh Sách Các Hạng Học Sinh'
    },
    score_add: {
      p: '/scores/add-score',
      f: 'scores/add',
      n: 'Thêm Hạng Học Sinh Mới'
    },
    score_edit: {
      p: '/scores/:id/edit',
      f: 'scores/edit',
      n: 'Sửa Thông Tin Hạng Học Sinh'
    },
    score_detail: {
      p: '/scores/:id',
      f: 'scores/detail',
      n: 'Chi Tiết Thông Tin Hạng Học Sinh'
    },
    semester_list: {
      p: '/semesters',
      f: 'semesters/list',
      n: 'Danh Sách Các Kỳ Học'
    },
    semester_add: {
      p: '/semesters/add-semester',
      f: 'semesters/add',
      n: 'Thêm Kỳ Học Mới'
    },
    semester_edit: {
      p: '/semesters/:id/edit',
      f: 'semesters/edit',
      n: 'Sửa Thông Tin Kỳ Học'
    },
    semester_detail: {
      p: '/semesters/:id',
      f: 'semesters/detail',
      n: 'Chi Tiết Thông Tin Kỳ Học'
    },
    session_list: {
      p: '/sessions',
      f: 'sessions/list',
      n: 'Danh Sách Các Buổi Học'
    },
    session_add: {
      p: '/sessions/add-session',
      f: 'sessions/add',
      n: 'Thêm Buổi Học Mới'
    },
    session_edit: {
      p: '/sessions/:id/edit',
      f: 'sessions/edit',
      n: 'Sửa Thông Tin Buổi Học'
    },
    session_detail: {
      p: '/sessions/:id',
      f: 'sessions/detail',
      n: 'Chi Tiết Thông Tin Buổi Học'
    },
    shift_list: {
      p: '/shifts',
      f: 'shifts/list',
      n: 'Danh Sách Các Ca Học'
    },
    shift_add: {
      p: '/shifts/add-shift',
      f: 'shifts/add',
      n: 'Thêm Ca Học Mới'
    },
    shift_edit: {
      p: '/shifts/:id/edit',
      f: 'shifts/edit',
      n: 'Sửa Thông Tin Ca Học'
    },
    shift_detail: {
      p: '/shifts/:id',
      f: 'shifts/detail',
      n: 'Chi Tiết Thông Tin Ca Học'
    },
    teacher_list: {
      p: '/teachers',
      f: 'teachers/list',
      n: 'Danh Sách Các Giáo Viên'
    },
    teacher_add: {
      p: '/teachers/add-teacher',
      f: 'teachers/add',
      n: 'Thêm Giáo Viên Mới'
    },
    teacher_edit: {
      p: '/teachers/:id/edit',
      f: 'teachers/edit',
      n: 'Sửa Thông Tin Giáo Viên'
    },
    teacher_detail: {
      p: '/teachers/:id',
      f: 'teachers/detail',
      n: 'Chi Tiết Thông Tin Giáo Viên'
    },
    tuition_list: {
      p: '/tuitions',
      f: 'tuitions/list',
      n: 'Danh Sách Các Gói Phí'
    },
    tuition_add: {
      p: '/tuitions/add-tuition',
      f: 'tuitions/add',
      n: 'Thêm Gói Phí Mới'
    },
    tuition_edit: {
      p: '/tuitions/:id/edit',
      f: 'tuitions/edit',
      n: 'Sửa Thông Tin Gói Phí'
    },
    tuition_detail: {
      p: '/tuitions/:id',
      f: 'tuitions/detail',
      n: 'Chi Tiết Thông Tin Gói Phí'
    },
    type_charge_list: {
      p: '/type-charges',
      f: 'type_charges/list',
      n: 'Danh Sách Các Ký Hiệu Thu Phí'
    },
    type_charge_add: {
      p: '/type-charges/add-type-charge',
      f: 'type_charges/add',
      n: 'Thêm Ký Hiệu Thu Phí Mới'
    },
    type_charge_edit: {
      p: '/type-charges/:id/edit',
      f: 'type_charges/edit',
      n: 'Sửa Thông Tin Ký Hiệu Thu Phí'
    },
    type_charge_detail: {
      p: '/type-charges/:id',
      f: 'type_charges/detail',
      n: 'Chi Tiết Thông Tin Ký Hiệu Thu Phí'
    },
    zone_list: {
      p: '/zones',
      f: 'zones/list',
      n: 'Danh Sách Các Khu Vực'
    },
    zone_add: {
      p: '/zones/add-zone',
      f: 'zones/add',
      n: 'Thêm Khu Vực Mới'
    },
    zone_edit: {
      p: '/zones/:id/edit',
      f: 'zones/edit',
      n: 'Sửa Thông Tin Khu Vực'
    },
    zone_detail: {
      p: '/zones/:id',
      f: 'zones/detail',
      n: 'Chi Tiết Thông Tin Khu Vực'
    },
    setting_list: {
      p: '/settings',
      f: 'settings/list',
      n: 'Danh Sách Các Cấu Hình'
    },
    setting_add: {
      p: '/settings/add-setting',
      f: 'settings/add',
      n: 'Thêm Thiết Lập Cấu Hình Mới'
    },
    setting_edit: {
      p: '/settings/:id/edit',
      f: 'settings/edit',
      n: 'Sửa Thiết Lập Cấu Hình'
    },
    setting_detail: {
      p: '/settings/:id',
      f: 'settings/detail',
      n: 'Chi Tiết Thiết Lập Cấu Hình'
    },
    program_code_list: {
      p: '/program-codes',
      f: 'program_codes/list',
      n: 'Danh Sách Các Mã Quy Chiếu'
    },
    program_code_add: {
      p: '/program-codes/add-program_code',
      f: 'program_codes/add',
      n: 'Thêm Mã Quy Chiếu Mới'
    },
    program_code_edit: {
      p: '/program-codes/:id/edit',
      f: 'program_codes/edit',
      n: 'Sửa Mã Quy Chiếu'
    },
    program_code_detail: {
      p: '/program-codes/:id',
      f: 'program_codes/detail',
      n: 'Thông Tin Mã Quy Chiếu'
    },
    discounts_form_list: {
      p: '/discounts-forms',
      f: 'discounts_forms/list',
      n: 'Danh Sách Các Hình Thức Giảm Trừ'
    },
    discounts_form_add: {
      p: '/discounts-forms/add-discounts_form',
      f: 'discounts_forms/add',
      n: 'Thêm Hình Thức Giảm Trừ Mới'
    },
    discounts_form_edit: {
      p: '/discounts-forms/:id/edit',
      f: 'discounts_forms/edit',
      n: 'Sửa Hình Thức Giảm Trừ'
    },
    discounts_form_detail: {
      p: '/discounts-forms/:id',
      f: 'discounts_forms/detail',
      n: 'Nội Dung Hình Thức Giảm Trừ'
    },
    scoring_guidelines_list: {
      p: '/scoring-guidelines',
      f: 'scoring_guidelines/list',
      n: 'Hướng Dẫn Về Điểm Số'
    },
    scoring_guidelines_add: {
      p: '/scoring-guidelines/add',
      f: 'scoring_guidelines/add',
      n: 'Thêm Mới Hướng Dẫn Điểm Số'
    },
    scoring_guidelines_edit: {
      p: '/scoring-guidelines/:id/edit',
      f: 'scoring_guidelines/edit',
      n: 'Cập Nhật Hướng Dẫn Điểm Số'
    },
    scoring_guidelines_detail: {
      p: '/scoring-guidelines/:id',
      f: 'scoring_guidelines/detail',
      n: 'Chi Tiết Hướng Dẫn Điểm Số'
    },
    banks_lists: {
      p: '/banks',
      f: 'banks/lists',
      n: 'Ngân hàng'
    },
    banks_add: {
      p: '/banks/add',
      f: 'banks/add',
      n: 'Thêm ngân hàng'
    },
    banks_edit: {
      p: '/banks/:id/edit',
      f: 'banks/edit',
      n: 'Sửa ngân hàng'
    },
    banks_detail: {
      p: '/banks/:id',
      f: 'banks/detail',
      n: 'Chi tiết ngân hàng'
    },
    quality_list: {
      p: '/qualities',
      f: 'qualities/list',
      n: 'Danh Sách Đánh Giá Contact'
    },
    quality_add: {
      p: '/qualities/add',
      f: 'qualities/add',
      n: 'Thêm Đánh Giá Contact Mới'
    },
    quality_edit: {
      p: '/qualities/:id/edit',
      f: 'qualities/edit',
      n: 'Sửa Thông Tin Đánh Giá Contact'
    },
    quality_detail: {
      p: '/qualities/:id',
      f: 'qualities/detail',
      n: 'Chi Tiết Thông Tin Đánh Giá Contact'
    },
    discount_code_list: {
      p: '/discount-code',
      f: 'discount_code/list',
      n: 'Quản lý mã chiết khấu'
    },
    discount_code_add: {
      p: '/discount-code/add',
      f: 'discount_code/Add',
      n: 'Thêm Mã Chiết Khấu Mới'
    },
    discount_code_edit: {
      p: '/discount-code/:id/edit',
      f: 'discount_code/Add',
      n: 'Sửa mã chiết khấu'
    },
    role_list: {
      p: '/roles',
      f: 'roles/list/index',
      n: 'Danh sách quyền',
    },
    role_edit: {
      p: '/roles/:id/edit',
      f: 'roles/edit/index',
      n: 'Cập nhật thông tin quyền',
    },
  },
})

export default {
  routers,
  router: {
    path: '/',
    name: 'Hệ Thống',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.branch_list,
      routers.branch_add,
      routers.branch_edit,
      routers.branch_detail,
      routers.settings_list,
      routers.class_list,
      routers.class_add,
      routers.class_edit,
      routers.class_detail,
      routers.contact_list,
      routers.contact_add,
      routers.contact_edit,
      routers.contact_detail,
      routers.cycle_list,
      routers.cycle_add,
      routers.cycle_edit,
      routers.cycle_detail,
      routers.fee_type_list,
      routers.fee_type_add,
      routers.fee_type_edit,
      routers.fee_type_detail,
      routers.grade_list,
      routers.grade_add,
      routers.grade_edit,
      routers.grade_detail,
      routers.holiday_list,
      routers.holiday_add,
      routers.holiday_edit,
      routers.holiday_detail,
      routers.payment_list,
      routers.payment_add,
      routers.payment_edit,
      routers.payment_detail,
      routers.pending_rule_list,
      routers.pending_rule_add,
      routers.pending_rule_edit,
      routers.pending_rule_detail,
      routers.product_list,
      routers.product_add,
      routers.product_edit,
      routers.product_detail,
      routers.program_list,
      routers.program_add,
      routers.program_edit,
      routers.program_detail,
      routers.reason_list,
      routers.reason_add,
      routers.reason_edit,
      routers.reason_detail,
      routers.region_list,
      routers.region_add,
      routers.region_edit,
      routers.region_detail,
      routers.room_list,
      routers.room_add,
      routers.room_edit,
      routers.room_detail,
      routers.book_list,
      routers.book_add,
      routers.book_edit,
      routers.book_detail,
      routers.score_list,
      routers.score_add,
      routers.score_edit,
      routers.score_detail,
      routers.semester_list,
      routers.semester_add,
      routers.semester_edit,
      routers.semester_detail,
      routers.session_list,
      routers.session_add,
      routers.session_edit,
      routers.session_detail,
      routers.shift_list,
      routers.shift_add,
      routers.shift_edit,
      routers.shift_detail,
      routers.teacher_list,
      routers.teacher_add,
      routers.teacher_edit,
      routers.teacher_detail,
      routers.tuition_list,
      routers.tuition_add,
      routers.tuition_edit,
      routers.tuition_detail,
      routers.type_charge_list,
      routers.type_charge_add,
      routers.type_charge_edit,
      routers.type_charge_detail,
      routers.zone_list,
      routers.zone_add,
      routers.zone_edit,
      routers.zone_detail,
      routers.setting_list,
      routers.setting_add,
      routers.setting_edit,
      routers.setting_detail,
      routers.program_code_list,
      routers.program_code_add,
      routers.program_code_edit,
      routers.program_code_detail,
      routers.discounts_form_list,
      routers.discounts_form_add,
      routers.discounts_form_edit,
      routers.discounts_form_detail,
      routers.scoring_guidelines_list,
      routers.scoring_guidelines_add,
      routers.scoring_guidelines_edit,
      routers.scoring_guidelines_detail,
      routers.banks_lists,
      routers.banks_add,
      routers.banks_edit,
      routers.banks_detail,
      routers.quality_list,
      routers.quality_add,
      routers.quality_edit,
      routers.quality_detail,
      routers.discount_code_list,
      routers.discount_code_add,
      routers.discount_code_edit,
      routers.role_list,
      routers.role_edit,
    ]
  }
}
