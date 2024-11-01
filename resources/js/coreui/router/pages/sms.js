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
  name: 'sms',
  pages: {
    send_sms_list: {
      p: '/send_sms',
      f: 'send_sms/list',
      n: 'Gửi Sms'
    },
    list_campaign: {
      p: '/sms/campaign/list',
      f: 'campaign/list',
      n: 'Danh sách chiến dịch đã gửi'
    },
    list_template: {
      p: '/sms/template/list',
      f: 'template/list',
      n: 'Danh mục mẫu tin nhắn CSKH'
    },
    add_template:{
      p: '/sms/template/add',
      f: 'template/add',
      n: 'Thêm mới mẫu tin nhắn CSKH'
    },
    edit_template:{
      p: '/sms/template/:id/edit',
      f: 'template/edit',
      n: 'Chỉnh sửa mẫu tin nhắn CSKH'
    },
  }
})

export default {
  routers,
  router: {
    path: '/send_sms',
    redirect: '/send_sms',
    name: 'SMS',
    component: {
      render (c) {
        return c('router-view')
      }
    },
    children: [
      routers.send_sms_list,
      routers.list_campaign,
      routers.list_template,
      routers.add_template,
      routers.edit_template,
    ]
  }
}
