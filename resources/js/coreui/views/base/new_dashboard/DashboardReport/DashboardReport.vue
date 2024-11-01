<template>
	<div class="wrapper">
		<b-row>
			<b-col cols="12">
				<b-card v-if="roles.listbranch === true" header>
					<div slot="header" class="text-center">
				        <i></i> <b class="uppercase">Doanh Số Toàn Bộ Các Trung Tâm</b>
				     </div>
					<b-col cols="12">
						<b-card>
							<div class="tab-header">
				                <ul class="nav nav-tabs">
				                    <li :class="{forActive: currentTab === index}" :key="index" v-for="(tab, index) in tabs"
				                        class="nav-item">
				                        <a class="nav-link" data-toggle="tab" aria-expanded="true"
				                           @click="changeTab(index, tab.month, tab.year)">{{tab.text}}</a>
				                    </li>
				                </ul>
				            </div>
							<div class="panel-body fixed-table">
								<div class="table-responsive scrollable">
									<table class="table table-striped table-bordered apax-table">
										<thead>
											<tr class="text-sm">
												<th class="width-80">Trung tâm</th>
												<th class="width-50">Doanh số</th>
												<th class="width-50">Doanh số trong ngày</th>
												<th class="width-50">Doanh số kế hoạch</th>
												<th class="width-50">% Kế hoạch</th>
												<th class="width-50">Số học sinh mới</th>
												<th class="width-50">Số học sinh sắp hết hạn</th>
												<th class="width-50">Số học sinh còn nợ</th>
											</tr>
										</thead>
										<tbody>

											<tr>
												<td class="bg-danger">{{dataAll.total.ten}}</td>
												<td class="bg-danger">{{dataAll.total.ps_no1 | formatMoney}}</td>
												<td class="bg-danger">{{dataAll.total.ps_ngay | formatMoney}}</td>
												<td class="bg-danger">{{dataAll.total.ps_no1kh | formatMoney}}</td>
												<td class="bg-danger">{{dataAll.total.percent}}%</td>
												<td class="bg-danger">{{dataAll.total.hs_moi}}</td>
												<td class="bg-danger">{{dataAll.total.hs_het_han}}</td>
												<td class="bg-danger">{{dataAll.total.hs_no}}</td>
											</tr>

											<tr v-for="(item,index) in dataAll.zones" :key="index">
												<td class="bg-info">{{item.ten}}</td>
												<td class="bg-info">{{item.ps_no1 | formatMoney}}</td>
												<td class="bg-info">{{item.ps_ngay | formatMoney}}</td>
												<td class="bg-info">{{item.ps_no1kh | formatMoney}}</td>
												<td class="bg-info">{{item.percent}}%</td>
												<td class="bg-info">{{item.hs_moi}}</td>
												<td class="bg-info">{{item.hs_het_han}}</td>
												<td class="bg-info">{{item.hs_no}}</td>
											</tr>

											<tr v-for="(item, index) in dataAll.result" :key="index">
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.ten}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.ps_no1 | formatMoney}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.ps_ngay | formatMoney}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.ps_no1kh | formatMoney}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.percent}}%</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.hs_moi | formatNumber}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.hs_het_han | formatNumber}}</td>
												<td :class="{'bg-info': item.invisible === 3, 'bg-danger': item.invisible === 4}">{{item.hs_no | formatNumber}}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</b-card>
					</b-col>
					<b-col cols="12" v-if="roles.topbranch === true">
						<b-row>
							<b-col cols="6">
								<TopCenter :listCenter="bestBranch" :bgColor="bestColor" :tableName="topCenterBest" :onChange="changeTopCenterBest"></TopCenter>
							</b-col>
							<b-col cols="6">
								<TopCenter :listCenter="badBranch" :bgColor="badColor" :tableName="topCenterBad" :onChange="changeTopCenterBad"></TopCenter>
							</b-col>
						</b-row>
					</b-col>
					<b-col cols="12" v-if="roles.topleader === true">
						<b-row>
							<b-col cols="6">
								<TopLeader :leader="bestLeader" :bgColor="bestColor" :tableName="topLeaderBest" :onChange="changeTopLeaderBest"></TopLeader>
							</b-col>
							<b-col cols="6">
								<TopLeader :leader="badLeader" :bgColor="badColor" :tableName="topLeaderBad" :onChange="changeTopLeaderBad"></TopLeader>
							</b-col>
						</b-row>
					</b-col>
					<b-col cols="12" v-if="roles.topsale === true">
						<b-row>
							<b-col cols="6">
								<TopSales :sale="bestSale" :bgColor="bestColor" :tableName="topSalesBest" :onChange="changeTopSaleBest"></TopSales>
							</b-col>
							<b-col cols="6">
								<TopSales :sale="badSale" :bgColor="badColor" :tableName="topSalesBad" :onChange="changeTopSaleBad"></TopSales>
							</b-col>
						</b-row>
					</b-col>
				</b-card>
			</b-col>
		</b-row>
	</div>
</template>
<script>
import u from '../../../../utilities/utility'
import TopCenter from './TopCenter.vue'
import TopLeader from './TopLeader.vue'
import TopSales from './TopSales.vue'
import moment from 'moment'

export default {
	components: { TopCenter, TopLeader, TopSales},
	props: {
        dataAll: {

		},
        badBranch: {

		},
        bestBranch: {

        },
        branchRenew: {

        },
        badLeader: {

        },
        bestLeader: {

        },
        bestSale: {

        },
        badSale: {

        },
        roles: {

        },
		onChangeTopCenterBest: {
            type: Function
		},
		onChangeTopCenterBad: {
            type: Function
		},
		onChangeTopLeaderBest: {
            type: Function
		},
        onChangeTopLeaderBad: {
            type: Function
		},
		onChangeTopSaleBest: {
            type: Function
		},
        onChangeTopSaleBad: {
            type: Function
		},
    },
	created(){
		// u.bus.$on('CALL_ME', (v)=>{
		// 	u.log('YOU ARE CALLING ME', v)
		// })
		// const x = 'OK 123'
		// this.o.s('test', x)
		// this.o.i('CALLING', (v)=>{
		// 	u.log('I AM CALLING', v)
		// })
		// u.log('Test', this.o.g('test'))
	},
	data(){
		return {
			topCenterBest: "Top 5 Best Trung Tâm",
			topCenterBad: "Top 5 Bad Trung Tâm",
			topLeaderBest: "Top 5 Best Leader",
			topLeaderBad: "Top 5 Bad Leader",
			topSalesBest: "Top 10 Best Sales",
			topSalesBad: "Top 10 Bad Sales",
			badColor: "bad",
			bestColor: "best",
			tabs: this.getTabInfo(),
			currentTab:0,
			dataAll: []
		}
	},
	methods:{
		getTabInfo() {
                const resp = [
                    {
                        name: 'tab-1',
                        text: `Tháng ${parseInt(this.moment().month()) + 1} / ${parseInt(this.moment().year())}`,
                        month: parseInt(this.moment().month()) + 1,
                        year: parseInt(this.moment().year())
                    },
                    {
                        name: 'tab-2',
                        text: `Tháng ${parseInt(this.moment().add(1, 'M').month()) - 1} / ${parseInt(this.moment().add(1, 'M').year())}`,
                        month: parseInt(this.moment().add(1, 'M').month()) - 1,
                        year: parseInt(this.moment().add(1, 'M').year())
                    },
                    {
                        name: 'tab-3',
                        text: `Tháng ${parseInt(this.moment().add(2, 'M').month()) - 3} / ${parseInt(this.moment().add(2, 'M').year())}`,
                        month: parseInt(this.moment().add(2, 'M').month()) - 3,
                        year: parseInt(this.moment().add(2, 'M').year())
                    }
                ]
                return resp
            },

        changeTab(val, month, year) {
        	month = month.toString();
        	if( month.length == 1 ) {
        		month = '0' + month;
        	}
        	this.currentTab = val;
        	var date = `${year}` + '-' + `${month}` + '-01';
        	u.a().get('/api/dashboards?date=' + date).then(response =>{
                this.dataAll = response.data.data;
            })
        },

		changeTopCenterBest(rows){
		    console.log(rows);
		    this.onChangeTopCenterBest(rows);
		    this.topCenterBest = `Top ${rows} Best Trung Tâm`;
		},
		changeTopCenterBad(rows){
            console.log(rows);
            this.onChangeTopCenterBad(rows);
            this.topCenterBad = `Top ${rows} Bad Trung Tâm`;
		},
		changeTopLeaderBest(rows){
		    this.onChangeTopLeaderBest(rows);
		    this.topLeaderBest = `Top ${rows} Best Leader`;
		},
		changeTopLeaderBad(rows){
            this.onChangeTopLeaderBad(rows);
            this.topLeaderBad = `Top ${rows} Bad Leader`;
		},
		changeTopSaleBest(rows){
		    this.onChangeTopSaleBest(rows);
		    this.topSalesBest = `Top ${rows} Best Sales`;
		},
		changeTopSaleBad(rows){
            this.onChangeTopSaleBad(rows);
            this.topSalesBad = `Top ${rows} Bad Sales`;
		}
	}
}
</script>
<style>
.fixed-table{
	max-height: 300px;
	overflow-y: auto;
}
</style>
