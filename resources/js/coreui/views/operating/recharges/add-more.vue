<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>Tên học sinh (Add more charge)</strong>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <label class="control-label">Tên học sinh</label>
                                    <div class="input-group mar-btm">
                                        <input class="form-control" type="text" readonly v-model="charge.student_name">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Mã CRM</label>
                                        <input class="form-control" type="text" readonly :value="charge.student_id">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Mã EFFECT</label>
                                        <input class="form-control" type="text" readonly :value="charge.accounting_id">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Mã LMS</label>
                                        <input class="form-control" type="text" readonly :value="charge.lms_stu_id">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Trung tâm</label>
                                        <input class="form-control" type="text" readonly :value="charge.branch_name">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">EC</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">EC Leader</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">CM</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Sản phẩm</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Chương trình</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Gói phí</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Giá thực thu</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Voucher</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Tích điểm</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Chiết khấu</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Khác</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>

            <b-row>
                <b-col cols="12">
                    <b-card header-tag="header" footer-tag="footer">
                        <div slot="header">
                            <strong>Thông tin thu tiền</strong>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Số tiền phải đóng</label>
                                        <input class="form-control" type="text" readonly :value="student.name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Số tiền đã thu</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Tổng tiền thu</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Công nợ</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Hình thức thu</label>
                                        <select class="form-control" disabled>
                                            <option>1 lần</option>
                                            <option>Nhiều lần</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Loại thu tiền</label>
                                        <select class="form-control">
                                            <option>Chuyển khoản</option>
                                            <option>Tiền mặt</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Ghi chú</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div slot="footer" class="text-right">
                            <button type="submit" class="btn btn-sm btn-success" @click="addCharge"><i class="fa fa-dot-circle-o"></i> Lưu</button>
                            <button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i> Nhập lại</button>
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
    name: 'Add-Charge',
    data(){
        return {
            students: [],
            student: '',
            charges: [],
            charge: {
                contract_id: '',
                method: '',
                payload: '',
                amount: '',
                creator_id: '',
                debt: ''
            }
        }
    },
    created(){
        let uri = '/api/charges/'+this.$route.params.id;
        axios.get(uri).then((response) => {
            this.charge = response.data[0];

            console.log(`this.charge ${JSON.stringify(this.charge.student_id)}`)
        });
    },
    methods: {
        addCharge(){
            axios.post('/api/charges', this.charge).then(response => {
              this.$router.push('/charges')
              // this.user.push(response.data.charges);

            });
        },
        getStudentDetail(){
            // console.log(student, idx);
        }
    }
}
</script>

<style scoped>

</style>