<template>
  <v-row justify="center" align="center">
    <div class="loader-box">
      <div class="loader"></div>
      <v-row no-gutters justify="center" align="center" style="width: 90px;height: 90px">
        <img src="/logo_transparent.png" height="48px" width="48px"/>
      </v-row>
    </div>
  </v-row>
</template>

<style lang="sass">
.loader-box
  height: 200px
  width: 90px
  position: relative

  .loader
    position: absolute
    border: 8px solid #fff
    border-radius: 50%
    border-top: 8px solid #009EFC
    border-left: 8px solid #009EFC
    width: 90px
    height: 90px
    -webkit-animation: spin 1.8s ease-in-out infinite
    animation: spin 1.8s ease-in-out infinite
    background: #fff

  img
    z-index: 1

  @-webkit-keyframes spin
    0%
      -webkit-transform: rotate(0deg)
    100%
      -webkit-transform: rotate(360deg)

  @keyframes spin
    0%
      transform: rotate(0deg)
    100%
      transform: rotate(360deg)
</style>
