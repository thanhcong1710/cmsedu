<template>
<b-card header>
	<div slot="header" class="text-center">
        <i></i> <b class="uppercase">{{tableName}}</b>
		<div class="number-rows">
			<input v-model="rows" @change="change"/>
			<select v-model="srows" @change="schange">
				<option :value="item.k" v-for="(item, idx) in items" :key="idx">{{ item.v }}</option>
			</select>			
		</div>
    </div>
	<div class="table-responsive scrollable fixed-table">
		<table class="table table-striped table-bordered apax-table">
			<thead>
				<tr class="text-sm">
					<th class="width-50">STT</th>
					<th class="width-80">Tên trung tâm</th>
					<th class="width-50">Doanh số</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(center, index) in listCenter" :key="index">
					<td :class="{bgColor: check() && index == 0 , nobgColor: !check() && index==0}">{{index+1}}</td>
					<td :class="{bgColor: check() && index == 0 , nobgColor: !check() && index==0}">{{center.ten}}</td>
					<td :class="{bgColor: check() && index == 0 , nobgColor: !check() && index==0}">{{center.ps_no1 | formatMoney}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</b-card>

</template>
<style scoped lang="scss">
	.bgColor{
		background: #f86c6b;
	}
	.nobgColor{
		background: #63c2de;
	}
	.number-rows{
		position: absolute;
		left: 10px;
		top: 6px;
	}
	.number-rows input, .number-rows select{
		border: 1px solid #ccc;
		box-shadow: none;
		text-shadow: none;
		height: 30px;
		width: 54px;
		padding: 0 6px;
	}
</style>
<script>
export default {
	components: { },
	props: {
        tableName: {
            type: String,
            default: '',
        },
        listCenter: {

		},
        bgColor: {
            type: String,
            default: '',
		},
		onChange: {
            type: Function
		}
    },
	data(){
		return {
			rows: 5,
			srows: "5",
			items: [
				{
				    k: 5,
					v: 5
				},
				{
				    k: 10,
					v: 10
				},
				{
				    k: 15,
					v: 15
				},				
				{
				    k: 20,
					v: 20
				},				
				{
				    k: 25,
					v: 25
				}
			]
		}
	},
	methods: {
		check(val){
			if (this.bgColor == "bad") return true
			else return false
		},
		change(){
			(!isNaN(this.rows) && (this.rows > 0))?this.onChange(this.rows):null;
		},
		schange(){
		    console.log(this.srows);
			(!isNaN(this.srows) && (this.srows > 0))?this.onChange(this.srows):null;
		}
	}
}
</script>