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
                 no-provider-sorting
                 primary-key="vehicleID"
                 :fields="fields"
                 :busy="isBusy"
                 :items="provider"
        >
            <!-- A virtual column for the "actions"-->
            <template v-slot:cell(actions)="data">
                <b-button @click="edit(data.item)" class="btn btn-primary fas fa-edit" title="Edit"></b-button>
            </template>

<!--            a spinner!-->
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>


        </b-table>
    </div>
</template>

<script>
    module.exports = {
        name: "VehicleTable",
        props: { //data passed in when creating the component
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
            provider() {
                let promise = axios.get('vehicle-api.php', {params: {searchfor:''}});

                return promise.then(response => {
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
            edit(vehicle) {
                this.$emit('edit', vehicle);
            }
        }

    }
</script>

<style scoped>

</style>