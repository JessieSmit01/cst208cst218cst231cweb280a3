<template>
    <div>
        <b-col lg="6">
            <b-form-group
                    label="Filter"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    label-for="filterInput"
            >
                <b-input-group size="sm">
                    <!--
                        FILTERING REFERENCE: https://bootstrap-vue.js.org/docs/components/table/#debouncing-filter-criteria-changes <-- but mostly used the "complete" example
                        - set a debounce like we did in class so it doesn't hit the API too hard by searching after every modification
                        - model the search field on the filter property,
                        - it is a search type input,
                        - give it a placeholder,
                        - and match the id with the label
                    -->
                    <b-form-input
                            debounce=500
                            v-model="filter"
                            type="search"
                            id="filterInput"
                            placeholder="Search"
                    ></b-form-input>
                    <!--
                        tack on a button after the search bar
                        clicking on it sets the filter to empty string,
                        and it is disabled when there is no
                     -->
                    <b-input-group-append>
                        <b-button :disabled="!filter" @click="filter = ''" variant="primary">Clear</b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-form-group>
        </b-col>
        <!-- TABLE PROPERTIES: all from https://bootstrap-vue.js.org/docs/components/table
            busy.sync: change the isBusy variable to match the table's busy state
            id: so the refresh method knows what to refer to,
            striped: cosmetic, makes it look nice, hover: gives it a little styling change on hover
            head-variant: looks nice, contrast!
            no-provider-sorting: enables local sorting
            primary-key: tell it the primary key so it can optimize rendering (else it uses the row index number that changes when sorting
            transition-props: tell it which bootstrap transition to use
            fields: which columns to show
            filter: a search string to filter on
            items: link to the provider to tell it to get the data from there
         -->
        <b-table
                :busy.sync="isBusy"
                id="vehicleTable"
                striped
                hover
                head-variant="dark"
                no-provider-sorting
                primary-key="vehicleID"
                :tbody-transition-props="transProps"
                :fields="fields"
                :filter="filter"
                :items="vehicleProvider"
        >
            <!-- A custom formatted header cell for the actions column
                the "head" links to the appropriate field, "data" to the table's data, click to the add method, rest is formatting
                disable the button according to the busy state of the table
                names of colour variants: https://bootstrap-vue.js.org/docs/reference/color-variants/
            -->
            <!-- https://bootstrap-vue.js.org/docs/components/table/#header-and-footer-custom-rendering-via-scoped-slots -->
            <template v-slot:head(actions)="data">
                <b-button
                        v-bind:disabled="isBusy"
                        @click="add()"
                        class="fas fa-plus"
                        title="Add"
                        variant="success"
                ></b-button>
            </template>

            <!-- A virtual column for the "actions"
                has the same links as the header to the field and the data
                button sends the particular data item over to the edit method
            -->
            <template v-slot:cell(actions)="data">
                <b-button
                        @click="edit(data.item)"
                        class="btn btn-primary fas fa-edit"
                        title="Edit"
                        id="btnEdit"
                        variant="medium"
                ></b-button>
            </template>

            <!-- a spinner! This is shown when the table is in the "busy" state, ie: when it is fetching data
                It looks at the busy status of the table, and shows it instead of the table data if it is busy
            -->
            <!-- https://bootstrap-vue.js.org/docs/components/table/#table-busy-state-->
            <template v-slot:table-busy>
                <div class="text-center text-danger">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>


        </b-table>
    </div>
</template>

<script>

    module.exports = {
        name: "VehicleTable", //how to refer to this component
        data() { //all of the data belonging to the table
            return {
                //this had to be added back in so that the add button could be disabled when the table is busy.
                //the .sync in the b-table is so that it goes two ways (ie: the table controls the value of isBusy, matching it to its busy state)
                isBusy: false,
                transProps: { //set the name of the table transition
                    name: 'flip-list'
                },
                filter: '', //the search string to filter on
                fields: [ //the fields to display
                    {
                        key: 'vehicleID',
                        label: 'ID', //change the label
                        sortable: true
                    },
                    {
                        key: 'make', //the rest of these will be Title Cased by vue
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
                        key: 'actions'
                    }
                ]
            }
        },
        methods: {
            /**
             * makes the axios call
             * returns in the form of a promise, which says, "the data is on the way",
             * which when it resolves, will return the data from the response (which is then set to the items)
             * @returns {Promise<any> | Promise<T | Array>}
             */
            vehicleProvider() {
                //https://bootstrap-vue.js.org/docs/components/table/#automated-table-busy-state
                //The provider automatically handles the isBusy
                //(ie: sets it true at the beginning of the method, and false at the end), so no need to directly code it

                //create a promise that is the axios get method to the api. No search currently implemented, so we are not sending in a search term
                let promise = axios.get('vehicle-api.php', {params: {searchfor:this.filter}});

                //return the promise where the response is what returns from the API, or empty array on error
                return promise.then(response => {
                    //on success, return the data from the response to be set to the items
                    return response.data;

                }).catch(errors => {
                    console.log(errors);
                    return [];
                });
            },
            /**
             * event called when a vehicle's edit button is clicked
             * emits the edit event with the vehicle
             * @param vehicle: to be edited
             */
            async edit(vehicle) {
                //emit the edit event, passing along the vehicle
                await this.$emit('edit', vehicle);
                //and then show the modal after the previous line has FINISHED (await)
                //it has to be asynchronous because the two events firing simultaneously was blocking the modal, so have to wait for it
                //to show the modal, reference the iD: https://bootstrap-vue.js.org/docs/components/modal/#using-thisbvmodalshow-and-thisbvmodalhide-instance-methods
                this.$bvModal.show('inputModal');
            },
            /**
             * event called when the add button is clicked. It just emits an add event.
             */
            async add() {
                //the "add" functions fine without being asynchronous, but it's possible that an edge case would break it
                await this.$emit('add' ); //emit the add event
                this.$bvModal.show('inputModal'); //then show the vehicle
            }
        }
    }
</script>

<style scoped>
/*style the table sort transition*/
    .flip-list-move {
        transition: transform .3s;
    }

</style>