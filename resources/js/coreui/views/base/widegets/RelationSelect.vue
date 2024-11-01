<!--
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 *  Apax ERP System
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */-->

<template>
	<div class="animated fadeIn">
	    <div class="row">
	    	<div class="col-sm-6">
		        <b-card>
		          <div slot="header">
		            <strong>Credit Card</strong> <small>Form</small>
		          </div>

		          <div class="row">
		            <div class="col-sm-12">
		              <b-form-fieldset label="Name">
		                <b-form-input type="text" id="name" placeholder="Enter your name"></b-form-input>
		              </b-form-fieldset>
		            </div>
		          </div>

		          <div class="row">
		            <div class="col-sm-12">
		              <b-form-fieldset label="Credit Card Number">
		                <b-form-input type="text" id="ccnumber" placeholder="0000 0000 0000 0000"></b-form-input>
		              </b-form-fieldset>
		            </div>
		          </div>

		          <div class="row">
		            <div class="col-sm-4">
		              <b-form-fieldset label="Month">
		                <b-form-select
		                  :plain="true"
		                  :options="[1,2,3,4,5,6,7,8,9,10,11,12]">
		                </b-form-select>
		              </b-form-fieldset>
		            </div><!--/.col-->
		            <div class="col-sm-4">
		              <b-form-fieldset label="Year">
		                <b-form-select
		                  :plain="true"
		                  :options="[2014,2015,2016,2017,2018,2019,2020,2021,2022,2023,2024,2025]">
		                </b-form-select>
		              </b-form-fieldset>
		            </div><!--/.col-->
		            <div class="col-sm-4">
		              <b-form-fieldset label="CVV/CVC">
		                <b-form-input type="text" id="cvv" placeholder="123"></b-form-input>
		              </b-form-fieldset>
		            </div><!--/.col-->
		          </div><!--/.row-->
		        </b-card>
	    	</div>

		    <div class="col-sm-6">
			    <form @submit.prevent="saveCompany">
			        <b-card>
			          <div slot="header">
			            <strong>Company Filter</strong> <small>Form</small>
			          </div>

			          <div class="form-group">
				            <label for="country">Company</label>
				            <input type="text" class="form-control" placeholder="Enter your company name..." />
				          </div>
			           <div class="form-group">
			            	<label for="country">Street</label>
				            <input type="text" class="form-control" placeholder="Enter street name..." />
				         </div>
			          <div class="row">
			            <div class="col-sm-8">
			              <div class="form-group">
				            <label for="country">City</label>
				            <select  class="form-control" name="city" id="city">
				            	<option>--Select city--</option>
				            </select>
				          </div>
			            </div><!--/.col-->
			            <div class="col-sm-4">
			              <div class="form-group">
				            <label for="country">Postal Code</label>
				            <select  class="form-control" name="post_code" id="post_code">
				            	<option>--Select Post code--</option>
				            </select>
				          </div>
			            </div><!--/.col-->
			          </div><!--/.row-->
			          <div class="form-group">
			            <label for="country">Country</label>
			            <select  class="form-control" name="country" id="country">
			            	<option>--Select country--</option>
			            </select>
			          </div>
			          <div class="form-group">
			          	<button type="submit" class="btn btn-success">Save company</button>
			          </div>
			        </b-card>
				</form>
		    </div>
	    </div>
	    <div class="row">
	    	<div class="col-12">
		    	<b-card>
		    		<div slot="header">
		            	<strong>Company List</strong>
		          	</div>
		    		<table class="table table-bordered">
		    			<thead>
		    				<tr>
		    					<th>ID</th>
		    					<th>Company name</th>
		    					<th>City</th>
		    					<th>Country</th>
		    					<th>Action</th>
		    				</tr>
		    			</thead>
		    			<tbody>
		    				<tr v-for="company in companies">
		    					<td>{{company.id}}</td>
		    					<td>{{company.name}}</td>
		    					<td>{{company.city_id}}</td>
		    					<td>{{company.country_id}}</td>
		    					<td>
		    						<button class="btn btn-sm btn-primary" ><i class="fa fa-pencil"></i></button>
		    						<button class="btn btn-sm btn-danger" ><i class="fa fa-times"></i></button>
		    					</td>
		    				</tr>
		    			</tbody>
		    		</table>
		    	</b-card>
	    	</div>
	    </div>
	</div>
</template>

<script>

import axios from 'axios';
export default {
	data () {
		return {
			companies: {
				name: '',
				city: '',
				country: ''
			}
		}
	},
	created(){
		axios.get('/api/companies').then(response => {
			this.companies = response.data;
			console.log(this.companies);
		});
	},
	methods: {
    	click () {
      	// do nothing
    	}
	}
}
</script>
