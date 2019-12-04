<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
	materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/http-vue-loader/src/httpVueLoader.js"></script>

    <title>Vehicle UI by cst208, cst218, cst231</title>
</head>
<body>
<div class="jumbotron text-center p-4">
    <h1>Vehicle UI by cst208, cst218, cst231</h1>
</div>

<!-- VUE SECTION-->
<div id="managed_by_vue_js">

<!--    The b-table component -->
    <vehicle-table
            @edit="editVehicle"
            @add="addVehicle"
    ></vehicle-table>

<!-- the b-modal component -->
    <vehicle-input
            :key="vehicle.vehicleID"
            :vehicle="vehicle"
            :title="modalTitle"
            @save="sendVehicle"
    ></vehicle-input>

<!--    DEBUG SECTION... KEEP OR DELETE???-->
    <footer class="row bg-info mt-5">
        <div class="col-sm-7">
            <h3>Vardump Vue Data</h3>
            <pre>{{$data}}</pre>
        </div>
        <div class="col-sm-5">
            <h3>Vardump axios result</h3>
            <pre>{{axiosResult}}</pre>
        </div>
    </footer>
</div>

<script>

    new Vue({
        el: '#managed_by_vue_js',
        data: {
            axiosResult: {}, //debug purposes
            searchString: '', //string to search by
            sqlDebug: '', //also debug
            vehicle: {}, //current vehicle being added/edited
            modalTitle: '' //what to title the modal
        },
        methods: {
            /**
             * //TODO: This comment doesn't match- should it?
             * open the modal to create a new vehicle
             * no need to send in an object, use the modal's default blank object
             */
            addVehicle: function() {
                //TODO: If you enter data into a new vehicle, cancel out, and then click add again, it will still retain the same data
                this.modalTitle = 'Create Vehicle';
                this.vehicle = {};
            },
            /**
             * open the modal to edit the vehicle
             * @param vehicle: the vehicle to edit
             */
            editVehicle: function(vehicle) {
                // this is called from VehicleTable.vue
                this.modalTitle = 'Edit Vehicle';
                this.vehicle = vehicle; //create a new object from what we received
            },

            /**
             * send the vehicle object to the database
             * @param vehicle: vehicle to send
             * @param errorMessages: do we need this?
             * @param status: or this?
             */
            sendVehicle: function(vehicle, errorMessages, status) {
                axios({
                    method: vehicle.vehicleID ? "put" : "post", // determine which method by whether or not vehicleID is set
                    url: "vehicle-api.php", //send to the API
                    data: vehicle //give it the vehicle object
                }).then(response => { //on success,
                    this.axiosResult = response; //show the axios result in the footer
                    status.code = 1; // let the component know that the vehicle was successfully added to the database

                    this.$bvModal.hide('inputModal'); //and hide the modal: https://bootstrap-vue.js.org/docs/components/modal#using-thisbvmodalshow-and-thisbvmodalhide-instance-methods
                    this.$root.$emit('bv::refresh::table', 'vehicleTable'); //refresh the table: https://bootstrap-vue.js.org/docs/components/table/#force-refreshing-of-table-data

                }).catch(errors => { //and if there are any errors,
                    let response = errors.response;
                    this.axiosResult = response; //show the errors in the footer
                    status.code = 0; // let the component know that it did not save to the database
                    if(response.status == 422) // validation error
                    {
                        Object.assign(errorMessages, response.data); // copy errorMessages to the errors object inside the component
                    }
                    else
                    {
                        if(response.status == 418) // database error - expect debug sql text to be returned
                        {
                            this.sqlDebug = response.data;
                        }
                    }
                });
            }
        },
        components: {
            'VehicleTable': httpVueLoader('./VehicleTable.vue'),
            'VehicleInput' : httpVueLoader('./VehicleInput.vue')
        },
        mounted() {
        }
    });
</script>
</body>
</html>

