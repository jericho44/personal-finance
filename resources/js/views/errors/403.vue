<template>
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - 404 Page-->
        <div class="d-flex flex-column flex-center mt-10 flex-column-fluid p-10">                    
            <!--begin::Message-->
            <h1 class="fw-bold mb-10" style="color: #A3A3C7">You Don't Have Access To The Page</h1>
            <!--end::Message-->
            <!--begin::Link-->           
            <router-link to="/" class="btn btn-primary">Return Home Page</router-link>                
            <!--end::Link-->
        </div>
        <!--end::Authentication - 404 Page-->
    </div>
</template>

<script>
    export default {
        data() {
            return {                           
            }

        },
    }

</script>
