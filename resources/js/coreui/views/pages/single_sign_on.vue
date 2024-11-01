<template></template>

<script>
import axios from "axios";
import u from "../../utilities/utility";
import db from "../../utilities/database";
import a from "../../utilities/authentication";

const SESSION_STATUS = "ss";

export default {
  name: "Login",
  data() {
    return {};
  },
  created() {
     axios.post("/api/single-sign-on", {
      hrm_id: this.$route.params.hrm_id,
      token: this.$route.params.token,
    }).then((response) => {
        a.login(this.$router, response.data, "/dashboard");
      })
      .catch((e) => {
        console.log(e);
      });
  },
  methods: {},
};
</script>
