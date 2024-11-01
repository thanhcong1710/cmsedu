import u from '../../../../utilities/utility'
export default {
  methods: {
    deleteStudent: function (student, filter = false) {
      if(confirm("Bạn có chắc chắn muốn xóa ?")){
        u.apax.$emit('apaxLoading', true)
        u.put(`/api/student-temp/delete?${new Date().getTime()}`, student).then((response) => {
          const status = parseInt(response.status)
          if (status === 1 && !filter)
            this.students = this.students.filter((item) => (parseInt(_.get(item, 'id')) !== parseInt(_.get(student, 'id'))))

          if (filter && status === 1)
            this.$router.push("/student-temp")
          u.apax.$emit('apaxLoading', false)
        })
      }
    },
    deleteStudentTempCare: function (id = 0, std_temp_id = 0) {
      if(confirm("Bạn có chắc chắn muốn xóa ?")){
        console.log("vao xoa du lieu")
        u.put(`/api/student-temp/care-delete`, {'temp_care_id':id}).then((response) => {
          return response
        })
      }
    },
  },
}
