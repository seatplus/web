<template>
  <div :class="['grid grid-cols-1 gap-6 mt-6 sm:mt-5', {'sm:grid-cols-2': gotSecondColumn}]">
    <div class="space-y-4">
      <LocationSlot
        name="Highslots"
        :items="items"
        :slots="highslots"
      />
      <LocationSlot
        name="Midslots"
        :items="items"
        :slots="midslots"
      />
      <LocationSlot
        name="Lowslots"
        :items="items"
        :slots="lowslots"
      />
      <LocationSlot
        name="Rigslots"
        :items="items"
        :slots="rigslots"
      />
      <LocationSlot
        name="Subsystems"
        :items="items"
        :slots="subsystems"
      />
    </div>
    <div
      v-if="gotSecondColumn"
      class="space-y-4"
    >
      <LocationSlot
        name="Fighter Tubes"
        :items="items"
        :slots="fighter_tubes"
      />
      <LocationSlot
        name="Fleet Hangar"
        :items="items"
        :slots="fleet_hangar"
      />
      <LocationSlot
        name="Ship Hangar"
        :items="items"
        :slots="ship_hangar"
      />
    </div>
    <div class="space-y-4 col-span-1 md:col-span-2">
      <LocationSlot
        name="Specialized Hold"
        :items="items"
        :slots="specialized"
      />
      <LocationSlot
        name="Drone/Fighter Bay"
        :items="items"
        :slots="dronebay"
      />
      <LocationSlot
        name="Cargo"
        :items="items"
        :slots="everything_else"
      />
    </div>
  </div>
</template>

<script>
import LocationSlot from "./LocationSlot";
export default {
    name: "ItemLayout",
    components: {LocationSlot},
    props: {
        items: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            highslots: ['HiSlot0', 'HiSlot1', 'HiSlot2', 'HiSlot3', 'HiSlot4', 'HiSlot5', 'HiSlot6', 'HiSlot7'],
            midslots: ['MedSlot0', 'MedSlot1', 'MedSlot2', 'MedSlot3', 'MedSlot4', 'MedSlot5', 'MedSlot6', 'MedSlot7'],
            lowslots: ['LoSlot0', 'LoSlot1', 'LoSlot2', 'LoSlot3', 'LoSlot4', 'LoSlot5', 'LoSlot6', 'LoSlot7'],
            rigslots: ['RigSlot0', 'RigSlot1', 'RigSlot2', 'RigSlot3', 'RigSlot4', 'RigSlot5', 'RigSlot6', 'RigSlot7'],
            subsystems: ['SubSystemSlot0', 'SubSystemSlot1', 'SubSystemSlot2', 'SubSystemSlot3', 'SubSystemSlot4', 'SubSystemSlot5', 'SubSystemSlot6', 'SubSystemSlot7'],
            fleet_hangar: ['FleetHangar'],
            ship_hangar: ['ShipHangar'],
            fighter_tubes: ['FighterTube0', 'FighterTube1', 'FighterTube2', 'FighterTube3', 'FighterTube4'],
            dronebay: ['DroneBay', 'FighterBay'],
            specialized: [
                'SpecializedAmmoHold', 'SpecializedCommandCenterHold', 'SpecializedFuelBay', 'SpecializedGasHold',
                'SpecializedIndustrialShipHold', 'SpecializedLargeShipHold', 'SpecializedMaterialBay', 'SpecializedMediumShipHold',
                'SpecializedMineralHold', 'SpecializedOreHold', 'SpecializedPlanetaryCommoditiesHold', 'SpecializedSalvageHold',
                'SpecializedShipHold', 'SpecializedSmallShipHold', 'SubSystemBay'
            ],
            everything_else: [
                'AssetSafety', 'AutoFit', 'BoosterBay', 'Cargo', 'CorpseBay', 'Deliveries', 'FrigateEscapeBay',
                'HiddenModifiers', 'Implant', 'Locked',  'QuafeBay',  'Skill', 'Unlocked', 'Wardrobe', 'Hangar', 'HangarAll'
            ]
        }
    },
    computed: {
        gotSecondColumn() {
            let possible_items = [...this.fighter_tubes, ...this.fleet_hangar, ...this.ship_hangar]

            return !_.isUndefined(_.find(this.items, (item) => possible_items.includes(item.location_flag)))
        }
    }
}
</script>

<style scoped>

</style>
