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
    <vehicle-table :vehicles="vehicles" @edit="editVehicle" @add="sendVehicle"></vehicle-table>

<!-- the b-modal component -->
    <vehicle-input :vehicle="vehicle" :modal-shown="showModalFromComponent" @save="sendVehicle"></vehicle-input>

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
            sqlDebug: '',
            showModalFromComponent: false,
            vehicle: {}
        },
        methods: {
            getData: function () {
                axios.get('vehicle-api.php', {params: {searchfor:this.searchString}})
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
            editVehicle: function(vehicle) {
                // this is called from VehicleTable.vue
                this.showModalFromComponent = true;
                this.vehicle = vehicle;
                // this.vehicle = {make: vehicle.make, model: vehicle.model, type: vehicle.type, year: vehicle.year};
            },
            sendVehicle: function(vehicle, errorMessages, status) {
                console.log("Send Vehicle:" + vehicle.make);
                axios({
                    method: vehicle.vehicleID ? "put" : "post", // determine which method by whether or not studentID is set
                    url: "vehicle-api.php",
                    data: vehicle
                }).then(response => {
                    this.axiosResult = response;
                    status.code = 1; // let the component know that the student was successfully added to the database
                    if(response.status == 201) // created and added to database
                    {
                        this.vehicles.push(response.data); // add new student to students array
                    }
                    if(response.status == 200) // student updated in database
                    {
                        // exit edit mode
                        this.editID = 0;
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

