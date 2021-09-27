<script>
import {Line} from 'vue-chartjs'

export default {
  // mixins: [Line],
  props: {
    data: {
      type: Object,
      required: true
    }
  },
  extends: Line,
  data: () => ({
    options: {
      responsive: true,
      maintainAspectRatio: false,
      elements: {
        point:{
          radius: 0
        },
        line: {
          tension: 0 // disables bezier curves
        }
      },
      fill: 'transparent',
      scales: {
        xAxes: [{
          gridLines: {
            display:false,
            drawOnChartArea: false
          },
          ticks: {
            display: false //this will remove only the label
          }
        }],
        yAxes: [{
          gridLines: {
            display:false,
            drawOnChartArea: false
          },
          ticks: {
            display: false //this will remove only the label
          }
        }],
      },
      height: '40px',
      legend: {
        display: false
      },
    }
  }),
  mounted() {
    const labels = this.data.prices.map(price => price[0]);
    const data = this.data.prices.map(price => price[1]);
    const chartData = {
      labels,
      datasets: [
        {
          fill: false,
          label: 'Price',
          backgroundColor: '#009EFC',
          borderColor: '#009EFC',
          data
        }
      ]
    }

    this.renderChart(chartData, this.options)
  }
}
</script>

<style>
  .coins-table canvas{
    width:200px !important;
    height:60px !important;
  }
  .coin-graph canvas{
    height:300px !important;
  }
</style>