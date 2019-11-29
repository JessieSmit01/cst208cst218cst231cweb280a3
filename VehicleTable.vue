<template>
    <!--    /* Assign to: Tara */-->

    <!--    //contains a b-table that lists and sorts an array of vehicles-->

    <!--    //8.	Create a Vue single file component which uses b-table - see https://bootstrap-vue.js.org/docs/components/table/#table-body-transition-support-->
    <!--    // See Complete Example (right side menu)-->
    <!--    //9.	Must use a promise based provider (that uses axios to load the table) with :items-->
    <!--    //10.	Allow for local sorting on all fields - ( use no-provider-sorting option)-->
    <!--    //11.	Add an ‘actions’ column with an edit button that opens the VehicleInput modal with the form inputs filled with the corresponding vehicle data-->
    <div>
        <b-button @click="toggleBusy">Toggle Busy State</b-button>
        <b-table responsive striped hover head-variant="dark" sticky-header="40%"
                 primary-key="vehicleID"
                 :fields="fields"
                 :busy="isBusy"
                 :items="provider"
        >
<!--            :items="provider"-->
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>

            <!-- A virtual column -->
            <template v-slot:cell(actions)="data">
                <b-button @click="edit()" class="btn btn-primary fas fa-edit" title="Edit"></b-button>
            </template>
        </b-table>
    </div>
</template>

<script>
    module.exports = {
        name: "VehicleTable",
        props: {
            vehicles: { //detached from the text inputs, not to be edited directly
                type: Array,
                default: () => (null)
            }
        },
        data() {
            return {
                isBusy: false,
                fields: [
                    {
                        key: 'vehicleID',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'make',
                        sortable: true
                    },
                    {
                        key: 'model',
                        sortable: true
                    },
                    {
                        key: 'type',
                        sortable: true
                    },
                    {
                        key: 'year',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        variant: 'info'
                    }
                ]
            }
        },
        methods: {
            provider(ctx) {
                let promise = axios.get('vehicle-api.php', {params: {searchfor:''}});

                return promise.then(response => {
                    console.log(response.data);
                    const items = response.data;

                    return(items)
                }).catch(errors => {
                    console.log(errors);
                    return [];
                })

            },
            toggleBusy() {
                this.isBusy = !this.isBusy;
            },
            edit() {

                console.log(vehicle + " edit was clicked");
            }
        }

    }
</script>

<style scoped>

</style>