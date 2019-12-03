<template>
    <!-- title does not currently change whether editing or creating new vehicle -->
    <b-modal title="Create Vehicle" v-model="modalShown" hide-footer>
        <!-- form input for the make of the vehicle -->
        <label>Make:</label>
        <b-form-group :invalid-feedback="errors.make" :state="states.make">
            <b-form-input v-model="vehicle.make" :state="states.make" trim @keyDown="errors.make=null"></b-form-input>
        </b-form-group>

        <!-- form input for the model of the vehicle -->
        <label>Model:</label>
        <b-form-group :invalid-feedback="errors.model" :state="states.model">
            <b-form-input v-model="vehicle.model" :state="states.model" trim @keyDown="errors.model=null"></b-form-input>
        </b-form-group>

        <!-- form input for the year of the vehicle -->
        <label>Year:</label>
        <b-form-group :invalid-feedback="errors.year" :state="states.year">
            <b-form-input type="number" v-model="vehicle.year" :state="states.year" trim @keyDown="errors.year=null"></b-form-input>
        </b-form-group>

        <!-- form input for the type of the vehicle, with four radio buttons -->
        <label>Type:</label>
        <b-form-group :invalid-feedback="errors.type" :state="states.type">
            <b-form-radio-group v-model="vehicle.type">
                <b-form-radio name="vehicleType" value="Sedan">Sedan</b-form-radio>
                <b-form-radio name="vehicleType" value="Compact">Compact</b-form-radio>
                <b-form-radio name="vehicleType" value="Cross Over">Cross Over</b-form-radio>
                <b-form-radio name="vehicleType" value="Truck">Truck</b-form-radio>
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
                    make: "",
                    model: "",
                    type: "",
                    year: 0
                })
            },
            // determine whether the model is shown or not, may not be necessary
            modalShown: {
                type: Boolean,
                // default value of true, to show it
                default: ()=>(false)
            }
        },
        data: function() {
            return {
                // copy data from the passed in student to the newVehicle (temporary) object
                //TODO: See if we can get the "newVehicle functioning properly, because it's definitely better practice
                // newVehicle: Object.assign({}, this.vehicle),
                errors: {},
                // status code of 0 means nothing to update
                status: {code:0}
            }
        },
        methods: {
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
                this.$emit('save', this.vehicle, this.errors, this.status);
            }
        },
        computed: {
            states: function() {
                // convert the fact that an error message exists to the proper state of the form inputs
                // if an error exists then set the state to false (which will show error to user)
                // if the error message does not exist (null) then set the state to null (no error message)
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

<style type="text/css" scoped>

</style>