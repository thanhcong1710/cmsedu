<template>
	<div class="animated fadeIn apax-form frm-dashboad">
		<!-- <b-col cols="12">
			<b-row>
				<b-col cols="12">
						<DashboardReport
								:roles="roles"
								:dataAll="dataAll"
								:branchRenew="branchRenew"
								:bestBranch="bestBranch"
								:badBranch="badBranch"
								:badLeader="badLeader"
								:bestLeader="bestLeader"
								:bestSale="bestSale"
								:badSale="badSale"

								:onChangeTopCenterBest="changeTopCenterBest"
								:onChangeTopCenterBad="changeTopCenterBad"
								:onChangeTopLeaderBest="changeTopLeaderBest"
								:onChangeTopLeaderBad="changeTopLeaderBad"
								:onChangeTopSaleBest="changeTopSaleBest"
								:onChangeTopSaleBad="changeTopSaleBad"
						></DashboardReport>
				</b-col>
			</b-row>
		</b-col> -->
		<b-col cols="12" v-if="roles.renewlist === true">
			<b-row>
				<b-col cols="12">
					<Renew :month="currentMonth" :branches="branchRenew"></Renew>
				</b-col>
			</b-row>
		</b-col>
		<b-col cols="12" v-if="roles.studentStatus === true">
			<b-row>
				<b-col cols="12">
					<StudentClassStatus :tableName="titleStudentStatus"></StudentClassStatus>
				</b-col>
			</b-row>
		</b-col>
		<b-col cols="12" v-if="roles.studentAttendances === true">
			<b-row>
				<b-col cols="12">
					<StudentAttendances :tableName="StudentAttendances"></StudentAttendances>
				</b-col>
			</b-row>
		</b-col>
		<!-- <b-col cols="12">
			<b-row>
				<b-col cols="12">
					<b-card header>
						<MonthlyNewStudents :tableName="titleNewStudent"></MonthlyNewStudents>
					</b-card>
				</b-col>
			</b-row>
		</b-col> -->
	</div>
</template>
<script>
import axios from 'axios'
import u from '../../../utilities/utility'
import Renew from './Renew.vue'
//import MonthlyNewStudents from './MonthlyNewStudents.vue'
//import DashboardReport from './DashboardReport/DashboardReport.vue'
import StudentClassStatus from './DashboardReport/StudentClassStatus.vue'
import StudentAttendances from './DashboardReport/StudentAttendances.vue'

export default {
	components: { 
		//DashboardReport, 
		Renew, 
		//MonthlyNewStudents, 
		StudentClassStatus,
		StudentAttendances 
	},
	data(){
		return {
			titleNewStudent: "Danh sách học sinh mới hàng tháng",
			titleStudentStatus: "Danh Sách Cảnh Báo Học Sinh Quá Hạn Cọc",
			StudentAttendances: "DANH SÁCH HỌC SINH KHÔNG ĐIỂM DANH",
			dataAll: [],
			bestBranch: [],
			badBranch: [],
			branchRenew: [],
			badLeader: [],
			bestLeader: [],
			bestSale: [],
			badSale: [],
			userdata: [],
			roles: {
				listbranch: true,
				topbranch: true,
				topleader: true,
				topsale: true,
				renewlist: true,
                studentStatus: true,
				studentAttendances:true,
			},
			limit: {
                dataAll: 5,
                bestBranch: 5,
                badBranch: 5,
                branchRenew:5,
                badLeader: 5,
                bestLeader: 5,
                bestSale: 10,
                badSale: 10,
                userdata: 5
            },
			currentMonth: ''
		}
	},
	created(){
		let currentdate = new Date();
		this.currentMonth = currentdate.getMonth();
		this.currentMonth = parseInt(this.currentMonth)+1;
		this.userdata = u.session().user;
        this.authorized();

        /*if(this.roles.listbranch){
            u.a().get('/api/dashboards/').then(response =>{
                this.dataAll = response.data.data;
                this.userdata = response.data.user_data
            })
		}

		if(this.roles.topbranch){
            u.a().get('api/dashboards/topbranch/1/' + this.limit.bestBranch).then(response =>{
                this.bestBranch = response.data
            })
            u.a().get('api/dashboards/topbranch/0/' + this.limit.badBranch).then(response =>{
                this.badBranch = response.data
            })
		}
*/
        if(this.roles.renewlist){
            u.a().get(`/api/dashboards/renew/${this.currentMonth}/2018/`).then(response => {
                this.branchRenew = response.data
            })
		}

		/*if(this.roles.topleader){
            u.a().get('/api/dashboards/topleadear/1/' + this.limit.bestLeader).then(response =>{
                this.bestLeader = response.data
            })
            u.a().get('/api/dashboards/topleadear/0/' + this.limit.badLeader).then(response =>{
                this.badLeader = response.data
            })
		}

        if(this.roles.topsale){
            u.a().get('/api/dashboards/topsale/1/' + this.limit.bestSale).then(response =>{
                this.bestSale = response.data
            })
            u.a().get('/api/dashboards/topsale/0/' + + this.limit.badSale).then(response =>{
                this.badSale = response.data
            })
		}*/

	},
	methods: {
		authorized(){
			switch(parseInt(this.userdata.role_id)){
				case u.r.super_administrator:
                case u.r.admin:
				case u.r.founder: {
					break;
				}
				case u.r.zone_ceo:
				case u.r.region_ceo: {
                    this.roles.topbranch = false;
                    this.roles.topsale = false;
                    this.roles.topleader = false;
					break;
				}
				case u.r.branch_ceo: {
                    this.roles.topbranch = false;
                    this.roles.topsale = false;
                    this.roles.topleader = false;
					break
				}
				case u.r.om:
				case u.r.cm:
				case u.r.cashier: {
                    this.roles.listbranch = false;
                    this.roles.topbranch = false;
                    this.roles.topsale = false;
                    this.roles.topleader = false;
				    break;
				}
				default: {
					this.roles.listbranch = false;
					this.roles.topbranch = false;
					this.roles.topsale = false;
					this.roles.topleader = false;
					this.roles.renewlist = false;
					this.roles.studentStatus = false;
				}
			}
		},
        changeTopCenterBest(rows){
		    if(this.roles.topbranch){
                u.a().get('api/dashboards/topbranch/1/' + rows).then(response =>{
                    this.bestBranch = response.data
                })
			}
		},
        changeTopCenterBad(rows){
		    if(this.roles.topbranch){
                u.a().get('api/dashboards/topbranch/0/' + rows).then(response =>{
                    this.badBranch = response.data
                })
			}
		},
        changeTopLeaderBest(rows){
            if(this.roles.topleader){
                u.a().get('/api/dashboards/topleadear/1/' + rows).then(response =>{
                    this.bestLeader = response.data
                })
			}
		},
        changeTopLeaderBad(rows){
		    if(this.roles.topleader){
                u.a().get('/api/dashboards/topleadear/0/' + rows).then(response =>{
                    this.badLeader = response.data
                })
			}
		},
        changeTopSaleBest(rows){
		    if(this.roles.topsale){
                u.a().get('/api/dashboards/topsale/1/' + rows).then(response =>{
                    this.bestSale = response.data
                })
			}
		},
        changeTopSaleBad(rows){
		    if(this.roles.topsale){
                u.a().get('/api/dashboards/topsale/0/' + rows).then(response =>{
                    this.badSale = response.data
                })
			}
		}
	}
}
</script>
