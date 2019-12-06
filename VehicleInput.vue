<template>
    <!-- title does not currently change whether editing or creating new vehicle -->
    <!--https://bootstrap-vue.js.org/docs/components/modal/ This website helped me in finding events for the modal.
    I was looking for events to fire when the modal was closed or forced to hide and cancelled.-->
    <b-modal
            v-bind:title="title"
            hide-footer
            ref="input-modal"
            id="inputModal"
            @cancel="resetOriginalValues"
            @close="resetOriginalValues"
            @hide="resetOriginalValues"
    >
        <!-- form input for the make of the vehicle -->
        <label>Make:</label>
        <b-form-group :invalid-feedback="errors.make" :state="states.make">
            <b-form-input v-model="newVehicle.make" :state="states.make" trim @keyDown="errors.make=null"></b-form-input>
        </b-form-group>

        <!-- form input for the model of the vehicle -->
        <label>Model:</label>
        <b-form-group :invalid-feedback="errors.model" :state="states.model">
            <b-form-input v-model="newVehicle.model" :state="states.model" trim @keyDown="errors.model=null"></b-form-input>
        </b-form-group>

        <!-- form input for the year of the vehicle -->
        <label>Year:</label>
        <b-form-group :invalid-feedback="errors.year" :state="states.year">
            <b-form-input type="number" min=1896 v-model="newVehicle.year" :state="states.year" trim @keyDown="errors.year=null"></b-form-input>
        </b-form-group>

        <!-- form input for the type of the vehicle, with four radio buttons -->
        <label>Type:</label>
        <b-form-group :invalid-feedback="errors.type" :state="states.type">
            <b-form-radio-group v-model="newVehicle.type">
                <b-form-radio
                        v-for="type in vehicleTypes" v-bind:value="type" v-bind:key="type" name="vehicleType">{{type}}</b-form-radio>
            </b-form-radio-group>
        </b-form-group>

        <!-- button to save the vehicle, currently doesn't spin or block exiting -->
        <button class="btn btn-primary far fa-save" title="Save" @click.stop="saveVehicle"/>
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
                type: String,
                default: ()=>"Create Vehicle"
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
            resetOriginalValues: function(){
                //Learned in class
                this.newVehicle = Object.assign({}, this.vehicle)
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
        //TODO: We have a style section below- do we need it?
    }
</script>

<style type="text/css" scoped>

</style>