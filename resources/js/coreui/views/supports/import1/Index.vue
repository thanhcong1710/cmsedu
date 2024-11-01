<template>
  <provider v-slot="{ state, actions }">
    <consumer v-bind="{ state, actions }" />
  </provider>
</template>

<script>
import Provider from './Provider'
import Consumer from './Consumer'

export default {
  components: {
    Provider,
    Consumer,
  },
}
</script>
