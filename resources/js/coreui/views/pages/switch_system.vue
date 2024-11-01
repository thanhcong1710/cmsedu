<template>
</template>

<script>
import u from "../../utilities/utility";
    export default {
      name: 'Login',
      data() {
        return {
          loading: {
            text: "Đang tải dữ liệu...",
            processing: false,
          },
        }
      },
      created() {
        u.g("/api/switch-system")
          .then((response) => {
            window.location.href =  response;
          })
          .catch((e) => {
          });
      },
      methods: {
      }
    }

</script>
