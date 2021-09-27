<template>
  <div>
    <h1 class="secondary--text font-weight-medium" style="font-size: 40px">Cryptocurrency Prices</h1>
    <v-data-table
        hide-default-footer
        :headers="headers"
        :items="coins"
        :items-per-page="-1"
        :loading="loading"
        class="rounded my-8 coins-table">
      <template v-slot:item.currency="{item}">
        <v-row align="center" no-gutters>
          <v-btn icon @click="toggleFavorite(item)">
            <v-icon :color="isFavorite(item) ? 'yellow darken-3' : 'grey lighten-1'">
              {{ isFavorite(item) ? 'mdi-star' : 'mdi-star-outline' }}
            </v-icon>
          </v-btn>
          <div class="mx-2">
            <v-img :src="item.image" height="40px" width="40px"></v-img>
          </div>
          <router-link
              :to="{name: 'coin-id', params: {id: item.id}}"
              class="secondary--text font-weight-medium text-decoration-none">
            {{ item.name }}
          </router-link>
          <span class="grey--text font-weight-medium ml-1 text-uppercase">{{ item.symbol }}</span>
        </v-row>
      </template>
      <template v-slot:item.current_price="{item}">
        <span>
          {{ item.current_price | fixed }} EUR
        </span>
      </template>
      <template v-slot:item.price_change_percentage_24h="{item}">
        <span
            :class="item.price_change_percentage_24h < 0 ? 'red--text' : item.price_change_percentage_24h > 0 ? 'green--text' : 'secondary--text'">
          {{ item.price_change_percentage_24h | fixed }}%
        </span>
      </template>
      <template v-slot:item.market_cap="{item}">
        <span>
          {{ item.market_cap | currency }} EUR
        </span>
      </template>
      <template v-slot:item.total_volume="{item}">
        <span>
          {{ item.total_volume | currency }} EUR
        </span>
      </template>
      <template v-slot:item.graph="{item}">
        <graph :data="item.chart"></graph>
      </template>
    </v-data-table>

    <infinite-load @infinite="loadMoreCoins" v-if="coins.length >= 10">
      <span slot="no-more"></span>
    </infinite-load>
  </div>
</template>

<script>
import hasFavorite from "@/mixins/hasFavorite";

import InfiniteLoad from "@/components/global/InfiniteLoad";
import Graph from "@/components/Graph";

export default {
  mixins: [hasFavorite],
  components: {Graph, InfiniteLoad},
  data() {
    return {
      loading: true,
      headers: [
        {text: 'Currency', value: 'currency'},
        {text: 'Price', value: 'current_price', align: 'right'},
        {text: 'Change 24h', value: 'price_change_percentage_24h', align: 'right'},
        {text: 'Market cap', value: 'market_cap', align: 'right'},
        {text: 'Volume 24h', value: 'total_volume', align: 'right'},
        {text: 'Price graph 7d', value: 'graph', align: 'right', width: '220px'},
      ]
    }
  },
  created() {
    this.$store.dispatch('coin/getCoins').finally(() => this.loading = false);
  },
  methods: {
    loadMoreCoins($state) {
      this.$store.dispatch('coin/getCoins', false)
          .then(() => $state.loaded())
          .finally(() => $state.loaded())
    }
  },
  computed: {
    coins() {
      return this.$store.state.coin.coins;
    }
  },
  filters: {
    currency(price) {
      const billion = 1000 * 1000 * 1000;
      const million = 1000 * 1000;
      if (price > billion) return (price / billion).toFixed(2) + 'B'
      else if (price > million) return (price / million).toFixed(0) + 'M'
      return price.toFixed(2)
    },
    fixed(price) {
      return price.toFixed(2)
    }
  }
}
</script>

<style lang="scss">
.theme--light.v-data-table .v-data-table-header th.sortable:hover,
.theme--light.v-data-table .v-data-table-header th.sortable.active,
.theme--light.v-data-table .v-data-table-header th.sortable.active .v-data-table-header__icon {
  color: #009EFC !important;
}

.theme--light.v-data-table .v-data-table-header th.sortable.active .v-data-table-header__icon {
  display: inline-block !important;
}

.coins-table {
  tbody {
    td {
      height: 80px !important;
    }
  }
}

.v-data-table-header__icon {
  display: none !important;
}

.small {
  max-width: 400px;
}
</style>