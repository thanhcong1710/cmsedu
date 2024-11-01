<template>
  <div class="tab-student-update apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Hồ sơ lưu trữ</b>        
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <input type="file" id="file_upload" ref="file" multiple="multiple">
          <button class="btn btn-success" @click="uploadFileKT">Upload file</button>
          <table class="table table-striped table-bordered apax-table">
            <thead>
              <tr class="text-sm">
                <th>Tên File</th>
                <th class="width-100" v-if="show_delete">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list_file" :key="index">
                <td><a :href="item.link" target="blank">{{item.name}}</a></td>
                <td v-if="show_delete">
                  <span class="apax-btn remove" @click="removeFile(item.file)" ><i title="Nhấp vào để xóa bỏ" class="fa fa-remove"></i></span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
import Button from '../../components/Button.vue'
import u from '../../utilities/utility'
  
  export default {
    data() {
      return {
        file:[],
        list_file:[],
        show_delete:false
      }
    },
    created() {
      if(u.session().user.role_id == 999999999){
        this.show_delete= true
      }
      this.loadFileKT()
    },
    methods: {
      uploadFileKT(){
        let formData = new FormData();
        for( var i = 0; i < this.$refs.file.files.length; i++ ){
            let file = this.$refs.file.files[i];
            formData.append('files[' + i + ']', file);
        }
        u.p(`/api/students/upload_file/${this.$route.params.id}`, formData).then(response => {
          this.loadFileKT();
          $('#file_upload').val('');
        })
      },
      loadFileKT(){
        u.g(`/api/students/load_file/${this.$route.params.id}`).then(response => {
          this.list_file=response
        })
      },
      removeFile(file_name){
         let formData ={
            file_name:file_name
         }
        u.p(`/api/students/remove_file/${this.$route.params.id}`, formData).then(response => {
          this.loadFileKT();
        })
      }
    },
  }
</script>

<style scoped>
  
</style>
