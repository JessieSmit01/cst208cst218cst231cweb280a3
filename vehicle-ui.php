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
    <vehicle-table :vehicles="vehicles" @edit="editVehicle"></vehicle-table>



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
            vehicles: [ ], //{'vehicleID':'12345', 'make':'Ford', 'model':'Mustang', 'type':'Sedan', 'year':1979} - original data
            axiosResult: {}, //debug purposes
            searchString: '', //string to search by
            sqlDebug: '',
        },
        methods: {
            getData: function () {
                axios.get('vehicle-api.php', {params: {searchfor:this.searchString}})
                    .then(response => {
                        // console.log(response);
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
                //THIS IS WHERE YOU TAKE IT ON CARSON
                console.log(vehicle);
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

