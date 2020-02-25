<template>
    <div>
        <!--TODO: create a list and let user select the propper entry right now its good enough-->
        <input type="text" v-model="corpOrAlliance.name" @input="performSearch">

        <b-form-group label="Select a corporation or alliance">
            <b-form-radio v-model="selected" v-for="corpOrAlliance in corpOrAlliances" :key="corpOrAlliance.id" :value="corpOrAlliance">
                <EveImage :size="16" :object="corpOrAlliance" /> {{corpOrAlliance.name}}
            </b-form-radio>
        </b-form-group>

        <!--<datalist id="corpOrAlliances">
            <option v-for="corpOrAlliance in corpOrAlliances" @select="$emit('corp-or-alliance', corpOrAlliance)"></option>
        </datalist>-->
    </div>
</template>

<script>
    import axios from 'axios';
    import EveImage from "../../Shared/EveImage"

    export default {
        name: "SearchCorpOrAlliance",
        components: {EveImage},
        data() {
            return {
                corpOrAlliance: {
                    name: ''
                },
                corpOrAlliances:[],
                selected: ''
            }
        },
        methods: {
            performSearch() {

                if (this.corpOrAlliance.name.length < 3)
                    return

                axios
                .get(route('search.alliance.corporation', this.corpOrAlliance.name))
                    .then(results => {

                        this.corpOrAlliances = _.map(results.data, (result) => {

                            let object = {
                                id: result.id,
                                name: result.name
                            }

                            if(result.category === 'corporation') {
                                object.corporation_id = result.id
                            } else {
                                object.alliance_id= result.id
                            }

                            return object

                        })
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }

        },
        watch: {
            selected: function() {
                return this.$emit('selected-corp-or-alliance', this.selected)
            }
        }
    }
</script>

<style scoped>

</style>
