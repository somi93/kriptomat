<template>
  <v-dialog v-model="model" width="600px">
    <v-card flat width="600px" tile>
      <v-card-title>
        <v-spacer></v-spacer>
        <span class="title font-weight-medium">
          {{ done ? 'Price alert details' : 'New price alert' }}
        </span>
        <v-spacer></v-spacer>
        <v-btn icon absolute right @click="model = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="mt-6">
        <template v-if="done">
          <v-row justify="center" align="center" no-gutters>
            <div class="mx-2">
              <v-img :src="coin.image.thumb" height="48px" width="48px"></v-img>
            </div>
            <div>
              <p class="grey--text headline font-weight-medium text-uppercase mb-0 secondary--text">
                1 {{ coin.symbol }} = {{ price }} EUR
              </p>
              <p class="grey--text caption mb-0">
                Current price: 1 <span class="text-uppercase"> {{ coin.symbol }} = {{
                  coin.market_data.current_price.eur
                }} EUR</span>
              </p>
            </div>
          </v-row>
          <v-divider class="mt-4"></v-divider>
          <p class="mt-4 body-2 font-weight-medium black--text">Note</p>
          <p class="grey--text body-2">
            {{ note }}
          </p>
          <v-divider class="my-4"></v-divider>
          <v-row justify="space-between" no-gutters>
            <p class="grey--text body-2">
              Set Date
            </p>
            <p class="grey--text body-2">
              {{ updatedCoin.created_at | date }}
            </p>
          </v-row>
          <v-row justify="space-between" no-gutters>
            <p class="grey--text body-2">
              Price on set date
            </p>
            <p class="grey--text body-2 text-uppercase">
              1 {{ coin.symbol }} = {{ price }} EUR
            </p>
          </v-row>
          <v-btn
              class="mt-1"
              @click="model = false"
              large
              block
              color="primary"
              depressed
              style="text-transform: none">
            Close
          </v-btn>

          <v-btn
              class="mt-2"
              @click="remove"
              large
              block
              color="primary"
              text
              style="text-transform: none">
            Delete alert
          </v-btn>
        </template>
        <template v-else>
          <p class="caption black--text mb-0">Select currency</p>
          <v-autocomplete
              v-model="coinSymbol"
              :items="items"
              :search-input.sync="search"
              outlined
              item-text="name"
              item-value="coin_id"
              :loading="loadingCoins"
              single-line
              label="Select Currency">
            <template v-slot:item="{item}">
              <v-row align="center" no-gutters>
                <span class="secondary--text font-weight-medium">
                  {{ item.name }}
                </span>
                <span class="grey--text font-weight-medium ml-1 text-uppercase">{{ item.symbol }}</span>
              </v-row>
            </template>
            <template v-slot:selection="{item}">
              <v-row align="center" no-gutters v-if="coin">
                <div class="mx-2">
                  <v-img :src="coin.image.thumb" height="24px" width="24px"></v-img>
                </div>
                <div>
                  <p class="secondary--text body-1 font-weight-medium mb-0" style="line-height: 14px">
                    {{ item.name }} <span class="text-uppercase">({{ item.symbol }})</span>
                  </p>
                  <p class="grey--text body-1 mb-0">
                    Price: <span class="text-uppercase">{{ coin.market_data.current_price.eur }} EUR</span>
                  </p>
                </div>
              </v-row>
            </template>
          </v-autocomplete>
          <p class="caption black--text mb-0">Notify me when the price reaches</p>
          <v-row>
            <v-col cols="12" lg="8">
              <v-text-field
                  v-model.number="price"
                  outlined
                  type="number"
                  :min="0.00000001"
                  step=".01"
                  suffix="EUR"
                  single-line
                  label="Price">
              </v-text-field>
            </v-col>
            <v-col cols="12" lg="4">
              <v-text-field
                  :value="percentage"
                  readonly
                  outlined
                  suffix="%"
                  single-line
                  label="Percentage">
              </v-text-field>
            </v-col>
          </v-row>
          <v-slider
              readonly
              class="mt-8"
              :value="percentage"
              thumb-label="always"
              thumb-size="40"
              tick-size="8px"
              :color="length > 0 ? '#33CC00' : length < 0 ? 'red' : 'grey'"
              min="-100"
              max="100">
            <template v-slot:thumb-label="props">
              <template v-if="props.value > 0">+</template>
              {{ props.value }}%
            </template>
          </v-slider>
          <p class="caption black--text mb-0">Add a note</p>
          <v-textarea
              auto-grow
              rows="1"
              v-model="note"
              outlined
              single-line
              label="Add a note">
          </v-textarea>
          <v-btn
              @click="addAlert"
              :loading="loading"
              large
              block
              color="primary"
              depressed
              style="text-transform: none">
            Save alert
          </v-btn>
        </template>
      </v-card-text>
      <confirm-box ref="confirmBox"></confirm-box>
    </v-card>
  </v-dialog>
</template>

<script>
import moment from "moment";
import debounce from 'lodash.debounce'

import userService from "@/services/user";
import coinService from "@/services/coin";

import hasModel from "@/mixins/hasModel";

import confirmBox from '@/components/global/ConfirmBox'

export default {
  mixins: [hasModel],
  components: {
    confirmBox
  },
  data: () => ({
    coin: null,
    coinSymbol: null,
    note: '',
    length: 20,
    price: 0,
    loading: false,
    loadingCoins: false,
    done: false,
    updatedCoin: null,
    search: '',
    items: []
  }),
  methods: {
    remove() {
      this.$refs.confirmBox.open('Remove Price Alert', 'Are you sure want to remove this price alert', {color: 'red'})
          .then(() => {
            userService.removeAlert(this.updatedCoin.id)
                .then(() => {
                  const index = this.alerts.findIndex(alert => alert.id === this.updatedCoin.id);
                  this.alerts.splice(index, 1);
                }).finally(() => this.model = false)
          });
    },
    addAlert() {
      const params = {
        "user_id": 1,
        "name": this.coin.id,
        "state": 1,
        "condition": this.coin.market_data.current_price.eur > this.price ? 1 : 2,
        "percent": this.percentage,
        "price": this.price,
        "note": this.note
      }
      this.loading = true;
      userService.addAlert(params)
          .then(coin => {
            this.updatedCoin = coin.data;
            this.$emit('addAlert', coin.data)
            this.done = true;
          })
          .finally(() => this.loading = false)
    }
  },
  computed: {
    percentage() {
      if (!this.coin) return null;
      return ((Number(this.price) - this.coin.market_data.current_price.eur) / Number(this.price) * 100.0).toFixed(2);
    }
  },
  watch: {
    coin(coin) {
      this.price = coin.market_data.current_price.eur;
    },
    coinSymbol(symbol) {
      coinService.coin(symbol)
          .then(coin => this.coin = coin);
    },
    search: debounce(function (q) {
      if (this.loadingCoins) return;
      this.loadingCoins = true;
      coinService.search({q})
          .then(coins => this.items = coins)
          .finally(() => this.loadingCoins = false)
    }, 350),
  },
  filters: {
    fixed(price) {
      return price.toFixed(2)
    },
    date(date) {
      return moment(date).format('DD/MM/YYYY, HH:mm:ss')
    }
  }
}
</script>