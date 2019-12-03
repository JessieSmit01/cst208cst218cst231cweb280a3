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


<!--        TODO: this button is not going to stay here. TESTING PURPOSES ONLY -->
        <b-button @click="refresh">Refresh Table</b-button>
        <!-- TABLE PROPERTIES: all from https://bootstrap-vue.js.org/docs/components/table
            ref: so the refresh method knows what to refer to,
            striped: cosmetic, makes it look nice, hover: gives it a little styling change on hover
            head-variant: looks nice, contrast!
            no-provider-sorting: enables local sorting
            primary-key: tell it the primary key so it can optimize rendering (else it uses the row index number that changes when sorting
            transition-props: tell it which bootstrap transition to use
            fields: which columns to show
            isBusy: link to boolean indicating if the data ia still loading
            items: link to the provider to tell it to get the data from there
         -->
        <b-table
                ref="table"
                striped
                hover
                head-variant="dark"
                no-provider-sorting
                primary-key="vehicleID"
                :tbody-transition-props="transProps"
                :fields="fields"
                :filter="filter"
                :busy="isBusy"
                :items="provider"
        >
            <!-- A custom formatted header cell for the actions column
                the "head" links to the appropriate field, "data" to the table's data, click to the add method, rest is formatting
                names of colour variants: https://bootstrap-vue.js.org/docs/reference/color-variants/
            -->
            <!-- https://bootstrap-vue.js.org/docs/components/table/#header-and-footer-custom-rendering-via-scoped-slots -->
            <template v-slot:head(actions)="data" >
                <b-button
                        @click="edit({})"
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
                //https://bootstrap-vue.js.org/docs/components/table/#automated-table-busy-state
                //do not actually need to set this anywhere when using a provider
                isBusy: false, //is true when the data is loading.
                transProps: { //set the name of the table transition
                    name: 'flip-list'
                },
                filter: '', //the search string to filter on
                fields: [ //the fields to display
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
                        label: 'icon here'
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
            provider() {
                //The provider automatically handles the isBusy
                //(ie: sets it true at the beginning of the method, and false at the end), so no need to directly code it

                //create a promise that is the axios get method to the api. No search currently implemented, so we are not sending in a search term
                let promise = axios.get('vehicle-api.php', {params: {searchfor:this.filter}});

                //return the promise where the response is what returns from the API, or empty array on error
                return promise.then(response => {
                    //on success, return the data from the response to be set to the items
                    return response.data;

                }).catch(errors => {
                    //TODO: Probably do some error handling
                    console.log(errors);
                    return [];
                });

            },
            /**
             * event called when a vehicle's edit button is clicked
             * emits the edit event with the vehicle
             * @param vehicle
             */
            edit(vehicle) {
                this.$emit('edit', vehicle);
            },

            // /**
            //  * event called when the add button is clicked. It just emits an add event.
            //  */
            // add() {
            //     this.$emit('add', {});
            // },

            /**
             * forces a refresh of the table data
             * looks at the refs, which we have set tha table as "table" so it refreshes that item
             * https://bootstrap-vue.js.org/docs/components/table/#force-refreshing-of-table-data
             */
            refresh()
            {
                this.$refs.table.refresh();
            }
        }

    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform .3s;
    }

</style>