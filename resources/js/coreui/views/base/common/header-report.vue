<template functional>
  <div slot="header">
    <strong>{{ props.title }}</strong>
    <div
      class="card-actions"
      v-if="!props.hiddenSkype"
    >
      <a
        href="skype:thanhcong1710?chat"
        target="_blank"
      >
        <small className="text-muted"><i class="fa fa-skype" /></small>
      </a>
    </div>
  </div>
</template>
