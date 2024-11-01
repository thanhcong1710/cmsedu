<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Thông Tin Khu Vực</strong>
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
                      <label class="control-label">Tên khu vực</label>
                      <input type="text" v-model="zone.name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="zone.status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="test fix-boder">
                    <strong>Danh sách trung tâm áp dụng</strong>
                    <b-modal id="addBranchs" hide-header class="add-branchs" v-model="show">
                      <h5 class="title-modal-fix">Chọn trung tâm áp dụng</h5>
                      <b-container fluid>
                        <b-row class="mb-1">
                          <b-col cols="12">
                            <b-row>
                              <b-col cols="4">
                                <b-form-input type="text" placeholder="Mã trung tâm"> </b-form-input>
                              </b-col>
                              <b-col cols="4">
                                <b-form-input type="text"
                                              placeholder="Tên trung tâm">
                                </b-form-input>
                              </b-col>
                              <b-col cols="2">
                                <div class="add-btn">
                                  <button style="height:36px;" class="btn btn-sm btn-info" @click="searchApplyBranch">Tìm Kiếm</button>
                                </div>
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
                        <b-btn size="sm" class="float-right" variant="warning" @click="saveBranches()">
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
                        <tr v-show="listTemporary" v-for="(branch,index) in listTemporary">
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
                    <button class="apax-btn full edit" type="submit" @click="updateZone"><i class="fa fa-save"></i> Lưu</button>
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
    name: 'Edit-Zone',
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
        zone: {
          name: '',
          status: ''
        },
        zones: [],
        branches: [],
        show: false,
        listSelectedBranchs: [],
        listTemporary: [],
        listRemoveBranch: []
      }
    },
    created(){

      let uri = '/api/zones/'+this.$route.params.id+'/edit';
      u.a().get(uri).then((response) => {
        this.zone = response.data;
      })
      axios.get(`/api/zones/${this.$route.params.id}/get-all-branches`).then(response => {
        this.branches = response.data;
      })

      u.a().get(`/api/zones/${this.$route.params.id}/branches`).then(response =>{
        this.listTemporary = response.data
        this.listSelectedBranchs = response.data
        // console.log(this.branches);
      })
      // let uri = '/api/zones/'+this.$route.params.id;
			// axios.get(uri).then((response) => {
			// 	this.zone = response.data;
			// 	console.log(`this.zone ${JSON.stringify(this.zone)}`)
			// });
    },
    methods: {
      removeBranch(id){
        this.removeZoneIdOfBranch(id)
        var self = this;
        var result = confirm("Bạn có chắc chắn rằng muốn xoá trung tâm này?");
        if (result) {
          self.listSelectedBranchs = self.listSelectedBranchs.filter(function (x) {
            return x.id !== id;
          })
          for (var i in this.listTemporary){
            if (this.listTemporary[i].id == id){
              this.listRemoveBranch.push(this.listTemporary[i])
            }
          }
          self.listTemporary = self.listSelectedBranchs;
        } 
        return false;
      },
      removeZoneIdOfBranch(id){
        u.a().get(`/api/zones/${id}/remove-branch`).then(response => {

        })
      },
      saveBranches(){
        var self = this;
        if (self.listTemporary) {
          self.listSelectedBranchs = self.listTemporary;
          for (var i in this.listTemporary){
            for (var j in this.listRemoveBranch){
              if (this.listTemporary[i].id == this.listRemoveBranch[j].id){
                var id = this.listTemporary[i].id
                self.listRemoveBranch = self.listRemoveBranch.filter(function(x) {
                  return x.id !== id;
                });
              }
            }
          }
        }
        self.show = false
      },
      searchApplyBranch(){

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
      exitModal(){
        this.$router.push('/zones')
      },
      updateZone(){
        let zone = {
          name: this.zone.name,
          status: this.zone.status
        }
        if(zone.name == '' || zone.status == ''){
          alert("Tên khu vực và trạng thái không được để trống")
          return false
        }else {
          let uri = `/api/zones/`+this.$route.params.id;
          // const zone_id = this.$route.params.id
          u.a().put(uri, this.zone).then((response) => {
            // this.$router.push('/zones')
            this.html.modal.display = true
            this.html.modal.message = "Sửa vùng thành công"
          })
          for(var i in this.listTemporary){
            this.listTemporary[i].zone_id = this.$route.params.id
            u.a().put(`/api/branches/`+this.listTemporary[i].id, this.listTemporary[i]).then(response => {
            })
          }
          // for (var j in this.listRemoveBranch){
          //   this.listRemoveBranch[j].zone_id = null
          //   u.a().put(`/api/branches/`+this.listRemoveBranch[j].id, this.listRemoveBranch[j]).then(response=>{

          //   })
          // }
          console.log('test', this.listTemporary);
          this.$router.push('/zones')
        }
      },
    }
  }
</script>

<style scoped>
  table td i {
    cursor: pointer;
  }
  .apax-form .card-body{
    padding: 15px;
  }
  .main .container-fluid{
    padding: 0px;
  }
</style>
