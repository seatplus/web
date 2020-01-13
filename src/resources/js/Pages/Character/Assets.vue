<template>
  <Layout>
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">

                  <div class="card" v-for="location_assets in this.getGroupedAssets()">
                      <div class="card-header">
                          <h3 v-if="location_assets[0]['location']" class="card-title">
                              {{location_assets[0]['location']['locatable']['name']}}
                          </h3>
                          <h3 v-else class="card-title">
                              Unknown Structure ({{location_assets[0]['location_id']}})
                          </h3>
                          <span class="float-right">
                                  {{getLocationsVolume(location_assets)}} volume and {{getLoationsItemsCount(location_assets)}} items
                              </span>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body p-0">
                          <table class="table table-responsive-md table-sm">
                              <thead class="thead-light">
                              <tr>
                                  <th>Quantity</th>
                                  <th>Type</th>
                                  <th>Volume</th>
                                  <th>Group</th>
                              </tr>
                              </thead>

                              <tbody v-for="asset in location_assets">
                                <tr>
                                  <td style="width: 5%">
                                      <div v-if="asset.content[0]">
                                          <asset-button :asset="asset"></asset-button>
                                      </div>
                                      <div v-else>
                                          {{ asset.quantity }}
                                      </div>

                                  </td>
                                  <td style="width: 55%">
                                      <b-media vertical-align="center">
                                          <template v-slot:aside>
                                              <EveImage v-if="$page.numer_of_owner > 1" :object="asset.owner" :size=32></EveImage>
                                              <EveImage :object="asset.type" :size=32></EveImage>
                                          </template>
                                          <span v-if="asset.name">
                                              {{asset.name}} ({{ asset.type.name }})
                                          </span>

                                          <span v-else>
                                              {{ asset.type.name }}
                                          </span>

                                          <span v-if="!asset.is_singleton" class="text-info">(packaged)</span>
                                      </b-media>

                                  </td>
                                  <td style="width: 20%">{{getMetricPrefix(asset.quantity * asset.type.volume)}}</td>
                                  <td style="width: 20%">{{asset.type.group.name}}</td>
                              </tr>

                                <b-collapse v-if="asset.content[0]"
                                            v-for="content in asset.content" :key="asset.content.item_id"
                                            :id="content.location_id.toString()" tag="tr" style="background-color: #f2f2f2">

                                    <td>
                                        <div v-if="content.content[0]">
                                            <b-button variant="link" size="sm" v-b-toggle="content.content[0].location_id">
                                                <i class="fas fa-plus"></i>
                                            </b-button>
                                        </div>
                                        <div v-else>
                                            {{ content.quantity }}
                                        </div>

                                    </td>
                                    <td>
                                        <b-media vertical-align="center">
                                            <template v-slot:aside>
                                                <EveImage v-if="$page.numer_of_owner > 1" :object="content.owner" :size=32></EveImage>
                                                <EveImage :object="content.type" :size=32></EveImage>
                                            </template>
                                            <span v-if="content.name">
                                              {{content.name}} ({{ content.type.name }})
                                          </span>

                                            <span v-else>
                                              {{ content.type.name }}
                                          </span>
                                        </b-media>

                                    </td>
                                    <td>{{getMetricPrefix(content.quantity * content.type.volume)}}</td>
                                    <td>{{content.type.group.name}}</td>

                                </b-collapse>

                              </tbody>
                          </table>
                      </div>
                      <!-- /.card-body -->
                  </div>

                  <pagination :collection="assets"></pagination>

              </div>
          </div>
      </div>

  </Layout>
</template>

<script>
  import Layout from "../../Shared/Layout"
  import EveImage from "../../Shared/EveImage"
  import AssetButton from "./AssetButton"
  import Pagination from "../../Shared/Pagination"
  export default {
    name: "Assets",
    components: {Layout, EveImage, AssetButton, Pagination},
    props: {
      assets: Object,
    },
    methods: {
      getGroupedAssets() {

        return _.groupBy(this.assets.data, 'location_id')
      },
      getMetricPrefix(numeric_value) {

        const  { prefix } = require('metric-prefix')

        return prefix(numeric_value, { precision: 3, unit: 'm³'})
      },
      getLocationsVolume(location_assets) {

        function volume(object) {
          return object.quantity * object.type.volume;
        }

        const  { prefix } = require('metric-prefix')

        return prefix(_.sum(_.map(location_assets,volume)), { precision: 3, unit: 'm³'})
      },
      getLoationsItemsCount(location_assets) {

        return _.size(location_assets)
      }
    }
  }
</script>

<style scoped>

</style>
