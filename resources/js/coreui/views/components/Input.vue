<template>
  <div v-if="markup === 'box'" class="apax-input box form-group" :id="id" :class="customClass">
		<div class="label">
			<i v-show="icon" :class="icon"></i>
			<label class="control-label">{{label}}</label>
		</div>
		<input
		:type="type"
		:name="name"
		:value="value"
		:readonly="readonly"
		:disabled="disabled"
		:placeholder="placeholder"
		class="form-control"
		@blur="handleBlur"
		@focus="handleFocus"
		@input="handleInput"
		@keydown="handleKeydown"
		@mouseover="handleHover"
		@mouseleave="handleLeave"
		@dblclick="handleDoubleClick"
		/>
  </div>
  <div v-else-if="markup === 'line'" class="apax-input line input-group" :id="id" :class="customClass">
    <div class="input-group-prepend">
      <span class="input-group-text">
        {{label}}
      </span>
    </div>
    <input
    :type="type"
    :name="name"
    :value="value"
		:readonly="readonly"
    :disabled="disabled"
    :placeholder="placeholder"
		class="form-control"
    @blur="handleBlur"
    @focus="handleFocus"
    @input="handleInput"
    @keydown="handleKeydown"
    @mouseover="handleHover"
    @mouseleave="handleLeave"
    @dblclick="handleDoubleClick"
    />
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i :class="icon"></i>
      </span>
    </div>
  </div>
  <div v-else-if="markup === 'card'" class="apax-input card form-group" :id="id" :class="customClass">
    <b-card>
      <div class="header">
        <i v-show="icon" :class="icon"></i>
        <label class="control-label">{{label}}</label>
      </div>
      <input
      :type="type"
      :name="name"
      :value="value"
			:readonly="readonly"
      :disabled="disabled"
      :placeholder="placeholder"
			class="form-control"
      @blur="handleBlur"
      @focus="handleFocus"
      @input="handleInput"
      @keydown="handleKeydown"
      @mouseover="handleHover"
      @mouseleave="handleLeave"
      @dblclick="handleDoubleClick"
      />
    </b-card>
  </div>
  <div v-else class="apax-input default form-group" :id="id" :class="customClass">
    <div class="title">
      <i v-show="icon" :class="icon"></i>
      <label class="control-label">{{label}}</label>
    </div>
    <input
    :type="type"
    :name="name"
    :value="value"
		:readonly="readonly"
    :disabled="disabled"
    :placeholder="placeholder"
		class="form-control"
    @blur="handleBlur"
    @focus="handleFocus"
    @input="handleInput"
    @keydown="handleKeydown"
    @mouseover="handleHover"
    @mouseleave="handleLeave"
    @dblclick="handleDoubleClick"
    />
  </div>
</template>

<script>
export default {
	name: 'apax-button',
	props: {
		id: {
			type: String,
			default: null
		},
		type: {
			type: String,
			default: 'text'
		},
		label: {
			type: String,
			default: null
		},
		markup: {
			type: String,
			default: null
		},
		icon: {
			type: String,
			default: null
		},
		customClass: {
			type: String,
			default: null
		},
		value: {
			type: String,
			default: null
		},
		name: {
			type: String,
			default: null
		},
		readonly: {
			type: Boolean,
			default: false
		},
		disabled: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: null
		},
		onBlur: Function,
		onFocus: Function,
		onInput: Function,
		onHover: Function,
		onLeave: Function,
		onKeydown: Function,
		onDoubleClick: Function,
	},
	methods: {
		info(e) {
			return {
				event: e,
				name: this.name,
				id: this.id,
				markup: this.markup,
				class: this.customClass
			}
		},
		handleBlur (e) {
			const resp = this.info(e)
			this.onBlur ? this.onBlur(resp) : null
			this.$emit('blur', resp)
		},
		handleInput (e) {
			const resp = this.info(e)
			this.onInput ? this.onInput(resp) : null
			this.$emit('input', resp)
		},
		handleKeydown (e) {
			const resp = this.info(e)
			this.onKeydown ? this.onKeydown(resp) : null
			this.$emit('keydown', resp)
		},
		handleFocus (e) {
			const resp = this.info(e)
			this.onFocus ? this.onFocus(resp) : null
			this.$emit('focus', resp)
		},
		handleHover (e) {
			const resp = this.info(e)
			this.onHover ? this.onHover(resp) : null
			this.$emit('mouseover', resp)
		},
		handleLeave (e) {
			const resp = this.info(e)
			this.onLeave ? this.onLeave(resp) : null
			this.$emit('mouseleave', resp)
		},
		handleDoubleClick (e) {
			const resp = this.info(e)
			this.onDoubleClick ? this.onDoubleClick(resp) : null
			this.$emit('dblclick', resp)
		}
	}
}
</script>

<style lang="scss" scoped>
.apax-input.line input {
  padding:0 10px;
}

</style>
