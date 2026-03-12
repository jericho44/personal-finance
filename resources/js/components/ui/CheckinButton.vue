<template>
    <div class="d-flex justify-content-center">
        <button @click="this.$router.push({name: 'l-scan'})" id="btn-checkin" class="btn px-10 py-5" style="background-color: #479C2B; color:white; border-radius:50px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_1717_7106)"><path d="M4 7V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H8" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20H8" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 4H18C18.5304 4 19.0391 4.21071 19.4142 4.58579C19.7893 4.96086 20 5.46957 20 6V7" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V17" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_1717_7106"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>
            Check In
        </button>
    </div>
</template>

<style scoped>
    #btn-checkin{
        box-shadow:5px 8px 10px rgba(128, 128, 128, 0.683) !important;
    }
    #btn-checkin:hover{
        background-color: #2a9207 !important;
        box-shadow: 0px 0px 15px rgba(128, 128, 128, 0.47) !important;
    }
</style>
