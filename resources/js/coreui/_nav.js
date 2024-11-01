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

import u from './utilities/utility'
const switch_system = {
  name : 'Đổi hệ thống',
  url  : '/switch_system',
  icon : 'fa fa-refresh',
}
// Dashboard items defination
const dashboard = {
  name : 'Bảng Tin',
  url  : '/dashboard',
  icon : 'icon-speedometer',
  badge: {
    variant: 'primary',
    text   : 'Hi',
  },
}

// Students items defination
const students = {
  name: 'Danh Sách',
  url : '/students',
  icon: 'fa fa-list',
}

const studentsTemp = {
  name: 'Dữ liệu thô',
  url : '/student-temp',
  icon: 'fa fa-database',
}

const cares = {
  name: 'Chăm Sóc',
  url : '/student-care',
  icon: 'fa fa-fax',
}

const checkin = {
  name: 'Checkin',
  url : '/student-checkin',
  icon: 'fa fa-fax',
}

const student = {
  name    : 'Học Sinh',
  url     : '/students',
  icon    : 'fa fa-graduation-cap',
  children: [checkin, students, cares],
}

const support_list = {
  name: 'Công cụ hỗ trợ',
  url : '/supports',
  icon: 'fa fa-list',
}

const support = {
  name    : 'Tools',
  url     : '/supports',
  icon    : 'fa fa-gavel',
  children: [support_list],
}

// employees items defination
const employees = {
  name: 'Danh Sách',
  url : '/users',
  icon: 'fa fa-list',
}

const ranks = {
  name: 'Xếp Hạng',
  url : '/user-ranks',
  icon: 'fa fa-bar-chart',
}

const management = {
  name: 'Quản lý',
  url : '/user-management',
  icon: 'fa fa-user-plus',
}

// const roles = {
//   name: 'Phân Quyền',
//   url: '/users/roles',
//   icon: 'fa fa-bar-chart'
// }

const users = {
  name    : 'Nhân Viên',
  url     : '/users',
  icon    : 'fa fa-group',
  children: [
    // employees,
    ranks,
    management,
  ],
}

// Operating items defination
// const approves = {
//   name: 'Phê Duyệt',
//   url : '/approves',
//   icon: 'fa fa-ship',
// }

const contracts = {
  name: 'Nhập Học',
  url : '/contracts',
  icon: 'fa fa-briefcase',
}

const enrolments = {
  name: 'Đăng Ký Lớp Học',
  url : '/enrolments',
  icon: 'fa fa-paw',
}

// const steps = {
//   name: 'Chuyển Kỳ Học',
//   url : '/steps',
//   icon: 'fa fa-trophy',
// }

const reserves = {
  name: 'Bảo Lưu',
  url : '/reserves',
  icon: 'fa fa-hourglass',
}

// const pendings = {
//   name: 'Pending',
//   url : '/pendings',
//   icon: 'fa fa-hourglass-2',
// }

// const semesterTransfer = {
//   name: 'Chuyển Kỳ',
//   url : '/semester-transfer',
//   icon: 'fa fa-history',
// }

const classTransfer = {
  name: 'Chuyển Lớp',
  url : '/class-transfers',
  icon: 'fa fa-refresh',
}

const tuitionTransfer = {
  name: 'Chuyển Phí',
  url : '/tuition-transfers',
  icon: 'fa fa-exchange',
}

const withdrawals = {
  name: 'Rút Phí',
  url : '/withdrawals',
  icon: 'fa fa-fire',
}

const branchTransfer = {
  name: 'Chuyển Trung Tâm',
  url : '/branch-transfers',
  icon: 'fa fa-location-arrow',
}

const cmTransfer = {
  name: 'Chuyển Người Quản Lý',
  url : '/cm-transfer',
  icon: 'fa fa-user-md',
}

const recharges = {
  name: 'Tái Phí',
  url : '/recharges',
  icon: 'fa fa-gg',
}

// const feedbackByDate = {
//   name: 'Theo ngày',
//   url : '/feedback/date',
//   icon: 'fa fa-calendar',
// }

// const feedbackByClass = {
//   name: 'Theo lớp',
//   url : '/feedback/class',
//   icon: 'fa fa-calendar-check-o',
// }

const feedbackStudent = {
  name    : 'Chăm sóc học sinh',
  url : '/feedback/class',
  icon    : 'fa fa-address-book',
  // children: [
  //   feedbackByDate,
  //   feedbackByClass,
  // ],
}

// const ctp = {
//   name: 'CTP',
//   url : '/ctp',
//   icon: 'fa fa-video-camera',
// }

const attendances = {
  name: 'Điểm danh',
  url : '/attendances',
  icon: 'fa fa-sticky-note',
}
const exchange = {
  name: 'Quy đổi',
  url : '/exchange',
  icon: 'fa fa-exchange',
}
const report_student_new = {
  name: 'Nhập điểm học sinh',
  url : '/report_student_new',
  icon: 'fa fa-edit',
}

const operating = {
  name    : 'Vận Hành',
  url     : '/operating',
  icon    : 'fa fa-desktop',
  children: [
    // approves,
    contracts,
    enrolments,
    // steps,
    reserves,
    // pendings,
    // withdrawals,
    tuitionTransfer,
    // semesterTransfer,
    classTransfer,
    branchTransfer,
    cmTransfer,
    recharges,
    feedbackStudent,
    // ctp,
    attendances,
    exchange,
    report_student_new,
  ],
}

// Accounting items defination
const charges = {
  name: 'Đã Thu Phí',
  url : '/charges',
  icon: 'fa fa-credit-card-alt',
}

const waitcharges = {
  name: 'Chờ Đóng Phí',
  url : '/waitcharges',
  icon: 'fa fa-money',
}

const accounting = {
  name    : 'Kế Toán',
  url     : '/accounting',
  icon    : 'fa fa-usd',
  children: [charges, waitcharges],
}

// Reports items defination
// const statistic = {
//   name: 'Thống Kê',
//   url: '/statistics',
//   icon: 'fa fa-pie-chart'
// }

const forms = {
  name: 'Theo mẫu/Form',
  url : '/forms',
  icon: 'fa fa-file',
}

const reports = {
  name    : 'Báo cáo/Report',
  url     : '/reports',
  icon    : 'fa fa-line-chart',
  children: [forms],
}

// Config items defination
const regions = {
  name: 'Phân Vùng',
  url : '/regions',
  icon: 'fa fa-globe',
}

const zones = {
  name: 'Khu Vực',
  url : '/zones',
  icon: 'fa fa-map',
}

const branches = {
  name: 'Đơn Vị Cơ Sở',
  url : '/branches',
  icon: 'fa fa-fort-awesome',
}

const tuitions = {
  name: 'Gói Phí',
  url : '/tuitions',
  icon: 'fa fa-diamond',
}

// const typeCharge = {
//   name: 'Ký Hiệu Thu Phí',
//   url: '/type-charges',
//   icon: 'fa fa-fonticons'
// }

// const payments = {
//   name: 'Hình Thức Thu Phí',
//   url: '/payments',
//   icon: 'fa fa-handshake-o'
// }

// const feeType = {
//   name: 'Loại Thu Phí',
//   url: '/fee-types',
//   icon: 'fa fa-credit-card'
// }

const contacts = {
  name: 'Hình Thức Liên Lạc',
  url : '/contacts',
  icon: 'fa fa-headphones',
}

const holidays = {
  name: 'Ngày Nghỉ Lễ',
  url : '/holidays',
  icon: 'fa fa-cloud',
}

const products = {
  name: 'Sản Phẩm',
  url : '/products',
  icon: 'fa fa-product-hunt',
}

const programs = {
  name: 'Chương Trình',
  url : '/programs',
  icon: 'fa fa-reddit-alien',
}

const cycles = {
  name: 'Kỳ Xếp Hạng Nhân Viên',
  url : '/cycles',
  icon: 'fa fa-flag',
}

const scores = {
  name: 'Xếp Hạng Học Sinh',
  url : '/scores',
  icon: 'fa fa-star',
}

const grades = {
  name: 'School Gade',
  url : '/grades',
  icon: 'fa fa-id-badge',
}

const books = {
  name: 'Sách',
  url : '/books',
  icon: 'fa fa-leanpub',
}

const semesters = {
  name: 'Kỳ Học',
  url : '/semesters',
  icon: 'fa fa-calendar',
}

const sessions = {
  name: 'Buổi Học',
  url : '/sessions',
  icon: 'fa fa-calendar-check-o',
}

const shifts = {
  name: 'Ca Học',
  url : '/shifts',
  icon: 'fa fa-clock-o',
}

const classes = {
  name: 'Lớp Học',
  url : '/classes',
  icon: 'fa fa-gavel',
}

const teachers = {
  name: 'Giáo Viên',
  url : '/teachers',
  icon: 'fa fa-user-circle',
}

const rooms = {
  name: 'Phòng Học',
  url : '/rooms',
  icon: 'fa fa-inbox',
}

const reasons = {
  name: 'Lý Do Bảo Lưu, Pending',
  url : '/reasons',
  icon: 'fa fa-leaf',
}

const rules = {
  name: 'Quy Định Bảo Lưu, Pending',
  url : '/pending-rules',
  icon: 'fa fa-life-ring',
}

const settings = {
  name: 'Cấu Hình',
  url : '/settings',
  icon: 'fa fa-gears',
}

const programCode = {
  name: 'Mã Quy Chiếu',
  url : '/program-codes',
  icon: 'fa fa-hashtag',
}

const discountsForms = {
  name: 'Hình Thức Giảm Trừ',
  url : '/discounts-forms',
  icon: 'fa fa-certificate',
}

const scoringGuidelines = {
  name: 'Hướng Dẫn Về Điểm Số',
  url : '/scoring-guidelines',
  icon: 'fa fa-list',
}

const banks = {
  name: 'Banks',
  url : '/banks',
  icon: 'fa fa-university',
}

const config_routes = {
  name: 'Danh sách routes',
  url : '/routes',
  icon: 'fa fa-university',
}

const config_roles = {
  name: 'Danh sách quyền',
  url : '/roles',
  icon: 'fa fa-university',
}
const config_quality = {
  name: 'Chất lượng contact',
  url : '/qualities',
  icon: 'fa fa-leanpub',
}

const discount_code = {
  name: 'Mã chiết khấu',
  url : '/discount-code',
  icon: 'fa fa-sticky-note',
}

const collaborator = {
  name: 'Cộng tác viên',
  url : '/collaborator',
  icon: 'fa fa-user-circle',
}

const config = {
  name    : 'Hệ Thống',
  url     : '/config',
  icon    : 'fa fa-windows',
  children: [
    regions,
    zones,
    branches,
    settings,
    tuitions,
    // typeCharge,
    // payments,
    // feeType,
    contacts,
    holidays,
    products,
    programs,
    cycles,
    scores,
    grades,
    semesters,
    sessions,
    shifts,
    classes,
    teachers,
    rooms,
    books,
    reasons,
    rules,
    programCode,
    discountsForms,
    scoringGuidelines,
    banks,
    config_routes,
    config_roles,
    config_quality,
    discount_code,
    collaborator,
  ],
}
const send_sms = {
  name: 'Gửi tin nhắn',
  url : '/send_sms',
  icon: 'fa fa-paper-plane',
}
const list_campaign = {
  name: 'Chiến dịch đã gửi',
  url : '/sms/campaign/list',
  icon: 'fa fa-line-chart',
}
const list_template = {
  name: 'Mẫu tin nhắn',
  url : '/sms/template/list',
  icon: 'fa fa-sticky-note',
}

const sms = {
  name    : 'SMS',
  url     : '/send_sms',
  icon    : 'fa fa-comment',
  children: [
    send_sms,
    list_template,
    list_campaign
  ],
}
const teacherMenu = [feedbackStudent]

const fullMenu = [
  switch_system,
  dashboard,
  student,
  users,
  operating,
  accounting,
  reports,
  config,
  support,
  sms
]

// Menu all items
const routers = () => {
  const menu = { items: fullMenu }
  const nav  = localStorage.getItem('__as')
  if (nav && nav.length > 10) {
    const roles = JSON.parse(nav)
    const list  = []
    const data  = u.session()
    menu.items.map((item) => {
      if (roles.includes(item.url.substr(1)) || roles.includes(item.url) ||item.url=='/switch_system') {
        if (item.children) {
          const subs    = []
          item.children.map((sub) => {
            if (roles.includes(sub.url.substr(1)) || roles.includes(sub.url)) {
              if (sub.url.substr(1) === 'approves' && [
                686868,
                55,
                56,
                68,
                69,
              ].indexOf(parseInt(data.user.role_id, 10)) > -1)
                subs.push(sub)
              else if (sub.url.substr(1) !== 'approves')
                subs.push(sub)
            }
            return sub
          })
          item.children = subs
        }
        list.push(item)
      }
      return item
    })
    menu.items  = u.role() === 'teacher' ? teacherMenu : list
    menu.items  = (u.session().user.role_id === 80 || u.session().user.role_id === 81) ? [checkin,reports] : list
  }
  //
  // u.log('Menu', menu)
  return menu
}

export default routers
