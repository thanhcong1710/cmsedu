<template functional>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list" />Danh sách quyền</strong>
          </div>
          <div class="panel-body">
            <table class="table table-bordered apax-table">
              <thead>
                <tr>
                  <th>Mã</th>
                  <th>Tên</th>
                  <th>Mô tả</th>
                  <th><i class="fa fa-cog fa-2x" /></th>
                </tr>
              </thead>
              <tbody v-if="props.state.roles">
                <tr
                  v-for="(role, index) in props.state.roles"
                  :key="index"
                >
                  <td>{{ role.id }}</td>
                  <td>{{ role.name }}</td>
                  <td>
                    {{ role.description }}
                  </td>
                  <td class="text-center cl-btn">
                    <router-link
                      :to="`/roles/${role.id}/edit`"
                    >
                      <span class="fa fa-edit" />
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>
