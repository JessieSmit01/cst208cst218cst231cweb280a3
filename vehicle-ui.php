
<!--/**************************************-->
<!-- * File Name: Vehicle.php-->
<!-- * User: cst208 Tara Epp, cst218 Carson Kearns, Jessie Smith-->
<!-- * Date: 2019-11-11/27/2019-->
<!-- * Project: CWEB280 A3-->
<!-- *-->
<!-- * the main user interface for managing vehicles-->
<!-- * ASSIGN TO: all 3-->
<!-- *-->
<!-- **************************************/-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
	materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/http-vue-loader/src/httpVueLoader.js"></script>

    <title>$Title$</title>
</head>
<body>

<div id="managed_by_vue_js">
    <vehicle-table :vehicles="vehicles" :key="vehicleID"></vehicle-table>
</div>


<script>

    new Vue({
        el: '#managed_by_vue_js',
        data: {
            vehicles: [],
            axiosResult: {}
        },
        methods: {
            getData: function () {
                axios.get('data-api.php', {params: {}})
                    .then(response => {
                        this.axiosResult = response;//ONLY FOR DEBUG
                    })
                    .catch(errors => {
                        this.axiosResult = errors;//ONLY FOR DEBUG
                    })
                    .finally()
            }
        },
        components: {
            'VehicleTable': httpVueLoader('./VehicleTable.vue')
        },
        mounted() {
            this.getData();
        }
    });
</script>

</body>
</html>

