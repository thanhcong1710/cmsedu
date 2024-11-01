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
  name: 'reports',
  pages: {
    report: {
      p: '/form/:id',
      n: 'Chi Tiết Báo Cáo'
    },
    form: {
      p: '/forms',
      n: 'Báo Cáo Theo Biểu'
    },
    statistic: {
      p: '/statistics',
      n: 'Báo Cáo Thống Kê'
    },
    report01a1: {
      p: '/reports/1a1',
      n: 'Báo Cáo Chi Tiết Học Sinh Full Fee Active'
    },
    report01b1: {
      p: '/reports/1b-1',
      n: 'BC02A1 BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ'
    },
    report01b2: {
      p: '/reports/1b-2',
      n: 'BC02A2 - BÁO CÁO HỌC SINH TÁI PHÍ - TỔNG HỢP'
    },
    report01b3: {
      p: '/reports/1b-3',
      n: 'BC02A3 - BÁO CÁO HỌC SINH TÁI PHÍ THEO EC'
    },
    report02a: {
      p: '/reports/2a',
      n: 'Báo Cáo Chi Tiết Học Sinh Pending'
    },
    report02b: {
      p: '/reports/2b',
      n: 'Báo Cáo Chi Tiết Học Sinh Bảo Lưu'
    },
    report01a: {
      p: '/reports/1a',
      n: 'Báo Cáo 01a'
    },
    report02: {
      p: '/reports/2',
      n: 'Báo Cáo 02'
    },
    report03: {
      p: '/reports/3',
      n: 'Báo Cáo 03'
    },
    report04: {
      p: '/reports/4',
      n: 'Báo Cáo 04'
    },
    report05: {
      p: '/reports/5',
      n: 'Báo Cáo 05'
    },
    report06: {
      p: '/reports/6',
      n: 'Báo Cáo 06'
    },
    report07: {
      p: '/reports/7',
      n: 'Báo Cáo 07'
    },
    report08: {
      p: '/reports/8',
      n: 'Báo Cáo 08'
    },
    report09: {
      p: '/reports/9',
      n: 'Báo Cáo 09'
    },
    report10: {
      p: '/reports/10',
      n: 'Báo Cáo 10'
    },
    report11: {
      p: '/reports/11',
      n: 'Báo Cáo 11'
    },
    report12: {
      p: '/reports/12',
      n: 'Báo Cáo 12'
    },
    report13: {
      p: '/reports/13',
      n: 'Báo Cáo 13'
    },
    report14: {
      p: '/reports/14',
      n: 'Báo Cáo 14'
    },
    report15: {
      p: '/reports/15',
      n: 'Báo Cáo 15'
    },
    report16: {
      p: '/reports/16',
      n: 'Báo Cáo 16'
    },
    report17a: {
      p: '/reports/17a',
      n: 'Báo Cáo 17a'
    },
    report17b: {
      p: '/reports/17b',
      n: 'Báo Cáo 17b'
    },
    report17c: {
      p: '/reports/17c',
      n: 'Báo Cáo 17c'
    },
    report17d: {
      p: '/reports/17d',
      n: 'Báo Cáo 17d'
    },
    report18: {
      p: '/reports/18',
      n: 'Báo Cáo 18'
    },
    report19: {
      p: '/reports/19',
      n: 'Báo Cáo 19'
    },
    report20: {
      p: '/reports/20',
      n: 'Báo Cáo 20'
    },
    report20b: {
      p: '/reports/20b',
      n: 'Báo Cáo 20b'
    },
    report27: {
      p: '/reports/27',
      n: 'Báo Cáo Thông Tin Học Viên'
    },
    report28: {
      p: '/reports/28',
      n: 'Báo Cáo Danh Sách Học Sinh Chưa Bị Withdraw'
    },
    newStudentReport: {
      p: '/reports/new-student',
      f: 'new-student-report/Index',
      n: 'Báo Cáo học sinh mới'
    },
    studentCareReport: {
      p: '/reports/student-care',
      f: 'student-care/index',
      n: 'Báo Cáo tương tác mới Tư vấn viên với khách hàng'
    },
    studentActiveReport: {
      p: '/reports/student-active',
      f: 'student-active/index',
      n: 'Báo cáo số lượng học sinh thực học tại trung tâm'
    },
    tuitionFeeReport: {
      p: '/reports/tuition-fee',
      f: 'tuition-fee/index',
      n: 'Báo cáo thống kê gói học trong tháng'
    },
    studentHistoryReport: {
      p: '/reports/student-history',
      f: 'student-history/index',
      n: 'Báo cáo lịch sử học viên theo trung tâm'
    },
    tuitionPercentage: {
      p: '/reports/tuition-percentage',
      f: 'tuition-percentage/index',
      n: 'Báo cáo tỉ lệ theo gói phí'
    },
    semesterPercentage: {
      p: '/reports/semester-percentage',
      f: 'semester-percentage/index',
      n: 'Báo cáo tỉ lệ theo chương trình học '
    },
    studentRenewalsPercentage: {
      p: '/reports/student-renewals-percentage',
      f: 'student-renewals-percentage/index',
      n: 'Báo cáo danh sách học sinh cần renew trong tháng'
    },
    studentQuantityReport: {
      p: '/reports/student-quantity-report',
      f: 'student-quantity/index',
      n: 'Báo cáo số lượng học sinh'
    },
    studentWithdrawReport: {
      p: '/reports/student-withdraw-report',
      f: 'student-withdraw/index',
      n: 'Danh sách học sinh đã ngừng học tại CMS'
    },
    studentTrialLear: {
      p: '/reports/student-trial-learn',
      f: 'student-trial-learn/index',
      n: 'Báo cáo danh sách học thử'
    },
    pendingStudentReport: {
      p: '/reports/pending-student',
      f: 'pending-student/Index',
      n: 'Báo cáo học sinh chờ xếp lớp'
    },
    activeStudentReport: {
      p: '/reports/student-active-report',
      f: 'student-active-report/Index',
      n: 'Báo cáo học sinh đang theo học'
    },
    report_r01: {
      p: '/reports/r01',
      n: 'Báo cáo khách hub lên gói phí tại trung tâm'
    },
    report_r02: {
      p: '/reports/r02',
      n: 'Báo cáo khách Marketing lên gói phí tại trung tâm'
    },
    report_r04: {
      p: '/reports/r04',
      n: 'Báo cáo học sinh mới'
    },
    report_r05: {
      p: '/reports/r05',
      n: 'Báo cáo doanh thu'
    },
    report_r06: {
      p: '/reports/r06',
      n: 'Báo cáo doanh thu theo nguồn'
    },
    report_r07: {
      p: '/reports/r07',
      n: 'Báo cáo tình hình học sinh UCREA'
    },
    report_r08: {
      p: '/reports/r08',
      n: 'Báo cáo tình hình học sinh IG - BH'
    },
    report_r09: {
      p: '/reports/r09',
      n: 'Báo cáo checkin'
    },
    report_r10: {
      p: '/reports/r10',
      n: 'Báo cáo học sinh đang học thử'
    },
    report_r11: {
      p: '/reports/r11',
      n: 'Báo cáo học sinh đã học thử'
    },
    report_r12: {
      p: '/reports/r12',
      n: 'Báo cáo confirm học sinh chi tiết'
    },
    report_r13: {
      p: '/reports/r13',
      n: 'Báo cáo confirm'
    },
  }
})

export default {
  routers,
  router: {
    path: "/forms",
    redirect: "/forms",
    name: "Báo Cáo",
    component: {
      render(c) {
        return c("router-view");
      }
    },
    children: [
      routers.pendingStudentReport,
      routers.activeStudentReport,
      routers.statistic,
      routers.form,
      routers.report01a,
      routers.report02,
      routers.report03,
      routers.report04,
      routers.report05,
      routers.report06,
      routers.report07,
      routers.report08,
      routers.report09,
      routers.report10,
      routers.report11,
      routers.report12,
      routers.report13,
      routers.report14,
      routers.report15,
      routers.report16,
      routers.report17a,
      routers.report17b,
      routers.report17c,
      routers.report17d,
      routers.report18,
      routers.report19,
      routers.report20,
      routers.report20b,
      routers.report27,
      routers.report28,
      routers.newStudentReport,
      routers.studentCareReport,
      routers.studentActiveReport,
      routers.tuitionFeeReport,
      routers.studentHistoryReport,
      routers.tuitionPercentage,
      routers.semesterPercentage,
      routers.studentRenewalsPercentage,
      routers.studentQuantityReport,
      routers.studentWithdrawReport,
      routers.studentTrialLear,
      routers.report01a1,
      routers.report01b1,
      routers.report01b2,
      routers.report01b3,
      routers.report02a,
      routers.report02b,
      routers.report_r01,
      routers.report_r02,
      routers.report_r04,
      routers.report_r05,
      routers.report_r06,
      routers.report_r07,
      routers.report_r08,
      routers.report_r09,
      routers.report_r10,
      routers.report_r11,
      routers.report_r12,
      routers.report_r13
    ]
  }
};
