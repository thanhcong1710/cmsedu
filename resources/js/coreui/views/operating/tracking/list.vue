<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <strong>Bảng Theo Dõi Chỗ Trống Trong Lớp</strong>
          </div>
          <div class="content-detail">
            <div id="page-content">
              <div class="row">
                <div class="col-lg-12">
                  <div class="panel">
                    <div class="panel-heading">
                      <div class="panel-control"></div>
                    </div>
                    <div class="panel-body">
                      <div class="table-responsive scrollable">
                        <table class="table table-striped table-bordered apax-table">
                          <thead>
                            <tr class="text-sm">
                              <th class="text-center">Lịch</th>
                              <th>1</th>
                              <th>2</th>
                              <th>3</th>
                              <th>4</th>
                              <th>5</th>
                              <th>6</th>
                              <th>7</th>
                              <th>8</th>
                              <th>9</th>
                              <th>10</th>
                              <th>11</th>
                              <th>12</th>
                              <th>13</th>
                              <th>14</th>
                              <th>15</th>
                              <th>16</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>2018-04-22</td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square" v-bind:class="{ 'trial-light' : location.status.trial, 'offical-light': location.status.offical, 'reserve-light': location.status.reserve }"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                              <td>
                                <div class="square"></div>
                              </td>
                            </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>  
      </b-card>
    </b-col>
  </b-row>
</div> 
</div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'

export default {
  components: {
 },
  name: 'Tracking',
  data () {
    return {
      locations: [],
      location:{
        status:{
        trial: false,
        offical: true,
        reserve: false
        }
      }
    }
  },
  created(){
  },
  methods: {
        }
  }
</script>

<style scoped>
.square {
    background-color: #797979;
    height: 20px;
    width: 20px;
    margin: 0 auto;
    box-shadow: 2px 2px 0 currentColor;
}
.trial-light{
background-color: #f8cb00 !important;
}
.offical-light{
  background-color: #2592d2 !important;
}
.reserve-light{
  background-color: #fb3a38 !important;
}
</style>