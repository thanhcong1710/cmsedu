<template functional>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div style="display: flex; flex-direction: column">
            <div>
              Họ và tên:
              <strong>
                {{ props.state.user && props.state.user.full_name }} -
                {{ props.state.user && props.state.user.accounting_id }}
              </strong>
            </div>
            <div>
              Email:
              <strong>
                {{ props.state.user && props.state.user.email }}
              </strong>
            </div>
            <div>
              Số điện thại:
              <strong>
                {{ props.state.user && props.state.user.phone }}
              </strong>
            </div>
          </div>
          <div class="panel-body" style="margin-top: 20px">
            <table class="table table-bordered apax-table">
              <thead>
                <tr>
                  <th>Mã quyền</th>
                  <th>Tên Quyền</th>
                  <th>Gán quyền</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(role, index) in props.state.roles"
                  :key="index"
                >
                  <td>{{ role.id }}</td>
                  <td>{{ role.name }}</td>
                  <td>
                    <toggle-button
                      :value="props.actions.checkRoleEnable(props.state.user && props.state.user.role_ids, role.id)"
                      :sync="true"
                      color="#068945"
                      :width="35"
                      :height="15"
                      style="top: 4px;"
                      @change="(target) => {props.actions.changeRole(role, target.value); return false}"
                    />
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
