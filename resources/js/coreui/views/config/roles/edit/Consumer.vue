<template functional>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div style="display: flex; flex-direction: column">
            <div><strong>Tên: </strong>{{ props.state.role && props.state.role.name }}</div>
            <div><strong>Mô tả: </strong>{{ props.state.role && props.state.role.description }}</div>
            <div>
              <strong>Trạng thái: {{ props.state.role && props.state.role.status === 1 }}</strong>
              <toggle-button
                v-if="props.state.role"
                :value="props.state.role && props.state.role.status === 1"
                color="#068945"
                :width="120"
                :labels="{checked: 'Hoạt động', unchecked: 'Không hoạt động'}"
                @change="props.actions.changeRoleStatus"
              />
            </div>
          </div>
          <div class="panel-body">
            <table class="table table-bordered apax-table">
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Tên</th>
                  <th>all</th>
                  <th>List</th>
                  <th>Search</th>
                  <th>Detail</th>
                  <th>Add</th>
                  <th>Edit</th>
                  <th>Delete</th>
                  <th>Report</th>
                  <th>Upload</th>
                  <th>Download</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(module, index) in props.state.modules"
                  :key="index"
                >
                  <td>{{ index + 1 }}</td>
                  <td>{{ module.value }}</td>
                  <td
                    v-for="(roleName, indexRole) in props.state.roles"
                    :key="indexRole"
                  >
                    <toggle-button
                      :value="props.actions.getStatus(roleName.key, module.key)"
                      color="#068945"
                      :width="35"
                      :height="15"
                      style="top: 4px;"
                      @change="(target) => {props.actions.changeRole(roleName.key, module.key, target.value)}"
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
