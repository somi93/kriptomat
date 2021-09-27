<template>
  <v-card flat>
    <v-card-title>
      <v-row justify="space-between" no-gutters>
        <div>
          <span class="caption grey--text">{{ coin.name }} Price</span><br/>
          <p class="display-1 font-weight-bold mb-0">
            {{ coin.market_data.current_price.eur | fixed }}
            <span class="title font-weight-bold">EUR</span>
          </p>
        </div>
        <div>
          <span class="caption grey--text">Last 24h</span><br/>
          <v-row no-gutters class="mt-1" align="center">
                <span class="font-weight-medium body-2 green--text">
                  <template v-if="coin.market_data.price_change_24h_in_currency.eur > 0">+</template>
                  {{ coin.market_data.price_change_24h_in_currency.eur | fixed }} EUR
                </span>
            <v-chip dark color="green" small class="ml-2">
              <template v-if="coin.market_data.price_change_percentage_24h > 0">+</template>
              {{ coin.market_data.price_change_percentage_24h | fixed }} %
            </v-chip>
          </v-row>
        </div>
        <v-btn-toggle v-model="chartType" active-class="primary--text">
          <v-btn x-small color="#E5F5FE">
            <span>1h</span>
          </v-btn>

          <v-btn x-small color="#E5F5FE">
            <span>24h</span>
          </v-btn>

          <v-btn x-small color="#E5F5FE">
            <span>1w</span>
          </v-btn>

          <v-btn x-small color="#E5F5FE">
            <span>1y</span>
          </v-btn>

          <v-btn x-small color="#E5F5FE">
            <span>All</span>
          </v-btn>
        </v-btn-toggle>
      </v-row>
    </v-card-title>
    <v-row v-if="loading" style="height: 300px" justify="center" align="center">
      <v-progress-circular
          indeterminate
          color="primary"
      ></v-progress-circular>
    </v-row>
    <graph :data="chart" v-else-if="chart"></graph>
    <v-divider></v-divider>
    <v-card-actions>
      <v-row no-gutters>
        <v-col cols="12" lg="3">
          <div>
            <span class="caption grey--text">Total Market cap</span><br/>
            <p class="font-weight-bold title mb-0">
              {{ coin.market_data.market_cap.eur | currency }}
            </p>
          </div>
        </v-col>
        <v-col cols="12" lg="3">
          <div>
            <span class="caption grey--text">Volume (24 hours)</span><br/>
            <p class="font-weight-bold title mb-0">
              {{ coin.market_data.market_cap_change_24h_in_currency.eur | currency }}
            </p>
          </div>
        </v-col>
        <v-col cols="12" lg="3">
          <div>
            <span class="caption grey--text">Circulating supply</span><br/>
            <p class="font-weight-bold title mb-0">
              {{ coin.market_data.circulating_supply | currency }}
            </p>
          </div>
        </v-col>
        <v-col cols="12" lg="3">
          <div>
            <span class="caption grey--text">Popularity on Kriptomat</span><br/>
            <p class="font-weight-bold title mb-0">
              {{ coin.coingecko_rank }}
            </p>
          </div>
        </v-col>
      </v-row>
    </v-card-actions>
  </v-card>
</template>

<script>
import Graph from "@/components/Graph";
import coinService from "@/services/coin";

export default {
  props: {
    coin: {
      type: Object,
      required: true
    }
  },
  components: {
    Graph
  },
  data: () => ({
    chart: null,
    chartType: 1,
    loading: true
  }),
  methods: {
    getChart(params) {
      this.loading = true;
      coinService.chart(this.$route.params.id, params)
          .then(chart => this.chart = chart)
          .finally(() => this.loading = false)
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
  },
  watch: {
    chartType: {
      handler: function (type) {
        switch (type) {
          case 0:
            this.getChart({days: 1});
            break;
          case 1:
            this.getChart({days: 1});
            break;
          case 2:
            this.getChart({days: 7});
            break;
          case 3:
            this.getChart({days: 30});
            break;
          case 4:
            this.getChart({days: 'max'});
            break;
        }
      },
      immediate: true
    }
  }
}
</script>