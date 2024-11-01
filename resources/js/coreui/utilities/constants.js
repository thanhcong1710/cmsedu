export const attendanceStatus = [
  {
    label: 'Chưa điểm danh',
    value: 0,
  },
  {
    label: 'Có đi học',
    value: 1,
  },
  {
    label: 'Vắng mặt',
    value: 2,
  },
  {
    label: 'Đi muộn',
    value: 3,
  },
  {
    label: 'Về sớm',
    value: 4,
  },
  {
    label: 'Vắng mặt có lý',
    value: 5,
  },
  {
    label: 'Nghỉ lễ',
    value: 6,
  },
  {
    label: 'Nghỉ có phép',
    value: 7,
  },
  {
    label: 'Vắng mặt do bảo lưu dữ chỗ',
    value: 8,
  },
]

export const contractStatus = {
  0: 'Đã xóa',
  1: 'Đã active nhưng chưa đóng phí',
  2: 'Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí',
  3: 'Đã active và đã thu đủ phí nhưng chưa được xếp lớp',
  4: 'Đang bảo lưu không giữ chỗ hoặc pending',
  5: 'Đang được nhận học bổng hoặc VIP',
  6: 'Đã được xếp lớp và đang đi học',
  7: 'Đã bị withdraw',
  8: 'Đã bỏ cọc',
}

export const reportTypeOptions = [
  {
    label: 'Báo cáo hàng ngày',
    value: 0,
  },
  {
    label: 'Báo cáo hàng tháng',
    value: 1,
  },
]

export const reportMonthTypeOptions = [
  {
    label: 'Báo cáo hàng tháng',
    value: 1,
  },
]

export const reportDaysTypeOptions = [
  {
    label: 'Báo cáo hàng ngày',
    value: 0,
  },
]

export const getLabelAttendanceStatusByValue = (value) => (attendanceStatus[value].label)

export const sourceForStudentTempOptions = [
  {
    label: 'Data lạnh',
    value: 'Data lạnh',
  },
  {
    label: 'Giới thiệu',
    value: 'Giới thiệu',
  },
  {
    label: 'Marketing',
    value: 'Marketing',
  },
  {
    label: 'Egroup',
    value: 'Egroup',
  },
  {
    label: 'Internet/Facebook',
    value: 'Internet/Facebook',
  },
  {
    label: 'Direct',
    value: 'Direct',
  },
  {
    label: 'B2B',
    value: 'B2B',
  },
  {
    label: 'B2C',
    value: 'B2C',
  },
]

export const genderOptions = [
  {
    label: 'Nam',
    value: 1,
  },
  {
    label: 'Nữ',
    value: 0,
  },
]

export const typeStudentTempOptions = [
    {
        label: 'Tất cả',
        value: -1,
    },
    {
        label: 'Dữ liệu hợp lệ',
        value: 0,
    },
    {
        label: 'Dữ liệu bị trùng',
        value: 1,
    },
    {
        label: 'Đã là học sinh',
        value: 2,
    },
]

export const contactOptions = [
    {
        label: 'Gọi điện thoại',
        value: 1,
    },
    {
        label: 'Gặp trực tiếp',
        value: 2,
    },
]

export const qualityOptions = [
    {
        label: 'Có con trong độ tuổi và có quan tâm đến CMS',
        value: 6,
    },
    {
        label: 'Có tín hiệu, Không nghe máy, dập máy',
        value: 9,
    },
    {
        label: 'Đến CMS ( làm bài test, tìm hiểu chương trình, tham gia sự kiện )',
        value: 4,
    },
    {
        label: 'Đồng ý lịch hẹn lên trải nghiệm',
        value: 5,
    },
    {
        label: 'Gọi chăm sóc khách hàng',
        value: 2,
    },
    {
        label: 'Nhận góp ý từ khách hàng',
        value: 1,
    },
    {
        label: 'Nói chuyện được với contact',
        value: 7,
    },
    {
      label: 'Nộp tiền',
      value: 3,
    },
    {
      label: 'Số điện thoại không tồn tại, nhầm máy',
      value: 8,
    },
    {
      label: 'Chọn trạng thái khách hàng',
      value: 0,
    },
]

export const scoreContractArray = {
  0: 0,
  1: 1,
  2: 1,
  3: 5,
  4: 4,
  5: 3,
  6: 2,
  7: 1,
  8: 0,
  9: 0,
}