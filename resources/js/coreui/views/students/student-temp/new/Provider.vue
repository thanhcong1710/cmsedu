<template>
  <div>
    <slot v-bind="{ state, actions }"/>
  </div>
</template>

<script>
  import u from '../../../../utilities/utility'
  import {getDate} from "../../../reports/common/utils";
  import MixinList from '../mixin/list'
  export default {
    mixins: [MixinList],
    data() {
      return {student: null, uid:0, ecId: null}
    },
    computed: {
      state: {
        get() {
          return {student: this.student,uid: u.session().user.id, ecId:this.ecId}
        },
        set({province}) {

        },
      },
      actions() {
        return {save: this.save, deleteStudent : this.deleteStudent}
      },
    },

    mounted() {
      this.ecId =  (u.session().user.title === "EC") ? 1 : 0
      if(this.$route.params.id){
        this.getStudent(this.$route.params.id);
      }
    },
    methods: {
      getStudent(id){
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/student-temp/${id}?${new Date().getTime()}`).then((res) => {
          this.student = res.data;
          u.apax.$emit('apaxLoading', false)
        })
      },
      save(data) {
        const params = {
          ...data,
          birthday: getDate(data.birthday),
          gud_date_of_birth1: getDate(data.gud_date_of_birth1),
          gud_date_of_birth2: getDate(data.gud_date_of_birth2),
          id: this.student && this.student.id,
        }

        if (u.session().user.title === "EC")
          params.branch_id = u.session().user.branch_id

       if(!this.validate(params)){
         return false;
       } 
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/students/check-phone-parent/${params.gud_mobile1}`).then(response => {
          console.log(response)
          u.apax.$emit('apaxLoading', false)
          if (parseInt(response.status) == 1) {
            let message = response.message;
            this.$notify({
              group: 'apax-atc',
              title: 'Chú Ý Dữ Liệu Nhập Học Chưa Đầy Đủ!',
              type: 'danger',
              duration: 3000,
              text: `Xin vui lòng kiểm tra lại các danh mục dữ liệu sau đây:<br/>-----------------------------------------------<br/>${message}`
            })
          }else{
            u.apax.$emit('apaxLoading', true)
            const method = this.student ? u.put: u.p;
            params.action = 'add'
            method(`/api/student-temp?${new Date().getTime()}`, params).then((res) => {
              u.apax.$emit('apaxLoading', false)
              if (res.status.error_code == 1){
                alert(res.status.message)
              }
              else{
                this.$router.go(-1)
              }
            })
          } 
        })
        
      },
      validate(params) {
        let message = "";
        let status = true;
        if (!_.get(params, 'gud_name1')) {
          message += `Họ tên Phụ huynh 1 bắt buộc phải nhập<br/>`;
          status = false
        }
        if (!_.get(params, 'gud_mobile1')) {
          message += `Số điện thoại Phụ huynh 1 bắt buộc phải nhập<br/>`;
          status = false
        }else{
          if (!u.vld.phone(_.get(params, 'gud_mobile1'))) {
            message += `Số điện thoại Phụ huynh 1 nhập không đúng<br/>`;
            status = false
          }
        }
        if (_.get(params, 'gud_mobile2')&& !u.vld.phone(_.get(params, 'gud_mobile2'))) {
          message += `Số điện thoại Phụ huynh 2 nhập không đúng<br/>`;
          status = false
        }
        if(_.get(params, 'gud_email1') && !u.vld.email(_.get(params, 'gud_email1'))){
          message += `Email phụ huynh 1 nhập không đúng<br/>`;
          status = false
        }
        if(_.get(params, 'gud_email2') && !u.vld.email(_.get(params, 'gud_email2'))){
          message += `Email phụ huynh 2 nhập không đúng<br/>`;
          status = false
        }
        if(!_.get(params, 'branch_id')){
          message += `Chưa chọn trung tâm<br/>`;
          status = false
        }
        if(!_.get(params, 'ec_id')){
          message += `Chưa chọn EC<br/>`;
          status = false
        }
        if (!status) {
          this.$notify({
            group: 'apax-atc',
            title: 'Chú Ý Dữ Liệu Nhập Học Chưa Đầy Đủ!',
            type: 'danger',
            duration: 3000,
            text: `Xin vui lòng kiểm tra lại các danh mục dữ liệu sau đây:<br/>-----------------------------------------------<br/>${message}`
          })
        }
        return status
      }
    },
    checkPhoneExist() {
      console.log('123');
      if( this.gud_mobile1!=''){
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/students/check-phone-parent/${this.gud_mobile1}`).then(response => {
          u.apax.$emit('apaxLoading', false)
          if (parseInt(response) == 1) {
            alert('Số điện thoại phụ huynnh đã tồn tại trên hệ thống vui lòng kiểm tra lại');
            this.gud_mobile1=''
          } 
        })
      }
    },
  }
</script>
