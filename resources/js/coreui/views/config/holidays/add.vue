<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Ngày Nghỉ Lễ Mới </strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                 <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Tên</label>
                <input type="text" v-model="holiday.name" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Từ ngày</label>
                <input type="date" v-model="holiday.start_date" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Đến ngày</label>
                <input type="date" v-model="holiday.end_date" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Khu vực áp dụng</label>
                <select v-model="holiday.zone_id" class="form-control" id="">
                  <option value="" disabled>Lựa chọn khu vực</option>
                  <option :value="zone.id" v-for="(zone,index) in zones">{{zone.name}}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Sản phẩm</label>
                  <div class="form-check-group"> 
                      <div class="form-check-left" v-for="(product,index) in products">
                          <input type="checkbox" :value="product.id" class="form-check-input" v-model="holiday.products" />
                          <label class="form-check-label left-check">{{product.name}}</label>
                      </div>
                  </div>
                </div>
              </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select class="form-control" v-model="holiday.status">
                  <option value="" disabled>Lựa chọn trạng thái</option>
                  <option value="0">Không hoạt động</option>
                  <option value="1">Hoạt động</option>
                </select>
              </div>
            </div>
          </div>
        </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="addHoliday"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="{name: 'Danh Sách Các Ngày Nghỉ Lễ'}">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>

       <b-modal 
              :title="html.modal.title" 
              :class="html.modal.class" size="sm" 
              v-model="html.modal.display" 
              @ok="action.modal" 
              ok-variant="primary"
          >
           <div v-html="html.modal.message"></div>
        </b-modal>


    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Add-Holiday',
    data() {
      return {
        //Set default checked
        html: {
          modal: {
            title: 'Thông Báo',
            class: 'modal-success',
            message: '',
            display:  false
          }
        },
        action: {
          modal: () => this.exitModal(),
        },
        product: '',
        products: [],
        branches: [],
        status: '0',
        area:'0',
        regions: [],
        zones: [],
        region: '',
        holidays: [],
        holiday: {
          zone_id: '',
          name: '',
          product_id: '',
          branch_id: '',
          start_date: '',
          end_date: '',
          status: '',
          products: [],
        }
      }
    },
    created(){
      axios.get(`/api/branches`).then(response =>{
        this.branches = response.data;
      })
      axios.get(`/api/zones`).then(response =>{
        this.zones = response.data;
      })
      axios.get(`/api/products`).then(response => {
        this.products = response.data.data
      })
      axios.get(`/api/holidays`).then(response => {
        this.holidays = response.data.data
      })

      axios.get(`/api/regions`).then(response => {
        this.regions = response.data.data
      })
    },
    methods: {
      addHoliday() {
        const holiday = {
          zone_id: this.holiday.name,
          name: this.holiday.name,
          product_id: this.holiday.product_id,
          start_date: this.holiday.start_date,
          end_date: this.holiday.end_date,
          status: this.holiday.status
        }
        if(holiday.zone_id == ''){
          alert("Khu vực áp dụng không để trống !")
          return false
        }else if(holiday.name == ''){
          alert("Tên không để trống !")
          return false
        }else if(holiday.start_date > holiday.end_date){
          alert("Ngày kết thúc không được lớn hơn ngày bắt đầu !")
          return false
        }else if(this.holiday.products.length === 0){
          alert("Vui lòng chọn 1 sản phẩm !")
          return false
        }else if(holiday.status === ''){
          alert("Chọn trạng thái !")
          return false
        }
        else {
          this.saveHoliday()
        }
      },
      saveHoliday(){
        axios.post(`/api/publicHolidays`, this.holiday).then(response => {
          // this.$router.push('/holidays')
          this.html.modal.message = "Thêm mới thành công ngày nghỉ lễ!"
          this.html.modal.display = true
        });
      },
      resetAll(){
        this.holiday.name = ''
        this.holiday.lms_proc_id = ''
        this.holiday.price = ''
        this.holiday.receivable = ''
        this.holiday.status = ''
        this.holiday.zone_id = ''
        this.holiday.start_date = ''
        this.holiday.end_date = ''
      },
      exitModal(){
          this.$router.push('/holidays')
      },
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
.form-check-left {
    float: left;
    display: block;
    width: 125px;
}
.form-check-label {
    margin-left: 15px;
}
</style>
