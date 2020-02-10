<template>
    <Layout>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Main character</h3>
                </div>
                <div class="card-body table-responsive p-0">

                        <table class="table table-hover">

                            <tbody>
                            <tr v-for="character in this.user.data.characters"
                                :key="character.character_id"
                                :class="{ 'table-primary': isActive(character.character_id)}"
                                @click="updateMainCharacter(character.character_id)"
                            >
                                <td>
                                    <div class="d-flex">
                                        <div class="p-1"><EveImage :object="character" :size="32" /></div>
                                        <div class="p-1 align-self-center">{{character.name}}</div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center">
                            <a :href="route('auth.eve')" class="btn btn-app">
                                <i class="fas fa-user-plus"/>
                                add more characters
                            </a>
                            <div class="p-1 align-self-center"></div>
                        </div>

                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import Layout from "../../Shared/Layout"
    import EveImage from "../../Shared/EveImage"
    import {Inertia} from "@inertiajs/inertia"
    export default {
        name: "UserSettings",
        components: {Layout, EveImage},
        props: {
            user: {
                typer: Object,
                required: true
            }
        },
        data()  {
            return {
                mainId: this.user.data.main_character.character_id
            }
        },
        methods: {
            isActive(characterId) {
                return characterId === this.mainId
            },
            updateMainCharacter(characterID) {

                if(characterID !== this.mainId)
                    return Inertia.post(route('update.main_character'),{ character_id: characterID});
            }
        },
        watch: {
            user: function () {
                this.mainId = this.user.data.main_character.character_id;
            }
        }
    }
</script>

<style scoped>

</style>
