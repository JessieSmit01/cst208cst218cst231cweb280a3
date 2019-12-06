<template>
    <!--
        bind the title to the given title
        set the id so the parent can refer to the modal
        when it's loading, disable all of the closing mechanisms
     -->
    <b-modal
            v-bind:title="title"
            id="inputModal"
            @show="resetDefaultVehicleData"
            v-bind:no-close-on-esc="loading"
            v-bind:no-close-on-backdrop="loading"
            v-bind:hide-header-close="loading"
    >
        <b-container>
            <!-- form input for the make of the vehicle -->
            <b-form-group :invalid-feedback="errors.make" :state="states.make" label="Name" :disabled="loading">
                <b-form-input v-model="newVehicle.make" :state="states.make" trim @keyDown="errors.make=null"></b-form-input>
            </b-form-group>

            <!-- form input for the model of the vehicle -->
            <b-form-group :invalid-feedback="errors.model" :state="states.model" label="Model" :disabled="loading">
                <b-form-input v-model="newVehicle.model" :state="states.model" trim @keyDown="errors.model=null"></b-form-input>
            </b-form-group>

            <!-- form input for the year of the vehicle -->
            <b-form-group :invalid-feedback="errors.year" :state="states.year" label="Year" :disabled="loading">
                <b-form-input type="number" min=1896 v-model="newVehicle.year" :state="states.year" trim @keyDown="errors.year=null"></b-form-input>
            </b-form-group>

            <!-- form input for the type of the vehicle, with four radio buttons -->
            <b-form-group :invalid-feedback="errors.type" :state="states.type" label="Type" :disabled="loading">
                <b-form-radio-group v-model="newVehicle.type">
                    <b-form-radio
                            v-for="type in vehicleTypes" v-bind:value="type" v-bind:key="type" name="vehicleType">{{type}}</b-form-radio>
                </b-form-radio-group>
            </b-form-group>
        </b-container>
        <!--        https://bootstrap-vue.js.org/docs/components/modal/#variants: used the example here for the footer template to override the default footer-->
        <template v-slot:modal-footer>
            <!--            When the modal is not loading, show the normal save button-->
            <div v-if="!loading">
                <b-button class="far fa-save" variant="primary" title="Save" @click="saveVehicle"/>
            </div>
            <!--           When the modal is loading, shaw a button with a spinner that says "saving" -->
            <div v-else>
                <b-button variant="primary" title="Save" @click="saveVehicle" :disabled=true><b-spinner class="align-middle"></b-spinner>
                    <strong>Saving...</strong></b-button>
            </div>
        </template>
    </b-modal>
</template>

<script>
    module.exports = {
        props: {
            // the vehicle we are editing
            vehicle: {
                type: Object,
                // default values for the vehicle object
                default: ()=>({
                    vehicleID: null,
                    make: null,
                    model: null,
                    type: null,
                    year: null
                })
            },
            title: {
                //the title of the modal
                type: String,
                default: ()=>"Create Vehicle"
            },
            //track when the modal is loading (ie: after clicking save, and before the data comes back. Set by the parent
            loading: {
                type: Boolean,
                default: ()=> false
            }
        },
        data: function() {
            return {
                // copy data from the passed in vehicle to the newVehicle (temporary) object
                newVehicle: Object.assign({}, this.vehicle),
                errors: {}, //no initial errors
                status: {code: 0}, // status code of 0 means nothing to update
                vehicleTypes: ['Sedan', 'Compact', 'Cross Over', 'Truck'] //valid vehicle types

            }
        },
        methods: {
            /**
             * saving vehicle function,
             * resets all errors,
             * set the status code to be waiting for server, and send the save event back to the parent
             */
            saveVehicle: function() {
                this.errors = {
                    vehicleID: null,
                    make: null,
                    model: null,
                    type: null,
                    year: null
                };
                // status code of -1 means we are waiting to hear back from the server
                this.status.code = -1;
                // newVehicle is connected to the text inputs, so we need to send that object to save new values to the database
                this.$emit('save', this.newVehicle, this.errors, this.status);
            },
            resetDefaultVehicleData: function()
            {
                this.newVehicle = Object.assign({}, this.vehicle);
            }
        },
        computed: {
            /**
             * convert the fact that an error message exists to the proper state of the form inputs
             * if an error exists then set the state to false (which will show error to user)
             * if the error message does not exist (null) then set the state to null (no error message)
             * @returns {{year: *, model: *, type: *, make: *}}
             */
            states: function() {
                //for each input, if there are errors on that field, return false, else return null
                return {
                    make: this.errors.make ? false : null,
                    model: this.errors.model ? false : null,
                    year: this.errors.year ? false : null,
                    type: this.errors.type ? false : null
                }
            }
        }
    }
</script>