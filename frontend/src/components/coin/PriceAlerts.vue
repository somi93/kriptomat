<template>
  <div>
    <price-alert-dialog v-model="priceAlertDialog" @addAlert="alerts.unshift($event)"></price-alert-dialog>
    <v-row align="center" no-gutters justify="space-between" class="mb-3">
      <p class="font-weight-medium secondary--text mb-0">Price Alerts</p>
      <v-btn small text color="primary" @click="priceAlertDialog = true"
             style="text-transform: none">
        <v-icon small>mdi-plus</v-icon>
        New price alert
      </v-btn>
    </v-row>
    <v-data-table
        disable-sort
        hide-default-footer
        :headers="headers"
        :items="alerts"
        :loading="loading"
        class="rounded mt-8 coins-table">
      <template v-slot:item.name="{item}">
        <v-row align="center">
          <div class="mx-2">
            <v-img :src="item.image.thumb" height="32px" width="32px"></v-img>
          </div>
          <span class="secondary--text font-weight-medium text-decoration-none">
          {{ item.name }}
        </span>
          <span class="grey--text font-weight-medium ml-1 text-uppercase">
          {{ item.symbol }}
        </span>
        </v-row>
      </template>
      <template v-slot:item.price="{item}">
        <span>1 {{ item.name }} {{ item.condition | condition }} {{ item.price | fixed }} EUR</span>
      </template>
      <template v-slot:item.created_at="{item}">
        <span>{{ item.created_at | date }}</span>
      </template>
      <template v-slot:item.delete="{item}">
        <v-btn icon color="error" @click="remove(item)">
          <v-icon>mdi-delete-outline</v-icon>
        </v-btn>
      </template>
    </v-data-table>
    <confirm-box ref="confirmBox"></confirm-box>
  </div>
</template>

<script>
import moment from "moment";

import userService from "@/services/user";

import confirmBox from '@/components/global/ConfirmBox'
import PriceAlertDialog from "@/components/coin/PriceAlertDialog";

export default {
  components: {
    confirmBox,
    PriceAlertDialog
  },
  data() {
    return {
      loading: true,
      headers: [
        {text: 'Currency', value: 'name'},
        {text: 'Alert Price', value: 'price', align: 'right'},
        {text: 'Set On', value: 'created_at', align: 'right'},
        {text: 'Delete', value: 'delete', align: 'right'}
      ],
      alerts: [],
      priceAlertDialog: false
    }
  },
  created() {
    // this.$store.dispatch('coin/getCoins');
    userService.alerts({user_id: 1})
        .then(alerts => this.alerts = alerts)
        .finally(() => this.loading = false)
  },
  methods: {
    remove(item) {
      this.$refs.confirmBox.open('Remove Price Alert', 'Are you sure want to remove this price alert', {color: 'red'})
          .then(() => {
            userService.removeAlert(item.id)
                .then(() => {
                  const index = this.alerts.findIndex(alert => alert.id === item.id);
                  this.alerts.splice(index, 1);
                })
          });
    }
  },
  // computed: {}
  filters: {
    date(date) {
      return moment(date).format('DD MMM YYYY, hh:mm A')
    },
    fixed(price) {
      return price.toFixed(2)
    },
    condition(condition) {
      return condition === 1 ? '>' : '<'
    }
  }
}
</script>