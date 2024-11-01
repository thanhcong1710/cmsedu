<template>
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mã CRM</label>
                <input type="text" class="form-control" v-model="cl.name">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mã EFFECT</label>
                <input class="form-control" type="text">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mã LMS</label>
                <input class="form-control"  type="text">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Loại Khách Hàng</label>
                <select v-model="cl.status" class="form-control" id="">
                  <option :value="0">Tất cả</option>
                  <option :value="1">Chính thức</option>
                  <option :value="2">Học trải nghiệm</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên học sinh</label>
                <input type="text" class="form-control" v-model="cl.name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Nick name</label>
                <input class="form-control" type="text">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="cl.status" class="form-control" id="">
                  <option :value="1">Hoạt động</option>
                  <option :value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div class="">
            <button class="btn btn-success" @click.prevent="filterByClass">Tìm kiếm</button>
            <button class="btn btn-default" type="reset">Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>Thông tin lớp học</strong>
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Mã CRM</th>
                <th class="width-150">MÃ EFFECT</th>
                <th class="width-150">MÃ LMS</th>
                <th class="width-150">Tên học sinh</th>
                <th class="width-150">Nick name</th>
                <th class="width-150">Gói phí</th>
                <th class="width-150">Loại khách hàng</th>
                <th class="width-150">Ngày bắt đầu</th>
                <th class="width-150">Ngày kết thúc</th>
                <th class="width-150">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <!-- class data -->
              <tr v-for="(cl, index) in students" :key="index">
                <td>{{index+1}}</td>
                <td>{{cl.id}}</td>
                <td>{{cl.accounting_id}}</td>
                <td>{{cl.lms_stu_id}}</td>
                <td>{{cl.name}}</td>
                <td>{{cl.nick}}</td>
                <td>{{cl.tuition_fee}}</td>
                <td>{{cl.type | getTypeName}}</td>
                <td>{{cl.contract[0].start_date}}</td>
                <td>{{cl.contract[0].start_date}}</td>
                <td>{{cl.contract[0].status | getContractName}}</td>
              </tr>
            </tbody>
          </table>
          <div class="text-center">
            <nav aria-label="Page navigation example">
              <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item"><a class="page-link" href="#" @click.prevent="getClasses(pagination.prev_page_url)">Previous</a></li>

                <li class="page-item disabled"><a class="page-link" href="#">Page {{pagination.current_page}} of {{pagination.last_page}}</a></li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item"><a class="page-link" href="#" @click.prevent="getClasses(pagination.next_page_url)">Next</a></li>
              </ul>
            </nav>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import Pagination from '../../../components/Pagination'
  import u from '../../../utilities/utility'

  export default {
     components: {
      appPagination: Pagination
    },
    name: 'Detail-Contract',
    data() {
      return {
        classes: [],
        cl: {
          name: '',
          lms_proc_id: '',
          status: ''
        },
        pagination: {},
        search: '',
        statusColor: '',
        students: []
      }
    },
    methods: {
      getClasses(page_url) {
        let vm = this;
        page_url = page_url || '/api/classes/students/'+this.$route.params.id
        axios.get(page_url)
          .then(response => {
            this.students = response.data.data
            this.pagination = response.data
            vm.makePagination(response.meta, response.links);
          }).catch(e => console.log(e));
      },
      makePagination(meta, links) {
        let pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          next_page_url: data.next_page_url,
          prev_page_url: data.prev_page_url
        }
        this.pagination = pagination;
      },
      deleteClass(id, index) {
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa hồ sơ của lớp học này không?");
        if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/classes/' + id)
            .then(response => {
              this.classes.splice(index, 1);
            })
            .catch(error => {
            });
        }
      },
      filterClass() {
        return this.students.filter((cl) => {
          return cl.name.match(this.search);
          // alert('not finish')
        });
      }
    },
    computed: {
      filterByClass() {
        return this.filterClass();
      }
    },
    created() {
      this.getClasses()
      this.filterClass()
    },
    filters: {
      getContractName(value) {
          if (value == 1) return "Active"
            if (value == 2) return "WithDraw"
             if (value == 0) return "Waiting"
      },
      getTypeName(value) {
        return value == 1 ? "VIP" : "Thường"
      },
      getStatusName(value) {
        return value == 1 ? "Hoạt động" : "Ngừng hoạt động";
      },
      statusColor(cl) {
        return cl == 1 ? "btn-primary" : "btn-danger";
      }
    }
  }
</script>

<style scoped lang="scss">
  .table-header {
    margin-bottom: 5px;
    margin-left: -15px;
  }

  .img img {
    width: 100px;
  }

  a {
    /* color: blue; */
  }

  .cl-btn a {
    color: #fff;
  }
</style>
