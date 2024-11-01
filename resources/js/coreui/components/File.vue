<template>
	<div v-b-tooltip.hover :title="title" class="apax-file upload form-group frame" :id="id" :class="full ? customClass : `mini ${customClass}`">
		<div v-show="full" class="img-preview-frame hidden" :class="full ? '' : 'mini'"><img class="preview-img" src="/../static/img/images/nophoto.png"></div>
		<div v-show="link !== ''" class="current-file" :class="full ? '' : 'mini'" v-html="preview(link)"></div>
		<div v-if="full" class="upload-wraper">
			<input type="file" :name="name" class="input-file form-control" @change="handleChanged" data-multiple-caption="{count} files selected" multiple :disabled="disabled" />
			<label class="control-label" :for="name"><i class="fa fa-upload"></i> <span class="atch-file">{{label}}</span></label>
		</div>
		<div v-else class="upload-wraper-mini">
			<input type="file" :name="name" class="input-file form-control" @change="handleChanged" data-multiple-caption="{count} files selected" multiple :disabled="disabled" />
			<label class="control-label" :for="name"><i class="fa fa-upload"></i> <span class="atch-file">{{label}}</span></label>
		</div>
	</div>
</template>

<script>

import u from '../utilities/utility'

export default {
	name: 'apax-button',
	props: {
		id: {
			type: String,
			default: null
		},
		label: {
			type: String,
			default: null
		},
		title: {
			type: String,
			default: 'Upload file!'
		},
		alias: {
			type: String,
			default: null
		},
		short: {
			type: Number,
			default: 0
		},
		customClass: {
			type: String,
			default: ''
		},
		full: {
			type: Boolean,
			default: true
		},
		name: {
			type: String,
			default: null
		},
		type: {
			type: String,
			default: null
		},
		field: {
			type: String,
			default: null
		},
		link: {
			type: String,
			default: null
		},
		disabled: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: null
		},
		multi: {
			type: Boolean,
			default: false
		},
		onChange: Function
	},
	methods: {
		preview(link = '') {
			let resp = ''
			if (link !== '' && typeof link === 'string') {
				resp = `<a class="download-file" target="_blank" href="${link}">Download file!</a>`
				const ext = link.slice((link.lastIndexOf(".") - 1 >>> 0) + 2)
				if (['png', 'jpg', 'jpeg', 'gif'].indexOf(ext) > -1) {
					resp = `<img class="preview-image" src="${link}" />`
				}
			}
			return resp
		},
		handleChanged (e) {
			u.log('here')
			const file = {
				ext: '',
				size: 0,
				name: '',
				type: '',
				data: '',
				sign: '',
				label: ''
			}
			const fileReader = new FileReader();
			const fileName = e.target.value.split( '\\' ).pop();
			const fileSize = e.target.files.length && e.target.files[0].size ? parseInt(e.target.files[0].size) / 1000 : 0
			const ficon = $(e.target).next().find('i')
			const label = $(e.target).next().find('span')
			const frame = $(e.target).next().parent().parent().find('.img-preview-frame')
			let curen = null
			if (this.link) {
				curen = $(e.target).next().parent().parent().find('.current-file')
			}
			if (e.target.files.length && e.target.files[0]) {
				fileReader.readAsDataURL(e.target.files[0])
				fileReader.onload = (e) => {
					const source = e.target.result
					const info = e.target.result.split(',')
					file.name = fileName
					file.size = fileSize
					file.ext = fileName.slice((fileName.lastIndexOf(".") - 1 >>> 0) + 2)
					file.label = fileName.replace(`.${file.ext}`, '')
					file.type = info[0]
					file.ext = file.ext.toLowerCase()
					file.sign = u.v.check(file.ext, this.type)
					file.data = info[1]
					file.alias = `${this.alias}.${file.ext}`
					let validate = true
					let message = ''
					if (this.type && this.type === 'doc' && file.sign !== 'doc') {
						validate = false
						message = `Định dạng file đang tải lên ".${file.ext}" không phải là file tài liệu!\nXin vui lòng chọn lại file tải lên với định dạng tài liệu hợp lệ.\nChỉ chấp nhận các định dạng file tài liệu như: pdf, doc, docx, xls, xlsx.`
					} else if (this.type === 'img' && file.sign !== 'img') {
						validate = false
						message = `Định dạng file đang tải lên ".${file.ext}" không phải là file ảnh!\nXin vui lòng chọn lại file tải lên với định dạng ảnh được cho phép.\nChỉ chấp nhận các định dạng file ảnh là: jpg, jpeg, png, gif.`
					} else if (this.type === 'transfer_file' && file.sign !== 'transfer_file'){
                        validate = false
                        message = `Định dạng file đang tải lên ".${file.ext}" không hợp lệ!\nXin vui lòng chọn lại file tải lên với định dạng ảnh được cho phép.\nChỉ chấp nhận các định dạng file ảnh là: jpg, png, pdf, doc, docx.`
                    }
					if (file.sign === 'not_valid') {
						validate = false
						message = `Định dạng file đang tải lên ".${file.ext}" không hợp lệ!\nXin vui lòng chọn lại file tải lên với định dạng cho phép, lưu ý:\nChỉ chấp nhận các định dạng file ảnh là: jpg, jpeg, png, gif\nHoặc các định dạng file tài liệu như: pdf, doc, docx, xls, xlsx`
					}
					if (validate && file.sign === 'img' && u.v.img.size < file.size) {
						validate = false
						message = `Dung lượng file ảnh đang tải lên "${file.size}KB" quá lớn;\nVui lòng upload file ảnh có dung lượng nhỏ hơn "${u.v.img.size}KB"!\n`
					}
					if (validate && file.sign === 'doc' && u.v.doc.size < file.size) {
						validate = false
						message = `Dung lượng file tài liệu đang tải lên "${file.size}KB" quá lớn;\nVui lòng upload file có dung lượng nhỏ hơn "${u.v.doc.size}KB"!\n`
					}
					if (validate && file.sign === 'transfer_file' && u.v.transfer_file.size < file.size){
                        validate = false
                        message = `Dung lượng file đang tải lên "${file.size}KB" quá lớn;\nVui lòng upload file có dung lượng nhỏ hơn "${u.v.transfer_file.size}KB"!\n`
					}


					if (validate) {
						if (this.full) {
							if (this.link) {
								curen.addClass('hidden')
							}
							if (file.sign === 'img') {
								frame.removeClass('hidden').find('img').attr('src', source)
							} else if (file.ext === 'doc' || file.ext === 'docx') {
								frame.removeClass('hidden').find('img').attr('src', '/../static/img/images/icon/doc.png')
							} else if (file.ext === 'xls' || file.ext === 'xlsx') {
								frame.removeClass('hidden').find('img').attr('src', '/../static/img/images/icon/xls.png')
							} else if (file.ext === 'pdf') {
								frame.removeClass('hidden').find('img').attr('src', '/../static/img/images/icon/pdf.png')
							} else if (file.sign === 'transfer_file'){

                            }
						}
						let fileIcon = 'fa fa-file-o'
						switch (file.ext) {
							case 'doc': fileIcon = 'fa fa-file-word-o'
								break
							case 'docx': fileIcon = 'fa fa-file-word-o'
								break
							case 'xls': fileIcon = 'fa fa-file-excel-o'
								break
							case 'xlsx': fileIcon = 'fa fa-file-excel-o'
								break
							case 'ppt': fileIcon = 'fa fa-file-powerpoint-o'
								break
							case 'pptx': fileIcon = 'fa fa-file-powerpoint-o'
								break
							case 'pdf': fileIcon = 'fa fa-file-pdf-o'
								break
							case 'txt': fileIcon = 'fa fa-file-text-o'
								break
							case 'xml': fileIcon = 'fa fa-file-code-o'
								break
							case 'md': fileIcon = 'fa fa-file-code-o'
								break
						}
						if (file.sign === 'img') {
							fileIcon = 'fa fa-file-image-o'
						}
						if(!this.multi){
							let newName = file.label
							ficon.removeClass().addClass(fileIcon)
							label.html(newName)
						}
						this.onChange ? this.onChange(file, this.field) : null
						this.$emit('change', file, this.field)
					} else {
						if (this.full) {
							frame.removeClass('hidden').addClass('hidden').find('img').attr('src', '/../static/img/images/nophoto.png')
						}
						ficon.removeClass().addClass('fa fa-upload')
						u.log('come here')
						this.$notify({
							group: 'form_upload',
							title: 'File Không Hợp Lệ!',
							position: 'top center',
							type: 'error',
							duration: 9000,
							speed: 900,
							text: message
						});
					}
				}
			}
		}
	}
}
jQuery(document).ready(function($) {
	function imageLoaded() {
		let w = $(this).width();
		let h = $(this).height();
		let parentW = $(this).parent().width();
		let parentH = $(this).parent().height();
		//if (w >= parentW){ //always true because of CSS
			if (h > parentH){
				$(this).css('top', -(h-parentH)/2);
			} else if (h < parentH){
				$(this).css('height', parentH).css('width', 'auto');
				$(this).css('left', -($(this).width()-parentW)/2);
			}
		//}
	}
	$('img.preview-image').each(function() {
		if( this.complete ) {
			imageLoaded.call( this );
		} else {
			$(this).one('load', imageLoaded);
		}
	});
});
</script>

<style lang="scss" scoped>
.apax-file.upload {
	text-align: center;
}
.apax-file.upload.frame.mini {
	width: 100%;
    position: relative;
    overflow: hidden
}
.apax-file .input-file {
  opacity: 0;
  padding: 0;
  width: 280px;
  height: 36px;
  overflow: hidden;
  position: absolute;
  z-index: 999;
}
.apax-file .input-file[name='upload_transfer_file'] + label {
	text-align: left;
	line-height: 28px;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	width: 100%;
}
.apax-file.no-current-file .current-file {
	display: none;
}
.apax-file.no-current-file{
	margin-bottom: 0;
}
.apax-file .input-file + label {
  font-size: 0.86rem;
  color: #FFF;
  background: #f93838;
  border-radius: 1px;
  text-transform: capitalize;
  padding: 8px 10px;
  box-shadow: 0px 1px 3px #999;
  border: 1px solid #ce0000;
  text-shadow: 0 1px 1px #333;
  display: inline-block;
  cursor: pointer;
  text-align: center;
}
.apax-file .input-file:focus + label,
.apax-file .input-file:hover + label {
  text-shadow: 0 1px 1px #000;
  background: #ce0000;
  outline: 1px dashed rgb(252, 40, 40);
	outline: -webkit-focus-ring-color auto 1px;
}
.apax-file .input-file + label * {
  pointer-events: none!important;
}
#update-profile .apax-file .input-file + label {
	right: 170px;
	position: absolute;
	top: 10px;
	width: 135px;
}
#update-profile .apax-file .input-file {
	right: 66px;
    top: 12px;
	cursor: pointer;
}
.apax-file .upload-wraper-mini .input-file + label {
	background: #ff4f4f;
	text-shadow: 0 1px 1px #111;
	box-shadow: none;
	padding: 3px 6px;
	font-size: 12px;
  border: 1px solid #ff1919;
}
.apax-file .input-file:focus + label,
.apax-file .input-file:hover + label {
  background: #ff1f1f;
  outline: 1px dashed rgb(167, 2, 2);
	outline: -webkit-focus-ring-color auto 1px;
}
.apax-file .current-file {
	margin: 0 0 65px 0;
}
.apax-file .current-file.mini {
	margin: 0 0 10px 0;
	text-align:center;
}
.apax-file.upload .current-file a {
	font-weight: bold!important;
	text-transform: uppercase!important;
	text-decoration: underline!important;
}
.upload-wraper {
	width: calc(100% - 30px);
	position: absolute;
	height: 50px;
	bottom: 0;
	text-align: center;
}
.img-preview-frame img.preview-img,
.apax-file.upload .current-file img
.apax-file.upload .current-file img.preview-image {
	border-radius: 50%;
	max-width: 300px;
	max-height: 230px;
	margin: 0;
	-webkit-box-shadow: 0 5px 5px -3px #777;
	   -moz-box-shadow: 0 5px 5px -3px #777;
	        box-shadow: 0 5px 5px -3px #777;
}
.img-preview-frame.hidden {
	display: none!important;
}
.avatar-wrap .current-file, .avatar-wrap .img-preview-frame{
	padding-bottom: 100%;
	position: relative;
	overflow: hidden;
	border-radius: 50%;
	background-color: #ccc;
}
.upload-wraper-mini {
	max-width: 100% !important;
	input {
		max-width: 100% !important;
	}
}
</style>
