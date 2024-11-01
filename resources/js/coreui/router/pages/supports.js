/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
import u from '../../utilities/utility'

const routers = u.load({
  name: 'supports',
  pages: {
    list: {
      p: 'list',
      n: 'Công cụ'
    },
    care1: {
      p: '/support/import1',
      f: 'import1/Index',
      n: 'Chăm Sóc kh'
    },
    tool_branch_transfer: {
      p: '/tool_branch_transfer/branch_transfer',
      f: 'tool_branch_transfer/Index',
      n: 'Chuyen TT'
    },
    supports_class_day: {
      p: '/supports/change/enrolment_last_date/:id',
      f: 'transfer/back_date/class_day',
      n: 'Sửa ngày xếp lớp'
    },
    supports_enrolment_last_date: {
      p: '/supports/branch/change_enrolment_last_date',
      f: 'branch/change_last_date',
      n: 'Sửa ngày xếp lớp'
    },
    convert_tuition_fee: {
      p: '/supports/convert-tuition-fee',
      f: 'convert_tuition_fee/index',
      n: 'Qui đổi gói phí'
    },
    transfer_all_class: {
      p: '/supports/transfer-all-class',
      f: 'transfer_all_class/index',
      n: 'Chuyển all học sinh'
    }
  }
})

export default {
  routers,
  router: {
    path: 'supports',
    redirect: '/supports/list',
    name: 'Tools',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.list,
      routers.care1,
      routers.tool_branch_transfer,
      routers.supports_class_day,
      routers.supports_enrolment_last_date,
      routers.convert_tuition_fee,
      routers.transfer_all_class,
    ]
  }
}
