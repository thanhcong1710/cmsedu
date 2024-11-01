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

import Dashboard from '../../views/base/new_dashboard/Dashboard'
import Colors from '../../views/base/theme/Colors'
import Typography from '../../views/base/theme/Typography'
import Charts from '../../views/base/charts/Charts'
import Widgets from '../../views/base/widegets/Widgets'
// import GoogleMaps from '../../views/base/widegets/GoogleMaps'
import RelationSelect from '../../views/base/widegets/RelationSelect'
import StandardButtons from '../../views/base/buttons/StandardButtons'
import ButtonGroups from '../../views/base/buttons/ButtonGroups'
import Dropdowns from '../../views/base/buttons/Dropdowns'
import SocialButtons from '../../views/base/buttons/SocialButtons'
import BasicForms from '../../views/base/forms/BasicForms'
import AdvancedForms from '../../views/base/forms/AdvancedForms'
import Cards from '../../views/base/widegets/Cards'
import Switches from '../../views/base/widegets/Switches'
import Tables from '../../views/base/tables/Tables'
import Modals from '../../views/base/notifications/Modals'
import Breadcrumbs from '../../views/base/widegets/Breadcrumbs'
import Carousels from '../../views/base/widegets/Carousels'
import Collapses from '../../views/base/widegets/Collapses'
import Jumbotrons from '../../views/base/widegets/Jumbotrons'
import ListGroups from '../../views/base/widegets/ListGroups'
import Navs from '../../views/base/widegets/Navs'
import Navbars from '../../views/base/widegets/Navbars'
import Paginations from '../../views/base/widegets/Paginations'
import Popovers from '../../views/base/widegets/Popovers'
import ProgressBars from '../../views/base/widegets/ProgressBars'
import Tooltips from '../../views/base/widegets/Tooltips'
import Alerts from '../../views/base/notifications/Alerts'
import Badges from '../../views/base/notifications/Badges'
import Invoice from '../../views/base/ui-kits/invoicing/Invoice'
import Compose from '../../views/base/ui-kits/email/Compose'
import Inbox from '../../views/base/ui-kits/email/Inbox'
import Message from '../../views/base/ui-kits/email/Message'
import Toastr from '../../views/base/notifications/Toastr'
import Flags from '../../views/base/icons/Flags'
import FontAwesome from '../../views/base/icons/FontAwesome'
import SimpleLineIcons from '../../views/base/icons/SimpleLineIcons'

const routers = {
  main: {
    path: '/dashboard',
    name: 'Dashboard',
    Dashboard
  },
  color: {
    path: '/colors',
    name: 'Colors',
    Colors
  },
  typography: {
    path: '/typography',
    name: 'Typography',
    Typography
  },
  dropdowns: {
    path: '/dropdowns',
    name: 'Dropdowns',
    Dropdowns
  },
  card: {
    path: '/cards',
    name: 'Cards',
    component: Cards
  },
  // google_map: {
  //   path: '/google-maps',
  //   name: 'Google Maps',
  //   component: GoogleMaps
  // },
  breadcrumb: {
    path: '/breadcrumbs',
    name: 'Breadcrumbs',
    component: Breadcrumbs
  },
  carousel: {
    path: '/carousels',
    name: 'Carousels',
    component: Carousels
  },
  collapse: {
    path: '/collapses',
    name: 'Collapses',
    component: Collapses
  },
  jumbotron: {
    path: '/jumbotrons',
    name: 'Jumbotrons',
    component: Jumbotrons
  },
  list_group: {
    path: '/listGroups',
    name: 'ListGroups',
    component: ListGroups
  },
  navs: {
    path: '/navs',
    name: 'Navs',
    component: Navs
  },
  navbar: {
    path: '/navbars',
    name: 'Navbars',
    component: Navbars
  },
  pagination: {
    path: '/paginations',
    name: 'Paginations',
    component: Paginations
  },
  popover: {
    path: '/popovers',
    name: 'Popovers',
    component: Popovers
  },
  progress_bar: {
    path: '/progress-bars',
    name: 'Progress Bars',
    component: ProgressBars
  },
  tooltip: {
    path: '/tooltips',
    name: 'Tooltips',
    component: Tooltips
  },
  alerts: {
    path: '/alerts',
    name: 'Alerts',
    component: Alerts
  },
  badges: {
    path: '/badges',
    name: 'Badges',
    component: Badges
  },
  invoice: {
    path: '/invoice',
    name: 'Invoice',
    component: Invoice
  },
  compose: {
    path: '/compose',
    name: 'Compose',
    component: Compose
  },
  inbox: {
    path: '/inbox',
    name: 'Inbox',
    component: Inbox
  },
  message: {
    path: '/message',
    name: 'Message',
    component: Message
  },
  toast: {
    path: '/toastr',
    name: 'Toastr',
    component: Toastr
  },
  flag: {
    path: '/flags',
    name: 'Flags',
    component: Flags
  },
  basic_form: {
    path: '/dashboard/form',
    name: 'Basic Forms',
    component: BasicForms
  },
  advanced_form: {
    path: '/dashboard/forms',
    name: 'Advanced Forms',
    component: AdvancedForms
  },
  chart: {
    path: '/dashboard/charts',
    name: 'Charts',
    component: Charts
  },
  modal: {
    path: '/modals',
    name: 'Modals',
    component: Modals
  },
  table: {
    path: '/tables',
    name: 'Data Tables',
    component: Tables
  },
  switch: {
    path: '/switches',
    name: 'Switches',
    component: Switches
  },
  font: {
    path: '/font',
    name: 'Awesome Font',
    component: FontAwesome
  },
  icon: {
    path: '/icons',
    name: 'SimpleLine Icons',
    component: SimpleLineIcons
  },
  wideget: {
    path: '/widgets',
    name: 'Widgets',
    component: Widgets
  },
  button: {
    path: '/buttons',
    name: 'Buttons',
    component: StandardButtons
  },
  button_group: {
    path: '/button-groups',
    name: 'Button Groups',
    component: ButtonGroups
  },
  social: {
    path: '/socials',
    name: 'Socials Buttons',
    component: SocialButtons
  },
  relation: {
    path: '/relations',
    name: 'Relations Select',
    component: RelationSelect
  }
}

export default {
  routers,
  router: {
    path: 'dashboard',
    name: 'Báo Cáo',
    component: Dashboard
  }
}
