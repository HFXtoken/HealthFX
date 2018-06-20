<template>
    <div>
        <a href="javascript:;" class="bkAppntmtBtn" @click="bookProvider(provider)">{{ trans('frontend.common.btn_appointment') }}</a>
        <a href="javascript:;" class="drDetEnquireBtn" @click="queryProvider(provider)">{{ trans('frontend.common.btn_enquire') }}</a>
    </div>
</template>

<script>

	import { EventBus } from '../bundle.js';
    import { mapGetters, mapMutations } from 'vuex'

    export default {

        name: "book-enquire",

        props: ['type'] ,

        data() {

        	return {
                provider: {},
                bookingModalName: this.type === 'doctor' ? 'SHOW_DOCTOR_BOOKING_MODAL' : 'SHOW_FACILITY_BOOKING_MODAL',
                queryModalName: this.type === 'doctor' ? 'SHOW_DOCTOR_ENQUIRY_MODAL' : 'SHOW_FACILITY_ENQUIRY_MODAL',
            }
        },

        computed: {
            ...mapGetters(['isUserLoggedIn'])
        },


        methods: {
        	...mapMutations(['setNextAction']),

            bookProvider(provider) {
                if(this.isUserLoggedIn) {
                    EventBus.$emit(this.bookingModalName, {data: provider});
                } else {
                    this.setNextAction({name: this.bookingModalName, data: provider});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },

            queryProvider(provider) {
                if(this.isUserLoggedIn) {
                    EventBus.$emit(this.queryModalName, {data: provider});
                } else {
                    this.setNextAction({name: this.queryModalName, data: provider});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },
        },

        mounted() {
        	EventBus.$on('PROVIDER_DATA', data => this.provider = data);
        }


    }
</script>