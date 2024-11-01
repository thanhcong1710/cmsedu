<template>
  <button v-if="title === null"
    class="apax-button"
    :id="id"
    :class="customClass"
    :name="name"
    :disabled="disabled"
    :large="large"
    :markup="markup"
    @blur="handleBlur"
    @click="handleClick"
    @mouseover="handleHover"
    @mouseleave="handleLeave"
  >
    <slot v-if="label !== ''" >
        <i v-show="icon !== ''" class="fa" :class="icon"></i> {{ label }}
    </slot>
		<slot v-else ></slot>
  </button>
  <button v-else v-b-tooltip.hover :title="title"
    class="apax-button"
    :id="id"
    :class="customClass"
    :name="name"
    :disabled="disabled"
    :large="large"
    :markup="markup"
    @blur="handleBlur"
    @click="handleClick"
    @mouseover="handleHover"
    @mouseleave="handleLeave"
  >
    <slot v-if="label !== ''" >
        <i v-show="icon !== ''" class="fa" :class="icon"></i> {{ label }}
    </slot>
		<slot v-else ></slot>
  </button>
</template>

<script>
export default {
	name: 'apax-button',
	props: {
		id: {
			type: String,
			default: null
		},
		customClass: {
			type: String,
			default: null
		},
		name: {
			type: String,
			default: null
		},
		label: {
			type: String,
			default: null
		},
		icon: {
			type: String,
			default: null
		},
		title: {
			type: String,
			default: null
		},
		disabled: {
			type: Boolean,
			default: false
		},
		large: {
			type: Boolean,
			default: false
		},
		markup: {
			type: String,
			default: 'default'
		},
		onClick: Function,
		onHover: Function,
		onLeave: Function,
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
		handleClick (e) {
			const resp = this.info(e)
			this.onClick ? this.onClick(resp) : null
			this.$emit('click', resp)
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
		}
	}
}
</script>

<style lang="scss" scoped>

$default-color: #e7f3fd;
$default-hover-color: #363636;
$default-background: rgb(87, 105, 134);
$default-hover-background: #424d58;

$primary-color: #f5fbff;
$primary-hover-color: #091d3d;
$primary-background: #1964d4;
$primary-hover-background: #093588;

$success-color: #f2fff6;
$success-hover-color: #052e09;
$success-background: #1fc00a;
$success-hover-background: #008104;

$warning-color: #fff6e9;
$warning-hover-color: #4b3310;
$warning-background: #fc9917;
$warning-hover-background: #ca5e05;

$error-color: #ffedec;
$error-hover-color: #1a0302;
$error-background: #ff3b34;
$error-hover-background: #bb0903;

$disabled-color: #747474;

@mixin button-color($color, $hover-color, $background, $hover-background) {
	color: $color;
	background: $background;
	opacity:1;
	text-shadow: 0 1px 1px $hover-color;
	border-radius: 1px;
	font-weight: bold;
	border: 1px solid $hover-color;
	&:hover {
		color: #FFF;
		text-shadow: 0 1px 1px #111;
		border: 1px solid $background;
		background: $hover-background;
		box-shadow: 0 0 1px 1px $default-hover-color;
	}
}

.apax-button {
	cursor: pointer;
	font-family: 'Avenir', Helvetica, Arial, sans-serif;
	font-size: 12px;
	text-shadow: 0 1px 1px #363636;
	padding: 5px 15px;
	text-decoration: none;
	outline: 0;
	margin: 1px;
	&:disabled {
		cursor: not-allowed;
		// color: $disabled-color !important;
		// background: #999999!important;
		// box-shadow: 0 0 0 1px $disabled-color inset !important;
		opacity:0.3!important;
	}

	&[large="true"] {
		font-size: 1rem;
	}

	&[markup="default"] {
		@include button-color($default-color, $default-hover-color, $default-background, $default-hover-background);
	}

	&[markup="primary"] {
		@include button-color($primary-color, $primary-hover-color, $primary-background, $primary-hover-background);
	}

	&[markup="success"] {
		@include button-color($success-color, $success-hover-color, $success-background, $success-hover-background);
	}

	&[markup="warning"] {
		@include button-color($warning-color, $warning-hover-color, $warning-background, $warning-hover-background);
	}

	&[markup="error"] {
		@include button-color($error-color, $error-hover-color, $error-background, $error-hover-background);
	}
}

</style>
