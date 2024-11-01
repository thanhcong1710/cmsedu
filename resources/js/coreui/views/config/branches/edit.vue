<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập nhật thông tin đơn vị cơ sở</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
               <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã đơn vị cơ sở</label>
                <input type="text" v-model="branch.hrm_id" class="form-control">
              </div>
            </div>
            <!--<div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã LMS</label>
                <input type="text" v-model="branch.brch_id" class="form-control">
              </div>
            </div>-->
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã Cyber</label>
               <input type="text" v-model="branch.accounting_id" class="form-control" :readonly="true">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên trung tâm</label>
                <input type="text" v-model="branch.name" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Ngày khai trương</label>
                <input type="date" v-model="branch.opened_date" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Khu Vực</label>
                <select v-model="branch.zone_id" class="form-control">
                  <option value="" disabled>Chọn Khu Vực</option>
                  <option :value="zone.id" v-for="(zone, index) in zones">{{ zone.name }}</option>
                </select>
              </div>
            </div>
            <!-- <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Vùng</label>
                <select v-model="branch.region_id" class="form-control">
                  <option value="" disabled>Chọn Vùng</option>
                  <option :value="region.id" v-for="(region, index) in regions">{{ region.name }}</option>
                </select>
              </div>
            </div> -->
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" v-model="branch.email" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Hot line</label>
                <input type="text" v-model="branch.phone" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="branch.status" class="form-control" id="">
                  <option value="" disabled>Chọn trạng thái</option>
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
                    <button class="apax-btn full edit" type="submit" @click="updateBranch"><i class="fa fa-save"></i> Lưu thay đổi</button>
                    <router-link class="apax-btn full warning" :to="'/branches'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>

          <b-modal 
              :title="html.modal.title" 
              :class="html.modal.class" size="sm" 
              v-model="html.modal.display" 
              @ok="action.modal" 
              ok-variant="primary"
          >
           <div v-html="html.modal.message"></div>
        </b-modal>




        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Edit-Branch',
    data() {
      return {
        action: {
          modal: () => this.exitForm()
        },
        html: {
          title: 'Thông Báo',
          loading: {
            content: 'Tải dữ liệu',
            action: false
          },
          modal: {
            class: 'modal-success',
            title: 'Thông Báo',
            display: false,
            done: false,
            message: ''
          }
        },
        branch: {
          name: '',
          hrm_id: '',
          status: '',
          accounting_id: '',
          brch_id: '',
          email:'',
          phone:'',
        },
        regions: [],
        region: '',
        zones: [],
        zone: ''
      }
    },
    created(){
      this.getAllRegionList()
      this.getAllZoneList()
      let uri = '/api/branches/'+this.$route.params.id;
      axios.get(uri).then((response) => {
        this.branch = response.data;
        console.log(`this.branch ${JSON.stringify(this.branch)}`)
      });
    },
    methods: {
        updateBranch(){
          let branch = {
            id: this.branch.id,
            name: this.branch.name,
            hrm_id: this.branch.hrm_id,
            status: this.branch.status,
            accounting_id: this.branch.accounting_id,
            lms_id: this.branch.brch_id,
            opened_date: this.branch.opened_date,
            zone_id: this.branch.zone_id,
            region_id: this.branch.region_id,
            phone:this.branch.phone,
            email:this.branch.email,
          }
          if(!branch.name|| branch.name == ''){
            alert ("Tên đơn vị không để trống !")
            return false
          }
          else {
            this.storeUpdateBranch(branch)
          }
        },
        storeUpdateBranch(branch){
          const data = branch
          u.a().put(`/api/branches/${data.id}`, data).then(response => {
            if(response.data){
              this.html.modal.message = "Cập nhập thành công đơn vị cơ sở !"
              this.html.modal.display = true
              // this.$router.push(`/branches`);
            };
          });
        },
        getAllRegionList(){
          u.a().get(`/api/get-all/regions/get-all-regions-list`).then(response => {
            this.regions = response.data
          })
        },
        getAllZoneList(){
          u.a().get(`/api/get-all/zones/get-all-zones-list`).then(response => {
            this.zones = response.data
          })
        },
        exitForm(){
          this.$router.push('/branches')
        }
    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
