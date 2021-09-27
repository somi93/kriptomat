<template>
  <v-row v-if="coin">
    <v-col cols="12" lg="9">
      <v-btn exact :to="{name: 'index'}" text color="primary" style="text-transform: none">
        <v-icon>mdi-chevron-left</v-icon>
        Back to Assets
      </v-btn>
      <v-row class="mt-6 mb-4" align="center" no-gutters>
        <div class="mr-4">
          <v-img :src="coin.image.small" height="40px" width="40px"></v-img>
        </div>
        <span class="secondary--text font-weight-medium text-decoration-none">
          {{ coin.name }}
        </span>
        <span class="grey--text font-weight-medium ml-3 text-uppercase">{{ coin.symbol }}</span>
        <v-btn icon @click="toggleFavorite(coin)">
          <v-icon :color="isFavorite(coin) ? 'yellow darken-3' : 'grey lighten-1'">
            {{ isFavorite(coin) ? 'mdi-star' : 'mdi-star-outline' }}
          </v-icon>
        </v-btn>
      </v-row>
      <p class="font-weight-medium secondary--text">Overview</p>
      <coin-table class="coin-graph mb-8" :coin="coin"></coin-table>
      <price-alerts></price-alerts>
    </v-col>
    <v-col cols="12" lg="3">
      <p class="font-weight-medium secondary--text">
        My BTC Wallet
        <v-btn icon small @click="showWallet = !showWallet">
          <v-icon small>{{showWallet ? 'mdi-eye' : 'mdi-eye-off'}}</v-icon>
        </v-btn>
      </p>
      <v-card flat class="pa-2" v-if="showWallet">
        <v-card-text>
          <p class="caption grey--text">Your Balance</p>
          <p class="display-1 font-weight-bold mb-2 secondary--text">
            0.012921
            <span class="title font-weight-bold">BTC</span>
          </p>
          <p class="caption grey--text mb-0">≈ 200.21 EUR</p>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" lg="6">
              <v-btn block color="primary" depressed
                     style="text-transform: none">
                <v-icon class="mr-2" small>mdi-qrcode</v-icon>
                Receive
              </v-btn>
            </v-col>
            <v-col cols="12" lg="6">
              <v-btn block color="primary" outlined
                     style="text-transform: none">
                <v-icon class="mr-2" small>mdi-send</v-icon>
                Send
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import hasFavorite from "@/mixins/hasFavorite";

import coinService from "@/services/coin";

import CoinTable from "@/components/coin/Table";
import PriceAlerts from "@/components/coin/PriceAlerts";

export default {
  mixins: [hasFavorite],
  components: {
    PriceAlerts,
    CoinTable
  },
  metaInfo() {
    const title = this.coin ? this.coin.name : ''
    return {
      title: title + ' | Kriptomat',
      meta: [
        {name: 'title', content: title + ' | Kriptomat'},
      ]
    }
  },
  data: () => ({
    coin: null,
    showWallet: true
  }),
  created() {
    coinService.coin(this.$route.params.id)
        .then(coin => this.coin = coin);
  },
}
</script>