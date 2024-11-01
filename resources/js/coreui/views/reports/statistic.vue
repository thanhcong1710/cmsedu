<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card header-tag="header" footer-tag="footer">
                        <div slot="header">
                            <i class="fa fa-align-justify"></i> <strong>Thống kê</strong>
                        </div>
                        <div class="content-detail">
                            <p class="h6"><a href="#">01. Thống kê hiệu suất phòng học</a></p>
                            <p class="h6"><a href="#">02. Thống kê học sinh Tái phí</a></p>
                            <p class="h6"><a href="#">03. Thống kê cọc bỏ, full phí/ Tổng trial, số full phí/ Tổng check in</a></p>
                            <p class="h6"><a href="#">04. Thống kê học sinh mới hàng tháng</a></p>
                            <p class="h6"><a href="#">05. Thống kê trung bình số lớp/GV, Học sinh full/GV</a></p>
                            <p class="h6"><a href="#">06. Thống kê hiệu suất phòng học, hiệu suất học sinh trên tổng sức chứa</a></p>
                            <p class="h6"><a href="#">07. Thống kê tổng số học sinh</a></p>
                            <p class="h6"><a href="#">08. Thống kê phân loại học sinh: Tốt, yếu, cá biệt</a></p>
                            <p class="h6"><a href="#">09. Thống kê phân loại CM</a></p>
                            <p class="h6"><a href="#">10. Thống kê học sinh withdraw</a></p>
                            <p class="h6"><a href="#">11. Thống kê doanh số</a></p>
                            <p class="h6"><a href="#">12. Thống kê chuyển lớp/ chuyển trung tâm</a></p>
                            <p class="h6"><a href="#">13. Thống kê chuyển phí</a></p>
                            <p class="h6"><a href="#">14. Thống kê công nợ</a></p>
                            <p class="h6"><a href="#">15. Thống kê pending</a></p>
                            <p class="h6"><a href="#">16. Thống kê quá hạn cọc</a></p>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import u from '../../utilities/utility'

export default {
  name: 'Report-Statistic',
  data () {
    return {
      
    }
  },
  methods: {
    
  }
}
</script>

<style scoped>

</style>