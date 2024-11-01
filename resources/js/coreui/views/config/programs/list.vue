<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-card header-tag="header">
        <div slot="header">
          <i class="fa fa-reddit-alien"></i> <b class="uppercase">Chương Trình Học</b>
        </div>
        <div class="panel">
          <div class="row">
            <div class="col-md-4">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-list-alt"></i> <b class="uppercase">Danh Sách Sản Phẩm / Gói phí</b>
                </div>
                <div class="panel">
                  <div class="col-md-12 apax-form-block" v-show="branches.length">
                    <label class="control-label apax-title">Danh Sách Trung Tâm</label>
                    <select @change="selectBranch" class="form-control input-sm" v-model="branch">
                      <option value="0">
                        Chọn trung tâm
                      </option>
                      <option :value="branch.id" v-for="(branch,ind) in branches" :key="ind">
                        {{branch.name}}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-12 apax-form-block">
                    <label class="control-label apax-title">Các Kỳ Học Hiện Tại</label>
                    <select @change="getProgram" class="form-control input-sm" v-model="semester">
                      <option value="0">
                        Chọn kỳ học
                      </option>
                      <option :value="semester.id" v-for="(semester,index) in semesters" :key="index">
                        <i class="fa fa-graduation-cap"></i> {{semester.name}}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-12 apax-form-block" v-show="exsemesters.length">
                    <label class="control-label apax-title">Các Kỳ Học Cũ</label>
                    <ul class="form-control apax-list select-able scroll">
                      <li v-for="(semester, index) in exsemesters" @click="getProgram(semester.id)" :key="index" :id="`semester-id${semester.id}`" :class="`list item-${index}`">
                        <i class="fa fa-graduation-cap"></i> {{semester.name}}
                      </li>
                    </ul>
                  </div>
                  <div class="col-md-12 apax-form-block" v-show="programs.length">
                    <label class="control-label apax-title">Chọn Chương Trình Học</label>
                    <div class="apax-tree">
                      <tree :data="programs"
                          text-field-name="name"
                          allow-batch
                          @item-click="selectProgram">
                      </tree>
                    </div>
                  </div>
                </div>
              </b-card>              
            </div>
            <div class="col-md-8">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-leanpub"></i> <b class="uppercase">Thông tin thiết lập chương trình học</b>
                </div>
                <div class="panel form-fields">
                  <div class="row rline">
                    <span class="col-md-4 tline title-bold txt-right">Trung tâm:</span>
                    <span class="col-md-8 fline"><input type="text" :value="selectedBranch.name" readonly /></span>
                  </div>
                  <div class="row rline">
                    <span class="col-md-4 tline title-bold txt-right">Kỳ học:</span>
                    <span class="col-md-8 fline"><input type="text" :value="selectSemester.name" readonly /></span>
                  </div>
                  <div v-if="add_program">
                    <div class="row rline" style="line-height:18px;">
                      <span class="col-md-4 tline title-bold txt-right">Chương trình cha:</span>
                      <span class="col-md-8 fline">
                        <select class="form-control" v-model="program_parent">
                          <option value="0" selected>Lựa chọn chương trình cha</option>
                          <option :value="program.id" v-for="(program, index) in programs" :key="index">{{program.name}}</option>
                        </select>
                        <i>Nếu muốn thêm mới chương trình cha vui lòng bỏ qua không nhập/chọn thông tin gì tại trường "Chương trình cha" và nhập tên chương trình cha cần thêm tại trường "Chương trình con" </i>
                      </span>
                    </div>
                    <div class="row rline">
                      <span class="col-md-4 tline title-bold txt-right">Chương trình con:</span>
                      <span class="col-md-8 fline"><input type="text" class="last-line" v-model="program_name" /></span>
                    </div>
                  </div>
                  <div v-else>
                    <div class="row rline">
                      <span class="col-md-4 tline title-bold txt-right">Chương trình cha:</span>
                      <span class="col-md-8 fline"><input type="text" :value="parentProgram.name" readonly /></span>
                    </div>
                    <div class="row rline">
                      <span class="col-md-4 tline title-bold txt-right">Chương trình con:</span>
                      <span class="col-md-8 fline"><input type="text" class="last-line" :value="childProgram.name" readonly /></span>
                    </div>
                  </div>
                  <div class="row rline form-group">
                    <span class="col-md-4 tline title-bold txt-right">Sản phẩm:</span>
                    <span class="col-md-8">
                      <select class="form-control" v-model="selectedProductID" @change="selectProduct" :disabled="disableProductID">
                        <option value="" selected>Lựa chọn sản phẩn</option>
                        <option :value="product.id" v-for="(product, index) in products" :key="index">{{product.name}}</option>
                      </select>
                    </span>
                  </div>
                  <div class="row rline form-group">
                    <span class="col-md-4 tline title-bold txt-right">Mã quy chiếu:</span>
                    <span class="col-md-8">
                      <select class="form-control" v-model="selectedProgramCodeID" :disabled="disableProgramCodeID">
                        <option value="">Chọn mã quy chiếu</option>
                        <option :value="cod.id" v-for="(cod, index) in codes" :key="index">{{cod.code}} ({{cod.description}})</option>
                      </select>
                    </span>
                  </div>
                  <div class="row rline form-group">
                    <span class="col-md-4 tline title-bold txt-right">Trạng thái:</span>
                    <span class="col-md-8">
                      <select class="form-control" v-model="selectedStatus" :disabled="disableStatus">
                        <option value="">Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="panel-footer">
                  <abt v-if="add_program"
                    :markup="'warning'"
                    :icon="'fa fa-plus'"
                    label="Thêm mới"
                    :disabled="![686868, 999999999, 56,58,37].includes(parseInt(session.user.role_id))"
                    title="Thêm mới Chương Trình Học"
                    :onClick="addProgram"
                    >
                  </abt>
                  <abt v-else
                    :markup="'success'"
                    :icon="'fa fa-save'"
                    label="Cập Nhật"
                    title="Cập Nhật Thông Tin Chương Trình Học"
                    :disabled="disableSave ||![686868, 999999999, 56,58,37].includes(parseInt(session.user.role_id))"
                    :onClick="updateProgram"
                    >
                  </abt>
                </div>
              </b-card>
            </div>
          </div>
        </div>
      </b-card>
    </div>
    <b-modal title="Thông Báo" :class="classModal" size="lg" v-model="modal" @ok="modal=false" ok-variant="primary">
      <div v-html="message"></div>
    </b-modal>
  </div>
</template>

<script>
    import axios from 'axios'
    import tree from 'vue-jstree'
    import Vue from 'vue'
    import abt from '../../../components/Button'
    import u from '../../../utilities/utility'

    export default {
      name: 'Register-Add',
      components: {
        abt,
        tree
      },
      data () {
        return {
          enrolments: [],
          classModal: '',
          message: '',
          term_program_product: {
            id: '',
            program_code_id: '',
            product_id: '',
            program_id: '',
            status: ''
          },
          modal: false,
          disableStatus: true,
          disableProductID: true,
          disableProgramCodeID: true,
          selectedStatus: '',
          selectedProductID: '',
          selectedProgramCodeID: '',
          disableSave: true,
          show: false,
          selectedBranch: {},
          selectSemester: {},
          showclass: false,
          type: true,
          title: 'Vue Tree Demo',
          branches: [],
          branch: 0,
          semesters: [],
          exsemesters: [],
          teachers: [],
          programs: [],
          semester: 0,
          classes: {},
          options: [],
          parentProgram: {},
          childProgram: {},
          products: [],
          product: '',
          codes: [],
          code: '',
          add_program: false,
          program_parent:0,
          program_name:'',
          session: u.session(),
        }
      },
      filters:{
        typeToName: function(value){
          if (value==0) return 'Thường';
          else return 'Vip'
        }
      },
      created(){
        if (u.authorized()) {
          u.a().get('/api/branch/role').then(response => {
              this.branches = response.data
          })
        } else {
          const session = u.session()          
          this.branch = parseInt(session.user.branch_id, 10)
          u.a().get(`api/branches/${this.branch}`).then(response =>{
            this.selectedBranch = response.data;
          })
        }
        u.a().get(`/api/all/products`).then(response =>{
          this.products = response.data
        })
        u.g('/api/semesters-listing/obsolete').then(response => {
            this.exsemesters = response
        })
        u.g('/api/semesters-listing/current').then(response => {
            this.semesters = response
        })
      },
      methods: {
        selectProgram (selected_program) {
          this.add_program = false;
          if (selected_program.data.parent_id != 0){
            u.a().get(`/api/programs/${selected_program.data.parent_id}`).then(response =>{
              this.parentProgram = response.data[0];
            })
            u.g(`/api/programs/${selected_program.data.id}/term`).then(response =>{
              if (response) {
                this.codes = response.program_codes
                this.term_program_product = response
                this.selectedStatus = response.status
                this.selectedProductID = response.product_id
                this.selectedProgramCodeID = response.program_code_id
              } else {
                this.selectedStatus = ''
                this.selectedProductID = ''
                this.selectedProgramCodeID = ''
              }
              if (selected_program.data.obsolete === 0) {
                this.disableStatus = false
                this.disableProductID = false
                this.disableProgramCodeID = false
                this.disableSave = false
              } else {
                this.disableStatus = true
                this.disableProductID = true
                this.disableProgramCodeID = true
                this.disableSave = true
              }
            })
            this.childProgram = selected_program.data;
          } else {
            this.parentProgram = selected_program.data
            this.childProgram = ''
            u.g(`/api/programs/${selected_program.data.id}/term`).then(response =>{
              if (response && !Array.isArray(response)) {
                this.codes = response.program_codes
                this.term_program_product = response
                this.selectedStatus = response.status
                this.selectedProductID = response.product_id
                this.selectedProgramCodeID = response.program_code_id
              } else {
                this.selectedStatus = ''
                this.selectedProductID = ''
                this.selectedProgramCodeID = ''
                this.term_program_product.id = 0
                this.term_program_product.program_id = selected_program.data.id
              }
              if (selected_program.data.obsolete === 0) {
                this.disableStatus = false
                this.disableProductID = false
                this.disableProgramCodeID = false
                this.disableSave = false
              } else {
                this.disableStatus = true
                this.disableProductID = true
                this.disableProgramCodeID = true
                this.disableSave = true
              }
            })
          }
        },
        selectProduct(){
          u.g(`/api/products/${this.selectedProductID}/program_codes`).then(res =>{
            this.selectedProgramCodeID = ''
            this.codes = res
          })
        },
        selectChild(val, parent_id){
          u.a().get(`/api/programs/${parent_id}`).then(response =>{
            this.parentProgram = response.data[0]
          })
          u.a().get(`/api/programs/${val}`).then(response =>{
            this.childProgram = response.data[0]
          })
          u.a().get(`/api/programs/${val}/term`).then(response =>{
            this.term_program_product = response.data[0]
            u.a().get(`/api/products/${this.term_program_product.product_id}/program_codes`).then(res =>{
              this.codes = res.data
            })
            
          })

        },
        selectBranch(){
          u.a().get(`api/branches/${this.branch}`).then(response =>{
            this.selectedBranch = response.data;
          })
          if (this.semester != ''){
            u.g(`/api/program/child/${this.branch}/semester/${this.semester}`).then(response =>{
              if (response && response.length) {
                this.programs = response
              } else {
                this.programs = []
              }
            })
          }
        },
        getProgram(id = 0){
          if (this.branch > 0) {
            const semester_id = id > 0 ? id : this.semester
            $.when($('ul.apax-list li.active').removeClass('active')).then($(`#semester-id${semester_id}`).addClass('active'))            
            u.a().get(`api/semesters/${semester_id}`).then(response =>{
              this.selectSemester = response.data
              this.showAddProgram();
            })
            u.g(`/api/program/child/${this.branch}/semester/${semester_id}`).then(response =>{
              if (response && response.length) {
                this.programs = response
              } else {
                this.programs = []
              }
            })
          } else {
            this.message = "Xin vui lòng chọn một trung tâm trước."
            this.classModal = "modal-primary"
            this.modal = true
          }
        },
        updateProgram(){
          let msg = ""
          let validate = true
          if (this.selectedStatus === ''){
            validate = false
            msg += "(*) Trạng thái kích hoạt là bắt buộc! <br/>"
          } else {
            this.term_program_product.status = this.selectedStatus
          }
          if (parseInt(this.selectedProductID, 10) === 0){
            validate = false
            msg += "(*) Gói sản phẩm là bắt buộc! <br/>"
          } else {
            this.term_program_product.product_id = this.selectedProductID
          }
          if (parseInt(this.selectedProgramCodeID, 10) === 0){
            validate = false
            msg += "(*) Mã quy chiếu là bắt buộc! <br/>"
          } else {
            this.term_program_product.program_code_id = this.selectedProgramCodeID
          }
          if (!validate){
            msg = `Thông tin chương trình học chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
            this.classModal='modal-primary'
            this.message = msg
            this.modal = true
          } else {
            this.term_program_product.program_id = this.childProgram.id ? this.childProgram.id : this.term_program_product.program_id
            if(this.term_program_product.id == undefined){
                const program_product = {
                  id: 0,
                  program_id: this.term_program_product.program_id,
                  product_id: this.selectedProductID,
                  program_code_id: this.selectedProgramCodeID,
                  status: this.selectedStatus,
              }
              this.term_program_product = program_product;
            }
            u.a().put(`/api/termProgramProducts/${this.term_program_product.id}`, this.term_program_product).then(response =>{
              this.message = "Thông tin chương trình học đã được cập nhật thành công."
              this.classModal = "modal-success"
              this.modal = true
            })
          }
        },
        showAddProgram(){
            this.disableSave = false
            this.disableStatus = false
            this.disableProductID = false
            this.disableProgramCodeID = false
            this.add_program = true;
            this.selectedProductID='';
            this.selectedProgramCodeID='';
            this.selectedStatus='';
            this.program_name = '';
            this.program_parent = 0;
        },
        addProgram(){
          const program = {
                branch_id: this.branch,
                semester_id: this.semester,
                program_parent: this.program_parent,
                program_name: this.program_name,
                product_id: this.selectedProductID,
                program_code_id: this.selectedProgramCodeID,
                status: this.selectedStatus,
            }
            if(program.program_name == ''){
                alert("Tên chương trình không để trống")
                return false
            }else if(program.product_id == ''){
                alert("Sản phẩm không để trống")
                return false
            }else if(program.program_code_id == ''){
                alert("Mã quy chiếu không để trống")
                return false
            }else if(program.status == ''){
                alert("Trạng thái không để trống")
                return false
            }
            else {
                u.a().put(`/api/program/add-ielts/${this.branch}`, program).then(response => {
                    this.message = "Thêm mới chương trình thành công!"
                    this.modal = true
                    this.getProgram(this.semester);
                    this.showAddProgram();
                })
            }
        },
      }
    }
</script>

<style scoped>
  .row {
    line-height: 30px;
  }
  .add-btn {
    float: right;
    margin: 10px 0;
    min-width: 120px;
  }

  .info-register {
    border: 1px solid #d7d6d6;
  }
  label.apax-title {
    margin:5px 0 3px;
    font-weight: 500;
    color:#333;
  }
  ul.apax-list.select-able li.active {
    background: #414c58;
    font-weight: 500;
    color: #FFF;
    text-shadow: 0 1px 1px #111;
  }  
  .status-active {
    border: 1px solid #becdbf;
    border-radius: 4px;
    padding: 3px;
    background-color: #2e9fff;
    color: #fff;
  }
  .apax-tree {
    padding: 10px;
    border-radius: 1px;
    border: 1px solid rgb(199, 213, 228);
  }
  .title-search {
    text-align: right;
    padding: 5px 0 0 0;
    font-size: 13px;
    font-weight: 500;
  }
  .apax-form-block {
    float:left;
    width: 100%;
    position: relative;
    overflow: hidden;
  }
  .txt-right {
    text-align: right
  }
  .tline {
    padding: 3px 0px;
  }
  .title-bold {
    font-weight: bold;
  }
  .fline input {
    border: none;
    border-bottom: 1px solid rgb(224, 224, 224);
    color: #555;
    padding: 0;
    height: 36px;
    width: 100%;
  }
  .form-fields .rline input.last-line {
    margin: 0 0 17px 0;
  }
  .col-4 {
    padding: 0px 0px 8px 8px;
  }
  #mytree {
    cursor: pointer;
  }

  #mytree {
    font-size: 14px;
    height: 53px;
  }
</style>

