<template>
  <v-app>
    <v-app-bar app color="white">
      <v-row justify="center" no-gutters>
        <v-col cols="12" sm="10" lg="8">
            <img
                alt="Kriptomat Logo"
                src="/logo.png"
            />
        </v-col>
      </v-row>
    </v-app-bar>

    <v-main style="background-color: #F8F8F8">
      <v-row justify="center">
        <v-col cols="12" sm="10" lg="8" class="py-10">
          <router-view></router-view>
        </v-col>
      </v-row>
    </v-main>
  </v-app>
</template>

<script>

export default {
  name: 'App',
  metaInfo() {
    return {
      title: "Kriptomat",
      meta: [
        {name: 'title', content: 'Kriptomat'},
      ]
    }
  },
  data: () => ({
    //
  }),
};
</script>
