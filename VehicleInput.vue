<template>
<b-modal title="Create Vehicle" v-model="modalShown" @shown="" hide-footer>
    <label>Make:</label>
    <b-form-group :invalid-feedback="errors.make" :state="states.make">
        <b-form-input v-model="vehicle.make" :state="states.make" trim @keyDown="errors.make=null"></b-form-input>
    </b-form-group>

    <label>Model:</label>
    <b-form-group :invalid-feedback="errors.model" :state="states.model">
        <b-form-input v-model="vehicle.model" :state="states.model" trim @keyDown="errors.model=null"></b-form-input>
    </b-form-group>

    <label>Year:</label>
    <b-form-group :invalid-feedback="errors.year" :state="states.year">
        <b-form-input type="number" v-model="vehicle.year" :state="states.year" trim @keyDown="errors.year=null"></b-form-input>
    </b-form-group>

    <label>Type:</label>
    <b-form-group :invalid-feedback="errors.type" :state="states.type">
        <b-form-radio-group>
            <b-form-radio v-model="vehicle.type" name="vehicleType" value="sedan">Sedan</b-form-radio>
            <b-form-radio v-model="vehicle.type" name="vehicleType" value="compact">Compact</b-form-radio>
            <b-form-radio v-model="vehicle.type" name="vehicleType" value="cross over">Cross Over</b-form-radio>
            <b-form-radio v-model="vehicle.type" name="vehicleType" value="truck">Truck</b-form-radio>
        </b-form-radio-group>
    </b-form-group>

    <button class="btn btn-primary far fa-save" title="Save" @click.stop="saveVehicle"/>
</b-modal>
</template>

<script>
    module.exports= {
        props: {
            vehicle: {
                type: Object,
                default: ()=>({vehicleID:null,make:"",model:"",type:"",year:0})
            },
            modalShown: {
                type: Boolean,
                default: ()=>(true)
            },
            editMode:{
                type: Boolean,
                default:()=>(false)
            }
        },
        data: function() {
            return {
                newVehicle: Object.assign({}, this.vehicle),
                errors: {},
                status: {code:0}
            }
        },
        methods: {
            saveVehicle: function() {
                this.errors = {vehicleID:null,make:null,model:null,type:null,year:null};
                this.status.code = -1;
                this.$emit('save', this.newVehicle, this.errors, this.status);
            }
        },
        computed: {
            states: function() {
                return {
                    make: this.errors.make ? false : null,
                    model: this.errors.model ? false : null,
                    year: this.errors.year ? false : null,
                    type: this.errors.type ? false : null
                }
            }
        },
        watch: {

        }
    }
</script>

<style type="text/css" scoped>

</style>