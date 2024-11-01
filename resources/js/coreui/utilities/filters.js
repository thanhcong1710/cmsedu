import Vue from 'vue'
import moment from 'moment'
import u from './utility'

Vue.filter('classStatus', (v) => v === 'no' ? 'Đang mở' : 'Đã đóng')
Vue.filter('strToLowerCase', (v) => v ? v & v.toString().toLowerCase() : '')
Vue.filter('strToUpperCase', (v) => v ? v.toString().toUpperCase() : '')
Vue.filter('typeToName', (v) => !v ? 'Chính thức' : 'Không chính thức')
Vue.filter('payloadType', (v) => parseInt(v) === 1 ? 'Nhiều lần' : 'Một lần')
Vue.filter('genderToName', (v) => v && v.toString().toLowerCase() === 'f' ? 'Nữ' : 'Nam')
Vue.filter('parentInfo', (name, phone) => name && phone ? `${name} (${phone})` : name)
Vue.filter('reserveToName', (v) => parseInt(v) === 1 ? 'Có' : 'Không')
Vue.filter('reserveType', (v) => parseInt(v) === 1 ? 'Đặc Biệt' : 'Bình Thường')
Vue.filter('customerType', (v) => parseInt(v) === 1 ? 'VIP' : 'Thường')
Vue.filter('tuitionTransferType', (v) => parseInt(v) === 1 ? 'Chuyển Buổi' : 'Chuyển Phí')
Vue.filter('prepareText', (v, l = 20) => v && v.length ? u.sub(v, l) : '')
Vue.filter('filterPercentage', (v, p) => parseInt(p) > 0 ? `${+(`${Math.round(`${(parseInt(v) / parseInt(p)) * 100 }e+2`)}e-2`)}%` : '0%')
Vue.filter('filterRatio', (v, p) => parseInt(p) > 0 ? +(`${Math.round(`${parseInt(v) / parseInt(p)}e+2`)}e-2`) : '0')
Vue.filter('validSessionContractType', (t, v) => t === 0 ? 3 : v)
Vue.filter('countLearnDay', (v) => Math.ceil(v))
Vue.filter('dayLeft', (v) => Math.floor(v))
Vue.filter('studentStatus', (v) => parseInt(v) === 1 ? 'Active' : parseInt(v) === 2 ? 'Transfering' : 'Withdraw')
Vue.filter('statusToName', (v) => parseInt(v) === 1 ? 'Active' : 'Inactivev')
Vue.filter('getStatusToName', (v) => parseInt(v) === 1 ? 'Hoạt động' : 'Ngừng hoạt động')
Vue.filter('reserveStatus', (v) => parseInt(v) === 3 ? 'Đã xóa' : parseInt(v) === 2 ? 'Từ chối' : parseInt(v) === 1 ? 'Đã duyệt' : 'Chờ duyệt')
Vue.filter('formatNumber', (v) => !isNaN(v) && v > 0 ? parseInt(v).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2) : 0)
Vue.filter('formatNumber2', (v) => parseFloat(v).toFixed(2))
Vue.filter('formatMoney', (v, c = 'đ') => !isNaN(v) && v > 0 && c !== '' ? `${parseInt(v).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)}${c}` : `0${c}`)
Vue.filter('classStudentStatus', (v) => parseInt(v) === 0 ? 'Chờ duyệt' : parseInt(v) === 1 ? 'Chấp nhận' : 'Từ chối')
Vue.filter('rankTypeToName', (v) => parseInt(v) === 0 ? 'Nhân viên' : 'Học sinh')
Vue.filter('trimChar', (v) => v.rtrim())
Vue.filter('totalPerMax', (total, max) => max ? `${parseInt(total)}/${parseInt(max)}` : '')
Vue.filter('tuitionFeeLabel', (name, price) => name && price ? `${name} - ${price}` : name)
Vue.filter('parentLabel', (name, phone) => name && phone ? `${name} (${phone})` : name)
Vue.filter('shortText', (v, l = 20) => v.toString().length && !isNaN(l) && l > 10 ? u.sub(v, l) : '')
Vue.filter('workingDate', (v, d = '') => v && v !== 'null' && v !== '0000-00-00' && v !== '' ? moment(v).format('YYYY-MM-DD') : d)
Vue.filter('formatDate', (v) => v && v !== '0000-00-00' ? moment(v).format('YYYY-MM-DD') : v)
Vue.filter('formatTime', (v) => v ? moment(v).format('YYYY-MM-DD HH:mm:ss') : v)
Vue.filter('timeFormat', (v, f = 'YYYY-MM-DD HH:mm:ss') => v ? moment(v).format(f) : v)
Vue.filter('timeShiftFormat', (v, f = 'HH:mm:ss') => v ? moment(v).format(f) : v)
Vue.filter('displayClass', (n, s) => parseInt(s, 10) === 0 ? '' : n)
Vue.filter('formatAnswer', (is_right) => is_right ? 'fa-check' : '')
Vue.filter('formatCurrency', (v, c = 'đ') => {
  let resp     = ''
  let number   = 0
  let currency = c
  if (parseFloat(v) < 100) {
    number   = parseFloat(v)
    currency = '%'
    resp     = `${number}${currency}`
  } else {
    number = parseInt(v)
    resp   = number > 0 ? `${number.toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)}${currency}` : 0
  }
  return resp
})
Vue.filter('formatCurrency2', (v, c = 'đ') => {
  let resp     = ''
  let number   = 0
  let currency = c
  if (parseFloat(v) < 100) {
    number   = parseFloat(v)
    currency = 'đ'
    resp     = `${number}${currency}`
  } else {
    number = parseInt(v)
    resp   = number > 0 ? `${number.toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)}${currency}` : 0
  }
  return resp
})

const studentSource = {
  1 : 'Google',
  2 : 'FACEBOOK',
  3 : 'Video trực tuyến',
  4 : 'QC ngoài trời',
  5 : 'QC trên taxi',
  6 : 'QC thang máy',
  7 : 'QC trực tuyến',
  8 : 'QC trên đài PT',
  9 : 'Hội thảo/Sự kiện',
  10: 'Bạn bè giới thiệu',
  11: 'KOLs/Hot Mom/Dad',
  12: 'Bài viết trên báo',
  13: 'Email/Ứng dụng',
  14: 'B2B-BUSINESS',
  15: 'B2B-SCHOOL',
  16: 'B2B-BUSINESS',
  17: 'EGROUP',
  18: 'TELE - HO',
  19: 'TELE - Center',
  20: 'CTV',
  21: 'Bán hàng trực tiếp',
  22: 'Walk in',
  23: 'Khác',
}

Vue.filter('studentSource', (v) => {
  return studentSource[v] || 'Khác'
})

Vue.filter('contractType', (v) => {
  let resp = ''
  switch (v) {
    case 1:
      resp = 'Chính thức'
      break
    case 2:
      resp = 'Tái phí bình thường'
      break
    case 3:
      resp = 'Tái phí do nhận chuyển phí'
      break
    case 4:
      resp = 'Chỉ nhận chuyển phí'
      break
    case 5:
    case 85:
      resp = 'Chuyển trung tâm'
      break
    case 6:
    case 86:
      resp = 'Chuyển Lớp'
      break
    case 7:
      resp = 'Tái phí nhưng chưa đóng đủ phí'
      break
    case 8:
      resp = 'Học bổng'
      break
    case 10:
      resp = 'Bảo lưu không giữ chỗ'
      break
    default:
      resp = ''
      break
  }
  return resp
})
Vue.filter('contractStatus', (v, t) => {
  let resp = ''
  if (t === 0)
    resp = v === 7 ? 'Đã withdraw' : 'Học trải nghiệm'
  else {
    switch (v) {
      case 1:
        resp = 'Đã active nhưng chưa đóng phí'
        break
      case 2:
        resp = 'Đã active, đặt cọc nhưng chưa thu đủ phí'
        break
      case 3:
        resp = 'Đã thu đủ phí nhưng chưa được xếp lớp'
        break
      case 4:
        resp = 'Đang bảo lưu hoặc pending'
        break
      case 5:
        resp = 'Đang được nhận học bổng hoặc học trải nghiệm hoặc là VIP'
        break
      case 6:
        resp = 'Đã được xếp lớp và đang đi học'
        break
      case 7:
        resp = 'Đã bị withdraw'
        break
      case 8:
        resp = 'Đã bỏ cọc'
        break
      default:
        resp = 'Đã bị xóa'
        break
    }
  }
  return resp
})
Vue.filter('studentType', (v) => {
  let resp = ''
  switch (v) {
    case 1:
      resp = 'Chính thức'
      break
    case 2:
      resp = 'học trải nghiệm'
      break
    case 3:
      resp = 'Withdraw'
      break
    case 4:
      resp = 'Pending'
      break
    case 5:
      resp = 'Đặt cọc'
      break
    case 6:
      resp = 'Tiềm năng'
      break
    default:
      resp = 'Checked'
      break
  }
  return resp
})
Vue.filter('rankType', (v) => {
  let resp = ''
  switch (v) {
    case 1:
      resp = 'Tốt'
      break
    case 7:
      resp = 'Yếu'
      break
    case 8:
      resp = 'Cá biệt'
      break
    case 9:
      resp = 'Chăm sóc đặc biệt'
      break
  }
  return resp
})
Vue.filter('genDownloadUrl', (v) => {
  return v
})
Vue.filter('reserveType', (v, c = 0) => {
  let resp = ''
  if (c === 0) {
    switch (v) {
      case 0:
        resp = 'Bảo lưu theo quy định'
        break
      case 1:
        resp = 'Bảo lưu ngoài quy định'
        break
      case 2:
        resp = 'Bảo lưu ngoài quy định'
        break
      default:
        resp = ''
    }
  } else {
    switch (v) {
      case 0:
        resp = 'Bảo lưu theo quy định (Bảo lưu bổ sung)'
        break
      case 1:
        resp = 'Bảo lưu ngoài quy định (Bảo lưu bổ sung)'
        break
      case 2:
        resp = 'Bảo lưu ngoài quy định (Bảo lưu bổ sung)'
        break
      default:
        resp = ''
    }
  }

  return resp
})
Vue.filter('paymentType', (v) => {
  let resp = ''
  switch (v) {
    case 0:
      resp = 'Tiền Mặt'
      break
    case 1:
      resp = 'Chuyển Khoản'
      break
    default:
      resp = 'Thẻ Tín Dụng'
  }
  return resp
})
Vue.filter('productName', (v) => {
  let resp = ''
  switch (v) {
    case 1:
      resp = 'iGarten'
      break
    case 2:
      resp = 'April'
      break
    case 3:
      resp = 'CDI 4.0'
      break
    case 4:
      resp = 'IELTS'
      break
    default:
      resp = ''
  }
  return resp
})
Vue.filter('studentsStatus', (s) => {
  let resp = ''
  if (s.is_pending)
    resp = 'Pending'
  else {
    if (s.enrolment_status && s.enrolment_status === 0)
      resp = 'Withdraw'
    else if (s.contract_status === null)
      resp = 'Tiềm năng'
    else
      resp = 'Active'
  }
  return resp
})
Vue.filter('reserveStatus', (v) => {
  let resp = ''
  switch (v) {
    case 0:
      resp = 'Chờ duyệt'
      break
    case 1:
      resp = 'Đã duyệt'
      break
    case 2:
      resp = 'Từ chối'
      break
    case 3:
      resp = 'Đã xóa'
      break
    default:
      resp = 'Đang xử lý'
  }
  return resp
})
Vue.filter('tuitionTransferApproveLevel', (v) => {
  let resp = ''
  switch (v) {
    case 0:
      resp = 'GĐTT/GĐV'
      break
    case 1:
      resp = 'Kế toán hội sở'
      break
    default:
      resp = ''
  }
  return resp
})
Vue.filter('reserveApproveLevel', (v) => {
  let resp = ''
  switch (v) {
    case 0:
      resp = 'GĐTT/GĐV'
      break
    case 1:
      resp = 'Kế toán hội sở'
      break
    default:
      resp = ''
  }
  return resp
})
Vue.filter('branchTransferStatus', (v) => {
  let resp = ''
  switch (v) {
      case 1:
      resp = 'Chờ duyệt đi'
      break
      case 2:
      resp = 'Trung tâm chuyển đã từ chối'
      break
      case 3:
      resp = 'Trung tâm nhận đã từ chối'
      break
      case 4:
      resp = 'Chờ duyệt đến'
      break
      case 5:
      resp = 'Chờ kế toán HO phê duyệt'
      break
      case 6:
      resp = 'Đã được phê duyệt'
      break
      case 7:
      resp = 'Kế toán HO từ chối phê duyệt'
      break
      default:
      resp = 'Đã xóa'
      break
  }
  return resp
})
Vue.filter('tuitionTransferStatus', (v) => {
  let resp = ''
  switch (v) {
    case 1:
      resp = 'Chờ giám đốc TT duyệt'
      break
      case 2:
      resp = 'Giám đốc TT đã từ chối'
      break
      case 3:
      resp = 'Kế toán HO đã từ chối'
      break
      case 4:
      resp = 'Chờ Kế toán HO phê duyệt'
      break
      case 5:
      resp = 'Kế toán HO đã duyệt'
      break
      case 6:
      resp = 'Đã được duyệt thành công'
      break
      default:
      resp = 'Đã bị hủy'
      break
  }
  return resp
})
