<template>
    <!--    /* Assign to: Tara */-->

    <!--    //contains a b-table that lists and sorts an array of vehicles-->

    <!--    //8.	Create a Vue single file component which uses b-table - see https://bootstrap-vue.js.org/docs/components/table/#table-body-transition-support-->
    <!--    // See Complete Example (right side menu)-->
    <!--    //9.	Must use a promise based provider (that uses axios to load the table) with :items-->
    <!--    //10.	Allow for local sorting on all fields - ( use no-provider-sorting option)-->
    <!--    //11.	Add an ‘actions’ column with an edit button that opens the VehicleInput modal with the form inputs filled with the corresponding vehicle data-->
    <div>

        <b-table
                striped
                hover
                head-variant="dark"
                sticky-header="40%"
                no-provider-sorting
                primary-key="vehicleID"
                :fields="fields"
                :busy="isBusy"
                :items="provider"
        >
            <!-- A custom formatted header cell for the actions column -->
            <!-- https://bootstrap-vue.js.org/docs/components/table/#header-and-footer-custom-rendering-via-scoped-slots-->
            <template v-slot:head(actions)="data" >
                <b-button
                        @click="add"
                        class="fas fa-plus"
                        variant="success"
                ></b-button>
            </template>

            <!-- A virtual column for the "actions"-->
            <template v-slot:cell(actions)="data">
                <b-button
                        @click="edit(data.item)"
                        class="btn btn-primary fas fa-edit"
                        title="Edit"
                        variant="medium"
                ></b-button>
            </template>

            <!-- a spinner! This is shown when the table is in the "busy" state, ie: when it is fetching data-->
            <!-- https://bootstrap-vue.js.org/docs/components/table/#table-busy-state-->
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
                //https://bootstrap-vue.js.org/docs/components/table/#automated-table-busy-state
                //do not actually need to set this anywhere when using a provider
                isBusy: false, //is true when the data is loading.
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
                        label: 'icon here',
                        // variant: 'dark'
                    }
                ]
            }
        },
        methods: {
            /**
             *
             * @returns {Promise<any> | Promise<T | Array>}
             */
            provider(ctx) {
                //Did not need the isBusy! it handled itself
                //we are busy until we get the data back
                // this.isBusy = true;

                //create a promise that is the axios get method to the api
                let promise = axios.get('vehicle-api.php', {params: {searchfor:''}});

                //return the promise where the response is what returns from the PAI
                return promise.then(response => {
                    //on success, return the data from the response to be set to the items
                    return response.data;
                    // return(items)
                }).catch(errors => {
                    //TODO: Probably do some error handling
                    console.log(errors);
                    return [];
                }).finally(
                    //success or error-- we are no longer busy
                    // this.isBusy = false
                );

            },
            /**
             * event called when a vehicle's edit button is clicked
             * emits the edit event with the vehicle
             * @param vehicle
             */
            edit(vehicle) {
                this.$emit('edit', vehicle);
            },

            /**
             * event called when the add button is clicked. It just emits an add event.
             */
            add() {
                this.$emit('add');
            }
        }

    }
</script>

<style scoped>

</style>