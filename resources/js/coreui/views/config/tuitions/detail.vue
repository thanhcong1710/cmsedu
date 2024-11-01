<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Danh Sách</strong>
              <router-link class="btn add-btn btn-sm btn-danger" :to="'/tuitions'">
                Quay lại
              </router-link>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-md-12" id="class_tuitions">
                  <div class="col-md-12 info-register fix-boder" id="info">
                    <div class="row">
                      <div class="col-md-3 title-bold">
                        Chi TiếtGói Phí
                      </div>
                      <div class="col-md-3">

                      </div>
                      <div class="col-md-3">
                      </div>
                      <div class="col-md-3 txt-right">
                        <button class="btn btn-warning btn-sm">Quay Lại</button>
                      </div>
                    </div>
                    <div class="row row-boss">
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Sản phẩm:</span>
                          <span class="col-md-8"><input class="form-control" readonly :value="selectedProduct" /></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Tên gói phí:</span>
                          <span class="col-md-8"><input class="form-control" type="text" readonly /></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Số buổi:</span>
                          <span class="col-md-8"><input type="number" class="form-control" v-model="tuition.session" readonly /></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Giá niêm yết:</span>
                          <span class="col-md-8"><input type="number" class="form-control" v-model="tuition.session" readonly /></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Giá thực thu:</span>
                          <span class="col-md-8"><input type="text" class="form-control" readonly></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Ngày bắt đầu hiệu lực:</span>
                          <span class="col-md-8"><input type="date" class="form-control" readonly></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Ngày hết hiệu lực:</span>
                          <span class="col-md-8"><input type="date" class="form-control" readonly></span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <span class="col-md-4 title-bold txt-right">Loại</span>
                          <span class="col-md-8">
                            <select class="form-control fix-form" readonly>
                              <option value="0">Đóng phí 1 lần</option>
                              <option value="1">Đóng phí nhiều</option>
                            </select>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Modal add -->
                  <!--<div class="test fix-boder">
                    <div class="row">
                      <span class=" col-md-4 title-bold padding-fix">Danh sách trung tâm áp dụng</span>
                      <span class="col-md-8"><b-btn @click="addBranchs" class="btn add-btn btn-gray">Thêm Trung Tâm</b-btn></span>
                    </div>

                    <b-modal id="addBranchs" hide-header class="add-branchs" v-model="show">
                      <h5 class="title-modal-fix">Chọn trung tâm áp dụng</h5>
                      <b-container fluid>
                        <b-row class="mb-1">
                          <b-col cols="12">
                            <b-row>
                              <b-col cols="1" class="title-search">Mã</b-col>
                              <b-col cols="4">
                                <b-form-input type="text"
                                              placeholder="Please write text">
                                </b-form-input>
                              </b-col>
                              <b-col cols="1" class="title-search">Tên</b-col>
                              <b-col cols="4">
                                <b-form-input type="text"
                                              placeholder="Please write text">
                                </b-form-input>
                              </b-col>
                            </b-row>
                            <b-row>
                              <b-col cols="7">
                                <div class="add-btn">
                                  <button class="btn btn-sm btn-info">Tìm Kiếm</button>
                                </div>
                              </b-col>
                            </b-row>

                          </b-col>
                        </b-row>
                        <div class="scroll-tb">
                          <table class="table table-bordered apax-table" id="table_list">
                            <thead>
                              <tr>
                                <th>
                                </th>
                                <th>
                                  STT
                                </th>
                                <th>
                                  Mã
                                </th>
                                <th>
                                  Trung tâm
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(branch,index) in branches">
                                <td>
                                  <input v-model="listTemporary" :value="branch" :id="'branch_'+branch.id" type="checkbox" />
                                </td>
                                <td>
                                  {{index+1}}
                                </td>
                                <td>
                                  {{branch.hrm_id}}
                                </td>
                                <td>
                                  {{branch.name}}
                                </td>
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
                  </div>-->
                  <!-- End modal add -->
                  <br />
                  <div class="scroll-tb">
                    <table class="table table-bordered apax-table" id="table_list">
                      <thead>
                        <tr>
                          <th style="width:3%">
                          </th>
                          <th style="width:10%">
                            STT
                          </th>
                          <th>
                            Mã
                          </th>
                          <th>
                            Trung tâm
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(branch,index) in branches">
                          <td>
                            <input v-model="listTemporary" :value="branch" :id="'branch_'+branch.id" type="checkbox" disabled />
                          </td>
                          <td>
                            {{index+1}}
                          </td>
                          <td>
                            {{branch.hrm_id}}
                          </td>
                          <td>
                            {{branch.name}}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Detail-Tuition',
    data() {
      return {
        //Set default checked

        show: false,
        selectedProduct: null,
        product: '',
        products: [],
        status: '0',
        area: '0',
        zones: [],
        zone: '',
        tuitions: [],
        branches: [],
        tuition: {
          name: '',
          branch_id: '',
          product_id: '',
          session: '',
          price: '',
          receivable: '',
          available_date: '',
          status: '',
          zone_id: '',
          expired_date: ''
        },
        listTemporary: [],
        listSelectedBranchs: [],
      }
    },
    created() {

      axios.get(`/api/branches`).then(response => {
        this.branches = response.data;
      })
      axios.get(`/api/products`).then(response => {
        this.products = response.data.data
      })
      axios.get(`/api/zones`).then(response => {
        this.zones = response.data.data
      })
      axios.get('/api/branches').then(response => {
        this.branches = response.data;
      });
    },
    methods: {
      addBranchs() {
        var self = this;
        self.show = true;
        if (self.listSelectedBranchs) {
          $.each(self.listSelectedBranchs, function (index, val) {
            $("#branch_" + val.id).prop("checked", true)
          })
        }
      },
      saveBranchs() {
        var self = this;
        if (self.listTemporary) {
          self.listSelectedBranchs = self.listTemporary;
        }
        self.show = false
      },
      cancelBranchs() {
        var self = this;
        self.show = false
        self.listTemporary = self.listSelectedBranchs;
      },
      removeBranch(id) {
        var self = this;
        var result = confirm("Bạn có chắc chắn rằng muốn xoá trung tâm này?");
        if (result) {
          self.listSelectedBranchs = self.listSelectedBranchs.filter(function (x) {
            return x.id !== id;
          })
          self.listTemporary = self.listSelectedBranchs;
        } return false;
      }
    },
    watch: {
      'tuition.product_id': function (val) {
        var self = this;
        self.product = self.products.filter(function (x) {
          return self.tuition.product_id === x.id;
        })
        self.selectedProduct = self.product[0].name;
      }
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

  .add-btn {
    float: right;
    margin: 10px 0;
  }

  .info-register {
    border: 1px solid #d7d6d6;
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

  .col-4 {
    padding: 0px 0px 8px 8px;
  }

  #class_infor {
    padding: 10px 10px;
  }

  #table_list th, td {
    font-size: 0.8rem;
  }

  .fix-boder {
    border: 1px solid #223b54;
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
</style>

