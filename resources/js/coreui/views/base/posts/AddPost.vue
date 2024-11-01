<template>
	<div class="animated fadeIn">
	    <div class="row">
	    	<div class="col-sm-8">
	        	<b-card>
		        	<div slot="header">
		            	<strong>Add new post</strong>
		        	</div>
		        	<form @submit.prevent="addPost" method="post">
		        		<div class="form-group">
		        			<label for="">Title</label>
		        			<input type="text" class="form-control" placeholder="Title" v-model="post.title">
		        		</div>
		        		<div class="form-group">
		        			<label for="">Category</label>
		        			<select v-model="post.category_id" id="category" class="form-control">
		        				<option v-bind:value="category.id" v-for="category in categories">{{ category.name}}</option>
		        			</select>
		        		</div>
		        		<div class="form-group">
		        			<label for="">Content</label>
		        			<textarea v-model="post.content" id="content" class="form-control" cols="30" rows="6"></textarea>
		        		</div>
		        		<div class="form-group">
		        			<label for="">Featured</label>
		        			<input type="file" class="form-control" @change="imageChanged">
		        		</div>
		        		<div class="form-group">
		        			<button type="submit" class="btn btn-success">Add new</button>
		        		</div>
		        	</form>
		        </b-card>
		    </div>
		</div>
	</div>
</template>
<script>
	import axios from 'axios'
	export default {
		data(){
			return {
				post: {
					title: '',
					content: '',
					featured: '',
					category_id: ''
				},
				categories : [{id: 1, name: 'category 1'}, {id: 2, name: 'category 2'}, {id: 3, name: 'category 3'}, {id: 4, name: 'category 4'}]
			}
		},
		methods: {
			addPost(){
				axios.post('/api/posts', this.post).then(response => {
					this.$router.push('/posts/list')
				})
			},
			imageChanged(e){
				console.log(e.target.files[0]);
				var fileReader = new FileReader();
				fileReader.readAsDataURL(e.target.files[0])
				fileReader.onload = (e) => {
					this.post.featured = e.target.result
				}
				console.log(this.post);
			}
		}
	}
</script>