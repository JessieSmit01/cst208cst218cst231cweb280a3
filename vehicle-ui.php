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
            :vehicles="vehicles"
            @edit="editVehicle"
            @add="addVehicle"
    ></vehicle-table>

<!-- the b-modal component -->
    <vehicle-input
            :key="vehicle.vehicleID"
            :vehicle="vehicle"
            :modal-shown="showModalFromComponent"
            @save="sendVehicle"
            @cancel="vehicle = {}"
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
            vehicles: [],
            axiosResult: {}, //debug purposes
            searchString: '', //string to search by
            sqlDebug: '',
            showModalFromComponent: false,
            vehicle: {}
        },
        methods: {
            getData: function () {
                axios.get('vehicle-api.php', {params: {searchfor: this.searchString}})
                    .then(response => {

                        this.vehicles = response.data;
                        this.axiosResult = response;//ONLY FOR DEBUG
                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = errors;//ONLY FOR DEBUG
                        if(response.status == 404) //error code for nothing found
                        {
                            this.vehicles = []; //nothing found so set vehicles to empty array
                        }
                        else if(response.status == 418) //error for 'Im a Teapot" -means an sql error occurred
                        {
                            this.sqlDebug = response.data;
                        }
                    })
                    .finally()
            },
            /**
             * open the modal to create a new vehicle
             * no need to send in an object, use the modal's default blank object
             */
            addVehicle: function() {
                this.showModalFromComponent = true; //show the modal
            },
            /**
             * open the modal to edit the vehicle
             * @param vehicle: the vehicle to edit
             */
            editVehicle: function(vehicle) {
                // this is called from VehicleTable.vue
                this.showModalFromComponent = true; //show the modal
                this.vehicle = Object.assign({}, vehicle); //create a new object from what we received
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
                    url: "vehicle-api.php",
                    data: vehicle
                }).then(response => {
                    this.axiosResult = response;
                    status.code = 1; // let the component know that the vehicle was successfully added to the database
                    if(response.status == 201) // created and added to database
                    {
                        this.vehicles.push(response.data); // add new vehicle to vehicles array
                    }
                    if(response.status == 200) // vehicle updated in database
                    {
                        //update the edited vehicle
                        this.vehicles[this.vehicles.findIndex(v => v.vehicleID === vehicle.vehicleID)] = response.data;
                        // exit edit mode
                        this.editID = 0; //TODO: We don't actually have an editID. Something else needs to be done
                    }
                }).catch(errors => {
                    let response = errors.response;
                    this.axiosResult = response;
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
            this.getData();
        }
    });
</script>
</body>
</html>

