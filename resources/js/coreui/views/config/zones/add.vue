<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Khu Vực Mới</strong>
              <div class="card-actions">
                <a href="skype:live:c7a5d68adf8682ff?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Tên khu vực</label>
                      <input type="text" v-model="zone.name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="zone.status">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
                <div class="test fix-boder">
                  <strong>Danh sách trung tâm áp dụng</strong>
                    <!-- the modal that apply branches for zone -->
                    <b-modal id="addBranchs" hide-header class="add-branchs" v-model="show">
                      <h5 class="title-modal-fix">Chọn trung tâm áp dụng</h5>
                      <b-container fluid>
                        <b-row class="mb-1">
                          <b-col cols="12">
                            <b-row>
                              <b-col cols="4">
                                <b-form-input type="text" placeholder="Mã trung tâm">
                                </b-form-input>
                              </b-col>
                              <b-col cols="4">
                                <b-form-input type="text" placeholder="Tên trung tâm">
                                </b-form-input>
                              </b-col>
                              <b-col cols="2">
                                  <button class="btn btn-sm btn-info" style="height: 36px;">Tìm Kiếm</button>
                              </b-col>
                            </b-row>
                          </b-col>
                        </b-row>
                        <div class="scroll-tb">
                          <table class="table table-bordered apax-table" id="table_list">
                            <thead>
                              <tr>
                                <th style="width:3%">
                                  <input id="iCheck" @click="checkAll('#iCheck')" name="CheckAll" type="checkbox" />
                                </th>
                                <th> STT </th>
                                <th> Mã </th>
                                <th> Trung tâm </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(branch,index) in branches" :key="index">
                                <td>
                                  <input v-model="listTemporary" :value="branch" :id="'branch_'+branch.id" type="checkbox" />
                                </td>
                                <td>{{index+1}}</td>
                                <td>{{branch.hrm_id}}</td>
                                <td>{{branch.name}}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </b-container>
                      <div slot="modal-footer" class="w-100">
                        <b-btn size="sm" class="float-right" variant="primary" @click="cancelBranchs()">
                          Quay lại
                        </b-btn>
                        <b-btn size="sm" class="float-right" variant="warning" @click="saveBranchs()">
                          Chọn
                        </b-btn>
                      </div>
                    </b-modal>
                  </div><br />
                  <div class="scroll-tb">
                    <table class="table table-bordered apax-table" id="table_list">
                      <thead>
                        <tr>
                          <th> STT </th>
                          <th> Mã </th>
                          <th> Trung tâm </th>
                          <th> Action </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-show="listSelectedBranchs" v-for="(branch,index) in listSelectedBranchs">
                          <td>{{index+1}}</td>
                          <td>{{branch.hrm_id}}</td>
                          <td>{{branch.name}}</td>
                          <td><i @click="removeBranch(branch.id)" class="fa fa-trash remove-branch"></i></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <b-btn @click="addBranchs" class="apax-btn full reset"><i class="fa fa-plus"></i> Thêm Trung Tâm</b-btn>
                    <button class="apax-btn full edit" type="submit" @click.prevent="validateAddZone"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" @click="reset"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/zones'">
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
    name: 'Add-Zone',
    data() {
      return {
        html: {
          modal: {
            title: 'Thông Báo',
            class: 'modal-success',
            message: '',
            display:  false
          }
        },
        action: {
              modal: () => this.exitModal()
        },
        show: false,
        zone: {
          name: '',
          status: ''
        },
        branches: [],
        listSelectedBranchs: [],
        listTemporary: []
      }
    },
    created(){
      axios.get(`/api/reports/branches`).then(response => {
        this.branches = response.data;
      })
    },
    methods: {
      removeBranch(id){
        var self = this;
        var result = confirm("Bạn có chắc chắn rằng muốn bỏ chọn trung tâm này?");
        if (result) {
          self.listSelectedBranchs = self.listSelectedBranchs.filter(function (x) {
            return x.id !== id;
          })
          self.listTemporary = self.listSelectedBranchs;
        } return false;
      },
      saveBranchs(){
        var self = this;
        if (self.listTemporary) {
          self.listSelectedBranchs = self.listTemporary;
        }
        self.show = false
      },
      cancelBranchs(){
        var self = this;
        self.show = false
        self.listTemporary = self.listSelectedBranchs;
      },
      checkAll(id){
        var p = {}
        if ($(id).prop("checked")) {
          if (this.branches) {
            $.each(this.branches, function (index, val) {
              $("#branch_" + val.id).prop("checked", true)
              
            })
          }
          this.listTemporary = this.branches;
        } else {
          $.each(this.branches, function (index, val) {
            $("#branch_" + val.id).prop("checked", false)
            
          })
          this.listTemporary = []
        }
      },
      addBranchs(){
        var self = this;
        self.show = true;
        if (self.listSelectedBranchs) {
          $.each(self.listSelectedBranchs, function (index, val) {
            $("#branch_" + val.id).prop("checked", true)
          })
        }
      },
      validateAddZone(){
        if(!this.zone.name || this.zone.status == ''){
          alert('Tên vùng và trạng thái không được để trống')
          return false
        } else {
          this.checkZoneExist()
        }
      },
      checkZoneExist(){
        const zone_name = this.zone.name
        axios.get(`/api/zones/${zone_name}/check-zone-exist`).then(response => {
          const exist = response.data;
          if(exist == 1){
            alert('Tên vùng đã tồn tại');
            return false
          } else {
            this.addZone();
            
          }
        })
      },
      reset(){
        this.zone.name = ''
        this.zone.status = ''
      },
      exitModal(){
        this.$router.push('/zones')
      },
      addZone() {
        if (this.zone.name && this.zone.status){
          u.a().post(`/api/zones`, this.zone).then(response =>{
            for(var i in this.listTemporary){
              this.listTemporary[i].zone_id = response.data.id;
              u.a().put(`/api/branches/`+this.listTemporary[i].id, this.listTemporary[i]).then(response => {

              })
            }
          })
        }
        // this.$router.push('/zones')
        this.html.modal.display = true
        this.html.modal.message = "Thêm mới vùng số thành công"
      },
    }
  }
</script>

<style scoped>
  .test {
    margin-top: 10px;
  }

    .test .padding-fix {
      padding: 17px;
    }

  .title-bold {
    font-weight: bold;
  }


  .info-register {
    border: 1px solid #d7d6d6;
    padding: 10px;
  }

  .status-active {
    border: 1px solid #becdbf;
    border-radius: 4px;
    padding: 3px;
    background-color: #2e9fff;
    color: #fff;
  }

  .title-search {
    text-align: right;
    padding: 5px 0 0 0;
    font-size: 13px;
    font-weight: 500;
  }


  #class_infor {
    padding: 10px 10px;
  }

  #table_list th, td {
    font-size: 0.8rem;
  }


  .txt-right {
    text-align: right
  }

  .row-boss {
    padding: 20px;
  }

  .fix-form {
    width: 30% !important;
  }

  .btn-gray {
    background-color: #6a6a6a;
  }

  .scroll-tb {
    max-height: 350px;
    overflow-y: auto;
  }

  .remove-branch {
    cursor: pointer;
  }

    .remove-branch:hover {
      background-color: #b4b1b1;
      border: 2px solid #b4b1b1;
      border-radius: 3px;
    }

  .title-modal-fix {
    margin-bottom: 20px;
    margin-top: 5px
  }

  .fixed-input {
    font-size: 14px;
    color: #737373;
  }
  #tuition span{
  cursor: pointer;
  }
  .apax-form .card-body{
    padding: 15px;
  }
  .main .container-fluid{
    padding: 0px;
  }
</style>
