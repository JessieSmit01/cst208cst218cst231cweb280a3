<template>
<b-modal title="Create Vehicle" v-model="modalShown" @shown="fixBackdrop" hide-footer>
    <b-form-group :invalid-feedback="errors.make" :state="states.make">
        <b-form-input v-model="newStudent.make" :state="states.make" trim @keyDown="errors.make=null"></b-form-input>
    </b-form-group>

    <div class="form-group">
        <label>Make:
            <input class="form-control" type="text" />
        </label>
    </div>

    <div class="form-group">
        <label>Model:
            <input class="form-control" type="text" />
        </label>
    </div>

    <div class="form-group">
        <label>Type:</label>
        <div class="form-check">
            <label><input type="radio" name="vehicleType" value="Sedan" /> Sedan</label>
        </div>
        <div class="form-check">
            <label><input type="radio" name="vehicleType" value="Compact" /> Compact</label>
        </div>
        <div class="form-check">
            <label><input type="radio" name="vehicleType" value="Cross Over" /> Cross Over</label>
        </div>
        <div class="form-check">
            <label><input type="radio" name="vehicleType" value="Truck" /> Truck</label>
        </div>
    </div>

    <div class="form-group">
        <label>Year:
            <input class="form-control" type="number" />
        </label>
    </div>
</b-modal>
</template>

<script>
    module.exports={
        props:{
            vehicle:{
                type:Object,
                default:()=>({vehicleID:null,make:"",model:"",type:"",year:0})
            },
            modalShown:{
                type:Boolean,
                default:()=>(true)
            }
        },
        data:function(){
            return{
                newVehicle:Object.assign({}, this.vehicle),
                errors:{},
                status:{code:0}
            }
        },
        methods:{
            fixBackdrop: function() { // this is for the modal
                document.querySelector(".modal-backdrop").classList.add("show");
            }
        },
        computed:{

        },
        watch:{

        }
    }
</script>

<style type="text/css" scoped>

</style>